<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    private $location_id;

    public function __construct()
    {
        parent::__construct();
        $this->location_id = $this->session->userdata('location_id');
        ini_set('display_errors', 1); ini_set('display_startup_errors', 1);error_reporting(E_ALL);
    }

    /* --------------------------------------
       BIRTHDAYS
    ---------------------------------------*/
    public function get_todays_birthdays()
    {
        $today = date('m-d');

        $this->tenantDb->select("emp_id, first_name, last_name, preferred_name, dob");
        $this->tenantDb->from("HR_employee");
        $this->tenantDb->where("location_id", $this->location_id);
        $this->tenantDb->where("DATE_FORMAT(dob,'%m-%d') =", $today);

        return $this->safeResult();
    }

    /* --------------------------------------
       LEAVE REQUESTS (Pending Only)
    ---------------------------------------*/
    public function get_pending_leaves()
    {
        $this->tenantDb->select("lm.*, e.first_name, e.last_name");
        $this->tenantDb->from("HR_leave_management lm");
        $this->tenantDb->join("HR_employee e", "e.emp_id = lm.emp_id", "left");
        $this->tenantDb->where("lm.location_id", $this->location_id);
        $this->tenantDb->where("lm.leave_status", 1); // pending

        return $this->safeResult();
    }

    /* --------------------------------------
       TASKS SUMMARY for current day
    ---------------------------------------*/
    public function get_task_summary()
    {
        $today = date('Y-m-d');

        $this->tenantDb->where("status", 1)->where("date", $today);
        $completed = $this->safeCount("hr_task_daily_status");

        $this->tenantDb->where("status", 0)->where("date", $today);
        $inProgress = $this->safeCount("hr_task_daily_status");

        return [
            "completed_today" => $completed,
            "in_progress"     => $inProgress,
        ];
    }

    /* --------------------------------------
       TIMESHEETS PENDING APPROVAL
    ---------------------------------------*/
    public function get_pending_timesheets_count()
    {
        $this->tenantDb->where("approval_status", "pending")
                       ->where("location_id", $this->location_id);
        return $this->safeCount("HR_timesheet_details");
    }

    /* --------------------------------------
       TODAYS ATTENDANCE (CLOCKED-IN EMPLOYEES)
    ---------------------------------------*/
    public function get_today_attendance()
    {
        $today = date('Y-m-d');

        $this->tenantDb->select("t.*, e.first_name, e.last_name, p.prep_name");
        $this->tenantDb->from("HR_timesheet_details t");
        $this->tenantDb->join("HR_employee e", "e.emp_id = t.employee_id", "left");
        $this->tenantDb->join("HR_prepArea p", "p.id = t.prep_area_id", "left");
        $this->tenantDb->where("t.roster_date", $today);
        $this->tenantDb->where("t.location_id", $this->location_id);
        $this->tenantDb->order_by("t.clock_in_time", "ASC");

        return $this->safeResult();
    }

    /* --------------------------------------
       PRESENT TODAY COUNT
    ---------------------------------------*/
    public function get_present_today_count()
    {
        $today = date('Y-m-d');

        $this->tenantDb->where("clock_in_time IS NOT NULL", NULL, FALSE)
                       ->where("roster_date", $today)
                       ->where("location_id", $this->location_id);
        return $this->safeCount("HR_timesheet_details");
    }
    
     /* --------------------------------------
       EMPLOYEE ON BREAK COUNT
    ---------------------------------------*/
 public function get_employee_on_break_count()
{
    $today = date('Y-m-d');
    
    $query = $this->tenantDb
        ->select('tb.timesheet_id')
        ->from('HR_timesheet_breaks tb')
        ->join('HR_timesheet_details ht', 'ht.timesheet_id = tb.timesheet_id', 'left')
        ->where('ht.roster_date', $today)
        ->where('ht.location_id', $this->location_id)
        ->where("tb.break_start_time !=", '')
        ->where("tb.break_start_time IS NOT NULL", NULL, FALSE)
        ->group_start()
            ->where("tb.break_end_time IS NULL", NULL, FALSE)
            ->or_where("tb.break_end_time", '')
        ->group_end()
        ->group_by('tb.timesheet_id')
        ->get();
    
    if ($query) {
        return $query->num_rows();
    }
    
    return 0;
}


    /* --------------------------------------
       TOTAL EMPLOYEES in todays timesheet
    ---------------------------------------*/
    public function get_total_employees_today()
{
    $today = date('Y-m-d');

    $this->tenantDb
                ->distinct()
                ->select("employee_id")
                ->where("roster_date", $today)
                ->where("location_id", $this->location_id);
    return $this->safeCount("HR_timesheet_details");
}


    /* --------------------------------------
       INCIDENT REPORTS
    ---------------------------------------*/
    public function get_injury_reports()
    {
        $today = date('Y-m-d');

        $this->tenantDb->select("i.*, e.first_name, e.last_name");
        $this->tenantDb->from("HR_Injury_Report i");
        $this->tenantDb->join("HR_employee e", "e.emp_id = i.emp_id", "left");
        $this->tenantDb->where("i.location_id", $this->location_id);
        $this->tenantDb->where("i.date_added", $today);

        return $this->safeResult();
    }
    
    public function get_incident_reports()
    {
        $today = date('Y-m-d');

        $this->tenantDb->select("i.*, e.first_name, e.last_name");
        $this->tenantDb->from("HR_Incident_Report i");
        $this->tenantDb->join("HR_employee e", "e.emp_id = i.emp_id", "left");
        $this->tenantDb->where("i.location_id", $this->location_id);
        $this->tenantDb->where("i.date_added", $today);

        return $this->safeResult();
    }
    
    // get total hour of teams for todays date
    public function get_total_team_hours()
     {
    $today = date('Y-m-d');

    $this->tenantDb->select("clock_in_time, clock_out_time, roster_break_duration, actual_break_duration");
    $this->tenantDb->from("HR_timesheet_details");
    $this->tenantDb->where("roster_date", $today);
    $this->tenantDb->where("location_id", $this->location_id);

    $rows = $this->safeResult();

    $totalSeconds = 0;
    $now = time();

    foreach ($rows as $r) {

        // Skip if no clock in
        if (!$r->clock_in_time) {
            continue;
        }

        $in  = strtotime($r->clock_in_time);
        $out = !empty($r->clock_out_time) ? strtotime($r->clock_out_time) : $now;

        // Basic worked seconds
        $worked = max(0, $out - $in);

        // Determine break minutes
        $breakMin = 0;

        if (!empty($r->actual_break_duration)) {
            $breakMin = (int) $r->actual_break_duration;
        } elseif (!empty($r->roster_break_duration)) {
            $breakMin = (int) $r->roster_break_duration;
        }

        $worked -= ($breakMin * 60);

        $totalSeconds += max(0, $worked);
    }

    // Convert to hours with 1 decimal, e.g. 184.5h
    $hours = round($totalSeconds / 3600, 1);

    return $hours;
}

    /* --------------------------------------
       SAFE QUERY HELPERS
       Guard against a failed query returning FALSE (e.g. a missing table
       for a tenant), which otherwise causes fatal errors such as
       "Call to a member function num_rows()/result() on bool".
       On any failure they log the issue and return an empty/zero result
       so the dashboard renders gracefully instead of crashing.
    ---------------------------------------*/

    /** Run the queued query and return its result rows, or [] on failure. */
    private function safeResult()
    {
        try {
            $query = $this->tenantDb->get();
            return ($query !== FALSE) ? $query->result() : [];
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard_model::safeResult failed: ' . $e->getMessage());
            $this->tenantDb->reset_query();
            return [];
        }
    }

    /** Count rows for the given table using the queued conditions, or 0 on failure. */
    private function safeCount($table)
    {
        try {
            if (!$this->tenantDb->table_exists($table)) {
                $this->tenantDb->reset_query();
                return 0;
            }
            return (int) $this->tenantDb->from($table)->count_all_results();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard_model::safeCount failed on ' . $table . ': ' . $e->getMessage());
            $this->tenantDb->reset_query();
            return 0;
        }
    }

}
