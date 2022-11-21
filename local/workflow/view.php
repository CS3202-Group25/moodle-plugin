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
 * Activity view page for the plugintype_pluginname plugin.
 *
 * @package   mod_workflow
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

global $DB, $OUTPUT, $PAGE, $USER, $CFG;

require_login();

$id = required_param('id', PARAM_INT);
[$course, $cm] = get_course_and_cm_from_cmid($id, 'workflow');
$workflow = $DB->get_record('workflow', ['id'=> $cm->instance], '*', MUST_EXIST);
$context = context_course::instance($course->id);

$PAGE->set_context(\context_system::instance());
$PAGE->set_title($workflow->name);
$PAGE->set_heading($workflow->name);
$PAGE->set_cm($cm, $course);

$roleid = $DB->get_record('role_assignments', array('contextid'=>$context->id, 'userid'=>$USER->id)) -> roleid;

$rolename = $DB->get_record('role', array('id'=>$roleid)) -> shortname;

$create_capability = has_capability('mod/workflow:createrequest', $context);
$forward_capability = has_capability('mod/workflow:forwardrequest', $context);
$approve_capability = has_capability('mod/workflow:approverequest', $context);

echo $OUTPUT->header();

$templatecontent = (object) [
    'title' => $workflow->name
];

echo $OUTPUT->render_from_template('mod_workflow/workflow_heading', $templatecontent);

if($create_capability){
    $header = array(1=>'Request ID', 2=>'Request Type', 3=>'Received By', 4=>'Status');

    $sql = "SELECT requestid, requesttype, receivedby, state FROM {workflow_request} WHERE studentid = :studentid AND workflowid = :workflowid";
    $requests = $DB->get_records_sql($sql, ['studentid' => $USER->id, 'workflowid' => $workflow->id]);

    $receivers = array();

    foreach ($requests as $key => $value) {
        $receiverid = $DB->get_record_sql("SELECT roleid FROM {role_assignments} ra JOIN {workflow_request} lwr ON ra.userid = lwr.receivedby AND lwr.receivedby = :receiver", ['receiver' => $value->receivedby]);
        $receiver = $DB->get_record_sql("SELECT shortname FROM {role} WHERE id = :roleid", ['roleid' => $receiverid->roleid]);
        $requests[$key]->receivedby = ucfirst($receiver->shortname);


        if ($receiver->shortname == "teacher") {
            $requests[$key]->receivedby = "Instructor";
        }
        if ($receiver->shortname == "editingteacher") {
            $requests[$key]->receivedby = "Teacher";
        }

    }

    if(sizeof($requests) != 0) {
        $templatecontent_table = (object)[
            'requests' => array_values($requests),
            'headers' => array_values($header),
            'cmid' => $cm->id,
            'buttons' => array(
                array(
                    'btnId' => 'create_req',
                    'btnValue' => 'Create a New Request',
                ))
        ];

        echo $OUTPUT->render_from_template('mod_workflow/request_table', $templatecontent_table);
    }else{
        $templatecontent_table = (object)[
            'cmid' => $cm->id,
            'buttons' => array(
                array(
                    'btnId' => 'create_req',
                    'btnValue' => 'Create a New Request',
                ))
        ];
        echo '<p style="text-align: center;margin-top:25px;">No sent requests</p>';

        echo $OUTPUT->render_from_template('mod_workflow/request_table', $templatecontent_table);
    }
}
elseif($forward_capability){
    if($USER -> id == $workflow -> instructorid) {
        $header = array(1 => 'Request ID', 2 => 'Request Type', 3 => 'Index no.', 4 => 'Status');

        $sql = "SELECT requestid, requesttype, studentid, state FROM {workflow_request} WHERE receivedby = :instructorid AND workflowid = :workflowid AND (state = 'Pending' OR state = 'Asked More Details' OR state = 'More Details Added')";
        $requests = $DB->get_records_sql($sql, ['instructorid' => $USER->id, 'workflowid' => $workflow->id]);

        if (sizeof($requests) != 0) {
            $templatecontent_table = (object)[
                'requests' => array_values($requests),
                'headers' => array_values($header),
                'cmid' => $cm->id,
            ];

            echo $OUTPUT->render_from_template('mod_workflow/request_table', $templatecontent_table);
        } else {
            echo '<p style="text-align: center;margin-top:25px;">No received requests</p>';
        }
    }else{
        echo '<p style="text-align: center;margin-top:25px;">You are not assigned to this workflow</p>';
    }
}
elseif($approve_capability){
    $header =array(1=>'Request ID', 2=>'Request Type', 3=>'Index no.', 4=>'Status');

    $sql = "SELECT requestid, requesttype, studentid, state FROM {workflow_request} WHERE receivedby = :lecturerid AND workflowid = :workflowid AND state = 'Forwarded'";
    $requests = $DB->get_records_sql($sql, ['lecturerid' => $USER->id, 'workflowid' => $workflow->id]);

    if(sizeof($requests) != 0) {
        $templatecontent_table = (object)[
            'requests' => array_values($requests),
            'headers' => array_values($header),
            'cmid' => $cm->id,
        ];

        echo $OUTPUT->render_from_template('mod_workflow/request_table', $templatecontent_table);
    }else {
        echo '<p style="text-align: center;margin-top:25px;">No received requests</p>';
    }
}

echo $OUTPUT->footer();




