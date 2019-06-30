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
 * Suspend a user enrollment without deleting the record
 *
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");

$ueid    = required_param('ue', PARAM_INT); // user enrolment id
$confirm = optional_param('confirm', false, PARAM_BOOL);
$filter  = optional_param('ifilter', 0, PARAM_INT);

$ue = $DB->get_record('user_enrolments', array('id' => $ueid), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id'=>$ue->userid), '*', MUST_EXIST);
$instance = $DB->get_record('enrol', array('id'=>$ue->enrolid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);

$context = context_course::instance($course->id);

require_capability('enrol/request:enabledisable', $context);

// set up PAGE url first!
$PAGE->set_url('/enrol/request/disableenrollment.php', array('ue'=>$ueid, 'ifilter'=>$filter));

require_login($course);

if (!enrol_is_enabled($instance->enrol)) {
    print_error('erroreditenrolment', 'enrol');
}

$plugin = enrol_get_plugin($instance->enrol);

$manager = new course_enrolment_manager($PAGE, $course, $filter);
$table = new course_enrolment_users_table($manager, $PAGE);

$returnurl = new moodle_url('/user/index.php', array('id' => $course->id)+$manager->get_url_params()+$table->get_url_params());
$usersurl = new moodle_url('/user/index.php', array('id' => $course->id));

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($usersurl);

// If the unenrolment has been confirmed and the sesskey is valid unenrol the user.
if ($confirm && confirm_sesskey()) {
    $plugin->update_user_enrol($instance, $ue->userid, ENROL_INSTANCE_DISABLED);
    redirect($returnurl);
}

$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$message = get_string('disableenrollmentconfirm', 'enrol_request', array('user'=>fullname($user, true), 'course'=>format_string($course->fullname)));
$fullname = fullname($user);
$title = get_string('disableenrollment', 'enrol_request');

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->navbar->add($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($fullname);
echo $OUTPUT->confirm($message, $yesurl, $returnurl);
echo $OUTPUT->footer();
