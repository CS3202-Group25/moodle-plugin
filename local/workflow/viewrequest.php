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


global $DB, $OUTPUT, $PAGE, $USER, $CFG;

require_once (__DIR__ . '/../../config.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/dbcontroller.php');

$PAGE->set_url(new moodle_url('/mod/workflow/viewrequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("View a Request");

require_login();

$requestController = new requestController();
$dbController = new dbController();

$requestid = required_param('requestid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);

$request = $requestController->getRequest($requestid);
$sender = $dbController->getUsersById($request->studentid);
$sentdate = userdate($request->sentdate);
$role = $dbController->getRoleById($USER->id);
//$picture = $DB->get_record('files', array('id'=>69));

if ($sender->picture != 0) {
    $file = $DB->get_record('files', array('id'=>$sender->picture));
    $photourl = moodle_url::make_pluginfile_url($file->contextid, $file->component, $file->filearea, $file->itemid, $file->filepath, $file->filename, false);   
}else{
    $photourl = new moodle_url('/theme/image.php/boost/core/1668406882/u/f2');
}

// $files = $DB->get_records('files', array('itemid' => $request->filesid));
$sql = "SELECT * FROM {files} WHERE itemid = :itemid AND filesize != 0";
$files = $DB->get_records_sql($sql, ['itemid' => $request->filesid]);


$filesurl = [];
foreach ($files as $key => $value) {
    $url = moodle_url::make_pluginfile_url($value->contextid, $value->component, $value->filearea, $value->itemid, $value->filepath, $value->filename, false);
    array_push($filesurl, $url);
}



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
    'requestId' => $requestid,
    'cmid'=> $cmid,
    'photourl' => $photourl,
    'filesurl' => array_values($filesurl)
];

echo $OUTPUT->render_from_template('mod_workflow/workflow_heading', $templateContent);

echo $OUTPUT->render_from_template('mod_workflow/view_request', $viewRequestContent);

echo $OUTPUT->footer();
