<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends MY_Controller {

    function __construct() {
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
       
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // $this->load->helper('notification');
		$this->load->model('employee_model');
		$this->load->model('auth_model');
	    $this->load->model('common_model');
        $this->location_id = $this->session->userdata('location_id') ? $this->session->userdata('location_id') : ($this->session->userdata('User_location_ids') ? $this->session->userdata('User_location_ids')[0] : null);
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        $this->roleId = get_logged_in_user_role($this->ion_auth,'id');
         $user_id = $this->ion_auth->user()->row()->id;
        $empData = $this->common_model->fetchRecordsDynamically('HR_employee', ['emp_id'], ['userId'=>$user_id]);
        $this->empId = (isset($empData[0]['emp_id']) ? $empData[0]['emp_id'] : ''); // incase of superadmin 
        // $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
     
    }
    
    // inserting fake/duumy employees
    
     public function seed()
    {
        $names = [
    'John', 'Kevin', 'Alice', 'Sophie', 'Michael', 'Emma', 'Daniel', 'Olivia', 'James', 'Ava',
    'David', 'Grace', 'Chris', 'Chloe', 'Ethan', 'Zoe', 'Liam', 'Ella', 'Ryan', 'Lily',
    'Noah', 'Mia', 'William', 'Isabella', 'Alexander', 'Sophia', 'Benjamin', 'Charlotte',
    'Henry', 'Amelia', 'Samuel', 'Harper', 'Thomas', 'Evelyn', 'Jack', 'Aria', 'Lucas',
    'Scarlett', 'Mason', 'Luna', 'Jacob', 'Abigail', 'Oliver', 'Emily', 'Elijah', 'Madison',
    'Logan', 'Avery', 'Jackson', 'Sofia'
];

        $created = 0;
        foreach ($names as $i => $name) {
            $email = strtolower($name) . ($i+1) . '@example.com';
            $password = $email . '#123!Allowed!';
            $additional_data = [
                'first_name' => $name,
                'last_name' => 'Doe'
            ];
            $group = [2]; // assuming group_id 2 is a standard employee group

            $user_id = $this->ion_auth->register($email, $password, $email, $additional_data, $group);

            if ($user_id) {
                $employee_data = [
                    'userId' => $user_id,
                    'first_name' => $name,
                    'preferred_name' => $name,
                    'last_name' => 'Doe',
                    'onboarding_status' => 1,
                    'emp_availability' => 'Full Time',
                    'employee_type' => 'Permanent',
                    'email' => $email,
                    'phone' => '9056101' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'pin' => hash('sha256', '2580'),
                    'dob' => '1996-03-23',
                    'effective_start_date' => '2025-06-01',
                    'location_id' => 13,
                    'bank_1' => 'State Bank',
                    'bsb_1' => '0987',
                    'account_no_1' => 'A12K34' . $i,
                    'percentage_1' => 100,
                    'account_name_1' => $name . ' Doe',
                    'nextkin_name_one' => 'Kin One',
                    'nextkin_email_one' => 'kin' . $i . '@example.com',
                    'nextkin_phone_no' => '8765432' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'nextkin_relationship_one' => 'Sibling',
                    'title' => 'Mr',
                    'unit_number' => '12',
                    'street_name' => 'gghh',
                    'street' => 'wdd',
                    'suburb' => 'MEl',
                    'postcode' => '1234',
                    'state' => 'act',
                    'stress_profile' => '2',
                    'tfn_number' => '00998877',
                    'super_fund_name' => 'hgchh',
                    'heighest_acd_achmts' => 'BCA',
                    'visa_status' => 'eyj',
                    'agree_terms_one' => 1,
                    'agree_terms_two' => 1,
                    'agree_terms_three' => 1,
                    'status' => '1',
                    'created_at' => date('Y-m-d')
                ];
               $empid =  $this->common_model->commonRecordCreate('HR_employee',$employee_data);
               $emplocData['location_id'] = 13; $emplocData['empId'] = $empid;
               $this->common_model->commonRecordCreate('HR_empIdToLocationId',$emplocData);
               $empposData['position_id'] = 1; $empposData['rate'] ='12';
               $empposData['emp_id'] = $empid; 
                $this->common_model->commonRecordCreate('HR_emp_to_position',$empposData);
               if($empid){
               $created++;    
               }else{
                   break;
               }
                
            }
        }

        echo "<h3>Successfully created $created dummy users and employees.</h3>";
    }
    
    public function employeeList(){ 
        
	  $userId = $this->ion_auth->get_user_id();
	  
	  $locationId = $this->location_id ?? 0;
	  $data['prepAreaLists'] = $this->common_model->fetchRecordsDynamically('HR_prepArea', '', ['location_id' => $locationId]) ?? [];
	  
	  if($this->roleId == 4){
	    // for employee logins only
	  $conditions = array('userId'=>$userId); $fields = ['emp_id'];
	  
	  $empData = $this->common_model->fetchRecordsDynamically('HR_employee', $fields, $conditions); 
	   
	  $this->editEmployee($empData[0]['emp_id']);
	  }else{
	      // for admin,manager,staff login
	  $data['locations'] = $this->auth_model->fetchLocationsFromUserId($userId);
	  $data['empLists'] = $this->employee_model->employeeList('','0'); // active employees list
	  $data['inActiveEmpLists'] = $this->employee_model->employeeList('1','0'); // Inactive employees list
	  $data['activeContractorLists'] = $this->employee_model->employeeList('','1'); 
	  $data['inactiveContractorLists'] = $this->employee_model->employeeList('1','1');
// 	  echo "<pre>"; print_r($data['activeContractorLists']); exit;
	 
      $this->load->view('general/header');
	  $this->load->view('employee/employeeList',$data);
	  $this->load->view('general/footer');    
	  }
	  
	}
	
	 
	
	public function editEmployee($empId = '')
   {
    // ---------- Defaults ----------
    $data = [];
    $empId = !empty($empId) ? $empId : ($this->empId ?? null);

    // ---------- Guard: empId ----------
    if (empty($empId)) {
        // Fail silently – no errors, no notices
        redirect('dashboard');
        return;
    }

    // ---------- Conditions ----------
    $conditions              = ['emp_id' => $empId];
    $conditionsLeave         = ['status' => '1'];
    $conditionsStress        = ['status' => '1', 'is_deleted' => '0'];
    $conditionsAvail         = ['emp_id' => $empId, 'is_deleted' => '0'];
    $locationId              = $this->location_id ?? 0;

    // ---------- Employee Data ----------
    $empData = $this->employee_model->employeeDetails($empId);
    $employee = (is_array($empData) && !empty($empData[0])) ? $empData[0] : [];

    // ---------- Leaves ----------
    $leaveData = $this->common_model
        ->fetchRecordsDynamically('HR_leaves', '', $conditionsLeave) ?? [];

    // ---------- Assign Safe Defaults ----------
    $data['empPositionAndRatesData'] = $this->common_model
        ->fetchRecordsDynamically('HR_emp_to_position', '', $conditions) ?? [];

    $data['availability'] = $this->common_model
        ->fetchRecordsDynamically('HR_emp_availability', '', $conditionsAvail) ?? [];

    $data['leaveRequestsData'] = $this->employee_model
        ->fetchLeaveRequestsRecord($empId, 'past') ?? [];

    $data['upcomingLeaveData'] = $this->employee_model
        ->fetchLeaveRequestsRecord($empId, 'upcoming') ?? [];

    $data['countOfLeaves'] = $this->employee_model
        ->fetchCountOfLeaves() ?? 0;

    $data['stressProfiles'] = $this->common_model
        ->fetchRecordsDynamically('HR_stressProfile', '', $conditionsStress) ?? [];

    $data['positions'] = $this->common_model
        ->fetchRecordsDynamically('HR_emp_position', '', $conditionsStress) ?? [];

    $data['payrollTypes'] = $this->common_model
        ->fetchRecordsDynamically(
            'HR_payroll_type',
            ['id', 'name'],
            ['is_deleted' => 0, 'location_id' => $locationId]
        ) ?? [];

    // ---------- Locations ----------
    $locationIdsArray = [];

    if (!empty($employee['location_ids'])) {
        $unserialized = @unserialize($employee['location_ids']);
        if (is_array($unserialized)) {
            $locationIdsArray = $unserialized;
        }
    }

    $data['locationNames'] = function_exists('fetchLocationNamesFromIds')
        ? fetchLocationNamesFromIds($locationIdsArray)
        : [];

    // ---------- Final Assignments ----------
    $data['employee']   = $employee;
    $data['leaveTypes'] = $leaveData;

    // ---------- Onboarding form tab visibility ----------
    // Controlled per location from HR > Settings > Onboarding Form Customize
    // (configureFor = 'onboarding_tabs'). Same config the External onboarding
    // form uses, so the edit-employee and employee-profile pages stay in sync.
    $onboardingTabsCfg = $this->common_model
        ->fetchRecordsDynamically('HR_configuration', ['data'], ['location' => $locationId, 'configureFor' => 'onboarding_tabs']) ?? [];
    $onbTabsDecoded = (isset($onboardingTabsCfg[0]['data']) && $onboardingTabsCfg[0]['data'] != '')
        ? json_decode($onboardingTabsCfg[0]['data'], true) : [];
    $data['onboardingTabsConfig'] = is_array($onbTabsDecoded) ? $onbTabsDecoded : [];
    
    // echo "<pre>"; print_r($employee); exit;

    // ---------- Prep Areas ----------
    $data['prepAreaLists'] = $this->common_model
        ->fetchRecordsDynamically(
            'HR_prepArea',
            '',
            ['location_id' => $locationId]
        ) ?? [];

    // ---------- Views ----------
    $this->load->view('general/header');
    $this->load->view('employee/editEmployee', $data);
    $this->load->view('general/footer');
}

	function employeeCommonUpdate($empId,$data){
	   // echo "<pre>"; print_r($data); exit;
	 $this->common_model->commonRecordUpdate('HR_employee','emp_id',$empId,$data);     
	}
	function employeeDelete(){
	   $data['is_deleted'] = $_POST['is_deleted']; 
	   $empId = $_POST['emp_id'];
	   $userId = $_POST['user_id'];
	   $this->common_model->commonRecordUpdate('HR_employee','emp_id',$empId,$data);
	   if(isset($_POST['is_deleted']) && $_POST['is_deleted'] == 1){
	    $data['deleted_at'] =   date('Y-m-d');
	   }else{
	    $data['date_modified'] =  date('Y-m-d');
	   }

	   $this->common_model->commonRecordDelete('Global_users',$userId,'id');
	   echo "Success";
	} 
	function employeeStatusUpdate(){
	  $dataToUpdate['status'] = $_POST['status'];
	  $this->employeeCommonUpdate($_POST['id'],$dataToUpdate);
	  $data['active'] = $_POST['status']; 
	  $this->common_model->commonRecordUpdate('Global_users','id',$_POST['user_id'],$data);
	   echo "Success";
	}
	
	function onboardNewEmployee(){
	  
	    if (!empty($_FILES['userfile']['name'])){
	         $uploadPath='./uploaded_files/'.$this->tenantIdentifier.'/HR/JobDescr';
             $jobDescrFilename = $this->common_model->uploadAttachment($_FILES,$uploadPath);
            }else{
              $jobDescrFilename = '';  
            }
	  
	   $data=array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'email' => $this->input->post('email'),
			'userId' => $this->input->post('userId'),
			'location_id' => $this->location_id,
			 'tier' => $this->input->post('tier') ? $this->input->post('tier') : null,
			'emp_prep_area' => $this->input->post('emp_prep_area') ? $this->input->post('emp_prep_area') : null,
			'employee_type' => $this->input->post('employee_type') ? $this->input->post('employee_type') : null,
			'location_ids' => serialize($this->input->post('locationIds')),
			'status' => 1,
			'job_desc' => $jobDescrFilename,
			'created_at' => date("Y-m-d"),
			'date_modified' => date("Y-m-d")
			);
	 
	 $emp_id = $this->employee_model->onboardNewEmployee($data); 
	 // if an employee belongs to multiple locations
	 if(isset($_POST['locationIds']) && !empty($_POST['locationIds'])){
	 foreach($_POST['locationIds'] as $locationId){
	  $locData = array(
			'location_id' => $locationId,
			'empId' => $emp_id,
			);
      $this->employee_model->allocateLocationToEmployee($locData);  			
	     }
	 }
	 if($this->input->post('type') =='onboard'){
	     // only send ponboarding email if onboard button has been clicked
	 $this->sendOnboardingEmail($emp_id,$_POST);   
	 
	 $statusUpdate = array('onboarding_status' => 1); // Status 1 onboarding email sent
	 $this->common_model->commonRecordUpdate('HR_employee','emp_id',$emp_id,$statusUpdate);
	 
	 }else{
	     
	     $response = [
    'emp_id' => $emp_id,
    'mailStatus' => [
        'status' => 'success',
        'error'  =>  ''
    ]
];


    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
        
	 }
	

	}
	
/**
 * Send the onboarding e-mail and return emp_id + mail status to Ajax.
 *
 * @param int $emp_id
 * @return void  (outputs JSON)
 */
  public function sendOnboardingEmail($emp_id, $formData = [])
  {
    // ---------------------------------------------------------
    // 0. Ensure SMTP settings are loaded for THIS request.
    //    The onboarding email is triggered from the employee list
    //    (AJAX). SMTP session data is normally primed only when the
    //    user opens the HR dashboard, so when it is missing/stale the
    //    sendEmail() helper silently falls back to PHP mail() which
    //    returns success on the server but never delivers. Load the
    //    settings fresh from the DB here so the SMTP path is always used.
    // ---------------------------------------------------------
    $this->primeSmtpSettings();

    // ---------------------------------------------------------
    $dataToEncrypt = [
        'location_id'       => $this->location_id,
        'tenantIdentifier'  => $this->tenantIdentifier,
        'empId'             => $emp_id,
        'mail_from'         => $this->session->userdata('mail_from'),
        'username'          => $this->session->userdata('username'),
        'mail_protocol'     => $this->session->userdata('mail_protocol'),
        'smtp_host'         => $this->session->userdata('smtp_host'),
        'smtp_port'         => $this->session->userdata('smtp_port'),
        'smtp_username'     => $this->session->userdata('smtp_username'),
        'smtp_pass'         => $this->session->userdata('smtp_pass'),
    ];

    // Organisation name (from the global organization_list) so the onboarding
    // email can be branded with the actual business name instead of "Bizadmin".
    $orgRow = $this->db->query(
        "SELECT orz_name FROM organization_list WHERE tenant_identifier = ?",
        array($this->tenantIdentifier)
    )->row();
    $dataToEncrypt['orz_name'] = (!empty($orgRow) && !empty($orgRow->orz_name)) ? $orgRow->orz_name : 'Bizadmin';

  
    $locationNamesList = [];

    if (!empty($formData['locationIds'])) {

        $locationIdsArray = $formData['locationIds'];  // Correct variable!

        // Fetch location names using helper
        $locationAssocArray = fetchLocationNamesFromIds($locationIdsArray, false);  
        // Example result: [10 => "Bendigo", 12 => "Werribee"]

        // Convert to simple array of names
        $locationNamesList = array_values($locationAssocArray);

        // Also add to encryption payload:
        $dataToEncrypt['location_names'] = $locationNamesList;
    }

   
    $encryptedData = $this->encryption->encrypt(json_encode($dataToEncrypt));

    $Maildata['onboardingUrl'] = base_url() . 'External/HR/onboardingForm/' 
        . urlencode(urlencode(urlencode($encryptedData)));

 
    $Maildata['employeeName'] = $this->input->post('first_name');

    // Send list of locations to email template
    $Maildata['locationNamesList'] = $locationNamesList;  

    // Company (organisation) name + a readable location label for the email
    // subject and greeting.
    $Maildata['orgName'] = $dataToEncrypt['orz_name'];
    $locationLabel = !empty($locationNamesList) ? implode(', ', $locationNamesList) : '';
    $Maildata['locationLabel'] = $locationLabel;

   
    $mailContent = $this->load->view('emails/onboardingEmail', $Maildata, TRUE);

  
    $emailSendTo = $this->input->post('email');

    $onboardingSubject = 'Complete Your Employee Onboarding - ' . $Maildata['orgName']
        . ($locationLabel !== '' ? ' ' . $locationLabel : '');

   $mailStatus = $this->sendEmail(
    $emailSendTo,
    $onboardingSubject,
    $mailContent,
    $this->session->userdata('mail_from')
);

// Log the outcome so delivery problems are visible on the server.
log_message(
    'error',
    'Onboarding email | emp_id=' . $emp_id
    . ' | to=' . $emailSendTo
    . ' | from=' . $this->session->userdata('mail_from')
    . ' | protocol=' . $this->session->userdata('mail_protocol')
    . ' | status=' . var_export($mailStatus['status'], true)
    . ' | error=' . (isset($mailStatus['error']) ? $mailStatus['error'] : '')
);

// ---------------------------------------------------------
// 7. AJAX RESPONSE
// ---------------------------------------------------------
$response = [
    'emp_id' => $emp_id,
    'mailStatus' => [
        'status' => $mailStatus['status'],
        'error'  => isset($mailStatus['error']) ? $mailStatus['error'] : ''
    ]
];


    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
}

/**
 * Load SMTP settings into the session for the current request.
 *
 * Mirrors the logic in HR\Home::index so that emails triggered outside
 * the dashboard (e.g. onboarding from the employee list) always use the
 * configured SMTP server instead of silently falling back to PHP mail().
 *
 * @return void
 */
  private function primeSmtpSettings()
  {
    $this->load->model('general_model');

    $system_id = $this->session->userdata('system_id');

    $emailSettings = $this->general_model->fetchSmtpSettings(
        $this->location_id ?? '',
        $system_id
    );

    if (empty($emailSettings)) {
        // Fallback backup SMTP record (location_id / system_id = 9999).
        $emailSettings = $this->general_model->fetchSmtpSettings('9999', '9999');
    }

    if (is_object($emailSettings)) {
        if (isset($emailSettings->mail_protocol) && $emailSettings->mail_protocol === 'smtp') {
            $this->session->set_userdata('mail_protocol', 'smtp');
            $this->configureSMTP($emailSettings);
        }

        $this->session->set_userdata(
            'mail_from',
            !empty($emailSettings->mail_from) ? $emailSettings->mail_from : 'info@bizadmin.com.au'
        );
    } else {
        $this->session->set_userdata('mail_from', 'info@bizadmin.com.au');
    }
  }

	
	function submitOnboardingProcessForEmployee(){
	    $empId = $this->input->post('emp_id');
	    
	    $positionIdsToRemove = json_decode($this->input->post('positionIdToRemove'));
        if (is_array($positionIdsToRemove) && !empty($positionIdsToRemove)) {
            $this->common_model->commonBulkRecordDelete('HR_emp_to_position',$positionIdsToRemove,'id');
        }
	   
        if (!empty($_FILES['userfile']['name'])){
        $uploadPath='./uploaded_files/'.$this->tenantIdentifier.'/HR/OnboardingFiles';
        $filename = $this->common_model->uploadAttachment($_FILES,$uploadPath);    
        $data_user['police_certificate'] = $filename;
        }
        $posted_data = $this->input->post();
        
        // Handle password update separately for Ion Auth
        $password = $this->input->post('password');
        if (!empty($password)) {
            // Get the userId from employee record
            $employee = $this->common_model->fetchRecordsDynamically('HR_employee', ['userId'], ['emp_id' => $empId]);
            if (!empty($employee) && isset($employee[0]['userId'])) {
                $userId = $employee[0]['userId'];
                // Update password using Ion Auth
                $this->ion_auth->update($userId, ['password' => $password]);
            }
        }
        
        // NOTE :  please add new index in below array for fiels added to employee form to execulde , if its name not matching with hr_employee table column
        
        $excludedValues = array('rate','Saturday_rate','Sunday_rate','holiday_rate','uniform_allowance','early_start','late_night','position_id','position_unique_id','positionIdToRemove','payroll_type_id','created_at','password');
        foreach($posted_data as $key=> $value){
        ($value !='' && !in_array($key,$excludedValues) ? $data_user[$key] = $value : '');   
        }
        
        
        
         $positionresponse = array();
         if(isset($_POST['position_id']) && !empty($_POST['position_id'])){
           foreach($_POST['position_id'] as $index => $positionid){
           $positionRatesData = array();
           $positionRatesData['emp_id'] = $_POST['emp_id'];
           $positionRatesData['position_id'] = $positionid;
           $positionRatesData['rate'] = isset($_POST['rate'][$index]) ? $_POST['rate'][$index] : 0;
           $positionRatesData['Saturday_rate'] = isset($_POST['Saturday_rate'][$index]) ? $_POST['Saturday_rate'][$index] : 0;
           $positionRatesData['Sunday_rate'] = isset($_POST['Sunday_rate'][$index]) ? $_POST['Sunday_rate'][$index] : 0;
           $positionRatesData['holiday_rate'] = isset($_POST['holiday_rate'][$index]) ? $_POST['holiday_rate'][$index] : 0;
           $positionRatesData['uniform_allowance'] = isset($_POST['uniform_allowance'][$index]) ? $_POST['uniform_allowance'][$index] : 0;
           $positionRatesData['early_start'] = isset($_POST['early_start'][$index]) ? $_POST['early_start'][$index] : 0;
           $positionRatesData['late_night'] = isset($_POST['late_night'][$index]) ? $_POST['late_night'][$index] : 0;
           $positionRatesData['payroll_type_id'] = isset($_POST['payroll_type_id'][$index]) ? $_POST['payroll_type_id'][$index] : null;
          
           
           if(isset($_POST['position_unique_id'][$index])){
            $this->common_model->commonRecordUpdate('HR_emp_to_position','id',$_POST['position_unique_id'][$index],$positionRatesData);   
            $uniqueId = $_POST['position_unique_id'][$index];
           }else{
            $uniqueId = $this->common_model->commonRecordCreate('HR_emp_to_position',$positionRatesData);     
           }
           
          
           $positionResponse = array();
           $positionResponse['id'] = $uniqueId;
           $positionResponse['position_id'] = $positionid;
    
           $positionResponses[] = $positionResponse;
          
        } 
        $response['positionAddedDetails'] = $positionResponses;
         }
        
       
        $data_user['date_modified'] = date("Y-m-d");
        // $data_user['onboarding_status'] = 1;
        $this->employeeCommonUpdate($empId,$data_user);
        $response['status'] = 'success';
		$response['message'] = 'success';
		
		echo json_encode($response);   
	}
	
	
	
	// save_availability : To record Employee Availability for each day
	
public function save_availability()
{
    $emp_id      = $this->input->post('emp_id');
    $same_hours  = $this->input->post('same_hours') ? 1 : 0;
    $location_id = $this->location_id;

    if (!$emp_id) {
        echo json_encode(["status" => "error", "message" => "Invalid employee"]);
        return;
    }

    // All week days
    $days = ["mon","tue","wed","thu","fri","sat","sun"];

    // Weekly structure posted
    $weekly = $this->input->post('weekly');
    if (!is_array($weekly)) {
        $weekly = [];
    }

    // SAME HOURS MODE: override weekly array
    if ($same_hours == 1) {

        $start = $this->input->post('same_start');
        $end   = $this->input->post('same_end');

        foreach ($days as $d) {
            $weekly[$d] = [
                "start" => $start ?: "",
                "end"   => $end   ?: ""
            ];
        }

    } else {
        // Ensure all days exist
        foreach ($days as $d) {
            $weekly[$d]["start"] = trim($weekly[$d]["start"] ?? "");
            $weekly[$d]["end"]   = trim($weekly[$d]["end"] ?? "");
        }
    }

    // Encode JSON
    $weekly_json = json_encode($weekly);

    // Prepare save row
    $saveData = [
        "emp_id"       => $emp_id,
        "weekly_json"  => $weekly_json,
        "same_hours"   => $same_hours,
        "location_id"  => $location_id,
        "updated_at"   => date('Y-m-d H:i:s'),
        "is_deleted"   => 0
    ];

    // Check existing
    $existing = $this->common_model->fetchRecordsDynamically(
        "HR_emp_availability",
        "",
        ["emp_id" => $emp_id, "is_deleted" => 0]
    );

    if (empty($existing)) {
        $this->common_model->commonRecordCreate("HR_emp_availability", $saveData);
    } else {
        $this->common_model->commonRecordUpdate(
            "HR_emp_availability",
            "emp_availability_id",
            $existing[0]["emp_availability_id"],
            $saveData
        );
    }

    // Log changes
    $logData = [
        "emp_id"      => $emp_id,
        "location_id" => $location_id,
        "weekly_json" => $weekly_json,
        "same_hours"  => $same_hours,
        "ip_address"  => $this->input->ip_address(),
        "user_agent"  => $this->input->user_agent(),
        "created_at"  => date("Y-m-d H:i:s")
    ];

    $this->common_model->commonRecordCreate("HR_employee_unavailability_logs", $logData);

    echo json_encode([
        "status"  => "success",
        "message" => "Availability updated successfully"
    ]);
}


public function get_availability_for_day()
{
    $emp_id = $this->input->post("emp_id");
    $dayKey = $this->input->post("day_key"); // mon/tue/wed...
    $location_id = $this->location_id;

    // Fetch availability record
    $result = $this->common_model->fetchRecordsDynamically(
        "HR_emp_availability",
        "",
        ["emp_id" => $emp_id, "is_deleted" => 0]
    );

    if (empty($result)) {
        echo json_encode([
            "available" => false,
            "message" => "No availability record found."
        ]);
        return;
    }

    $row = $result[0];
    $weekly = json_decode($row["weekly_json"], true);
    $same = intval($row["same_hours"]) === 1;

    // Determine availability
    if ($same) {
        $start = $weekly["mon"]["start"] ?? "";
        $end   = $weekly["mon"]["end"] ?? "";
    } else {
        $start = $weekly[$dayKey]["start"] ?? "";
        $end   = $weekly[$dayKey]["end"] ?? "";
    }

    // No hours set → unavailable
    $available = (!empty($start) && !empty($end));

    echo json_encode([
        "available" => $available,
        "start" => $start ?: "",
        "end" => $end ?: "",
        "same_hours" => $same
    ]);
}


// importing employee from cafeadmin , please delete this code later


public function importEmployees() {
    

$this->load->view('general/header');
    $this->load->view('employee/importEmployees');
    $this->load->view('general/footer');
    
}

/**
 * Import Employees from CSV
 * This method handles CSV file upload and imports employee data from old system to new system
 * 
 * Flow:
 * 1. Create records in Global_users table
 * 2. Populate Global_users_to_location (location_id = 11)
 * 3. Populate Global_userid_to_roles (role_id = 4)
 * 4. Enter details in HR_employee table
 * 5. Enter employee hourly rate in HR_emp_to_position (position_id = 5)
 * 6. Enter data in HR_empIdToLocationId (location_id = 11)
 */
public function importEmployeesFromCSV()
{
    // Check if file is uploaded
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != 0) {
        $response = array(
            'status' => 'error',
            'message' => 'Please upload a valid CSV file'
        );
        echo json_encode($response);
        return;
    }

    $file = $_FILES['csv_file']['tmp_name'];
    
    try {
        // Load PhpSpreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Get headers from first row
        $headers = array_shift($rows);
        
        // Track import statistics
        $stats = array(
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => array()
        );
        
        // Process each employee row
        foreach ($rows as $index => $row) {
            $stats['total']++;
            $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
            
            // Create associative array from row
            $employeeData = array_combine($headers, $row);
            
            // Skip empty rows or rows without email
            if (empty($employeeData['email']) || trim($employeeData['email']) == '') {
                continue;
            }
            
            // Start transaction for this employee
            $this->tenantDb->trans_start();
            
            try {
                // STEP 1: Create user in Global_users table
                $userId = $this->createGlobalUser($employeeData);
                
                
                if (!$userId) {
                    throw new Exception("Failed to create global user for email: " . $employeeData['email']);
                }
                
                // STEP 2: Add to Global_users_to_location (location_id = 11)
                $this->addUserToLocation($userId, 11);
                
                // STEP 3: Add to Global_userid_to_roles (role_id = 4 for employees)
                $this->addUserToRole($userId, 4);
                
                // STEP 4: Create HR_employee record
                $empId = $this->createHREmployee($employeeData, $userId);
                
                if (!$empId) {
                    throw new Exception("Failed to create HR employee record for email: " . $employeeData['email']);
                }
                
                // STEP 5: Add position and rate to HR_emp_to_position (position_id = 5)
                $this->addEmployeePosition($empId, $employeeData);
                
                // STEP 6: Add to HR_empIdToLocationId (location_id = 11)
                $this->addEmployeeToLocation($empId, 11);
                
                // Commit transaction
                $this->tenantDb->trans_complete();
                
                if ($this->tenantDb->trans_status() === FALSE) {
                    throw new Exception("Transaction failed for employee: " . $employeeData['email']);
                }
                
                $stats['success']++;
                
            } catch (Exception $e) {
                $this->tenantDb->trans_rollback();
                $stats['failed']++;
                $stats['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }
        
        // Prepare response
        $response = array(
            'status' => 'success',
            'message' => "Import completed. Success: {$stats['success']}, Failed: {$stats['failed']}, Total: {$stats['total']}",
            'stats' => $stats
        );
        
    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => 'Error processing CSV: ' . $e->getMessage()
        );
    }
    
    echo json_encode($response);
}

/**
 * Create user in Global_users table
 * 
 * @param array $data Employee data from CSV
 * @return int|bool User ID or false on failure
 */
private function createGlobalUser($data)
{
    // Generate a random password (will be reset by user during onboarding)
    $randomPassword = bin2hex(random_bytes(16));
    
    $userData = array(
        'ip_address' => '',
        'first_name' => trim($data['first_name']),
        'last_name' => isset($data['last_name']) ? trim($data['last_name']) : '',
        'username' => $this->generateUsername($data['email']),
        'email' => trim($data['email']),
        'phone' => isset($data['phone']) ? trim($data['phone']) : '',
        'company' => $this->session->userdata('company') ? $this->session->userdata('company') : 11,
        'password' => password_hash($randomPassword, PASSWORD_ARGON2I),
        'role_id' => 4, // Employee role
        'location_ids' => serialize(array(11)), // Serialize location array
        'system_ids' => serialize(array('101', '109', '110')), // Default system IDs for employees
        'created_on' => time(),
        'active' => 1,
        'is_deleted' => 0,
        'date_modified' => date('Y-m-d'),
        'overwriteRoleLevelMenu' => 0
    );
    
    // Add PIN if available
    if (isset($data['pin']) && !empty($data['pin'])) {
        $userData['pin'] = trim($data['pin']);
    }
    
    // Check if user already exists
    $existingUser = $this->tenantDb->get_where('Global_users', array('email' => $userData['email']))->row();
    
    if ($existingUser) {
        // Return existing user ID
        return $existingUser->id;
    }
    
    // Insert user
    $this->tenantDb->insert('Global_users', $userData);
    return $this->tenantDb->insert_id();
}

/**
 * Add user to Global_users_to_location table
 * 
 * @param int $userId
 * @param int $locationId
 * @return bool
 */
private function addUserToLocation($userId, $locationId)
{
    // Check if already exists
    $exists = $this->tenantDb->get_where('Global_users_to_location', array(
        'user_id' => $userId,
        'location_id' => $locationId
    ))->row();
    
    if ($exists) {
        return true;
    }
    
    $data = array(
        'user_id' => $userId,
        'location_id' => $locationId
    );
    
    return $this->tenantDb->insert('Global_users_to_location', $data);
}

/**
 * Add user to Global_userid_to_roles table
 * 
 * @param int $userId
 * @param int $roleId
 * @return bool
 */
private function addUserToRole($userId, $roleId)
{
    // Check if already exists
    $exists = $this->tenantDb->get_where('Global_userid_to_roles', array(
        'user_id' => $userId,
        'group_id' => $roleId
    ))->row();
    
    if ($exists) {
        return true;
    }
    
    $data = array(
        'user_id' => $userId,
        'group_id' => $roleId
    );
    
    return $this->tenantDb->insert('Global_userid_to_roles', $data);
}

/**
 * Create HR employee record
 * 
 * @param array $data Employee data from CSV
 * @param int $userId Global user ID
 * @return int|bool Employee ID or false on failure
 */
private function createHREmployee($data, $userId)
{
    // Check if employee already exists
    $existingEmp = $this->tenantDb->get_where('HR_employee', array('email' => trim($data['email'])))->row();
    
    if ($existingEmp) {
        // Update userId if needed
        if ($existingEmp->userId != $userId) {
            $this->tenantDb->where('emp_id', $existingEmp->emp_id);
            $this->tenantDb->update('HR_employee', array('userId' => $userId));
        }
        return $existingEmp->emp_id;
    }
    
    // Prepare employee data
    $employeeData = array(
        'userId' => $userId,
        'onboarding_status' => isset($data['onboarding_status']) ? $data['onboarding_status'] : 0,
        'first_name' => trim($data['first_name']),
        'last_name' => isset($data['last_name']) ? trim($data['last_name']) : '',
        'email' => trim($data['email']),
        'phone' => isset($data['phone']) ? trim($data['phone']) : '',
        'employee_type' => isset($data['employee_type']) ? $data['employee_type'] : '',
        'location_id' => 11, // Fixed location
        'location_ids' => serialize(array(11)), // Serialize location array
        'status' => 1,
        'created_at' => date('Y-m-d'),
        'date_modified' => date('Y-m-d'),
        'is_deleted' => 0
    );
    
    // Add optional fields if they exist in CSV
    $optionalFields = array(
        'pin', 'preferred_name', 'emp_availability', 'title', 'name', 
        'dob', 'effective_start_date', 'unit_number', 'street_name', 'street',
        'suburb', 'postcode', 'state', 'tfn_number', 'super_fund_name', 
        'super_annuation_no', 'heighest_acd_achmts', 'visa_status',
        'nextkin_name_one', 'nextkin_email_one', 'nextkin_phone_no', 
        'nextkin_relationship_one', 'nextkin_name_two', 'nextkin_email_two',
        'nextkin_relationship_two', 'emergency_address',
        'bank_1', 'bsb_1', 'account_no_1', 'percentage_1', 'account_name_1',
        'bank_2', 'bsb_2', 'account_no_2', 'percentage_2', 'account_name_2',
        'bank_3', 'bsb_3', 'account_no_3', 'percentage_3', 'account_name_3',
        'police_certificate', 'medical_history', 'tax_declaration',
        'completed_super_annu', 'advice_of_tax_file', 'quality_assurance',
        'job_desc', 'tfn_type', 'previous_surname', 'have_surname_changed',
        'resident_type', 'loan_type', 'job_type', 'claim_tax_free',
        'visa_expiry', 'nominatedByEmployer'
    );
    
    foreach ($optionalFields as $field) {
        if (isset($data[$field]) && !empty($data[$field]) && $data[$field] != '0000-00-00') {
            $employeeData[$field] = trim($data[$field]);
        }
    }
    
    // Handle date fields properly
    $dateFields = array('dob', 'effective_start_date', 'fire_emg_completed_date', 
                       'oh_s_completed_date', 'visa_expiry');
    foreach ($dateFields as $dateField) {
        if (isset($data[$dateField]) && !empty($data[$dateField]) && 
            $data[$dateField] != '0000-00-00') {
            $employeeData[$dateField] = $data[$dateField];
        }
    }
    
    // Handle numeric fields
    $numericFields = array('early_start', 'late_night');
    foreach ($numericFields as $numField) {
        if (isset($data[$numField]) && is_numeric($data[$numField])) {
            $employeeData[$numField] = $data[$numField];
        }
    }
    
    // Insert employee
    $this->tenantDb->insert('HR_employee', $employeeData);
    return $this->tenantDb->insert_id();
}

/**
 * Add employee position and rates
 * 
 * @param int $empId Employee ID
 * @param array $data Employee data from CSV
 * @return bool
 */
private function addEmployeePosition($empId, $data)
{
    $positionId = 5; // Fixed position ID as per requirement
    
    // Check if position already exists for this employee
    $exists = $this->tenantDb->get_where('HR_emp_to_position', array(
        'emp_id' => $empId,
        'position_id' => $positionId
    ))->row();
    
    if ($exists) {
        return true; // Already exists
    }
    
    $positionData = array(
        'emp_id' => $empId,
        'position_id' => $positionId,
        'rate' => isset($data['rate']) && is_numeric($data['rate']) ? $data['rate'] : '0',
        'Saturday_rate' => isset($data['Saturday_rate']) && is_numeric($data['Saturday_rate']) ? $data['Saturday_rate'] : '0',
        'Sunday_rate' => isset($data['Sunday_rate']) && is_numeric($data['Sunday_rate']) ? $data['Sunday_rate'] : '0',
        'holiday_rate' => isset($data['holiday_rate']) && is_numeric($data['holiday_rate']) ? $data['holiday_rate'] : '0',
        'uniform_allowance' => isset($data['uniform_allowance']) && is_numeric($data['uniform_allowance']) ? $data['uniform_allowance'] : '0',
        'early_start' => isset($data['early_start']) && is_numeric($data['early_start']) ? $data['early_start'] : '0',
        'late_night' => isset($data['late_night']) && is_numeric($data['late_night']) ? $data['late_night'] : '0',
        'payroll_type_id' => null // Set to null or get from data if available
    );
    
    return $this->tenantDb->insert('HR_emp_to_position', $positionData);
}

/**
 * Add employee to location mapping
 * 
 * @param int $empId Employee ID
 * @param int $locationId Location ID
 * @return bool
 */
private function addEmployeeToLocation($empId, $locationId)
{
    // Check if already exists
    $exists = $this->tenantDb->get_where('HR_empIdToLocationId', array(
        'empId' => $empId,
        'location_id' => $locationId
    ))->row();
    
    if ($exists) {
        return true;
    }
    
    $data = array(
        'empId' => $empId,
        'location_id' => $locationId
    );
    
    return $this->tenantDb->insert('HR_empIdToLocationId', $data);
}

/**
 * Generate username from email
 * 
 * @param string $email
 * @return string
 */
private function generateUsername($email)
{
    $username = explode('@', $email)[0];
    $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
    
    // Check if username exists
    $exists = $this->tenantDb->get_where('Global_users', array('username' => $username))->row();
    
    if ($exists) {
        // Append random number
        $username .= rand(100, 999);
    }
    
    return strtolower($username);
}

/**
 * Display import view
 */
public function importView()
{
    $this->load->view('hr/import_employees');
}

	
	
}
