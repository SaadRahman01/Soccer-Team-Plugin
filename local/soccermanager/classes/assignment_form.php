<?php

/**
 * Form for creating/editing soccer team assignments
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class local_soccermanager_assignment_form extends moodleform
{

    public function definition()
    {
        global $DB;

        $mform = $this->_form;
        $courseid = $this->_customdata['courseid'];
        $id = isset($this->_customdata['id']) ? $this->_customdata['id'] : 0;

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        // Get enrolled students
        $context = context_course::instance($courseid);
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $students = get_role_users($studentrole->id, $context, false, 'u.id, u.username, u.firstname, u.lastname, u.email');

        $studentoptions = array('' => get_string('selectstudent', 'local_soccermanager'));
        foreach ($students as $student) {
            $studentoptions[$student->id] = fullname($student);
        }

        $mform->addElement('select', 'userid', get_string('selectstudent', 'local_soccermanager'), $studentoptions);
        $mform->addRule('userid', get_string('required'), 'required', null, 'client');

        // Position selector
        $positions = array('' => get_string('selectposition', 'local_soccermanager')) +
            local_soccermanager_get_positions();
        $mform->addElement('select', 'position', get_string('position', 'local_soccermanager'), $positions);
        $mform->addRule('position', get_string('required'), 'required', null, 'client');

        // Jersey number
        $jerseynumbers = array('' => get_string('jerseynumber', 'local_soccermanager')) +
            local_soccermanager_get_jersey_numbers();
        $mform->addElement('select', 'jerseynumber', get_string('jerseynumber', 'local_soccermanager'), $jerseynumbers);
        $mform->addRule('jerseynumber', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }

    public function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);

        // Check if jersey number is already taken
        $conditions = array(
            'courseid' => $data['courseid'],
            'jerseynumber' => $data['jerseynumber']
        );

        if ($data['id']) {
            // Exclude current record when editing
            $sql = "courseid = ? AND jerseynumber = ? AND id <> ?";
            $params = array($data['courseid'], $data['jerseynumber'], $data['id']);
            $exists = $DB->record_exists_select('soccermanager_assignments', $sql, $params);
        } else {
            $exists = $DB->record_exists('soccermanager_assignments', $conditions);
        }

        if ($exists) {
            $errors['jerseynumber'] = get_string('jerseynumberinuse', 'local_soccermanager', $data['jerseynumber']);
        }

        // Check if student is already assigned (only for new assignments)
        if (!$data['id']) {
            $studentassigned = $DB->record_exists(
                'soccermanager_assignments',
                array('courseid' => $data['courseid'], 'userid' => $data['userid'])
            );
            if ($studentassigned) {
                $errors['userid'] = get_string('studentalreadyassigned', 'local_soccermanager');
            }
        }

        return $errors;
    }
}
