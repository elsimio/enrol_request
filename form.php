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
 * Form for enrollment request sumbmissions
 *
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later 
 * @author Michael Avelar <michaela@moodlerooms.com>
 * @version enrolrequest_form.php v1.0
 * @package enrolrequest
 **/

defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->libdir.'/formslib.php');

class enrol_request_form extends moodleform {
    var $_enrolrequestid;   // Request enrollment mod id
    var $_enrolrequesterid;      // ID of the user making the request

	public function __construct($url, $modid, $userid = null, $editable = true) {
        global $USER;
        $this->_enrolrequestid = $modid;
        if (!$editable && record_exists('enrolrequest_requests', 'modid', $modid, 'userid', $userid) && $USER->id != $userid) {
            $this->_enrolrequesterid = $userid;
        } else {
            $this->_enrolrequesterid = null;
        }
        parent::__construct($url, null, 'post', '', null, $editable);
	}
	
    function definition() {
        global $CFG, $SITE;

        $mform =& $this->_form;

        if (!is_null($this->_enrolrequesterid)) {
	        $user = $DB->get_record('user', array('id'=>$this->_enrolrequesterid), '*', MUST_EXIST);

	        $usercustom = mod_enrolrequest_profile_user_record($user->id);

	    /// Basic Data section
	        $mform->addElement('header', 'basicdata', get_string('basicdatatitle', 'enrolrequest'));
	    /// Lastname element for display only
	        $mform->addElement('text', 'lastname', get_string('lastname', 'enrolrequest'));
	        $mform->setType('lastname', PARAM_TEXT);
	        $mform->setDefault('lastname', $user->lastname);
	    /// Firstname field for display only
	        $mform->addElement('text', 'firstname', get_string('firstname', 'enrolrequest'));
	        $mform->setType('firstname', PARAM_TEXT);
	        $mform->setDefault('firstname', $user->firstname);
	    /// Phone number field for display only
	        $mform->addElement('text', 'phone', get_string('phone', 'enrolrequest'));
	        $mform->setType('phone', PARAM_TEXT);
	        $mform->setDefault('phone', $user->phone1);
	    /// Email field for display only
	        $mform->addElement('text', 'email', get_string('email', 'enrolrequest'));
	        $mform->setType('email', PARAM_TEXT);
	        $mform->setDefault('email', $user->email);
	    /// City field for display only
	        $mform->addElement('text', 'city', get_string('city', 'enrolrequest'));
	        $mform->setType('city', PARAM_TEXT);
	        $mform->setDefault('city', $user->city);
	    /// Country field for display only
	        $countries = get_list_of_countries();

	        $mform->addElement('select', 'nationality', get_string('nationality', 'enrolrequest'), $countries);
	        $mform->setDefault('nationality', $user->country);
        }

        $mform->addElement('hidden', 'instanceid');
        $mform->setType('instanceid', PARAM_INT);
        $mform->setDefault('instanceid', $this->_enrolrequestid);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $SITE->id);

        $mform->addElement('hidden', 'page');
        $mform->setType('page', PARAM_INT);

        $this->add_action_buttons(false, get_string('requestenrollment', 'enrol_request'));
    }

    /**
     * Set the form data
     *
     * @return void
     **/
    function mod_enrolrequest_set_data($request = null) {
        global $SITE;

        if (!is_null($request)) {
            // Set hidden elements added to previous request
	        $request->instanceid    = $this->_enrolrequestid;
	        $request->courseid      = $SITE->id;
	        $request->page          = optional_param('page', NULL, PARAM_INT);

	        $this->set_data($request);
        } else {
            // Set hidden elements added to previous request
            $formdata = new stdClass;
            $formdata->instanceid    = $this->_enrolrequestid;
            $formdata->courseid      = $SITE->id;
            $formdata->page          = optional_param('page', NULL, PARAM_INT);

            $this->set_data($formdata);
        }
    }

    /**
     * Enforce validation rules here
     *
     * @param object $data Post data to validate
     * @param object uploaded files info
     * @return array
     **/
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }

    /**
     * Function determines if there is a custom profile value for fieldname,
     * if so, returns options for field if it is a menu of choices type of custom
     * profile field.
     *
     * @param string    $fieldname  Fieldname to check, compares to lowercase shortname of user profile field
     * @param object    $user       User object with all custom profile fields and values
     * @param array     $options    Options for select type of mform element
     * @param string    $default    Default value for this field (eg: current $user value for that field)
     * @return null (params passed by ref)
     **/
    function mod_enrolrequest_get_field_options($fieldname, $user, &$options, &$default) {
        // If there is a user profile field matching this fieldname...
        if (isset($user->{$fieldname})) {
            // Get options for this field (only for menu of choices type of custom profile field)
            if ($options = get_field('user_info_field', 'param1', 'shortname', $fieldname)) {
	            // Create array
	            $options = explode("\n", $options);
	            $options = array_combine($options, $options);
	            // If the profile field is empty (not yet set), create a 'choose' default option
	            if (empty($user->{$fieldname})) {
	                $options['_choose'] = get_string('choose');
	                $default = '_choose';
	            } else {
	                $default = $user->{$fieldname};
	            }
            } else {
                $options = null;
            }
        }
    }
}
?>