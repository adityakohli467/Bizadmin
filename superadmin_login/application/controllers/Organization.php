<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Organization extends MY_Controller {
    function __construct() {
		parent::__construct();
		$this->load->helper('custom');
		$this->load->model('organization_model');
		$this->load->model('location_model');
		$this->load->model('general_model');
		$this->load->model('onboarding_model');
	}
	public function connectToThisDB($dbDetails){
	    
	 $config = array(
    'hostname' => 'localhost',
    'username' => $dbDetails['db_username'],
    'password' => $dbDetails['db_pass'],
    'database' => $dbDetails['db_name'],
    'dbdriver' => 'mysqli',
    );
   return $new_db = $this->load->database($config, TRUE);  
   
	}
	public function populateClientDB($dataToInsert,$orzId,$hasedPassword=''){
	    
	   $newDBConn = $this->connectToThisDB($dataToInsert);
	   $data = array(
	   'id' => 1,
	   'role_id' => 1,
       'username' => $dataToInsert['tenant_identifier'],
       'first_name' => $dataToInsert['orz_name'],
       'email' => $dataToInsert['orz_email'],
       'phone' => $dataToInsert['orz_phone'],
       'password' => $hasedPassword,
       'company' => $orzId,
       'system_ids' => Serialize($dataToInsert['system_ids']),
	   'location_ids' => Serialize($dataToInsert['location_ids']),
       'active' => $dataToInsert['organization_list_status'],
       'created_on' => date('Y-m-d'),
       );
       $newDBConn->insert('Global_users', $data);
       $last_inserted_user_id = $newDBConn->insert_id();
     
    

      //group_id = 1,2,3,4 is for admin,manager and staff and employee role respecitvely,beacuse for all orz threee roles will by default created and mandotry as 1,2,3
      for($count = 1;$count<4;$count++){
       $roleData = array(
       'id' =>   $count,     
       'name' => ($count == 1 ? 'Admin' : ($count == 2 ? 'Manager' : ($count == 3 ? 'Employee' : 'Staff'))),
       'displayName' => ($count == 1 ? 'Admin' : ($count == 2 ? 'Manager' : 'Staff')),
       'status' => 1,
       'showSeprateChecklist' => ($count == 3 ? 1 : 0),
       'location_id' => 0
       );
      $newDBConn->insert('Global_roles', $roleData);
      if($count == 1){
       $last_inserted_role_id =    $newDBConn->insert_id();
      }

      }
     
     // assign admin role to created user 
     $roleToUserData = array(
       'user_id' => $last_inserted_user_id,
       'group_id' => $last_inserted_role_id,
       );
      $newDBConn->insert('Global_userid_to_roles', $roleToUserData); 
      
      // Enter all assigned location in orz database , assigned to admin for the first time later they can modify it once they login
      foreach($dataToInsert['location_ids'] as $location_id){
        
        $locationToUserData = array(
       'user_id' => $last_inserted_user_id,
       'location_id' => $location_id,
       );
      $newDBConn->insert('Global_users_to_location', $locationToUserData);   
      }
      
      // enter one backup SMTP details incase orz didnt enter it for anyb system or location , these cred will be used
      $smtpData = array(
          'id' => 1,
          'location_id' => '9999',
          'system_id' => '9999',
          'smtp_host' => 'smtp.office365.com',
          'smtp_username' => 'info@bizadmin.com.au',
          'smtp_pass' => '1800@Footscray123!',
          'smtp_port' => '25',
          'smtp_encryptionType' =>'tls',
          'mail_protocol' => 'smtp',
          'mail_from' => 'info@bizadmin.com.au',
          );
       $newDBConn->insert('Global_SmtpSettings', $smtpData);   

	}
	
	public function index(){
	    if($this->session->userdata('IsUserLogged')){
	        
	        $res=$this->general_model->fetchAllRecord('organization_list');
	       // echo "<pre>"; print_r($res); exit;
	        $data['record'] = $res;
	        $data['controller_add'] = 'organization/add'; 
	        $data['controller_edit'] = 'organization/edit'; 
	        $data['controller_view'] = 'organization/view'; 
	      
	        $data['page_title'] = 'Organization List';
	        $data['table_name'] = 'organization_list';
	        $data['table_columns'][] = array(
	                'column_title' =>'Organization Name',
	                'column_name' =>'orz_name',
	                'sort' => '1',
	            );
	            
	          
            $data['table_columns'][] = array(
                    'column_title' =>'Email',
                    'column_name' =>'orz_email',
                    'sort' => '1',
                );
            $data['table_columns'][] = array(
                    'column_title' =>'Phone',
                    'column_name' =>'orz_phone',
                    'sort' => '0',
                );
            $data['table_columns'][] = array(
                    'column_title' =>'Status',
                    'column_name' =>'organization_list_status',
                    'sort' => '1',
                ); 
            
	        $data['table_action'] = array('view','edit','delete');
	        
	        $this->load->view('general/header');
    		$this->load->view('general/listing',$data);
    		$this->load->view('general/footer');
		
	    }else{
	        redirect('auth');
	    }
	    
		
	}
	public function add(){ 
	    if($this->session->userdata('IsUserLogged')){
	        if($_POST){
	            $filename = ''; 
	            if(!empty($_FILES['orz_logo']['name'])){
    
                  $_FILES['file']['name'] = $_FILES['orz_logo']['name'];
                  $_FILES['file']['type'] = $_FILES['orz_logo']['type'];
                  $_FILES['file']['tmp_name'] = $_FILES['orz_logo']['tmp_name'];
                  $_FILES['file']['error'] = $_FILES['orz_logo']['error'];
                  $_FILES['file']['size'] = $_FILES['orz_logo']['size'];
          
                  $config['upload_path'] = './uploaded_files/organization_logos';
                  $config['allowed_types'] = 'jpg|jpeg|png|gif';
                  $config['max_size'] = '5000';
                  $config['file_name'] = $_FILES['orz_logo']['name'];
           
                  $this->load->library('upload',$config); 
            
                  if($this->upload->do_upload('file')){
                    $uploadData = $this->upload->data();
                    $filename = $uploadData['file_name'];
                   
                  }else{
                     
                  }
                }
                // using argon2
                $hasedPassword = $this->hash_password($_POST['orz_password']);
              
	           $postData= array(
	               'orz_name' => $_POST['orz_name'],
	               'orz_email' => $_POST['orz_email'],
	               'tenant_identifier' => $_POST['tenant_identifier'],
	               'orz_phone' => $_POST['orz_phone'],
	               'orz_address' => $_POST['orz_address'],
	               'orz_password' => $hasedPassword,
	               
	                'db_name' => $_POST['db_name'],
	                'db_username' => $_POST['db_username'],
	                'db_pass' => $_POST['db_pass'],
	                
	                'mail_protocol' => $_POST['mail_protocol'],
	                'mail_port' => $_POST['mail_port'],
	                'mail_host' => $_POST['mail_host'],
	                'mail_username' => $_POST['mail_username'],
	                'mail_pass' => $_POST['mail_pass'],
	                
	               
	               'orz_logo' => $filename,
	               'organization_list_status' => $_POST['organization_list_status'],
	               'system_ids' => Serialize($_POST['system_ids']),
	               'location_ids' => Serialize($_POST['location_ids']),
	               'date_added' => date('Y-m-d'),
	               );
	           $orzID=$this->general_model->add('organization_list',$postData);
	           if($orzID){
	               // Run full automated onboarding (DB creation, schema import, seed data, config files, folders)
	               $setupResult = $this->onboarding_model->runFullSetup($_POST, $orzID, $hasedPassword);
	               
	               if ($setupResult['success']) {
	                   $this->session->set_userdata('sucess_msg', 'Organization created successfully! All setup steps completed.');
	               } else {
	                   // Partial success - org created but some steps failed
	                   $errorMessages = array_map(function($e) { return $e['message']; }, $setupResult['errors']);
	                   $this->session->set_userdata('sucess_msg', 'Organization created, but some setup steps had issues.');
	                   $this->session->set_userdata('error_msg', implode(' | ', $errorMessages));
	               }
	               
	               // Store the setup log in session for the status page
	               $this->session->set_userdata('last_setup_log', $setupResult['log']);
	               $this->session->set_userdata('last_setup_errors', $setupResult['errors']);
	               
	               redirect('organization/setup_status');
	               return;
	           }else{
	               $this->session->set_userdata('error_msg','Failed to add record');
	           }
	           redirect('organization');
	        }else{
	            $res=$this->general_model->fetchRecord('system_details');
	            $data['locations']  =$this->location_model->fetchAllRecord();
	           // echo "<pre>"; print_r($data['locations']); exit;
	            $data['system_details'] = $res;
	            $data['form_type'] = 'add';
    	        $this->load->view('general/header');
        		$this->load->view('organization/add',$data);
        		$this->load->view('general/footer');
	        }
	        
		
	    }else{
	        redirect('auth');
	    }	
	}

	public function hash_password($password, $identity = NULL)
	{
	
		if (empty($password) || strpos($password, "\0") !== FALSE)	{
			return FALSE;
		}

		$algo = PASSWORD_ARGON2I;
		$params =  [
	'memory_cost'	=> defined('PASSWORD_ARGON2_DEFAULT_MEMORY_COST') ? PASSWORD_ARGON2_DEFAULT_MEMORY_COST : 1 << 12,
	'time_cost'	=> defined('PASSWORD_ARGON2_DEFAULT_TIME_COST') ? PASSWORD_ARGON2_DEFAULT_TIME_COST : 2,
	'threads'	=> defined('PASSWORD_ARGON2_DEFAULT_THREADS') ? PASSWORD_ARGON2_DEFAULT_THREADS : 2
];

		if ($algo !== FALSE && $params !== FALSE)
		{
			$hash = password_hash($password, $algo, $params);
			if (is_null($hash) || $hash === FALSE) {
				return FALSE;
			}
			return $hash;
		}

		return FALSE;
	}
	
	public function edit($id = '')
{
    if (!$this->session->userdata('IsUserLogged')) {
        return redirect('auth');
    }

    // Handle POST submission
    if ($this->input->post()) {

        $post = $this->input->post();

        // Safely fetch values with fallback defaults
        $id = isset($post['id']) ? (int)$post['id'] : 0;

        $postData = [
            'orz_name'      => isset($post['orz_name']) ? trim($post['orz_name']) : '',
            'orz_email'     => isset($post['orz_email']) ? trim($post['orz_email']) : '',
            'tenant_identifier' => isset($post['tenant_identifier']) ? trim($post['tenant_identifier']) : '',
            
            // add DB info while creating orz not while updayting, if needed uncomment this c ode
            // 'db_name'     => isset($_POST['db_name']) ? trim($_POST['db_name']) : '',
            // 'db_username' => isset($_POST['db_username']) ? trim($_POST['db_username']) : '',
            // 'db_pass'     => isset($_POST['db_pass']) ? trim($_POST['db_pass']) : '',


            'mail_protocol' => isset($post['mail_protocol']) ? trim($post['mail_protocol']) : '',
            'mail_port'     => isset($post['mail_port']) ? trim($post['mail_port']) : '',
            'mail_host'     => isset($post['mail_host']) ? trim($post['mail_host']) : '',
            'mail_username' => isset($post['mail_username']) ? trim($post['mail_username']) : '',
            'mail_pass'     => isset($post['mail_pass']) ? trim($post['mail_pass']) : '',

            'orz_phone'     => isset($post['orz_phone']) ? trim($post['orz_phone']) : '',
            'orz_address'   => isset($post['orz_address']) ? trim($post['orz_address']) : '',
            'organization_list_status' => isset($post['organization_list_status']) ? (int)$post['organization_list_status'] : 0,

            'system_ids'    => isset($post['system_ids']) ? serialize($post['system_ids']) : serialize([]),
            'location_ids'  => isset($post['location_ids']) ? serialize($post['location_ids']) : serialize([]),

            'date_updated'  => date('Y-m-d')
        ];

        // Add password only if provided
        if (!empty($post['orz_password'])) {
            $postData['orz_password'] = $this->hash_password($post['orz_password']);
        }

        /**
         * FILE UPLOAD (SAFE)
         */
        if (!empty($_FILES['orz_logo']['name'])) {

            $config = [
                'upload_path'   => './uploaded_files/organization_logos',
                'allowed_types' => 'jpg|jpeg|png|gif',
                'max_size'      => 5000,
                'file_name'     => $_FILES['orz_logo']['name']
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('orz_logo')) {
                $uploadData = $this->upload->data();
                $postData['orz_logo'] = $uploadData['file_name'];
            } else {
                log_message('error', 'Logo Upload Error: ' . $this->upload->display_errors());
            }
        }

        /**
         * UPDATE MAIN ORGANIZATION TABLE
         */
        $res = $this->general_model->update('organization_list', $id, $postData);

        if ($res) {

            // Fetch stored DB credentials from the database (NOT from POST - browser autofill corrupts password fields)
            $orgRecord = $this->general_model->fetchAllRecord('organization_list', $id);
            if (!empty($orgRecord)) {
                $storedOrg = $orgRecord[0];
                $dbDetails = [
                    'db_name'     => $storedOrg->db_name,
                    'db_username' => $storedOrg->db_username,
                    'db_pass'     => $storedOrg->db_pass,
                ];
            } else {
                $dbDetails = [
                    'db_name'     => $post['db_name'] ?? '',
                    'db_username' => $post['db_username'] ?? '',
                    'db_pass'     => $post['db_pass'] ?? '',
                ];
            }

            // Now update ORG user in tenant DB
            $newDBConn = $this->connectToThisDB($dbDetails);

            if ($newDBConn) {
                $orz_user_id = get_user_id_by_organization_id($newDBConn, $id);

                if ($orz_user_id) {

                    $updatedData = [
                        'username'  => $post['tenant_identifier'] ?? '',
                        'email'     => $post['orz_email'] ?? '',
                        'phone'     => $post['orz_phone'] ?? '',
                        'system_ids'=> serialize($post['system_ids'] ?? []),
                        'location_ids'=> serialize($post['location_ids'] ?? []),
                        'active'    => isset($post['organization_list_status']) ? (int)$post['organization_list_status'] : 0,
                        'date_modified' => date('Y-m-d')
                    ];

                    if (!empty($post['orz_password'])) {
                        $updatedData['password'] = $this->hash_password($post['orz_password']);
                    }

                    $newDBConn->where('id', $orz_user_id);
                    $newDBConn->update('Global_users', $updatedData);
                }
            }

            $this->session->set_flashdata('sucess_msg', 'Record updated successfully');
        } else {
            $this->session->set_flashdata('error_msg', 'Failed to update record');
        }

        return redirect('organization');

    } else {
        // Load edit page
        $record = $this->general_model->fetchAllRecord('organization_list', $id);

        $data['locations'] = $this->location_model->fetchAllRecord();
        $data['record'] = $record;
        $data['system_details'] = $this->general_model->fetchRecord('system_details');
        $data['form_type'] = 'edit';

        $this->load->view('general/header');
        $this->load->view('organization/add', $data);
        $this->load->view('general/footer');
    }
}

	public function view($id){ 
	    if($this->session->userdata('IsUserLogged')){
	        
            $res=$this->general_model->fetchAllRecord('organization_list',$id);
            $data['locations']  =$this->location_model->fetchAllRecord();
            $data['record'] = $res;
            $system_details=$this->general_model->fetchRecord('system_details');
	        $data['system_details'] = $system_details;
            $data['form_type'] = 'view';
            $this->load->view('general/header');
    		$this->load->view('organization/add',$data);
    		$this->load->view('general/footer');
	       
		
	    }else{
	        redirect('auth');
	    }
	  	
	}
	
	public function system_listing(){
	    if($this->session->userdata('IsUserLogged')){
	        
	        $res=$this->general_model->fetchRecord('system_details');
	        $data['record'] = $res;
	        $data['controller_add'] = 'organization/add_system'; 
	        $data['controller_edit'] = 'organization/edit_system'; 
	        $data['controller_view'] = 'organization/view_system'; 
	        $data['controller_viewMenu'] = 'menu/menu_list';
	        $data['page_title'] = 'System Details';
	        $data['table_name'] = 'system_details';
	        $data['table_columns'][] = array(
	                'column_title' =>'System Name',
	                'column_name' =>'system_name',
	                'sort' => '1',
	            ); 
            
	        $data['table_action'] = array('view','View Menu','edit','delete');
	        
	        $this->load->view('general/header');
    		$this->load->view('general/listing',$data);
    		$this->load->view('general/footer');
		
	    }else{
	        redirect('auth');
	    }
	    
		
	}
	public function add_system(){ 
	    if($this->session->userdata('IsUserLogged')){
	        if($_POST){
	          
	           $postData= array(
	               'system_name' => (isset($_POST['system_name']) ? $_POST['system_name'] : ''),
	               'system_icon' => (isset($_POST['system_icon']) ? $_POST['system_icon'] : ''),
	               'system_color' =>  (isset($_POST['system_color']) ? $_POST['system_color'] : ''),
	               'slug' =>   (isset($_POST['slug']) ? $_POST['slug'] : ''),
	                'system_details_status' => '1',
	               'date_added' => date('Y-m-d'),
	               );    
	           $res=$this->general_model->add('system_details',$postData);
	           if($res){
	               $this->session->set_userdata('sucess_msg','Record added successfully');
	           }else{
	               $this->session->set_userdata('error_msg','Failed to add record');
	           }
	           redirect('organization/system_listing');
	        }else{
	            $data['form_type'] = 'add';
    	        $this->load->view('general/header');
        		$this->load->view('organization/add_system_details',$data);
        		$this->load->view('general/footer');
	        }
	        
		
	    }else{
	        redirect('auth');
	    }	
	}
	public function edit_system($id=''){ 
	    if($this->session->userdata('IsUserLogged')){
	        if($_POST){
	         
	           $postData= array(
	               'system_name' => (isset($_POST['system_name']) ? $_POST['system_name'] : ''),
	               'system_icon' => (isset($_POST['system_icon']) ? $_POST['system_icon'] : ''),
	               'system_color' =>  (isset($_POST['system_color']) ? $_POST['system_color'] : ''),
	               'slug' =>   (isset($_POST['slug']) ? $_POST['slug'] : ''),
	               'date_updated' => date('Y-m-d'),
	               );
	            $id = $_POST['id'];
	           // echo "<pre>"; print_r($postData); exit;
	           $res=$this->general_model->update('system_details',$id,$postData);
	           if($res){
	               $this->session->set_flashdata('sucess_msg','Record updated successfully');
	           }else{
	               $this->session->set_flashdata('error_msg','Failed to update record');
	           }
	           redirect('organization/system_listing');
	        }else{
	            $res=$this->general_model->fetchRecord('system_details',$id);
	            $data['record'] = $res;
	            $data['form_type'] = 'edit';
    	        $this->load->view('general/header');
        		$this->load->view('organization/add_system_details',$data);
        		$this->load->view('general/footer');
	        }
	        
		
	    }else{
	        redirect('auth');
	    }
	  	
	}
	public function view_system($id){ 
	    if($this->session->userdata('IsUserLogged')){
	        
            $res=$this->general_model->fetchRecord('system_details',$id);
            $data['record'] = $res;
            $data['form_type'] = 'view';
            $this->load->view('general/header');
    		$this->load->view('organization/add_system_details',$data);
    		$this->load->view('general/footer');
	       
		
	    }else{
	        redirect('auth');
	    }
	  	
	}
	
	// =========================================================================
	// Organization Delete (PIN-protected)
	// =========================================================================

	/** The preset delete PIN — stored as SHA256 hash */
	private function getDeletePinHash(){
	    return hash('sha256', '1802');
	}

	/**
	 * AJAX: Verify the delete PIN
	 */
	public function verify_delete_pin(){
	    if(!$this->session->userdata('IsUserLogged') || !$this->input->is_ajax_request()){
	        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
	        return;
	    }
	    
	    $pin = $this->input->post('pin');
	    if(empty($pin)){
	        echo json_encode(['success' => false, 'message' => 'Please enter the PIN']);
	        return;
	    }
	    
	    $inputHash = hash('sha256', $pin);
	    if($inputHash === $this->getDeletePinHash()){
	        // Store a short-lived token in session so the actual delete call can verify
	        $this->session->set_userdata('delete_pin_verified', time());
	        echo json_encode(['success' => true]);
	    } else {
	        echo json_encode(['success' => false, 'message' => 'Incorrect PIN. Please try again.']);
	    }
	}

	/**
	 * AJAX: Permanently delete an organization and all its data
	 */
	public function delete_organization(){
	    if(!$this->session->userdata('IsUserLogged') || !$this->input->is_ajax_request()){
	        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
	        return;
	    }
	    
	    // Verify the PIN was recently verified (within 5 minutes)
	    $pinVerifiedAt = $this->session->userdata('delete_pin_verified');
	    if(!$pinVerifiedAt || (time() - $pinVerifiedAt) > 300){
	        echo json_encode(['success' => false, 'message' => 'PIN verification expired. Please re-enter PIN.']);
	        return;
	    }
	    
	    $orzId = (int)$this->input->post('orz_id');
	    if($orzId <= 0){
	        echo json_encode(['success' => false, 'message' => 'Invalid organization ID']);
	        return;
	    }
	    
	    // Fetch org details (need db credentials, tenant_identifier, etc.)
	    $orgRecord = $this->general_model->fetchAllRecord('organization_list', $orzId);
	    if(empty($orgRecord)){
	        echo json_encode(['success' => false, 'message' => 'Organization not found']);
	        return;
	    }
	    
	    $org = $orgRecord[0];
	    
	    // Run the full delete via Onboarding_model
	    $result = $this->onboarding_model->deleteOrganizationData($org, $orzId);
	    
	    // Clear the PIN verification
	    $this->session->unset_userdata('delete_pin_verified');
	    
	    echo json_encode($result);
	}

	/**
	 * AJAX: Send PIN reset email
	 */
	public function forgot_delete_pin(){
	    if(!$this->session->userdata('IsUserLogged') || !$this->input->is_ajax_request()){
	        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
	        return;
	    }
	    
	    $to = 'adityakohli467@gmail.com';
	    $subject = 'Bizadmin Super Admin - Delete PIN Reminder';
	    $message = '<html><body>';
	    $message .= '<h3>Bizadmin Super Admin - Delete PIN</h3>';
	    $message .= '<p>Your current delete PIN for organization deletion is: <strong>1802</strong></p>';
	    $message .= '<p>This PIN is required to permanently delete an organization and all its data.</p>';
	    $message .= '<p><small>Requested at: ' . date('Y-m-d H:i:s') . '</small></p>';
	    $message .= '</body></html>';
	    
	    $this->email->from('info@bizadmin.com.au', 'Bizadmin Super Admin');
	    $this->email->to($to);
	    $this->email->subject($subject);
	    $this->email->message($message);
	    $this->email->set_mailtype('html');
	    
	    if($this->email->send()){
	        echo json_encode(['success' => true, 'message' => 'PIN has been sent to your registered email.']);
	    } else {
	        echo json_encode(['success' => true, 'message' => 'PIN has been sent to your registered email.']);
	        // We still show success to not leak email delivery status
	        log_message('error', 'Failed to send delete PIN email: ' . $this->email->print_debugger());
	    }
	}

	/**
	 * Display the setup status page after automated onboarding
	 */
	public function setup_status(){
	    if($this->session->userdata('IsUserLogged')){
	        $data['setup_log']    = $this->session->userdata('last_setup_log') ?: [];
	        $data['setup_errors'] = $this->session->userdata('last_setup_errors') ?: [];
	        
	        // Clear the session data after displaying
	        $this->session->unset_userdata('last_setup_log');
	        $this->session->unset_userdata('last_setup_errors');
	        
	        $this->load->view('general/header');
	        $this->load->view('organization/setup_status', $data);
	        $this->load->view('general/footer');
	    } else {
	        redirect('auth');
	    }
	}

	/**
	 * Re-run the automated setup for an existing organization
	 * Useful when the initial setup failed partway through
	 */
	public function rerun_setup($orzId = 0){
	    if(!$this->session->userdata('IsUserLogged')){
	        redirect('auth');
	        return;
	    }
	    
	    $orzId = (int)$orzId;
	    if($orzId <= 0){
	        $this->session->set_flashdata('error_msg', 'Invalid organization ID');
	        redirect('organization');
	        return;
	    }
	    
	    // Fetch org details
	    $orgRecord = $this->general_model->fetchAllRecord('organization_list', $orzId);
	    if(empty($orgRecord)){
	        $this->session->set_flashdata('error_msg', 'Organization not found');
	        redirect('organization');
	        return;
	    }
	    
	    $org = $orgRecord[0];
	    
	    // Build postData from the org record (same format as add form)
	    $postData = [
	        'tenant_identifier' => $org->tenant_identifier,
	        'orz_name'          => $org->orz_name,
	        'orz_email'         => $org->orz_email,
	        'orz_phone'         => isset($org->orz_phone) ? $org->orz_phone : '',
	        'db_name'           => $org->db_name,
	        'db_username'       => $org->db_username,
	        'db_pass'           => $org->db_pass,
	        'organization_list_status' => $org->organization_list_status,
	        'system_ids'        => unserialize($org->system_ids),
	        'location_ids'      => unserialize($org->location_ids),
	    ];
	    
	    // Use the stored hashed password for the admin user
	    $hashedPassword = $org->orz_password;
	    
	    // Run the full setup again
	    $setupResult = $this->onboarding_model->runFullSetup($postData, $orzId, $hashedPassword);
	    
	    if ($setupResult['success']) {
	        $this->session->set_userdata('sucess_msg', 'Setup re-run completed successfully!');
	    } else {
	        $errorMessages = array_map(function($e) { return $e['message']; }, $setupResult['errors']);
	        $this->session->set_userdata('sucess_msg', 'Setup re-run completed with some issues.');
	        $this->session->set_userdata('error_msg', implode(' | ', $errorMessages));
	    }
	    
	    $this->session->set_userdata('last_setup_log', $setupResult['log']);
	    $this->session->set_userdata('last_setup_errors', $setupResult['errors']);
	    
	    redirect('organization/setup_status');
	}


}
