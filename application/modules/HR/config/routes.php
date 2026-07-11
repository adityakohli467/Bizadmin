<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
// $route['admin/get_roster_weeks/(:num)'] = 'admin/get_roster_weeks';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Employees

$route['HR/employees'] = 'Employees/employeeList';
$route['HR/Employee/employeeStatusUpdate'] = 'Employees/employeeStatusUpdate';
$route['HR/Employee/employeeDelete'] = 'Employees/employeeDelete';
$route['HR/Employee/edit/(:any)'] = 'Employees/editEmployee/$1';
$route['HR/Employee/employeeprofile'] = 'Employees/editEmployee';
$route['HR/Employee/managePay'] = 'Employees/managePay';
$route['HR/Employee/savePayRates'] = 'Employees/savePayRates';
$route['HR/onboardNewEmployee'] = 'Employees/onboardNewEmployee';
$route['HR/Employee/sendOnboardingEmail/(:any)'] = 'Employees/sendOnboardingEmail/$1';
$route['HR/Employee/submitOnboardingProcessForEmployee'] = 'Employees/submitOnboardingProcessForEmployee';
$route['HR/Employees/add_unavailability'] = 'Employees/add_unavailability';
$route['HR/Employees/del_unavailability'] = 'Employees/del_unavailability';
// import employees
$route['HR/importEmployees'] = 'Employees/importEmployees';
$route['HR/Employee/importEmployeesFromCSV'] = 'Employees/importEmployeesFromCSV';

//  contracters routes

$route['HR/contractors'] = 'Contractors/contractorList';
$route['HR/contractors/addEditContractor'] = 'Contractors/addEditContractor';
$route['HR/editContractor/(:any)'] = 'Contractors/addEditContractor/$1';
$route['HR/contractors/submitContractorForm'] = 'Contractors/submitContractorForm';
$route['HR/contractors/contractorUpdateStatus'] = 'Contractors/contractorUpdateStatus';
$route['HR/contractors/submitContractorDocs'] = 'Contractors/uploadContractorDocs';
// CONFIG RELATED ROUTES

$route['HR/configuresubmit'] = 'Config/allConfig';
$route['HR/uploadConfigFiles'] = 'Config/uploadConfigFiles';
$route['HR/Config/createStressProfile'] = 'Config/createStressProfile';
$route['HR/Config/updateStressProfile'] = 'Config/updateStressProfile';
$route['HR/Config/addleaveType'] = 'Config/addleaveType';
$route['HR/Config/updateleaveType'] = 'Config/updateleaveType';
$route['HR/Config/saveOnboardingTabConfig'] = 'Config/saveOnboardingTabConfig';
$route['HR/Leave/delete'] = 'Config/deleteLeave';
$route['HR/Config/emailSetting'] = 'Config/addEmailsSetting';
$route['HR/Config/positionSetting'] = 'Config/addPositionSetting';
// $route['Supplier/manage_supplier/(:any)/(:any)'] = 'Supplier/manage_supplier/$1/$2';

// Leave request

$route['HR/Leave/requestLeave'] = 'Leaves/requestLeave';
$route['HR/Leave/cancelLeave'] = 'Leaves/cancelLeave';
$route['HR/myLeaves'] = 'Leaves/myLeaves';
$route['HR/Leave/approve'] = 'Leaves/approve';
$route['HR/Leave/reject'] = 'Leaves/reject';
$route['HR/Leave/details/(:num)'] = 'Leaves/details/$1';
$route['HR/Leave/ajax_list'] = 'Leaves/ajax_list';

// Documents
$route['HR/documents'] = 'Document/index';

//Complicance Routes
$route['HR/compliance/incidentR'] = 'Compliance/Incident/incidentList';
$route['HR/Incident/AddIncidentReport'] = 'Compliance/Incident/incidentR';
$route['HR/Incident/AddIncidentReport/(:any)'] = 'Compliance/Incident/incidentR/$1';
$route['HR/Incident/updateIncidentR/(:any)'] = 'Compliance/Incident/updateIncidentR/$1';
$route['HR/Incident/delete'] = 'Compliance/Incident/deleteRecord';
// injury report

$route['HR/compliance/injuryR'] = 'Compliance/Injury/injuryList';
$route['HR/Injury/AddInjuryReport'] = 'Compliance/Injury/InjuryR';
$route['HR/Injury/AddInjuryReport/(:any)'] = 'Compliance/Injury/InjuryR/$1';
$route['HR/Injury/updateInjuryR/(:any)'] = 'Compliance/Injury/updateInjuryR/$1';
$route['HR/Injury/delete'] = 'Compliance/Injury/deleteRecord';

// resignation letter
$route['HR/compliance/resignationL'] = 'Compliance/Resignationletter/resignationList';
$route['HR/resignationL/AddResignationL'] = 'Compliance/Resignationletter/resignationL';
$route['HR/resignationL/AddResignationL/(:any)'] = 'Compliance/Resignationletter/resignationL/$1';
$route['HR/resignationL/updateResignationL/(:any)'] = 'Compliance/Resignationletter/updateResignationL/$1';
$route['HR/resignationL/delete'] = 'Compliance/Resignationletter/deleteRecord';

// // Reimbursement
$route['HR/compliance/reimbursementR'] = 'Compliance/Reimbursement/reimbursementList';
$route['HR/reimbursement/AddReimbursement'] = 'Compliance/Reimbursement/reimbursementForm';
$route['HR/reimbursement/AddReimbursement/(:any)'] = 'Compliance/Reimbursement/reimbursementForm/$1';
$route['HR/reimbursement/updateReimbursement/(:any)'] = 'Compliance/Reimbursement/updateResignationL/$1';
$route['HR/reimbursement/delete'] = 'Compliance/Reimbursement/deleteRecord';

// MEMO routes
$route['HR/memo'] = 'Memo/memoList';
$route['HR/memo/sendMemo'] = 'Memo/sendMemo';
$route['HR/memo/deleteMemo'] = 'Memo/deleteMemo';
$route['HR/memo/fetchMemoList'] = 'Memo/fetchMemoList';
$route['HR/memo/addMemoComments'] = 'Memo/addMemoComments';

// Roster, Site AND Prep Area
$route['HR/roster'] = 'Roster/rosterList';
$route['HR/myRoster'] = 'Roster/myRoster';
$route['HR/rosterForm/(:any)'] = 'Roster/rosterForm/$1';
$route['HR/rosterForm'] = 'Roster/rosterForm';
$route['HR/roster/add'] = 'Roster/addRoster';
$route['HR/roster/fetchRoster'] = 'Roster/fetchRoster';
$route['HR/sites'] = 'Site/index';
$route['HR/Site/delete'] = 'Site/delete';
$route['HR/site/add'] = 'Site/add';
$route['HR/prep'] = 'Prep/index';
$route['HR/prep/add'] = 'Prep/add';
$route['HR/rosterView/(:any)'] = 'Roster/rosterView/$1';
$route['HR/rosterView/(:any)/(:any)'] = 'Roster/rosterView/$1/$2';
$route['HR/recreateRoster'] = 'Roster/recreateRoster';
$route['HR/fetchRosterByWeek'] = 'Roster/fetchRosterByWeek'; 
// view by team member UI
$route['HR/rosterViewByTM/(:any)'] = 'Roster/rosterViewByTM/$1';
$route['HR/rosterViewWTM/(:any)'] = 'Roster/rosterViewWTM/$1';
$route['HR/fetchRosterOnArrowClick/(:any)/(:any)'] = 'Roster/fetchRosterOnArrowClick/$1/$2';

//timesheet
$route['HR/timesheetWithoutRoster'] = 'Timesheet/timesheetList';
$route['HR/verifyFaceForClockIn'] = 'Timesheet/verifyFaceForClockIn';
$route['HR/timesheetView/(:any)'] = 'Timesheet/timesheetView/$1';
$route['HR/timesheet'] = 'Timesheet/timesheet';
$route['HR/timesheet/verifyFace'] = 'Timesheet/verifyFace';
$route['HR/viewWeeklyTimesheet/(:any)/(:any)'] = 'Timesheet/viewWeeklyTimesheet/$1/$2';
$route['HR/viewTimesheetWithoutRoster/(:any)'] = 'Timesheet/viewTimesheetWithoutRoster/$1';
$route['HR/timesheet/searchEmployees'] = 'Timesheet/searchEmployees';

$route['HR/addTimesheetWithoutRoster/(:num)'] = 'Timesheet/timesheetWithoutRoster/0/0/$1'; // edit timesheet without roster
$route['HR/addTimesheetWithoutRoster/([0-9]{4}-[0-9]{2}-[0-9]{2})/([0-9]{4}-[0-9]{2}-[0-9]{2})'] = 'Timesheet/timesheetWithoutRoster/$1/$2'; // view timesheet from weekly calender chnage from inner page

// MOST GENERAL last
$route['HR/addTimesheetWithoutRoster'] = 'Timesheet/timesheetWithoutRoster'; // add timesheet withhout roster


//leaves

$route['HR/leaveDashbaord'] = 'Leaves/leaveDashbaord';

//timesheet details
$route['HR/Home/getTimesheetDetails'] = 'Home/getTimesheetDetails';

//HR assistant chatbot
$route['HR/chatbot/ask'] = 'Chatbot/ask';

