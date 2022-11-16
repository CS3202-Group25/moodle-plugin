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

require_once("$CFG->libdir/formslib.php");

class provideFurther extends moodleform{
    public function definition(){
        global $CFG;

        $mform=$this->_form;

        $mform->addElement('textarea','reqID','Request ID',);
        $mform->setType('reqID',PARAM_NOTAGS);
        
        $mform->addElement('textarea','details','More Details',);
        $mform->setType('details',PARAM_NOTAGS);

        $mform->addElement('filepicker','files','Select Files');

        $this->add_action_buttons(true,"Send Details");
    }

    function validation($data, $files) {
        return array();
    }
}
?>