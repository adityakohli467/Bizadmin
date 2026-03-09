<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chillinghome extends MY_Controller {
    function __construct() {
		parent::__construct();
		 !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
		  $this->load->model('temp_model');
	
		   $this->load->model('Chillingtemp/prep_model');
		  $this->load->model('config_model');
		   $this->load->model('general_model');
		    $this->load->model('Chillingtemp/chillingtemp_model');
	   $this->selected_location_id = $this->session->userdata('location_id');
	   $this->system_id = $this->session->userdata('system_id');
	   $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
	}
	function insertPasRecords(){
	    // this for developer only to enter past data for chilling food, if manager misses and we have audit to come suppose
	   //$start_date ='2024-01-01';
    //     $end_date = '2024-11-27';
    //     $this->chillingtemp_model->insertPastrecords($start_date,$end_date);    
	}
	
	public function index($system_id='')
    {   
       
      
        $data['site_detail'] = $this->chillingtemp_model->get_allSitesForDash(); 
         $chillingTempConfigurationData = $this->config_model->getConfiguration('','chillingTemp');
        if(isset($chillingTempConfigurationData[0]['data']) && !empty($chillingTempConfigurationData[0]['data'])){ 
            $chillingTempConfigurationData = unserialize($chillingTempConfigurationData[0]['data']);
             $data['minTempAtFinish'] =  (isset($chillingTempConfigurationData['tempAtFinishMin']) ? $chillingTempConfigurationData['tempAtFinishMin'] :'');
             $data['minTempAfterTwoHrs'] =  (isset($chillingTempConfigurationData['tempAfterTwoHrs']) ? $chillingTempConfigurationData['tempAfterTwoHrs'] :'');
             $data['minTempAfterFourHrs'] =  (isset($chillingTempConfigurationData['tempAfterFourHrs']) ? $chillingTempConfigurationData['tempAfterFourHrs'] :'');
        }    
        // echo "<pre>"; print_r($data['site_detail']); exit;
         $data['todaysChillingTempData'] = $this->chillingtemp_model->fetchTodaysEnteredTempData();
        
        // fetch all the temp data for past 7 days whose temp was exceded while recording
        $data['exceededTempData'] = $this->chillingtemp_model->fetchExceededTempData();
        
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
          
          
      	$this->load->view('general/header');
      	$this->load->view('ChillingTemp/dashboard',$data);
      	$this->load->view('general/footer');
     }
    
    function saveTempDashboardData(){
        $json_data = file_get_contents('php://input');
      $tempData = array();
    if (!empty($json_data)) {
        $tempData = json_decode($json_data, true)[0];
    }
 
   
        $data['site_name'] = $this->temp_model->getSiteNameFromId($tempData['site_id'],'TEMP_chillingSites');
        $data['prep_name'] = $this->temp_model->getPrepNameFromId($tempData['prep_id'],'TEMP_chillingPrepArea');
       
        $mailConfigurationData = $this->config_model->getConfiguration('chillingTempExceed_mail','mail');
       
        $data['tempAtFinish']= $tempData['tempAtFinish'];
        $data['tempAfterTwohours']= $tempData['tempAfterTwohours'];
        $data['tempAfterFourhours']= $tempData['tempAfterFourhours'];
        $data['entered_by']= $tempData['entered_by'];
        $data['foodName'] = $tempData['foodName'];
        $data['minTempAtFinish'] = $tempData['minTempAtFinish'];
        $data['minTempAfterFourHrs'] = $tempData['minTempAfterFourHrs'];
        $data['minTempAfterTwoHrs'] = $tempData['minTempAfterTwoHrs'];
        $data['locationName'] = fetchLocationNamesFromIds($this->selected_location_id,true);
        $mailContent = $this->load->view('Mail/chillingTempExceed',$data,TRUE);
        if($tempData['isTempok'] == 'notOk' && $tempData['is_completed'] == '1'){
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
    $tempData['date_entered'] = date("Y-m-d");
    unset($tempData['minTempAtFinish']); unset($tempData['minTempAfterFourHrs']); unset($tempData['minTempAfterTwoHrs']);
    $this->chillingtemp_model->recordChillingTempForTodays($tempData,$tempData['id']); 
   
          
    }
    public function updateExceededTemp(){
         $id = $this->input->post('id');
         $data['correctedTemp'] = $this->input->post('correctedTemp');
         $data['manager_comments'] = $this->input->post('manager_comments');
         $this->chillingtemp_model->updateExceededTemp($id,$data); 
    }
    
    
    function tempCHistory(){
      
        // if($this->selected_location_id == 31){
        //   $start_date ='2024-01-01';
        // $end_date = '2024-11-27';
        // $siteId = 17;
        // $prepId = 19;
        // $this->chillingtemp_model->insertPastrecords($start_date,$end_date,$siteId,$prepId); 
        // exit;    
        // }
       
         $data['site_detail'] = $this->chillingtemp_model->get_allSitesForDash();
      	$this->load->view('general/header');
      	$this->load->view('ChillingTemp/tempHistory',$data);
      	$this->load->view('general/footer');
        
    }
  function historyChillingData($encodedDateRange='',$site_id=''){
      
      if($encodedDateRange == '' && $site_id == ''){
      $dateRange =  $this->input->post('date_range'); 
      $site_id =  $this->input->post('site_id');    
      }else{
         $dateRange = urldecode($encodedDateRange); 
      }
     
    $data['site_detail'] = $this->chillingtemp_model->get_allSitesForDash($site_id);
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
      $data['weeklyTempData'] = $this->chillingtemp_model->fetchTempViewHistoryData($fromDate,$toDate,$site_id);
    //   echo "<pre>"; print_r($data['weeklyTempData']); exit;
    
        // Only admin and manager can edit history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';
    
        $this->load->view('general/header');
      	$this->load->view('ChillingTemp/tempHistoryDetails',$data);
      	$this->load->view('general/footer');
      	
      } else {
    // Handle invalid input or display an error message
      echo "Invalid date range format";
     }

       
    }
    
   public function tempHistoryUpdateAlldata()
  {
    // Only admin and manager can update history
    $roleName = get_logged_in_user_role($this->ion_auth, 'name');
    if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
        return;
    }

    $id = $this->input->post('id');
    $data = [
        'foodName' => $this->input->post('foodName'),
        'startTime' => $this->input->post('startTime'),
        'finishTime' => $this->input->post('finishTime'),
        'tempAtFinish' => $this->input->post('tempAtFinish'),
        'chillingStartTime' => $this->input->post('chillingStartTime'),
        'tempAfterTwohours' => $this->input->post('tempAfterTwohours'),
        'tempAfterFourhours' => $this->input->post('tempAfterFourhours'),
        'entered_by' => $this->input->post('entered_by')
    ];

    // Update the record in the database
     $this->temp_model->commonRecordUpdate('TEMP_chillingTemprecordHistory','id',$id,$data);
    
      echo json_encode(['status' => 'success']);
}


   function tempHistoryUpdatec(){
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
          $data['tempAtFinish'] = $updatedTempData;
          $this->chillingtemp_model->updateExceededTemp($data['id'],$data);    
         }
         
         }
       }  
     }
    
     
     $dateRange = $_POST['dateRange']; $siteId = $_POST['site_id'];
     $encodedDateRange = urlencode($dateRange);
   redirect('/Temp/home/chillinghistoryData/'.$encodedDateRange.'/'.$siteId);    
   } 
   
    public function save_signature()
   {
    $signature = $this->input->post('signature', TRUE);
    // echo $signature; exit;
    if (empty($signature)) {
        echo json_encode(['status' => 'error', 'message' => 'Signature is required.']);
        return;
    }

    $this->temp_model->save_signature($signature,'TEMP_chillingTemprecordHistory');

    echo json_encode(['status' => 'success', 'message' => 'Signature saved.']);
   }
	
}