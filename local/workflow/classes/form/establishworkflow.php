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

global $PAGE, $CFG;
require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir . '/pagelib.php');
$PAGE->requires->js( new moodle_url($CFG->wwwroot . '/mod/mymod/script.js') );

class establishWorkflow extends moodleform {
    //Add elements to form
    public function definition() {
        global $DB;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'courseCode', 'Course Code'); // Add elements to your form.
        $mform->setType('courseCode', PARAM_NOTAGS);                   // Set type of element.

//        $sql = "select id from {course} where shortname = 'CS1032' ";
//        $test = $DB->get_records_sql($sql);

        $instructors = array();
        $instructors['0'] = "Aruna Senanayake";
        $mform->addElement('select', 'instructor', 'Instructor', $instructors); // Add elements to your form.

        $mform->addElement('date_selector', 'startDate', get_string('from'));

        $mform->addElement('date_selector', 'endDate', get_string('to'));

        $this->add_action_buttons();

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
