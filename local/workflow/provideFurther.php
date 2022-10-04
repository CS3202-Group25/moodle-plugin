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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot."/local/workflow/classes/form/provideFurther.php");

global $DB;

$requestId=$_GET["requestId"];

$PAGE->set_url(new moodle_url('/local/workflow/provideFurther.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Send More Details - request ".$requestId);

require_login();

$user_role=($DB->get_record_sql("SELECT * FROM mdl_role_assignments WHERE userid=".$USER->id))->roleid;

if ($user_role != '5') {
    redirect($CFG->wwwroot.'/my',"You are not allowed to do that!");
}

$form1=new provideFurther();

echo $OUTPUT->header();

$templatecontext=(object)[
    'description'=>"Ask a student for further details about the selected request.",
];

echo $OUTPUT->render_from_template("local_workflow/provideFurther",$templatecontext);

if($form1->is_cancelled()){
    redirect($CFG->wwwroot.'/my',"You cancelled sending details!");
}else{
    $DB->execute("UPDATE mdl_local_workflow_request SET commentlecturer=".$USER->id." WHERE requestid=".$requestId);
}

$form1->display();

echo $OUTPUT->footer();
?>