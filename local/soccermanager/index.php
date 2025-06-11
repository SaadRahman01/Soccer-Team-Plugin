<?php

/**
 * Main page for soccer team management
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/tablelib.php');

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', 'view', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);
require_capability('local/soccermanager:manage', $context);

$PAGE->set_url('/local/soccermanager/index.php', array('courseid' => $courseid));
$PAGE->set_title(get_string('soccermanager', 'local_soccermanager'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

// Handle actions
if ($action === 'delete' && $id) {
    require_sesskey();
    $DB->delete_records('soccermanager_assignments', array('id' => $id, 'courseid' => $courseid));
    redirect($PAGE->url, get_string('assignmentdeleted', 'local_soccermanager'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('soccermanager', 'local_soccermanager'));

// Display assignment form
$mform = new local_soccermanager_assignment_form(null, array('courseid' => $courseid, 'id' => $id));

if ($mform->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $mform->get_data()) {
    // Process form submission
    $assignment = new stdClass();
    $assignment->courseid = $courseid;
    $assignment->userid = $data->userid;
    $assignment->position = $data->position;
    $assignment->jerseynumber = $data->jerseynumber;
    $assignment->timemodified = time();

    if ($data->id) {
        // Update existing assignment
        $assignment->id = $data->id;
        $DB->update_record('soccermanager_assignments', $assignment);
    } else {
        // Create new assignment
        $assignment->timecreated = time();
        $DB->insert_record('soccermanager_assignments', $assignment);
    }

    redirect($PAGE->url, get_string('assignmentsaved', 'local_soccermanager'));
}

// If editing, load existing data
if ($action === 'edit' && $id) {
    $assignment = $DB->get_record(
        'soccermanager_assignments',
        array('id' => $id, 'courseid' => $courseid),
        '*',
        MUST_EXIST
    );
    $mform->set_data($assignment);
}

$mform->display();

// Display current assignments table
$assignments = $DB->get_records_sql('
    SELECT sa.*, u.firstname, u.lastname, u.email
    FROM {soccermanager_assignments} sa
    JOIN {user} u ON sa.userid = u.id
    WHERE sa.courseid = ?
    ORDER BY sa.jerseynumber
', array($courseid));

if ($assignments) {
    echo $OUTPUT->heading(get_string('currentassignments', 'local_soccermanager'), 3);

    $table = new html_table();
    $table->head = array(
        get_string('jersey', 'local_soccermanager'),
        get_string('student', 'local_soccermanager'),
        get_string('position', 'local_soccermanager'),
        get_string('actions', 'local_soccermanager')
    );

    $positions = local_soccermanager_get_positions();

    foreach ($assignments as $assignment) {
        $editurl = new moodle_url($PAGE->url, array('action' => 'edit', 'id' => $assignment->id));
        $deleteurl = new moodle_url($PAGE->url, array(
            'action' => 'delete',
            'id' => $assignment->id,
            'sesskey' => sesskey()
        ));

        $actions = html_writer::link($editurl, get_string('edit', 'local_soccermanager')) . ' | ' .
            html_writer::link(
                $deleteurl,
                get_string('delete', 'local_soccermanager'),
                array('onclick' => 'return confirm("Are you sure?")')
            );

        $table->data[] = array(
            $assignment->jerseynumber,
            fullname($assignment),
            $positions[$assignment->position],
            $actions
        );
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noassignments', 'local_soccermanager'));
}

echo $OUTPUT->footer();
