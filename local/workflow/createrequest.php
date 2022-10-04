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

$PAGE->set_url(new moodle_url('/mod/workflow/createrequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Create Request');

require_login();

// get data from db
$workflowid = '2';
$instructorid = '5';
$assessment = array('Quiz'=>array('1'=>'Quiz 01', '2'=>'Quiz 02', '3'=>'Quiz 03'), 'Assignment'=>array('11'=>'Assignment 01', '12'=>'Assignment 02', '13'=>'Assignment 03'));

// display form
//Instantiate simplehtml_form
$mform = new createrequest(null, array('assessment'=>$assessment));

$requestController = new requestController();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/mod/workflow/student_index.php', 'You cancelled the create request form');
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.

    $studentid = $USER->id;
    
    if ($fromform->req_type == 0) {
        $requesttype = 'Extend Deadline';

        $extendtime = $fromform->extend_time;

        if ($fromform->assessment_type == 0) {
            $assessmenttype = 'Quiz';
            $assessmentid = (array_keys($assessment['Quiz'])[$fromform->assessment_quiz]);
        } else {
            $assessmenttype = 'Assignment';
            $assessmentid = (array_keys($assessment['Assignment'])[$fromform->assessment_assign]);
        }
    
        $isbatchrequest = $fromform->yesno;
        
    } else {
        $requesttype = 'Recorrection';
        $isbatchrequest = 0;
    }

//    $recordtoinsert->studentid = $fromform->index_no;
    $reason = $fromform->reason;

    $sql = "SELECT id FROM {files} WHERE itemid = :itemid";
    $exist = $DB->record_exists_sql($sql, ['itemid' => $fromform->files_filemanager]);  


    if($exist == 1) {
        $filesid = $fromform->files_filemanager;
    } else {
        $filesid = NULL;
    }
    $time = new DateTime("now", core_date::get_user_timezone_object());
    $sentdate = $time->getTimestamp();

    $askedmoredetails = 0;
    $state = 'Pending';
    $commentlecturer = NULL;
    $receivedby = 0;

    $requestid = $requestController->createRequest($workflowid, $studentid, $requesttype, $isbatchrequest, $reason, $filesid, $askedmoredetails, $commentlecturer, $sentdate, $receivedby);

    if ($fromform->req_type == 0) {
        $requestController->createRequestExtend($requestid, $assessmenttype, $assessmentid, $extendtime);
    }

} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

}

echo $OUTPUT->header();

$templatecontent = (object) [
    'title' => 'Create Request'
];

echo $OUTPUT->render_from_template('mod_workflow/workflow_heading', $templatecontent);

//displays the form
$mform->display();

echo $OUTPUT->footer();
