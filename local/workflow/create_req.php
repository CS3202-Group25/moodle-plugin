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
 */

require_once(__DIR__ . '/../../config.php');
//include create_req.php
require_once($CFG->dirroot . '/local/workflow/classes/form/create_req.php');

$PAGE->set_url(new moodle_url('/local/workflow/create_req.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Create Request');

// display form
//Instantiate simplehtml_form
$mform = new createrequest();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/local/workflow/student_index.php', 'You cancelled the create request form');
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $recordtoinsert = new stdClass();

    $recordtoinsert->index_no = $fromform->index_no;
    
    if ($fromform->req_type == 0) {
        $recordtoinsert->requesttype = 'Extend Deadline';
        $recordtoinsert->extendtime = $fromform->extend_time;
        $recordtoinsert->assessmenttype = $fromform->assessment_type;
        $recordtoinsert->assessmentname = $fromform->assessment;
    } else {
        $recordtoinsert->requestype = 'Recorrection';
    }


    $recordtoinsert->reason = $fromform->reason;
    $recordtoinsert->file = $fromform->files_filemanager;

    // var_dump($recordtoinsert);
    // die;
   
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.

}

echo $OUTPUT->header();

$templatecontent = (object) [
    'title' => 'Create Request'
];

echo $OUTPUT->render_from_template('local_workflow/workflow_heading', $templatecontent);

//displays the form
$mform->display();

echo $OUTPUT->footer();
