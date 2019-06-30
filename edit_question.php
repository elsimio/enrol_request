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
 * Edit Question View: edit a question from one enrollment request
 * 
 * @package   enrol_request
 * @copyright 2013 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/enrol/request/lib.php');
require_once($CFG->dirroot.'/enrol/request/edit_question_form.php');

$enrolid = required_param('enrolid', PARAM_INT);
$id = optional_param('id', false, PARAM_INT);

if (! $enrol_request = $DB->get_record("enrol_request", array("enrolid" => $enrolid))) {	
	print_error('Enrollment Request not found!');
}

if (! $enrol = $DB->get_record("enrol", array("id" => $enrol_request->enrolid))) {
	print_error('Enrollment not found!');
}

if (! $course = $DB->get_record("course", array("id" => $enrol->courseid))) {
	print_error('Course not found!');
}

$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/request:editquestion', $context);

$PAGE->set_url('/enrol/request/edit_question.php', array('enrolid' => $enrolid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/request/edit.php', array('courseid' => $course->id));

if ($id) {
    $instance = $DB->get_record('enrol_request_questions', array('id' => $id));
	$instance->enrolid = $enrol->id;		
} else {
    $instance = new stdClass();
    $instance->id       = null;
	$instance->questiontext = '';	
	if ($enrol){
		$instance->enrolid = $enrol->id;	
	}	
}

$enrollment = $DB->get_record('enrol_request', array('enrolid' => $enrol->id));

$plugin = enrol_get_plugin('request');

// Handle the 'editor' field
$questiontextoptions = array('maxfiles'=>0, 'maxbytes'=>0, 'context' => $context); // No files are accepted
$instance->questiontextformat = FORMAT_HTML;
$instance->enrolrequestid = $enrollment->id;

$instance = file_prepare_standard_editor($instance, 'questiontext', $questiontextoptions, $context, 'enrol_request', 'questiontext', $instance->enrolrequestid);

// create form and set initial data
$mform = new enrol_request_edit_question_form(NULL, array($instance, $plugin, $context));

$return = "$CFG->wwwroot/enrol/request/edit.php?courseid=".$course->id."&id=".$enrolid;

if ($mform->is_cancelled()){
	redirect($return);
} else if ($data = $mform->get_data()) {
	$data = file_postupdate_standard_editor($data, 'questiontext', $questiontextoptions, $context, 'enrol_request', 'questiontext', $instance->enrolid);
	if ($instance->id) {
		$newdata = new stdClass();
		$newdata->id = $instance->id;
		$newdata->enrolrequestid = $instance->enrolrequestid;
		$newdata->questiontext = $data->questiontext;
		enrol_request_edit_question($newdata);		
	} else {	
		$newdata = new stdClass();		
		$newdata->enrolrequestid = $instance->enrolrequestid;
		$newdata->questiontext = $data->questiontext;
		enrol_request_add_question($newdata);		
	}
	
	redirect($return);
}

$PAGE->set_url('/enrol/request/edit_question.php', array('enrolid' => $enrolid));
$PAGE->set_pagelayout('course');
$PAGE->set_context($context);

$PAGE->set_title($enrol->name);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($enrol->name);

$mform->display();

echo $OUTPUT->footer();
