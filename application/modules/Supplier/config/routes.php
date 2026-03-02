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

// Suppliers controller
$route['Supplier/configurationSupplierController'] = 'Supplier/configurationSupplier';
$route['Supplier/configuration'] = 'Supplier/configurationSupplier';
$route['Supplier/list'] = 'Supplier/listSupplier';
$route['Supplier/manage_supplier/(:any)/(:any)'] = 'Supplier/manage_supplier/$1/$2';
$route['Supplier/save_supplier/(:any)'] = 'Supplier/manage_supplier/$1';
$route['Supplier/mandatory_record'] = 'Supplier/mandatory_record';
$route['Supplier/SupplierDelete'] = 'Supplier/supplierStatus';

$route['Supplier/manage_categories'] = 'Supplier/listSupplierCategory';
$route['Supplier/manage_categories/(:any)/(:any)'] = 'Supplier/manageSupplierCategory/$1/$2';
$route['Supplier/save_category/(:any)'] = 'Supplier/manageSupplierCategory/$1';
$route['Supplier/supplierCategoryDelete'] = 'Supplier/supplierCategoryStatus';

// Sub Categories
$route['Supplier/manage_subcategories'] = 'Supplier/listSupplierSubCategory';
$route['Supplier/manage_subcategories/(:any)/(:any)'] = 'Supplier/manageSupplierSubCategory/$1/$2';
$route['Supplier/supplierSubCategoryDelete'] = 'Supplier/supplierSubCategoryStatus';
$route['Supplier/save_Subcategory/(:any)'] = 'Supplier/manageSupplierSubCategory/$1';

$route['Supplier/budgetUpdate'] = 'Supplier/budgetUpdate';
// Product controller
$route['Supplier/supplier_item/(:any)'] = 'Supplier/listSupplierProducts/$1';
// $route['Supplier/manage_products'] = 'Supplier/listProducts';
$route['Supplier/manage_products/(:any)/(:any)'] = 'Supplier/manageProducts/$1/$2';
// $route['Supplier/manage_products/(:any)/(:any)/(:any)'] = 'Supplier/manageProducts/$1/$2/$3';
$route['Supplier/save_product/(:any)'] = 'Supplier/manageProducts/$1';
$route['Supplier/productDelete'] = 'Supplier/productStatus';
$route['Supplier/productUnapprove'] = 'Supplier/productUnapprove';

// Product category controller
$route['Supplier/manage_product_categories'] = 'Supplier/listProductCategory';

$route['Supplier/save_product_category/(:any)'] = 'Supplier/manageProductCategory/$1';
$route['Supplier/productCategoryDelete'] = 'Supplier/productCategoryStatus';

// Product UOM controller
$route['Supplier/manage_product_UOM'] = 'Supplier/listUOM';
$route['Supplier/manage_product_UOM/(:any)/(:any)'] = 'Supplier/manageUOM/$1/$2';
$route['Supplier/save_product_UOM/(:any)'] = 'Supplier/manageUOM/$1';
$route['Supplier/product_UOM_delete'] = 'Supplier/UOMStatus';
$route['Supplier/Product/bulkUpdate/(:any)'] = 'Product/bulkUpdate/$1';
$route['Supplier/Product/downloadSampleProduct/(:any)'] = 'Product/download_sample/$1';
$route['Supplier/Product/importProduct'] = 'Product/importProduct';
// Prep Area Add, Edit
$route['Supplier/save_area/(:any)'] = 'Admin/manageArea/$1';
$route['Supplier/area_delete'] = 'Admin/AreaStatus';

// Other routes
$route['Supplier/branchesDashboard'] = 'Auth/branchesDashboard';
$route['Supplier/dashboard/(:any)'] = 'Auth/dashboard/$1';

$route['Supplier/admin/area'] = 'admin/index';

// STOCK COUNT RELATED ROUTES  

$route['Supplier/stock/index'] = 'Stock/index';
$route['Supplier/stockupdate/(:any)'] = 'Stock/index/$1';
$route['Supplier/monthlystock/(:any)'] = 'Stock/monthlystockCount/$1';
$route['Supplier/monthlystockUpdate'] = 'Stock/monthlystockUpdate';
$route['Supplier/updateStock'] = 'Stock/updateStock';
 // to access view/place order page
 // for actually placing/sending order to supplier

// Order Related Routes
$route['Supplier/placeOrder/(:any)'] = 'Orders/index/$1';
$route['Supplier/sendOrder/(:any)'] = 'Orders/sendOrder/$1';
$route['Supplier/updateOrder'] = 'Orders/updateOrder';
$route['Supplier/notifyManagerAboutBudgetExceededOrder'] = 'Orders/notifyManagerAboutBudgetExceededOrder';
$route['Supplier/Orders/completed'] = 'Orders/completedOrder';
$route['Supplier/Orders/deleteInvoice'] = 'Orders/deleteInvoice';
$route['Supplier/Orders/receiveOrderDetails/(:any)'] = 'Orders/receiveOrderDetails/$1';
$route['Supplier/Orders/receiveOrder'] = 'Orders/receiveOrder';
$route['Supplier/Orders/approveBudgetExceedOrder/(:any)/(:any)'] = 'Orders/approveBudgetExceedOrder/$1/$2';
$route['Supplier/Orders/receiveOrder/(:any)/(:any)'] = 'Orders/viewOrder/$1/$2';
$route['(:any)/Supplier/outer-url/(:any)'] = 'Orders/viewOrder/$1/$2';
$route['Supplier/Orders/orderDelete'] = 'Orders/orderDelete';

// Internal Order related routes
$route['Supplier/internalorder/location'] = 'Internalorder/internalorder/locationList';
$route['Supplier/internalorder/save_sublocation/(:any)'] = 'Internalorder/internalorder/manageSubLocation/$1';
$route['Supplier/internalorder/location_delete'] = 'Internalorder/internalorder/sublocationStatus';

$route['Supplier/internalorder/products'] = 'Internalorder/product/productList';
$route['Supplier/internalorder/productsSample'] = 'Internalorder/product/download_sample';
$route['Supplier/internalorder/save_product/(:any)'] = 'Internalorder/product/manageProducts/$1';
$route['Supplier/internalorder/product_delete'] = 'Internalorder/product/productStatus';
$route['Supplier/internalorder/productUpdateSortOrder'] = 'Internalorder/product/productUpdateSortOrder';
$route['Supplier/internalorder/fetchProductData'] = 'Internalorder/product/fetchProductData';

$route['Supplier/internalorder/importProduct'] =       'Internalorder/product/importProduct';
$route['Supplier/internalorder/exportProducts'] =      'Internalorder/product/exportProducts';
$route['Supplier/internalorder/productCount'] =        'Internalorder/product/productCount';
$route['Supplier/internalorder/productCount/(:any)'] = 'Internalorder/product/productCount/$1';
$route['Supplier/internalorder/addProductCount'] =      'Internalorder/product/addProductCount';
$route['Supplier/internalorder/categoryList'] =      'Internalorder/product/categoryList';
$route['Supplier/internalorder/addCategory'] =      'Internalorder/product/addCategory';
$route['Supplier/internalorder/updateCategory'] =      'Internalorder/product/updateCategory';
$route['Supplier/internalorder/placeOrder'] =      'Internalorder/placeorder/placeOrderInternal';
$route['Supplier/internalorder/placeOrder/(:any)'] = 'Internalorder/placeorder/placeOrderInternal/$1';
$route['Supplier/internalorder/saveInternalOrder']  = 'Internalorder/placeorder/saveInternalOrder/';
$route['Supplier/internalorder/markCompleted'] =      'Internalorder/placeorder/markCompleted';
$route['Supplier/internalorder/markdelivered'] =      'Internalorder/placeorder/markdelivered';

$route['Supplier/internalorder/makeOrder'] =      'Internalorder/placeorder/makeOrderInternal';
$route['Supplier/internalorder/uploadOrderAttachment'] =      'Internalorder/placeorder/uploadOrderAttachment';
$route['Supplier/internalorder/history'] =      'Internalorder/internalorder/orderHistory';

$route['Supplier/internalorder/editInternalOrder/(:any)/(:any)'] = 'Internalorder/internalorder/editInternalOrder/$1/$2';
$route['Supplier/internalorder/updateInternalOrder'] = 'Internalorder/internalorder/updateInternalOrder';
$route['Supplier/internalorder/deleteOrder'] = 'Internalorder/internalorder/deleteOrder';
$route['Supplier/internalorder/validateOrderIfAlreadyPlacedToday'] = 'Internalorder/placeorder/validateOrderIfAlreadyPlacedToday';
$route['Supplier/internalorder/reports'] = 'Internalorder/reports';
$route['Supplier/filterInternalOrderReport'] = 'Internalorder/reports/filter';
$route['Supplier/configuresubmit']= 'Config/configureSave';

// Budget related path
$route['Supplier/Budget/index'] = 'Budget/index';
$route['Supplier/budgetrecordsave'] = 'Budget/saveBudget';
$route['Supplier/Budget/saveSupplierBudget'] = 'Budget/saveSupplierBudget';

// Records related Path

$route['Supplier/filterReportWeekly'] = 'Report/filterReportWeekly';
$route['Supplier/filterReportMonthly'] = 'Report/filterReportMonthly';
$route['Supplier/Report/weeklyReport'] = 'Report/weeklyReport';
$route['Supplier/Report/monthlyReport'] = 'Report/monthlyReport';

