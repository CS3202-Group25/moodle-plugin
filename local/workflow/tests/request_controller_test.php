<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local_workflow
 * @author
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

defined('MOODLE_INTERNAL') || die();

use local_workflow\workflowController;

global $CFG;
require_once($CFG->dirroot . '/local/workflow/classes/workflowController.php');


class request_controller_test extends advanced_testcase
{
    public function test_create_request()
    {
        $this->resetAfterTest();
        $this->setUser(3);
        $workflowController = new workflowController();
        $workflows = $workflowController->getAllWorkflows();
        $this->assertEmpty($workflows);

        $courseid = 1;
        $lecturerid = 10;
        $instructorid = 10;
        $startdate = 20220910;
        $enddate = 20230910;

        $newWorkflow = $workflowController->createWorkflow($courseid, $lecturerid, $instructorid, $startdate, $enddate);

        $this->assertTrue($newWorkflow);
        $workflows = $workflowController->getAllWorkflows();
        $this->assertNotEmpty($workflows);

        $lastRecord = array_pop($workflows);

        $this->assertEquals(1, $lastRecord->courseid);
//        $test_workflow_name = $workflow->getName($record->id);
//        $this->assertEquals("Test workflow", $test_workflow_name);
    }
}

