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


global $DB, $OUTPUT, $PAGE, $USER;
require_once (__DIR__ . '/../../config.php');

$PAGE->set_url(new moodle_url('/local/workflow/viewRequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("View a Request");

require_login();

//$role = $DB->get_record('role', array('shortname' => 'teacher'));
//$context = get_context_instance(CONTEXT_COURSE, 4);
//$teachers = get_role_users($role->id, $context);

$requestId = $_GET["requestid"];
$request = $DB->get_record('local_workflow_request',array('requestid'=>$requestId));
$sender = $DB->get_record('user',array('id'=>$request->studentid));
//$sentdate = date('l jS \of F Y h:i:s A', $sender->sentdate);
$sentdate = userdate($request->sentdate);
$roleId = $DB->get_record('role_assignments',array('userid'=>$USER->id), "roleid");
//$picture = $DB->get_record('files', array('id'=>69));

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

//    'profilePic' => $picture->contenthash,
    'date' => $sentdate,
    'name' => "{$sender->firstname} {$sender->lastname}",
    'description' => $request->reason,
    'buttons' => $buttons,
    'requestId' => $requestId

];

echo $OUTPUT->render_from_template('local_workflow/workflow_heading', $templateContent);

echo $OUTPUT->render_from_template('local_workflow/view_request', $viewRequestContent);

echo $OUTPUT->footer();
