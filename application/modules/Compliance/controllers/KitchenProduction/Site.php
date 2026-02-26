<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends MY_Controller
{
    public function __construct() 
    {   
        parent::__construct();
        $this->load->model('common_model');
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $this->selected_location_id = $this->session->userdata('location_id');
    }
    
    public function index()
    {
        $condition = ['status' => 1, 'location_id' => $this->selected_location_id, 'is_deleted' => 0];
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', $condition);
        $this->load->view('general/header');
        $this->load->view('KitchenProduction/siteList', $data);
        $this->load->view('general/footer');
    }
    
    public function add()
    {
        if(isset($this->POST['site_name'])){
            $site_data = array(
                'site_name' => $this->POST['site_name'],
                'status' => 1,
                'location_id' => $this->session->userdata('location_id'),
                'created_at' => date('Y-m-d'),
            );
            
            // Handle staff comments if provided
            if(isset($this->POST['staff_comments'])) {
                $site_data['staff_comments'] = serialize(array_filter($this->POST['staff_comments']));
            }
            
            $this->common_model->commonRecordCreate('Compliance_KitchenProductionsites', $site_data);
            redirect('Compliance/KitchenProduction/Site', 'refresh');
        } else {
            $data['form_type'] = 'add';   
            $this->load->view('general/header');
            $this->load->view('KitchenProduction/siteAdd', $data);
            $this->load->view('general/footer');
        }
    }
    
    public function edit($site_id='')
    {
        if(isset($this->POST['site_name'])){
            $site_data = array(
                'site_name' => $this->POST['site_name'],
                'updated_date' => date('Y-m-d'),
            );
            
            // Handle staff comments if provided
            if(isset($this->POST['staff_comments'])) {
                $site_data['staff_comments'] = serialize(array_filter($this->POST['staff_comments']));
            }

            $this->common_model->commonRecordUpdate('Compliance_KitchenProductionsites', 'id', $site_id, $site_data);
            redirect('Compliance/KitchenProduction/Site', 'refresh');
        } else {
            $condition = ['status' => 1, 'location_id' => $this->selected_location_id, 'id' => $site_id];
            $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', $condition);
            $data['form_type'] = 'edit';   
            $this->load->view('general/header');
            $this->load->view('KitchenProduction/siteAdd', $data);
            $this->load->view('general/footer');
        }
    }
    
    function change_status()
    {
        $site_data['status'] = $this->POST['status'];
        $this->common_model->commonRecordUpdate('Compliance_KitchenProductionsites', 'id', $this->POST['id'], $site_data);
        echo "success";
    }
   
    public function delete()
    {
        $site_data['is_deleted'] = 1;
        $this->common_model->commonRecordUpdate('Compliance_KitchenProductionsites', 'id', $this->POST['id'], $site_data);
        echo "success";
    }
}
?>
