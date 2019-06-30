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
 * Enrollment by candidates by request
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('edit_form.php');

$courseid   = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);

$course = get_course($courseid);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/request:config', $context);

$PAGE->set_url('/enrol/request/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id'=>$course->id));
if (!enrol_is_enabled('request')) {
    redirect($return);
}

$plugin = enrol_get_plugin('request');

$enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'request', 'status' => 0));

if ($instanceid && $enrol) {
    $instance = $DB->get_record(
        'enrol',
        array(
            'courseid' => $course->id,
            'enrol' => 'request',
            'id' => $instanceid),
        '*', MUST_EXIST);
	$instance1 = $DB->get_record('enrol_request', array('enrolid' => $enrol->id));
	$instance->deadline = $instance1->deadline;
	$instance->enrolid = $instance1->id;		
} else {
    require_capability('moodle/course:enrolconfig', $context);
    // no instance yet, we have to add new instance
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));
    $instance = (object)$plugin->get_instance_defaults();
	$instance->id = null;
}
$instance->courseid = $course->id;

$mform = new enrol_request_edit_form(NULL, array($instance, $plugin, $context));

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
    if ($instance->id && $instance1->id) {
        $instance->name					= $data->name;
        $instance->timemodified			= time();
		$DB->update_record('enrol', $instance);
		
        $instance1->name				= $data->name;
        $instance1->deadline			= $data->deadline;
		$instance1->canresubmit      = (isset($data->canresubmit))?$data->canresubmit:0;
		$instance1->maxenroldays		= $data->maxenroldays;
		$instance1->isselfenrollment = (isset($data->isselfenrollment))?$data->isselfenrollment:0;
        $instance1->timemodified		= time();		
        $plugin->update_instance($instance1, $data);
    } else {
        $fields = array(
			'name'					=> $data->name,
			'deadline'				=> $data->deadline,
			'canresubmit'			=> (isset($data->canresubmit))?$data->canresubmit:0,
			'isselfenrollment'		=> (isset($data->isselfenrollment))?$data->isselfenrollment:0,
			'maxenroldays'			=> $data->maxenroldays);			
        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_request'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_request'));
$mform->display();
echo $OUTPUT->footer();