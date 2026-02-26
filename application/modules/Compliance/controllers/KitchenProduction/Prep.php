<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prep extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('common_model');
        $this->load->model('site_model');
        $this->load->model('prep_model');
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        $this->POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $this->selected_location_id = $this->session->userdata('location_id');
    }

    public function index()
    {
        $condition = ['status' => 1, 'location_id' => $this->selected_location_id, 'is_deleted' => 0];
        $data['prep_detail'] = $this->prep_model->fetchAllPrepArea('Compliance_KitchenProductionPrepArea', 'Compliance_KitchenProductionsites');
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', ['status' => 1, 'location_id' => $this->selected_location_id, 'is_deleted' => 0]);
        $this->load->view('general/header');
        $this->load->view('KitchenProduction/prepList', $data);
        $this->load->view('general/footer');
    }

    public function add()
    {
        if (isset($this->POST['prep_name'])) {
            $prep_data = array(
                'prep_name' => $this->POST['prep_name'],
                'site_id' => (isset($this->POST['site_id']) ? $this->POST['site_id'] : ''),
                'status' => 1,
                'location_id' => $this->session->userdata('location_id'),
                'created_at' => date('Y-m-d'),
            );
            $this->common_model->commonRecordCreate('Compliance_KitchenProductionPrepArea', $prep_data);
            redirect('Compliance/KitchenProduction/Prep', 'refresh');
        } else {
            $data['form_type'] = 'add';
            $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', ['status' => 1, 'location_id' => $this->selected_location_id, 'is_deleted' => 0]);
            $this->load->view('general/header');
            $this->load->view('KitchenProduction/prepAdd', $data);
            $this->load->view('general/footer');
        }
    }

    public function edit($prep_id = '')
    {
        if (isset($this->POST['prep_name'])) {
            $prep_id = $this->POST['id'];
            $prep_data = array(
                'prep_name' => $this->POST['prep_name'],
                'site_id' => (isset($this->POST['site_id']) ? $this->POST['site_id'] : ''),
                'updated_date' => date('Y-m-d'),
            );
            $this->common_model->commonRecordUpdate('Compliance_KitchenProductionPrepArea', 'id', $prep_id, $prep_data);
            redirect('Compliance/KitchenProduction/Prep', 'refresh');
        } else {
            $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionPrepArea', '', ['id' => $prep_id, 'location_id' => $this->selected_location_id]);
            $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_KitchenProductionsites', '', ['status' => 1, 'location_id' => $this->selected_location_id, 'is_deleted' => 0]);
            $data['form_type'] = 'edit';
            $this->load->view('general/header');
            $this->load->view('KitchenProduction/prepAdd', $data);
            $this->load->view('general/footer');
        }
    }

    public function change_status()
    {
        $prep_data['status'] = $this->POST['status'];
        $this->common_model->commonRecordUpdate('Compliance_KitchenProductionPrepArea', 'id', $this->POST['id'], $prep_data);
        echo "success";
    }

    public function updateSortOrder()
    {
        $newOrder = $this->input->post('order');
        foreach ($newOrder as $index => $itemId) {
            $prepID = substr($itemId, 4);
            $this->tenantDb->set('sort_order', $index + 1);
            $this->tenantDb->where('id', $prepID);
            $this->tenantDb->update('Compliance_KitchenProductionPrepArea');
        }
        echo "success";
    }

    public function delete()
    {
        $prep_data['is_deleted'] = 1;
        $this->common_model->commonRecordUpdate('Compliance_KitchenProductionPrepArea', 'id', $this->POST['id'], $prep_data);
        echo "success";
    }
}
