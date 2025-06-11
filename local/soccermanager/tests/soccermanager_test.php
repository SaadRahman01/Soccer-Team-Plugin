<?php

/**
 * Unit tests for the soccer manager plugin
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_soccermanager_testcase extends advanced_testcase
{

    /**
     * Test creating and retrieving assignments
     */
    public function test_assignment_crud()
    {
        global $DB;

        $this->resetAfterTest();

        // Create test data
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Create assignment
        $assignment = new stdClass();
        $assignment->courseid = $course->id;
        $assignment->userid = $user->id;
        $assignment->position = 'goalkeeper';
        $assignment->jerseynumber = 1;
        $assignment->timecreated = time();
        $assignment->timemodified = time();

        $assignmentid = $DB->insert_record('soccermanager_assignments', $assignment);

        // Test retrieval
        $retrieved = $DB->get_record('soccermanager_assignments', array('id' => $assignmentid));
        $this->assertEquals($course->id, $retrieved->courseid);
        $this->assertEquals($user->id, $retrieved->userid);
        $this->assertEquals('goalkeeper', $retrieved->position);
        $this->assertEquals(1, $retrieved->jerseynumber);
    }

    /**
     * Test jersey number uniqueness constraint
     */
    public function test_jersey_uniqueness()
    {
        global $DB;

        $this->resetAfterTest();

        // Create test data
        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Create first assignment
        $assignment1 = new stdClass();
        $assignment1->courseid = $course->id;
        $assignment1->userid = $user1->id;
        $assignment1->position = 'goalkeeper';
        $assignment1->jerseynumber = 1;
        $assignment1->timecreated = time();
        $assignment1->timemodified = time();

        $DB->insert_record('soccermanager_assignments', $assignment1);

        // Try to create second assignment with same jersey number
        $assignment2 = new stdClass();
        $assignment2->courseid = $course->id;
        $assignment2->userid = $user2->id;
        $assignment2->position = 'defender';
        $assignment2->jerseynumber = 1; // Same jersey number
        $assignment2->timecreated = time();
        $assignment2->timemodified = time();

        // This should fail due to unique constraint
        $this->expectException(dml_write_exception::class);
        $DB->insert_record('soccermanager_assignments', $assignment2);
    }

    /**
     * Test web service functionality
     */
    public function test_webservice_get_assignments()
    {
        $this->resetAfterTest();

        // Create test data
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Set up user context
        $this->setUser($user);

        // Create assignment directly in database
        $assignment = new stdClass();
        $assignment->courseid = $course->id;
        $assignment->userid = $user->id;
        $assignment->position = 'midfielder';
        $assignment->jerseynumber = 10;
        $assignment->timecreated = time();
        $assignment->timemodified = time();

        global $DB;
        $DB->insert_record('soccermanager_assignments', $assignment);

        // Test web service
        $assignments = local_soccermanager_external::get_assignments($course->id);

        $this->assertCount(1, $assignments);
        $this->assertEquals($user->id, $assignments[0]['userid']);
        $this->assertEquals('midfielder', $assignments[0]['position']);
        $this->assertEquals(10, $assignments[0]['jerseynumber']);
    }
}
