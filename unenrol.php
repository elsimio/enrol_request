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
 * Unenrol view: delete an enrollment request sumbmission
 * 
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of 2010 Petr Skoda  {@link http://skodak.org} (Moodle 2.2 selfenrol plugin)
 */

 
require_once('../../config.php');
require_once($CFG->dirroot.'/enrol/request/lib.php');
require_once($CFG->dirroot.'/enrol/request/request_form.php');

require_login();

$enrolrequestid = required_param('enrolrequestid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

if (!is_null($enrolrequestid)) {
	$request = $DB->get_record('enrol_request_requests', array('id' => $enrolrequestid));
	
    if (!$request) {
        print_error("The request ID is not valid!");
    } else {
    	$course = enrol_request_get_course_from_request($enrolrequestid);
		$enrollment = enrol_request_get_enrollment_from_request($enrolrequestid);
    }
} else {
    print_error("Invalid enrollment request. Please contact support");
}

if(!$course || !$enrollment){
	print_error("Invalid enrollment request. Please contact support");
}

// Check if the request belongs to the current user
if($request->userid != $USER->id){
	print_error("You have no permission to unenrol from this enrollment request! Only the own user can unenrol him/herself.");
} 

if (!is_null($request) && $confirm && confirm_sesskey()) {
	enrol_request_delete_request($enrolrequestid);
    
    //TODO replace add_to_log for events_trigger()
	//add_to_log($enrolrequestid, 'enrol_request', 'unenrol', '../enrol/request/view_request.php?id='.$enrolrequestid, $course->id);
	// Trigger event.
	/*$params = array(
		'objectid' => $enrolrequestid,
		'courseid' => $course->id,
		'context' => context_course::instance($course->id),
		'relateduserid' => $USER->id,
		'other' => array('enrol' => 'enrol_request')
	);
	$event = \enrol_request\event\user_enrolment_deleted::unenrol($params);
	$event->trigger();*/
	 
    redirect(new moodle_url('/course/view.php?id=' . $course->id));
}

$context = context_course::instance($course->id);

require_capability('enrol/request:unenrol', $context);

$PAGE->set_url('/enrol/request/unenrol.php', array('enrolrequestid' => $enrolrequestid));
$PAGE->set_pagelayout('course');
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);

// Print the page header
$strenrolrequests = get_string("modulenameplural", "enrol_request");
$strenrolrequest  = get_string("modulename", "enrol_request");

//$navlinks = array();
//$navlinks[] = array('name' => $strenrolrequests, 'link' => "index.php?id=$course->id", 'type' => 'activity');
//$navlinks[] = array('name' => format_string($course->shortname), 'link' => '', 'type' => 'title');
//$navlinks[] = array('name' => format_string($enrollment->name), 'link' => '', 'type' => 'activityinstance');

//$navigation = build_navigation($navlinks);

echo $OUTPUT->header();

$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$nourl = new moodle_url('/course/view.php', array('id' => $course->id));
$message = get_string('unenrolconfirm', 'enrol_request', format_string($course->fullname));
echo $OUTPUT->confirm($message, $yesurl, $nourl);

echo $OUTPUT->footer();
