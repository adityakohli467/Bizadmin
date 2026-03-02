<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Site_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function add_site($data) {
        return $this->tenantDb->insert('TEMP_calibrationSites', $data);
    }

    function change_status($data) {
        $Newdata = array(
            'status' => $data['status'],
            'updated_date' => date("Y-m-d")
        );
        $this->tenantDb->set($Newdata);
        $this->tenantDb->where('id', $data['id']);
        $this->tenantDb->update('TEMP_calibrationSites');
        return true;
    }

    function deletesite($id) {
        $data = array(
            'is_deleted' => 1,
            'updated_date' => date("Y-m-d")
        );
        $this->tenantDb->set($data);
        $this->tenantDb->where('id', $id);
        $this->tenantDb->update('TEMP_calibrationSites');
        return true;
    }

    public function get_all_sites($location_id = '', $site_id = '') {
        $where_conditions = array(
            'is_deleted' => 0,
            'location_id' => $location_id
        );
        if ($site_id != '') {
            $where_conditions['id'] = $site_id;
        }
        $sites = $this->tenantDb->select('*')
            ->where($where_conditions)
            ->order_by("created_at", "asc")
            ->get('TEMP_calibrationSites')->result_array();
        return $sites;
    }

    public function get_site_by_id($id) {
        $this->tenantDb->select('id, site_name, created_at');
        $this->tenantDb->where('id', $id);
        return $this->tenantDb->get('TEMP_calibrationSites')->result_array();
    }

    public function update_site($data, $id) {
        $this->tenantDb->set($data);
        $this->tenantDb->where('id', $id);
        $this->tenantDb->update('TEMP_calibrationSites');
        return true;
    }
}
