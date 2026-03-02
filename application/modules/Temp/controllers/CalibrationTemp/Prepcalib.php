<?php

class Prepcalib extends MY_Controller
{
    public function __construct() 
    {   
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->load->model('general_model');
        $this->load->model('Calibrationtemp/prep_model', 'prep_model');
        $this->load->model('common_model');
        $this->load->model('Calibrationtemp/site_model');
        $this->POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $this->selected_location_id = $this->session->userdata('location_id');
    }

    public function index(){
        $where_conditions = array('is_deleted' => 0, 'location_id' => $this->selected_location_id);
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('TEMP_calibrationSites', '', $where_conditions);
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea();
        $this->load->view('general/header');
        $this->load->view('CalibrationTemp/prep/prepList', $data);
        $this->load->view('general/footer');
    }

    public function add(){
        if(isset($this->POST['prep_name'])){
            $site_data = array(
                'prep_name' => $this->POST['prep_name'],
                'site_id' => (isset($this->POST['site_id']) ? $this->POST['site_id'] : ''),
                'status'=> 1,
                'location_id' => $this->session->userdata('location_id'),
                'created_at' => date('Y-m-d'),
            );
            $this->common_model->commonRecordCreate('TEMP_calibrationPrepArea', $site_data);	
            echo "success";
        }
    }

    public function edit($site_id=''){
        if(isset($this->POST['site_name'])){
            $site_data = array(
                'prep_name' => $this->POST['prep_name'],
                'site_id' => (isset($this->POST['site_id']) ? $this->POST['site_id'] : ''),
                'updated_date' => date('Y-m-d'),
            );
            $this->common_model->commonRecordUpdate('TEMP_calibrationPrepArea', 'id', $site_id, $site_data);
        }
    }

    function change_status(){
        $this->prep_model->change_status($this->POST);
    }

    function updatePrep(){
        $this->prep_model->updatePrep($this->POST, $this->POST['id']);
    }
	
    public function updateSortOrder(){
        $newOrder = $this->input->post('order');
        foreach ($newOrder as $index => $itemId) {
            $equipID = substr($itemId, 4);
            $this->tenantDb->set('sort_order', $index + 1);
            $this->tenantDb->where('id', $equipID);
            $this->tenantDb->update('TEMP_calibrationPrepArea');
        }
        echo "success";
    }

    public function delete(){
        $res = $this->prep_model->deletesite($this->POST['id']);
        echo $res;
    }
}
