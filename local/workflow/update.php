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

$requestId = $_GET["requestid"];
$value = $_GET["value"];

// function changeStatus($newValue, $requestId, $field){
//     global $DB;
//     $needToUpdate = array_values($DB->get_record('workflow_request', array('requestid'=>$requestId), "requestid"));
//     $DB->set_field_select('workflow_request', $field, $newValue, "requestid $needToUpdate");
// }
$requestController = new requestController();

if($value === 'cancel') {
    $requestController->deleteRequest($requestId);
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have successfully deleted the request");
}elseif ($value === 'approve'){
    $requestController->changeStatus('approved', $requestId, 'state');
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have approved the request");
}elseif($value === 'disapprove'){
    $requestController->changeStatus('disapproved', $requestId, 'state');
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have approved the request");
}elseif($value === 'forward'){
    $requestController->changeStatus('forwarded', $requestId, 'state');
//    changeStatus('Lecturer', $requestId, 'receivedby');
    redirect($CFG->wwwroot . '/mod/workflow/viewallrequests.php', "You have forwarded the request to lecturer");
}