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
 * @package     mod_workflow
 * @author
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');


class request_controller_test extends advanced_testcase
{
    public function test_createRequest()
    {
        $this->resetAfterTest();
        $this->setUser(2);
        $requestController = new requestController();
        $requests = $requestController->getAllRequests();
        $this->assertEmpty($requests);

        $workflowid = "10";
        $studentid = "100";
        $requesttype = "Extend Deadline";
        $isbatchrequest = 0;
        $reason = 'Test Reason';
        $askedmoredetails = 0;
        $filesid = 10;
        $commentlecturer = 'Test comment';
        $sentdate = 10;
        $receivedby = 2;

        $testrequest = $requestController->createRequest($workflowid, $studentid, $requesttype, $isbatchrequest, $reason, $filesid, $askedmoredetails, $commentlecturer, $sentdate, $receivedby);

        $requests = $requestController->getAllRequests();
        $this->assertNotEmpty($requests);

        $record = array_pop($requests);

        $testRequestReason = $requestController->getRequest($record->requestid)->reason;
        $this->assertEquals('Test Reason', $testRequestReason);
    }

    public function test_createRequestExtend()
    {
        $this->resetAfterTest();
        $this->setUser(2);
        $requestController = new requestController();
        $requests = $requestController->getAllExtendRequests();
        $this->assertEmpty($requests);

        $requestid = "10";
        $assessmentid = "1";
        $assessmenttype = "Quiz";
        $extendtime = "3";

        $testrequest = $requestController->createRequestExtend($requestid, $assessmenttype, $assessmentid, $extendtime);

        $requests = $requestController->getAllExtendRequests();
        $this->assertNotEmpty($requests);

        $record = array_pop($requests);

        $testRequestType = $requestController->getExtendRequest($record->requestid)->assessmenttype;
        $this->assertEquals('Quiz', $testRequestType);
    }

    public function test_changeStatus()
    {
        $this->resetAfterTest();
        $this->setUser(2);
        $requestController = new requestController();
        $requests = $requestController->getAllRequests();
        $this->assertEmpty($requests);

        $workflowid = "10";
        $studentid = "100";
        $requesttype = "Extend Deadline";
        $isbatchrequest = 0;
        $reason = 'Test Reason';
        $askedmoredetails = 0;
        $filesid = 10;
        $commentlecturer = 'Test comment';
        $sentdate = 10;
        $receivedby = 2;

        $testrequest = $requestController->createRequest($workflowid, $studentid, $requesttype, $isbatchrequest, $reason, $filesid, $askedmoredetails, $commentlecturer, $sentdate, $receivedby);

        $requests = $requestController->getAllRequests();
        $this->assertNotEmpty($requests);

        $record = array_pop($requests);

        $requestController->changeStatus($record->requestid, "Forwarded");
        $testRequestState = $requestController->getRequest($record->requestid)->state;
        $this->assertEquals('Forwarded', $testRequestState);
    }
}

