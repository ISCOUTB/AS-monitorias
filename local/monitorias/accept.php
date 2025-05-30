<?php
require_once(__DIR__ . '/../../config.php');
require_login();

$userid = required_param('userid', PARAM_INT);

// Verificar que quien accede es el mismo que está en la URL
if ($USER->id != $userid) {
    print_error('No tienes permiso para realizar esta acción.');
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Confirmación de solicitud de monitoría');

// Mensaje para el estudiante
echo html_writer::div('Has aceptado recibir monitoría. Un monitor será notificado.', 'alert alert-success');

// Notificar al monitor fijo (por correo o ID)
$monitor = $DB->get_record('user', ['email' => 'monitor1@prueba.local']);

if ($monitor) {
    $message = new \core\message\message();
    $message->component = 'local_monitorias';
    $message->name = 'monitor_request';
    $message->userfrom = \core_user::get_noreply_user();
    $message->userto = $monitor;
    $message->subject = "Nuevo estudiante requiere monitoría";
    $message->fullmessage = "El estudiante {$USER->firstname} {$USER->lastname} ha aceptado recibir monitoría.";
    $message->fullmessageformat = FORMAT_MARKDOWN;
    $message->fullmessagehtml = "<p>El estudiante <strong>{$USER->firstname} {$USER->lastname}</strong> ha aceptado recibir monitoría.</p>";
    $message->notification = 1;

    message_send($message);
}

echo $OUTPUT->footer();

