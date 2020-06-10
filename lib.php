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

defined('MOODLE_INTERNAL') || die();

/**
 * Enrolment request plugin implementation.
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author  Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @author  Eugene Venter - based on code by Martin Dougiamas and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot. '/user/profile/lib.php');
require_once($CFG->dirroot. '/enrol/locallib.php');
require_once($CFG->dirroot. '/enrol/paypalrequest/lib.php');
require_once($CFG->dirroot. '/enrol/striperequest/lib.php');
require_once($CFG->libdir . '/enrollib.php');

define("STATUS_PENDING", 				0);
define("STATUS_ENROLLED",				1);
define("STATUS_PAID", 					2);
define("STATUS_NOTSELECTED",			3);
define("STATUS_SELECTED",				4);
define("STATUS_SELECTED_SCHOLARSHIP",	5);
define("STATUS_WAITING_LIST",           6);
define("STATUS_PAYMENT_NOT_RECEIVED",   7);
define("STATUS_EARLY_BIRD",			    8);
define("STATUS_ENROLLED_NO_INDES",	    9);

define("TYPE_SELECTION_REGULAR",	"REGULAR");
define("TYPE_SELECTION_SCHOLARSHIP", "SCHOLARSHIP");

class enrol_request_plugin extends enrol_plugin {

	public function get_info_icons(array $instances) {
		return array(new pix_icon('icon', get_string('pluginname', 'enrol_request'), 'enrol_request'));
	}

	public function roles_protected() {
		// users with role assign cap may tweak the roles later
		return false;
	}

	public function allow_unenrol(stdClass $instance) {
		// users with unenrol cap may unenrol other users manually - requires enrol/request:unenrol
		return true;
	}

	public function allow_manage(stdClass $instance) {
		// users with manage cap may tweak period and status - requires enrol/request:manage
		return true;
	}

	public function show_enrolme_link(stdClass $instance) {
		return ($instance->status == ENROL_INSTANCE_ENABLED);
	}

	/**
	 * Sets up navigation entries.
	 *
	 * @param object $instance
	 * @return void
	 */
	public function add_course_navigation($instancesnode, stdClass $instance) {
		if ($instance->enrol !== 'request') {
			 throw new coding_exception('Invalid enrol instance type!');
		}

        $context = context_course::instance($instance->courseid);
		if (has_capability('enrol/request:config', $context)) {
			$managelink = new moodle_url('/enrol/request/edit.php', array('courseid'=>$instance->courseid, 'id'=>$instance->id));
			$instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
		}
	}

	/**
	 * Returns edit icons for the page with list of instances
	 * @param stdClass $instance
	 * @return array
	 */
	public function get_action_icons(stdClass $instance) {
		global $OUTPUT;

		if ($instance->enrol !== 'request') {
			throw new coding_exception('invalid enrol instance!');
		}
        $context = context_course::instance($instance->courseid);

		$icons = array();

		if (has_capability('enrol/request:manage', $context)) {
			$managelink = new moodle_url("/enrol/request/manage.php", array('enrolid'=>$instance->id));
			$icons[] = $OUTPUT->action_icon($managelink, new pix_icon('i/users', get_string('managerequests', 'enrol_request'), 'core', array('class'=>'iconsmall')));
		}
        if (has_capability('enrol/request:config', $context)) {
            $editlink = new moodle_url("/enrol/request/edit.php", array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon(
                't/edit',
                get_string('edit'),
                'core',
                array('class' => 'iconsmall')));
        }

		return $icons;
	}

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     * @param  stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
            $context = context_course::instance($instance->courseid);
            return has_capability('enrol/request:config', $context);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
            $context = context_course::instance($instance->courseid);
            return has_capability('enrol/request:config', $context);
    }

	/**
	 * Returns link to page which may be used to add new instance of enrolment plugin in course.
	 * @param int $courseid
	 * @return moodle_url page url
	 */
	public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

		if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/request:config', $context)) {
			return NULL;
		}

		// multiple instances supported - different cost for different roles
		return new moodle_url('/enrol/request/edit.php', array('courseid'=>$courseid));
	}

	/**
	 * Creates course enrol form, checks if form submitted
	 * and enrols user if necessary. It can also redirect.
	 *
	 * @param stdClass $instance
	 * @return string html text, usually a form in a text box
	 */
	function enrol_page_hook(stdClass $instance) {
		global $CFG, $USER, $OUTPUT, $PAGE, $DB;

		ob_start();

		if ($DB->record_exists('user_enrolments', array('userid'=>$USER->id, 'enrolid'=>$instance->id))) {
			return ob_get_clean();
		}

		if ($instance->enrolstartdate != 0 && $instance->enrolstartdate > time()) {
			return ob_get_clean();
		}

		if ($instance->enrolenddate != 0 && $instance->enrolenddate < time()) {
			return ob_get_clean();
		}

		$course = $DB->get_record('course', array('id'=>$instance->courseid));
		$enrolrequest = $DB->get_record('enrol_request', array('enrolid'=>$instance->id));
		$request = $DB->get_record('enrol_request_requests', array('enrolrequestid' => $enrolrequest->id, 'userid' => $USER->id), '*');

        $context = context_course::instance($instance->courseid);

		$shortname = format_string($course->shortname, true, array('context' => $context));
		$strloginto = get_string("loginto", "", $shortname);
		$strcourses = get_string("courses");

		// force login only for guest user, not real users with guest role
		if (isguestuser()) {
				if (empty($CFG->loginhttps)) {
					$wwwroot = $CFG->wwwroot;
				} else {
					// This actually is not so secure ;-), 'cause we're
					// in unencrypted connection...
					$wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
				}
				echo '<div class="mdl-align"><p>'.get_string('guestnotallowed', 'enrol_request').'</p>';
				echo '<p><b>'.get_string('guestlogininfo', 'enrol_request').'</b></p>';
				echo '<p><a href="'.$wwwroot.'/login/">'.get_string('login').'</a></p>';
				echo '</div>';

		// All users that have submitted a request should be able to see their status
		} else if(!is_null($request) && is_object($request)) {
			require_once("$CFG->dirroot/enrol/request/form.php");
			$form = new enrol_request_form(null, $instance);

			$instanceid = optional_param('instance', 0, PARAM_INT);

			if($request->status == STATUS_SELECTED || $request->status == STATUS_SELECTED_SCHOLARSHIP){
				$paypalInstance = new enrol_paypalrequest_plugin();
				$stripeRequestInstance = new enrol_striperequest_plugin();
				//enrol_page_hook

			    $instances = enrol_get_instances($course->id, false);
			    foreach ($instances as $instance) {
			        if ($instance->enrol == 'paypalrequest' and $request->status == STATUS_SELECTED and trim($instance->name) == TYPE_SELECTION_REGULAR) {
			        	// Mark that the user was selected so that he can see the Paypal form
			        	$instance->userselected = TRUE;
						echo $paypalInstance->enrol_page_hook($instance);
						break;

			        } elseif ($instance->enrol == 'paypalrequest' and $request->status == STATUS_SELECTED_SCHOLARSHIP and trim($instance->name) == TYPE_SELECTION_SCHOLARSHIP) {
			        	// Mark that the user was selected so that he can see the Paypal form
			        	$instance->userselected = TRUE;
						echo $paypalInstance->enrol_page_hook($instance);
						break;
			        } elseif ($instance->enrol == 'striperequest' and $request->status == STATUS_SELECTED and trim($instance->name) == TYPE_SELECTION_REGULAR) {
			        	// Mark that the user was selected so that he can see the Stripe form
			        	$instance->userselected = TRUE;
						echo $stripeRequestInstance->enrol_page_hook($instance);
						break;

			        } elseif ($instance->enrol == 'striperequest' and $request->status == STATUS_SELECTED_SCHOLARSHIP and trim($instance->name) == TYPE_SELECTION_SCHOLARSHIP) {
			        	// Mark that the user was selected so that he can see the Stripe form
			        	$instance->userselected = TRUE;
						echo $stripeRequestInstance->enrol_page_hook($instance);
						break;
			        }
			    }
			} else {
				include($CFG->dirroot.'/enrol/request/enrolstatus.html');
			}

		} elseif ($enrolrequest->deadline > time()) {
			include($CFG->dirroot.'/enrol/request/enrol.html');
		} else {
			echo "<br>";
			echo $OUTPUT->container(get_string('deadlineHasPassed', 'enrol_request'), 'generalbox', 'notice');
		}

		return $OUTPUT->box(ob_get_clean());
	}

	/**
	 * Add new instance of enrol request plugin.
	 * 
	 * @param object $course
	 * @param array instance fields
	 * @return int id of new instance, null if can not be created
	 */
	public function add_instance($course, array $fields = NULL) {
		global $DB;

		if ($DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>'request'))) {
			// only one instance is allowed
			return NULL;
		}

		// New enrollment instance
		$enrolid = parent::add_instance($course, $fields);

		// Create new instance of enrol_request
		$instance = new stdClass;
		$instance->enrolid			= $enrolid;
		$instance->name			 	= $fields['name'];
		$instance->deadline		 	= $fields['deadline'];
		$instance->canresubmit	  	= $fields['canresubmit'];
		$instance->isselfenrollment = $fields['isselfenrollment'];
		$instance->maxenroldays	 	= $fields['maxenroldays'];
		$instance->timemodified	 	= time();

		$enrolrequestid = $DB->insert_record('enrol_request', $instance);

		return $enrolid;
	}

	/**
	 * Update an existing instance of enrol request plugin.
	 * 
	 * @param object $enrolrequest
	 * @return boolean Success/Fail
	 **/
	function update_instance($enrolrequest, $data) {
		global $DB;

		$status = true;

		// Create update instance object
		$instance = new stdClass;
		$instance->id               = $enrolrequest->id;
		$instance->name             = $enrolrequest->name;
		$instance->deadline         = $enrolrequest->deadline;
		$instance->canresubmit      = (isset($enrolrequest->canresubmit))?$enrolrequest->canresubmit:0;
		$instance->maxenroldays     = $enrolrequest->maxenroldays;
		$instance->timemodified     = time();
		$instance->isselfenrollment = (isset($enrolrequest->isselfenrollment))?$enrolrequest->isselfenrollment:0;

		$status = $DB->update_record('enrol_request', $instance);

	    return $status;
	}

    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;

        if (has_capability("enrol/request:enabledisable", $context)) {

			switch ($ue->status) {
				case ENROL_INSTANCE_ENABLED:
		            $url = new moodle_url('/enrol/request/disableenrollment.php', $params);
		            $actions[] = new user_enrolment_action(new pix_icon('i/hide', ''), get_string('disableenrollment', 'enrol_request'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
					break;

				case ENROL_INSTANCE_DISABLED:
		            $url = new moodle_url('/enrol/request/enableenrollment.php', $params);
		            $actions[] = new user_enrolment_action(new pix_icon('i/show', ''), get_string('enableenrollment', 'enrol_request'), $url, array('class'=>'enrollink', 'rel'=>$ue->id));
					break;

			}
        }

		if (has_capability("enrol/request:delete", $context)) {
            $urldelete = new moodle_url('/enrol/request/deleteenrollment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('deleteenrollment', 'enrol_request'), $urldelete, array('class'=>'enrollink', 'rel'=>$ue->id));
		}

        return $actions;
    }
}

/**
 * List of helper functions to handle the enrol/request business logic
 */

function enrol_request_save_request($formdata, $enrolid) {
	global $USER, $DB;

	// instance of the enrollment request plugin associated to this course
	$enrollment = $DB->get_record('enrol_request', array('enrolid' => $enrolid));

	// add three additional fields
	$formdata->modid = $enrollment->id;
	$formdata->userid = $USER->id;
	$formdata->timesubmitted = time ();

	// If this is a self-enrollment, request status must be 'enrolled'
	if($enrollment->isselfenrollment) {
		$formdata->status = STATUS_ENROLLED;
	}

	if ($requestid = $DB->insert_record('enrol_request_requests', $formdata )) {

		// If this is a self-enrollment, the participant must be enrolled
		if($enrollment->isselfenrollment) {
			$requestee = $DB->get_record('user', array('id' => $formdata->userid));

			/*
			if (!enrol_into_course($course, $requestee, 'manual')) {
				return false;
			}*/

		}

		if ($additionalqs = $DB->get_records( 'enrol_request_questions', array('enrolrequestid' => $enrollment->id))) {
			foreach ( $additionalqs as $q ) {
				$fname = 'q' . $q->id;
				if (isset ( $formdata->$fname ) && ! empty ( $formdata->$fname )) {
					$response = new stdClass ( );
					$response->requestid = $requestid;
					$response->questionid = $q->id;
					$response->response = $formdata->$fname;

					if (!$DB->insert_record ('enrol_request_responses', $response)) {
					   return false;
					}
				}
			}
			return true;
		}

		return true;
	}
	return false;
}

/**
 * Returns enrollment instances in given enrollment request
 * @param int $enrolrequestid
 * @param string $status
 * @return array of enrol instances
 */
function enrol_request_get_instances($enrolrequestid, $status=NULL) {
	global $DB, $CFG;

	if (is_null($status)) {
	    return $DB->get_records('enrol_request_requests', array('enrolrequestid'=>$enrolrequestid), 'id');
	}

	$result = $DB->get_records('enrol_request_requests', array('enrolrequestid'=>$enrolrequestid, 'status'=>$status), 'id');

	return $result;
}

/**
 * Modified from user/profile/lib.php to retrieve all values including textarea fields
 *
 * @param integer $userid   Userid of user to retrieve custom profile data
 * @return object object of user profile fields and data
 **/
function mod_enrol_request_profile_user_record($userid) {
    global $CFG;

	// Load library to get all custom profile fields and values
    require_once($CFG->dirroot.'/user/profile/lib.php');

    $user = new object();

    if ($fields = get_records_select('user_info_field')) {
        foreach ($fields as $field) {
            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
            $newfield = 'profile_field_'.$field->datatype;
            $formfield = new $newfield($field->id, $userid);
            //if ($formfield->is_user_object_data()) $user->{$field->shortname} = $formfield->data;
            $user->{$field->shortname} = $formfield->data;
        }
    }

    return $user;
}

/**
 * Get the course related to a request
 * @param integer $enrolrequestid ID of a request for enrollment
 * @return object Course that the user is requesting enrollment
 */
function enrol_request_get_course_from_request($enrolrequestid) {
	global $CFG, $DB;

	$course = NULL;

    $sql = "
			SELECT 
			    enl.courseid
			FROM 
			    {$CFG->prefix}enrol_request_requests err,
			    {$CFG->prefix}enrol_request ent,
			    {$CFG->prefix}enrol enl
			WHERE
			    err.enrolrequestid = ent.id
			    AND ent.enrolid = enl.id
			    AND err.id =  ?
   			";

	if ($result = $DB->get_record_sql($sql, array($enrolrequestid))) {
		$course = $DB->get_record('course', array('id' => $result->courseid), '*');
	}

	return $course;
}

/**
 * Get the enrollment related to a request
 * @param integer $enrolrequestid ID of a request for enrollment
 * @return object Enrollment that the user is requesting enrollment
 */
function enrol_request_get_enrollment_from_request($enrolrequestid) {
	global $CFG, $DB;

	$enrollment = NULL;

    $sql = "
			SELECT 
			    ent.id AS enrollmentid
			FROM 
			    {$CFG->prefix}enrol_request_requests err,
			    {$CFG->prefix}enrol_request ent
			WHERE
			    err.enrolrequestid = ent.id
			    AND err.id =  ?
   			";

	if ($result = $DB->get_record_sql($sql, array($enrolrequestid))) {
		$enrollment = $DB->get_record('enrol_request', array('id' => $result->enrollmentid), '*');
	}

	return $enrollment;
}

/**
 * Get the course related to a question from an enrollment request
 *
 * @param integer $questionid ID of a question from an enrollment request
 * @return object Course that the user is requesting enrollment
 */
function enrol_request_get_course_from_question($questionid) {
	global $CFG, $DB;

	$course = NULL; 

    $sql = "
			SELECT 
				enl.courseid
			FROM 
				{$CFG->prefix}enrol_request_questions erq,
				{$CFG->prefix}enrol_request ent,
				{$CFG->prefix}enrol enl
			WHERE 
				erq.enrolrequestid = ent.id
				AND ent.enrolid = enl.id
				AND erq.id = ?
   			";

	if ($result = $DB->get_record_sql($sql, array($questionid))) {
		$course = $DB->get_record('course', array('id' => $result->courseid), '*');
	}

	return $course;
}

/**
 * Get the enrollment related to a question from an enrollment request
 *
 * @param integer $questionid ID of a question from an enrollment request
 * @return object Enrollment related to a question
 */
function enrol_request_get_enrollment_from_question($questioid) {
	global $CFG, $DB;

	$enrollment = NULL;

    $sql = "
			SELECT 
			    ent.id AS enrollmentid
			FROM 
			    {$CFG->prefix}enrol_request_questions erq,
			    {$CFG->prefix}enrol_request ent
			WHERE
			    erq.enrolrequestid = ent.id
			    AND erq.id =  ?
   			";

	if ($result = $DB->get_record_sql($sql, array($questioid))) {
		$enrollment = $DB->get_record('enrol_request', array('id' => $result->enrollmentid), '*');
	}

	return $enrollment;
}

/**
 * Get the enrol instance related to a request
 * @param integer $enrolrequestid ID of a request for enrollment
 * @return object Enrol instance that the user is requesting enrollment
 */
function enrol_request_get_enrol_from_request($enrolrequestid) {
	global $CFG, $DB;

	$enrol = NULL; 

    $sql = "
			SELECT 
			    enr.id AS enrolid
			FROM 
			    {$CFG->prefix}enrol_request_requests err,
			    {$CFG->prefix}enrol_request ent,
			    {$CFG->prefix}enrol enr
			WHERE
			    err.enrolrequestid = ent.id
			    AND ent.enrolid = enr.id
			    AND err.id =  ?
   			";

	if ($result = $DB->get_record_sql($sql, array($enrolrequestid))) {
		$enrol = $DB->get_record('enrol', array('id' => $result->enrolid), '*');
	}

	return $enrol;
}

//TODO
function enrol_request_display_user_profile($userid){
	global $CFG, $DB, $OUTPUT;

	if (!$user = $DB->get_record("user", array("id" => $userid)) ) {
		error("No such user in this course");
	}

	$hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));

	echo '<div class="userprofile">';

	// Print the standard content of this page, the basic profile info
	echo $OUTPUT->heading(fullname($user));

	echo '<div class="userprofilebox clearfix"><div class="profilepicture">';
	echo $OUTPUT->user_picture($user, array('size'=>100));
	echo '</div>';

	echo '<div class="descriptionbox"><div class="description">';
	// Print the description

	if ($user->description && !isset($hiddenfields['description'])) {
        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $usercontext->id, 'user', 'profile', null);
        $options = array('overflowdiv'=>true);
        echo format_text($user->description, $user->descriptionformat, $options);
	}
	echo '</div>';


	// Print all the little details in a list
	echo '<table class="list" summary="">';

	if (! isset($hiddenfields['country']) && $user->country) {
	    enrol_request_print_row(get_string('country') . ':', get_string($user->country, 'countries'));
	}

	if (! isset($hiddenfields['city']) && $user->city) {
	    enrol_request_print_row(get_string('city') . ':', $user->city);
	}

    if ($user->address) {
        enrol_request_print_row(get_string("address").":", "$user->address");
    }
    if ($user->phone1) {
        enrol_request_print_row(get_string("phone").":", "$user->phone1");
    }
    if ($user->phone2) {
        enrol_request_print_row(get_string("phone2").":", "$user->phone2");
    }

	// print email
    enrol_request_print_row(get_string("email").":", obfuscate_mailto($user->email, ''));

	if ($user->url && !isset($hiddenfields['webpage'])) {
	    $url = $user->url;
	    if (strpos($user->url, '://') === false) {
	        $url = 'http://'. $url;
	    }
	    enrol_request_print_row(get_string("webpage") .":", '<a href="'.s($url).'">'.s($user->url).'</a>');
	}

	if ($user->icq && !isset($hiddenfields['icqnumber'])) {
	    enrol_request_print_row(get_string('icqnumber').':',"<a href=\"http://web.icq.com/wwp?uin=".urlencode($user->icq)."\">".s($user->icq)." <img src=\"http://web.icq.com/whitepages/online?icq=".urlencode($user->icq)."&amp;img=5\" alt=\"\" /></a>");
	}

	if ($user->skype && !isset($hiddenfields['skypeid'])) {
	    enrol_request_print_row(get_string('skypeid').':','<a href="callto:'.urlencode($user->skype).'">'.s($user->skype).
	        ' <img src="http://mystatus.skype.com/smallicon/'.urlencode($user->skype).'" alt="'.get_string('status').'" '.
	        ' /></a>');
	}
	if ($user->yahoo && !isset($hiddenfields['yahooid'])) {
	    enrol_request_print_row(get_string('yahooid').':', '<a href="http://edit.yahoo.com/config/send_webmesg?.target='.urlencode($user->yahoo).'&amp;.src=pg">'.s($user->yahoo)." <img src=\"http://opi.yahoo.com/online?u=".urlencode($user->yahoo)."&m=g&t=0\" alt=\"\"></a>");
	}
	if ($user->aim && !isset($hiddenfields['aimid'])) {
	    enrol_request_print_row(get_string('aimid').':', '<a href="aim:goim?screenname='.urlencode($user->aim).'">'.s($user->aim).'</a>');
	}
	if ($user->msn && !isset($hiddenfields['msnid'])) {
	    enrol_request_print_row(get_string('msnid').':', s($user->msn));
	}

	/// Print the Custom User Fields
	enrol_request_profile_display_fields($user->id);


	if (!isset($hiddenfields['mycourses'])) {
	    if ($mycourses = enrol_get_all_users_courses($user->id, true, NULL, 'visible DESC,sortorder ASC')) {
	        $shown=0;
	        $courselisting = '';
	        foreach ($mycourses as $mycourse) {
	            if ($mycourse->category) {
	                $class = '';
	                if ($mycourse->visible == 0) {
						$context = context_course::instance($mycourse->id);
	                    if (!has_capability('moodle/course:viewhiddencourses', $context)) {
	                        continue;
	                    }
	                    $class = 'class="dimmed"';
	                }
	                $courselisting .= "<a href=\"{$CFG->wwwroot}/user/view.php?id={$user->id}&amp;course={$mycourse->id}\" $class >" . format_string($mycourse->fullname) . "</a>, ";
	            }
	            $shown++;
	            if($shown==20) {
	                $courselisting.= "...";
	                break;
	            }
	        }
	        enrol_request_print_row(get_string('courseprofiles').':', rtrim($courselisting,', '));
	    }
	}
	if (!isset($hiddenfields['firstaccess'])) {
	    if ($user->firstaccess) {
	        $datestring = userdate($user->firstaccess)."&nbsp; (".format_time(time() - $user->firstaccess).")";
	    } else {
	        $datestring = get_string("never");
	    }
	    enrol_request_print_row(get_string("firstaccess").":", $datestring);
	}
	if (!isset($hiddenfields['lastaccess'])) {
	    if ($user->lastaccess) {
	        $datestring = userdate($user->lastaccess)."&nbsp; (".format_time(time() - $user->lastaccess).")";
	    } else {
	        $datestring = get_string("never");
	    }
	    enrol_request_print_row(get_string("lastaccess").":", $datestring);
	}

	if (!isset($hiddenfields['suspended'])) {
	    if ($user->suspended) {
	        enrol_request_print_row('', get_string('suspended', 'auth'));
	    }
	}

	echo "</table></div></div>";

	echo '</div>';  // userprofile class
}

function enrol_request_profile_display_fields($userid) {
    global $CFG, $USER, $DB;

    if ($categories = $DB->get_records('user_info_category')) {
        foreach ($categories as $category) {
            if ($fields = $DB->get_records_select('user_info_field', "categoryid=$category->id", NULL, 'sortorder ASC')) {
                foreach ($fields as $field) {
                    require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                    $newfield = 'profile_field_'.$field->datatype;
                    $formfield = new $newfield($field->id, $userid);
                    if ($formfield->is_visible() and !$formfield->is_empty()) {
                        enrol_request_print_row(format_string($formfield->field->name).':', $formfield->display_data());
                    }
                }
            }
        }
    }
}

function enrol_request_print_row($left, $right) {
    echo "\n<tr><td class=\"label c0\">$left</td><td class=\"info c1\">$right</td></tr>\n";
}

/**
 * This function process a request of changing user status
 */
function enrol_request_manage_process_request($enrolrequest) {
	global $DB, $OUTPUT;
	$currtime = time();
	$errors = array();

	$notified = optional_param_array('notified', 0, PARAM_RAW);
	$oldstatus = optional_param_array('oldstatus', 0, PARAM_RAW);

	$enrollment = $DB->get_record('enrol', array('id'=>$enrolrequest->enrolid));
	$requestcourse = $DB->get_record('course', array('id'=>$enrollment->courseid));

	if ($notified) {
		$notified = array_flip($notified);
	}

	// First, process the changed status, then notify users with email associated with their current status, then save object
	if ($oldstatus) {
	    foreach ($oldstatus as $rid => $sid) {
	        $updateobj = NULL;

	        // Determine if the status was changed
	        $itemstatus = optional_param('status-'.$rid, 0, PARAM_INT);
	        if ($itemstatus != $sid) {
				$updateobj = $DB->get_record('enrol_request_requests', array('id' => $rid));

				$updateobj->status = $itemstatus;
				$updateobj->timeselected = time();
				$updateobj->notified = time();
	        }

	        // Determine if user was checked to be notified
	        if (($notified) && isset($notified[$rid])) {
				if (empty($updateobj)) {
					$updateobj = $DB->get_record('enrol_request_requests', array('id' => $rid));
				}
				$updateobj->notified = enrol_request_notify_user($updateobj, $requestcourse, $errors);
				//$updateobj->notified = time();
	        }
	        if (!empty($updateobj)) {
				// Update the request
				enrol_request_update_request_status($updateobj, $enrolrequest, $requestcourse, $errors);
	        }
	    }
	}

	if (!empty($errors)) {
		/// Print any errors found to user
		$displaystr = 'Errors Occured with Saving Status:<br/>';
		foreach ($errors as $errmsg) {
			$displaystr .= $errmsg.'<br/>';
		}
		$OUTPUT->box($displaystr, 'generalbox');
	}
}

/**
 * Function updates a request status and/or notification flag if set
 *  - If notification set, emails user of their status
 *  - If status set, takes appropriate action (eg:
 *      + status pending        	(0) (shouldnt occur with current workflow / does nothing)
 *      + status enrolled       	(1) (enrols user in requested course)
 *      + status paid           	(2) (enrols user in requested course)
 *      + status notselected    	(3) (does not enroll/allow access to course)
 *      + status selected       	(4) (allows access to course/does not enroll)
 *      + waiting list          	(6) (shouldnt occur with current workflow / does nothing)
 *      + payment not received  	(7) (does nothing)
 *      + early bird            	(8) (does nothing)
 *      + status enrolled no indes	(9) (enrols user in requested course) 
 *
 * @param object $requestobj Object with at least the update record id and one update field
 *                                  $requestobj->id = updaterecid
 *                                  $requestobj->notified   = timestamp
 *                                  $requestobj->status     = int of status 0-3
 * @param array $errors array by ref, containing updateid => errormessage
 * @param bool  $process    True to enrol, false to do nothing with changed status
 * @return bool true if record updated and no errors in notification/enrollment, false otherwise
 **/
function enrol_request_update_request_status($requestobj, $modinst = NULL, $requestcourse = NULL, &$errors, $process = true) {
    global $DB, $USER;

    if ($process) {
		$oldstatus = $DB->get_field('enrol_request_requests', 'status', array('id' => $requestobj->id));
    }

    if (!$DB->update_record('enrol_request_requests', $requestobj)) {
        $errors[] = get_string('errupdatestatus', 'enrol_request');
        return false;
    }

    // Logic to properly handle new status for request, if status was changed
    if ($process && ($requestobj->status != $oldstatus)) {
        switch ($requestobj->status) {
            // Pending status, no change
            case STATUS_PENDING:
            // Not selected status, do nothing
            case STATUS_NOTSELECTED:
                break;
			// Enrolled status, enroll directly into course
            case STATUS_ENROLLED:
			// Enrolled No INDES status, enroll directly into course
            case STATUS_ENROLLED_NO_INDES:
			// Paid status, enroll directly into course
            case STATUS_PAID:
                $requestee = $DB->get_record('user', array('id' => $requestobj->userid));

		        $student = get_archetype_roles('student');
		        $student = reset($student);

				// enrol instance
				$enrol = enrol_request_get_enrol_from_request($requestobj->id);

				// Enrollment plugin
				$plugin = enrol_get_plugin('request');
				$plugin->enrol_user($enrol, $requestee->id, $student->id, time());

                break;
			case STATUS_SELECTED:
			case STATUS_SELECTED_SCHOLARSHIP:
            // Selected status, allow entry into course which may require payment
            // via enrollment plugin.  Do nothing here
            case STATUS_WAITING_LIST:
            // Waiting list status, do nothing
			case STATUS_PAYMENT_NOT_RECEIVED:
			// Payment Not Received status, do nothing
			case STATUS_EARLY_BIRD:
			// Payment Received Early status, do nothing
        }
    }
    return true;
}

/* Function to convert any date on timestamp format to a readable format in Spanish, English, French or Portuguese Format
*/

function date2_human_format($timestamp, $lang){
    $actualday = date('d', $timestamp);
    $actualmonth = date('m', $timestamp);
    $actualyear = date('Y', $timestamp);
    $actualmonth = get_string("month".$actualmonth, 'certificate');

    if('ES' === strtoupper($lang)){
        $date = $actualday.' '.get_string('of_month', 'certificate').' '.$actualmonth.' '.get_string('of_year', 'certificate').' '.$actualyear;
    }elseif('EN' === strtoupper($lang)){
        $date = $actualmonth.' '.$actualday.', '.$actualyear;
    }elseif('PT_BR' === strtoupper($lang)){
        $date = $actualday.' '.get_string('from', 'certificate').' '.$actualmonth.' '.get_string('of_year', 'certificate').' '.$actualyear;
    }elseif('FR' == strtoupper($lang)){
        $date = $actualday.' '.$actualmonth.' '.$actualyear;
    }

    return $date;
}

//TODO review this function. Email received does not contains the correct body:
/*
 * $a->recipientfullname, your pending request for enrollment into course: courseurl\">$a->coursefullname has been updated.
 * Your status has been changed to $a->status.
 *
 * /

/**
 * Notifies the user associated with the updateobject
 *
 * @param $updateobj    object  User enrollment request db object
 * @param $errors       array   Returns errors in the array
 * @return timestamp/null       Timestamp on successful email, NULL on failure
 */
function enrol_request_notify_user($updateobj, $requestcourse, &$errors) {
	global $CFG, $USER, $DB;

	if (!isset($updateobj->status)) {
	    return NULL;
	}

	$site = get_site();

	$statusshort = 'pending';
	$statusname = get_string('status'.$updateobj->status, 'enrol_request');

    $recipient = $DB->get_record('user', array('id' => $updateobj->userid));

    $emailparams = new stdClass();
    $emailparams->recipientfullname = fullname($recipient);
    $emailparams->coursefullname    = $requestcourse->fullname;
    $emailparams->courseurl         = $CFG->wwwroot.'/course/view.php?id='.$requestcourse->id;
    $emailparams->status            = $statusname;
    if(empty($requestcourse->lang)){
        $emailparams->lang          = "ES";
    }else{
        $emailparams->lang          = strtoupper($requestcourse->lang);
    }

    $startdate = date2_human_format($requestcourse->startdate, $emailparams->lang);
    $emailparams->startdate         = $startdate;

    $actualtimestamp = time()+date("Z");
    $actualdate = date2_human_format($actualtimestamp, $emailparams->lang);
    $emailparams->actualdate        = $actualdate;

    $courseformatoptions = course_get_format($requestcourse->id)->get_course();
    if(isset($courseformatoptions->numsections)){
        $numSections = $courseformatoptions->numsections;
        $emailparams->numsections   = $numSections;
    }else{
        $emailparams->numsections   = floor(($requestcourse->enddate - $requestcourse->startdate)/604800);
	}

	switch ($updateobj->status) {
		case STATUS_SELECTED:
            $statusshort = 'selected';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectselected', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlselected', 'enrol_request', $emailparams);
            break;
		case STATUS_SELECTED_SCHOLARSHIP:
            $statusshort = 'selectedscholarship';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "SCHOLARSHIP") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
					break;
                }
            }
            $subject = get_string('emailsubjectselectedscholarship', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlselectedscholarship', 'enrol_request', $emailparams);
            break;
        case STATUS_NOTSELECTED:
            $statusshort = 'notselected';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectnotselected', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlnotselected', 'enrol_request', $emailparams);
            break;
        case STATUS_PAID:
            $statusshort = 'paid';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectpaid', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlpaid', 'enrol_request', $emailparams);
            break;
        case STATUS_ENROLLED:
            $statusshort = 'enrolled';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
			$subject = get_string('emailsubjectenrolled', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlenrolled', 'enrol_request', $emailparams);
            break;
        case STATUS_ENROLLED_NO_INDES:
            $statusshort = 'enrolled_no_indes';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
			$subject = get_string('emailsubjectenrollednoindes', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlenrollednoindes', 'enrol_request', $emailparams);
            break;
        case STATUS_PENDING:
            $statusshort = 'pending';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectpending', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlpending', 'enrol_request', $emailparams);
            break;
        case STATUS_WAITING_LIST:
            $statusshort = 'waitinglist';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectwaitinglist', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlwaitinglist', 'enrol_request', $emailparams);
            break;
        case STATUS_PAYMENT_NOT_RECEIVED:
            $statusshort = 'paymentnotreceived';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectpaymentnotreceived', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlpaymentnotreceived', 'enrol_request', $emailparams);
            break;
        case STATUS_EARLY_BIRD:
            $statusshort = 'earlybird';
            $enrolinstances = enrol_get_instances($requestcourse->id, true);
            foreach ($enrolinstances as $courseenrolinstance) {
                if (($courseenrolinstance->enrol == "paypalrequest" || $courseenrolinstance->enrol == "striperequest") && trim(strtoupper($courseenrolinstance->name)) == "REGULAR") {
                    $enrolenddate = date2_human_format($courseenrolinstance->enrolenddate, $emailparams->lang);
                    $emailparams->enrolenddate = $enrolenddate;
                    $emailparams->cost = $courseenrolinstance->cost;
                    $emailparams->cost_transfer = $courseenrolinstance->customint1;
                    break;
                }
            }
            $subject = get_string('emailsubjectearlybird', 'enrol_request', $emailparams);
            $msgbody = get_string('emailbodyhtmlearlybird', 'enrol_request', $emailparams);
            break;
	}

    $bodyhtml = '<head>';

    $bodyhtml .= '</head>'.
                 "\n<body id=\"enrolrequest_notify\">\n<div id=\"page\">\n".
                 $msgbody.
                 "\n</div>\n</body>";

    $message = $msgbody;
	if (!(email_to_user($recipient, $site->shortname, $subject, $msgbody, $bodyhtml))) {
        $errors[] = 'Failed to send email to user: '.fullname($recipient);
        return NULL;
    }

    return time();
}

/**
 * Function deletes request and associated custom answer responses
 *
 * @param integer   $requestid  The id of the request to delete
 * @return boolean
 **/
function enrol_request_delete_request($requestid) {
	global $USER, $DB;

	if ($DB->record_exists('enrol_request_requests', array('id' => $requestid ))) {
		$DB->delete_records('enrol_request_requests', array('id' => $requestid ));

		if ($DB->record_exists('enrol_request_responses', array('requestid' => $requestid ))) {
			$DB->delete_records('enrol_request_responses', array('requestid' => $requestid ));
		}
	}
	return true;
}

/**
 * Adds a question
 *
 * @param object   $formdata  Object with all attributes of a question
 * @return void
 **/
function enrol_request_add_question($formdata) {
	global $USER, $DB;

	if($formdata && $formdata->enrolrequestid && $DB->record_exists('enrol_request', array('id' => $formdata->enrolrequestid))){
		$record = new stdClass();
		$record->enrolrequestid	= $formdata->enrolrequestid;
		$record->questiontext	= $formdata->questiontext;

		$DB->insert_record('enrol_request_questions', $record, false);

	} else {
		print_error('Question data not well defined!');
	}
}

/**
 * Updates an existing question
 *
 * @param object   $formdata  Object with all attributes of a question
 * @return void
 **/
function enrol_request_edit_question($formdata) {
	global $USER, $DB;

	if($formdata && $formdata->id && $DB->record_exists('enrol_request', array('id' => $formdata->enrolrequestid))){
		$question = $DB->get_record('enrol_request_questions', array('id' => $formdata->id));

	} else {
		print_error('Question data not well defined!');
	}

	if($question){
		$question->questiontext = $formdata->questiontext;

		$DB->update_record('enrol_request_questions', $question);
	}
}

/**
 * Deletes question and related responses
 *
 * @param integer   $questionid  The id of the question
 * @return void
 **/
function enrol_request_delete_question($questionid) {
	global $USER, $DB;

	if ($DB->record_exists('enrol_request_questions', array('id' => $questionid ))) {
		$DB->delete_records('enrol_request_questions', array('id' => $questionid ));

		if ($DB->record_exists('enrol_request_responses', array('questionid' => $questionid ))) {
			$DB->delete_records('enrol_request_responses', array('questionid' => $questionid ));
		}
	}
}

/**
 * Change the status of an enrollment request to enrolled.
 * This method is important for the integration between enrol_request and enrol_paypalrequest
 *
 * @param object $course
 * @param object $user
 * @return boolean Was the status changed?
 **/
function enrol_request_change_status_enrolled($course, $user) {
	global $CFG, $DB;

	$status_changed = FALSE;

	if ($enrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'request', 'status' => 0))) {
		if ($enrolrequest = $DB->get_record('enrol_request', array('enrolid' => $enrol->id))) {
			if ($userrequest = $DB->get_record('enrol_request_requests', array('enrolrequestid' => $enrolrequest->id, 'userid' => $user->id))) {
				$userrequest->status = STATUS_ENROLLED;
				$DB->update_record('enrol_request_requests', $userrequest);

				$status_changed = TRUE;
			}
		}
	}

	return $status_changed;
}
