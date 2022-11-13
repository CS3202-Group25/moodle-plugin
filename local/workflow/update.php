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

global $DB, $CFG;

require_once (__DIR__ . '/../../config.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');

require_login();

$value = $_GET["value"];
$requestid = required_param('requestid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);

$workflowid = $DB->get_record('workflow_request', array('requestid' => $requestid))->workflowid;
$lecturerid = $DB->get_record('workflow', array('id'=>$workflowid))->lecturerid;
$requestController = new requestController();

if($value === 'cancel') {
    $requestController->deleteRequest($requestid);
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have successfully deleted the request");
}elseif ($value === 'approve'){
    $requestController->changeStatus($requestid, 'Approved');
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have approved the request");
}elseif($value === 'disapprove'){
    $requestController->changeStatus($requestid, 'Disapproved');
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have approved the request");
}elseif($value === 'forward'){
    $requestController->changeStatus($requestid, 'Forwarded');
    $requestController->changeReceiver($requestid, $lecturerid);
    redirect($CFG->wwwroot . '/mod/workflow/view.php?id=' . $cmid, "You have forwarded the request to lecturer");
}