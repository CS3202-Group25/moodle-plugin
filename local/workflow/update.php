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

global $DB, $CFG, $USER;

require_once (__DIR__ . '/../../config.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/messagesender.php');

require_login();

$value = $_GET["value"];
$requestid = required_param('requestid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);

$request = $DB->get_record('workflow_request', array('requestid'=>$requestid));
$workflowid = $DB->get_record('workflow_request', array('requestid' => $requestid))->workflowid;
$lecturerid = $DB->get_record('workflow', array('id'=>$workflowid))->lecturerid;
$courseid = $DB->get_record('course_modules', array('id'=>$cmid))->course;
$context = context_course::instance($courseid);
$coursename = $DB->get_record('course', array('id'=> $courseid))->fullname;
$studentid = $DB->get_record('workflow_request', array('requestid'=>$requestid))->studentid;

$requestController = new requestController();
$messagesender = new \mod_workflow\messageSender();

if($value === 'cancelIns') {
    $messagesender->send($studentid, $cmid, $requestid, $value);
//    $requestController->deleteRequest($requestid);
    $requestController->changeStatus($requestid, 'Cancelled  by Instructor');
    redirect("$CFG->wwwroot/mod/workflow/view.php?id=$cmid", "You have successfully deleted the request");
}elseif ($value === 'cancelStudent') {
//    $requestController->deleteRequest($requestid);
    $requestController->changeStatus($requestid, 'Cancelled by Student');
    redirect("$CFG->wwwroot/mod/workflow/view.php?id=$cmid", "You have successfully deleted the request");
}elseif ($value === 'approve'){
    $messagesender->send($studentid, $cmid, $requestid, $value);
//    if($request->isbatchrequest == 1) {
//        $studentids = $DB->get_fieldset_select('role_assignments', 'userid', 'contextid = :contextid and roleid=:roleid', [
//            'contextid' => $context->id,
//            'roleid' => '5',
//        ]);
//        foreach ($studentids as $id) {
//            if($studentid != $id) {
//                $messagesender->send($studentid, $cmid, $requestid, $value);
//            }
//        }
//    }
    $requestController->changeStatus($requestid, 'Approved');
    $requestController->changeDeadline($requestid);
    redirect("$CFG->wwwroot/mod/workflow/view.php?id=$cmid", "You have approved the request");
}elseif($value === 'disapprove'){
    $messagesender->send($studentid, $cmid, $requestid, $value);
    $requestController->changeStatus($requestid, 'Disapproved');
    redirect("$CFG->wwwroot/mod/workflow/view.php?id=$cmid", "You have approved the request");
}elseif($value === 'forward'){
    $messagesender->send($studentid, $cmid, $requestid, $value);
    $messagesender->send($lecturerid, $cmid, $requestid, $value);
    $requestController->changeStatus($requestid, 'Forwarded');
    $requestController->changeReceiver($requestid);
    redirect("$CFG->wwwroot/mod/workflow/view.php?id=$cmid", "You have forwarded the request to lecturer");
}