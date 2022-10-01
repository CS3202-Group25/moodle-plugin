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

global $DB, $USER, $OUTPUT, $PAGE, $CFG;
require_once (__DIR__ . '/../../config.php');
require_once ($CFG->dirroot . '/local/workflow/classes/form/establishWorkflow.php');

$PAGE->set_url(new moodle_url('/local/workflow/establishWorkflow.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Establish Workflow");

//$role = $DB->get_record('role', array('shortname' => 'teacher'));
//        $courseId = ($DB->get_record('course',array('shortname'=>($mform->getdata())->courseCode),'id'))->id;
//        var_dump($courseId);
//        die;
//        $context = get_context_instance(CONTEXT_COURSE, $courseId->id);
//        $instructorObjects = get_role_users($role->id, $context);

$mform = new establishWorkflow();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/my', 'You cancelled the form');
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $recordToInsert = new stdClass();
    $recordToInsert->courseid=($DB->get_record('course',array('shortname'=>$fromform->courseCode),'id'))->id;
    $recordToInsert->instructorid = 5;
    $recordToInsert->lecturerid =$USER->id;
    $recordToInsert->startdate= $fromform->startDate;
    $recordToInsert->enddate=$fromform->endDate;

    $DB->insert_record(local_workflow, $recordToInsert);

    $courseName = ($DB->get_record('course',array('shortname'=>$fromform->courseCode),'fullname'))->fullname;
    redirect($CFG->wwwroot . '/local/workflow/establishWorkflow.php', "You have successfully established a workflow for {$courseName}");
}

echo $OUTPUT->header();

$templateContent = (object) [
    'title' => 'Establish Workflow'
];

echo $OUTPUT->render_from_template('local_workflow/workflow_heading', $templateContent);

$mform->display();

echo $OUTPUT->footer();
