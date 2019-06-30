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
 * Request Form: Form for enrollment request sumbmissions
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/enrol/request/lib.php');

class enrol_request_form extends moodleform {
    var $_enrolrequestid;		// Enrollment Request for the course	
    var $_userid;				// Current user who will request enrollment
    
	public function __construct($url, $enrolrequestid, $userid = null,  $editable = true) {
        global $USER, $DB;

		$this->_userid = $userid;
		
		$enrollment = $DB->get_record('enrol_request', array('id'=>$enrolrequestid));
		
		if($enrollment != NULL){
			$this->_enrolrequestid = $enrollment->id;
		} else {
			$this->_enrolrequestid = null;
		}
		
		parent::__construct($url, null, 'post', '', null, $editable);		
	}
    
    function definition() {
    	global $DB;
		
        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;
		
		// Add additional questions if any
		$addquestions = $DB->get_records('enrol_request_questions', array('enrolrequestid' => $this->_enrolrequestid));	
		$enrol_request = $DB->get_record('enrol_request', array('id' => $this->_enrolrequestid));
		
        if ($addquestions) {
            $mform->addElement('header', 'additionalquestions', get_string('additionalquestionstitle', 'enrol_request'));
			
			if($mform->isFrozen()){
				$request = $DB->get_record('enrol_request_requests', array('enrolrequestid' => $this->_enrolrequestid, 'userid' => $this->_userid));
				
				$status = get_string('status' . $request->status, 'enrol_request');
				$statusMessage = get_string('statusmessage', 'enrol_request', $status);
				
				$mform->addElement('static', 'message', '', $statusMessage);
			}
			
			$questionNumber = 1;
            foreach ($addquestions as $q) {
            	$mform->addElement('header', 'question'.$q->id, get_string('question', 'enrol_request') . " - " . $questionNumber);
                $mform->addElement('static', 'q'.$q->id.'text', '', $q->questiontext);
                $mform->addElement('textarea', 'q'.$q->id, '', 'rows="6" cols="43"');
				$questionNumber++;
            }
        }
		
        $mform->addElement('hidden', 'enrolrequestid');
        $mform->setDefault('enrolrequestid', $this->_enrolrequestid);
		$mform->setType('enrolrequestid', PARAM_TEXT);
        
		$mform->addElement('hidden', 'enrolid');
        $mform->setDefault('enrolid', $enrol_request->enrolid);
		$mform->setType('enrolid', PARAM_TEXT);		
		
        $this->add_action_buttons(true, ($instance ? null : get_string('requestenrollment', 'enrol_request')));

        $this->set_data($instance);
    }

    function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);
		
        return $errors;
    }
	
	function hardFreeze(){
		$this->_form->hardFreeze();
	}
}