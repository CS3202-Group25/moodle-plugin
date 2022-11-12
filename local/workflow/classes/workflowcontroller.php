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

use stdClass;
use dml_exception;

class workflowController
{
    public function createWorkflow($courseid, $lecturerid, $instructorid, $startdate, $enddate)
    {
        global $DB;
        $recordToInsert = new stdClass();
        $recordToInsert->courseid = $courseid;
        $recordToInsert->lecturerid = $lecturerid;
        $recordToInsert->instructorid = $instructorid;
        $recordToInsert->startdate = $startdate;
        $recordToInsert->enddate = $enddate;

        try {
            return $DB->insert_record('workflow', $recordToInsert, false);
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function deleteWorkflow($workflowid)
    {
        global $DB;
        $DB->delete_records('workflow', ['workflowid' => $workflowid]);
    }

    public function getWorkflow($workflowid)
    {
        global $DB;
        return $DB->get_record('workflow', array('workflowid' => $workflowid));
    }

    public function getAllWorkflows()
    {
        global $DB;
        try {
            return $DB->get_records('workflow');
        } catch (dml_exception $e) {
            return [];
        }
    }

}