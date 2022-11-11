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
require_once ($CFG->dirroot . '/local/workflow/classes/workflowController.php');

$PAGE->set_url(new moodle_url('/local/workflow/establishWorkflow.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Establish Workflow");

require_login();
// Do or display something.
$mform = new establishWorkflow();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/my', 'You cancelled the form');
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $workflowController = new workflowController();
    $courseid=($DB->get_record('course',array('shortname'=>$fromform->courseCode),'id'))->id;
    $instructorid = 5;
    $lecturerid =$USER->id;
    $startdate= $fromform->startDate;
    $enddate=$fromform->endDate;

    $workflowController->createWorkflow($courseid, $instructorid, $lecturerid, $startdate, $enddate);

//    $message = new stdClass();
//    $message->component = 'local_workflow'; // Your plugin's name
//    $message->name = 'Establish Workflow'; // Your notification name from message.php
//    $message->userfrom = core_user::get_noreply_user(); // If the message is 'from' a specific user you can set them here
//    $message->userto = ($DB->get_record('user',array('firstname'=>'Balara')))->id;
//    $message->subject = 'message subject 1';
//    $message->fullmessage = 'message body';
//    $message->fullmessageformat = FORMAT_MARKDOWN;
//    $message->fullmessagehtml = '<p>message body</p>';
//    $message->smallmessage = 'small message';
//    $message->notification = 0; // Because this is a notification generated from Moodle, not a user-to-user message
//    $message->contexturl = (new \moodle_url('/course/'))->out(false); // A relevant URL for the notification
//    $message->contexturlname = 'Course list'; // Link title explaining where users get to for the contexturl
//    $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
//    $message->set_additional_content('email', $content);
//
//    $messageid = message_send($message);

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

//$role = $DB->get_record('role', array('shortname' => 'teacher'));
//        $courseId = ($DB->get_record('course',array('shortname'=>($mform->getdata())->courseCode),'id'))->id;
//        var_dump($courseId);
//        die;
//        $context = get_context_instance(CONTEXT_COURSE, $courseId->id);
//        $instructorObjects = get_role_users($role->id, $context);

