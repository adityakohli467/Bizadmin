<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Redirect if not logged in
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $this->load->model('generalcomp_model');
        $this->load->model('task_model');
        $this->load->model('common_model');
        $this->load->model('config_model');
        $this->load->model('general_model');
        $this->load->model('prep_model');

        $this->tenantIdentifier    = $this->session->userdata('tenantIdentifier');
        $this->selected_location_id = $this->session->userdata('location_id');
    }

    public function index($system_id = '')
    {
        if (isset($system_id) && $system_id !== '') {
            $this->session->set_userdata('system_id', $system_id);
        }

        $data['todaysEnteredData'] = $this->generalcomp_model->fetchTodaysEnteredDataForIncomingGoods();
        $data['site_detail']       = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoodsSites',
            [],
            ['status' => 1, 'location_id' => $this->selected_location_id]
        );
        $data['prep_detail']       = $this->prep_model->fetchAllPrepArea(
            'Compliance_IncomingGoodsPrepArea',
            'Compliance_IncomingGoodsSites'
        );

        // Email configuration
        $emailSettings = $this->general_model->fetchSmtpSettings($this->selected_location_id, $system_id);

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
        }

        // Suppliers
        $where_conditions = [
            'is_deleted'  => 0,
            'location_id' => $this->selected_location_id
        ];
        $data['suppliers'] = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoodsproducts',
            '',
            $where_conditions
        );

        // Today's entered data from history
        $condition = [
            'date_entered' => date('Y-m-d'),
            'location_id'  => $this->selected_location_id
        ];

        $history_data = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoods_history',
            '',
            $condition
        );

        $todaysEnteredData = [];
        foreach ($history_data as $record) {
            $todaysEnteredData[$record['supplier_id']] = [
                'entered_by'   => $record['entered_by']   ?? '',
                'supplier_id'  => $record['supplier_id']  ?? '',
                'invoice_no'   => $record['invoice_no']   ?? '',
                'temp'         => $record['temp']         ?? '',
                'comments'     => $record['comments']     ?? '',
                'received_by'  => $record['received_by']  ?? '',
                'signature'    => $record['signature']    ?? ''
            ];
        }
        $data['todaysEnteredData'] = $todaysEnteredData;

        $this->load->view('general/header');
        $this->load->view('Goods/dashboard', $data);
        $this->load->view('general/footer');
    }

    public function saveDashboardData()
    {
        $supplier_id = $this->input->post('supplier');
        $field       = $this->input->post('field');
        $value       = $this->input->post('value');
        $prepId      = $this->input->post('prep');

        if (!$supplier_id || !$field) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $data = [
            'supplier_id'  => $supplier_id,
            'prep_id'      => $prepId,
            $field         => $value,
            'date_entered' => date('Y-m-d'),
            'location_id'  => $this->selected_location_id
        ];

        $conditionPr = [
            'supplier_id'  => $supplier_id,
            'date_entered' => date('Y-m-d')
        ];

        $exists = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoods_history',
            '',
            $conditionPr
        );

        if ($exists) {
            $this->common_model->commonRecordUpdate(
                'Compliance_IncomingGoods_history',
                'id',
                $exists[0]['id'],
                $data
            );
        } else {
            $this->common_model->commonRecordCreate('Compliance_IncomingGoods_history', $data);
        }

        echo json_encode(['status' => 'success']);
    }

    public function history()
    {
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoodsSites',
            '',
            ['status' => 1, 'location_id' => $this->selected_location_id]
        );

        $this->load->view('general/header');
        $this->load->view('Goods/history', $data);
        $this->load->view('general/footer');
    }

    public function historyData($encodedDateRange = '', $prep_id = '')
    {
        if ($encodedDateRange === '' && $prep_id === '') {
            $dateRange = $this->input->post('date_range');
            $prep_id   = $this->input->post('site_id');
        } else {
            $dateRange = urldecode($encodedDateRange);
        }

        $dateParts = explode(" to ", $dateRange);
        if (count($dateParts) !== 2) {
            show_error('Invalid date range format');
            return;
        }

        $fromDate = date('Y-m-d', strtotime(trim($dateParts[0])));
        $toDate   = date('Y-m-d', strtotime(trim($dateParts[1])));

        // Generate date range array
        $uniqueDates = [];
        $currentDate = $fromDate;
        while ($currentDate <= $toDate) {
            $uniqueDates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        $data['site_detail'] = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoodsSites',
            '',
            ['location_id' => $this->selected_location_id]
        );

        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoodsPrepArea',
            '',
            ['location_id' => $this->selected_location_id]
        );

        $condition = [
            'status'      => 1,
            'is_deleted'  => 0,
            'location_id' => $this->selected_location_id
        ];
        $data['suppliers'] = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoodsproducts',
            '',
            $condition
        );

        // History data
        $condition = [
            'date_entered >=' => $fromDate,
            'date_entered <=' => $toDate,
            'location_id'     => $this->selected_location_id
        ];

        if ($prep_id !== '') {
            $condition['prep_id'] = $prep_id;
        }

        $history_data = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoods_history',
            '',
            $condition
        );

        // Restructure by date → supplier_id
        $weeklyHistoryData = [];
        foreach ($history_data as $item) {
            $date = $item['date_entered'];
            $sid  = $item['supplier_id'];

            if (!isset($weeklyHistoryData[$date])) {
                $weeklyHistoryData[$date] = [];
            }
            $weeklyHistoryData[$date][$sid] = $item;
        }

        $data['uniqueDates']     = $uniqueDates;
        $data['dateRange']       = $dateRange;
        $data['site_id']         = $prep_id;
        $data['weeklyHistoryData'] = $weeklyHistoryData;

        // Only admin and manager can edit history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        $data['can_edit_history'] = $this->ion_auth->is_admin() || strtolower($roleName) === 'manager';

        $this->load->view('general/header');
        $this->load->view('Goods/historyDetails', $data);
        $this->load->view('general/footer');
    }

    /**
     * Main syntax errors were here:
     *   - extra comma in array
     *   - wrong method name commonRecordUpdateMultipleConditions (probably should be commonRecordUpdate)
     */
    public function updateHistory()
    {
        // Only admin and manager can update history
        $roleName = get_logged_in_user_role($this->ion_auth, 'name');
        if (!$this->ion_auth->is_admin() && strtolower($roleName) !== 'manager') {
            echo json_encode(['status' => 'error', 'message' => 'You do not have permission to edit history data']);
            return;
        }

        $post = $this->input->post();

        $supplier_id  = $post['supplier_id']  ?? null;
        $date_entered = $post['date_entered'] ?? null;
        $prep_id      = $post['prep_id']      ?? null;
        $location_id  = $post['location_id']  ?? null;

        if (empty($supplier_id) || empty($date_entered) || empty($prep_id) || empty($location_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            return;
        }

        $condition = [
            'supplier_id'  => $supplier_id,
            'date_entered' => $date_entered,
            'prep_id'      => $prep_id,
            'location_id'  => $location_id
        ];

        $update_data = [];

        foreach ([
            'supplier_name', 'invoice_no', 'temp', 'comments',
            'received_by', 'signature', 'wasteM_value', 'entered_by'
        ] as $field) {
            if (isset($post[$field]) && $post[$field] !== '') {
                $update_data[$field] = $post[$field];
            }
        }

        if (empty($update_data)) {
            echo json_encode(['status' => 'error', 'message' => 'No valid fields provided for update']);
            return;
        }

        $exists = $this->common_model->fetchRecordsDynamically(
            'Compliance_IncomingGoods_history',
            '',
            $condition
        );

        if (!empty($exists)) {
            // Update existing record
            $this->common_model->commonRecordUpdate(
                'Compliance_IncomingGoods_history',
                'id',
                $exists[0]['id'],
                $update_data
            );
            $response = ['status' => 'success', 'message' => 'Record updated successfully'];
        } else {
            // Create new record
            $data = array_merge($condition, $update_data);
            $insert_id = $this->common_model->commonRecordCreate('Compliance_IncomingGoods_history', $data);

            $response = $insert_id
                ? ['status' => 'success', 'message' => 'Record inserted successfully']
                : ['status' => 'error', 'message' => 'Failed to insert record'];
        }

        echo json_encode($response);
    }

    // ────────────────────────────────────────────────────────────────────────────────
    //  Suppliers / Products management
    // ────────────────────────────────────────────────────────────────────────────────

    public function listSuppliers()
    {
        $condition = [
            'status'      => 1,
            'location_id' => $this->selected_location_id
        ];

        $data['suplliers']   = $this->common_model->fetchRecordsDynamically('Compliance_IncomingGoodsproducts', '', $condition);
        $data['site_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_IncomingGoodsSites', '', $condition);
        $data['prep_detail'] = $this->common_model->fetchRecordsDynamically('Compliance_IncomingGoodsPrepArea', '', $condition);

        $this->load->view('general/header');
        $this->load->view('Goods/listSuppliers', $data);
        $this->load->view('general/footer');
    }

    public function addOrUpdateProduct()
    {
        $id = $this->input->post('id');

        $data = [
            'supplier_name' => $this->input->post('supplier_name') ?? null,
            'prep_id'       => $this->input->post('prep_id')       ?? null
        ];

        if ($id) {
            $this->common_model->commonRecordUpdate('Compliance_IncomingGoodsproducts', 'id', $id, $data);
        } else {
            $this->common_model->commonRecordCreate('Compliance_IncomingGoodsproducts', $data);
        }

        redirect('Compliance/Goods/home/listSuppliers');
    }

    public function getProductById($id)
    {
        $condition = ['id' => $id];
        $product = $this->common_model->fetchRecordsDynamically('Compliance_IncomingGoodsproducts', '', $condition);
        echo json_encode($product);
    }

    public function delete()
    {
        $id = $this->input->post('id', TRUE);
        $this->common_model->softDeleteRecord('Compliance_IncomingGoodsproducts', 'id', $id);
        echo "success";
    }
}