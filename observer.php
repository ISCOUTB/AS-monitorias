defined('MOODLE_INTERNAL') || die();

class local_monitorias_observer {
    public static function quiz_submitted(\mod_quiz\event\attempt_submitted $event) {
        global $DB;

        $attempt = $DB->get_record('quiz_attempts', ['id' => $event->objectid]);
        $grade = $attempt->sumgrades;
        
        // Cambia esto por el umbral que tú definas como "riesgo"
        if ($grade < 3) {
            // Enviar notificación al estudiante
            $user = $DB->get_record('user', ['id' => $attempt->userid]);

            $message = new \core\message\message();
            $message->component = 'local_monitorias';
            $message->name = 'monitoria_alert';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $user;
            $message->subject = "Rendimiento en riesgo";
            $message->fullmessage = "Obtuviste una nota baja. ¿Deseas agendar una monitoría?";
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml = "<p>Obtuviste una nota baja. <a href='link-a-plugin'>Solicitar monitoría</a></p>";
            $message->notification = 1;

            message_send($message);
        }
    }
}

