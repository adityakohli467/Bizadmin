<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends MY_Controller {

    function __construct() {
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
       
		$this->load->model('employee_model');
		$this->load->model('auth_model');
		$this->load->model('config_model');
	    $this->load->model('common_model');
        $this->location_id = $this->session->userdata('location_id');
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');

    }
    
    function allConfig(){
       $conditions = array('status' => '1', 'is_deleted' => '0','location_id' => $this->location_id);
       $conditionsStressP = array('status' => '1', 'is_deleted' => '0');
       $conditionsTwo = array('location' => $this->location_id, 'configureFor' => 'documents');
       $conditionsMail = array('location' => $this->location_id, 'configureFor' => 'emails');
       $conditionsGeneralConfig = array('location' => $this->location_id, 'configureFor' => 'feature_toggle');
       $conditionsGeneralConfigTimesheetWORoster = array('location' => $this->location_id, 'configureFor' => 'timesheetWORoster_toggle');
       $conditionsLocationCapture = array('location' => $this->location_id, 'configureFor' => 'location_capture_toggle');
       $conditionsSuperConfig = array('location' => $this->location_id, 'configureFor' => 'superannuation');
        
       $fields = ['data','metaData'];
       $data['allStressProfile'] = $this->common_model->fetchRecordsDynamically('HR_stressProfile', '',$conditionsStressP);
       $data['uploadedFiles'] = $this->common_model->fetchRecordsDynamically('HR_configuration', $fields,$conditionsTwo);
       $data['allLeaveTypes'] = $this->common_model->fetchRecordsDynamically('HR_leaves');
       $data['mailConfigData'] = $this->common_model->fetchRecordsDynamically('HR_configuration','',$conditionsMail);
       $data['positionConfigData'] = $this->common_model->fetchRecordsDynamically('HR_emp_position',['position_id','position_name','position_type'],$conditions);
       $data['payrollTypeConfigData'] = $this->common_model->fetchRecordsDynamically('HR_payroll_type',['id','name'],$conditions);
       
    
      $superConfig = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsSuperConfig);

if(isset($superConfig[0]['data']) && $superConfig[0]['data'] !='') {
    $data['superConfigData'] = json_decode($superConfig[0]['data'], true);
} else {
    // Default values
    $data['superConfigData'] = [
        'super_percentage' => '12',
        'enable_tier_payroll' => '0',
        'payroll_tax_rate' => '5.45',
        'accounting_software' => 'myob'
    ];
}     
       
       $toggleConfig = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsGeneralConfig);
        if(isset($toggleConfig[0]['data']) && $toggleConfig[0]['data'] !='') {
            $generalConfigData = json_decode($toggleConfig[0]['data'], true);
            $data['generalConfigData']['feature_toggle'] = isset($generalConfigData['value']) ? $generalConfigData['value'] : '0';
            
        }
        
        
        $toggleConfigTWOR = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsGeneralConfigTimesheetWORoster);
        if(isset($toggleConfigTWOR[0]['data']) && $toggleConfigTWOR[0]['data'] !='') {
            $generalConfigDataTWOR = json_decode($toggleConfigTWOR[0]['data'], true);
            $data['generalConfigData']['timesheetWORoster_toggle'] = isset($generalConfigDataTWOR['value']) ? $generalConfigDataTWOR['value'] : '0';
            
        }
        
        $toggleConfigLocationCapture = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsLocationCapture);
        if(isset($toggleConfigLocationCapture[0]['data']) && $toggleConfigLocationCapture[0]['data'] !='') {
            $generalConfigDataLocationCapture = json_decode($toggleConfigLocationCapture[0]['data'], true);
            $data['generalConfigData']['location_capture_toggle'] = isset($generalConfigDataLocationCapture['value']) ? $generalConfigDataLocationCapture['value'] : '0';
            
        }
        
        // Onboarding form tab customization settings
        $conditionsOnboardingTabs = array('location' => $this->location_id, 'configureFor' => 'onboarding_tabs');
        $onboardingTabsCfg = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsOnboardingTabs);
        $data['onboardingTabsConfig'] = (isset($onboardingTabsCfg[0]['data']) && $onboardingTabsCfg[0]['data'] != '') ? json_decode($onboardingTabsCfg[0]['data'], true) : [];
        if(!is_array($data['onboardingTabsConfig'])) { $data['onboardingTabsConfig'] = []; }
        
       
       
    //  echo "<pre>"; print_r($data['uploadedFiles']); exit;
      $this->load->view('general/header');
	  $this->load->view('config/allConfig',$data);
	  $this->load->view('general/footer');  
        
    }
    // Stress profile tab
    function createStressProfile(){
        $this->common_model->commonRecordCreate('HR_stressProfile',$_POST);
        echo "success";
    }
    function updateStressProfile(){
    $this->common_model->commonRecordUpdate('HR_stressProfile','id',$_POST['id'],$_POST); 
    echo "success";
    }
    
    // Documents Tab
    function uploadConfigFiles(){
        
        $uploaded_files = [];
        $uploadPath='./uploaded_files/'.$this->tenantIdentifier.'/HR/OtherFiles';
        $uploadedFileName = $this->common_model->uploadAttachment($_FILES,$uploadPath);
        
          $data['configureFor'] = 'documents';
          $data['created_date'] = date('Y-m-d');
          $data['location'] = $this->location_id;
          $data['data'] = $uploadedFileName;
          $data['metaData'] = $_POST['file_type'];
          $this->common_model->commonRecordDelete('HR_configuration',$_POST['file_type'],'metaData'); 
          $this->common_model->commonRecordCreate('HR_configuration',$data);
          
          $response['status'] = 'success';
          $response['message'] = "File Uploaded";
          echo json_encode($response); exit; 
	     
    }
    
    // Leave Tab
    function addleaveType(){
         $data = $_POST;
         $data['is_paid'] = (isset($_POST['is_paid']) && $_POST['is_paid'] == 'on' ? 1 : 0);
         $this->common_model->commonRecordCreate('HR_leaves',$data);  
    }
    
    function updateleaveType(){
    $data = $_POST; $data['is_paid'] = (isset($_POST['is_paid']) && $_POST['is_paid'] == 'on' ? 1 : 0);   
    $this->common_model->commonRecordUpdate('HR_leaves','id',$_POST['id'],$data);    
    }
    
    function deleteLeave(){
       $this->common_model->commonRecordDelete($_POST['tableName'],$_POST['id']); 
       echo "success";
    }
    
    // Emails Tab
    
    function addEmailsSetting(){
      if(!empty($_POST)){
        	    $mailTypeList = array();
        	      $resultArray = array();
                  $nontificationMail = array();
      // Loop through the 'mailType' values
    
      foreach ($_POST['mailType'] as $index => $mailType) {
      if (!isset($resultArray[$mailType])) {
        $resultArray[$mailType] = array(
            'configId' => (isset($_POST['configId']) ? $_POST['configId'] : ''),
            'metaData' => $mailType,
            'emailTo' => array(),
        );
       }
     $resultArray[$mailType]['emailTo'][] = trim($_POST['emailTo'][$index]);
     }
            $resultArray = array_values($resultArray);
            $allConfigIds = (isset($_POST['configId']) ? $_POST['configId'] : '');
  
                 foreach ($resultArray as $keyConfigId=> $configMailData) {
        	      $configData = array( 
						'data' => serialize($configMailData['emailTo']),
						'configureFor' => 'emails',
						'metaData'=> $configMailData['metaData'],
						'created_date' => date('Y-m-d'),
					);
					if(isset($_POST['configId'][$keyConfigId])){
					$id =  $_POST['configId'][$keyConfigId];
					unset($allConfigIds[$keyConfigId]);
					}else{
					    $id ='';
					}
					$result = $this->config_model->configure($configData,$id); 	 
                 }
                 // remove all the ids wch has be removed from UI in last update, issue was that last column with only one record type was not deleting
                 // for eg. if For weekly float there is only one email and while updating if we delete that row it was not deleting
                 if(isset($allConfigIds) && !empty($allConfigIds)){
                foreach ($allConfigIds as $configId) {
                $result = $this->config_model->deleteConfig($configId);     
                 }     
                 }
                 
             echo "Success";
		    
		    }  
        
    }
    
    function addPositionSetting(){
     $data = []; 
    // fetch all existing position , to compare & match if some position has been deleted in recent ajax post , if yes, than mark that id as deleted in Database
     $conditions = array('status' => '1', 'is_deleted' => '0','location_id' => $this->location_id);
     $fieldsToFetch = array('position_id');
     $positionConfigData = $this->common_model->fetchRecordsDynamically('HR_emp_position',$fieldsToFetch,$conditions);
     if(isset($positionConfigData) && isset($_POST['position_id'])){
         $existingPids = array_column($positionConfigData,'position_id');
         $postedPids  = $_POST['position_id'];
         $positiontoDelete = array_diff($existingPids, $postedPids);
         if(isset($positiontoDelete) && !empty($positiontoDelete)){
       $this->common_model->commonBulkRecordDelete('HR_emp_position',$positiontoDelete,'position_id');       
         }
        
     }
   
   foreach ($_POST['position_name'] as $key => $position_name) {

    $data = [
        'position_name' => $position_name,
        'position_type' => $_POST['position_type'][$key]
    ];

    if (isset($_POST['position_id'][$key])) { 
        $position_id = $_POST['position_id'][$key];
        $this->common_model->commonRecordUpdate('HR_emp_position', 'position_id', $position_id, $data);

    } else {
        $data['location_id'] = $this->location_id; 
        $data['date_added'] = date('Y-m-d');
        $data['status'] = 1;
        $this->common_model->commonRecordCreate('HR_emp_position', $data);
    }
}

       
     
        
    }
    
     function addPayrollType(){
     $data = []; 
    // fetch all existing position , to compare & match if some position has been deleted in recent ajax post , if yes, than mark that id as deleted in Database
     $conditions = array('status' => '1', 'is_deleted' => '0','location_id' => $this->location_id);
     $fieldsToFetch = array('id');
     $positionConfigData = $this->common_model->fetchRecordsDynamically('HR_payroll_type',$fieldsToFetch,$conditions);
     if(isset($positionConfigData) && isset($_POST['id'])){
         $existingPids = array_column($positionConfigData,'id');
         $postedPids  = $_POST['id'];
         $positiontoDelete = array_diff($existingPids, $postedPids);
         if(isset($positiontoDelete) && !empty($positiontoDelete)){
       $this->common_model->commonBulkRecordDelete('HR_payroll_type',$positiontoDelete,'id');       
         }
        
     }
   $this->common_model->commonRecordDelete('HR_payroll_type','location_id',$this->location_id); 
   
     foreach($_POST['name'] as $key=> $position_name){
         if (isset($_POST['position_id'][$key])) { 
         $position_id = $_POST['position_id'][$key];
         $data['name'] = $position_name; 
       $this->common_model->commonRecordUpdate('HR_payroll_type','id',$position_id,$data);
         }else{
      $data['location_id'] = $this->location_id; $data['date_added'] = date('Y-m-d');$data['status'] = 1; $data['name'] = $position_name;
      $this->common_model->commonRecordCreate('HR_payroll_type',$data);      
         }
         
     }
       echo "success";
     
        
    }
    
      public function saveGeneralConfig() {
        $response = ['status' => 'error', 'message' => 'Invalid request'];
        
        if($this->input->is_ajax_request()) {
            $config_key = $this->input->post('config_key');
            $value = $this->input->post('value');

            if($config_key && isset($value)) {
                // Prepare data to save
                $data_to_save = ['value' => $value];

                // Check if config exists
                $existing = $this->common_model->fetchRecordsDynamically('HR_configuration',['id'],['configureFor' => $config_key,'location' => $this->location_id]);
                    
                $save_data = [
                    'configureFor' => $config_key,
                    'data' => json_encode($data_to_save),
                    'location' => $this->location_id,
                    'metaData' => 'Enable face verification and roster settings',
                    'created_date' => date('Y-m-d H:i:s')
                ];
                
                if (isset($existing[0]['id'])) {
                 $this->common_model->commonRecordUpdate('HR_configuration', 'id', $existing[0]['id'], $save_data);
                } else {
                $this->common_model->commonRecordCreate('HR_configuration',$save_data);
                   
                }
                
                $response = ['status' => 'success', 'message' => 'Configuration saved'];
            }
        }
        
        echo json_encode($response);
        exit;
    } 
    
    // Save show/hide state for an individual onboarding form tab
    public function saveOnboardingTabConfig() {
        $response = ['status' => 'error', 'message' => 'Invalid request'];

        if($this->input->is_ajax_request()) {
            $tab_key = $this->input->post('tab_key');
            $value   = $this->input->post('value');
            $allowed = ['personalDetails','emergencyDetails','bankDetails','policeClearance','taxDetails','superAnnuation','privacyPolicy'];

            if($tab_key && in_array($tab_key, $allowed, true) && isset($value)) {
                $existing = $this->common_model->fetchRecordsDynamically('HR_configuration', ['id','data'], ['configureFor' => 'onboarding_tabs','location' => $this->location_id]);

                $config = [];
                if(isset($existing[0]['data']) && $existing[0]['data'] != '') {
                    $decoded = json_decode($existing[0]['data'], true);
                    if(is_array($decoded)) { $config = $decoded; }
                }

                // Make sure all known tabs are present (default enabled)
                foreach($allowed as $tk) {
                    if(!isset($config[$tk])) { $config[$tk] = '1'; }
                }

                $config[$tab_key] = ($value == '1' || $value === 1) ? '1' : '0';

                $save_data = [
                    'configureFor' => 'onboarding_tabs',
                    'data'         => json_encode($config),
                    'location'     => $this->location_id,
                    'metaData'     => 'Onboarding Form Tab Customization',
                    'created_date' => date('Y-m-d H:i:s')
                ];

                if(isset($existing[0]['id'])) {
                    $this->common_model->commonRecordUpdate('HR_configuration', 'id', $existing[0]['id'], $save_data);
                } else {
                    $this->common_model->commonRecordCreate('HR_configuration', $save_data);
                }

                $response = ['status' => 'success', 'message' => 'Configuration saved'];
            }
        }

        echo json_encode($response);
        exit;
    }
    

public function saveSuperannuationSettings() {
    $response = ['status' => 'error', 'message' => 'Invalid request'];
    
    if($this->input->post()) {
        $super_percentage = $this->input->post('super_percentage');
        $enable_tier_payroll = $this->input->post('enable_tier_payroll') ? '1' : '0';
        $payroll_tax_rate = $this->input->post('payroll_tax_rate');
        $accounting_software = $this->input->post('accounting_software');
        
        // Prepare data to save
        $data_to_save = [
            'super_percentage' => $super_percentage,
            'enable_tier_payroll' => $enable_tier_payroll,
            'payroll_tax_rate' => $payroll_tax_rate,
            'accounting_software' => $accounting_software,
            'public_holidays' => $this->input->post('public_holidays'),
            'holiday_state' => $this->input->post('holiday_state'),
            'holiday_year' => $this->input->post('holiday_year'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        
        // Check if config exists
        $existing = $this->common_model->fetchRecordsDynamically(
            'HR_configuration',
            ['id'],
            [
                'configureFor' => 'superannuation',
                'location' => $this->location_id
            ]
        );
        
        $save_data = [
            'configureFor' => 'superannuation',
            'data' => json_encode($data_to_save),
            'location' => $this->location_id,
            'metaData' => 'Superannuation Settings',
            'created_date' => date('Y-m-d H:i:s')
        ];
        
        if (isset($existing[0]['id'])) {
            $this->common_model->commonRecordUpdate('HR_configuration', 'id', $existing[0]['id'], $save_data);
        } else {
            $this->common_model->commonRecordCreate('HR_configuration', $save_data);
        }
        
        $response = ['status' => 'success', 'message' => 'Settings saved successfully'];
    }
    
    echo json_encode($response);
    exit;
}
    
}

