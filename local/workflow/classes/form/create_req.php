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

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class createrequest extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('static', 'course', 'Course'); // Add elements to your form.
//        $mform->addHelpButton('course', 'course', 'moodle', 'Hi', false);
        $mform->setType('course', PARAM_NOTAGS);                   // Set type of element.
        $mform->setDefault('course', 'InXX-SX-XX0000');        // Default value.


        $mform->addElement('static', 'assessment', 'Assessment'); // Add elements to your form.
        $mform->setType('assessment', PARAM_NOTAGS);                   // Set type of element.
        $mform->setDefault('assessment', 'Quiz 01');        // Default value.

        $mform->addElement('text', 'index_no', 'Index No'); // Add elements to your form.
        $mform->setType('index_no', PARAM_NOTAGS);                   // Set type of element.
        $mform->addRule('index_no', 'Missing Index', 'required', null, 'server');
//        $mform->setDefault('index_no', 'Please enter index no.');        // Default value.

        $mform->addElement('date_time_selector', 'extend_time', 'Extend Time');
//        $mform->addElement('text', 'extend_time', 'Extend Time'); // Add elements to your form.
        $mform->setType('extend_time', PARAM_NOTAGS);                   // Set type of element.
//        $mform->setDefault('extend_time', 'Please enter extend time.');        // Default value.

        $mform->addElement('textarea', 'reason', "Reason", 'wrap="virtual" rows="5" cols="130"');
        $mform->setType('reason', PARAM_NOTAGS);                   // Set type of element.
        $mform->addRule('reason', 'Missing Reason', 'required', null, 'server');
//        $mform->setDefault('reason', 'Please enter reason');        // Default value.

        $data = $this->_customdata['data'];
        $options = $this->_customdata['options'];

        $mform->addElement('filemanager', 'files_filemanager', get_string('files'), null, $options);
        $mform->addElement('hidden', 'returnurl', $data->returnurl);
        if (isset($data->emaillink)) {
            $emaillink = html_writer::link(new moodle_url('mailto:' . $data->emaillink), $data->emaillink);
            $mform->addElement('static', 'emailaddress', '',
                get_string('emailtoprivatefiles', 'moodle', $emaillink));
        }
        $mform->setType('returnurl', PARAM_LOCALURL);


//        $options = array('subdirs' => 1, 'maxbytes' => $maxbytes, 'maxfiles' => -1, 'accepted_types' => '*',
//            'areamaxbytes' => $maxareabytes);
//        file_prepare_standard_filemanager($mform, 'files', $options, $context, 'user', 'private', 0);

        $this->add_action_buttons(true, get_string('savechanges'));

        $this->set_data($data);

        echo $data;

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}