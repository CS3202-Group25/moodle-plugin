<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_workflow
 * @copyright   2022 SEP25
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_workflow\workflow;
/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function workflow_supports($feature)
{
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_workflow into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_workflow_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function workflow_add_instance($workflow)
{
    global $DB, $CFG;

    $oldworkflows= $DB->get_records('workflow');
    $courseids = [];

    foreach ($oldworkflows as $oldworkflow) {
        array_push($courseids, $oldworkflow->courseid);
    }

    if(in_array($workflow->courseid, $courseids)){
        redirect($CFG->wwwroot."/course/view.php?id=$workflow->courseid","A workflow already exist! You cannot create a another one!");
    }else{
        return $DB->insert_record('workflow', $workflow);
    }
}

/**
 * Updates an instance of the mod_workflow in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_workflow_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function workflow_update_instance($moduleinstance, $mform = null)
{
    global $DB;

    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('workflow', $moduleinstance);
}

/**
 * Removes an instance of the mod_workflow from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function workflow_delete_instance($id)
{
    global $DB;

    $exists = $DB->get_record('workflow', array('id' => $id));

    if (!$exists) {
        return false;
    }

    $DB->delete_records('workflow', array('id' => $id));

    return true;
}
