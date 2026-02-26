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
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// application/config/routes.php

// Home controller
$route['logout'] = 'Auth/logout';
$route['default_controller'] = 'Home';
$route['index'] = 'Home/index';

 /// CONFIGURATION 
   $route['Cleaning/settings']= 'Config/configureAddUpdate';
  $route['Cleaning/configuresubmit']= 'Config/configureAddUpdate';
  $route['Cleaning/configureFoodTempsubmit']= 'Config/configureAddUpdateFoodTemp';
  
  // Food Temp. routes
  
  $route['Cleaning/foodTemp/site']= 'FoodTemp/Sitefood';
  $route['Cleaning/foodTemp/prep']= 'FoodTemp/Prepfood';
  $route['Cleaning/home/foodTempHistory']= 'Config/configureAddUpdate';
  
//   Waste management routes

$route['Compliance/Sanitation/history']= 'Waste/Home/history';
$route['Compliance/Waste/historyData']= 'Waste/Home/historyData';

// Sanitation system routes
$route['Compliance/Sanitation/history']= 'Sanitation/Home/history';
$route['Compliance/Sanitation/historyData']= 'Sanitation/Home/historyData';

// Kitchen Production system routes
$route['Compliance/KitchenProduction/history']= 'KitchenProduction/Home/history';
$route['Compliance/KitchenProduction/historyData']= 'KitchenProduction/Home/historyData';
  

// $route['Temp/prep/index'] = 'Temp/prep/index';

?>