<?php
defined('MOODLE_INTERNAL') || die();

// Evento cuando se califica un intento de cuestionario
function local_monitorias_quiz_attempt_submitted(\mod_quiz\event\attempt_submitted $event) {
    global $DB, $USER;

    $attempt = $event->get_record_snapshot('quiz_attempts', $event->objectid);

    // Umbral de riesgo
    if ($attempt->sumgrades < 3.0) {
        // Aquí iría la lógica para notificar al estudiante y monitor
        // Por ahora lo registramos
        debugging("Estudiante en riesgo detectado: {$USER->id}", DEBUG_DEVELOPER);
    }
}

