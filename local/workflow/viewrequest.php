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


global $DB, $OUTPUT, $PAGE, $USER, $CFG;

require_once (__DIR__ . '/../../config.php');
require_once ($CFG->dirroot . '/mod/workflow/classes/requestcontroller.php');


$requestid = required_param('requestid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
[$course, $cm] = get_course_and_cm_from_cmid($cmid, 'workflow');

$PAGE->set_url(new moodle_url('/mod/workflow/viewrequest.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("View a Request");

require_login();

$requestController = new requestController();

$request = $DB->get_record('workflow_request', array('requestid'=>$requestid));
$state = $request->state;
$fileid = $request->filesid;
$filename = $DB->get_record('files', array('itemid'=>$fileid))->filename;
$sender = $DB->get_record('user', array('id'=>$request->studentid));
$sentdate = userdate($request->sentdate);
$role = $DB->get_record('role_assignments',array('userid'=>$USER->id));
$askedmore = $request->morereason;

$PAGE->set_heading("View Request: $request->requesttype");
$PAGE->navbar->add("View Request: $request->requesttype");
$PAGE->set_cm($cm, $course);

if ($sender->picture != 0) {
    $file = $DB->get_record('files', array('id'=>$sender->picture));
    $photourl = $CFG->wwwroot.'/pluginfile.php/'.$file->contextid.'/user/icon/boost/f2?rev='.$sender->picture;
    // $photourl = moodle_url::make_pluginfile_url($file->contextid, $file->component, $file->filearea, $file->itemid, $file->filepath, $file->filename, false);   
}else{
    $photourl = new moodle_url('/theme/image.php/boost/core/1668406882/u/f2');
}

if($fileid){
    $hasFile = true;
}else{
    $hasFile = false;
}

if($askedmore){
    $hasmorereason = true;
    $morereason = $request->morereason;
    $morefilesid = $request->morefilesid;
    $morefilename = $DB->get_record('files', array('itemid'=>$morefilesid))->filename;
    if($morefilesid){
        $hasmorefile = true;
    }else{
        $hasmorefile = false;
    }
}else{
    $hasmorereason = false;
}

$altbuttons=array();
if($role->roleid === "4"){
    if($request->askedmoredetails == 0) {
        $buttons = array(
            array(
                'btnId' => 'forward',
                'btnValue' => 'Forward',
            ),
            array(
                'btnId' => 'cancelIns',
                'btnValue' => 'Cancel',
            ));
    }
    if($request->askedmoredetails == 0){
        $altbuttons = array(array('btnId' => 'askFurther','linkText' => 'askfurther.php','btnValue' => 'Ask Further Details'));
    }
}elseif($role->roleid === "3"){
    $buttons = array(
        array(
            'btnId' => 'approve',
            'btnValue' => 'Approve',
        ),
        array(
            'btnId' => 'disapprove',
            'btnValue' => 'Disapprove',
        ));
}else{
    if($request->state == "Pending" || $request->state == "Asked More Details" || $request->state == "More Details Added") {
        $buttons = array(
            array(
                'btnId' => 'cancelStudent',
                'btnValue' => 'Cancel',
            ));
    }if($request->askedmoredetails == 1){
        $altbuttons = array(array('btnId' => 'sendfurther','linkText' => 'providefurther.php','btnValue' => 'Send further details'));
    }
}

echo $OUTPUT->header();

if($request->requesttype == "Extend Deadline"){
    $requestextend = $DB->get_record('workflow_request_extend', array('requestid'=>$requestid));
    if($request->isbatchrequest == 1){
        $isbatchrequest = 'Yes';
    }elseif($request->isbatchrequest == 0){
        $isbatchrequest = 'No';
    }

    if ($requestextend->assessmenttype == "Quiz") {
        $assessment = $DB->get_record('quiz', array('id'=>$requestextend->assessmentid))->name;
        $extendtime = $requestextend->extendtime;

    } elseif ($requestextend->assessmenttype == "Assignment") {
        $assessment = $DB->get_record('assign', array('id'=>$requestextend->assessmentid))->name;
        $extendtime = $requestextend->extendtime;
    }

    $viewRequestContent = (object) [
        'date' => $sentdate,
        'name' => "{$sender->firstname} {$sender->lastname}",
        'studentid' =>$sender->id,
        'requesttype' => $request->requesttype,
        'isbatchrequest' => $isbatchrequest,
        'assessmenttype' => $requestextend->assessmenttype,
        'assessment' => $assessment,
        'extendtime' => userdate($extendtime),
        'description' => $request->reason,
        'state' => $state,
        'buttons' => $buttons,
        'altbuttons' => $altbuttons,
        'filepath' => "files/$fileid/$filename",
        'requestid' => $requestid,
        'cmid'=> $cmid,
        'isassessment' => true,
        'photourl' => $photourl,
        'hasfile' => $hasFile,
        'hasmorefile' => $hasmorefile,
        'hasmorereason' => $hasmorereason,
        'morereason' => $morereason,
        'morefilesid' => $morefilesid,
        'morefilename' => $morefilename,
        'morefilepath' => "files/$morefilesid/$morefilename",

    ];

    echo $OUTPUT->render_from_template('mod_workflow/view_request', $viewRequestContent);
}else{
    $viewRequestContent = (object) [
        'date' => $sentdate,
        'name' => "{$sender->firstname} {$sender->lastname}",
        'studentid' =>$sender->id,
        'requesttype' => $request->requesttype,
        'description' => $request->reason,
        'state' => $state,
        'buttons' => $buttons,
        'altbuttons' => $altbuttons,
        'filepath' => "files/$fileid/$filename",
        'requestid' => $requestid,
        'cmid'=> $cmid,
        'isassessment' => false,
        'photourl' => $photourl,
        'hasfile' => $hasFile,
        'hasmorefile' => $hasmorefile,
        'hasmorereason' => $hasmorereason,
        'morereason' => $morereason,
        'morefilesid' => $morefilesid,
        'morefilename' => $morefilename,
        'morefilepath' => "files/$morefilesid/$morefilename",
    ];
    echo $OUTPUT->render_from_template('mod_workflow/view_request', $viewRequestContent);
}

echo $OUTPUT->footer();
