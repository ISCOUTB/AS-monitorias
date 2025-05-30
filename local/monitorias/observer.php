<?php
defined('MOODLE_INTERNAL') || die();

class local_monitorias_observer {

    public static function quiz_submitted(\mod_quiz\event\attempt_submitted $event) {
        global $DB;

        // Obtener intento
        $attempt = $DB->get_record('quiz_attempts', ['id' => $event->objectid]);
        if (!$attempt) {
            return;
        }

        // Obtener nota máxima del cuestionario
        $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz]);
        $grade = $attempt->sumgrades;
        $maxgrade = $quiz->sumgrades;

        // Calcular nota normalizada (sobre 5)
        $nota = round(($grade / $maxgrade) * 5, 2);

        // Verificar si la nota está en riesgo (por ejemplo, menor a 3.0)
        if ($nota < 3.0) {
            $user = $DB->get_record('user', ['id' => $attempt->userid]);

            // Enviar notificación
            $message = new \core\message\message();
            $message->component = 'local_monitorias';
            $message->name = 'monitoria_alert';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $user;
            $message->subject = "¡Estás en riesgo académico!";
            $message->fullmessage = "Tu nota fue de $nota. ¿Deseas recibir una monitoría? Visita tu área personal para responder.";
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml = "<p>Tu nota fue de <strong>$nota</strong>. <a href='/local/monitorias/accept.php?userid={$user->id}'>Aceptar monitoría</a></p>";
            $message->notification = 1;

            message_send($message);
        }
    }
}

