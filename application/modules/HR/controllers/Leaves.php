<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaves extends MY_Controller {

    function __construct() {
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
       
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // $this->load->helper('notification');
		$this->load->model('employee_model');
		$this->load->model('auth_model');
	    $this->load->model('common_model');
	    $this->load->model('Leave_model');
        $this->location_id = $this->session->userdata('location_id') ? $this->session->userdata('location_id') : ($this->session->userdata('User_location_ids') ? $this->session->userdata('User_location_ids')[0] : null);
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        $this->roleId = get_logged_in_user_role($this->ion_auth,'id');
        // $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
     
    }
    
    function leaveDashbaord(){
	  $location_id = $this->session->userdata('location_id') ?: null;

        $summary = $this->Leave_model->get_leave_summary($location_id);
        $recent = $this->Leave_model->get_leave_requests($location_id, null, 50, 0);

        $data = [
            'summary' => $summary,
            'recent_requests' => $recent,
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        // Rendered inside the shared Velzon layout (header > content > footer)
        $this->load->view('general/header');
        $this->load->view('Leaves/leavesDashboardDynamic', $data);
        $this->load->view('general/footer');
    }
    
    
    /**
     * AJAX: list requests (JSON)
     */
    public function ajax_list() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $location_id = $this->session->userdata('location_id') ?: null;
        $status = $this->input->get('status'); // optional
        $limit = (int)$this->input->get('limit') ?: 20;
        $offset = (int)$this->input->get('offset') ?: 0;

        $rows = $this->Leave_model->get_leave_requests($location_id, $status, $limit, $offset);
        $this->output->set_content_type('application/json')->set_output(json_encode($rows));
    }

    /**
     * Approve leave (POST)
     */
    public function approve() {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }

        $id = (int)$this->input->post('id');
        $approver_id = $this->session->userdata('user_id') ?: 0;
        $comment = $this->input->post('comment');

        if (!$id) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>false,'message'=>'Invalid leave id']));
            return;
        }

        $ok = $this->Leave_model->approve_leave($id, $approver_id, $comment);
        if ($ok) {
            // TODO: send notification/email to employee
            $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>true]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>false,'message'=>'Could not approve leave']));
        }
    }

    /**
     * Reject leave (POST)
     */
    public function reject() {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
        }

        $id = (int)$this->input->post('id');
        $comment = trim($this->input->post('comment'));
        $approver_id = $this->session->userdata('user_id') ?: 0;

        if (!$id || $comment === '') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>false,'message'=>'Leave id and reject comment required']));
            return;
        }

        $ok = $this->Leave_model->reject_leave($id, $approver_id, $comment);
        if ($ok) {
            // TODO: send notification/email to employee
            $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>true]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success'=>false,'message'=>'Could not reject leave']));
        }
    }

    /**
     * Leave details (ajax modal)
     */
    public function details($id = 0) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id = (int)$id;
        if (!$id) {
            echo json_encode(['success'=>false,'message'=>'Invalid id']);
            return;
        }
        $row = $this->Leave_model->get_leave_by_id($id);

        $balances = [];
        $history_summary = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'approved_days' => 0];
        if (!empty($row['emp_id'])) {
            $balances = $this->Leave_model->get_employee_leave_balance($row['emp_id']);
            $history_summary = $this->Leave_model->get_employee_leave_stats($row['emp_id']);
        }

        echo json_encode([
            'success' => true,
            'data' => $row,
            'balances' => $balances,
            'history' => $history_summary
        ]);
    }

    /**
     * Employee leave balance (ajax)
     */
    public function balance($emp_id = 0) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $emp_id = (int)$emp_id;
        if (!$emp_id) {
            echo json_encode(['success'=>false,'message'=>'Invalid employee id']);
            return;
        }
        $bal = $this->Leave_model->get_employee_leave_balance($emp_id);
        echo json_encode(['success'=>true,'data'=>$bal]);
    }
    
    
    function requestLeave(){
      $uploadPath = './uploaded_files/'.$this->tenantIdentifier.'/HR/MedicalCertificate';
      
      // Create directory if it doesn't exist
      if (!is_dir($uploadPath)) {
          mkdir($uploadPath, 0755, true);
      }
      
      // Handle file upload using common_model
      $uploadedFileName = '';
      if (isset($_FILES['userfile']) && !empty($_FILES['userfile']['name'][0])) {
          $uploadedFileName = $this->common_model->uploadAttachment($_FILES, $uploadPath);
      }
      
      // Prepare data for insert
      $data = [
          'emp_id' => $this->input->post('emp_id'),
          'leave_type' => $this->input->post('leave_type'),
          'start_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
          'end_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
          'leaveComments' => $this->input->post('leaveComments'),
          'medical_certificate' => $uploadedFileName,
          'leave_status' => 1, // Pending
          'location_id' => $this->location_id,
          'date_added' => date('Y-m-d H:i:s')
      ];
      
      // Insert leave request using common_model
      $leaveId = $this->common_model->commonRecordCreate('HR_leave_management', $data);
      
      if ($leaveId) {
          // Send email notification to managers
          $conditionsMail = ['location' => $this->location_id, 'configureFor' => 'emails'];
          $fields = ['data'];
          $mailConfigData = $this->common_model->fetchRecordsDynamically('HR_configuration', $fields, $conditionsMail);
          
          if (isset($mailConfigData[0]['data']) && !empty($mailConfigData[0]['data'])) {
              $managerEmail = unserialize($mailConfigData[0]['data']);
              $emailSendTo = explode(',', $managerEmail[0]);
              
              // Prepare encryption data
              $dataToEncrypt = [
                  'location_id' => $this->location_id,
                  'tenantIdentifier' => $this->tenantIdentifier,
                  'id' => $leaveId,
                  'emp_id' => $this->input->post('emp_id'),
                  'mail_from' => $this->session->userdata('mail_from'),
                  'username' => $this->session->userdata('username'),
                  'mail_protocol' => $this->session->userdata('mail_protocol'),
                  'smtp_host' => $this->session->userdata('smtp_host'),
                  'smtp_port' => $this->session->userdata('smtp_port'),
                  'smtp_username' => $this->session->userdata('smtp_username'),
                  'smtp_pass' => $this->session->userdata('smtp_pass'),
              ];
              
              $encryptedData = $this->encryption->encrypt(json_encode($dataToEncrypt));
              
              // Prepare email data
              $Maildata['nameEmail'] = $this->session->userdata('username');
              $Maildata['start_date'] = $this->input->post('start_date');
              $Maildata['end_date'] = $this->input->post('end_date');
              
              // Get leave type name
              $laveTypeCondition = ['id' => $this->input->post('leave_type')];
              $fieldLeaveType = ['leaveTypeName'];
              $leaveTypeData = $this->common_model->fetchRecordsDynamically('HR_leaves', $fieldLeaveType, $laveTypeCondition);
              
              $Maildata['leave_type'] = $leaveTypeData[0]['leaveTypeName'] ?? 'N/A';
              $Maildata['leaveComments'] = $this->input->post('leaveComments');
              $Maildata['approveLeaveUrl'] = base_url().'External/HR/approveLeave/'.urlencode(urlencode(urlencode($encryptedData)));
              $Maildata['rejectLeaveUrl'] = base_url().'External/HR/rejectLeave/'.urlencode(urlencode(urlencode($encryptedData)));
              
              $mailContent = $this->load->view('emails/leaveRequest', $Maildata, TRUE);
              $mailStatus = $this->sendEmail($emailSendTo, 'Leave Request', $mailContent, $this->session->userdata('mail_from'));
          }
          
          // Return success
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode(['success' => true, 'message' => 'Leave request submitted successfully']));
      } else {
          $this->output
              ->set_content_type('application/json')
              ->set_output(json_encode(['success' => false, 'message' => 'Failed to submit leave request']));
      }
    }
    
    function cancelLeave(){
        $data['leave_status'] = 0;
      $this->common_model->commonRecordUpdate('HR_leave_management','id',$_POST['id'],$data);   
    }

    /**
     * Employee-facing page: view own leave history, status and balances
     */
    function myLeaves(){
        $user_id = $this->ion_auth->user()->row()->id;
        $empData = $this->common_model->fetchRecordsDynamically(
            'HR_employee',
            ['emp_id', 'first_name', 'last_name'],
            ['userId' => $user_id]
        );
        $empId = isset($empData[0]['emp_id']) ? $empData[0]['emp_id'] : '';

        $data = [
            'empId'    => $empId,
            'empName'  => trim(($empData[0]['first_name'] ?? '') . ' ' . ($empData[0]['last_name'] ?? '')),
            'leaves'   => $empId ? $this->Leave_model->get_employee_leaves($empId) : [],
            'balances' => $empId ? $this->Leave_model->get_employee_leave_balance($empId) : [],
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        $this->load->view('general/header');
        $this->load->view('Leaves/myLeaves', $data);
        $this->load->view('general/footer');
    }
}

?>