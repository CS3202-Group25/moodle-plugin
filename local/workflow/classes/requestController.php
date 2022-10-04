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
 */

use stdClass;
use dml_exception;

class requestController
{
    public function createRequest($workflowid, $studentid, $requesttype, $isbatchrequest, $reason, $filesid, $askedmoredetails, $commentlecturer, $sentdate, $receivedby){

        global $DB;
        $recordToInsert = new stdClass();
        $recordToInsert->workflowid = $workflowid;
        $recordToInsert->studentid = $studentid;
        $recordToInsert->requesttype = $requesttype;
        $recordToInsert->isbatchrequest = $isbatchrequest;
        $recordToInsert->reason = $reason;
        $recordToInsert->filesid = $filesid;
        $recordToInsert->state = 'Pending';
        $recordToInsert->askedmoredetails = $askedmoredetails;
        $recordToInsert->commentlecturer = $commentlecturer;
        $recordToInsert->sentdate = $sentdate;
        $recordToInsert->receivedby = $receivedby;

        try {
            return $DB->insert_record('local_workflow_request', $recordToInsert);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function createRequestExtend($requestid, $assessmenttype, $assessmentid, $extendtime){

        global $DB;
        $recordToInsert = new stdClass();
        $recordToInsert->requestid = $requestid;
        $recordToInsert->assessmenttype = $assessmenttype;
        $recordToInsert->assessmentid = $assessmentid;
        $recordToInsert->extendtime = $extendtime;

        try {
            return $DB->insert_record('local_request_extend', $recordToInsert, false);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function getAllRequests()
    {
        global $DB;
        try {
            return $DB->get_records('local_workflow_request');
        } catch (dml_exception $e) {
            return [];
        }
    }

    public function getRequest($requestid)
    {
        global $DB;
        return $DB->get_record('local_workflow_request', array('requestid'=>$requestid));
    }

    public function changeStatus($newValue, $requestid, $field){
        global $DB;
        try {
            $needToUpdate = array_values($this->getRequest($requestid));
            return $DB->set_field_select('local_workflow_request', $field, $newValue, "requestid $needToUpdate->requestid");
        } catch (dml_exception $e){
            return false;
        }
    }

    public function deleteRequest($requestid){
        global $DB;
        return $DB->delete_records('local_workflow_request', ['requestid' => $requestid]);
    }
}