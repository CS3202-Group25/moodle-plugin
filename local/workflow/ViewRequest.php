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


global $DB, $OUTPUT, $PAGE, $USER, $CFG;

require_once (__DIR__ . '/../../config.php');
require_once ($CFG->dirroot . '/local/workflow/classes/requestController.php');
require_once ($CFG->dirroot . '/local/workflow/classes/dbController.php');

$PAGE->set_url(new moodle_url('/local/workflow/viewRequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("View a Request");

require_login();

$requestController = new requestController();
$dbController = new dbController();

//$role = $DB->get_record('role', array('shortname' => 'teacher'));
//$context = get_context_instance(CONTEXT_COURSE, 4);
//$teachers = get_role_users($role->id, $context);

$requestid = $_GET["requestid"];

$request = $requestController->getRequest($requestid);
$sender = $dbController->getUsersById($request->studentid);
$sentdate = userdate($request->sentdate);
$role = $dbController->getRoleById($USER->id);
//$picture = $DB->get_record('files', array('id'=>69));

if($role->roleid === "4"){
    $buttons = array(
        array(
            'btnId' => 'forward',
            'btnValue' => 'Forward',
    ),
        array(
            'btnId' => 'cancel',
            'btnValue' => 'Cancel',
    ));
}elseif($role->roleid === "3"){
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
