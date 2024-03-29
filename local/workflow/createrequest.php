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

global $CFG, $PAGE, $USER, $OUTPUT, $DB;
require_once(__DIR__ . '/../../config.php');
//include create_req.php
require_once($CFG->dirroot . '/mod/workflow/classes/form/createrequest.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/messagesender.php');

$cmid = optional_param('cmid', true, PARAM_INT);
[$course, $cm] = get_course_and_cm_from_cmid($cmid, 'workflow');

$PAGE->set_url(new moodle_url('/mod/workflow/createrequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Create Request');
$PAGE->set_heading('Create Request');
$PAGE->navbar->add('Create Request');
$PAGE->set_cm($cm, $course);

require_login();

$requestController = new requestController();
$messagesender = new \mod_workflow\messageSender();

$workflowid = $DB->get_record('course_modules', array('id'=>$cmid))->instance;
$instructorid = $DB->get_record('workflow', array('id'=> $workflowid))->instructorid;

//Instantiate simplehtml_form
$mform = new createrequest();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/mod/workflow/view.php?id=' . $cmid, 'You cancelled the create request form');
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $studentid = $USER->id;
    
    if ($fromform->req_type == 0) {
        $requesttype = 'Extend Deadline';

        $extendtime = $fromform->extend_time;

        if ($fromform->assessment_type == 0) {
            $assessmenttype = 'Quiz';
            $assessmentid = $fromform->assessment_quiz;
        } else {
            $assessmenttype = 'Assignment';
            $assessmentid = $fromform->assessment_assign;
        }
    
        $isbatchrequest = $fromform->yesno;
        
    } else {
        $requesttype = 'Recorrection';
        $isbatchrequest = 0;
    }

    $reason = $fromform->reason;

    $sql = "SELECT id FROM {files} WHERE itemid = :itemid";
    $exist = $DB->record_exists_sql($sql, ['itemid' => $fromform->files]);

    if($exist == 1) {
        $filesid = $fromform->files;
        $filename = $mform->get_new_filename('files');
    } else {
        $filesid = NULL;
        $filename = '';
    }

    $thisdir = getcwd();
    $newdir = $filesid;
    mkdir($thisdir . "/files/" . $newdir, 0777, true);
    $success = $mform->save_file('files', $thisdir . "/files/" . $newdir . "/" . $filename, false);

    $time = new DateTime("now", core_date::get_user_timezone_object());
    $sentdate = $time->getTimestamp();

    $askedmoredetails = 0;
    $state = 'Pending';
    $commentlecturer = NULL;
    $receivedby = $instructorid;

    $requestid = $requestController->createRequest($workflowid, $studentid, $requesttype, $isbatchrequest, $reason, $filesid, $askedmoredetails, $commentlecturer, $sentdate, $receivedby);

    if ($fromform->req_type == 0) {
        $requestController->createRequestExtend($requestid, $assessmenttype, $assessmentid, $extendtime);
    }

    $messagesender->sendCreate($instructorid, "You received a request $requestid of the type of $requesttype.", $cmid, $requestid);

    redirect($CFG->wwwroot . '/mod/workflow/view.php?id=' . $cmid, 'You submitted the create request form');

}

echo $OUTPUT->header();

$temp = new stdClass();
$temp->cmid = $cmid;
$mform->set_data($temp);

$mform->display();

echo $OUTPUT->footer();
