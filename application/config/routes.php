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
|	https://codeigniter.com/userguide3/general/routing.html
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

/////////    IMPORTANT /////////////////////////////////////////

// path for landing page main website bizadmin
// /home/bizadmincom/public_html/application/views/general/homepage.php



$route['default_controller'] = 'home';
$route['Common/fetchRecordsDynamicallyAjax']  = 'Common/fetchRecordsDynamicallyAjax';

// SYSTEM ROUTES

$route['Supplier/(\d+)']  = 'Supplier/Home/index/$1';
$route['Cash/(\d+)']  = 'Cash/Home/index/$1';
$route['HR/(\d+)']  = 'HR/Home/index/$1';
$route['HR/(\d+)/(:any)']  = 'HR/Home/clockInClockOut/$1/$2'; // for timesheet
$route['Temp/(\d+)']  = 'Temp/Home/index/$1';
$route['Clean/(\d+)']  = 'Clean/Home/index/$1';
$route['Dms/(\d+)']  = 'Dms/Home/index/$1';
$route['Compliance/(\d+)']  = 'Compliance/Cake/Cakehome/index/$1';
$route['Shifts/(\d+)']  = 'Shifts/Home/index/$1';


$route['Catering/(\d+)']  = 'Catering/General/dashboard/$1';
$route['Recipe/(\d+)']  = 'Recipe/Home/index/$1';

// Database migration runner (must be before catch-all route)
$route['migration/(:any)'] = 'migration/$1';
$route['migration'] = 'migration/index';

// for loading login page where we pass tenant identifier in URL dont remove it
$route['(:any)'] = 'auth/index/$1';



