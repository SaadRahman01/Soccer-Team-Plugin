<?php

/**
 * Library functions for the soccer manager plugin
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the course navigation to add soccer team management link
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function local_soccermanager_extend_navigation_course($navigation, $course, $context)
{
    // Check if user has capability to manage soccer teams
    if (has_capability('local/soccermanager:manage', $context)) {
        $url = new moodle_url('/local/soccermanager/index.php', array('courseid' => $course->id));
        $navigation->add(
            get_string('soccermanager', 'local_soccermanager'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'soccermanager',
            new pix_icon('i/group', '')
        );
    }
}

/**
 * Get available soccer positions
 *
 * @return array
 */
function local_soccermanager_get_positions()
{
    return [
        'goalkeeper' => get_string('position_goalkeeper', 'local_soccermanager'),
        'defender' => get_string('position_defender', 'local_soccermanager'),
        'midfielder' => get_string('position_midfielder', 'local_soccermanager'),
        'forward' => get_string('position_forward', 'local_soccermanager'),
    ];
}

/**
 * Get available jersey numbers
 *
 * @return array
 */
function local_soccermanager_get_jersey_numbers()
{
    $numbers = [];
    for ($i = 1; $i <= 25; $i++) {
        $numbers[$i] = $i;
    }
    return $numbers;
}

/**
 * Check if jersey number is available in course
 *
 * @param int $courseid
 * @param int $jerseynumber
 * @param int $excludeuserid
 * @return bool
 */
function local_soccermanager_is_jersey_available($courseid, $jerseynumber, $excludeuserid = 0)
{
    global $DB;

    $conditions = ['courseid' => $courseid, 'jerseynumber' => $jerseynumber];
    if ($excludeuserid) {
        $conditions['userid'] = ['<>', $excludeuserid];
    }

    return !$DB->record_exists('soccermanager_assignments', $conditions);
}
