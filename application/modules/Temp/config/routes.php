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
   $route['Temp/settings']= 'Config/configureAddUpdate';
  $route['Temp/configuresubmit']= 'Config/configureAddUpdate';
   $route['Temp/configureAutomatedNotificationsubmit']= 'Config/configureAutomatedNotificationsubmit';
  $route['Temp/configureFoodTempsubmit']= 'Config/configureAddUpdateFoodTemp';
   $route['Temp/configureChillingTempsubmit']= 'Config/configureAddUpdateChillingTemp';
  $route['Temp/home/tempHistoryFormUpdate'] = 'Home/tempHistoryUpdate';
  $route['Temp/home/equipTempHistoryUpdate'] = 'Home/tempHistoryUpdate';
  $route['Temp/home/historyData/(:any)/(:any)'] = 'Home/historyData/$1/$2';
  
  
  // Food Temp. routes
  
  $route['Temp/foodTemp/site']= 'FoodTemp/Sitefood';
  $route['Temp/foodTemp/prep']= 'FoodTemp/Prepfood';
  $route['Temp/home/foodTempHistory']= 'FoodTemp/Foodtemphome/tempHistory';
  $route['Temp/home/foodhistoryData'] = 'FoodTemp/Foodtemphome/historyData';
  $route['Temp/home/foodhistoryData/(:any)/(:any)'] = 'FoodTemp/Foodtemphome/historyData/$1/$2';
  $route['Temp/home/tempHistoryUpdate'] = 'FoodTemp/Foodtemphome/tempHistoryUpdate';
  $route['Temp/home/uploadFoodTemperatureAttachment'] = 'FoodTemp/Foodtemphome/uploadTemperatureAttachment';
  $route['Temp/home/fetchFoodAttachmentUploadedToday'] = 'FoodTemp/Foodtemphome/fetchAttachmentUploadedToday';
  
  $route['Temp/Foodtemphome/saveTempDashboardData']= 'FoodTemp/Foodtemphome/saveTempDashboardData';
  $route['Temp/Foodtemphome/updateExceededTemp']= 'FoodTemp/Foodtemphome/updateExceededTemp';
  
    // Chilling Temp. routes
  
  $route['Temp/chillingTemp/site'] = 'ChillingTemp/Sitec';
  $route['Temp/chillingTemp/prep']= 'ChillingTemp/Prepc';
  $route['Temp/home/chillingTempHistory']= 'ChillingTemp/Chillinghome/tempCHistory';
  $route['Temp/home/chillinghistoryData'] = 'ChillingTemp/Chillinghome/historyChillingData';
  $route['Temp/home/chillinghistoryData/(:any)/(:any)'] = 'ChillingTemp/Chillinghome/historyChillingData/$1/$2';
  $route['Temp/home/saveChillingDashboardData']= 'ChillingTemp/Chillinghome/saveTempDashboardData';
  $route['Temp/home/tempHistoryUpdatec'] = 'ChillingTemp/Chillinghome/tempHistoryUpdatec';
  
 

// $route['Temp/prep/index'] = 'Temp/prep/index';

// slicing temp routes

  $route['Temp/sliceTemp/site'] = 'SliceTemp/Sitec';
  $route['Temp/sliceTemp/prep']= 'SliceTemp/Prepc';
  $route['Temp/home/slicinghistoryData'] = 'SliceTemp/Slicinghome/historyChillingData';
  $route['Temp/home/sliceTempHistory'] = 'SliceTemp/Slicinghome/tempCHistory';
  $route['Temp/home/slicinghistoryData/(:any)/(:any)'] = 'SliceTemp/Slicinghome/historyChillingData/$1/$2';
  $route['Temp/home/saveChillingDashboardData']= 'SliceTemp/Slicinghome/saveTempDashboardData';
  $route['Temp/home/tempSliceHistoryUpdatec'] = 'SliceTemp/Slicinghome/tempHistoryUpdatec';
  
  // Fryer Temp 
  
  
 $route['Temp/Fryertemp/site']= 'FryerTemp/Sitefry';
  $route['Temp/Fryertemp/prep']= 'FryerTemp/Prepfry';
  $route['Temp/home/fryerTempHistory']= 'FryerTemp/Fryerhome/tempHistory';
  $route['Temp/home/fryerhistoryData'] = 'FryerTemp/Fryerhome/historyData';
  $route['Temp/home/fryerhistoryData/(:any)/(:any)'] = 'FryerTemp/Fryerhome/historyData/$1/$2';
  $route['Temp/home/tempHistoryUpdate'] = 'FryerTemp/Fryerhome/tempHistoryUpdate';
  $route['Temp/home/uploadFoodTemperatureAttachment'] = 'FryerTemp/Fryerhome/uploadTemperatureAttachment';
  $route['Temp/home/fetchFoodAttachmentUploadedToday'] = 'FryerTemp/Fryerhome/fetchAttachmentUploadedToday';
  
  $route['Temp/Fryerhome/saveTempDashboardData']= 'FryerTemp/Fryerhome/saveTempDashboardData';
  $route['Temp/Fryerhome/updateExceededTemp']= 'FryerTemp/Fryerhome/updateExceededTemp';
  
  // Calibration Temp routes
  
  $route['Temp/calibrationTemp/site'] = 'CalibrationTemp/Sitecalib';
  $route['Temp/calibrationTemp/prep'] = 'CalibrationTemp/Prepcalib';
  $route['Temp/home/calibrationTempHistory'] = 'CalibrationTemp/Calibrationhome/tempCHistory';
  $route['Temp/home/calibrationhistoryData'] = 'CalibrationTemp/Calibrationhome/historyCalibrationData';
  $route['Temp/home/calibrationhistoryData/(:any)/(:any)'] = 'CalibrationTemp/Calibrationhome/historyCalibrationData/$1/$2';

?>