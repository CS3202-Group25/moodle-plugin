<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Version details
 *
 * @package    mod_workflow
 * @copyright  2022 SEP25
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_workflow;

use stdClass;
use dml_exception;

class messageSender{

    public function send($usertoid, $cmid, $requestid, $value, $contextid){
        global $USER, $DB, $PAGE;

        $message = new \core\message\message();
        $message->component = 'mod_workflow'; // Your plugin's name
        $message->name = 'workflow_notification'; // Your notification name from message.php
        $message->userfrom = $USER; // If the message is 'from' a specific user you can set them here
        $message->userto = $usertoid;
        $message->contexturl = (new \moodle_url("/mod/workflow/viewrequest.php?requestid=$requestid&cmid=$cmid"))->out(false); // A relevant URL for the notification
        $message->contexturlname = 'View the Request'; // Link title explaining where users get to for the contexturl

        $request = $DB->get_record('workflow_request', array('requestid'=>$requestid));
        $studentid = $request->studentid;
        $courseid = $DB->get_record('course_modules', array('id'=>$cmid))->course;
        $coursename = $DB->get_record('course', array('id'=> $courseid))->fullname;
        if($request->requesttype == "Extend Deadline"){
            $requestextend = $DB->get_record('workflow_request_extend', array('requestid'=>$requestid));
            if ($requestextend->assessmenttype == "Quiz") {
                $assessment = $DB->get_record('quiz', array('id'=>$requestextend->assessmentid))->name;

            } elseif ($requestextend->assessmenttype == "Assignment") {
                $assessment = $DB->get_record('assign', array('id'=>$requestextend->assessmentid))->name;
            }

            if($value == "approve"){
                if($request->isbatchrequest == 1) {
                    $studentids = $DB->get_fieldset_select('role_assignments', 'userid', 'contextid = :contextid and roleid=:roleid', [
                        'contextid' => $contextid,
                        'roleid' => '5',
                    ]);
                    foreach ($studentids as $id) {
                        if($studentid === $id) {
                            $message = new \core\message\message();
                            $message->component = 'mod_workflow'; // Your plugin's name
                            $message->name = 'workflow_notification'; // Your notification name from message.php
                            $message->userfrom = $USER; // If the message is 'from' a specific user you can set them here
                            $message->userto = $id;
                            $message->contexturl = (new \moodle_url("/mod/workflow/viewrequest.php?requestid=$requestid&cmid=$cmid"))->out(false); // A relevant URL for the notification
                            $message->contexturlname = 'View the Request'; // Link title explaining where users get to for the contexturl
                            $message->subject = 'Request Approved';
                            $msg = "Your request for extending deadline of $requestextend->assessmenttype, $assessment in $coursename module has been approved by the lecturer. The new deadline has been updated in the moodle";
                            $message->fullmessage = $msg;
                            $message->fullmessageformat = FORMAT_MARKDOWN;
                            $message->fullmessagehtml = '<p>'.$msg.'</p>';
                            $message->smallmessage = $msg;
                            $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
                            $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
                            $message->set_additional_content('email', $content);

                            $messageid = message_send($message);
                        }else{
                            $message = new \core\message\message();
                            $message->component = 'mod_workflow'; // Your plugin's name
                            $message->name = 'workflow_notification'; // Your notification name from message.php
                            $message->userfrom = $USER; // If the message is 'from' a specific user you can set them here
                            $message->userto = $id;
                            $message->subject = 'Deadline Extended';
                            $msg = "The deadline of $requestextend->assessmenttype, $assessment in $coursename module has been extended by the lecturer. The new deadline has been updated in the moodle";
                            $message->fullmessage = $msg;
                            $message->contexturl = '';
                            $message->contexturlname = ''; // Link title explaining where users get to for the contexturl
                            $message->fullmessage = $msg;
                            $message->fullmessageformat = FORMAT_MARKDOWN;
                            $message->fullmessagehtml = '<p>'.$msg.'</p>';
                            $message->smallmessage = $msg;
                            $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
                            $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
                            $message->set_additional_content('email', $content);

                            $messageid = message_send($message);
                        }
                    }
                }elseif($request->isbatchrequest == 0) {
                    $message->subject = 'Request Approved';
                    $msg = "Your request for extending deadline of $requestextend->assessmenttype, $assessment in $coursename module has been approved by the lecturer. The new deadline has been updated in the moodle";
                    $message->fullmessage = $msg;
                }
            }elseif ($value == "disapprove"){
                $message->subject = 'Request Disapproved';
                $msg = "Your request for extending deadline of $requestextend->assessmenttype, $assessment in $coursename module has been disapproved by the lecturer.";
                $message->fullmessage = $msg;
            }elseif ($value == "cancelIns"){
                $message->subject = 'Request Cancelled';
                $msg = "Your request for extending deadline of $requestextend->assessmenttype, $assessment in $coursename module has been cancelled by the instructor without forwarding to the lecturer.";
                $message->fullmessage = $msg;
            }elseif ($value == "forward"){
                $role = $DB->get_record('role_assignments',array('userid'=>$usertoid));
                $message->subject = 'Request Forwarded';
                if($role->roleid === "5") {
                    $msg = "Your request for extending deadline of $requestextend->assessmenttype, $assessment in $coursename module has been forwarded to the lecturer by the instructor.";
                    $message->fullmessage = $msg;
                }else{
                    $msg = "A request for extending deadline of $requestextend->assessmenttype, $assessment in $coursename module has been forwarded to you by the instructor.";
                    $message->fullmessage = $msg;
                }
            }
        }else{
            if($value == "approve"){
                $message->subject = 'Request Approved';
                $msg = "Your request for recorrection in $coursename module has been approved by the lecturer.";
                $message->fullmessage = $msg;
            }elseif ($value == "disapprove"){
                $message->subject = 'Request Disapproved';
                $msg = "Your request for recorrection in $coursename module has been disapproved by the lecturer.";
                $message->fullmessage = $msg;
            }elseif ($value == "cancelIns"){
                $message->subject = 'Request Cancelled';
                $msg = "Your request for recorrection in $coursename module has been cancelled by the instructor without forwarding to the lecturer.";
                $message->fullmessage = $msg;
            }elseif ($value == "forward"){
                $role = $DB->get_record('role_assignments',array('userid'=>$usertoid));
                $message->subject = 'Request Forwarded';
                if($role->roleid === "5") {
                    $msg = "Your request for recorrection in $coursename module has been forwarded to the lecturer by the instructor.";
                    $message->fullmessage = $msg;
                }else{
                    $msg = "A request for recorrection in $coursename module has been forwarded to the you by the instructor.";
                    $message->fullmessage = $msg;
                }
            }
        }

        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>'.$msg.'</p>';
        $message->smallmessage = $msg;
        $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
        $message->set_additional_content('email', $content);

        if($request->isbatchrequest == 0) {
            $messageid = message_send($message);
        }elseif($value == 'forward'){
            $messageid = message_send($message);
        }elseif($value == 'cancelIns'){
            $messageid = message_send($message);
        }elseif($value == 'disapprove'){
            $messageid = message_send($message);
        }
    }

    public function sendCreate($usertoid, $msg, $cmid, $requestid){
        global $USER;

        $message = new \core\message\message();
        $message->component = 'mod_workflow'; // Your plugin's name
        $message->name = 'workflow_notification'; // Your notification name from message.php
        $message->userfrom = $USER; // If the message is 'from' a specific user you can set them here
        $message->userto = $usertoid;
        $message->subject = 'Request Received';
        $message->fullmessage = $msg;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>'.$msg.'</p>';
        $message->smallmessage = $msg;
        $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
        $message->contexturl = (new \moodle_url("/mod/workflow/viewrequest.php?requestid=$requestid&cmid=$cmid"))->out(false); // A relevant URL for the notification
        $message->contexturlname = 'View the Request'; // Link title explaining where users get to for the contexturl
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
        $message->set_additional_content('email', $content);

        $messageid = message_send($message);
    }

    public function sendAskedMore($usertoid, $msg, $cmid, $requestid){
        global $USER;

        $message = new \core\message\message();
        $message->component = 'mod_workflow'; // Your plugin's name
        $message->name = 'workflow_notification'; // Your notification name from message.php
        $message->userfrom = $USER; // If the message is 'from' a specific user you can set them here
        $message->userto = $usertoid;
        $message->subject = 'Further details required';
        $message->fullmessage = $msg;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>'.$msg.'</p>';
        $message->smallmessage = $msg;
        $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
        $message->contexturl = (new \moodle_url("/mod/workflow/viewrequest.php?requestid=$requestid&cmid=$cmid"))->out(false); // A relevant URL for the notification
        $message->contexturlname = 'View the Request'; // Link title explaining where users get to for the contexturl
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
        $message->set_additional_content('email', $content);

        $messageid = message_send($message);
    }


    public function receivedMore($usertoid, $msg, $cmid, $requestid){
        global $USER;

        $message = new \core\message\message();
        $message->component = 'mod_workflow'; // Your plugin's name
        $message->name = 'workflow_notification'; // Your notification name from message.php
        $message->userfrom = $USER; // If the message is 'from' a specific user you can set them here
        $message->userto = $usertoid;
        $message->subject = 'Further details received';
        $message->fullmessage = $msg;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>'.$msg.'</p>';
        $message->smallmessage = $msg;
        $message->notification = 1; // Because this is a notification generated from Moodle, not a user-to-user message
        $message->contexturl = (new \moodle_url("/mod/workflow/viewrequest.php?requestid=$requestid&cmid=$cmid"))->out(false); // A relevant URL for the notification
        $message->contexturlname = 'View the Request'; // Link title explaining where users get to for the contexturl
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
        $message->set_additional_content('email', $content);

        $messageid = message_send($message);
    }

}
