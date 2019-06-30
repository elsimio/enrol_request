/**
 * Various Javascript functions for handling some dynamic UI
 *
 * @author Michael Avelar <michaela@moodlerooms.com>
 * @version javascript.php v1.0
 * @package enrolrequest
 **/

/**
 * function changes value of parent form's status select-box
 *
 * @param int requestid Id of target request
 * @param int status    Status id
 * @return bool
 **/
function enrol_request_change_parent_status(requestid, status) {
    if (window.opener.document.getElementById('status-'+requestid).value = status) {
        return true;
    }
    return false;
}

/**
 * function changes value of all status select-box
 *
 * @param int status    Status id
 * @param int requests  Requests ids
 **/
function enrol_request_change_all_status_to(status, requests) {
	requests = requests.split(",");
	
	for (i in requests) {
		document.getElementById('status-'+requests[i]).value = status;
	}
}

/**
 * Function submits form with passed url
 *
 **/
function enrol_request_submit(url, formid) {
    var theform = document.getElementById(formid);
    
    if(!theform) {
        return false;
    }
    if(theform.tagName.toLowerCase() != 'form') {
        return false;
    }
    // Change the form url to passed url
    theform.action = url;

    if(!theform.onsubmit || theform.onsubmit()) {
        return theform.submit();
    }
}