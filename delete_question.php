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
 * Delete Question View: delete a question from one enrollment request
 * 
 * @package   enrol_request
 * @copyright 2013 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/enrol/request/lib.php');
require_once($CFG->dirroot.'/enrol/request/request_form.php');

$questionid = required_param('questionid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();

if (!is_null($questionid)) {
	$question = $DB->get_record('enrol_request_questions', array('id' => $questionid));
	
    if (!$question) {
        print_error("This is not a valid question of an Enrollment Request. Please contact support");
    } else {
    	$course = enrol_request_get_course_from_question($questionid);
		$enrollment = enrol_request_get_enrollment_from_question($questionid);
    }
} else {
    print_error("This is not a valid question of an Enrollment Request. Please contact support");
}

if(!$course || !$enrollment){
	print_error("This is not a valid question of an Enrollment Request. Please contact support");
}

if (!is_null($question) && $confirm && confirm_sesskey()) {
	enrol_request_delete_question($questionid);
    
    //TODO replace add_to_log for events_trigger()    
	//add_to_log($questionid, 'enrol_request', 'delete_question', '/enrol/request/delete_question.php?questionid='.$questionid, $course->id);
	 
    redirect(new moodle_url('/enrol/request/edit.php?courseid=' . $course->id.'&id='.$enrollment->enrolid));
}

$context = context_course::instance($course->id);

require_capability('enrol/request:editquestion', $context);

$PAGE->set_url('/enrol/request/delete_question.php', array('questionid'=>$questionid));
$PAGE->set_pagelayout('course');
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);

// Print the page header
$strenrolrequests = get_string("modulenameplural", "enrol_request");
$strenrolrequest  = get_string("modulename", "enrol_request");

echo $OUTPUT->header();

$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$nourl = new moodle_url('/enrol/request/edit.php', array('courseid' => $course->id, 'id' => $enrollment->enrolid));
$message = get_string('deletequestionconfirm', 'enrol_request');
$message .= "<br><br>";
$message .= "<div>" . format_string($question->questiontext) . "</div>";

echo $OUTPUT->confirm($message, $yesurl, $nourl);

echo $OUTPUT->footer();
