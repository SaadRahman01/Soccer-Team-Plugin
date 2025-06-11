<?php

/**
 * Web service definitions for the soccer manager plugin
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_soccermanager_get_assignments' => array(
        'classname'   => 'local_soccermanager_external',
        'methodname'  => 'get_assignments',
        'classpath'   => 'local/soccermanager/classes/external.php',
        'description' => 'Get soccer team assignments for a course',
        'type'        => 'read',
        'capabilities' => 'local/soccermanager:view',
        'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'local_soccermanager_get_student_assignment' => array(
        'classname'   => 'local_soccermanager_external',
        'methodname'  => 'get_student_assignment',
        'classpath'   => 'local/soccermanager/classes/external.php',
        'description' => 'Get soccer team assignment for a specific student',
        'type'        => 'read',
        'capabilities' => 'local/soccermanager:view',
        'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    )
);

$services = array(
    'Soccer Manager API' => array(
        'functions' => array(
            'local_soccermanager_get_assignments',
            'local_soccermanager_get_student_assignment'
        ),
        'restrictedusers' => 0,
        'enabled' => 1
    )
);
