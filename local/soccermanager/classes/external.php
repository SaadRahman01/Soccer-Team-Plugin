<?php

/**
 * External API for soccer manager plugin
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;

class local_soccermanager_external extends external_api
{

    /**
     * Returns description of method parameters for get_assignments
     * @return external_function_parameters
     */
    public static function get_assignments_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID')
            )
        );
    }

    /**
     * Get all soccer team assignments for a course
     * @param int $courseid
     * @return array
     */
    public static function get_assignments($courseid)
    {
        global $DB;

        $params = self::validate_parameters(
            self::get_assignments_parameters(),
            array('courseid' => $courseid)
        );

        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('local/soccermanager:view', $context);

        $assignments = $DB->get_records_sql('
            SELECT sa.*, u.firstname, u.lastname, u.email
            FROM {soccermanager_assignments} sa
            JOIN {user} u ON sa.userid = u.id
            WHERE sa.courseid = ?
            ORDER BY sa.jerseynumber
        ', array($params['courseid']));

        $result = array();
        foreach ($assignments as $assignment) {
            $result[] = array(
                'id' => $assignment->id,
                'courseid' => $assignment->courseid,
                'userid' => $assignment->userid,
                'firstname' => $assignment->firstname,
                'lastname' => $assignment->lastname,
                'email' => $assignment->email,
                'position' => $assignment->position,
                'jerseynumber' => $assignment->jerseynumber,
                'timecreated' => $assignment->timecreated,
                'timemodified' => $assignment->timemodified
            );
        }

        return $result;
    }

    /**
     * Returns description of method result value for get_assignments
     * @return external_description
     */
    public static function get_assignments_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Assignment ID'),
                    'courseid' => new external_value(PARAM_INT, 'Course ID'),
                    'userid' => new external_value(PARAM_INT, 'User ID'),
                    'firstname' => new external_value(PARAM_TEXT, 'First name'),
                    'lastname' => new external_value(PARAM_TEXT, 'Last name'),
                    'email' => new external_value(PARAM_EMAIL, 'Email address'),
                    'position' => new external_value(PARAM_TEXT, 'Soccer position'),
                    'jerseynumber' => new external_value(PARAM_INT, 'Jersey number'),
                    'timecreated' => new external_value(PARAM_INT, 'Time created'),
                    'timemodified' => new external_value(PARAM_INT, 'Time modified')
                )
            )
        );
    }

    /**
     * Returns description of method parameters for get_student_assignment
     * @return external_function_parameters
     */
    public static function get_student_assignment_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid' => new external_value(PARAM_INT, 'User ID')
            )
        );
    }

    /**
     * Get soccer team assignment for a specific student
     * @param int $courseid
     * @param int $userid
     * @return array
     */
    public static function get_student_assignment($courseid, $userid)
    {
        global $DB;

        $params = self::validate_parameters(
            self::get_student_assignment_parameters(),
            array('courseid' => $courseid, 'userid' => $userid)
        );

        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('local/soccermanager:view', $context);

        $assignment = $DB->get_record_sql('
            SELECT sa.*, u.firstname, u.lastname, u.email
            FROM {soccermanager_assignments} sa
            JOIN {user} u ON sa.userid = u.id
            WHERE sa.courseid = ? AND sa.userid = ?
        ', array($params['courseid'], $params['userid']));

        if (!$assignment) {
            return null;
        }

        return array(
            'id' => $assignment->id,
            'courseid' => $assignment->courseid,
            'userid' => $assignment->userid,
            'firstname' => $assignment->firstname,
            'lastname' => $assignment->lastname,
            'email' => $assignment->email,
            'position' => $assignment->position,
            'jerseynumber' => $assignment->jerseynumber,
            'timecreated' => $assignment->timecreated,
            'timemodified' => $assignment->timemodified
        );
    }

    /**
     * Returns description of method result value for get_student_assignment
     * @return external_description
     */
    public static function get_student_assignment_returns()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Assignment ID'),
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid' => new external_value(PARAM_INT, 'User ID'),
                'firstname' => new external_value(PARAM_TEXT, 'First name'),
                'lastname' => new external_value(PARAM_TEXT, 'Last name'),
                'email' => new external_value(PARAM_EMAIL, 'Email address'),
                'position' => new external_value(PARAM_TEXT, 'Soccer position'),
                'jerseynumber' => new external_value(PARAM_INT, 'Jersey number'),
                'timecreated' => new external_value(PARAM_INT, 'Time created'),
                'timemodified' => new external_value(PARAM_INT, 'Time modified')
            ),
            'Student assignment',
            VALUE_OPTIONAL
        );
    }
}
