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
 * Enrollment Request plugin: language string definitions.
 * 
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */
$string['pluginname'] = 'INDES Enrollment Request';
$string['pluginname_desc'] = 'Enrollment based on user request for further analysis by Course Administrators.';

$string['addinstancebeforequestions'] = 'Please, save first the Enrollment Request then add the questions';
$string['additionalquestions'] = 'Questions';
$string['additionalquestions_help'] = 'These are the questions to be answered by the users requesting enrollment';
$string['additionalquestionstitle'] = 'Enrollment Request Questions';
$string['addmorequestions'] = 'Add 1 more question';
$string['basicdatatitle'] = 'Basic Data';
$string['beforetoday'] = 'Date must be at least a year before today.';
$string['deadline'] = 'Deadline';
$string['of'] = 'of';
$string['showing'] = 'Showing';
$string['changeAllStatusTo'] = 'Change all Status to';
$string['canresubmit'] = 'Can Resubmit Requests';
$string['changestatus'] = 'Change Status';
$string['chooseacourse'] = 'Must choose a course';
$string['city'] = 'City';
$string['confirmchangestatus'] = 'Are you sure you want to permanently change all visible request status and notify selected users?';
$string['confirmdeleterequest'] = 'Are you sure you want to permanently delete this pending request?';
$string['country'] = 'Country';
$string['course_courseurl'] = 'Course Link';
$string['customtitle'] = 'Custom Data';
$string['dateofbirth'] = 'Date of Birth';
$string['defaultname'] = 'Request Enrollment';
$string['department'] = 'Section / Department / Division';
$string['educationtitle'] = 'Education';
$string['email'] = 'Email';
$string['emailsubject'] = 'Your Pending course enrollment request status has been updated.';
$string['emailsubjectselected'] = 'BID-INDES Virtual Course: Selected {$a->coursefullname}';
$string['emailsubjectselectedscholarship'] = 'BID-INDES Virtual Course: Selected with Scholarship {$a->coursefullname}';
$string['emailsubjectnotselected'] = 'BID-INDES Virtual Course: Not Selected {$a->coursefullname}';
$string['emailsubjectpaid'] = 'BID-INDES Virtual Course: Paid {$a->coursefullname}';
$string['emailsubjectenrolled'] = 'BID-INDES Virtual Course: Enrolled {$a->coursefullname}';
$string['emailsubjectenrollednoindes'] = 'BID-INDES Virtual Course: Enrolled No INDES {$a->coursefullname}';
$string['emailsubjectpending'] = 'BID-INDES Virtual Course: Pending {$a->coursefullname}';
$string['emailsubjectwaitinglist'] = 'BID-INDES Virtual Course: Waiting List {$a->coursefullname}';
$string['emailsubjectpaymentnotreceived'] = 'BID-INDES Virtual Course: Payment Not Received {$a->coursefullname}';
$string['emailsubjectearlybird'] = 'BID-INDES Virtual Course: Early-bird Payment Received {$a->coursefullname}';
$string['emailbodyhtml'] = '{$a->firstname} {$a->lastname}, curso : {$a->coursefullname} se ha actualizado.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['selected1'] = '<p>Please confirm the locations of this Moodle installation.</p><p><b>Web address:</b>Specify the full web address where Moodle will be accessed.  If your web site is accessible via multiple URLs then choose the most natural one that your students would use.  Do not include a trailing slash.</p><p><b>Moodle directory:</b>Specify the full directory path to this installationMake sure the upper/lower case is correct.</p><p><b>Data directory:</b>You need a place where Moodle can save uploaded files.  Thisdirectory should be readable AND WRITEABLE by the web server user (usually "nobody" or "apache"), but it must not be accessible directly via the web. The installer will try to create it if doesnt exist.</p>';
$string['emailbodyhtmlselected'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlselectedscholarship'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlnotselected'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlpaid'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlenrolled'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlenrollednoindes'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlpending'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlwaitinglist'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlpaymentnotreceived'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['emailbodyhtmlearlybird'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/>  Your status has been changed to <i>{$a->status}</i>.';
$string['enablestatususe'] = 'Enable Status';
$string['enablestatususedesc'] = 'Uncheck to disable usage of this status sitewide.';
$string['enrolrequest'] = 'Enrollment Request';
$string['guestnotallowed'] = 'Not Allowed for Guest';
$string['guestlogininfo'] = 'Please log in in order to submit for this course';
$string['erremail'] = 'Couldn\'t email user';
$string['errenrol'] = 'Couldn\'t enrol user into course';
$string['errorrequest'] = 'Couldn\'t save response';
$string['errupdatestatus'] = 'Couldnt update record';
$string['female'] = 'Female';
$string['fieldofstudy'] = 'Field of study';
$string['firstname'] = 'First and Middle Name';
$string['gender'] = 'Gender';
$string['generalhelp'] = 'General Settings';
$string['graduationyear'] = 'Year of Graduation';
$string['highestgrade'] = 'Highest grade obtained';
$string['institutename'] = 'Name of the Institution';
$string['isselfenrollment'] = 'Is this a Self-Enrollment';
$string['lastname'] = 'Last Name';
$string['major'] = 'Major';
$string['male'] = 'Male';
$string['manageruser'] = 'Managing User';
$string['managerequests'] = 'Manage Requests';
$string['maxenroldays'] = 'Maximum Enrollment Days';
$string['maxenroldaysdesc'] = 'Max allowed number of days a registering user can be in \"Selected\" status without being changed.  If the max allowable days is exceeded for a user, that user\'s request status will change to \"Not Selected\".  (Set to 0 to disable this automatic status change).';
$string['modname'] = 'enrolrequest';
$string['modulename'] = 'Enrollment Request';
$string['modulenameplural'] = 'Enrollment Requests';
$string['mustchoose'] = 'Must choose an option';
$string['notified'] = 'Date Notified';
$string['notify'] = 'Notify';
$string['nationality'] = 'Nationality';
$string['newrequest'] = 'New Enrollment Request';
$string['numrequests'] = '# Pending Requests';
$string['perpage'] = 'Per Page';
$string['phone'] = 'Phone';
$string['position'] = 'Position';
$string['question'] = 'Question';
$string['questionname'] = 'Question Name';
$string['questiontext'] = 'Question Text';
$string['requestenrollment'] = 'Request Enrollment';
$string['requestuser'] = 'Requesting User';
$string['responsibilityone'] = '1: ';
$string['responsibilitytwo'] = '2: ';
$string['responsibilitythree'] = '3: ';
$string['responsibilities'] = 'Briefly, the three most important roles and responsibilities in charge';
$string['request:config'] = 'Allows course administrators to set the configuration of an enrollment request';
$string['request:delete'] = 'Allows course administrators to delete requests';
$string['request:editquestion'] = 'Allows course administrators to add or edit questions';
$string['request:enabledisable'] = 'Allows course administrators to enable/disable requests';
$string['request:export'] = 'Allows user to export enrollment requests as a file';
$string['request:manage'] = 'Allows course administrators to review enrollment requests and update their status';
$string['request:request'] = 'Allows to user to request enrollment';
$string['request:unenrol'] = 'Allows course requester to cancel his/her own enrollment request';
$string['request:view'] = 'Allows user to view enrollment request form';
$string['request:viewrequests'] = 'Allows user to view all enrollment requests, edit status, and notify';
$string['requestedcourse'] = 'Requested Course';
$string['requestnum'] = 'Request #';
$string['siteonlytxt'] = 'The Enrollment Request module can only be installed on the Site page.';
$string['status'] = 'Status';
$string['status0'] = 'Pending';
$string['status1'] = 'Enrolled';
$string['status2'] = 'Paid';
$string['status3'] = 'Not Selected';
$string['status4'] = 'Selected';
$string['status5'] = 'Selected with Scholarship';
$string['status6'] = 'Waiting List';
$string['status7'] = 'Payment not Received';
$string['status8'] = 'Early-bird';
$string['status9'] = 'Enrolled No INDES';
$string['statusmessage'] = 'Your current enrollment status is: <i>{$a}</i>';
$string['statuspending'] = 'Pending Status';
$string['statuspendingdesc'] = 'Requested enrollment <b><i>pending</i></b> status options';
$string['statusenrolled'] = 'Enrolled Status';
$string['statusenrolleddesc'] = 'Requested enrollment <b><i>enrolled</i></b> status options';
$string['statusenrollednoindes'] = 'Enrolled No INDES Status';
$string['statusenrollednoindesdesc'] = 'Requested enrollment <b><i>enrolled no INDES</i></b> status options';
$string['statusnotselected'] = 'Not Selected Status';
$string['statusnotselecteddesc'] = 'Requested enrollment <b><i>not selected</i></b> status options';
$string['statusselected'] = 'Selected Status';
$string['statusselecteddesc'] = 'Requested enrollment <b><i>selected</i></b> status options';
$string['statusselectedscholarship'] = 'Selected with Scholarship Status';
$string['statusselectedscholarshipdesc'] = 'Requested enrollment <b><i>selected with scholarship</i></b> status options';
$string['statuspaid'] = 'Paid Status';
$string['statuspaiddesc'] = 'Requested enrollment <b><i>paid</i></b> status options';
$string['success'] = 'Your request has been successfully submitted. INDES will notify the applicant about the selection results. This will take place one week after the application deadline. If you are selected, we will provide you with instructions on how to pay the tuition and the corresponding terms of payment.';
$string['totalpending'] = 'Total Pending';
$string['timesubmitted'] = 'Date Submitted';
$string['updatestatusandnotify'] = 'Update/Notify Requests';
$string['updatestatustooltip'] = 'Updates visible requests status and notify selected valid users';
$string['worktitle'] = 'Work';
$string['deadlineHasPassed'] = 'Registration for this course is now closed.';
$string['noEnrollments'] = 'There is no enrollment request for this course yet.';
$string['noEnrollmentsFound'] = 'There is no enrollment request associated with the selected status';
$string['requestenrollmentcourse'] = 'Register for this course';
$string['buttonreviewenrollment'] = 'Review my enrollment';
$string['buttondeleteenrollment'] = 'Delete my enrollment';
$string['unenrolconfirm'] = 'Do you really want to delete your enrollment request from course "{$a}"?';
$string['disableenrollmentconfirm'] = 'Do you really want to disable the enrollment of participant "{$a->user}" in the course "{$a->course}"?';
$string['enableenrollmentconfirm'] = 'Do you really want to enable the enrollment of participant "{$a->user}" in the course "{$a->course}"?';
$string['disableenrollment'] = 'Disable enrollment of participant';
$string['enableenrollment'] = 'Enable enrollment of participant';
$string['deleteenrollmentconfirm'] = 'Do you really want to delete the enrollment of participant "{$a->user}" in the course "{$a->course}"?';
$string['deleteenrollment'] = 'Delete enrollment of participant';
$string['deletequestionconfirm'] = 'Do you really want to delete the following question"?';
$string['headeraddquestion'] = 'Add New Question';
$string['headereditquestion'] = 'Edit Question';
