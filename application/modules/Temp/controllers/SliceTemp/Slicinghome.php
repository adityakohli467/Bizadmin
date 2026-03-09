
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slicinghome extends MY_Controller {
    function __construct() {
        parent::__construct();
      
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->load->model('common_model');
        $this->load->model('temp_model');
        $this->load->model('Slicetemp/prep_model');
        $this->load->model('config_model');
        $this->load->model('general_model');
        $this->load->model('Slicetemp/slicingtemp_model');
        $this->selected_location_id = $this->session->userdata('location_id');
        $this->system_id = $this->session->userdata('system_id');
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        
         
    }
    
    function insertPasRecords() {
        // This for developer only to enter past data for chilling food
        // $start_date = '2024-01-01';
        // $end_date = '2024-11-27';
        // $this->slicingtemp_model->insertPastrecords($start_date, $end_date);
    }

    public function index($system_id = '') {
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
        $data['site_detail'] = $this->slicingtemp_model->get_allSitesForDash();
        // echo "<pre>"; print_r($data['site_detail']); exit;
        $condition = ['status' => 1];
        $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_slicingProducts','',$condition);
        $chillingTempConfigurationData = $this->config_model->getConfiguration('', 'chillingTemp');
        if (isset($chillingTempConfigurationData[0]['data']) && !empty($chillingTempConfigurationData[0]['data'])) {
            $chillingTempConfigurationData = unserialize($chillingTempConfigurationData[0]['data']);
            $data['minTempAtFinish'] = isset($chillingTempConfigurationData['tempAtFinishMin']) ? $chillingTempConfigurationData['tempAtFinishMin'] : '';
            $data['minTempAfterTwoHrs'] = isset($chillingTempConfigurationData['tempAfterTwoHrs']) ? $chillingTempConfigurationData['tempAfterTwoHrs'] : '';
            $data['minTempAfterFourHrs'] = isset($chillingTempConfigurationData['tempAfterFourHrs']) ? $chillingTempConfigurationData['tempAfterFourHrs'] : '';
        }
        $data['todaysSlicingTempData'] = $this->slicingtemp_model->fetchTodaysEnteredTempData();
        $data['exceededTempData'] = $this->slicingtemp_model->fetchExceededTempData();

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

        $emailSettings = $this->general_model->fetchSmtpSettings($this->selected_location_id, $this->system_id);
        if (empty($emailSettings)) {
            $emailSettings = $this->general_model->fetchSmtpSettings('9999', '9999');
            $this->configureSMTP($emailSettings);
        } else {
            if ($emailSettings->mail_protocol === 'smtp') {
                $this->configureSMTP($emailSettings);
            }
        }
        if (isset($emailSettings->mail_from)) {
            $this->session->set_userdata('mail_from', $emailSettings->mail_from);
        } else {
            $this->session->set_userdata('mail_from', 'info@bizadmin.com.au');
        }

        $this->load->view('general/header');
        $this->load->view('SliceTemp/dashboard', $data);
        $this->load->view('general/footer');
    }
    
      public function saveRecord()
     {
     

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

    // Check if a record already exists for this product
    $conditionPr = array('product_id'=>$product_id,'date_entered' => date('Y-m-d'));
    $exists = $this->common_model->fetchRecordsDynamically('TEMP_slicingTemprecordHistory','',$conditionPr);
   
    if ($exists) {
        $this->common_model->commonRecordUpdate('TEMP_slicingTemprecordHistory','id',$exists[0]['id'], $data);
    } else {
       
        $this->common_model->commonRecordCreate('TEMP_slicingTemprecordHistory',$data);
    }

    echo json_encode(['status' => 'success']);
}

    public function updateRecord()
     {
     // Only admin and manager can update history
     $roleName = get_logged_in_user_role($this->ion_auth, 'name');
     if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
         echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
         return;
     }

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

     $this->common_model->commonRecordUpdate('TEMP_slicingTemprecordHistory','id',$rowId, $data);

    echo json_encode(['status' => 'success']);
}


  

    public function updateExceededTemp() {
        $id = $this->input->post('id');
        $data['correctedTemp'] = $this->input->post('correctedTemp');
        $data['manager_comments'] = $this->input->post('manager_comments');
        $this->slicingtemp_model->updateExceededTemp($id, $data);
        echo json_encode(['status' => 'success']);
    }

    function tempCHistory() {
        $data['site_detail'] = $this->slicingtemp_model->get_allSitesForDash();
        $this->load->view('general/header');
        $this->load->view('SliceTemp/tempHistory', $data);
        $this->load->view('general/footer');
    }

    function historyChillingData($encodedDateRange = '', $site_id = '') {
     
        if ($encodedDateRange == '' && $site_id == '') {
            $dateRange = $this->input->post('date_range');
            $site_id = $this->input->post('site_id');
        } else {
            $dateRange = urldecode($encodedDateRange);
        }
        
       

        $data['site_detail'] = $this->slicingtemp_model->get_allSitesForDash($site_id);
      
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
            
            $data['weeklyTempData'] = $this->slicingtemp_model->fetchTempViewHistoryData($fromDate, $toDate, $site_id);
            // echo "<pre>"; print_r($data['weeklyTempData']); exit;
            
            // Only admin and manager can edit history
            $roleName = get_logged_in_user_role($this->ion_auth, 'name');
            $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';
            
            $this->load->view('general/header');
            $this->load->view('SliceTemp/tempHistoryDetails', $data);
            $this->load->view('general/footer');
        } else {
            echo "Invalid date range format";
        }
    }

    public function tempHistoryUpdateAlldata() {
    // Only admin and manager can update history
    $roleName = get_logged_in_user_role($this->ion_auth, 'name');
    if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
        return;
    }

    $id = $this->input->post('id');
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        return;
    }

    $data = [
        'foodName' => $this->input->post('foodName'),
        'internal_batch_code_allocated' => $this->input->post('internal_batch_code_allocated'),
        'start_slicing' => $this->input->post('start_slicing'),
        'time_finished_slicing' => $this->input->post('time_finished_slicing'),
        'temp_of_product_at_end_of_slicing' => $this->input->post('temp_of_product_at_end_of_slicing'),
        'time_chilling_process_started' => $this->input->post('time_chilling_process_started'),
        'time_chilling_process_finished' => $this->input->post('time_chilling_process_finished'),
        'temp_of_product_at_start_of_slicing' => $this->input->post('temp_of_product_at_start_of_slicing'),
        'comments' => $this->input->post('comments'),
        'entered_by' => $this->input->post('entered_by'),
        'signature' => $this->input->post('signature')
    ];

    // Remove empty or null values to avoid overwriting with defaults
    $data = array_filter($data, function($value) {
        return $value !== '' && $value !== null;
    });

    if (empty($data)) {
        echo json_encode(['status' => 'error', 'message' => 'No data provided for update']);
        return;
    }
   
    $result = $this->common_model->commonRecordUpdate('TEMP_slicingTemprecordHistory', 'id', $id, $data);
    echo json_encode(['status' => 'success']);
}

    function tempHistoryUpdatec() {
        if (!empty($_POST)) {
            foreach ($_POST as $siteprepAndrecordID => $updatedTempData) {
                if ($siteprepAndrecordID != 'dateRange' && $siteprepAndrecordID != 'site_id') {
                    $updatedTempDataString = explode('_', $siteprepAndrecordID);
                    $data['site_id'] = $updatedTempDataString[1] ?? '';
                    $data['prep_id'] = $updatedTempDataString[2] ?? '';
                    $data['id'] = $updatedTempDataString[3] ?? '';
                    $data['date_entered'] = $updatedTempDataString[4] ?? '';
                    if ($data['site_id'] != '' && $data['prep_id'] != '' && $data['id'] != '' && $data['date_entered'] != '') {
                        $data['temp_of_product_at_start_of_slicing'] = $updatedTempData;
                        $this->slicingtemp_model->updateExceededTemp($data['id'], $data);
                    }
                }
            }
        }
        $dateRange = $_POST['dateRange'];
        $siteId = $_POST['site_id'];
        $encodedDateRange = urlencode($dateRange);
        redirect('/Temp/home/chillinghistoryData/' . $encodedDateRange . '/' . $siteId);
    }
    
     function listProduct(){
         $condition = array('status' => 1);
        $data['products'] = $this->common_model->fetchRecordsDynamically('TEMP_slicingProducts','',$condition);
        $where_conditions = array('is_deleted' => 0, 'location_id' => $this->selected_location_id );
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('TEMP_slicingSites','',$where_conditions);
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea();
        $this->load->view('general/header');
        $this->load->view('SliceTemp/listProduct', $data);
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
            $this->common_model->commonRecordUpdate('TEMP_slicingProducts','id',$id, $data);
        } else {
            $this->common_model->commonRecordCreate('TEMP_slicingProducts',$data);
        }

        echo json_encode(['status' => 'success']);
    }
    
    public function getProductById($id) {
        $condition = array('id' => $id);
        $product = $this->common_model->fetchRecordsDynamically('TEMP_slicingProducts','',$condition);
        echo json_encode($product);
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
   
     public function deleteProduct() {
        $id = $this->input->post('id');
        $data = [
            'is_deleted' => 1,
            'status' => 1
        ];
        
         $this->common_model->commonRecordUpdate('TEMP_slicingProducts','id',$id, $data);
        echo json_encode(['success' => true]);
    
    }
    
   
}
?>