<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    function __construct() {
		parent::__construct();
		!$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
		$this->load->model('temp_model');
		$this->load->model('config_model');
		$this->load->model('general_model');
		$this->load->model('equip_model');
	    $this->selected_location_id = $this->session->userdata('location_id');
	    $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
	      ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	}
	
	 public function index($system_id='')
    {   
        
      
        (isset($system_id) && $system_id !='' ? $this->session->set_userdata('system_id',$system_id) : '');
        $this->load->model('equip_model');
        
        $data['site_detail'] = $this->temp_model->get_allSitesForDash(); 
        $data['EquipListForDash'] = $this->temp_model->get_allEquipForDash(); 
        $data['todaysTempData'] = $this->temp_model->fetchTodaysEnteredTempData();
        // fetch all the temp data for past 1 month whose temp was exceded while recording
        $data['exceededTempData'] = $this->temp_model->fetchExceededTempData();
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
        
        // echo "<pre>"; print_r($data['EquipListForDash']); exit;
        // $data['commentsQuestions'] = $this->equip_model->get_all_sitesQuestion();
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
          }else{
            $this->session->set_userdata('mail_from','info@bizadmin.com.au');  
          }
          
          
      	$this->load->view('general/header');
      	$this->load->view('dashboard/dashboard',$data);
      	$this->load->view('general/footer');
  
    	
        
    }
    
    function saveTempDashboardData(){
        $json_data = file_get_contents('php://input');
      $tempData = array();
    if (!empty($json_data)) {
        $tempData = json_decode($json_data, true)[0];
    }
//     ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
   if($tempData['equip_IsTempok'] == 'notOk'){
        $data['site_name'] = $this->temp_model->getSiteNameFromId($tempData['site_id']);
        $data['prep_name'] = $this->temp_model->getPrepNameFromId($tempData['prep_id']);
        $eqippDetails =  $this->temp_model->getEquipNameFromId($tempData['equip_id'],'temp_min,temp_max',true);
        $mailConfigurationData = $this->config_model->getConfiguration('tempExceed_mail','mail');
       
        $data['recordedEntry']= $tempData['equip_temp'];
        $data['staff_comments']= $tempData['staff_comments'];
        $data['entered_by']= $tempData['entered_by'];
        $data['equip_name'] = $eqippDetails->equip_name;
        $data['range'] = $eqippDetails->temp_min.' to '.$eqippDetails->temp_max;
        $data['locationName'] = fetchLocationNamesFromIds($this->selected_location_id,true);
        $mailContent = $this->load->view('Mail/TempExceeded',$data,TRUE);
        
         if(isset($mailConfigurationData) && !empty($mailConfigurationData)){ 
            $emailTo = (isset($mailConfigurationData[0]) && $mailConfigurationData[0] ? unserialize($mailConfigurationData[0]['data']) : array());
            $emailSendTo =  (isset($emailTo[0]) && !empty($emailTo[0]) ? $emailTo[0] : 'kaushika@kjcreate.com.au ');
             $Notificationmsg = 'Temperature exceeded at site '.$data['site_name'].' for location '.$data['locationName'];
             
            createNotification($this->tenantDb,$this->session->userdata('system_id'),$this->selected_location_id,'alert',$Notificationmsg); 
            // echo $emailSendTo; exit;
           $this->sendEmail($emailSendTo, 'Temperature Exceeded', $mailContent,$this->session->userdata('mail_from'));      
         }
        
       
   }
    $tempData['location_id'] = $this->selected_location_id;
    $tempData['date_entered'] = date("Y-m-d");
    $tempData['is_completed'] = 1;
    $tempData['entered_time'] = date("H:i:s");
    
    $this->temp_model->updateTempForTodays($tempData['equip_id'],$tempData); 
    
          
    }
    public function updateExceededTemp(){
         $id = $this->input->post('id');
         $data['correctedTemp'] = $this->input->post('correctedTemp');
         $data['manager_comments'] = $this->input->post('manager_comments');
         $this->temp_model->updateExceededTemp($id,$data); 
    }
    
    
    function tempHistory(){
         $data['site_detail'] = $this->temp_model->get_allSitesForDash();
      	$this->load->view('general/header');
      	$this->load->view('dashboard/tempHistory',$data);
      	$this->load->view('general/footer');
        
    }
  function historyData($encodedDateRange='',$site_id=''){
      
     if($encodedDateRange == '' && $site_id == ''){
      $dateRange =  $this->input->post('date_range'); 
      $site_id =  $this->input->post('site_id');    
      }else{
     $dateRange = urldecode($encodedDateRange); 
      } 
    $data['site_detail'] = $this->temp_model->get_allSitesForDash();
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
    $data['uniqueDates'] = $uniqueDates;
    $data['dateRange'] = $dateRange;
    $data['site_id'] = $site_id;  
    
       $data['EquipListForDash'] = $this->temp_model->get_allEquipForDash($site_id); 
       $data['weeklyTempData'] = $this->temp_model->fetchTempViewHistoryData($fromDate,$toDate,$site_id);
    //   echo "<pre>"; print_r($data['weeklyTempData']); exit;
    
        // Only admin and manager can edit history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';
    
        $this->load->view('general/header');
      	$this->load->view('dashboard/tempHistoryDetails',$data);
      	$this->load->view('general/footer');
      	
      } else {
    // Handle invalid input or display an error message
      echo "Invalid date range format";
     }

       
    }
    
  public function uploadTemperatureAttachment()
{
    $orzName = $this->tenantIdentifier;    

    $config['upload_path'] = './uploaded_files/'.$orzName.'/Temp/TemperatureAttachments/';
    $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
    $config['encrypt_name'] = TRUE;
    $config['max_size'] = 3048;

    $this->load->helper('image_compression'); // IMPORTANT
    $this->load->library('upload', $config);

    $uploaded_files = [];
    $countfiles = count($_FILES['userfile']['name']);

    // initialize upload library once (faster)
    $this->upload->initialize($config);

    for ($i = 0; $i < $countfiles; $i++) {

        if (!empty($_FILES['userfile']['name'][$i])) {

            // Prepare each file for CI upload
            $_FILES['file']['name']     = $_FILES['userfile']['name'][$i];
            $_FILES['file']['type']     = $_FILES['userfile']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['userfile']['tmp_name'][$i];
            $_FILES['file']['error']    = $_FILES['userfile']['error'][$i];
            $_FILES['file']['size']     = $_FILES['userfile']['size'][$i];

            // Try upload
            if (!$this->upload->do_upload('file')) {
                echo json_encode([
                    'status' => false,
                    'message' => $this->upload->display_errors('', '')
                ]);
                return;
            }

            // Uploaded info
            $uploadData = $this->upload->data();
            $fullPath   = $uploadData['full_path'];   // full original path
            $filePath   = $uploadData['file_path'];   // directory path
            $fileName   = $uploadData['file_name'];   // compressed file uses same name

            // Check if file is image (skip PDF)
            $isImage = in_array($uploadData['file_ext'], ['.jpg', '.jpeg', '.png', '.gif']);

            if ($isImage) {
                // Compress IN-PLACE (overwrite original)
                compress_to_size($fullPath, $fullPath, 900);  // target 900 KB

                // Alternatively: compress_image($fullPath, $fullPath, 70); // 70% quality
            }

            $uploaded_files[] = $fileName;
        }
    }

    // Save DB record
    $data = [
        'attachment' => serialize($uploaded_files)
    ];
    $this->temp_model->updateTempForTodays($_POST['equipId'], $data, TRUE);

    // Return success response
    echo json_encode([
        'status' => true,
        'message' => 'Files uploaded & compressed successfully',
        'files' => $uploaded_files
    ]);
    exit;
}


    public function fetchAttachmentUploadedToday(){
    $equipId =  $this->input->post('equipId');
    $result = $this->temp_model->fetchAttachmentUploadedToday($equipId);
    if(isset($result[0]['attachment']) && $result[0]['attachment'] !=''){
      $attachments = unserialize($result[0]['attachment']);  
    }else{
        $attachments =  array();
    }
    echo json_encode($attachments);
    }
    
    function tempHistoryUpdate(){
   
     // Only admin and manager can update history
     $roleName = get_logged_in_user_role($this->ion_auth, 'name');
     if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
         echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
         return;
     }

     // Disable CodeIgniter's output class
     $this->output->set_header('Content-Type: application/json');
     $this->output->set_status_header(200);
     
     $updateCount = 0;
     $batchData = array();
     
     if(!empty($_POST)){
       // Collect all data first
       foreach($_POST as $siteprepAndrecordID => $updatedTempData){
         if($siteprepAndrecordID !='dateRange' && $siteprepAndrecordID !='site_id' && strpos($siteprepAndrecordID, 'csrf') === false){  
         $updatedTempDataString = explode('_', $siteprepAndrecordID);  
         $siteId = $updatedTempDataString[1] ? $updatedTempDataString[1] :'';  
         $prepId = $updatedTempDataString[2] ? $updatedTempDataString[2] : ''; 
         $equipId = $updatedTempDataString[3] ? $updatedTempDataString[3] : ''; 
         $dateToInsertUpdate = $updatedTempDataString[4] ? $updatedTempDataString[4] : ''; 
         
         if($siteId !='' && $prepId !='' && $equipId !='' && $dateToInsertUpdate !='' && $updatedTempData !=''){
          $batchData[] = array(
            'equip_temp' => $updatedTempData,
            'is_completed' => 1,
            'equip_IsTempok' => 'ok',
            'location_id' => $this->selected_location_id,
            'site_id' => $siteId,
            'prep_id' => $prepId,
            'equip_id' => $equipId,
            'date_entered' => $dateToInsertUpdate
          );
         }
         
         }
       }  
       
       // Batch update all at once
       if(!empty($batchData)){
         $updateCount = $this->temp_model->batchTempHistoryUpdate($batchData);
       }
     }

     // Use CodeIgniter's output method
     $response = array('status' => 'success', 'message' => 'Updated '.$updateCount.' records successfully');
     $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode($response));
   } 
   
    public function save_signature()
   {
    $signature = $this->input->post('signature', TRUE);
    // echo $signature; exit;
    if (empty($signature)) {
        echo json_encode(['status' => 'error', 'message' => 'Signature is required.']);
        return;
    }

    $this->temp_model->save_signature($signature,'TEMP_record_tempHistory');

    echo json_encode(['status' => 'success', 'message' => 'Signature saved.']);
   }
    
	
}