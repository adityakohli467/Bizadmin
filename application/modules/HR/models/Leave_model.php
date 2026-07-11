<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_model extends CI_Model {

    public function __construct() {
        parent::__construct();

        // Prevent $this->tenantDb being null
        if (!isset($this->tenantDb)) {
            log_message('error', 'tenantDb is NOT loaded in Leave_model. Load it in MY_Controller.');
        }
    }

    /* -----------------------------------------------
     * Safe DB Helper: Handle Boolean DB Errors
     * ----------------------------------------------- */
    private function safe_result_array($query) {
        if (!$query || $query === false) {
            log_message('error', 'Database error: ' . $this->tenantDb->last_query());
            return [];
        }
        return $query->result_array();
    }

    private function safe_row_array($query) {
        if (!$query || $query === false) {
            log_message('error', 'Database error: ' . $this->tenantDb->last_query());
            return [];
        }
        return $query->row_array();
    }

    /* -----------------------------------------------
     * Get Leave Requests (List)
     * ----------------------------------------------- */
    public function get_leave_requests($location_id = null, $status = null, $limit = 50, $offset = 0) {

        try {
            $this->tenantDb->select('hlm.*, hl.leaveTypeName, e.first_name, e.last_name, e.preferred_name, e.email')
                ->from('HR_leave_management hlm')
                ->join('HR_leaves hl', 'hl.id = hlm.leave_type', 'left')
                ->join('HR_employee e', 'e.emp_id = hlm.emp_id', 'left')
                ->where('hlm.leave_status !=', 0);

            if ($location_id) {
                $this->tenantDb->where('hlm.location_id', (int)$location_id);
            }
            if ($status !== null) {
                $this->tenantDb->where('hlm.leave_status', (int)$status);
            }

            $this->tenantDb->order_by('hlm.date_added', 'DESC');
            $this->tenantDb->limit((int)$limit, (int)$offset);

            $q = $this->tenantDb->get();
            return $this->safe_result_array($q);

        } catch (Exception $e) {
            log_message('error', 'get_leave_requests failed: ' . $e->getMessage());
            return [];
        }
    }

    /* -----------------------------------------------
     * Summary Counts
     * ----------------------------------------------- */
    public function get_leave_summary($location_id = null) {

        try {
            $sql = "SELECT
                        SUM(hlm.leave_status = 1) AS pending,
                        SUM(hlm.leave_status = 2) AS approved,
                        SUM(hlm.leave_status = 3) AS rejected,
                        SUM(hlm.start_date >= CURDATE() 
                            AND hlm.start_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)) AS upcoming
                    FROM HR_leave_management hlm
                    WHERE hlm.leave_status != 0";

            if ($location_id) {
                $sql .= " AND hlm.location_id = " . (int)$location_id;
            }

            $q = $this->tenantDb->query($sql);
            return $this->safe_row_array($q);

        } catch (Exception $e) {
            log_message('error', 'get_leave_summary failed: ' . $e->getMessage());
            return [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'upcoming' => 0
            ];
        }
    }

    /* -----------------------------------------------
     * Get Leave Details by ID
     * ----------------------------------------------- */
    public function get_leave_by_id($id) {

        try {
            $this->tenantDb->select('hlm.*, hl.leaveTypeName, e.first_name, e.last_name, e.preferred_name, e.email')
                ->from('HR_leave_management hlm')
                ->join('HR_leaves hl', 'hl.id = hlm.leave_type', 'left')
                ->join('HR_employee e', 'e.emp_id = hlm.emp_id', 'left')
                ->where('hlm.id', (int)$id)
                ->limit(1);

            $q = $this->tenantDb->get();
            return $this->safe_row_array($q);

        } catch (Exception $e) {
            log_message('error', 'get_leave_by_id failed: ' . $e->getMessage());
            return [];
        }
    }

    /* -----------------------------------------------
     * Approve Leave
     * ----------------------------------------------- */
    public function approve_leave($id, $approver_id, $comment = '') {
        return $this->set_leave_status($id, 2, $approver_id, $comment);
    }

    /* -----------------------------------------------
     * Reject Leave
     * ----------------------------------------------- */
    public function reject_leave($id, $approver_id, $comment) {

        if (trim($comment) === '') {
            log_message('error', 'Reject leave failed: Comment required');
            return false;
        }

        return $this->set_leave_status($id, 3, $approver_id, $comment);
    }

    /* -----------------------------------------------
     * Shared status updater. Only writes optional
     * approver columns when they exist in the schema,
     * so it works regardless of migration state.
     * ----------------------------------------------- */
    private function set_leave_status($id, $status, $approver_id, $comment) {

        try {
            $update = ['leave_status' => (int)$status];

            if ($this->tenantDb->field_exists('approver_id', 'HR_leave_management')) {
                $update['approver_id'] = (int)$approver_id;
            }
            if ($this->tenantDb->field_exists('approver_comment', 'HR_leave_management')) {
                $update['approver_comment'] = $comment;
            }
            if ($this->tenantDb->field_exists('approved_date', 'HR_leave_management')) {
                $update['approved_date'] = date('Y-m-d H:i:s');
            }

            $ok = $this->tenantDb->where('id', (int)$id)
                ->update('HR_leave_management', $update);

            return $ok !== false;

        } catch (Exception $e) {
            log_message('error', 'set_leave_status failed: ' . $e->getMessage());
            return false;
        }
    }

    /* -----------------------------------------------
     * Leaves (pending or approved) overlapping a date range.
     * Used to flag employees on leave in a roster week.
     * ----------------------------------------------- */
    public function get_leaves_in_range($start, $end, $location_id = null) {

        try {
            $this->tenantDb->select('emp_id, start_date, end_date, leave_status')
                ->from('HR_leave_management')
                ->where('leave_status !=', 0)   // not cancelled
                ->where('leave_status !=', 3)   // not rejected
                ->where('start_date <=', $end)
                ->where('end_date >=', $start);

            if ($location_id) {
                $this->tenantDb->where('location_id', (int)$location_id);
            }

            $this->tenantDb->order_by('start_date', 'ASC');

            $q = $this->tenantDb->get();
            return $this->safe_result_array($q);

        } catch (Exception $e) {
            log_message('error', 'get_leaves_in_range failed: ' . $e->getMessage());
            return [];
        }
    }

    /* -----------------------------------------------
     * Employee's own leave history (all statuses except cancelled)
     * ----------------------------------------------- */
    public function get_employee_leaves($emp_id) {

        try {
            $this->tenantDb->select('hlm.*, hl.leaveTypeName')
                ->from('HR_leave_management hlm')
                ->join('HR_leaves hl', 'hl.id = hlm.leave_type', 'left')
                ->where('hlm.emp_id', (int)$emp_id)
                ->where('hlm.leave_status !=', 0)
                ->order_by('hlm.date_added', 'DESC');

            $q = $this->tenantDb->get();
            return $this->safe_result_array($q);

        } catch (Exception $e) {
            log_message('error', 'get_employee_leaves failed: ' . $e->getMessage());
            return [];
        }
    }

    /* -----------------------------------------------
     * Aggregated leave stats for one employee
     * ----------------------------------------------- */
    public function get_employee_leave_stats($emp_id) {

        $default = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'approved_days' => 0];

        try {
            $sql = "SELECT
                        COUNT(*) AS total,
                        SUM(leave_status = 1) AS pending,
                        SUM(leave_status = 2) AS approved,
                        SUM(leave_status = 3) AS rejected,
                        COALESCE(SUM(CASE WHEN leave_status = 2
                            THEN DATEDIFF(end_date, start_date) + 1 ELSE 0 END), 0) AS approved_days
                    FROM HR_leave_management
                    WHERE emp_id = " . (int)$emp_id . "
                      AND leave_status != 0";

            $q = $this->tenantDb->query($sql);
            $row = $this->safe_row_array($q);

            return array_merge($default, array_map('intval', $row ?: []));

        } catch (Exception $e) {
            log_message('error', 'get_employee_leave_stats failed: ' . $e->getMessage());
            return $default;
        }
    }

    /* -----------------------------------------------
     * Employee Leave Balance
     * ----------------------------------------------- */
    public function get_employee_leave_balance($emp_id) {

        try {
            $balances_query = $this->tenantDb->select('hl.id AS leave_type_id, hl.leaveTypeName, hl.entitlements')
                ->from('HR_leaves hl')
                ->get();

            $balances = $this->safe_result_array($balances_query);

            foreach ($balances as &$b) {

                $q = $this->tenantDb->select('SUM(DATEDIFF(end_date, start_date) + 1) AS used_days')
                    ->from('HR_leave_management')
                    ->where('emp_id', (int)$emp_id)
                    ->where('leave_type', (int)$b['leave_type_id'])
                    ->where('leave_status', 2)
                    ->get();

                $row = $this->safe_row_array($q);

                $b['used_days'] = isset($row['used_days']) ? (int)$row['used_days'] : 0;
                $b['remaining'] = max(0, (float)$b['entitlements'] - (float)$b['used_days']);
            }

            return $balances;

        } catch (Exception $e) {
            log_message('error', 'get_employee_leave_balance failed: ' . $e->getMessage());
            return [];
        }
    }

}
?>
