<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   mod_workflow
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $DB;
global $CFG;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/workflow/lib.php');

/**
 * Disabled workflow settings form.
 *
 * @package   mod_workflow
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_workflow_mod_form extends moodleform_mod {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        global $DB, $USER;

        $courseid = optional_param('course', true, PARAM_INT);
        $context = context_course::instance($courseid);
        $lecturerid = $USER->id;

        $mform = $this->_form;

        $mform->addElement('text', 'name', "Name");
        $mform->setDefault('name', "");
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $courseid);

        $mform->addElement('hidden', 'lecturerid');
        $mform->setType('lecturerid', PARAM_INT);
        $mform->setDefault('lecturerid', $lecturerid);

        $instructorids = $DB->get_fieldset_select('role_assignments', 'userid', 'contextid = :contextid and roleid=:roleid', [
            'contextid' => $context->id,
            'roleid' => '4',
        ]);

        $instructors = [];

        foreach ($instructorids as $instructorid) {
            $instructor = $DB->get_record('user', ['id' => $instructorid]);
            $instructors[$instructor->id] = $instructor->firstname . ' ' . $instructor->lastname;
        }

        $mform->addElement('select', 'instructorid', 'Instructor', $instructors);

        $mform->addElement('date_selector', 'startDate', get_string('from'));

        $mform->addElement('date_selector', 'endDate', get_string('to'));

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

    }

}
