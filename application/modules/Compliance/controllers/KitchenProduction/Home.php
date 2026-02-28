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
        $this->load->model('prep_model');
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        $this->selected_location_id = $this->session->userdata('location_id');
    }
    
    public function index($system_id='')
    {   
        (isset($system_id) && $system_id !='' ? $this->session->set_userdata('system_id',$system_id) : '');
        
        // Configure SMTP settings
        $emailSettings = $this->general_model->fetchSmtpSettings($this->selected_location_id, $system_id);
        if(empty($emailSettings)){
            $emailSettings = $this->general_model->fetchSmtpSettings('9999','9999');
            $this->configureSMTP($emailSettings);
        } else {
            if ($emailSettings->mail_protocol === 'smtp') {
                $this->configureSMTP($emailSettings);
            }   
        }
        if(isset($emailSettings->mail_from)){
            $this->session->set_userdata('mail_from', $emailSettings->mail_from);
        }
        
        // Fetch sites and prep areas
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', array(), ['status=1', 'location_id' => $this->selected_location_id, 'is_deleted' => 0]); 
        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionPrepArea', array(), ['status=1', 'location_id' => $this->selected_location_id, 'is_deleted' => 0]); 
        
        // Fetch active products
        $condition = array('status' => 1, 'is_deleted' => 0, 'location_id' => $this->selected_location_id); 
        $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionproducts', '', $condition);
        
        // Today's entered data
        $condition = array('date_entered' => date('Y-m-d'), 'location_id' => $this->selected_location_id);
        $history_data = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProduction_history', '', $condition);

        // Process history data into $todaysEnteredData format
        $todaysEnteredData = array();
        foreach ($history_data as $record) {
            $todaysEnteredData[$record['product_id']] = array(
                'quantity' => $record['quantity'],
                'entered_by' => $record['entered_by']
            );
        }
        $data['todaysEnteredData'] = $todaysEnteredData;
        
        $this->load->view('general/header');
        $this->load->view('KitchenProduction/dashboard', $data);
        $this->load->view('general/footer');
    }
    
    public function saveDashboardData()
    {
        $product_id = $this->input->post('product_id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $prepId = $this->input->post('prep');

        if (!$product_id || !$field) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $data = [
            'product_id' => $product_id,
            'prep_id' => $prepId,
            $field => $value,
            'date_entered' => date('Y-m-d'),
            'location_id' => $this->selected_location_id
        ];

        // Check if a record already exists for this product today
        $conditionPr = array('product_id' => $product_id, 'date_entered' => date('Y-m-d'), 'location_id' => $this->selected_location_id);
        $exists = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProduction_history', '', $conditionPr);

        if ($exists) {
            $this->common_model->commonRecordUpdate('Compliance_KitchenProduction_history', 'id', $exists[0]['id'], $data);
        } else {
            $this->common_model->commonRecordCreate('Compliance_KitchenProduction_history', $data);
        }

        echo json_encode(['status' => 'success']);
    }
    
    function history()
    {
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', ['status' => 1, 'location_id' => $this->selected_location_id, 'is_deleted' => 0]);
        
        $this->load->view('general/header');
        $this->load->view('KitchenProduction/history', $data);
        $this->load->view('general/footer');
    }
    
    public function historyData($encodedDateRange = '', $site_id = '') 
    {
        // Handle input
        if ($encodedDateRange == '' && $site_id == '') {
            $dateRange = $this->input->post('date_range');
            $site_id = $this->input->post('site_id');
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
            $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', array('location_id' => $this->selected_location_id, 'is_deleted' => 0)); 
            $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionPrepArea', '', array('location_id' => $this->selected_location_id, 'is_deleted' => 0)); 
            $condition = array('status' => 1, 'is_deleted' => 0, 'location_id' => $this->selected_location_id);
            $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionproducts', '', $condition);

            // Fetch history data — build query manually to support WHERE IN for prep_ids
            $this->tenantDb->where('date_entered >=', $fromDate);
            $this->tenantDb->where('date_entered <=', $toDate);
            $this->tenantDb->where('location_id', $this->selected_location_id);

            // If a site is selected, get all prep area IDs for that site and filter by them
            if ($site_id != '' && $site_id != 'Select Site') {
                $prepAreas = $this->common_model->fetchRecordsDynamically(
                    'Compliance_KitchenProductionPrepArea', 
                    ['id'], 
                    ['site_id' => $site_id, 'location_id' => $this->selected_location_id, 'is_deleted' => 0]
                );
                if (!empty($prepAreas)) {
                    $prepIds = array_column($prepAreas, 'id');
                    $this->tenantDb->where_in('prep_id', $prepIds);
                }
            }

            $history_data = $this->tenantDb->get('Compliance_KitchenProduction_history')->result_array();

            // Restructure history data by date and product_id
            $weeklyData = array();
            foreach ($history_data as $item) {
                $date_entered = $item['date_entered'];
                $product_id = $item['product_id'];
                if (!isset($weeklyData[$date_entered])) {
                    $weeklyData[$date_entered] = array();
                }
                $weeklyData[$date_entered][$product_id] = $item;
            }

            // Pass data to view
            $data['uniqueDates'] = $uniqueDates;
            $data['dateRange'] = $dateRange;
            $data['site_id'] = $prep_id;
            $data['weeklyData'] = $weeklyData;

            $this->load->view('general/header');
            $this->load->view('KitchenProduction/historyDetails', $data);
            $this->load->view('general/footer');
        } else {
            show_error('Invalid date range format');
        }
    }

    public function updateHistory() 
    {
        // Validate input
        $product_id = $this->input->post('product_id', TRUE);
        $date_entered = $this->input->post('date_entered', TRUE);
        $prep_id = $this->input->post('prep_id', TRUE);
        $location_id = $this->input->post('location_id', TRUE);
        $quantity = $this->input->post('quantity', TRUE);
        $entered_by = $this->input->post('entered_by', TRUE);

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

        // Determine which field is being updated
        $update_data = array();
        if ($quantity !== '' && $quantity !== NULL) {
            $update_data['quantity'] = $quantity;
        }
        if ($entered_by !== '' && $entered_by !== NULL) {
            $update_data['entered_by'] = $entered_by;
        }

        // If no fields to update, return error
        if (empty($update_data)) {
            $response = array('status' => 'error', 'message' => 'No valid fields provided for update');
            echo json_encode($response);
            return;
        }

        // Check if record exists
        $exists = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProduction_history', '', $condition);

        if (!empty($exists)) {
            // Update existing record
            $this->common_model->commonRecordUpdateMultipleConditions('Compliance_KitchenProduction_history', $condition, $update_data);
            $response = array('status' => 'success', 'message' => 'Record updated successfully');
        } else {
            // Prepare full data for new record
            $data = array(
                'product_id' => $product_id,
                'date_entered' => $date_entered,
                'prep_id' => $prep_id,
                'location_id' => $location_id,
                'quantity' => $quantity ?: NULL,
                'entered_by' => $entered_by ?: NULL
            );
            // Insert new record
            $insert_id = $this->common_model->commonRecordCreate('Compliance_KitchenProduction_history', $data);
            if ($insert_id) {
                $response = array('status' => 'success', 'message' => 'Record inserted successfully');
            } else {
                $response = array('status' => 'error', 'message' => 'Failed to insert record');
            }
        }

        echo json_encode($response);
    }
   
    // Product management functions
    function listProduct()
    {
        $condition = array('status' => 1, 'is_deleted' => 0, 'location_id' => $this->selected_location_id);
        $data['products'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionproducts', '', $condition);
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', $condition);
        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionPrepArea', '', $condition);
        
        $this->load->view('general/header');
        $this->load->view('KitchenProduction/listProduct', $data);
        $this->load->view('general/footer');
    }
    
    public function addOrUpdateProduct() 
    {
        $id = $this->input->post('id');
  
        $data = [
            'product_name' => $this->input->post('product_name') ?? null,
            'prep_id' => $this->input->post('prep_id') ?? null,
            'location_id' => $this->selected_location_id,
            'status' => 1
        ];

        if ($id) {
            $data['updated_date'] = date('Y-m-d');
            $this->common_model->commonRecordUpdate('Compliance_KitchenProductionproducts', 'id', $id, $data);
        } else {
            $data['created_at'] = date('Y-m-d');
            $this->common_model->commonRecordCreate('Compliance_KitchenProductionproducts', $data);
        }

        redirect('Compliance/KitchenProduction/home/listProduct');
    }
    
    public function getProductById($id) 
    {
        $condition = array('id' => $id);
        $product = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionproducts', '', $condition);
        echo json_encode($product);
    }
    
    public function delete()
    {
        $id = $this->input->post('id', TRUE);
        $tableName = $this->input->post('table_name', TRUE);
        $data = [
            'is_deleted' => 1,
        ];
       
        $this->common_model->commonRecordUpdate($tableName, 'id', $id, $data);
        echo "success";
    }
    
    public function updateSortOrder()
    {
        $newOrder = $this->input->post('order');
        foreach ($newOrder as $index => $itemId) {
            $productID = substr($itemId, 4);
            $this->tenantDb->set('sort_order', $index + 1);
            $this->tenantDb->where('id', $productID);
            $this->tenantDb->update('Compliance_KitchenProductionproducts');
        }
        echo "success";
    }
}
