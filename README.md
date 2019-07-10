# enrol_request
The enrolment plugin "enrol request" add a request step with questions into the course enrolment process. Users will be informed by mail as soon as their course application has been changed of status. The plugin can handle 10 different status. This plugin can work to accept payments with the plugin enrol_paypalrequest and stripe payment.<br>
Steps to install and use the plugin:<br>
1- Unzip the file inside of your ~/moodle/enrol/ folder<br>
2- Go to the Enrolment methods from any course and add the method called "INDES Enrollment Request"<br>
3- After that you can create questions to the enrolment created<br>
4- If a user made a request to the course using the enrolment created, is possible to see and change to any of the 10 status available, it can send a notification to the user too in the case your click on the checkbox<br>
5- If you check the status "Selected" or "Selected With Scholarship", the user need to pay to be able be enrolled in the course, those 2 options works only if you are using the plugins "INDES PayPal Request" or the "Stripe Payment", in the case you would like to charge some fee to access to the course, the methods using the other plugins mentioned before need to be created with anticipation.
