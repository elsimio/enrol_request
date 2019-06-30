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
 * Manage view: view for managing sumbmissions
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */

require('../../config.php');
require($CFG->dirroot.'/enrol/request/lib.php');
require($CFG->dirroot.'/enrol/request/request_form.php');
require($CFG->dirroot.'/enrol/request/requests_table.php');

$messageInformation = '';

// requires log-in
require_login();

$enrolid   = required_param('enrolid', PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);  				// Number of results to display per page
$selstatus = optional_param('status', 'pending', PARAM_ALPHA);		// Type of result to query
$statussubmit = optional_param('statussubmit', '', PARAM_ACTION);	// Used to verify if the 

$enrol = $DB->get_record('enrol', array('id'=>$enrolid), '*', MUST_EXIST);
$enrolrequest = $DB->get_record('enrol_request', array('enrolid'=>$enrolid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$enrol->courseid), '*', MUST_EXIST);

// define COURSE context to check if the user has access to view/edit it
$context = context_course::instance($course->id);
require_capability('enrol/request:manage', $context);

$PAGE->set_url('/enrol/request/request.php', array('enrolid'=>$enrolid));
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('course');
$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
$PAGE->requires->js('/enrol/request/request.js');
echo "\n<script type=\"text/javascript\">
//<![CDATA[
function selectAll(){
	var items=document.getElementsByName('notified[]');
	for(var i=0; i<items.length; i++){
		if(items[i].type=='checkbox')
			items[i].checked=true;
	}
}

function UnSelectAll(){
	var items=document.getElementsByName('notified[]');
	for(var i=0; i<items.length; i++){
		if(items[i].type=='checkbox')
			items[i].checked=false;
	}
}
//]]></script>\n";

// Print the page header
$strenrolrequests = get_string("modulenameplural", "enrol_request");
$strenrolrequest  = get_string("modulename", "enrol_request");

// Print the main part of the page
$OUTPUT->heading($strenrolrequests . ': ' .$course->fullname);

echo $OUTPUT->header();

// If this form was submitted, need to process posted status array and notify array to update requests and optionally notify selected request users
if ($statussubmit) {
	enrol_request_manage_process_request($enrolrequest);
}

// shows manager view
manager_view($enrolrequest);

// Finish the page
echo $OUTPUT->footer();

/**
 * This function shows the manager view
 * for a user that has access to change status of requests
 */
function manager_view($enrolrequest) {
	global $CFG, $USER, $DB, $OUTPUT, $selstatus, $perpage, $enrol;
	
	$strenrolrequest  = get_string("modulename", "enrol_request");
	
	/// Print the list of instances with associated course in sortable table
	$table = new enrol_request_requests_table();
	
	/// Define table columns, headers, and base url
    $table->define_columns(array('fullname', 'status', 'timesubmitted', 'view', 'changestatus', 'notified', 'notify'));
    //$table->define_headers(array(get_string('username'), get_string('status', 'enrol_request'), get_string('timesubmitted', 'enrol_request'), get_string('view'), get_string('changestatus', 'enrol_request'), get_string('notified', 'enrol_request'), get_string('notify')));
    $table->define_headers(array(get_string('username'), get_string('status', 'enrol_request'), get_string('timesubmitted', 'enrol_request'), get_string('view'), get_string('changestatus', 'enrol_request'), get_string('status', 'enrol_request'), get_string('notify', 'enrol_request')));
    $table->define_baseurl("$CFG->wwwroot/enrol/request/manage.php?enrolid={$enrol->id}&perpage=$perpage&status=$selstatus");
	
	/// Table settings
    $table->sortable(true, 'timesubmitted');
    $table->no_sorting('view');
    $table->no_sorting('changestatus');
    $table->no_sorting('notify');
    $table->collapsible(false);
    $table->pageable(true);
	
	/// Column styles
    $table->column_style('name', 'white-space', 'nowrap');
	
	/// Set attributes in the table tag
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('cellpadding', '5px');
    $table->set_attribute('width', '100%');
    $table->set_attribute('id', 'enrol_request_requests');
    $table->set_attribute('class', 'generaltable generalbox enrol-request-center-table');
	
    $table->setup();
	
	/// Prepare sql for query
    $sqlselectarr   = array('er.id', 'er.status as status', 'er.timesubmitted as timesubmitted', 'er.notified', 'u.firstname as firstname', 'u.lastname as lastname');
    $sqlfromarr     = array("{$CFG->prefix}enrol_request_requests er", "{$CFG->prefix}user u");
    $sqlwherearr    = array("er.enrolrequestid = {$enrolrequest->id}", "er.userid = u.id");
    $orderbysql        = '';

    if ($sort = $table->get_sql_sort()) {
        $orderbysql = ' ORDER BY '.$sort;
    }

    switch ($selstatus) {
        case 'all':
            $statussql = '';
            break;
        case 'pending':
            $sqlwherearr['status'] = 'er.status = '.STATUS_PENDING;
            break;
        case 'enrolled':
            $sqlwherearr['status'] = 'er.status = '.STATUS_ENROLLED;
            break;
        case 'paid':
            $sqlwherearr['status'] = 'er.status = '.STATUS_PAID;
            break;
        case 'notselected':
            $sqlwherearr['status'] = 'er.status = '.STATUS_NOTSELECTED;
            break;
        case 'selected':
            $sqlwherearr['status'] = 'er.status = '.STATUS_SELECTED;
            break;
        case 'selectedscholarship':
            $sqlwherearr['status'] = 'er.status = '.STATUS_SELECTED_SCHOLARSHIP;
            break;
        case 'waitinglist':
            $sqlwherearr['status'] = 'er.status = '.STATUS_WAITING_LIST;
            break;
        case 'paymentnotreceived':
            $sqlwherearr['status'] = 'er.status = '.STATUS_PAYMENT_NOT_RECEIVED;
            break;
        case 'earlybird':
            $sqlwherearr['status'] = 'er.status = '.STATUS_EARLY_BIRD;
            break;			
        case 'enrolled_no_indes':
            $sqlwherearr['status'] = 'er.status = '.STATUS_ENROLLED_NO_INDES;
            break;			
	}
	
	if (isset($sqlwherearr['status'])) {
		$statussql = ' AND '.$sqlwherearr['status'];
	} else {
		$statussql = '';
	}
	
	/// Set table paging info	
	$submiturl = $CFG->wwwroot.'/enrol/request/manage.php?enrolid='.$enrol->id;

	$sqltotal = "SELECT COUNT(er.id) FROM {$CFG->prefix}enrol_request_requests er WHERE er.enrolrequestid = {$enrolrequest->id} $statussql";
    $total = $DB->count_records_sql($sqltotal);
	
    $table->pagesize($perpage, $total);
	
	// Get enrollment requests
    $sql = 'SELECT '.implode(', ', $sqlselectarr).' FROM '.implode(', ', $sqlfromarr).' WHERE '.implode(' AND ', $sqlwherearr).$orderbysql;
	$requests = $DB->get_records_sql($sql, null, $table->get_page_start(), $table->get_page_size());
    
	// Setting table wrap parameters to prepare presentation
	$table->set_wrap_parameters(count($requests), $total, $submiturl, $perpage, $selstatus);
	$data = array();
	
	if ($requests = $DB->get_records_sql($sql, null, $table->get_page_start(), $table->get_page_size())) {
		$strview = get_string('view');
		$strviewimg = '<img src="'.$OUTPUT->image_url('i/hide').'" alt="'.$strview.'" />';
		$strviewtitle = $strview.' '.$strenrolrequest;
		$all_requests_ids = array();
		
		foreach ($requests as $r) {
			array_push($all_requests_ids, $r->id);
			$link = new moodle_url('/enrol/request/view_request.php', array('enrolrequestid'=>$r->id));
	   		$action = new popup_action('click', $link, 'ratingscales', array('height' => 830, 'width' => 730, 'toolbar' => false));
	   		$actionview = $OUTPUT->action_link($link, $strviewimg, $action, array('title'=>$strviewtitle));
			$status = 'status'.$r->status;
			
			if (!isset($$status)) {
				$$status = get_string($status, 'enrol_request');
			}
			
			$checked = false;
			$datenotified = "-";
			
			if ($r->notified > 0) {
			   $checked = true;
			   $datenotified = date('Y/m/d h:i:s', $r->notified);
			}
			
			$checkbox = '<input name="notified[]" type="checkbox" value="'.$r->id.'" alt="checkbox" />';
			
			$statii = array(
							'0' => get_string('status0', 'enrol_request'), 
							'1' => get_string('status1', 'enrol_request'), 
							'2' => get_string('status2', 'enrol_request'), 
							'3' => get_string('status3', 'enrol_request'), 
							'4' => get_string('status4', 'enrol_request'),
							'5' => get_string('status5', 'enrol_request'),
                            '6' => get_string('status6', 'enrol_request'),
							'7' => get_string('status7', 'enrol_request'),
							'8' => get_string('status8', 'enrol_request'),
							'9' => get_string('status9', 'enrol_request')							
							);
			$disabled = false;
			
			// Disables if status are enrolled/paid or if status is not enrolled and user was notified
			if ($r->status == 1 || $r->status == 2 || ($r->status == 3 && $r->notified > 0) || $r->status == 4) {
				// Disable this limiting feature for now
				$disabled = false;
			}
			
			$instance['id'] = "status-".$r->id;
			$statusmenu = html_writer::select($statii, "status-".$r->id, $r->status, false, $instance);
			$oldstatus = '<input type="hidden" name="oldstatus['.$r->id.']" value="'.$r->status.'"/>';
			
			if ($checked) {
				$oldval = '1';
			} else {
				$oldval = '0';
			}
			
			$oldnotify = '<input type="hidden" name="oldnotify['.$r->id.']" value="'.$oldval.'"/>';
			
			array_push($data, array(
								$r->firstname.' '.$r->lastname, 
								get_string($status, 'enrol_request'), 
								userdate($r->timesubmitted), 
								$actionview, 
								$statusmenu.$oldstatus, 
								$datenotified, 
								$checkbox.$oldnotify));
			
		}
	}
	
	$perpageoptions = array(5 => '5', 10 => '10', 20 => '20', 50 => '50', 100 => '100');
	$statusoptions = array(
						'pending' => get_string('status0', 'enrol_request'),
						'enrolled' => get_string('status1', 'enrol_request'),
						'paid' => get_string('status2', 'enrol_request'),
						'notselected' => get_string('status3', 'enrol_request'),
						'selected' => get_string('status4', 'enrol_request'),
						'selectedscholarship' => get_string('status5', 'enrol_request'),
                        'waitinglist' => get_string('status6', 'enrol_request'),
                        'paymentnotreceived' => get_string('status7', 'enrol_request'),
                        'earlybird' => get_string('status8', 'enrol_request'),
                        'enrolled_no_indes' => get_string('status9', 'enrol_request'),
						'all' => get_string('all')
						);
	
	/// Per page and Status filter options form
	echo $OUTPUT->box_start();

	echo "<form id=\"pagingform\" method=\"post\" action=\"$submiturl\">";
	
	if ($requests) {
		$requests_list = implode(",", $all_requests_ids);
	
	 	echo '<span style="float:right;">';
	 	echo '<label for="menuchangeAllStatus">' . get_string('changeAllStatusTo', 'enrol_request') . ': </label>';
		$attributes['onchange'] = 'enrol_request_change_all_status_to(this.value, \''.$requests_list.'\');';				 
		echo html_writer::select($statii, 'changeAllStatus', '', false, $attributes);
		//echo html_writer::select($options, 'viewing', $viewing, false, array('id' => 'viewing','onchange' => 'this.form.submit()'));	  
		echo '</span>';
	}
	
	echo '<label for="menuperpage">'.get_string('perpage', 'enrol_request').':</label> ';
	$attributes['onchange'] = 'enrol_request_submit(\'' . $submiturl . '&status=' . $selstatus . '&perpage=' . '\'+this.value, \'pagingform\');';				 
	echo html_writer::select($perpageoptions, 'perpage', $perpage, '', $attributes);
	
	echo '<label style="margin-left: 5px;" for="menustatus">'.get_string('status', 'enrol_request').':</label> ';
	$attributes['onchange'] = 'enrol_request_submit(\'' . $submiturl . '&status=\'+this.value+\'&perpage=' . $perpage . '\', \'pagingform\');';
	echo html_writer::select($statusoptions, 'status', $selstatus, '', $attributes);
	
	echo '</form>';
	
	echo $OUTPUT->box_end();
	
	if ($requests) {
		/// Print results table with status/notify array elements within form element
		echo '<form id="statusform" method="post" action="'.$submiturl.'">';
		
		/// Hidden form elements for various url elements
		echo '<input type="hidden" value="statussubmit" name="statussubmit"/>';
		echo '<input type="hidden" value="'.$perpage.'" name="perpage"/>';
		echo '<input type="hidden" value="'.$selstatus.'" name="status"/>';		
		
		// In order to prevent the table of output prematurely, data is added only here
		foreach ($data as $datarow) {
			$table->add_data($datarow);
		}
		
		// Printing page details
		echo $table->finish_output();
		
        echo '<div class="enrol-request-center-text">';
		echo '<br/><input type="submit" value="'.get_string('updatestatusandnotify', 'enrol_request').'" title="'.get_string('updatestatustooltip', 'enrol_request').'" onclick="return confirm(\''. get_string('confirmchangestatus', 'enrol_request') .'\');" />';
		echo '<input type="button" onclick="selectAll()" value="'.get_string('selectall').'"/>';
		echo '<input type="button" onclick="UnSelectAll()" value="'.get_string('deselectall').'"/>';
        echo '</div>';
		echo '</form>';
	} else {
		if($selstatus == 'all'){
			$message = get_string('noEnrollments', 'enrol_request');	
		} else {
			$message = get_string('noEnrollmentsFound', 'enrol_request');
		}
		
		// Center all text elements on this form/page
		echo "<br>";
		echo $OUTPUT->container($message, 'generalbox', 'notice');		
	}
}
