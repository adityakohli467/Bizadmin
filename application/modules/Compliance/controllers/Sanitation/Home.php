<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    function __construct() {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $this->load->model('generalcomp_model');
        $this->load->model('task_model');
        $this->load->model('common_model');
        $this->load->model('config_model');
        $this->load->model('general_model');
        $this->load->model('prep_model');
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        $this->selected_location_id = $this->session->userdata('location_id');
    }

    public function index($system_id = '') {
        if ($system_id) {
            $this->session->set_userdata('system_id', $system_id);
        }

        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationsites', [], ['status' => 1,'location_id' => $this->selected_location_id]);
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea('Compliance_SanitationPrepArea','Compliance_Sanitationsites');
        $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationproducts', [], ['status' => 1,'location_id' => $this->selected_location_id]);
    // echo "<pre>";
    // print_r($data['site_detail']);
    // print_r($data['prep_detail']);
    // print_r($data['products']);
    // exit;
        // Email settings
        $emailSettings = $this->general_model->fetchSmtpSettings($this->selected_location_id, $system_id);
        if (empty($emailSettings)) {
            $emailSettings = $this->general_model->fetchSmtpSettings('9999', '9999');
            $this->configureSMTP($emailSettings);
        } else if ($emailSettings->mail_protocol === 'smtp') {
            $this->configureSMTP($emailSettings);
        }
        if (isset($emailSettings->mail_from)) {
            $this->session->set_userdata('mail_from', $emailSettings->mail_from);
        }

        // Today's data
        $condition = ['date_entered' => date('Y-m-d'), 'location_id' => $this->selected_location_id];
        $history_data = $this->common_model->fetchRecordsDynamically('Compliance_SanitationHistory', [], $condition);
        $todaysEnteredData = [];
        foreach ($history_data as $record) {
            $todaysEnteredData[$record['product_id']] = [
                'entered_by' => $record['entered_by'],
                'commenced' => $record['commenced'],
                'time_completed' => $record['time_completed'],
                'completed_mds' => $record['completed_mds'],
                'comments' => $record['comments'],
                'recorded_by' => $record['recorded_by'],
                'is_completed' => $record['is_completed'],
                'date_entered' => date('Y-m-d'),
                'location_id' => $this->selected_location_id
            ];
        }
        $data['todaysEnteredData'] = $todaysEnteredData;

        $this->load->view('general/header');
        $this->load->view('Sanitation/dashboard', $data);
        $this->load->view('general/footer');
    }

    public function saveDashboardData() {
        $product_id = $this->input->post('product_id', TRUE);
        $field = $this->input->post('field', TRUE);
        $value = $this->input->post('value', TRUE);
        $prep_id = $this->input->post('prep_id', TRUE);

        if (!$product_id || !$field || !$prep_id) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        // Validate field
        $allowed_fields = [ 'entered_by', 'commenced', 'time_completed', 'completed_mds', 'comments', 'recorded_by', 'best_before'];
        if (!in_array($field, $allowed_fields)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid field']);
            return;
        }

        $update_data = [$field => $value ?: NULL];
        $condition = [
            'product_id' => $product_id,
            'date_entered' => date('Y-m-d'),
            'prep_id' => $prep_id,
            'location_id' => $this->selected_location_id
        ];

        $exists = $this->common_model->fetchRecordsDynamically('Compliance_SanitationHistory', [], $condition);
        if ($exists) {
            $this->common_model->commonRecordUpdateMultipleConditions('Compliance_SanitationHistory', $condition, $update_data);
            $response = ['status' => 'success', 'message' => 'Record updated successfully'];
        } else {
            $data = array_merge($condition, $update_data);
            $insert_id = $this->common_model->commonRecordCreate('Compliance_SanitationHistory', $data);
            $response = $insert_id ? ['status' => 'success', 'message' => 'Record inserted successfully'] : ['status' => 'error', 'message' => 'Failed to insert record'];
        }

        echo json_encode($response);
    }

    public function completeTask() {
        $product_id = $this->input->post('product_id', TRUE);
        $prep_id = $this->input->post('prep_id', TRUE);
        $date_entered = $this->input->post('date_entered', TRUE) ?: date('Y-m-d');

        if (!$product_id || !$prep_id) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $condition = [
            'product_id' => $product_id,
            'prep_id' => $prep_id,
            'date_entered' => $date_entered,
            'location_id' => $this->selected_location_id
        ];
        $exists = $this->common_model->fetchRecordsDynamically('Compliance_SanitationHistory', [], $condition);
        if ($exists) {
            $this->common_model->commonRecordUpdateMultipleConditions('Compliance_SanitationHistory', $condition, ['is_completed' => 1]);
            echo json_encode(['status' => 'success', 'message' => 'Task marked as completed']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No record found to complete']);
        }
    }

    public function history() {
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationsites', [], ['status' => 1,'location_id' => $this->selected_location_id]);
        $this->load->view('general/header');
        $this->load->view('Sanitation/history', $data);
        $this->load->view('general/footer');
    }

    public function historyData($encodedDateRange = '', $prep_id = '') {
        if ($encodedDateRange == '' && $prep_id == '') {
            $dateRange = $this->input->post('date_range');
            $prep_id = $this->input->post('site_id');
        } else {
            $dateRange = urldecode($encodedDateRange);
        }

        $dateParts = explode(" to ", $dateRange);
        if (count($dateParts) == 2) {
            $fromDate = date('Y-m-d', strtotime(trim($dateParts[0])));
            $toDate = date('Y-m-d', strtotime(trim($dateParts[1])));

            $uniqueDates = [];
            $currentDate = $fromDate;
            while ($currentDate <= $toDate) {
                $uniqueDates[] = $currentDate;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }

            $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationsites', [], ['location_id' => $this->selected_location_id]);
            $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_SanitationPrepArea', [], []);
            $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationproducts', [], ['status' => 1,'location_id' => $this->selected_location_id]);

            $condition = [
                'date_entered >=' => $fromDate,
                'date_entered <=' => $toDate,
                'location_id' => $this->selected_location_id
            ];
            if ($prep_id) {
                $condition['prep_id'] = $prep_id;
            }

            $history_data = $this->common_model->fetchRecordsDynamically('Compliance_SanitationHistory', [], $condition);
            $weeklyData = [];
            foreach ($history_data as $item) {
                $date_entered = $item['date_entered'];
                $product_id = $item['product_id'];
                if (!isset($weeklyData[$date_entered])) {
                    $weeklyData[$date_entered] = [];
                }
                $weeklyData[$date_entered][$product_id] = $item;
            }

            $data['uniqueDates'] = $uniqueDates;
            $data['dateRange'] = $dateRange;
            $data['site_id'] = $prep_id;
            $data['weeklyData'] = $weeklyData;

            // Only admin and manager can edit history
            $roleName = get_logged_in_user_role($this->ion_auth, 'name');
            $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';

            $this->load->view('general/header');
            $this->load->view('Sanitation/historyDetails', $data);
            $this->load->view('general/footer');
        } else {
            show_error('Invalid date range format');
        }
    }

    public function updateSanitationHistory() {
        // Only admin and manager can update history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
            return;
        }

        $product_id = $this->input->post('product_id', TRUE);
        $date_entered = $this->input->post('date_entered', TRUE);
        $prep_id = $this->input->post('prep_id', TRUE);
        $location_id = $this->selected_location_id;
        $commenced = $this->input->post('commenced', TRUE);
        $time_completed = $this->input->post('time_completed', TRUE);
        $completed_mds = $this->input->post('completed_mds', TRUE);
        $comments = $this->input->post('comments', TRUE);
        $entered_by = $this->input->post('entered_by', TRUE);

        if (empty($product_id) || empty($date_entered) || empty($prep_id) || empty($location_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            return;
        }

        $condition = [
            'product_id' => $product_id,
            'date_entered' => $date_entered,
            'prep_id' => $prep_id,
            'location_id' => $this->selected_location_id
        ];

        $update_data = [];
        
        if ($entered_by !== '' && $entered_by !== NULL) $update_data['entered_by'] = $entered_by;
       
        if ($commenced !== '' && $commenced !== NULL) $update_data['commenced'] = $commenced;
        if ($time_completed !== '' && $time_completed !== NULL) $update_data['time_completed'] = $time_completed;
        if ($completed_mds !== '' && $completed_mds !== NULL) $update_data['completed_mds'] = $completed_mds;
        if ($comments !== '' && $comments !== NULL) $update_data['comments'] = $comments;
      

        if (empty($update_data)) {
            echo json_encode(['status' => 'error', 'message' => 'No valid fields provided for update']);
            return;
        }

        $exists = $this->common_model->fetchRecordsDynamically('Compliance_SanitationHistory', [], $condition);
        if ($exists) {
            $this->common_model->commonRecordUpdateMultipleConditions('Compliance_SanitationHistory', $condition, $update_data);
            $response = ['status' => 'success', 'message' => 'Record updated successfully'];
        } else {
            $data = array_merge($condition, [
                
                'entered_by' => $entered_by ?: NULL,
                'commenced' => $commenced ?: NULL,
                'time_completed' => $time_completed ?: NULL,
                'completed_mds' => $completed_mds ?: NULL,
                'comments' => $comments ?: NULL,
            ]);
            $insert_id = $this->common_model->commonRecordCreate('Compliance_SanitationHistory', $data);
            $response = $insert_id ? ['status' => 'success', 'message' => 'Record inserted successfully'] : ['status' => 'error', 'message' => 'Failed to insert record'];
        }

        echo json_encode($response);
    }

    public function listProduct() {
        $condition = ['status' => 1,'is_deleted' => 0,'location_id' => $this->selected_location_id];
        $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationproducts', [], $condition);
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationsites', [], $condition);
        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_SanitationPrepArea', [], $condition);

        $this->load->view('general/header');
        $this->load->view('Sanitation/listProduct', $data);
        $this->load->view('general/footer');
    }

    public function addOrUpdateProduct() {
        $id = $this->input->post('id', TRUE);
        $product_name = $this->input->post('product_name', TRUE);
        $prep_id = $this->input->post('prep_id', TRUE);
        $site_id = $this->input->post('site_id', TRUE);

        if (empty($product_name) || empty($prep_id) || empty($site_id)) {
            $this->session->set_flashdata('error', 'Product name, prep area, and site are required.');
            redirect('Compliance/Sanitation/Home/listProduct');
        }

        $data = [
            'product_name' => $product_name,
            'prep_id' => $prep_id,
            'site_id' => $site_id
        ];

        if ($id) {
            $this->common_model->commonRecordUpdate('Compliance_Sanitationproducts', 'id', $id, $data);
            $this->session->set_flashdata('success', 'Product updated successfully.');
        } else {
          
            $this->common_model->commonRecordCreate('Compliance_Sanitationproducts', $data);
            $this->session->set_flashdata('success', 'Product added successfully.');
        }

        redirect('Compliance/Sanitation/Home/listProduct');
    }

    public function getProductById($id) {
        $condition = ['id' => $id];
        $product = $this->common_model->fetchRecordsDynamically('Compliance_Sanitationproducts', [], $condition);
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