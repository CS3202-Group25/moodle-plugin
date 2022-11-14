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

global $CFG;
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class createrequest extends moodleform {
    //Add elements to form
    public function definition() {
        global $DB, $COURSE;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->disable_form_change_checker();

        $cmid = optional_param('cmid',true, PARAM_INT);
        $courseid = $DB->get_record('course_modules', array('id'=>$cmid))->course;

//        $mform->addElement('static', 'course', 'Course'); // Add elements to your form.
////        $mform->addHelpButton('course', 'course', 'moodle', 'Hi', false);
//        $mform->setType('course', PARAM_NOTAGS);                   // Set type of element.
//        $mform->setDefault('course', 'Course1');        // Default value.

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('select', 'req_type', 'Request Type', array('Extend Deadline', 'Recorrection')); // Add elements to your form.
        $mform->setType('req_type', PARAM_NOTAGS);                   // Set type of element.
        // $mform->addRule('req_type', 'Missing Request Type', 'required', null, 'server');

        $mform->addElement('select', 'assessment_type', 'Assessment Type', array('Quiz', 'Assignment')); // Add elements to your form.
        $mform->setType('assessment_type', PARAM_NOTAGS);                   // Set type of element.
        $mform->hideIf('assessment_type', 'req_type', 'eq', 1);
        // $mform->addRule('assessment_type', 'Missing Assessment Type', 'required', null, 'server');

        $quizzes = $DB->get_records('quiz', array('course'=>$courseid));

        $quiznames[null] = 'None';
        foreach ($quizzes as $quiz) {
            $quiznames[$quiz->id] = $quiz->name;
        }
        $mform->addElement('select', 'assessment_quiz', 'Assessment', $quiznames); // Add elements to your form.
        $mform->setType('assessment_quiz', PARAM_NOTAGS);                   // Set type of element.
        $mform->hideIf('assessment_quiz', 'req_type', 'eq', 1);
        $mform->hideIf('assessment_quiz', 'assessment_type', 'eq', 1);

        $assignments = $DB->get_records('assign', array('course'=>$courseid));

        $assignmentnames[null] = 'None';
        foreach ($assignments as $assignment) {
            $assignmentnames[$assignment->id] = $assignment->name;
        }
        $mform->addElement('select', 'assessment_assign', 'Assessment', $assignmentnames); // Add elements to your form.
        $mform->setType('assessment_assign', PARAM_NOTAGS);                   // Set type of element.
        $mform->hideIf('assessment_assign', 'req_type', 'eq', 1);
        $mform->hideIf('assessment_assign', 'assessment_type', 'eq', 0);

        // $mform->addElement('advcheckbox', 'isbatchreq', 'Is this a batch request', 'Yes', array('group' => 1), array(0, 1));

        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'yesno', '', get_string('yes'), 1, 'yes');
        $radioarray[] = $mform->createElement('radio', 'yesno', '', get_string('no'), 0, 'no');
        $mform->addGroup($radioarray, 'isbatchreq', 'Is this a batch request', array(' '), false);
        $mform->hideIf('isbatchreq', 'req_type', 'eq', 1);

      

//        $mform->addElement('text', 'index_no', 'Index No'); // Add elements to your form.
//        $mform->setType('index_no', PARAM_NOTAGS);                   // Set type of element.
//        $mform->addRule('index_no', 'Missing Index', 'required', null, 'server');
        // $mform->hideIf('index_no', 'isbatchreq', 'eq', 1);
    //    $mform->setDefault('index_no', 'Please enter index no.');        // Default value.
       
        $mform->addElement('date_time_selector', 'extend_time', 'Extend Time');
        $mform->addElement('text', 'extend_time', 'Extend Time'); // Add elements to your form.
        $mform->setType('extend_time', PARAM_NOTAGS);                   // Set type of element.
        $mform->setDefault('extend_time', 'Please enter extend time.');        // Default value.
        $mform->hideIf('extend_time', 'req_type', 'eq', 1);

        // $mform->addElement('duration', 'timelimit', get_string('timelimit', 'quiz'));
        // $mform->setType('timelimit', PARAM_NOTAGS);                   // Set type of element.
        // $mform->addRule('timelimit', 'Missing Time Limit', 'required', null, 'server');        

        $mform->addElement('textarea', 'reason', "Reason", 'wrap="virtual" rows="5" cols="130"');
        $mform->setType('reason', PARAM_NOTAGS);                   // Set type of element.
        $mform->addRule('reason', 'Missing Reason', 'required', null, 'server');
//        $mform->setDefault('reason', 'Please enter reason');        // Default value.

        // $data = $this->_customdata['data'];
        // $options = $this->_customdata['options'];

        // $options = array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => 1, 'accepted_types' => '*',
        // 'areamaxbytes' => 10485760);

        $options = array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => -1, 'accepted_types' => '*');

        $mform->addElement('filemanager', 'files_filemanager', get_string('files'), null, $options);
        $mform->setType('file_manager', PARAM_LOCALURL);

        $this->add_action_buttons(true, get_string('savechanges'));

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}