<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Foodtemphome extends MY_Controller {
    function __construct() {
		parent::__construct();
		 !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
		  $this->load->model('temp_model');
		   $this->load->model('common_model');
		   $this->load->model('Foodtemp/prep_model');
		  $this->load->model('config_model');
		   $this->load->model('general_model');
		    $this->load->model('Foodtemp/foodtemp_model');
	   $this->selected_location_id = $this->session->userdata('location_id');
	   $this->system_id = $this->session->userdata('system_id');
	   $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
	}
	
	public function index($system_id='')
    {   
        // $this->temp_model->updateTempDataRrecords();
        
        $data['site_detail'] = $this->foodtemp_model->get_allSitesForDash(); 
         $foodTempConfigurationData = $this->config_model->getConfiguration('','foodTemp');
        if(isset($foodTempConfigurationData[0]['data']) && !empty($foodTempConfigurationData[0]['data'])){ 
            $foodTempConfigurationData = unserialize($foodTempConfigurationData[0]['data']);
             $data['foodMaxTemp'] =  (isset($foodTempConfigurationData['foodMaxTemp']) ? $foodTempConfigurationData['foodMaxTemp'] :'');
             $data['foodMinTemp'] =  (isset($foodTempConfigurationData['foodMinTemp']) ? $foodTempConfigurationData['foodMinTemp'] :'');
        }    
        // echo "<pre>"; print_r($data['site_detail']); exit;
         $data['todaysFoodTempData'] = $this->foodtemp_model->fetchTodaysEnteredTempData();
        
        // fetch all the temp data for past 7 days whose temp was exceded while recording
        $data['exceededTempData'] = $this->foodtemp_model->fetchExceededTempData();
        
        $foodTempConfigurationData = $this->config_model->getConfiguration('','foodTemp');
        $chillingTempConfigurationData = $this->config_model->getConfiguration('','chillingTemp');
        if(isset($foodTempConfigurationData[0]['data']) && !empty($foodTempConfigurationData[0]['data'])){ 
             $foodTempConfigurationData = unserialize($foodTempConfigurationData[0]['data']);
             $data['showFoodTemp'] =  (isset($foodTempConfigurationData['showFoodTemp']) ? $foodTempConfigurationData['showFoodTemp'] :'');
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
         
           $where_conditions = array('is_deleted' => 0, 'location_id' => $this->selected_location_id );
         $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_foodtempProducts','',$where_conditions); 
      	$this->load->view('general/header');
      	$this->load->view('FoodTemp/dashboard',$data);
      	$this->load->view('general/footer');
  
    	
        
    }
    
    function saveTempDashboardData(){
        $json_data = file_get_contents('php://input');
      $tempData = array();
    if (!empty($json_data)) {
        $tempData = json_decode($json_data, true)[0];
    }
 
   if($tempData['food_IsTempok'] == 'notOk'){
        $data['site_name'] = $this->temp_model->getSiteNameFromId($tempData['site_id'],'TEMP_foodSites');
        $data['prep_name'] = $this->temp_model->getPrepNameFromId($tempData['prep_id'],'TEMP_foodPrepArea');
       
        $mailConfigurationData = $this->config_model->getConfiguration('foodTempExceed_mail','mail');
       
        $data['recordedEntry']= $tempData['food_temp'];
        $data['staff_comments']= $tempData['staff_comments'];
        $data['entered_by']= $tempData['entered_by'];
        $data['foodName'] = $tempData['foodName'];
        $data['range'] = $tempData['currentFoodMinTempAllowed'].' to '.$tempData['currentFoodMaxTempAllowed'];
        $data['locationName'] = fetchLocationNamesFromIds($this->selected_location_id,true);
        $mailContent = $this->load->view('Mail/foodTempExceeded',$data,TRUE);
        
         if(isset($mailConfigurationData) && !empty($mailConfigurationData)){ 
            $emailTo = (isset($mailConfigurationData[0]) && $mailConfigurationData[0] ? unserialize($mailConfigurationData[0]['data']) : array());
            $emailSendTo =  (isset($emailTo[0]) && !empty($emailTo[0]) ? $emailTo[0] : 'kaushika@kjcreate.com.au');
             $Notificationmsg = 'Temperature exceeded at site '.$data['site_name'].' for location '.$data['locationName'];
             
            createNotification($this->tenantDb,$this->session->userdata('system_id'),$this->selected_location_id,'alert',$Notificationmsg); 
            // echo $this->session->userdata('mail_from'); exit;
           $this->sendEmail($emailSendTo, 'Temperature Exceeded', $mailContent,$this->session->userdata('mail_from'));      
         }
        
       
   }
    $tempData['location_id'] = $this->selected_location_id;
    if($tempData['date_entered'] ==''){
     $tempData['date_entered'] = date("Y-m-d");   
    }
    
    $tempData['is_completed'] = 1;
    $tempData['attachment'] = $this->session->userdata('tempAttachment');
    
    // echo "<pre>"; print_r($tempData); exit;
   
    $result = $this->foodtemp_model->recordFoodTempForTodays($tempData); 
    $this->session->unset_userdata('tempAttachment');
    
    // Return JSON response
    echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
          
    }
    public function updateExceededTemp(){
         $id = $this->input->post('id');
         $data['correctedTemp'] = $this->input->post('correctedTemp');
         $data['manager_comments'] = $this->input->post('manager_comments');
         $this->foodtemp_model->updateExceededTemp($id,$data); 
    }
    
    
    function tempHistory(){
         $data['site_detail'] = $this->foodtemp_model->get_allSitesForDash();
      	$this->load->view('general/header');
      	$this->load->view('FoodTemp/tempHistory',$data);
      	$this->load->view('general/footer');
        
    }
  function historyData($encodedDateRange='',$site_id=''){
      
      if($encodedDateRange == '' && $site_id == ''){
      $dateRange =  $this->input->post('date_range'); 
      $site_id =  $this->input->post('site_id');    
      }else{
         $dateRange = urldecode($encodedDateRange); 
      }
     
    $data['site_detail'] = $this->foodtemp_model->get_allSitesForDash($site_id);
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
      $data['weeklyTempData'] = $this->foodtemp_model->fetchTempViewHistoryData($fromDate,$toDate,$site_id);
    //   echo "<pre>"; print_r($data['weeklyTempData']); exit;
    
        // Only admin and manager can edit history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';
    
        $this->load->view('general/header');
      	$this->load->view('FoodTemp/tempHistoryDetails',$data);
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
    $result = $this->foodtemp_model->fetchAttachmentUploadedToday($id);
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
    // Keep original date_entered if provided, otherwise use today
    if(!isset($tempData['date_entered']) || empty($tempData['date_entered'])){
        $tempData['date_entered'] = date("Y-m-d");
    }
    $tempData['is_completed'] = 1;
 
    $result = $this->foodtemp_model->updateExceededTemp($tempData['id'],$tempData);
    
    // Return JSON response
    echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
    }
   public function save_signature()
   {
    $signature = $this->input->post('signature', TRUE);
    // echo $signature; exit;
    if (empty($signature)) {
        echo json_encode(['status' => 'error', 'message' => 'Signature is required.']);
        return;
    }

    $this->temp_model->save_signature($signature,'TEMP_foodTemprecordHistory');

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
          $this->foodtemp_model->updateExceededTemp($data['id'],$data);    
         }
         
         }
       }  
     }
    
     
     $dateRange = $_POST['dateRange']; $siteId = $_POST['site_id'];
     $encodedDateRange = urlencode($dateRange);
   redirect('/Temp/home/foodhistoryData/'.$encodedDateRange.'/'.$siteId);    
   } 
   
   
     function listProduct(){
        
      
        $where_conditions = array('is_deleted' => 0, 'location_id' => $this->selected_location_id );
        $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_foodtempProducts','',$where_conditions);
        
       
        
        
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('TEMP_foodtempProducts','',$where_conditions);
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea();
        $this->load->view('general/header');
        $this->load->view('FoodTemp/listProduct', $data);
        $this->load->view('general/footer');
    }
    
   public function addOrUpdateProduct() {
       
        $id = $this->input->post('id');
        $product_name = $this->input->post('product_name');
        $prep_id = $this->input->post('prep_id');

        $data = [
            'product_name' => $product_name,
            'location_id' => $this->selected_location_id,
            'foodType' => $this->input->post('foodType'),
            'prep_id' => $prep_id,
            'status' => 1
        ];

        if ($id) {
            $this->common_model->commonRecordUpdate('TEMP_foodtempProducts','id',$id, $data);
        } else {
            $this->common_model->commonRecordCreate('TEMP_foodtempProducts',$data);
        }

        echo json_encode(['status' => 'success']);
    }
    
    public function getProductById($id) {
        $condition = array('id' => $id);
        $product = $this->common_model->fetchRecordsDynamically('TEMP_foodtempProducts','',$condition);
        echo json_encode($product);
    }
    
     public function deleteProduct() {
        $id = $this->input->post('id');
        $data = [
            'is_deleted' => 1,
            'status' => 1
        ];
        
         $this->common_model->commonRecordUpdate('TEMP_foodtempProducts','id',$id, $data);
        echo json_encode(['success' => true]);
    
    }
    
	
}