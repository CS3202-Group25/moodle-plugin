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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot."/mod/workflow/classes/form/askfurther.php");
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/messagesender.php');

global $DB,$USER;

$requestId=$_GET["requestid"];

$PAGE->set_url(new moodle_url('/mod/workflow/askfurther.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Ask for Further Details - request ".$requestId);

require_login();

$user_role=($DB->get_record_sql("SELECT * FROM {role_assignments} WHERE userid=".$USER->id))->roleid;

if ($user_role != '4') {
    redirect($CFG->wwwroot.'/my',"You are not allowed to do that!");
}

$form1=new askFurther();
$form1->setReqID($requestId);

$requestController = new requestController();
$messagesender = new \mod_workflow\messageSender();

echo $OUTPUT->header();

$templatecontext=(object)[
    'description'=>"Ask a student for further details about the selected request."
];

echo $OUTPUT->render_from_template("mod_workflow/ask_further",$templatecontext);

if($form1->is_cancelled()){
    redirect($CFG->wwwroot.'/my',"You cancelled asking for details!");
}else if($formdata=$form1->get_data()){
    $reqID=$formdata->reqID;
    $requestController->confirmInquiry($reqID);
    $receiver = $DB->get_record_sql("SELECT * FROM mdl_workflow_request WHERE requestid=".$reqID)->studentid;
    $cmid = optional_param('cmid', true, PARAM_INT);
    $messagesender->sendAskedMore($receiver,"Further details about $reqID are required.",$cmid,$reqID);
    redirect($CFG->wwwroot.'/my',"Inquiry sent!");
}

$form1->display();

echo $OUTPUT->footer();
?>