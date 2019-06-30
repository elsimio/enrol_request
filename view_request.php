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

$enrolrequestid = required_param('enrolrequestid', PARAM_INT);

if (!is_null($enrolrequestid)) {
	$request = $DB->get_record('enrol_request_requests', array('id' => $enrolrequestid), '*');
	
    if (!$request) {
        error("Invalid request id");
    } else {
    	$course = enrol_request_get_course_from_request($enrolrequestid);
		$enrollment = enrol_request_get_enrollment_from_request($enrolrequestid);
		$user = $DB->get_record('user', array('id' => $request->userid));
    }
} else {
    $request =  NULL;
}

$context = context_course::instance($course->id);

require_capability('enrol/request:manage', $context);

$enrolRequestLabel = get_string('enrolrequest', 'enrol_request');

$PAGE->set_url('/enrol/request/view_request.php', array('enrolrequestid' => $enrolrequestid));
$PAGE->set_context($context);
$PAGE->set_pagelayout('popup');
$PAGE->set_title($enrolRequestLabel);
$PAGE->requires->js('/enrol/request/request.js');
$PAGE->requires->css('/enrol/request/styles.css');

echo $OUTPUT->header();


// Settings for the form
$formURL = $CFG->wwwroot.'/enrol/request/view_request.php?enrolrequestid='.$enrolrequestid;
$editable = false;
$mform = new enrol_request_form($formURL, $enrollment->id, $request->userid, $editable);

// Is this a form submission?
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

enrol_request_display_user_profile($user->id);

$mform->display();

echo $OUTPUT->box_start();
// If platform manager, can select a status
if (has_capability('enrol/request:manage', $context)) {
    if ($request->status == STATUS_PENDING) {
        echo '<div class="enrol-request-center-text">';
        echo '<input type="button" value="'.get_string('status'.STATUS_ENROLLED, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_ENROLLED.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_PAID, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_PAID.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_NOTSELECTED, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_NOTSELECTED.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_SELECTED, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_SELECTED.'); self.close();"/>';
		echo '<input type="button" value="'.get_string('status'.STATUS_SELECTED_SCHOLARSHIP, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_SELECTED_SCHOLARSHIP.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_WAITING_LIST, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_WAITING_LIST.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_PAYMENT_NOT_RECEIVED, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_PAYMENT_NOT_RECEIVED.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_EARLY_BIRD, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_EARLY_BIRD.'); self.close();"/>';
        echo '<input type="button" value="'.get_string('status'.STATUS_ENROLLED_NO_INDES, 'enrol_request').'" onclick="enrol_request_change_parent_status('.$request->id.', '.STATUS_ENROLLED_NO_INDES.'); self.close();"/>';		
        echo '</div><br/>';
    }
}

echo '<div class="enrol-request-center-text">';
echo $OUTPUT->close_window_button();
echo '</div><br/>';
        
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
