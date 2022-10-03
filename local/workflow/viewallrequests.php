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

require_once(__DIR__ . '/../../config.php');

global $DB, $USER;

$PAGE->set_url(new moodle_url('/mod/workflow/viewallrequests.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('View All Requests');

require_login();

// $courseid = '2';
$workflowid = '2';

$sql = 'SELECT shortname FROM {role} r JOIN {role_assignments} ra ON r.id = ra.roleid WHERE ra.userid = :userid';
$role = $DB->get_record_sql($sql, ['userid' => $USER->id]);


if ($role->shortname == 'student') {
    $header = array(1=>'Request ID', 2=>'Request Type', 3=>'Received By', 4=>'Status');

    $sql = "SELECT requestid, requesttype, receivedby, state FROM {workflow_request} WHERE studentid = :studentid AND workflowid = :workflowid";
    $requests = $DB->get_records_sql($sql, ['studentid' => $USER->id, 'workflowid' => $workflowid]);

    $receivers = array();
    foreach ($requests as $key => $value) {
        $receiverid = $DB->get_record_sql("SELECT roleid FROM {role_assignments} ra JOIN {workflow_request} lwr ON ra.userid = lwr.receivedby AND lwr.receivedby = :receiver", ['receiver' => $value->receivedby]);
        $receiver = $DB->get_record_sql("SELECT shortname FROM {role} WHERE id = :roleid", ['roleid' => $receiverid->roleid]);
        
        $requests[$key]->receivedby = ucfirst($receiver->shortname);
    }

}else if ($role->shortname == 'teacher') {
    $header = array(1=>'Request ID', 2=>'Request Type', 3=>'Index no.', 4=>'Status');

    $sql = "SELECT requestid, requesttype, studentid, state FROM {workflow_request} WHERE receivedby = :instructorid AND workflowid = :workflowid AND state = 'pending'";
    $requests = $DB->get_records_sql($sql, ['instructorid' => $USER->id, 'workflowid' => $workflowid]);

} else if ($role->shortname == 'editingteacher') {
    $header =array(1=>'Request ID', 2=>'Request Type', 3=>'Index no.', 4=>'Status');

    $sql = "SELECT requestid, requesttype, studentid, state FROM {workflow_request} WHERE receivedby = :lecturerid AND workflowid = :workflowid AND state <> 'pending'";
    $requests = $DB->get_records_sql($sql, ['lecturerid' => $USER->id, 'workflowid' => $workflowid]);
}

echo $OUTPUT->header();

$templatecontent = (object) [
    'title' => 'View All Requests'
];

$templatecontent_table = (object) [
    'requests' => array_values($requests),
    'headers' => array_values($header),
];

echo $OUTPUT->render_from_template('mod_workflow/workflow_heading', $templatecontent);

echo$OUTPUT->render_from_template('mod_workflow/request_table', $templatecontent_table);

echo $OUTPUT->footer();
