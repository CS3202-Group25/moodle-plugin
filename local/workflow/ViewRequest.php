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


global $DB;
require_once (__DIR__ . '/../../config.php');

$PAGE->set_url(new moodle_url('/local/workflow/viewRequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("View a Request");

//$role = $DB->get_record('role', array('shortname' => 'teacher'));
//$context = get_context_instance(CONTEXT_COURSE, 4);
//$teachers = get_role_users($role->id, $context);

$roleId = $DB->get_record('role_assignments',array('userid'=>$USER->id), "roleid");


if($roleId->roleid === "4"){
    $buttons = array(
        array(
            'btnId' => 'forward',
            'btnValue' => 'Forward',
    ),
        array(
            'btnId' => 'cancel',
            'btnValue' => 'Cancel',
    ));
}elseif($roleId->roleid === "3"){
    $buttons = array(
        array(
            'btnId' => 'approve',
            'btnValue' => 'Approve',
        ),
        array(
            'btnId' => 'disapprove',
            'btnValue' => 'Disapprove',
        ));
}else{
    $buttons = array(
        array(
            'btnId' => 'cancel',
            'btnValue' => 'Cancel',
        ));
}

echo $OUTPUT->header();

$templateContent = (object) [
    'title' => 'View a Request',
];

$viewRequestContent = (object) [

    'description' => 'Please note that Lab 1 in CS2022 - Data Structures & Algorithms will be held today (Thursday, 17th December) at 1 PM through Zoom and Hackerrank . Attendance is compulsory for the Lab sessions. For more details: refer Lab1 section in Moodle',

    'buttons' => $buttons,

//    'test' => array_values($teachers)[0]->firstname

];

echo $OUTPUT->render_from_template('local_workflow/workflow_heading', $templateContent);

echo $OUTPUT->render_from_template('local_workflow/view_request', $viewRequestContent);

echo $OUTPUT->footer();
