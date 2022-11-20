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
            return $DB->insert_record('workflow_request', $recordToInsert);
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
            return $DB->insert_record('workflow_request_extend', $recordToInsert, false);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function getAllRequests()
    {
        global $DB;
        try {
            return $DB->get_records('workflow_request');
        } catch (dml_exception $e) {
            return [];
        }
    }

    public function getRequest($requestid)
    {
        global $DB;
        return $DB->get_record('workflow_request', array('requestid'=>$requestid));
    }

    public function changeStatus($requestid, $state){
        global $DB;
        $sql = 'update {workflow_request} set state = :state where requestid= :requestid';
        $params = [
            'state' => $state,
            'requestid' => $requestid,
        ];

        try {
            return $DB->execute($sql, $params);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function confirmInquiry($requestid,$inquiry){
        global $DB;
        $sqlquery="update mdl_workflow_request set askedmoredetails=1,commentlecturer='$inquiry' where requestid='$requestid'";
        try {
            return $DB->execute($sqlquery);
        } catch(dml_exception $e) {
            return false;
        }
    }

    public function updateDetails($requestid,$details,$files){
        global $DB;
        $sqlquery = "update mdl_workflow_request set reason='$details', filesid='$files' where requestid='$requestid'";
        try {
            return $DB->execute($sqlquery);
        } catch(dml_exception $e) {
            return false;
        }
    }

    public function changeReceiver($requestid){
        global $DB;
        $workflowid = $DB->get_record('workflow_request', array('requestid' => $requestid))->workflowid;
        $lecturerid = $DB->get_record('workflow', array('id'=>$workflowid))->lecturerid;
        $sql = 'update {workflow_request} set receivedby = :receivedby where requestid= :requestid';
        $params = [
            'receivedby' => $lecturerid,
            'requestid' => $requestid,
        ];

        try {
            return $DB->execute($sql, $params);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function changeDeadline($requestid){
        global $DB;
        $request = $DB->get_record('workflow_request', array('requestid'=>$requestid));
        $requesttype = $request->requesttype;
        $isbatchrequest = $request->isbatchrequest;
        $studentid = $request->studentid;
        if($requesttype=="Extend Deadline") {
            $requestextend = $DB->get_record('workflow_request_extend', array('requestid' => $requestid));
            if ($requestextend->assessmenttype == "Quiz") {
                $quizid = $requestextend->assessmentid;
                $newtime = $requestextend->extendtime;
                if($isbatchrequest == 1) {
                    $sql = 'update {quiz} set timeclose = :newtime where id= :quizid';
                    $params = [
                        'newtime' => $newtime,
                        'quizid' => $quizid,
                    ];

                    try {
                        return $DB->execute($sql, $params);
                    } catch (dml_exception $e) {
                        return false;
                    }
                }elseif ($isbatchrequest == 0){
                    $quiz = $DB->get_record('quiz', array('id'=>$quizid));
                    $recordToInsert = new stdClass();
                    $recordToInsert->quiz = $quizid;
                    $recordToInsert->userid = $studentid;
                    $recordToInsert->timeopen = $quiz->timeopen;
                    $recordToInsert->timeclose = $newtime;
                    $recordToInsert->attempts = $quiz->attempts;
                    $recordToInsert->password = $quiz->password;

                    try {
                        return $DB->insert_record('quiz_overrides', $recordToInsert, false);
                    } catch (dml_exception $e) {
                        return false;
                    }
                }
            } elseif ($requestextend->assessmenttype == "Assignment") {
                $assignid = $requestextend->assessmentid;
                $newdate = $requestextend->extendtime;
                if($isbatchrequest == 1) {
                    $sql = 'update {assign} set duedate = :newdate where id= :assignid';
                    $params = [
                        'newdate' => $newdate,
                        'assignid' => $assignid,
                    ];

                    try {
                        return $DB->execute($sql, $params);
                    } catch (dml_exception $e) {
                        return false;
                    }
                }elseif ($isbatchrequest == 0){
                    $assign = $DB->get_record('quiz', array('id'=>$assignid));
                    $recordToInsert = new stdClass();
                    $recordToInsert->assignid = $assignid;
                    $recordToInsert->userid = $studentid;
                    $recordToInsert->allowsubmissionsfromdate = $assign->allowsubmissionsfromdate;
                    $recordToInsert->duedate = $newdate;

                    try {
                        return $DB->insert_record('assign_overrides', $recordToInsert, false);
                    } catch (dml_exception $e) {
                        return false;
                    }
                }
            }
        }
    }

    public function deleteRequest($requestid){
        global $DB;
        return $DB->delete_records('workflow_request', ['requestid' => $requestid]);
    }
}