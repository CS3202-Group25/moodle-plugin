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

global $CFG, $DB,$PAGE, $OUTPUT, $USER;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot."/mod/workflow/classes/form/providefurther.php");
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/messagesender.php');

global $DB;

$requestId=optional_param('requestid', true, PARAM_INT);;
$cmid = optional_param('cmid', true, PARAM_INT);
[$course, $cm] = get_course_and_cm_from_cmid($cmid, 'workflow');

$PAGE->set_url(new moodle_url('/mod/workflow/providefurther.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Send More Details - Request ".$requestId);
$PAGE->set_heading("Send More Details - Request $requestId");
$PAGE->navbar->add("Send More Details - Request $requestId");
$PAGE->set_cm($cm, $course);

require_login();

$user_role=($DB->get_record_sql("SELECT * FROM {role_assignments} WHERE userid=".$USER->id))->roleid;

if ($user_role != '5') {
    redirect($CFG->wwwroot.'/my',"You are not allowed to do that!");
}

$form1=new provideFurther();
$form1->setReqID($requestId);

$requestController = new requestController();
$messagesender = new \mod_workflow\messageSender();

if($form1->is_cancelled()){
    redirect($CFG->wwwroot . '/mod/workflow/view.php?id=' . $cmid, "You cancelled sending details!");
}else if($formdata=$form1->get_data()){
    $reqID=$formdata->reqID;
    $details=$formdata->details;

    $sql = "SELECT id FROM {files} WHERE itemid = :itemid";
    $exist = $DB->record_exists_sql($sql, ['itemid' => $formdata->files]);

    if($exist == 1) {
        $filesid = $formdata->files;
        $filename = $form1->get_new_filename('files');
    } else {
        $filesid = NULL;
        $filename = '';
    }

    $thisdir = getcwd();
    $newdir = $filesid;
    mkdir($thisdir . "/files/" . $newdir, 0777, true);
    $success = $form1->save_file('files', $thisdir . "/files/" . $newdir . "/" . $filename, false);

    $requestController->updateDetails($reqID,$details,$filesid);

    $workflowid = $DB->get_record('course_modules', array('id'=>$cmid))->instance;
    $instructorid = $DB->get_record('workflow', array('id'=> $workflowid))->instructorid;
    $messagesender->receivedMore($instructorid,"Further details about $reqID are received.",$cmid,$reqID);

    redirect($CFG->wwwroot . '/mod/workflow/view.php?id=' . $cmid, 'You submitted send more details form successfully.');
}

echo $OUTPUT->header();

$temp = new stdClass();
$temp->cmid = $cmid;
$form1->set_data($temp);

$form1->display();

echo $OUTPUT->footer();
?>