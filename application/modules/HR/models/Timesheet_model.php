<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Timesheet_model extends CI_Model{
	
	function __construct() {
		parent::__construct();
		$this->location_id = $this->session->userdata('location_id');
		$this->load->model('common_model');
	}
	
	public function searchEmployees($query, $location_id) {
        $this->tenantDb->select("emp_id, first_name, last_name, email, position_id");
        $this->tenantDb->from("HR_employee");
        $this->tenantDb->like("first_name", $query);
        $this->tenantDb->or_like("last_name", $query);
        $this->tenantDb->or_like("email", $query);
        $this->tenantDb->where("location_id", $location_id);
        $this->tenantDb->where("status", 1);
        $this->tenantDb->where("is_deleted", 0);

        $rows = $this->tenantDb->get()->result_array();

        $output = [];
        foreach ($rows as $r) {
            $output[] = [
                "emp_id"   => $r['emp_id'],
                "name"     => $r['first_name'] . " " . $r['last_name'],
                "email"    => $r['email'],
                "photo"    => '',
                "position" => '',
            ];
        }

        return $output;
    }
	
 public function getTimesheetForDate($date, $location_id)
    {
        $fields = [
            'HR_timesheet_details.timesheet_id',
            'HR_timesheet_details.employee_id',
            'HR_timesheet_details.prep_area_id',
            'HR_timesheet_details.position_id',
            'HR_timesheet_details.clock_in_time',
            'HR_timesheet_details.clock_out_time',
            'HR_timesheet_details.actual_break_duration',
            'HR_timesheet_details.approval_status',
            'HR_timesheet_details.roster_start_time',
            'HR_timesheet_details.roster_end_time',
            'CONCAT(e.first_name, " ", e.last_name) as name',
            'e.employee_type',
            'e.pin',
            'p.prep_name',
            'pos.position_name',
            'MAX(b.break_start_time) as latest_break_start_time',
            'b.break_end_time as latest_break_end_time'
        ];

        $this->tenantDb->select(implode(',', $fields))
            ->from('HR_timesheet_details')
            ->join('HR_employee e', 'HR_timesheet_details.employee_id = e.emp_id', 'inner')
            ->join('HR_prepArea p', 'HR_timesheet_details.prep_area_id = p.id', 'left')
            ->join('HR_emp_position pos', 'HR_timesheet_details.position_id = pos.position_id', 'left')
            ->join('HR_timesheet_breaks b', 'HR_timesheet_details.timesheet_id = b.timesheet_id AND HR_timesheet_details.employee_id = b.employee_id AND b.is_deleted = 0', 'left')
            ->where([
                'HR_timesheet_details.roster_date' => $date,
                'HR_timesheet_details.is_deleted' => 0,
                'HR_timesheet_details.location_id' => $location_id
            ])
            ->where('(HR_timesheet_details.status = 1 OR HR_timesheet_details.status IS NULL)', NULL, FALSE)
            ->group_by('HR_timesheet_details.timesheet_id')
            ->order_by('e.first_name, e.last_name');

        $query = $this->tenantDb->get();
        
        // echo $this->tenantDb->last_query(); exit;
        return $query->result_array();
    }

    public function getBreakDurationForTimesheet($timesheet_id, $employeeId)
    {
        $this->tenantDb->select('SUM(break_duration) as total_break_duration')
            ->from('HR_timesheet_breaks')
            ->where(['timesheet_id' => $timesheet_id,'employee_id'     => $employeeId, 'is_deleted' => 0]);
        $query = $this->tenantDb->get();
        $result = $query->row_array();
        return $result['total_break_duration'] ?? 0;
    }

    public function getLatestBreak($timesheet_id, $employeeId)
    {
        $this->tenantDb->select('break_id, break_start_time, break_end_time')
            ->from('HR_timesheet_breaks')
            ->where(['timesheet_id' => $timesheet_id,'employee_id' => $employeeId, 'is_deleted' => 0])
            ->order_by('break_start_time', 'DESC')
            ->limit(1);
        $query = $this->tenantDb->get();
        return $query->row_array();
    }
    
    // timesheet with roster
     public function get_timesheets_by_date_range($start_date, $end_date, $location_id,$dateSort=false) {
        $fields = [
            'HR_timesheet_details.timesheet_id',
            'HR_timesheet_details.employee_id',
            'HR_timesheet_details.prep_area_id',
            'HR_timesheet_details.position_id',
            'HR_timesheet_details.clock_in_time',
            'HR_timesheet_details.clock_out_time',
            'HR_timesheet_details.actual_break_duration',
            'HR_timesheet_details.roster_date',
            'HR_timesheet_details.approval_status',
            'HR_timesheet_details.manual_break_override',
            'HR_timesheet_details.manual_break_minutes',
            'HR_timesheet_details.manager_comment',
            'CONCAT(e.first_name, " ", e.last_name) as employee_name',
            'e.employee_type',
            'e.pin',
            'p.prep_name',
            'pos.position_name',
            'SUM(b.break_duration) as total_break_duration',
            'TIMEDIFF(HR_timesheet_details.clock_out_time, HR_timesheet_details.clock_in_time) as total_time',
            'SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(HR_timesheet_details.clock_out_time, HR_timesheet_details.clock_in_time)) - IFNULL(SUM(b.break_duration), 0)) as total_hours',
            'r.shift_start_time',
            'r.shift_end_time'
        ];
        
        $this->tenantDb->select($fields)
            ->from('HR_timesheet_details')
            ->join('HR_employee e', 'HR_timesheet_details.employee_id = e.emp_id', 'inner')
            ->join('HR_prepArea p', 'HR_timesheet_details.prep_area_id = p.id', 'inner')
            ->join('HR_emp_position pos', 'HR_timesheet_details.position_id = pos.position_id', 'left')
            ->join('HR_timesheet_breaks b', 'HR_timesheet_details.timesheet_id = b.timesheet_id and HR_timesheet_details.employee_id = b.employee_id AND b.is_deleted = 0', 'left')
            ->join('HR_roster_details r', 'HR_timesheet_details.employee_id = r.employee_id AND HR_timesheet_details.roster_date = r.roster_date', 'left')
            ->where('HR_timesheet_details.roster_date >=', $start_date)
            ->where('HR_timesheet_details.roster_date <=', $end_date)
            ->where([
                'HR_timesheet_details.is_deleted' => 0,
                'HR_timesheet_details.location_id' => $location_id
            ])
            ->group_by('HR_timesheet_details.timesheet_id');
            
// Optional date sorting (PRIMARY)
if ($dateSort == true) {
    $this->tenantDb->order_by('HR_timesheet_details.roster_date', 'ASC');
}

// Always keep these secondary sorts
$this->tenantDb
    ->order_by('e.first_name', 'ASC')
    ->order_by('e.last_name', 'ASC')
    ->order_by('HR_timesheet_details.clock_in_time', 'ASC');


        
        $query = $this->tenantDb->get();
        $timesheets = $query->result_array();
        
        // Auto-approve timesheets if clock times match shift times (within 5 minutes)
        foreach ($timesheets as &$timesheet) {
            if ($timesheet['shift_start_time'] && $timesheet['shift_end_time']) {
                $clock_in_diff = abs(strtotime($timesheet['clock_in_time']) - strtotime($timesheet['shift_start_time']));
                $clock_out_diff = abs(strtotime($timesheet['clock_out_time']) - strtotime($timesheet['shift_end_time']));
                $five_minutes = 5 * 60; // 5 minutes in seconds
                
                if ($clock_in_diff <= $five_minutes && $clock_out_diff <= $five_minutes) {
                    $timesheet['approval_status'] = 'Approved';
                    $this->tenantDb->where('timesheet_id', $timesheet['timesheet_id'])
                        ->update('HR_timesheet_details', ['approval_status' => 'approved']);
                }
            }
        }
        
        return $timesheets;
    }
    
  /**
 * Update timesheet clock in and clock out times
 * 
 * @param int $timesheet_id
 * @param string $clock_in_time
 * @param string $clock_out_time
 * @return bool
 */
public function update_timesheet_times($timesheet_id, $clock_in_time = null, $clock_out_time = null) {
    if (empty($timesheet_id)) {
        return false;
    }
    
    $data = [];
    
    if (!empty($clock_in_time)) {
        $data['clock_in_time'] = $clock_in_time;
    }
    
    if (!empty($clock_out_time)) {
        $data['clock_out_time'] = $clock_out_time;
    }
    
    if (empty($data)) {
        return false;
    }
    
    // Add updated timestamp
    $data['updated_at'] = date('Y-m-d H:i:s');
    
    // Calculate total hours if both times are provided
    if (!empty($clock_in_time) && !empty($clock_out_time)) {
        $start = new DateTime($clock_in_time);
        $end = new DateTime($clock_out_time);
        $diff = $start->diff($end);
        
        $total_hours = sprintf('%02d:%02d:%02d', 
            ($diff->days * 24) + $diff->h, 
            $diff->i, 
            $diff->s
        );
        
        $data['total_hours'] = $total_hours;
    }
    
    $this->tenantDb->where('timesheet_id', $timesheet_id);
    return $this->tenantDb->update('HR_timesheet_details', $data);
//   echo  $this->tenantDb->last_query(); exit;
}
    
    /**
 * Approve a single timesheet entry
 * 
 * @param int $timesheet_id
 * @return bool
 */
public function approve_single_timesheet($timesheet_id) {
    if (empty($timesheet_id)) {
        return false;
    }
    
    $data = [
        'approval_status' => 'approved',
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $this->tenantDb->where('timesheet_id', $timesheet_id);
    $this->tenantDb->where('is_deleted', 0); // Only approve non-deleted timesheets
    return $this->tenantDb->update('HR_timesheet_details', $data);
}

    
     public function approve_employee_timesheets($employee_id, $start_date, $end_date) {
        $this->tenantDb->where([
            'employee_id' => $employee_id,
            'roster_date >=' => $start_date,
            'roster_date <=' => $end_date,
            'is_deleted' => 0
        ])->update('HR_timesheet_details', ['approval_status' => 'approved']);
        
        return $this->tenantDb->affected_rows() > 0;
    }
    
    // timesheet without roster 25-11-2025
    
    // Main function: Get all tasks for a week, grouped by Prep Area
    public function get_weekly_tasks_by_prep_area($timesheetId, $week_start = '')
   {
       
       
    $this->tenantDb->select('
        t.task_id as task_row_id,
        t.employee_id,
        t.task_date,
        t.prep_area_id,
        t.task_description,      

        emp.first_name,
        emp.last_name,

        COALESCE(pa.prep_name, "All Prep Area") as prep_name,
        COALESCE(pa.color, "gray") as area_color
    ');

    $this->tenantDb->from('HR_tasks t');
    $this->tenantDb->join('HR_employee emp', 'emp.emp_id = t.employee_id', 'left');
    $this->tenantDb->join('HR_prepArea pa', 'pa.id = t.prep_area_id', 'left');
    $this->tenantDb->join('HR_timesheet ts', 'ts.id = t.timesheet_id', 'left');

    $this->tenantDb->where('ts.location_id', $this->location_id);
    $this->tenantDb->where('ts.id', $timesheetId);

    $this->tenantDb->order_by('pa.prep_name ASC, emp.first_name ASC, t.task_date ASC');

    $results = $this->tenantDb->get()->result_array();

    // Grouped data
    $grouped = [];

    foreach ($results as $row) {

        $area_name  = $row['prep_name'] ?: 'Uncategorized';
        $area_color = $row['area_color'] ?: 'bg-blue-500';

        if (!isset($grouped[$area_name])) {
            $grouped[$area_name] = [
                'id'        => $row['prep_area_id'] ?? 0,
                'name'      => $area_name,
                'color'     => $area_color,
                'employees' => []
            ];
        }

        $emp_id   = $row['employee_id'];
        $emp_name = trim($row['first_name'] . ' ' . $row['last_name']);

        if (!isset($grouped[$area_name]['employees'][$emp_id])) {
            $grouped[$area_name]['employees'][$emp_id] = [
                'id'       => $emp_id,
                'name'     => $emp_name ?: 'Unknown Employee',
                'tasks'    => [
                    'Mon' => [], 'Tue' => [], 'Wed' => [],
                    'Thu' => [], 'Fri' => [], 'Sat' => [], 'Sun' => []
                ]
            ];
        }

        // Determine week day
        $day_key = date('D', strtotime($row['task_date']));
        $day_map = [
            'Mon'=>'Mon','Tue'=>'Tue','Wed'=>'Wed','Thu'=>'Thu',
            'Fri'=>'Fri','Sat'=>'Sat','Sun'=>'Sun'
        ];
        $day = $day_map[$day_key] ?? 'Mon';

        // Decode JSON tasks
        $taskList = [];
        if (!empty($row['task_description'])) {
            $taskList = json_decode($row['task_description'], true);
            if (!is_array($taskList)) { 
                $taskList = []; 
            }
        }

        // Push every task separately for UI structure
        foreach ($taskList as $taskText) {
            $grouped[$area_name]['employees'][$emp_id]['tasks'][$day][] = [
                'id'   => $row['task_row_id'],  // row ID only
                'note' => $taskText             // actual task text from JSON
            ];
        }
    }

    return array_values($grouped);
}



    // Helper: Convert color name to Tailwind class
    private function get_color_class($color)
    {
        $map = [
            'blue'    => 'bg-blue-500',
            'green'   => 'bg-green-500',
            'red'     => 'bg-red-500',
            'orange'  => 'bg-orange-500',
            'purple'  => 'bg-purple-500',
            'pink'    => 'bg-pink-500',
            'yellow'  => 'bg-yellow-500',
            'gray'    => 'bg-gray-500',
        ];
        return $map[strtolower($color)] ?? 'bg-gray-500';
    }
    
    public function timesheetEntryThisweekForTimesheetWithoutRoster($weekStart, $weekEnd)
{
    $query = $this->tenantDb->select('employee_id')
                            ->from('HR_timesheet_details')
                            ->where('roster_date >=', $weekStart)
                            ->where('roster_date <=', $weekEnd)
                            ->where('is_deleted', 0)
                            ->where('status', 1)
                            ->where('location_id', $this->location_id)
                            ->group_by('employee_id')
                            ->get();

    return $query ? $query->result_array() : [];
}

public function taskThisweekForTimesheetWithoutRoster($weekStart, $weekEnd)
{
    $query = $this->tenantDb->from('HR_tasks')
                            ->where('task_date >=', $weekStart)
                            ->where('task_date <=', $weekEnd)
                            ->where('is_deleted', 0)
                            ->where('location_id', $this->location_id)
                            ->get();

    return $query ? $query->result_array() : [];
}

// for dashboard 
  public function getTodayTasks($empId, $date = null)
{
    if ($date === null) {
        $date = date('Y-m-d');
    }
    if (!$empId) return [];

    // Fetch all daily tasks for employee
    $tasks = $this->tenantDb
        ->select('id, task_descr, status, date, time_task_completed_at')
        ->from('hr_task_daily_status')
        ->where('emp_id', $empId)
        ->where('date', $date)
        ->order_by('id', 'ASC')
        ->get()
        ->result_array();

    // Format output for view
    $processedTasks = [];

    foreach ($tasks as $t) {
        $processedTasks[] = [
            'id'        => $t['id'],
            'task'      => $t['task_descr'],
            'due'       => $t['date'],
            'status'    => (int) $t['status'],  // 0 = pending, 1 = done
            'completed_at' => $t['time_task_completed_at']
        ];
    }

    return $processedTasks;
}


// Helper to check JSON
private function isJson($string)
{
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

public function getMonthlyTaskDates($empId, $year, $month)
{   
    if (!$empId) return [];
    $firstDay = "$year-$month-01";
    $lastDay  = date("Y-m-t", strtotime($firstDay)); // last day of month

    $conditions = [
        'employee_id' => $empId,
        'is_deleted'  => 0
    ];

    $dateRange = [
        'task_date >=' => $firstDay,
        'task_date <=' => $lastDay
    ];

    // Combine conditions
    $allConditions = array_merge($conditions, $dateRange);

    // Use your reusable fetch method
    $rows = $this->common_model->fetchRecordsDynamically('HR_tasks',['task_id', 'task_date', 'task_description'],$allConditions,'','task_date ASC');

    $taskMap = [];

    foreach ($rows as $row) {
        $day = (int) date("d", strtotime($row['task_date']));
        $taskMap[$day] = true;  // mark this day as having tasks
    }

    return $taskMap; // e.g. [5 => true, 12 => true, 17 => true]
}



public function getTodaysShift($empId, $date)
    {   
        if (!$empId) return [];
        $q = $this->tenantDb
            ->select('timesheet_id, roster_id, roster_date, roster_start_time, roster_end_time, roster_break_start_time, roster_break_duration, clock_in_time, clock_out_time, actual_break_duration')
            ->from('HR_timesheet_details')
            ->where('employee_id', $empId)
            ->where('roster_date', $date)
            ->where('is_deleted', 0)
            ->order_by('roster_start_time', 'ASC')
            ->limit(1)
            ->get();

        if ($q === false) return null;
        return $q->row_array();
    }

    /**
     * Sum worked seconds for the given date range (inclusive).
     * For each timesheet detail row:
     *  - if clock_in & clock_out present -> add (out - in)
     *  - if clock_in present & clock_out missing -> add (now - in)
     *  - subtract break durations (actual_break_duration preferred else roster_break_duration)
     */
    public function getWorkedSecondsForRange($empId, $startDate, $endDate)
    {  
        if (!$empId) return [];
        
        $q = $this->tenantDb
            ->select('clock_in_time, clock_out_time, actual_break_duration, roster_break_duration, roster_date')
            ->from('HR_timesheet_details')
            ->where('employee_id', $empId)
            ->where('is_deleted', 0)
            ->where('roster_date >=', $startDate)
            ->where('roster_date <=', $endDate)
            ->get();

        if ($q === false) return 0;

        $rows = $q->result_array();
        $total = 0;
        $now = time();

        foreach ($rows as $r) {
            if (empty($r['clock_in_time'])) continue;

            $inTs = strtotime($r['clock_in_time']);
            if ($inTs === false) continue;

            if (!empty($r['clock_out_time'])) {
                $outTs = strtotime($r['clock_out_time']);
                if ($outTs === false) $outTs = $now;
            } else {
                $outTs = $now;
            }

            $diff = max(0, $outTs - $inTs);

            // subtract break
            $breakMins = 0;
            if (!empty($r['actual_break_duration']) || $r['actual_break_duration'] === '0') {
                $breakMins = (int)$r['actual_break_duration'];
            } elseif (!empty($r['roster_break_duration']) || $r['roster_break_duration'] === '0') {
                $breakMins = (int)$r['roster_break_duration'];
            }

            $diff -= max(0, $breakMins * 60);

            $total += max(0, $diff);
        }

        return (int)$total;
    }

    /**
     * Count daily tasks completed and total tasks in a date range for employee
     * uses hr_task_daily_status (one task per row)
     */
    public function getWeeklyTaskSummary($empId, $startDate, $endDate)
    {   
        if (!$empId) return [];
        $q = $this->tenantDb
            ->select('COUNT(*) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS completed', false)
            ->from('hr_task_daily_status')
            ->where('emp_id', $empId)
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->get();

        if ($q === false) return ['total' => 0, 'completed' => 0];

        return $q->row_array();
    }

    /**
     * Count distinct dates in the week that have a clock_in_time (attendance days)
     */
    public function getAttendanceDaysCount($empId, $startDate, $endDate)
    {   
        if (!$empId) return [];
        $q = $this->tenantDb
            ->select('COUNT(DISTINCT roster_date) AS days', false)
            ->from('HR_timesheet_details')
            ->where('employee_id', $empId)
            ->where('roster_date >=', $startDate)
            ->where('roster_date <=', $endDate)
            ->where("(clock_in_time IS NOT NULL AND clock_in_time != '')", null, false)
            ->get();

        if ($q === false) return 0;
        $row = $q->row_array();
        return (int)($row['days'] ?? 0);
    }


public function getEmployeeTimesheets($empId)
{    
    if (!$empId) return [];
    return $this->tenantDb
        ->distinct()
        ->select('ht.id, ht.date_from, ht.date_to')
        ->from('HR_timesheet ht')
        ->join('HR_timesheet_details htd', 'htd.parent_timesheet_id = ht.id', 'left')
        ->where('htd.employee_id', $empId)
        ->where('ht.is_deleted', 0)
        ->order_by('ht.date_from', 'DESC')
        ->limit(10)
        ->get()
        ->result_array();
}

/**
 * Get detailed timesheet records for an employee within a date range
 * Including clock in/out times, locations, break durations, and calculated hours
 * 
 * @param int $emp_id Employee ID
 * @param string $start_date Start date (Y-m-d)
 * @param string $end_date End date (Y-m-d)
 * @param int $location_id Location ID
 * @return array Array of timesheet records
 */
public function getDetailedTimesheetByDateRange($emp_id, $start_date, $end_date, $location_id) {
    $this->tenantDb->select([
        'HR_timesheet_details.timesheet_id',
        'HR_timesheet_details.roster_date',
        'HR_timesheet_details.clock_in_time',
        'HR_timesheet_details.clock_out_time',
        'HR_timesheet_details.clock_in_latitude',
        'HR_timesheet_details.clock_in_longitude',
        'HR_timesheet_details.clock_in_address',
        'HR_timesheet_details.clock_out_latitude',
        'HR_timesheet_details.clock_out_longitude',
        'HR_timesheet_details.clock_out_address',
        'HR_timesheet_details.actual_break_duration',
        'HR_timesheet_details.approval_status',
        'HR_prepArea.prep_name',
        'HR_emp_position.position_name',
        'TIMESTAMPDIFF(SECOND, HR_timesheet_details.clock_in_time, HR_timesheet_details.clock_out_time) as total_seconds',
        'SUM(HR_timesheet_breaks.break_duration) as total_break_minutes'
    ])
    ->from('HR_timesheet_details')
    ->join('HR_prepArea', 'HR_timesheet_details.prep_area_id = HR_prepArea.id', 'left')
    ->join('HR_emp_position', 'HR_timesheet_details.position_id = HR_emp_position.position_id', 'left')
    ->join('HR_timesheet_breaks', 'HR_timesheet_details.timesheet_id = HR_timesheet_breaks.timesheet_id AND HR_timesheet_details.employee_id = HR_timesheet_breaks.employee_id AND HR_timesheet_breaks.is_deleted = 0', 'left')
    ->where([
        'HR_timesheet_details.employee_id' => $emp_id,
        'HR_timesheet_details.location_id' => $location_id,
        'HR_timesheet_details.is_deleted' => 0
    ])
    ->where('HR_timesheet_details.roster_date >=', $start_date)
    ->where('HR_timesheet_details.roster_date <=', $end_date)
    ->group_by('HR_timesheet_details.timesheet_id')
    ->order_by('HR_timesheet_details.roster_date', 'ASC');

    $query = $this->tenantDb->get();
    return $query->result_array();
}

/**
 * Get weekly report data for all employees
 * Includes timesheet, roster, and hourly rates for cost calculation
 * 
 * @param string $start_date Start date (Y-m-d)
 * @param string $end_date End date (Y-m-d)
 * @param int $location_id Location ID
 * @return array Array of employee report data
 */
public function getWeeklyReportData($start_date, $end_date, $location_id) {
    $fields = [
        'HR_timesheet_details.timesheet_id',
        'HR_timesheet_details.employee_id',
        'HR_timesheet_details.position_id',
        'HR_timesheet_details.roster_date',
        'HR_timesheet_details.clock_in_time',
        'HR_timesheet_details.clock_out_time',
        'HR_timesheet_details.roster_start_time',
        'HR_timesheet_details.roster_end_time',
        'HR_timesheet_details.actual_break_duration',
        'HR_timesheet_details.manual_break_override',
        'HR_timesheet_details.manual_break_minutes',
        'CONCAT(e.first_name, " ", e.last_name) as employee_name',
        'e.first_name',
        'e.last_name',
        'pos.position_name',
        'etp.rate as weekday_rate',
        'etp.Saturday_rate',
        'etp.Sunday_rate',
        'etp.holiday_rate',
        'SUM(b.break_duration) as total_break_duration'
    ];
    
    $this->tenantDb->select($fields)
        ->from('HR_timesheet_details')
        ->join('HR_employee e', 'HR_timesheet_details.employee_id = e.emp_id', 'inner')
        ->join('HR_emp_position pos', 'HR_timesheet_details.position_id = pos.position_id', 'left')
        ->join('HR_emp_to_position etp', 'HR_timesheet_details.employee_id = etp.emp_id AND HR_timesheet_details.position_id = etp.position_id', 'left')
        ->join('HR_timesheet_breaks b', 'HR_timesheet_details.timesheet_id = b.timesheet_id AND HR_timesheet_details.employee_id = b.employee_id AND b.is_deleted = 0', 'left')
        ->where('HR_timesheet_details.roster_date >=', $start_date)
        ->where('HR_timesheet_details.roster_date <=', $end_date)
        ->where([
            'HR_timesheet_details.is_deleted' => 0,
            'HR_timesheet_details.location_id' => $location_id
        ])
        ->group_by('HR_timesheet_details.timesheet_id')
        ->order_by('e.first_name', 'ASC')
        ->order_by('e.last_name', 'ASC')
        ->order_by('HR_timesheet_details.roster_date', 'ASC');
    
    $query = $this->tenantDb->get();
    return $query->result_array();
}

/**
 * Get roster details for date range
 * 
 * @param string $start_date Start date (Y-m-d)
 * @param string $end_date End date (Y-m-d)
 * @param int $location_id Location ID
 * @return array Array keyed by employee_id and date
 */
public function getRosterDetailsForReport($start_date, $end_date, $location_id) {
    $this->tenantDb->select([
        'rd.employee_id',
        'rd.roster_date',
        'rd.shift_start_time',
        'rd.shift_end_time'
    ])
    ->from('HR_roster_details rd')
    ->join('HR_roster r', 'rd.roster_id = r.roster_id', 'inner')
    ->where('rd.roster_date >=', $start_date)
    ->where('rd.roster_date <=', $end_date)
    ->where('rd.is_deleted', 0)
    ->where('r.location_id', $location_id)
    ->where('r.is_deleted', 0)
    ->order_by('rd.roster_date', 'ASC');
    
    $results = $this->tenantDb->get()->result_array();
    
    // Group by employee_id and date for easy lookup
    $grouped = [];
    foreach ($results as $row) {
        $empId = $row['employee_id'];
        $date = $row['roster_date'];
        if (!isset($grouped[$empId][$date])) {
            $grouped[$empId][$date] = [];
        }
        $grouped[$empId][$date][] = $row;
    }
    
    return $grouped;
}


	
}