<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Product extends MY_Controller {
    function __construct() {
		parent::__construct();
	   $this->load->model('internalorder_model');
	   $this->load->model('supplier_model');
	   $this->load->model('common_model');
	   $this->location_id = $this->session->userdata('location_id');
	   $this->system_id = $this->session->userdata('system_id');
	  !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
	  $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
// 	  ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
	}
	




	public function productList(){
	    $data['productList'] = $this->internalorder_model->fetchProducts();
	    $data['locationList'] = $this->internalorder_model->fetchLocations($this->location_id,'id,name','notIsKitchen');
	    $conditions = array('is_deleted'=>'0');
        $data['categoryLists'] = $this->common_model->fetchRecordsDynamically('SUPPLIERS_internalOrderCategory', '', $conditions);
        $data['uomLists'] = $this->common_model->fetchRecordsDynamically('SUPPLIERS_product_UOM', array('product_UOM_id','product_UOM_name'), $conditions);
     
	    $this->load->view('general/header');
      	$this->load->view('Internalorder/productList',$data);
      	$this->load->view('general/footer');
	}
	function fetchProductData(){
	$productData = $this->internalorder_model->fetchProducts($_POST['id']); 
	
	echo json_encode($productData);
	}
	
	public function manageProducts($type=""){
        
         if($type == 'edit'){
             $id = $_POST['productIdToUpdate'];
           $result = $this->internalorder_model->updateProduct($id,$_POST);
             return redirect(base_url('/Supplier/internalorder/products'));
        }else if($type == 'add'){
            
          $result = $this->internalorder_model->addProduct($_POST);
           return redirect(base_url('/Supplier/internalorder/products'));
        }
    }
    public function productStatus(){
        $id = $_POST['id'];
        $status = $_POST['status'];
        $table = $_POST['table'];
        if($status=='delete'){
         $data = array( 'is_deleted' => 1  );   
        }else{
         $data = array( 'status' => $status  );   
        }
        
        $result = $this->internalorder_model->productStatus($id,$data,$table);
        echo "success";
    }
     public function productUpdateSortOrder(){
       $newOrder = $this->input->post('order');
   
    // Update the database with the new sort order

       foreach ($newOrder as $index => $itemId) {
        $prdctID = substr($itemId, 4);
        $this->tenantDb->set('sort_order', $index + 1);
        $this->tenantDb->where('id', $prdctID);
        $this->tenantDb->update('SUPPLIERS_internalOrderProducts');
      }
    echo "success";
    }
    
    function productCount($id=''){
         $conditionsSub = array('is_kitchen'=> 0,'location_id' => $this->location_id); $colsToFetchSub = array('id,name');
         $data['locationList'] = $this->common_model->fetchRecordsDynamically('SUPPLIERS_internalOrderLocations', $colsToFetchSub, $conditionsSub);
         $locationWithkictchen = $this->internalorder_model->fetchLocations($this->location_id,'is_kitchen,email,ccemail');
         $conditions = array('is_deleted'=>'0');
         $data['categoryLists'] = $this->common_model->fetchRecordsDynamically('SUPPLIERS_internalOrderCategory', '', $conditions);
         $data['productList'] = $this->internalorder_model->fetchProducts();
         $data['productCountData'] = $this->internalorder_model->fetchProductCountData(); 
        // echo  $this->location_id; exit;
         $data['selectedSubLoc'] = $id;
         $kithendetails = array_filter($locationWithkictchen, function ($value) {
            return $value['is_kitchen'] == 1;
        });
      
         $data['form_type'] = 'add';
         $data['kithendetails'] = reset($kithendetails);
         $data['uomLists'] = $this->common_model->fetchRecordsDynamically('SUPPLIERS_product_UOM', array('product_UOM_id','product_UOM_name'), $conditions);
        $this->load->view('general/header');
      	$this->load->view('Internalorder/productCount',$data);
      	$this->load->view('general/footer');  
        
    }
    
    function addProductCount(){
      $dataToInsert = array();
      $dataToUpdate = array();
      // at once we can submit only one sub location data 
      $selectedSubLocationId  = $_POST['selectedSubLocationId'];
      
       foreach ($_POST['productID'] as $key => $productCount) {
        $locationAndProductID = explode('_', $productCount);
      
        if(isset($locationAndProductID[1]) &&  $locationAndProductID[1] == $selectedSubLocationId){
          
        if(isset($locationAndProductID[0]) && isset($locationAndProductID[1])){
          
        $existingProductCountData =   $this->internalorder_model->fetchProductCountData($locationAndProductID[0],$locationAndProductID[1]); 
        
        }
        if(isset($existingProductCountData) && !empty($existingProductCountData)){
          $rowUpdateData = array(
          'id' => $existingProductCountData[0]['id'],
          'dailtQtyNeed' => (isset($_POST['dailtQtyNeed'][$key]) && $_POST['dailtQtyNeed'][$key] !='' ? $_POST['dailtQtyNeed'][$key] : NULL),
          'qtyToMake' => (isset($_POST['qtyToMake'][$key]) && $_POST['qtyToMake'][$key] !='' ? $_POST['qtyToMake'][$key] : NULL),
        );  
          $dataToUpdate[] = $rowUpdateData;    
        }else{
          $rowData = array(
          'product_id' => isset($locationAndProductID[0]) ? $locationAndProductID[0] : null,
          'sublocation_id' => isset($locationAndProductID[1]) ? $locationAndProductID[1] : null,
          'dailtQtyNeed' => (isset($_POST['dailtQtyNeed'][$key]) && $_POST['dailtQtyNeed'][$key] !='' ? $_POST['dailtQtyNeed'][$key] : NULL),
          'qtyToMake' => (isset($_POST['qtyToMake'][$key]) && $_POST['qtyToMake'][$key] !='' ? $_POST['qtyToMake'][$key] : NULL),
          'location_id' => $this->location_id,
          'date_completed' => date('y-m-d')
        );
        $dataToInsert[] = $rowData;  
        // $dataLocation['last_countedAt'] = date('Y-m-d');
        // //  echo "<pre>"; print_r($rowData); exit;
        //  $this->internalorder_model->updateLocation($locationAndProductID[1],$dataLocation);
        }
       
        }
       
     }
    
    //  echo "<pre>"; print_r($dataToInsert); exit;
    if(!empty($dataToInsert)){
     $this->internalorder_model->insertProductCountBatch($dataToInsert);
    }
     if(!empty($dataToUpdate)){
         $this->internalorder_model->updateProductCountBatch($dataToUpdate); 
     }
    return redirect(base_url('/Supplier/internalorder/productCount/'.$selectedSubLocationId));  
     

    }
    
    function categoryList(){
        $conditions = array('is_deleted'=>'0');
        $data['categoryLists'] = $this->common_model->fetchRecordsDynamically('SUPPLIERS_internalOrderCategory', '', $conditions);
        $this->load->view('general/header');
      	$this->load->view('Internalorder/categoryList',$data);
      	$this->load->view('general/footer'); 
    }
    
    function addCategory(){
        $data['category_name'] = $this->input->post('category_name');
        $data['location_id'] = $this->location_id;
        $this->common_model->commonRecordCreate('SUPPLIERS_internalOrderCategory',$data); 
        echo "success";
    }
    function updateCategory(){
        $data['category_name'] = $this->input->post('category_name');
        $idCat = $this->input->post('id');
        $this->common_model->commonRecordUpdate('SUPPLIERS_internalOrderCategory','id',$idCat,$data);   
        echo "success";
    }
    
    public function download_sample($supplierId='') {
    // Load required libraries and models

    // $conditions = array('status'=>'1','is_deleted'=>'0');
    // $categoryList = $this->common_model->fetchRecordsDynamically('SUPPLIERS_internalOrderCategory', '', $conditions);
    
  
    $uomLists = $this->common_model->fetchRecordsDynamically('SUPPLIERS_product_UOM', array('product_UOM_id','product_UOM_name'), $conditions);
    $uomListFormatted = array();
foreach ($uomLists as $uomList) {
    $uomListFormatted[] = '['.$uomList['product_UOM_id'] . ']' . $uomList['product_UOM_name'];
}

// $categoryListFormatted = array();
// foreach ($categoryList as $category) {
//     $categoryListFormatted[] = '['.$category['id'] . ']' . $category['category_name'];
// }

$locationList = $this->internalorder_model->fetchLocations($this->location_id, 'id,name', 'notIsKitchen');
$subLocationList = array();
foreach ($locationList as $subLocation) {
    $subLocationList[] = '['.$subLocation['id'] . ']' . $subLocation['name'];
}

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headings
$sheet->setCellValue('A1', 'name');
$sheet->setCellValue('B1', 'category_id');
$sheet->setCellValue('C1', 'price');
$sheet->setCellValue('D1', 'sublocation_id');
$sheet->setCellValue('E1', 'requireAttach');
$sheet->setCellValue('F1', 'requireTemp');
$sheet->setCellValue('G1', 'UOM');

// Populate default values
$startingRow = 2;
$numRows = 800;
for ($row = $startingRow; $row <= $startingRow + $numRows - 1; $row++) {
    $sheet->setCellValue('A' . $row, '');  // Input field, leave blank
    $sheet->setCellValue('B' . $row, '');
    $sheet->setCellValue('E' . $row, '0'); // Input field
    $sheet->setCellValue('F' . $row, '0');
}

// Populate dropdowns
for ($row = $startingRow; $row <= $startingRow + $numRows - 1; $row++) {
    // // Add dropdown for category_id
    // $validation = $sheet->getCell('B' . $row)->getDataValidation();
    // $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    // $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    // $validation->setShowInputMessage(true);
    // $validation->setShowErrorMessage(true);
    // $validation->setShowDropDown(true);
    // $validation->setErrorTitle('Input error');
    // $validation->setError('Please pick correct value from the drop-down list only, manual entry is not allowed');
    // $validation->setPromptTitle('Pick from list');
    // $validation->setPrompt('Please pick a value from the drop-down list');
    // $validation->setFormula1('"'.implode(',', $categoryListFormatted).'"');
     
     $validation = $sheet->getCell('G' . $row)->getDataValidation();
    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $validation->setShowInputMessage(true);
    $validation->setShowErrorMessage(true);
    $validation->setShowDropDown(true);
    $validation->setErrorTitle('Input error');
    $validation->setError('Please pick correct value from the drop-down list only, manual entry is not allowed');
    $validation->setPromptTitle('Pick from list');
    $validation->setPrompt('Please pick a value from the drop-down list');
    $validation->setFormula1('"'.implode(',', $uomListFormatted).'"');
    
    // Add dropdown for sublocation_id
    $validation = $sheet->getCell('D' . $row)->getDataValidation();
    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $validation->setShowInputMessage(true);
    $validation->setShowErrorMessage(true);
    $validation->setShowDropDown(true);
    $validation->setErrorTitle('Input error');
    $validation->setError('Please pick correct value from the drop-down list only, manual entry is not allowed');
    $validation->setPromptTitle('Pick from list');
    $validation->setPrompt('Please pick a value from the drop-down list');
    $validation->setFormula1('"'.implode(',', $subLocationList).'"');
}

// Set column widths for readability
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(40);
$sheet->getColumnDimension('F')->setWidth(40);

// Create Xlsx writer object
$writer = new Xlsx($spreadsheet);

// Set headers to force download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="InternalProductSample.xlsx"');
header('Cache-Control: max-age=0');

// Save the spreadsheet to php://output (download it)
$writer->save('php://output');
}

    public function exportProducts() {
        $productIds = $this->input->post('product_ids');
        
        if (empty($productIds) || !is_array($productIds)) {
            redirect(base_url('/Supplier/internalorder/products'));
            return;
        }
        
        // Fetch UOM list for mapping
        $conditions = array('is_deleted' => '0');
        $uomLists = $this->common_model->fetchRecordsDynamically('SUPPLIERS_product_UOM', array('product_UOM_id', 'product_UOM_name'), $conditions);
        $uomMapping = array();
        $uomListFormatted = array();
        foreach ($uomLists as $uomList) {
            $uomMapping[$uomList['product_UOM_id']] = $uomList['product_UOM_name'];
            $uomListFormatted[] = '[' . $uomList['product_UOM_id'] . ']' . $uomList['product_UOM_name'];
        }
        
        // Fetch sub-location list for mapping
        $locationList = $this->internalorder_model->fetchLocations($this->location_id, 'id,name', 'notIsKitchen');
        $locationMapping = array();
        $subLocationList = array();
        foreach ($locationList as $subLocation) {
            $locationMapping[$subLocation['id']] = $subLocation['name'];
            $subLocationList[] = '[' . $subLocation['id'] . ']' . $subLocation['name'];
        }
        
        // Fetch selected products
        $products = array();
        foreach ($productIds as $pid) {
            $productData = $this->internalorder_model->fetchProducts($pid);
            if (!empty($productData)) {
                $products[] = $productData[0];
            }
        }
        
        if (empty($products)) {
            redirect(base_url('/Supplier/internalorder/products'));
            return;
        }
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column headings (same as download_sample)
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'category_id');
        $sheet->setCellValue('C1', 'price');
        $sheet->setCellValue('D1', 'sublocation_id');
        $sheet->setCellValue('E1', 'requireAttach');
        $sheet->setCellValue('F1', 'requireTemp');
        $sheet->setCellValue('G1', 'UOM');
        
        // Populate product data
        $currentRow = 2;
        foreach ($products as $product) {
            $productName = isset($product['name']) ? $product['name'] : '';
            $categoryId = isset($product['category_id']) ? $product['category_id'] : '';
            $price = isset($product['price']) ? $product['price'] : '';
            $requireAttach = isset($product['requireAttach']) ? $product['requireAttach'] : '0';
            $requireTemp = isset($product['requireTemp']) ? $product['requireTemp'] : '0';
            $uomId = isset($product['uom']) ? $product['uom'] : '';
            
            // Format UOM as [id]name
            $uomFormatted = '';
            if (!empty($uomId) && isset($uomMapping[$uomId])) {
                $uomFormatted = '[' . $uomId . ']' . $uomMapping[$uomId];
            }
            
            // Get sublocation_id data - parse from sublocation_id field (JSON: {subLocId: parLevel})
            $subLocData = null;
            if (!empty($product['sublocation_id'])) {
                if (is_array($product['sublocation_id'])) {
                    $subLocData = $product['sublocation_id'];
                } else {
                    $subLocData = json_decode($product['sublocation_id'], true);
                }
            }
            // Fallback to par_level array from model
            if (empty($subLocData) && !empty($product['par_level']) && is_array($product['par_level'])) {
                $subLocData = $product['par_level'];
            }
            
            // If product has multiple sub-locations, create one row per sub-location
            if (!empty($subLocData) && is_array($subLocData)) {
                foreach ($subLocData as $subLocId => $parLevel) {
                    $subLocFormatted = '';
                    if (!empty($subLocId) && isset($locationMapping[$subLocId])) {
                        $subLocFormatted = '[' . $subLocId . ']' . $locationMapping[$subLocId];
                    }
                    
                    $sheet->setCellValue('A' . $currentRow, $productName);
                    $sheet->setCellValue('B' . $currentRow, $categoryId);
                    $sheet->setCellValue('C' . $currentRow, $price);
                    $sheet->setCellValue('D' . $currentRow, $subLocFormatted);
                    $sheet->setCellValue('E' . $currentRow, $requireAttach);
                    $sheet->setCellValue('F' . $currentRow, $requireTemp);
                    $sheet->setCellValue('G' . $currentRow, $uomFormatted);
                    
                    // Add dropdowns for this row (same as download_sample)
                    $this->setExportRowDropdowns($sheet, $currentRow, $uomListFormatted, $subLocationList);
                    
                    $currentRow++;
                }
            } else {
                // No sub-location data, export single row
                $sheet->setCellValue('A' . $currentRow, $productName);
                $sheet->setCellValue('B' . $currentRow, $categoryId);
                $sheet->setCellValue('C' . $currentRow, $price);
                $sheet->setCellValue('D' . $currentRow, '');
                $sheet->setCellValue('E' . $currentRow, $requireAttach);
                $sheet->setCellValue('F' . $currentRow, $requireTemp);
                $sheet->setCellValue('G' . $currentRow, $uomFormatted);
                
                $this->setExportRowDropdowns($sheet, $currentRow, $uomListFormatted, $subLocationList);
                
                $currentRow++;
            }
        }
        
        // Add extra empty rows with dropdowns for additional entries
        $extraRows = 50;
        for ($row = $currentRow; $row < $currentRow + $extraRows; $row++) {
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('E' . $row, '0');
            $sheet->setCellValue('F' . $row, '0');
            $this->setExportRowDropdowns($sheet, $row, $uomListFormatted, $subLocationList);
        }
        
        // Set column widths for readability
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(40);
        
        // Create Xlsx writer and download
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="InternalProductExport_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
    }
    
    /**
     * Helper to set dropdowns on a row for UOM and sublocation (same format as download_sample)
     */
    private function setExportRowDropdowns($sheet, $row, $uomListFormatted, $subLocationList) {
        // UOM dropdown
        $validation = $sheet->getCell('G' . $row)->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Please pick correct value from the drop-down list only, manual entry is not allowed');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list');
        $validation->setFormula1('"' . implode(',', $uomListFormatted) . '"');
        
        // Sublocation dropdown
        $validation = $sheet->getCell('D' . $row)->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Please pick correct value from the drop-down list only, manual entry is not allowed');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list');
        $validation->setFormula1('"' . implode(',', $subLocationList) . '"');
    }

    public function importProduct() {
        
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    
    $orzName = $this->tenantIdentifier;    
    $uploadPath = './uploaded_files/'.$orzName.'/Supplier/ProductImport/';
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }
    
    $config['upload_path'] = $uploadPath;
    $config['allowed_types'] = 'xlsx|xls';
    $config['encrypt_name'] = TRUE;
    $config['max_size']      = 90240;

    $this->load->library('upload', $config);
    $this->upload->initialize($config);

        if ($this->upload->do_upload('file')) {
            $fileData = $this->upload->data();
            $filePath = $fileData['full_path'];

            $spreadsheet = IOFactory::load($filePath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            
            // Step 1: Group rows by product name to avoid duplicates
            // When exported, a product with multiple sub-locations creates multiple rows
            // We need to merge them back into a single product with multiple sub-locations
            $groupedProducts = array();
            $count = 1;
            foreach ($sheetData as $row) {
                if ($count > 1) {
                    $productName = trim($row['A']);
                    if ($productName == '') {
                        break; // Stop at first empty name
                    }
                    
                    $sublocation_id = (preg_match('/\[(.*?)\]/', $row['D'], $matches) ? $matches[1] : '');
                    $uom_id = (preg_match('/\[(.*?)\]/', $row['G'], $matches) ? $matches[1] : '');
                    $price = str_replace(['$', ' '], '', $row['C']);
                    
                    $key = strtolower($productName); // Group by lowercase name to avoid case-sensitive duplicates
                    
                    if (!isset($groupedProducts[$key])) {
                        $groupedProducts[$key] = array(
                            'productName' => $productName,
                            'category_id' => $row['B'],
                            'price' => $price,
                            'uom' => $uom_id,
                            'requireAttach' => $row['E'],
                            'requireTemp' => $row['F'],
                            'subLocId' => array(),
                            'par_level' => array(),
                        );
                    }
                    
                    // Only add non-empty sublocation IDs (avoid empty sublocation records)
                    if (!empty($sublocation_id)) {
                        // Avoid duplicate sublocation for the same product
                        if (!in_array($sublocation_id, $groupedProducts[$key]['subLocId'])) {
                            $groupedProducts[$key]['subLocId'][] = $sublocation_id;
                            $groupedProducts[$key]['par_level'][] = '';
                        }
                    }
                }
                $count++;
            }
            
            // Step 2: Process each unique product — check for existing before inserting
            $imported = 0;
            $skipped = 0;
            foreach ($groupedProducts as $data) {
                // Check if a product with the same name already exists (not deleted)
                $this->tenantDb->select('id');
                $this->tenantDb->from('SUPPLIERS_internalOrderProducts');
                $this->tenantDb->where('name', $data['productName']);
                $this->tenantDb->where('location_id', $this->location_id);
                $this->tenantDb->where('is_deleted', 0);
                $existingProduct = $this->tenantDb->get()->row_array();
                
                if (!empty($existingProduct)) {
                    // Product already exists — skip to avoid duplicates
                    $skipped++;
                    continue;
                }
                
                // Ensure subLocId and par_level arrays have at least one entry for addProduct
                if (empty($data['subLocId'])) {
                    $data['subLocId'] = array('');
                    $data['par_level'] = array('');
                }
                
                $this->internalorder_model->addProduct($data);
                $imported++;
            }
            
            // Clean up uploaded file after processing
            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            if ($isAjax) {
                echo json_encode(array(
                    'status' => 'success', 
                    'imported' => $imported, 
                    'skipped' => $skipped,
                    'message' => $imported . ' products imported' . ($skipped > 0 ? ', ' . $skipped . ' duplicates skipped' : '')
                ));
                return;
            }
            return redirect(base_url('/Supplier/internalorder/products'));
        } else {
            if ($isAjax) {
                echo json_encode(array('status' => 'error', 'message' => strip_tags($this->upload->display_errors())));
                return;
            }
            echo $this->upload->display_errors();
        }
    }
    
    
    
	
}

?>