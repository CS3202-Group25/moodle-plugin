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

global $DB, $CFG;

require_once (__DIR__ . '/../../config.php');

require_login();

$requestId = $_GET["requestId"];
$value = $_GET["value"];

function changeStatus($newValue, $requestId, $field){
    global $DB;
    $needToUpdate = array_values($DB->get_record('local_workflow_request', array('requestid'=>$requestId), "requestid"));
    $DB->set_field_select('local_workflow_request', $field, $newValue, "requestid $needToUpdate");
}

if($value === 'cancel') {
    $deleteRequest = $DB->delete_records('local_workflow_request', ['requestid' => $requestId]);
    redirect($CFG->wwwroot . '/local/workflow/view_all_req.php', "You have successfully deleted the request");
}elseif ($value === 'approve'){
    changeStatus('Approved', $requestId, 'state');
    redirect($CFG->wwwroot . '/local/workflow/view_all_req.php', "You have approved the request");
}elseif($value === 'disapprove'){
    changeStatus('Disapproved', $requestId, 'state');
    redirect($CFG->wwwroot . '/local/workflow/view_all_req.php', "You have approved the request");
}elseif($value === 'forward'){
    changeStatus('Forwarded To Lecturer', $requestId, 'state');
//    changeStatus('Lecturer', $requestId, 'receivedby');
    redirect($CFG->wwwroot . '/local/workflow/view_all_req.php', "You have forwarded the request to lecturer");
}



