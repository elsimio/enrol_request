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
 * Request view: view for enrollment request sumbmissions
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */

require('../../config.php');
require($CFG->dirroot.'/enrol/request/lib.php');
require($CFG->dirroot.'/enrol/request/request_form.php');

$messageInformation = '';

require_login();

$enrolid   = required_param('enrolid', PARAM_INT);

$enrol = $DB->get_record('enrol', array('id'=>$enrolid), '*', MUST_EXIST);
$enrolrequest = $DB->get_record('enrol_request', array('enrolid'=>$enrolid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$enrol->courseid), '*', MUST_EXIST);
$request = $DB->get_record('enrol_request_requests', array('enrolrequestid' => $enrolrequest->id, 'userid' => $USER->id), '*');

$context = context_course::instance($course->id);

if(!$request){
	$request = NULL;
}

require_capability('enrol/request:request', $context);

$PAGE->set_url('/enrol/request/request.php', array('enrolid'=>$enrolid));
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('course');
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);

// Navigation (Breadcrumb)
$url = new moodle_url('/enrol/index.php', array('id'=>$course->id));
$PAGE->navbar->add(get_string('enrolme', 'enrol'), $url);
$PAGE->navbar->add(get_string('enrolrequest','enrol_request'));


$formURL = $CFG->wwwroot.'/enrol/request/request.php?enrolid='.$enrolid;

$editable = false;
if (is_null($request)) {
	$editable = true;
}

$mform = new enrol_request_form($formURL, $enrolrequest->id, $USER->id, $editable);
$requestSaved = false;

// Is this a form submission?
if ($formdata = $mform->get_data()) {
	$requestSaved = enrol_request_save_request($formdata, $enrolid);
	
	if($requestSaved) {
		$messageInformation .= $OUTPUT->notification(get_string('success', 'enrol_request'), 'notifysuccess');
		$mform->hardFreeze();
		//$OUTPUT->box('success');
	} else {
		$messageInformation .= $OUTPUT->error_text(get_string("errorrequest", 'enrol_request'));
	}
} else {
    if (!is_null($request)) {
       if ($responses = $DB->get_records('enrol_request_responses', array('requestid' => $request->id))) {
           foreach ($responses as $response) {
               $qid = 'q'.$response->questionid;
               $request->$qid = format_string($response->response);
           }
       }
    }

    // Set some form hidden variables
    $mform->set_data($request);
}

echo $OUTPUT->header();

if(trim($messageInformation) != ''){
	echo $messageInformation;
}

echo $OUTPUT->heading(get_string('pluginname', 'enrol_request'));

$mform->display();

if($requestSaved || !is_null($request)){
	echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
}

echo $OUTPUT->footer();
