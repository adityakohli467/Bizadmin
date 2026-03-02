<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calibrationhome extends MY_Controller {
    function __construct() {
        parent::__construct();
      
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->load->model('common_model');
        $this->load->model('temp_model');
        $this->load->model('Calibrationtemp/prep_model', 'prep_model');
        $this->load->model('config_model');
        $this->load->model('general_model');
        $this->load->model('Calibrationtemp/calibrationtemp_model');
        $this->selected_location_id = $this->session->userdata('location_id');
        $this->system_id = $this->session->userdata('system_id');
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
    }

    public function index($system_id = '') {
        $data['site_detail'] = $this->calibrationtemp_model->get_allSitesForDash();
        $condition = ['status' => 1, 'is_deleted' => 0];
        $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_calibrationProducts', '', $condition);
        $data['todaysCalibrationData'] = $this->calibrationtemp_model->fetchTodaysEnteredCalibrationData();

        $foodTempConfigurationData = $this->config_model->getConfiguration('', 'foodTemp');
        $chillingTempConfigurationData = $this->config_model->getConfiguration('', 'chillingTemp');
        if (isset($foodTempConfigurationData[0]['data']) && !empty($foodTempConfigurationData[0]['data'])) {
            $foodTempConfigurationData = unserialize($foodTempConfigurationData[0]['data']);
            $data['showFoodTemp'] = isset($foodTempConfigurationData['showFoodTemp']) ? $foodTempConfigurationData['showFoodTemp'] : '';
        }
        if (isset($chillingTempConfigurationData[0]['data']) && !empty($chillingTempConfigurationData[0]['data'])) {
            $chillingTempConfigurationData = unserialize($chillingTempConfigurationData[0]['data']);
            $data['showChillingTemp'] = isset($chillingTempConfigurationData['showChillingTemp']) ? $chillingTempConfigurationData['showChillingTemp'] : '';
        }

        $this->load->view('general/header');
        $this->load->view('CalibrationTemp/dashboard', $data);
        $this->load->view('general/footer');
    }

    public function saveRecord() {
        $product_id = $this->input->post('product_id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $prepId = $this->input->post('prepId');
        $siteId = $this->input->post('siteId');

        if (!$product_id || !$field) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $data = [
            'product_id' => $product_id,
            'prep_id' => $prepId,
            'site_id' => $siteId,
            $field => $value,
            'date_entered' => date('Y-m-d'),
            'location_id' => $this->selected_location_id
        ];

        $conditionPr = array('product_id' => $product_id, 'date_entered' => date('Y-m-d'), 'prep_id' => $prepId, 'site_id' => $siteId);
        $exists = $this->common_model->fetchRecordsDynamically('TEMP_calibrationRecordHistory', '', $conditionPr);
       
        if ($exists) {
            $this->common_model->commonRecordUpdate('TEMP_calibrationRecordHistory', 'id', $exists[0]['id'], $data);
        } else {
            $this->common_model->commonRecordCreate('TEMP_calibrationRecordHistory', $data);
        }

        echo json_encode(['status' => 'success']);
    }

    public function updateRecord() {
        $product_id = $this->input->post('product_id');
        $rowId = $this->input->post('rowId');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        if (!$product_id || !$field) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $data = [
            'product_id' => $product_id,
            $field => $value,
        ];

        $this->common_model->commonRecordUpdate('TEMP_calibrationRecordHistory', 'id', $rowId, $data);

        echo json_encode(['status' => 'success']);
    }

    function tempCHistory() {
        $data['site_detail'] = $this->calibrationtemp_model->get_allSitesForDash();
        $this->load->view('general/header');
        $this->load->view('CalibrationTemp/tempHistory', $data);
        $this->load->view('general/footer');
    }

    function historyCalibrationData($encodedDateRange = '', $site_id = '') {
        if ($encodedDateRange == '' && $site_id == '') {
            $dateRange = $this->input->post('date_range');
            $site_id = $this->input->post('site_id');
        } else {
            $dateRange = urldecode($encodedDateRange);
        }

        $data['site_detail'] = $this->calibrationtemp_model->get_allSitesForDash($site_id);
        $dateParts = explode(" to ", $dateRange);

        if (count($dateParts) == 2) {
            $fromDate = date('Y-m-d', strtotime(trim($dateParts[0])));
            $toDate = date('Y-m-d', strtotime(trim($dateParts[1])));
            $uniqueDates = array();
            $currentDate = $fromDate;
            while ($currentDate <= $toDate) {
                $uniqueDates[] = $currentDate;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
            $data['dateRange'] = $dateRange;
            $data['site_id'] = $site_id;
            $data['uniqueDates'] = $uniqueDates;
            
            $data['weeklyTempData'] = $this->calibrationtemp_model->fetchCalibrationHistoryData($fromDate, $toDate, $site_id);
            $this->load->view('general/header');
            $this->load->view('CalibrationTemp/tempHistoryDetails', $data);
            $this->load->view('general/footer');
        } else {
            echo "Invalid date range format";
        }
    }

    public function tempHistoryUpdateAlldata() {
        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            return;
        }

        $data = [
            'equipment_name' => $this->input->post('equipment_name'),
            'ice_point_temp' => $this->input->post('ice_point_temp'),
            'boiling_point_temp' => $this->input->post('boiling_point_temp'),
            'corrective_action' => $this->input->post('corrective_action'),
            'calibrated_by' => $this->input->post('calibrated_by'),
        ];

        $data = array_filter($data, function($value) {
            return $value !== '' && $value !== null;
        });

        if (empty($data)) {
            echo json_encode(['status' => 'error', 'message' => 'No data provided for update']);
            return;
        }

        $result = $this->common_model->commonRecordUpdate('TEMP_calibrationRecordHistory', 'id', $id, $data);
        echo json_encode(['status' => 'success']);
    }

    function listProduct() {
        $condition = array('status' => 1, 'is_deleted' => 0);
        $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_calibrationProducts', '', $condition);
        $where_conditions = array('is_deleted' => 0, 'location_id' => $this->selected_location_id);
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('TEMP_calibrationSites', '', $where_conditions);
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea();
        $this->load->view('general/header');
        $this->load->view('CalibrationTemp/listProduct', $data);
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
            $this->common_model->commonRecordUpdate('TEMP_calibrationProducts', 'id', $id, $data);
        } else {
            $this->common_model->commonRecordCreate('TEMP_calibrationProducts', $data);
        }

        echo json_encode(['status' => 'success']);
    }

    public function getProductById($id) {
        $condition = array('id' => $id);
        $product = $this->common_model->fetchRecordsDynamically('TEMP_calibrationProducts', '', $condition);
        echo json_encode($product);
    }

    public function save_signature() {
        $signature = $this->input->post('signature', TRUE);
        if (empty($signature)) {
            echo json_encode(['status' => 'error', 'message' => 'Signature is required.']);
            return;
        }

        $this->temp_model->save_signature($signature, 'TEMP_calibrationRecordHistory');

        echo json_encode(['status' => 'success', 'message' => 'Signature saved.']);
    }

    public function deleteProduct() {
        $id = $this->input->post('id');
        $data = [
            'is_deleted' => 1,
            'status' => 0
        ];
        
        $this->common_model->commonRecordUpdate('TEMP_calibrationProducts', 'id', $id, $data);
        echo json_encode(['success' => true]);
    }
}
?>
