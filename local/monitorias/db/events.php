<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_quiz\event\attempt_submitted',
        'callback'    => 'local_monitorias_observer::quiz_submitted',
        'includefile' => '/local/monitorias/observer.php',
        'internal'    => false,
        'priority'    => 9999
    ],
];

