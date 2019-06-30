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
 * Edit Question Form: form for add and edit questions
 * @package   enrol_request
 * @copyright 2013 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_request_edit_question_form extends moodleform {

    // form definition
    function definition() {
        global $CFG, $DB;
        
        $mform =& $this->_form;
		list($instance, $plugin, $context) = $this->_customdata;

		if($instance->id){
			$header = get_string('headereditquestion', 'enrol_request');
		} else {
			$header = get_string('headeraddquestion', 'enrol_request');
		}

		$mform->addElement('header', 'header', $header);

		$questiontextoptions = array('maxfiles'=>0, 'maxbytes'=>0, 'context'=>$context); // No files are accepted

		$mform->addElement('editor', 'questiontext_editor', get_string('questiontext', 'enrol_request'), NULL, $questiontextoptions);
		$mform->setType('questiontext_editor', PARAM_RAW);
		$mform->addRule('questiontext_editor', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'id');
		$mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'enrolid');
		$mform->setType('enrolid', PARAM_INT);				

        $this->add_action_buttons(true, ($instance->id ? null : $header));

        $this->set_data($instance);
    }
}
