<?php
defined('MOODLE_INTERNAL') || die();

class block_monitorias_block extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_monitorias_block');
    }

    public function get_content() {
        global $USER, $COURSE, $DB, $CFG;

        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->dirroot . '/grade/lib.php');
        require_once($CFG->libdir . '/gradelib.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);

        if (!has_capability('moodle/grade:viewall', $context)) {
            $this->content = new stdClass();
            $this->content->text = get_string('nopermission', 'block_monitorias_block');
            return $this->content;
        }

        $this->content = new stdClass();
        $output = '';
        $threshold = 3.0;
        $students = get_enrolled_users($context, 'mod/assign:submit');

        if (empty($students)) {
            $this->content->text = 'No hay estudiantes en el curso.';
            return $this->content;
        }

        // 1. Reparar estructura de calificaciones
        $this->fix_course_grade_structure($COURSE->id);

        // 2. Obtener ítem de calificación del curso
        $course_grade_item = $this->get_course_grade_item($COURSE->id);
        if (!$course_grade_item) {
            $this->content->text = 'No se pudo obtener la calificación total del curso.';
            return $this->content;
        }

        // 3. Obtener calificaciones
        $studentids = array_keys($students);
        list($in_sql, $in_params) = $DB->get_in_or_equal($studentids, SQL_PARAMS_NAMED);
        $params = array_merge(['itemid' => $course_grade_item->id], $in_params);
        
        $grades = $DB->get_records_sql(
            "SELECT * 
             FROM {grade_grades} 
             WHERE itemid = :itemid 
               AND userid $in_sql",
            $params
        );

        // Organizar calificaciones por usuario
        $student_grades = [];
        foreach ($grades as $grade) {
            $student_grades[$grade->userid] = $grade;
        }

        $has_low_grades = false;
        
        foreach ($students as $student) {
            $student_grade = null;
            $is_new_student = false;
            
            if (isset($student_grades[$student->id])) {
                $grade_obj = $student_grades[$student->id];
                
                // SOLUCIÓN: Manejar estados de calificación
                if ($grade_obj->finalgrade !== null && is_numeric($grade_obj->finalgrade)) {
                    $student_grade = (float)$grade_obj->finalgrade;
                } 
                elseif ($grade_obj->rawgrade !== null && is_numeric($grade_obj->rawgrade)) {
                    $student_grade = (float)$grade_obj->rawgrade;
                }
            }

            // Determinar si es estudiante nuevo sin calificaciones
            if ($student_grade === null) {
                $enrol_time = $DB->get_field_sql(
                    "SELECT MIN(ue.timestart) 
                     FROM {user_enrolments} ue
                     JOIN {enrol} e ON e.id = ue.enrolid
                     WHERE ue.userid = :userid 
                       AND e.courseid = :courseid",
                    ['userid' => $student->id, 'courseid' => $COURSE->id]
                );
                
                // Considerar "nuevo" si se matriculó en los últimos 7 días
                $is_new_student = ($enrol_time > (time() - 604800));
            }

            // SOLUCIÓN: Solo mostrar estudiantes con calificación válida < 3
            if ($student_grade !== null && $student_grade < $threshold) {
                $has_low_grades = true;
                $output .= "<div class='alert alert-warning'><strong>" . fullname($student) . "</strong>: Nota: $student_grade<br>";

                // Buscar scheduler
                $scheduler = $DB->get_record('scheduler', ['course' => $COURSE->id]);
                if (!$scheduler) {
                    $output .= "<em>No hay schedulers configurados en este curso.</em></div><hr>";
                    continue;
                }

                // Buscar slot disponible
                $slot = $DB->get_record_sql(
                    "SELECT * FROM {scheduler_slots} 
                     WHERE schedulerid = ? AND teacherid = ? 
                     ORDER BY starttime ASC LIMIT 1",
                    [$scheduler->id, $USER->id]
                );

                if (!$slot) {
                    $output .= "<em>No hay horarios disponibles para monitorías.</em></div><hr>";
                    continue;
                }

                // Intentar agendar cita
                try {
                    $existing = $DB->get_record('scheduler_appointment', [
                        'slotid' => $slot->id,
                        'studentid' => $student->id
                    ]);

                    if (!$existing) {
                        $appointment = (object)[
                            'slotid' => $slot->id,
                            'studentid' => $student->id,
                            'attended' => 0,
                            'grade' => null,
                            'appointmentnote' => 'Asignación automática por bloque de monitorías',
                            'timecreated' => time(),
                            'timemodified' => time(),
                            'appointmentnoteformat' => 0,
                            'teachernoteformat' => 0,
                            'studentnoteformat' => 0
                        ];

                        $DB->insert_record('scheduler_appointment', $appointment);
                        $slotdate = userdate($slot->starttime, '%A %d %B %Y, %H:%M');
                        $output .= "✅ <em>Cita agendada: $slotdate</em>";
                        $this->send_monitor_notification($student, $USER, $COURSE->fullname, $slotdate);
                    } else {
                        $output .= "<em>El estudiante ya tiene una cita asignada.</em>";
                    }
                } catch (Exception $e) {
                    error_log("[Monitorias] Error en BD: " . $e->getMessage());
                    $output .= "<em>Error al procesar citas. Contacte al administrador.</em>";
                }

                $output .= "</div><hr>";
            }
            // SOLUCIÓN: Mostrar estudiantes nuevos con nota pendiente
            elseif ($is_new_student) {
                $output .= "<div class='alert alert-info'>";
                $output .= "<strong>" . fullname($student) . "</strong>: Estudiante nuevo (aún sin calificaciones)";
                $output .= "</div>";
            }
        }

        if (!$has_low_grades) {
            $output = "<div class='alert alert-success'>No hay estudiantes con calificaciones bajas (< $threshold).</div>";
        }

        $this->content->text = $output;
        return $this->content;
    }

    /**
     * Repara la estructura de calificaciones del curso
     */
    private function fix_course_grade_structure($courseid) {
        global $DB;
        
        // 1. Buscar y eliminar ítems duplicados
        $duplicate_items = $DB->get_records_sql(
            "SELECT MIN(id) as min_id, courseid 
             FROM {grade_items} 
             WHERE itemtype = 'course' 
               AND courseid = ?
             GROUP BY courseid 
             HAVING COUNT(*) > 1",
            [$courseid]
        );
        
        if ($duplicate_items) {
            foreach ($duplicate_items as $item) {
                $DB->execute(
                    "DELETE FROM {grade_items} 
                     WHERE itemtype = 'course' 
                       AND courseid = ? 
                       AND id > ?",
                    [$courseid, $item->min_id]
                );
            }
        }
        
        // 2. Forzar recálculo de calificaciones
        if ($grade_category = grade_category::fetch_course_category($courseid)) {
            $grade_category->load_grade_item();
            $grade_category->generate_grades();
        }
    }

    /**
     * Obtiene el ítem de calificación del curso de forma segura
     */
    private function get_course_grade_item($courseid) {
        global $DB;
        
        // Intentar obtener el ítem oficial
        if ($grade_item = grade_item::fetch_course_item($courseid)) {
            return $grade_item;
        }
        
        // Si falla, usar método alternativo
        return $DB->get_record_sql(
            "SELECT * 
             FROM {grade_items} 
             WHERE courseid = ? 
               AND itemtype = 'course' 
             ORDER BY id ASC 
             LIMIT 1",
            [$courseid]
        );
    }

    private function send_monitor_notification($student, $teacher, $coursename, $slotdate) {
        // Notificación al estudiante
        $message = new \core\message\message();
        $message->component = 'block_monitorias_block';
        $message->name = 'monitoringnotification';
        $message->userfrom = $teacher;
        $message->userto = $student;
        $message->subject = "Sesión de monitoría agendada - $coursename";
        $message->fullmessage = "Hola {$student->firstname},\n\nSe ha agendado una sesión de monitoría para el curso '$coursename'.\nFecha: $slotdate.\n\nPor favor revisa tu calendario.";
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->smallmessage = "Monitoría agendada para $slotdate";
        $message->notification = 1;
        message_send($message);

        // Notificación al profesor
        $message2 = new \core\message\message();
        $message2->component = 'block_monitorias_block';
        $message2->name = 'monitoringnotification';
        $message2->userfrom = core_user::get_noreply_user();
        $message2->userto = $teacher;
        $message2->subject = "Nueva cita de monitoría con {$student->firstname}";
        $message2->fullmessage = "Has sido asignado a una sesión de monitoría con:\n\nEstudiante: {$student->firstname} {$student->lastname}\nCurso: $coursename\nFecha: $slotdate";
        $message2->fullmessageformat = FORMAT_PLAIN;
        $message2->smallmessage = "Nueva cita el $slotdate";
        $message2->notification = 1;
        message_send($message2);
    }
}