<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    function __construct() {
		parent::__construct();
		!$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
		$this->load->model('common_model');
		$this->load->model('roster_model');
		$this->load->model('timesheet_model');
		$this->load->model('dashboard_model');
		$this->load->model('general_model');
		  $this->load->model('employee_model');
	    $this->load->helper('timesheet');
	    $this->location_id = $this->session->userdata('location_id') ? $this->session->userdata('location_id') : ($this->session->userdata('User_location_ids') ? $this->session->userdata('User_location_ids')[0] : null);
	    $user_id = $this->ion_auth->user()->row()->id;
        $empData = $this->common_model->fetchRecordsDynamically('HR_employee', ['emp_id','first_name','last_name'], ['userId'=>$user_id]);
        $this->empId = (isset($empData[0]['emp_id']) ? $empData[0]['emp_id'] : ''); // incase of superadmin  empId will be blank because we do not store superadmin info in employee table
        $this->first_name = (isset($empData[0]['first_name']) ? $empData[0]['first_name'] : '');
        $this->last_name = (isset($empData[0]['last_name']) ? $empData[0]['last_name'] : '');
        
        // ini_set('display_errors', 1); ini_set('display_startup_errors', 1);error_reporting(E_ALL);


	}
	
public function index($system_id = '')
{
    // ---------- Defaults ----------
    $data = [];
    $data['today'] = (int) date("d");
    $empId = !empty($this->empId) ? $this->empId : null;

    // ---------- System ID ----------
    if (!empty($system_id)) {
        $this->session->set_userdata('system_id', $system_id);
    }

    // ---------- Email Settings ----------
    $emailSettings = null;

    if (isset($this->general_model)) {
        $emailSettings = $this->general_model->fetchSmtpSettings(
            $this->location_id ?? '',
            $system_id
        );
    }

    if (empty($emailSettings)) {
        $emailSettings = $this->general_model->fetchSmtpSettings('9999', '9999');
    }

    if (is_object($emailSettings)) {
        if (isset($emailSettings->mail_protocol) && $emailSettings->mail_protocol === 'smtp') {
            $this->session->set_userdata('mail_protocol', 'smtp');
            $this->configureSMTP($emailSettings);
        }

        $this->session->set_userdata(
            'mail_from',
            !empty($emailSettings->mail_from) ? $emailSettings->mail_from : 'info@bizadmin.com.au'
        );
    } else {
        $this->session->set_userdata('mail_from', 'info@bizadmin.com.au');
    }

    // ---------- Availability ----------
    $data['availability'] = [];
    if (!empty($empId)) {
        $conditionsAvail = [
            'emp_id' => $empId,
            'is_deleted' => '0'
        ];
        $data['availability'] = $this->common_model
            ->fetchRecordsDynamically('HR_emp_availability', '', $conditionsAvail) ?? [];
    }

    // ---------- Today's Roster ----------
    $todaysRosterDatas = null;
    $todaysRoster = $this->roster_model->fetchEmployeeTodaysRoster();

    if (is_array($todaysRoster) && !empty($todaysRoster[0]) && is_object($todaysRoster[0])) {
        $dayName = strtolower(date('l'));

        if (property_exists($todaysRoster[0], $dayName)) {
            $todaysRosterDatas = $todaysRoster[0]->{$dayName};
        }
    }

    // ---------- Decode Roster ----------
    if (!empty($todaysRosterDatas)) {
        $decoded = json_decode($todaysRosterDatas, true);

        if (is_array($decoded)) {
            foreach ($decoded as $key => $value) {
                // Protect against undefined $string
                if (is_string($value)) {
                    $parts = explode('_', $value);
                    $last = end($parts);
                    if (!empty($last)) {
                        $empId = $last;
                    }
                }
            }
        }
    }

    // ---------- Employee Dashboard ----------
    if (!empty($empId)) {
        $data['employeeProfileWidgetData'] = $this->profileWidget($empId) ?? [];
        $data['taskDays'] = $this->displayShiftsOnDashboard($empId) ?? [];
        $data['todayTasks'] = $this->timesheet_model
            ->getTodayTasks($empId, date('Y-m-d')) ?? [];
        $data['employeeTimesheets'] = $this->timesheet_model
            ->getEmployeeTimesheets($empId) ?? [];
        $data['attendance'] = $this->attendanceTimeline($empId) ?? [];
        $data['empId'] = $empId;
        
        // Fetch leave types for leave request form
        $leaveTypeConditions = ['status' => 1, 'is_deleted' => 0];
        $data['leaveTypes'] = $this->common_model->fetchRecordsDynamically(
            'HR_leaves', 
            ['id', 'leaveTypeName', 'entitlements'], 
            $leaveTypeConditions,
            'leaveTypeName ASC'
        ) ?? [];
    }

    // ---------- Manager Dashboard ----------
    $data['birthdays_today'] = $this->dashboard_model->get_todays_birthdays() ?? [];
    $data['pending_leaves'] = $this->dashboard_model->get_pending_leaves() ?? [];
    $data['task_summary'] = $this->dashboard_model->get_task_summary() ?? [];
    $data['employee_on_break_count'] = $this->dashboard_model->get_employee_on_break_count() ?? 0;
    $data['attendance_today'] = $this->dashboard_model->get_today_attendance() ?? [];
    $data['present_today'] = $this->dashboard_model->get_present_today_count() ?? 0;
    $data['total_employees'] = $this->dashboard_model->get_total_employees_today() ?? 0;
    $data['incident_reports'] = $this->dashboard_model->get_incident_reports() ?? [];
    $data['injury_reports'] = $this->dashboard_model->get_injury_reports() ?? [];
    $data['total_team_hours'] = $this->dashboard_model->get_total_team_hours() ?? 0;

    // ---------- Views ----------
    $this->load->view('general/header');

    if ($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('manager')) {
        $this->load->view('general/dashboard_manager', $data);
    } else {
        $this->load->view('general/dashboard', $data);
    }

    $this->load->view('general/footer');
}

    
    public function profileWidget($empId = null)
     {
    // Date vars
    $today = date('Y-m-d');

    // Week start (Monday) and end (Sunday) relative to $today
    $monday = date('Y-m-d', strtotime('monday this week', strtotime($today)));
    $sunday = date('Y-m-d', strtotime('sunday this week', strtotime($today)));

    // 1) Today's shift
    $shift = $this->timesheet_model->getTodaysShift($empId, $today);

    // 2) Is shift started?
    $shiftStarted = false;
    $shiftClockInDisplay = '--:-- --';
    if (!empty($shift) && !empty($shift['clock_in_time'])) {
        $shiftStarted = true;
        $shiftClockInDisplay = date('h:i A', strtotime($shift['clock_in_time']));
    }

    $workedSeconds = $this->timesheet_model->getWorkedSecondsForRange($empId, $monday, $sunday);
    $hoursThisWeek = $this->formatSecondsToHoursLabel($workedSeconds);

    // 4) Total tasks week
    $taskSummary = $this->timesheet_model->getWeeklyTaskSummary($empId, $monday, $sunday);
    $tasksCompleted = (int)($taskSummary['completed'] ?? 0);
    $tasksTotal = (int)($taskSummary['total'] ?? 0);

    // 5) Attendance rate (days with clock in / 7)
    $attendanceDays = $this->timesheet_model->getAttendanceDaysCount($empId, $monday, $sunday);
    $attendanceRate = round(($attendanceDays / 7) * 100);
    

    // Build data for view (employee name/position left static as requested)
    $employeeProfileWidgetData = [
        'employee_name' => $this->first_name .' '.$this->last_name,          
        'employee_position' => 'Welcome to portal !!',
        'today_shift' => $shift,               
        'shift_started' => $shiftStarted,
        'shift_clockin_display' => $shiftClockInDisplay,
        'hours_this_week' => $hoursThisWeek,
        'tasks_completed' => $tasksCompleted,
        'tasks_total' => $tasksTotal,
        'attendance_rate' => $attendanceRate,
        'week_start' => $monday,
        'week_end' => $sunday
    ];

  return $employeeProfileWidgetData;
}

  /**
   * Helper to format seconds to human-readable label like "38.5h" or "5h 30m"
  */
   private function formatSecondsToHoursLabel($seconds)
   {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    if ($hours >= 1) {
        // show decimals like 38.5h
        $decimal = $hours + round($minutes / 60, 1);
        // Show 1 decimal place
        return rtrim(rtrim(number_format($decimal, 1), '0'), '.') . 'h';
    } else {
        return sprintf('%dm', $minutes);
    }
}

    
    public function displayShiftsOnDashboard($empId)
     {
   
    $year  = date("Y");
    $month = date("m");
    $taskDays = $this->timesheet_model->getMonthlyTaskDates($empId, $year, $month);
    return $taskDays;
}

    // for showing employee clock in and clok out status an hours worked
    
    public function attendanceTimeline($empId, $date = null)
    {
    // Ensure employee id
    if (empty($empId)) {
        return; // or show error
    }

    // Use requested date or today
    $date = $date ?: date('Y-m-d');

    // Target daily seconds (8 hours)
    $targetHours = 8;
    $targetSeconds = $targetHours * 3600;

    // Fetch all timesheet detail rows for this employee & date (location filter optional)
    $this->tenantDb->select('timesheet_id, roster_date, roster_start_time, roster_end_time, roster_break_start_time, roster_break_duration, clock_in_time, clock_out_time, actual_break_duration');
    $this->tenantDb->from('HR_timesheet_details');
    $this->tenantDb->where('employee_id', $empId);
    $this->tenantDb->where('roster_date', $date);
    // optionally check location_id if required:
    $this->tenantDb->order_by('timesheet_id', 'ASC');
    $q = $this->tenantDb->get();
// echo $this->tenantDb->last_query(); die();
    if ($q === false) {
        log_message('error', 'attendanceTimeline DB error: ' . print_r($this->tenantDb->error(), true));
        $rows = [];
    } else {
        $rows = $q->result_array();
    }
    
    // echo "<pre>"; print_r($rows); exit;

    // Initialize values
    $clockInTimes = [];
    $clockOutTimes = [];
    $breaks = []; // array of ['start' => timestamp, 'duration_minutes' => int]
    $totalBreakSeconds = 0;

    foreach ($rows as $r) {
        // Normalize clock_in_time/clock_out_time values to full timestamps
        if (!empty($r['clock_in_time'])) {
            $clockInTimes[] = strtotime($r['clock_in_time']);
        }
        if (!empty($r['clock_out_time'])) {
            $clockOutTimes[] = strtotime($r['clock_out_time']);
        }

        // Prefer actual_break_duration (minutes), fallback to roster_break_duration
        $breakMins = null;
        if (isset($r['actual_break_duration']) && $r['actual_break_duration'] !== null && $r['actual_break_duration'] !== '') {
            $breakMins = (int) $r['actual_break_duration'];
        } elseif (isset($r['roster_break_duration']) && $r['roster_break_duration'] !== null && $r['roster_break_duration'] !== '') {
            $breakMins = (int) $r['roster_break_duration'];
        }

        if (!empty($r['roster_break_start_time'])) {
            // roster_break_start_time might be stored as time only like "12:30:00" — construct timestamp
            $breakStartStr = $date . ' ' . $r['roster_break_start_time'];
            $breakStartTs = strtotime($breakStartStr);
            if ($breakStartTs !== false) {
                $breaks[] = [
                    'start_ts' => $breakStartTs,
                    'duration_minutes' => $breakMins ?: 0
                ];
                $totalBreakSeconds += ($breakMins ?: 0) * 60;
            }
        } elseif ($breakMins) {
            // No start time but duration available — add duration only
            $totalBreakSeconds += $breakMins * 60;
            $breaks[] = [
                'start_ts' => null,
                'duration_minutes' => $breakMins
            ];
        }
    }

    // Determine the earliest clock-in and latest clock-out
    $earliestClockIn = !empty($clockInTimes) ? min($clockInTimes) : null;
    $latestClockOut = !empty($clockOutTimes) ? max($clockOutTimes) : null;

    // If no explicit clock out, treat "now" as current time for progress calculation
    $nowTs = time();
    $calculatedClockOut = $latestClockOut ?: $nowTs;

    // Calculate worked seconds: sum(clock_out - clock_in) across matched pairs.
    // We'll pair clock-ins with next available clock-out (simple greedy).
    $workedSeconds = 0;
    if (!empty($clockInTimes)) {
        // sort arrays
        sort($clockInTimes);
        sort($clockOutTimes);

        $ci = 0; $co = 0;
        while ($ci < count($clockInTimes)) {
            $inTs = $clockInTimes[$ci];
            // find next clock_out >= inTs
            $matchingOut = null;
            while ($co < count($clockOutTimes)) {
                if ($clockOutTimes[$co] >= $inTs) {
                    $matchingOut = $clockOutTimes[$co];
                    $co++;
                    break;
                }
                $co++;
            }

            // if matchingOut found, add difference, else use $nowTs (ongoing)
            if ($matchingOut !== null) {
                $workedSeconds += max(0, $matchingOut - $inTs);
            } else {
                // no clock_out found for this in — use now
                $workedSeconds += max(0, $nowTs - $inTs);
            }

            $ci++;
        }
    }

    // subtract breaks (totalBreakSeconds)
    $workedSeconds = max(0, $workedSeconds - $totalBreakSeconds);

    // Percent of target
    $progressPercent = $targetSeconds > 0 ? round(min(100, ($workedSeconds / $targetSeconds) * 100)) : 0;

    // Format helpers
    $formatTime = function($ts) {
        return $ts ? date('h:i A', $ts) : '--:-- --';
    };

    $formatDuration = function($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $minutes);
        }
        return sprintf('%dm', $minutes);
    };

    // Determine break label (first break start time & resume time if possible)
    $breakStartLabel = '--:-- --';
    $breakResumeLabel = '--:-- --';
    if (!empty($breaks)) {
        // pick first break with start_ts
        $firstBreak = null;
        foreach ($breaks as $b) {
            if (!empty($b['start_ts'])) { $firstBreak = $b; break; }
        }
        if ($firstBreak) {
            $breakStartLabel = $formatTime($firstBreak['start_ts']);
            // resume = start + duration
            $resumeTs = $firstBreak['start_ts'] + ($firstBreak['duration_minutes'] * 60);
            $breakResumeLabel = $formatTime($resumeTs);
        } else {
            // we have only durations — show 'Break: duration'
            $breakStartLabel = ($breaks[0]['duration_minutes'] > 0) ? $breaks[0]['duration_minutes'] . 'm' : '--:-- --';
            $breakResumeLabel = '--:-- --';
        }
    }

    // Prepare data for view
    $data = [
        'date' => $date,
        'clock_in' => $formatTime($earliestClockIn),
        'break_start' => $breakStartLabel,
        'resume' => $breakResumeLabel,
        'clock_out' => $formatTime($latestClockOut),
        'worked_seconds' => $workedSeconds,
        'worked_label' => $formatDuration($workedSeconds),
        'target_label' => sprintf('%dh %02dm', $targetHours, 0),
        'progress_percent' => $progressPercent
    ];

    // Load partial view or return json if called via ajax
    // Example: $this->load->view('timesheet/attendance_timeline', $data);
    return $data;
}


    
     public function clockInClockOut($system_id = '', $istimesheet = '')
    {
        if (!empty($system_id)) {
            $this->session->set_userdata('system_id', $system_id);
        }
       
        $data['location_name']= fetchLocationNamesFromIds($this->session->userdata('User_location_ids'),true);
        $conditionsGeneralConfig = array('location' => $this->location_id, 'configureFor' => 'feature_toggle');
        $toggleConfig = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsGeneralConfig);
        
        if(isset($toggleConfig[0]['data']) && $toggleConfig[0]['data'] !='') {
        $generalConfigData = json_decode($toggleConfig[0]['data'], true);
        $data['generalConfigData']['feature_toggle'] = isset($generalConfigData['value']) ? $generalConfigData['value'] : '0';
        }
        
        // Fetch location capture toggle setting
        $conditionsLocationCapture = array('location' => $this->location_id, 'configureFor' => 'location_capture_toggle');
        $toggleConfigLocationCapture = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsLocationCapture);
        
        if(isset($toggleConfigLocationCapture[0]['data']) && $toggleConfigLocationCapture[0]['data'] !='') {
        $generalConfigDataLocationCapture = json_decode($toggleConfigLocationCapture[0]['data'], true);
        $data['generalConfigData']['location_capture_toggle'] = isset($generalConfigDataLocationCapture['value']) ? $generalConfigDataLocationCapture['value'] : '0';
        }
       
       
        // Configure SMTP settings
        $emailSettings = $this->general_model->fetchSmtpSettings($this->location_id, $system_id);
        if (empty($emailSettings)) {
            $emailSettings = $this->general_model->fetchSmtpSettings('9999', '9999');
            $this->configureSMTP($emailSettings);
        } else {
            if ($emailSettings->mail_protocol === 'smtp') {
                $this->configureSMTP($emailSettings);
            }
        }
        
       

        $this->session->set_userdata('mail_from', $emailSettings->mail_from ?? 'info@bizadmin.com.au');

        $currentDate = date('Y-m-d');

        // Fetch timesheet records
        $timesheetDetailsData = array();
        $timesheetDetailsData = $this->timesheet_model->getTimesheetForDate($currentDate, $this->location_id);
       
        // Group timesheet entries by employee for multi-shift display
        $groupedByEmployee = [];
        foreach ($timesheetDetailsData as $entry) {
            $empId = $entry['employee_id'];
            if (!isset($groupedByEmployee[$empId])) {
                $groupedByEmployee[$empId] = [
                    'employee_id' => $empId,
                    'name' => $entry['name'],
                    'employee_type' => $entry['employee_type'] ?? '',
                    'pin' => $entry['pin'] ?? '',
                    'position_name' => $entry['position_name'] ?? 'Not Assigned',
                    'shifts' => [],
                    'total_worked_seconds' => 0
                ];
            }
            
            // Add this shift to the employee's shifts array
            $groupedByEmployee[$empId]['shifts'][] = [
                'timesheet_id' => $entry['timesheet_id'],
                'prep_area_id' => $entry['prep_area_id'],
                'prep_name' => $entry['prep_name'] ?? 'None',
                'position_id' => $entry['position_id'],
                'position_name' => $entry['position_name'] ?? 'Not Assigned',
                'clock_in_time' => !empty($entry['clock_in_time']) ? round_time_to_quarter($entry['clock_in_time']) : null,
                'clock_out_time' => !empty($entry['clock_out_time']) ? round_time_to_quarter($entry['clock_out_time']) : null,
                'actual_break_duration' => $entry['actual_break_duration'] ?? 0,
                'latest_break_start_time' => $entry['latest_break_start_time'] ?? null,
                'latest_break_end_time' => $entry['latest_break_end_time'] ?? null,
                'roster_start_time' => $entry['roster_start_time'] ?? null,
                'roster_end_time' => $entry['roster_end_time'] ?? null,
                'approval_status' => $entry['approval_status'] ?? 'pending'
            ];
            
            // Calculate total worked seconds for this employee (handles overnight shifts)
            // Use ROUNDED times for hours calculation (payroll consistency)
            if (!empty($entry['clock_in_time']) && !empty($entry['clock_out_time'])) {
                $roundedIn = round_time_to_quarter($entry['clock_in_time']);
                $roundedOut = round_time_to_quarter($entry['clock_out_time']);
                $clockInTs = strtotime($roundedIn);
                $clockOutTs = strtotime($roundedOut);
                // Handle overnight shift (clock_out appears earlier than clock_in)
                if ($clockOutTs <= $clockInTs) {
                    $clockOutTs += 86400; // Add 24 hours
                }
                $diff = $clockOutTs - $clockInTs - (($entry['actual_break_duration'] ?? 0) * 60);
                $groupedByEmployee[$empId]['total_worked_seconds'] += max(0, $diff);
            }
        }
        
        $data['empLists'] = array_values($groupedByEmployee);
        $data['empListsFlat'] = $timesheetDetailsData; // Keep flat list for backward compatibility

        // Fetch prep areas
        $prepConditions = ['location_id' => $this->location_id, 'is_deleted' => 0, 'status' => 1];
        $prepAreas = $this->common_model->fetchRecordsDynamically('HR_prepArea', ['id', 'prep_name', 'color'], $prepConditions, 'prep_name');
        $selectedPrepAreas = $this->ion_auth->user()->row()->prepIds ?? '';
        if (!empty($selectedPrepAreas)) {
            $selectedPrepIds = unserialize($selectedPrepAreas);
            $filteredPrepAreas = array_filter($prepAreas, function ($area) use ($selectedPrepIds) {
                return in_array((int)$area['id'], $selectedPrepIds);
            });
            $data['prepAreas'] = array_values($filteredPrepAreas);
        } else {
            $data['prepAreas'] = $prepAreas;
        }

        $data['currentDate'] = $currentDate;

        // $this->load->view('general/header');
        $this->load->view('TimesheetClockIn/clockin', $data);
        $this->load->view('general/footer');
    }
    
    public function getTimesheetDetails()
    {
        // Validate inputs
        $week_start = $this->input->post('week_start');
        $week_end = $this->input->post('week_end');
        $emp_id = $this->input->post('emp_id');
        
        if (empty($week_start) || empty($week_end) || empty($emp_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
            return;
        }
        
        // Fetch detailed timesheet data
       
        $timesheetDetails = $this->timesheet_model->getDetailedTimesheetByDateRange($emp_id, $week_start, $week_end, $this->location_id);
        
        if (empty($timesheetDetails)) {
            echo json_encode([
                'success' => true,
                'data' => [],
                'message' => 'No timesheet records found for this period'
            ]);
            return;
        }
        
        // Format the data for display
        $formattedData = [];
        foreach ($timesheetDetails as $detail) {
            $clockIn = !empty($detail['clock_in_time']) ? date('h:i A', strtotime($detail['clock_in_time'])) : '--:--';
            $clockOut = !empty($detail['clock_out_time']) ? date('h:i A', strtotime($detail['clock_out_time'])) : '--:--';
            $date = date('M d, Y', strtotime($detail['roster_date']));
            
            // Calculate total hours
            $totalSeconds = (int)$detail['total_seconds'];
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $totalHours = sprintf('%dh %02dm', $hours, $minutes);
            
            // Get location/address
            $location = !empty($detail['full_address']) ? $detail['full_address'] : 'N/A';
            
            // Get break info
            $breakInfo = '';
            if (!empty($detail['break_start']) && !empty($detail['break_end'])) {
                $breakStart = date('h:i A', strtotime($detail['break_start']));
                $breakEnd = date('h:i A', strtotime($detail['break_end']));
                $breakDuration = !empty($detail['break_duration']) ? round($detail['break_duration'] / 60) . ' min' : '';
                $breakInfo = "{$breakStart} - {$breakEnd} ({$breakDuration})";
            } else {
                $breakInfo = 'No break';
            }
            
            // Status badge
            $status = !empty($detail['clock_out_time']) ? 'Completed' : 'In Progress';
            $statusClass = !empty($detail['clock_out_time']) ? 'success' : 'warning';
            
            $formattedData[] = [
                'date' => $date,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'total_hours' => $totalHours,
                'location' => $location,
                'break_info' => $breakInfo,
                'status' => $status,
                'status_class' => $statusClass
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $formattedData
        ]);
    }
    
   
    
    
    
	
}