<?php
class Sitecalib extends MY_Controller
{
    public function __construct() 
    {   
        parent::__construct();
        $this->load->model('Calibrationtemp/site_model');
        $this->load->model('common_model');
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $this->selected_location_id = $this->session->userdata('location_id');
    }

    public function index(){
        $data['site_detail'] = $this->site_model->get_all_sites($this->selected_location_id); 
        $this->load->view('general/header');
        $this->load->view('CalibrationTemp/site/siteList', $data);
        $this->load->view('general/footer');
    }
	
    public function add(){
        if(isset($this->POST['site_name'])){
            $site_data = array(
                'site_name' => $this->POST['site_name'],
                'staff_comments' => serialize($this->POST['staff_comments']),
                'manager_comments' => serialize($this->POST['manager_comments']),
                'status'=> 1,
                'location_id' => $this->session->userdata('location_id'),
                'created_at' => date('Y-m-d'),
            );
            $this->common_model->commonRecordCreate('TEMP_calibrationSites', $site_data);
            redirect('Temp/calibrationTemp/site', 'refresh');
        }else{
            $data['form_type'] = 'add';   
            $this->load->view('general/header');
            $this->load->view('CalibrationTemp/site/siteAdd', $data);
            $this->load->view('general/footer');
        }
    }

    public function edit($site_id=''){
        if(isset($this->POST['site_name'])){
            $site_data = array(
                'site_name' => $this->POST['site_name'],
                'staff_comments' => serialize($this->POST['staff_comments']),
                'manager_comments' => serialize($this->POST['manager_comments']),
                'updated_date' => date('Y-m-d'),
            );
            $this->common_model->commonRecordUpdate('TEMP_calibrationSites', 'id', $site_id, $site_data);
            redirect('Temp/calibrationTemp/site', 'refresh');
        }else{
            $data['site_detail'] = $this->site_model->get_all_sites($this->selected_location_id, $site_id);    
            $data['form_type'] = 'edit';   
            $this->load->view('general/header');
            $this->load->view('CalibrationTemp/site/siteAdd', $data);
            $this->load->view('general/footer');
        }
    }

    function change_status(){
        $this->site_model->change_status($this->POST);
    }

    public function delete(){
        $res = $this->site_model->deletesite($this->POST['id']);
        echo $res;
    }
}
