<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    function __construct() {
		parent::__construct();
		 !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
		  $this->load->model('generalcomp_model');
		  $this->load->model('task_model');
		  $this->load->model('common_model');
		  $this->load->model('config_model');
		   $this->load->model('general_model');
		$this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
	   $this->selected_location_id = $this->session->userdata('location_id');
	}
	
	 public function index($system_id='')
    {   
        (isset($system_id) && $system_id !='' ? $this->session->set_userdata('system_id',$system_id) : '');
        // $this->load->model('equip_model');
        
        $data['todaysEnteredData'] = $this->generalcomp_model->fetchTodaysEnteredData();
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementsites',array(),['status=1','location_id' => $this->selected_location_id]); 
        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementPrepArea',array(),['status=1','location_id' => $this->selected_location_id]); 
    //   echo "<pre>"; print_r($data['taskListForDash']); exit;
      
       
        // $phpArray = json_decode($data['site_detail'][0]['prep_areas'], true);
        // echo "<pre>"; print_r($data['exceededTempData']); exit;
        
          // 9999 -> is global smtp wch can be used as a backup to send email id orifinal smtp of orz. is not working
          $emailSettings = $this->general_model->fetchSmtpSettings($this->selected_location_id,$system_id);
          if(empty($emailSettings)){
           $emailSettings = $this->general_model->fetchSmtpSettings('9999','9999');
           $this->configureSMTP($emailSettings);
          }else{
           if ($emailSettings->mail_protocol === 'smtp') {
          $this->configureSMTP($emailSettings);
          }   
          }
          if(isset($emailSettings->mail_from)){
           $this->session->set_userdata('mail_from',$emailSettings->mail_from);
          }
          $condition = array('status' => 1,'is_deleted' => 0); 
          $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagementproducts','',$condition);
          
          // todays data
        $condition = array('date_entered' => date('Y-m-d')); // 2025-10-09
        $history_data = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagement_history', '', $condition);

        // Process history data into $todaysEnteredData format
        $todaysEnteredData = array();
        foreach ($history_data as $record) {
            $todaysEnteredData[$record['product_id']] = array(
                'wasteM_value' => $record['wasteM_value'],
                'items_wasted' => isset($record['items_wasted']) ? $record['items_wasted'] : '',
                'entered_by' => $record['entered_by']
            );
        }
  $data['todaysEnteredData'] = $todaysEnteredData;
        //   echo "<pre>"; print_r($data['history']); exit;
      	$this->load->view('general/header');
      	$this->load->view('WasteManagement/dashboard',$data);
      	$this->load->view('general/footer');
  
    	
        
    }
    
    public function saveDashboardData()
    {
       
 
     $product_id = $this->input->post('product_id');
     $field = $this->input->post('field');
     $value = $this->input->post('value');
 
     if (!$product_id || !$field) {
         echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
         return;
     }
 
     // Look up the product's actual prep_id (JS sends site_id, not prep_id)
     $prepId = 0;
     $productRecord = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagementproducts', '', array('id' => $product_id));
     if (!empty($productRecord)) {
         $prepId = $productRecord[0]['prep_id'];
     }
 
     $data = [
         'product_id' => $product_id,
         'prep_id' => $prepId,
          $field => $value,
         'date_entered' => date('Y-m-d'),
         'location_id' => $this->selected_location_id
         
     ];
 
     // Check if a record already exists for this product
     $conditionPr = array('product_id'=>$product_id,'date_entered' => date('Y-m-d'));
     $exists = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagement_history','',$conditionPr);
 
     if ($exists) {
         $this->common_model->commonRecordUpdate('Compliance_wasteManagement_history','id',$exists[0]['id'], $data);
     } else {
         $this->common_model->commonRecordCreate('Compliance_wasteManagement_history',$data);
     }
 
     echo json_encode(['status' => 'success']);
 }

    
   
    
    
    function history(){
      $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementsites', '', ['status' => 1,'location_id' => $this->selected_location_id]);
      
      $this->load->view('general/header');
      $this->load->view('WasteManagement/history', $data);
      $this->load->view('general/footer');
        
    }
    
    public function historyData($encodedDateRange = '', $prep_id = '') {
        // Handle input
        if ($encodedDateRange == '' && $prep_id == '') {
            $dateRange = $this->input->post('date_range');
            $prep_id = $this->input->post('site_id');
        } else {
            $dateRange = urldecode($encodedDateRange);
        }

        // Validate and process date range
        $dateParts = explode(" to ", $dateRange);
        if (count($dateParts) == 2) {
            $fromDate = date('Y-m-d', strtotime(trim($dateParts[0])));
            $toDate = date('Y-m-d', strtotime(trim($dateParts[1])));

            // Generate unique dates
            $uniqueDates = array();
            $currentDate = $fromDate;
            while ($currentDate <= $toDate) {
                $uniqueDates[] = $currentDate;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }

            // Fetch data
            $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementsites', '', array('location_id' => $this->selected_location_id)); 
            $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementPrepArea', '', array('location_id' => $this->selected_location_id)); 
            $productCondition = array('status' => 1, 'is_deleted' => 0);
            $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagementproducts', '', $productCondition);

            // Fetch history data
            $condition = array(
                'date_entered >=' => $fromDate,
                'date_entered <=' => $toDate,
                'location_id' => $this->selected_location_id
            );
            if ($prep_id != '') {
                $condition['prep_id'] = $prep_id;
            }
            // echo "<pre>"; print_r($condition); exit;
            $history_data = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagement_history', '', $condition);

            // Restructure history data by date and product_id
            $weeklyWasteData = array();
            foreach ($history_data as $item) {
                $date_entered = $item['date_entered'];
                $product_id = $item['product_id'];
                if (!isset($weeklyWasteData[$date_entered])) {
                    $weeklyWasteData[$date_entered] = array();
                }
                $weeklyWasteData[$date_entered][$product_id] = $item;
            }

            // Pass data to view
            $data['uniqueDates'] = $uniqueDates;
            $data['dateRange'] = $dateRange;
            $data['site_id'] = $prep_id;
            $data['weeklyWasteData'] = $weeklyWasteData;
            
            //  echo "<pre>"; print_r($data['site_detail']);
            //  echo "==========";
            //   echo "<pre>"; print_r($data['prep_detail']);
            //   echo "==========";
            //   echo "<pre>"; print_r($data['products']);
            //     echo "==========";
            // echo "<pre>"; print_r($uniqueDates);
            //  echo "==========";
            // echo "<pre>"; print_r($weeklyWasteData); exit;

            // Only admin and manager can edit history
            $roleName = get_logged_in_user_role($this->ion_auth, 'name');
            $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';

            $this->load->view('general/header');
            $this->load->view('WasteManagement/historyDetails', $data);
            $this->load->view('general/footer');
        } else {
            show_error('Invalid date range format');
        }
    }

   // In application/modules/Compliance/controllers/Waste/Home.php
// In application/modules/Compliance/controllers/Waste/Home.php
public function updateWasteHistory() {
    // Only admin and manager can update history
    $roleName = get_logged_in_user_role($this->ion_auth, 'name');
    if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
        return;
    }

    // Validate input
    $product_id = $this->input->post('product_id', TRUE);
    $date_entered = $this->input->post('date_entered', TRUE);
    $prep_id = $this->input->post('prep_id', TRUE);
    $location_id = $this->input->post('location_id', TRUE);
    $wasteM_value = $this->input->post('wasteM_value', TRUE);
    $entered_by = $this->input->post('entered_by', TRUE);
    $items_wasted = $this->input->post('items_wasted', TRUE);

    // Check for required fields
    if (empty($product_id) || empty($date_entered) || empty($prep_id) || empty($location_id)) {
        $response = array('status' => 'error', 'message' => 'Missing required fields');
        echo json_encode($response);
        return;
    }

    // Define condition for checking existing record
    $condition = array(
        'product_id' => $product_id,
        'date_entered' => $date_entered,
        'prep_id' => $prep_id,
        'location_id' => $location_id
    );

    // Determine which field is being updated (only include fields actually sent in POST)
    $update_data = array();
    if ($this->input->post('wasteM_value') !== FALSE) {
        $update_data['wasteM_value'] = $wasteM_value;
    }
    if ($this->input->post('entered_by') !== FALSE) {
        $update_data['entered_by'] = $entered_by;
    }
    if ($this->input->post('items_wasted') !== FALSE) {
        $update_data['items_wasted'] = $items_wasted;
    }

    // If no fields to update, return error
    if (empty($update_data)) {
        $response = array('status' => 'error', 'message' => 'No valid fields provided for update');
        echo json_encode($response);
        return;
    }

    // Check if record exists
    $exists = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagement_history', '', $condition);

    if (!empty($exists)) {
        // Update existing record with only the provided field
        $this->common_model->commonRecordUpdateMultipleConditions('Compliance_wasteManagement_history', $condition, $update_data);
        $response = array('status' => 'success', 'message' => 'Record updated successfully');
    } else {
        // Prepare full data for new record
        $data = array(
            'product_id' => $product_id,
            'date_entered' => $date_entered,
            'prep_id' => $prep_id,
            'location_id' => $location_id,
            'wasteM_value' => $wasteM_value ?: NULL,
            'entered_by' => $entered_by ?: NULL,
            'items_wasted' => $items_wasted ?: NULL
        );
        // Insert new record
        $insert_id = $this->common_model->commonRecordCreate('Compliance_wasteManagement_history', $data);
        if ($insert_id) {
            $response = array('status' => 'success', 'message' => 'Record inserted successfully');
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to insert record');
        }
    }

    echo json_encode($response);
}

   
   
   // product add update etc..
   
     function listProduct(){
         $productCondition = array('status' => 1,'is_deleted' => 0);
         $condition = array('status' => 1,'location_id' => $this->selected_location_id);
        $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagementproducts','',$productCondition);
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementsites','',$condition);
        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementPrepArea','',$condition);
        
        // echo "<pre>"; print_r($data['products']); print_r($data['site_detail']);  print_r($data['prep_detail']); exit;
        $this->load->view('general/header');
        $this->load->view('WasteManagement/listProduct', $data);
        $this->load->view('general/footer');
    }
    
   public function addOrUpdateProduct() {
        $id = $this->input->post('id');
        $prep_id = $this->input->post('prep_id');

        // Look up site_id from the selected prep area
        $site_id = 0;
        if ($prep_id) {
            $prepRecord = $this->common_model->fetchRecordsDynamically('Compliance_WasteManagementPrepArea', '', array('id' => $prep_id));
            if (!empty($prepRecord)) {
                $site_id = $prepRecord[0]['site_id'];
            }
        }

       $data = [
         'product_name' => $this->input->post('product_name') ?? null,
         'par_level'    => $this->input->post('par_level') ?? null,
         'prep_id'      => $prep_id ?? null,
         'site_id'      => $site_id,
         'status'       => 1,
         'is_deleted'   => 0,
        ];

        if ($id) {
            $this->common_model->commonRecordUpdate('Compliance_wasteManagementproducts','id',$id, $data);
        } else {
            $this->common_model->commonRecordCreate('Compliance_wasteManagementproducts',$data);
        }

       redirect('Compliance/Waste/home/listProduct');
    }
    
    public function getProductById($id) {
        $condition = array('id' => $id);
        $product = $this->common_model->fetchRecordsDynamically('Compliance_wasteManagementproducts','',$condition);
        echo json_encode($product);
    }
    
      public function delete(){
        $id = $this->input->post('id', TRUE);
        $tableName = $this->input->post('table_name', TRUE);
         $data = [
            'is_deleted' => 1,
        ];
       
         $this->common_model->commonRecordUpdate($tableName, 'id', $id, $data);
         echo "success";
        
    }
    
	
}