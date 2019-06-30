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
 * Setting Form: Enrollment by candidates by request
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_request_edit_form extends moodleform {
	var $_questions;
	
    function definition() {
    	global $DB, $OUTPUT, $CFG;
    	
        $mform = $this->_form;
		
        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_request'));

		// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'), array('size'=>'64', 'maxlength' => '255'));
		$mform->setDefault('name', get_string('defaultname', 'enrol_request'));
		$mform->setType('name', PARAM_TEXT);

        /// Add the enrolment deadline
        $mform->addElement('date_selector', 'deadline', get_string('deadline', 'enrol_request'));
        $mform->setDefault('deadline', time() + 3600 * 24);
		$mform->setType('deadline', PARAM_TEXT);

        /// Add can resubmit option checkbox
        $mform->addElement('checkbox', 'canresubmit', get_string('canresubmit', 'enrol_request'));
        $mform->setDefault('canresubmit', 0);

        /// Add override option for maximum amount of time request allowed to be in selected status
        $mform->addElement('text', 'maxenroldays', get_string('maxenroldays', 'enrol_request'));
        $mform->setDefault('maxenroldays', $plugin->get_config('enrol_request_max_enrol_days'));
		$mform->setType('maxenroldays', PARAM_INT);

        /// Add checkbox to set if this is a self-enrollment
        $mform->addElement('checkbox', 'isselfenrollment', get_string('isselfenrollment', 'enrol_request'));
        $mform->setDefault('isselfenrollment', 0);

        /// Add grouping of field(s) for additional questions to ask (all optional)
        $mform->addElement('header', 'additionalquestions', get_string('additionalquestions', 'enrol_request'));
        $mform->addHelpButton('additionalquestions', 'additionalquestions', 'enrol_request');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
		
        $mform->addElement('hidden', 'courseid');
		$mform->setType('courseid', PARAM_INT);
		
		$table = new html_table();
		$table->head  = array ("Order", "Question", "Action");
		
		$table->align = array('left', 'left', 'center');
		$table->wrap = array('', '', '');
		$table->attributes['class'] = 'standardtable generaltable';
		$table->width = '80%';
		$table->size = array('10', '*', '10');

		if($instance->id){
			$questions = $DB->get_records_menu('enrol_request_questions',array('enrolrequestid' => $instance->enrolid), 'id', 'id,questiontext');
			$order = 1;
			foreach($questions as $key => $question){
				$editlink = new moodle_url("/enrol/request/edit_question.php", array('enrolid' => $instance->id, 'id' => $key));				
				$editicon = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core', array('class'=>'smallicon')));
				
				$deletelink = new moodle_url("/enrol/request/delete_question.php", array('questionid'=>$key));
				$deleteicon = $OUTPUT->action_icon($deletelink, new pix_icon('t/delete', get_string('delete'), 'core', array('class'=>'smallicon')));
				
				$table->data[] = array ($order, $question, $editicon . " " . $deleteicon);
				
				$order++;
			}
			
			$mform->addElement('html', html_writer::table($table));
			
			// Add link to add more questions
			$addquestionurl = new moodle_url('/enrol/request/edit_question.php?enrolid='.$instance->id);
	    	$content = $OUTPUT->action_link($addquestionurl, get_string('addmorequestions', 'enrol_request'));
			$mform->addElement('static', 'addmorequestions', $content);
		} else {
			//$content = $OUTPUT->box(get_string('addinstancebeforequestions', 'enrol_request'));
			$content = $OUTPUT->notification(get_string('addinstancebeforequestions', 'enrol_request'));
			$mform->addElement('static', 'addmorequestions', '', $content);
		}

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));
		$this->set_data($instance);
    }

    function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);
		
        return $errors;
    }
}