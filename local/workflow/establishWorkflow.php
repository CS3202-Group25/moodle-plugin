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
require_once ($CFG->dirroot . '/local/workflow/classes/form/establishWorkflow.php');

$PAGE->set_url(new moodle_url('/local/workflow/establishWorkflow.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Establish Workflow");

$mform = new establishWorkflow();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . '/my', 'You cancelled the form');
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $recordToInsert = new stdClass();
    $recordToInsert->courseCode =$fromform->courseCode;
    $recordToInsert->instructor =$fromform->instructo;
    $recordToInsert->semester =$fromform->semester;
    $recordToInsert->intake =$fromform->intake;

    $DB->insert_record(workflow, $recordToInsert);
}

echo $OUTPUT->header();

$templateContent = (object) [
    'title' => 'Establish Workflow'
];

echo $OUTPUT->render_from_template('local_workflow/workflow_heading', $templateContent);

$mform->display();

echo $OUTPUT->footer();