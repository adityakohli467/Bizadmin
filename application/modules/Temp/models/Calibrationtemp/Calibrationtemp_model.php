<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calibrationtemp_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->selected_location_id = $this->session->userdata('location_id');
    }

    private function safe_result($query) {
        if (!$query) {
            log_message('error', 'DB Error in ' . __FUNCTION__ . ': ' . $this->tenantDb->last_query());
            log_message('error', $this->tenantDb->error()['message']);
            return [];
        }
        return $query->result_array();
    }

    public function get_allSitesForDash($siteId = '') {
        $this->tenantDb->select('TEMP_calibrationSites.*, JSON_ARRAYAGG(JSON_OBJECT("id", TEMP_calibrationPrepArea.id, "prep_name", TEMP_calibrationPrepArea.prep_name)) as prep_areas', false);
        $this->tenantDb->from('TEMP_calibrationSites');
        $this->tenantDb->join('TEMP_calibrationPrepArea', 'TEMP_calibrationPrepArea.site_id = TEMP_calibrationSites.id', 'inner');
        $this->tenantDb->group_by('TEMP_calibrationSites.id')
            ->where('TEMP_calibrationSites.location_id', $this->selected_location_id)
            ->where('TEMP_calibrationSites.is_deleted', 0)
            ->where('TEMP_calibrationPrepArea.is_deleted', 0)
            ->where('TEMP_calibrationSites.status', 1);
        if ($siteId != '') {
            $this->tenantDb->where('TEMP_calibrationSites.id', $siteId);  
        }
        $query = $this->tenantDb->get();
        return $this->safe_result($query);
    }

    public function get_allProducts() {
        $this->tenantDb->select('id, product_name, prep_id, status');
        $this->tenantDb->from('TEMP_calibrationProducts');
        $this->tenantDb->where('status', 1);
        $this->tenantDb->where('is_deleted', 0);
        $query = $this->tenantDb->get();
        return $this->safe_result($query);
    }

    public function fetchTodaysEnteredCalibrationData() {
        $this->tenantDb->select('id, product_id, ice_point_temp, boiling_point_temp, corrective_action, calibrated_by, site_id, prep_id, location_id, date_entered');
        $this->tenantDb->from('TEMP_calibrationRecordHistory');
        $this->tenantDb->where('date_entered', date('Y-m-d'));
        $this->tenantDb->where('location_id', $this->selected_location_id);
        $this->tenantDb->order_by('id', 'ASC');
        $query = $this->tenantDb->get();
        return $this->safe_result($query);
    }

    public function fetchCalibrationHistoryData($fromDate, $toDate, $site_id) {
        $this->tenantDb->distinct();
        $this->tenantDb->select('h.id, h.product_id, p.product_name, h.ice_point_temp, h.boiling_point_temp, h.corrective_action, h.calibrated_by, h.site_id, h.prep_id, h.location_id, h.date_entered');
        $this->tenantDb->from('TEMP_calibrationRecordHistory h');
        $this->tenantDb->join('TEMP_calibrationProducts p', 'h.product_id = p.id', 'LEFT');
        $this->tenantDb->where('h.location_id', $this->selected_location_id);
        $this->tenantDb->where('h.site_id', $site_id);
        $this->tenantDb->where('h.date_entered >=', $fromDate);
        $this->tenantDb->where('h.date_entered <=', $toDate);
        $query = $this->tenantDb->get();
        return $this->safe_result($query);
    }
}
?>
