<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fryerhome extends MY_Controller {
    function __construct() {
		parent::__construct();
		 !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
		  $this->load->model('temp_model');
		   $this->load->model('common_model');
		   $this->load->model('Fryertemp/prep_model');
		  $this->load->model('config_model');
		   $this->load->model('general_model');
		    $this->load->model('Fryertemp/fryertemp_model');
	   $this->selected_location_id = $this->session->userdata('location_id');
	   $this->system_id = $this->session->userdata('system_id');
	   $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
	   
	  
	}
	
	public function index($system_id='')
    {   
        // $this->temp_model->updateTempDataRrecords();
        
        $data['site_detail'] = $this->fryertemp_model->get_allSitesForDash(); 
         $fryerTempConfigurationData = $this->config_model->getConfiguration('','foodTemp');
        if(isset($fryerTempConfigurationData[0]['data']) && !empty($fryerTempConfigurationData[0]['data'])){ 
            $fryerTempConfigurationData = unserialize($fryerTempConfigurationData[0]['data']);
             $data['foodMaxTemp'] =  (isset($fryerTempConfigurationData['foodMaxTemp']) ? $fryerTempConfigurationData['foodMaxTemp'] :'');
             $data['foodMinTemp'] =  (isset($fryerTempConfigurationData['foodMinTemp']) ? $fryerTempConfigurationData['foodMinTemp'] :'');
        }    
        // echo "<pre>"; print_r($data['site_detail']); exit;
         $data['todaysFoodTempData'] = $this->fryertemp_model->fetchTodaysEnteredTempData();
        
        // fetch all the temp data for past 7 days whose temp was exceded while recording
        $data['exceededTempData'] = $this->fryertemp_model->fetchExceededTempData();
        
        $fryerTempConfigurationData = $this->config_model->getConfiguration('','foodTemp');
        $chillingTempConfigurationData = $this->config_model->getConfiguration('','chillingTemp');
        if(isset($fryerTempConfigurationData[0]['data']) && !empty($fryerTempConfigurationData[0]['data'])){ 
             $fryerTempConfigurationData = unserialize($fryerTempConfigurationData[0]['data']);
             $data['showFoodTemp'] =  (isset($fryerTempConfigurationData['showFoodTemp']) ? $fryerTempConfigurationData['showFoodTemp'] :'');
        } 
        
        if(isset($chillingTempConfigurationData[0]['data']) && !empty($chillingTempConfigurationData[0]['data'])){ 
             $chillingTempConfigurationData = unserialize($chillingTempConfigurationData[0]['data']);
             $data['showChillingTemp'] =  (isset($chillingTempConfigurationData['showChillingTemp']) ? $chillingTempConfigurationData['showChillingTemp'] :'');
        } 
      
        
          $emailSettings = $this->general_model->fetchSmtpSettings($this->selected_location_id,$this->system_id);
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
          }else{
            $this->session->set_userdata('mail_from','info@bizadmin.com.au');  
          }
          $condition = ['status' => 1];
         $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_fryertempProducts','',$condition); 
      	$this->load->view('general/header');
      	$this->load->view('FryerTemp/dashboard',$data);
      	$this->load->view('general/footer');
  
    	
        
    }
    
    function saveTempDashboardData(){
    $json_data = file_get_contents('php://input');
    $tempData = array();
    
    if (!empty($json_data)) {
        $tempData = json_decode($json_data, true)[0];
    }
    
   
        $data['site_name'] = $this->temp_model->getSiteNameFromId($tempData['site_id'],'TEMP_fryerSites');
        $data['prep_name'] = $this->temp_model->getPrepNameFromId($tempData['prep_id'],'TEMP_fryerPrepArea');
        

        $data['recordedEntry']= isset($tempData['food_temp']) ? $tempData['food_temp'] : '';
        $data['staff_comments']= isset($tempData['staff_comments']) ? $tempData['staff_comments'] : '';
        $data['entered_by']= isset($tempData['entered_by']) ? $tempData['entered_by'] : '';
        $data['recorded_by']= isset($tempData['recorded_by']) ? $tempData['recorded_by'] : '';
        $data['range'] = $tempData['currentFoodMinTempAllowed'].' to '.$tempData['currentFoodMaxTempAllowed'];
        $data['locationName'] = fetchLocationNamesFromIds($this->selected_location_id,true);
        
        // Add all chilling process data to email
        $data['start_cooking_time'] = isset($tempData['start_cooking_time']) ? $tempData['start_cooking_time'] : '';
        $data['finish_cooking_time'] = isset($tempData['finish_cooking_time']) ? $tempData['finish_cooking_time'] : '';
        $data['temp_end_cooking'] = isset($tempData['temp_end_cooking']) ? $tempData['temp_end_cooking'] : '';
        $data['time_chilling_start'] = isset($tempData['time_chilling_start']) ? $tempData['time_chilling_start'] : '';
        $data['time_chilling_finished'] = isset($tempData['time_chilling_finished']) ? $tempData['time_chilling_finished'] : '';
        $data['temp_end_chilling'] = isset($tempData['temp_end_chilling']) ? $tempData['temp_end_chilling'] : '';
       
       
        
      
    
    // Set location and date
    $tempData['location_id'] = $this->selected_location_id;
    if(!isset($tempData['date_entered']) || $tempData['date_entered'] == ''){
        $tempData['date_entered'] = date("Y-m-d");   
    }
    
    // Set completion status and attachment
    $tempData['is_completed'] = 1;
    // $tempData['attachment'] = $this->session->userdata('tempAttachment');
  
    // Save to database
    $this->fryertemp_model->recordFoodTempForTodays($tempData); 
    $this->session->unset_userdata('tempAttachment');
    
    // Return success response
    echo json_encode(array('status' => 'success', 'message' => 'Data saved successfully'));
}
    public function updateExceededTemp(){
         $id = $this->input->post('id');
         $data['correctedTemp'] = $this->input->post('correctedTemp');
         $data['manager_comments'] = $this->input->post('manager_comments');
         $this->fryertemp_model->updateExceededTemp($id,$data); 
    }
    
    
    function tempHistory(){
         $data['site_detail'] = $this->fryertemp_model->get_allSitesForDash();
      	$this->load->view('general/header');
      	$this->load->view('FryerTemp/tempHistory',$data);
      	$this->load->view('general/footer');
        
    }
  function historyData($encodedDateRange='',$site_id=''){
      
      if($encodedDateRange == '' && $site_id == ''){
      $dateRange =  $this->input->post('date_range'); 
      $site_id =  $this->input->post('site_id');    
      }else{
         $dateRange = urldecode($encodedDateRange); 
      }
     
    $data['site_detail'] = $this->fryertemp_model->get_allSitesForDash($site_id);
    $dateParts = explode(" to ", $dateRange);
    
    if (count($dateParts) == 2) {
    $fromDate = date('Y-m-d',strtotime(trim($dateParts[0])));
    $toDate = date('Y-m-d',strtotime(trim($dateParts[1])));
   

    $uniqueDates = array();
    $currentDate = $fromDate;
    while ($currentDate <= $toDate) {
    $uniqueDates[] = $currentDate;
    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    }      
      $data['dateRange'] = $dateRange;
      $data['site_id'] = $site_id;
      $data['uniqueDates'] = $uniqueDates;
      $data['weeklyTempData'] = $this->fryertemp_model->fetchTempViewHistoryData($fromDate,$toDate,$site_id);
    //   echo "<pre>"; print_r($data['weeklyTempData']); exit;
    
        // Only admin and manager can edit history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';
    
        $this->load->view('general/header');
      	$this->load->view('FryerTemp/tempHistoryDetails',$data);
      	$this->load->view('general/footer');
      	
      } else {
    // Handle invalid input or display an error message
      echo "Invalid date range format";
     }

       
    }
    
    public function uploadTemperatureAttachment()
    {
    $orzName = $this->tenantIdentifier;
    $config['upload_path'] = './uploaded_files/'.$orzName.'/Temp/FoodTemperatureAttachments/';
    $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf'; // Add allowed file types
    $config['encrypt_name'] = TRUE;
    $config['max_size'] = 8048; // Maximum file size in KB (8MB)

    $this->load->library('upload', $config);

    $uploaded_files = [];


      // Count total files
      $countfiles = count($_FILES['userfile']['name']);
 
      // Looping all files
      for($i=0;$i<$countfiles;$i++){
 
        if(!empty($_FILES['userfile']['name'][$i])){
 
          // Define new $_FILES array - $_FILES['file']
          $_FILES['file']['name'] = $_FILES['userfile']['name'][$i];
          $_FILES['file']['type'] = $_FILES['userfile']['type'][$i];
          $_FILES['file']['tmp_name'] = $_FILES['userfile']['tmp_name'][$i];
          $_FILES['file']['error'] = $_FILES['userfile']['error'][$i];
          $_FILES['file']['size'] = $_FILES['userfile']['size'][$i];

          // Set preference
          $config['upload_path'] = './uploaded_files/'.$orzName.'/Temp/FoodTemperatureAttachments/';
          $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
          $config['max_size'] = '5000'; // max_size in kb
          $config['file_name'] = $_FILES['file']['name'][$i];
 
          //Load upload library
          $this->load->library('upload',$config); 
 
    
          if($this->upload->do_upload('file')){
            // Get data about the file
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
          
            // Initialize array
            $uploaded_files[$i] = $filename;
          }
        }
 
      }

    $this->session->set_userdata('tempAttachment',serialize($uploaded_files));
    
    echo "Uploaded Files: " . implode(', ', $uploaded_files);
}
    public function fetchAttachmentUploadedToday(){
    $id =  $this->input->post('id');
    $result = $this->fryertemp_model->fetchAttachmentUploadedToday($id);
    if(isset($result[0]['attachment']) && $result[0]['attachment'] !=''){
      $attachments = unserialize($result[0]['attachment']);  
    }else{
        $attachments =  array();
    }
    echo json_encode($attachments);
    }
    
    function tempHistoryUpdatePastrecords(){
         // Only admin and manager can update history
         $roleName = get_logged_in_user_role($this->ion_auth, 'name');
         if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
             echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
             return;
         }

         $json_data = file_get_contents('php://input');
      $tempData = array();
    if (!empty($json_data)) {
        $tempData = json_decode($json_data, true)[0];
    }
    
    $tempData['location_id'] = $this->selected_location_id;
    $tempData['date_entered'] = date("Y-m-d");
    $tempData['is_completed'] = 1;
 
    $this->fryertemp_model->updateExceededTemp($tempData['id'],$tempData);  
    }
   public function save_signature()
   {
    $signature = $this->input->post('signature', TRUE);
    // echo $signature; exit;
    if (empty($signature)) {
        echo json_encode(['status' => 'error', 'message' => 'Signature is required.']);
        return;
    }

    $this->temp_model->save_signature($signature,'TEMP_fryerTemprecordHistory');

    echo json_encode(['status' => 'success', 'message' => 'Signature saved.']);
   }

   function tempHistoryUpdate(){
    //  echo "<pre>"; print_r($_POST); exit;
     if(!empty($_POST)){
       foreach($_POST as $siteprepAndrecordID => $updatedTempData){
         if($siteprepAndrecordID !='dateRange' && $siteprepAndrecordID !='site_id'){  
         $updatedTempDataString = explode('_', $siteprepAndrecordID);  
         $data['site_id'] = $updatedTempDataString[1] ? $updatedTempDataString[1] :'';  
         $data['prep_id'] = $updatedTempDataString[2] ? $updatedTempDataString[2] : ''; 
         $data['id'] = $updatedTempDataString[3] ? $updatedTempDataString[3] : ''; 
         $data['date_entered'] = $updatedTempDataString[4] ? $updatedTempDataString[4] : '';
         
         if($data['site_id'] !='' && $data['prep_id'] !='' && $data['id'] !='' && $data['date_entered'] !=''){
          $data['food_temp'] = $updatedTempData;
          $this->fryertemp_model->updateExceededTemp($data['id'],$data);    
         }
         
         }
       }  
     }
    
     
     $dateRange = $_POST['dateRange']; $siteId = $_POST['site_id'];
     $encodedDateRange = urlencode($dateRange);
   redirect('/Temp/home/foodhistoryData/'.$encodedDateRange.'/'.$siteId);    
   } 
   
   
     function listProduct(){
       

         $condition = array('status' => 1);
        $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_fryertempProducts','',$condition);
       
        $where_conditions = array('is_deleted' => 0, 'location_id' => $this->selected_location_id );
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('TEMP_fryerSites','',$where_conditions);
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea();
        $this->load->view('general/header');
        $this->load->view('FryerTemp/listProduct', $data);
        $this->load->view('general/footer');
    }
    
   public function addOrUpdateProduct() {
        $id = $this->input->post('id');
        $product_name = $this->input->post('product_name');
        $prep_id = $this->input->post('prep_id');

        $data = [
            'product_name' => $product_name,
            'prep_id' => $prep_id,
            'status' => 1
        ];

        if ($id) {
            $this->common_model->commonRecordUpdate('TEMP_fryertempProducts','id',$id, $data);
        } else {
            $this->common_model->commonRecordCreate('TEMP_fryertempProducts',$data);
        }

        echo json_encode(['status' => 'success']);
    }
    
    public function getProductById($id) {
        $condition = array('id' => $id);
        $product = $this->common_model->fetchRecordsDynamically('TEMP_fryertempProducts','',$condition);
        echo json_encode($product);
    }
    
    
      public function deleteProduct() {
        $id = $this->input->post('id');
        $data = [
            'is_deleted' => 1,
            'status' => 1
        ];
        
         $this->common_model->commonRecordUpdate('TEMP_fryertempProducts','id',$id, $data);
        echo json_encode(['success' => true]);
    
    }
    
	
}