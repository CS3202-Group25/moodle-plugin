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

require_once (__DIR__ . '/../../config.php');
//require_once ($CFG->dirroot . '/local/workflow/classes/form/viewRequest.php');

$PAGE->set_url(new moodle_url('/local/workflow/viewRequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("View a Request");

//$mform = new establishWorkflow();

echo $OUTPUT->header();

$templateContent = (object) [
    'title' => 'View a Request',
];

$viewRequestContent = (object) [
    'profilePic' => '/local/images/Fb_Dp.jpg',
//    'sender' => ,

    'description' => 'Please note that Lab 1 in CS2022 - Data Structures & Algorithms will be held today (Thursday, 17th December) at 1 PM through Zoom and Hackerrank . Attendance is compulsory for the Lab sessions. For more details: refer Lab1 section in Moodle',

    'buttons' => array_values(array(
        1 => array(
            'btnId' => 'forward',
            'btnValue' => 'Forward',
        ),
        2 => array(
            'btnId' => 'approve',
            'btnValue' => 'Approve',
        ),
        3 => array(
            'btnId' => 'disapprove',
            'btnValue' => 'Disapprove',
        ),
        4 => array(
            'btnId' => 'cancel',
            'btnValue' => 'Cancel',
        ),
    )),
];

echo $OUTPUT->render_from_template('local_workflow/workflow_heading', $templateContent);

echo $OUTPUT->render_from_template('local_workflow/view_request', $viewRequestContent);


echo $OUTPUT->footer();