<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

defined('BASEPATH') OR exit('No direct script access allowed');

class Roster extends MY_Controller {

    function __construct() {
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        // $this->load->helper('notification');
		$this->load->model('employee_model');
		$this->load->model('auth_model');
	    $this->load->model('common_model');
        $this->location_id = $this->session->userdata('location_id') ? $this->session->userdata('location_id') : ($this->session->userdata('User_location_ids') ? $this->session->userdata('User_location_ids')[0] : null);
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        $this->roleId = get_logged_in_user_role($this->ion_auth,'id');
    }
    
    
  public function rosterForm()
    {
    // ---------- Previous URL (safe) ----------
    if (isset($this->session)) {
        $this->session->set_userdata('previous_url', current_url());
    }

    // ---------- Location ----------
    $locationId = $this->location_id ?? 0;

    // ---------- Conditions ----------
    $conditions = [
        'location_id' => $locationId,
        'is_deleted'  => '0'
    ];

    // ---------- Base Data (safe defaults) ----------
    $data = [
        'empLists'          => [],
        'positionLists'    => [],
        'prepAreas'         => [],
        'rosterId'          => 0,
        'weekRange'         => '',
        'rosterStartDate'   => '',
        'rosterInfo'        => [],
        'allDayRosterData'  => []
    ];

    // ---------- Employees ----------
    // Get current user's employee ID and role
    $user_id = $this->ion_auth->user()->row()->id;
    $empData = $this->common_model->fetchRecordsDynamically('HR_employee', ['emp_id'], ['userId'=>$user_id]);
    $currentEmpId = (isset($empData[0]['emp_id']) ? $empData[0]['emp_id'] : '');
    $data['roleId'] = $this->roleId;
    $data['currentEmpId'] = $currentEmpId;
    
    // For employee role (4), only show their own data
    if($this->roleId == 4 && !empty($currentEmpId)){
        $data['empLists'] = $this->employee_model->employeeList('','',true) ?? [];
        // Filter to only current employee
        $data['empLists'] = array_filter($data['empLists'], function($emp) use ($currentEmpId) {
            return isset($emp['emp_id']) && $emp['emp_id'] == $currentEmpId;
        });
    } else {
        $data['empLists'] = $this->employee_model->employeeList('', '', true) ?? [];
    }
   

    // ---------- Positions ----------
    $data['positionLists'] = $this->common_model->fetchRecordsDynamically('HR_emp_position', '', $conditions) ?? [];

    $data['prepAreas'] = $this->common_model->fetchRecordsDynamically('HR_prepArea', '', $conditions) ?? [];
    
     // Check if custom dates are provided
    $customStartDate = $this->input->get('start_date');
    $customEndDate = $this->input->get('end_date');

    // ---------- Week Date Range ----------
    try {
        if (!empty($customStartDate) && !empty($customEndDate)) {
            $startDate = new DateTime($customStartDate);
            $endDate = new DateTime($customEndDate);
        } else {
            $startDate = new DateTime('monday this week');
            $endDate   = (clone $startDate)->modify('+6 days');
        }

        $data['rosterStartDate'] = $startDate->format('Y-m-d');
        $data['rosterEndDate'] = $endDate->format('Y-m-d');
        $data['weekRange'] = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        $data['weekRangeShort'] = $startDate->format('d M') . ' - ' . $endDate->format('d M');
    } catch (Throwable $e) {
        // Absolute fallback
        $data['rosterStartDate'] = date('Y-m-d');
          $data['rosterEndDate'] = date('Y-m-d', strtotime('+6 days'));
        $data['weekRange'] = date('d M Y') . ' - ' . date('d M Y', strtotime('+6 days'));
        $data['weekRangeShort'] = date('d M') . ' - ' . date('d M', strtotime('+6 days'));
    }

    // ---------- Safely simulate GET params ----------
    $_GET['weekRange']       = $data['weekRange'];
    $_GET['rosterStartDate'] = $data['rosterStartDate'];
    $_GET['rosterEndDate'] = $data['rosterEndDate'];

    // ---------- Employees on leave during this roster week ----------
    $data['empLeavesThisWeek'] = [];
    try {
        $this->load->model('Leave_model');
        $weekLeaves = $this->Leave_model->get_leaves_in_range(
            $data['rosterStartDate'],
            $data['rosterEndDate'],
            $locationId
        );
        foreach ($weekLeaves as $wl) {
            if (empty($wl['emp_id'])) { continue; }
            $data['empLeavesThisWeek'][$wl['emp_id']][] = [
                'start_date'   => $wl['start_date'],
                'end_date'     => $wl['end_date'],
                'leave_status' => (int)$wl['leave_status'],
            ];
        }
    } catch (Throwable $e) {
        $data['empLeavesThisWeek'] = [];
    }

    // ---------- Fetch Roster ----------
    $rosterData = [];

    if (method_exists($this, 'fetchRosterByWeek')) {
        $response = $this->fetchRosterByWeek(true);
        if (is_array($response)) {
            $rosterData = $response;
        }
    }

    // ---------- Merge only allowed keys ----------
    $allowedKeys = [
        'rosterId',
        'weekRange',
        'rosterStartDate',
        'rosterInfo',
        'allDayRosterData',
        'debugInfo',  // DEBUG: Added for troubleshooting
        'codeVersion' // DEBUG: Version marker
    ];

    foreach ($allowedKeys as $key) {
        if (isset($rosterData[$key])) {
            $data[$key] = $rosterData[$key];
        }
    }
    
    // Get superannuation config
    $superConfig = $this->common_model->fetchRecordsDynamically('HR_configuration',['data'], ['location' => $this->location_id, 'configureFor' => 'superannuation']);
        
    
    $data['tierBasedEnabled'] = (isset($superConfig[0]['data']) && is_array($config = json_decode($superConfig[0]['data'], true)) && isset($config['enable_tier_payroll']) && $config['enable_tier_payroll'] == '1') ? 1 : 0;
    

    // ---------- Final Safety ----------
    $data['rosterInfo']       = is_array($data['rosterInfo']) ? $data['rosterInfo'] : [];
    $data['allDayRosterData'] = is_array($data['allDayRosterData']) ? $data['allDayRosterData'] : [];
    $data['rosterId']         = (int) ($data['rosterId'] ?? 0);
    
    // DEBUG V4: Force diagnostic info
    $data['codeVersion'] = 'V4_INDEX_FUNCTION';
    $data['debugInfo'] = [
        'totalFromDb' => count($data['allDayRosterData']),
        'rosterDataExists' => !empty($rosterData),
        'rosterDataKeys' => array_keys($rosterData ?? []),
        'allDayRosterDataKeys' => array_keys($data['allDayRosterData'] ?? [])
    ];

    // ---------- Views ----------
    $this->load->view('general/header');
    $this->load->view('roster/roster', $data);
    $this->load->view('general/footer');
}


   public function fetchRosterByWeek($returnData = false) {
    // Fetch and decode query parameters with null coalescing
    $weekRange = urldecode($this->input->get('weekRange', true)) ?? '';
    $rosterStartDate = urldecode($this->input->get('rosterStartDate', true)) ?? '';
    $locationId = $this->location_id;

    // Initialize data array
    $data = [
        'rosterId' => 0,
        'weekRange' => $weekRange,
        'rosterStartDate' => $rosterStartDate,
        'empLists' => $this->employee_model->employeeList('', '', true),
        'positionLists' => $this->common_model->fetchRecordsDynamically('HR_emp_position', '', ['is_deleted' => 0]),
        'prepAreas' => $this->common_model->fetchRecordsDynamically('HR_prepArea', '', ['is_deleted' => 0]),
        'rosterInfo' => [],
        'allDayRosterData' => []
    ];

    // Validate and parse rosterStartDate
    try {
        if (empty($rosterStartDate)) {
            throw new Exception('Roster start date is missing.');
        }
        $startDate = new DateTime($rosterStartDate);
        if (!empty($rosterEndDate)) {
            $endDate = new DateTime($rosterEndDate);
        } else {
            $endDate = (clone $startDate)->modify('+6 days');
        }
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
    } catch (Exception $e) {
        // Fallback to current week's Monday if date is invalid
        $startDate = new DateTime('monday this week');
        $endDate = (clone $startDate)->modify('+6 days');
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
         $data['weekRange'] = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
    }

    // Fetch rosters from HR_roster for the given week range and location
    $whereRoster = [
        'start_date <=' => $endDateFormatted,
        'end_date >=' => $startDateFormatted,
        'location_id' => $locationId,
        'is_deleted' => 0
    ];
    $rosterInfo = $this->common_model->fetchRecordsDynamically('HR_roster', '', $whereRoster);
    $data['rosterInfo'] = $rosterInfo ?? [];

    // Fetch roster details for the matched rosters
    $rosterDetails = [];
    if (isset($rosterInfo) && !empty($rosterInfo)) {
        $rosterId = $rosterInfo[0]['roster_id'] ?? null;
        if (!empty($rosterId)) {
            $whereDetails = ['roster_id' => $rosterId,'is_deleted' => 0];
            $rosterDetails = $this->common_model->fetchRecordsDynamically('HR_roster_details', '', $whereDetails);
        }
    }

    // Format roster details for localStorage
    $allDayRosterData = [];
    $debugKeyCollisions = [];
    $debugRecordCount = count($rosterDetails ?? []);
    
    // DEBUG MARKER V3 - This proves the new code is running
    $data['codeVersion'] = 'V3_UPDATED';
    
    if (isset($rosterDetails) && !empty($rosterDetails)) {
        foreach ($rosterDetails as $idx => $detail) {
            $shiftBoxName = date('d', strtotime($detail['roster_date'] ?? date('Y-m-d'))) . '_' . ($detail['prep_area_id'] ?? 0);
            
            // Create truly unique key using shift_start_time formatted without colons
            // This ensures same employee can have multiple shifts with different start times
            $startTimeKey = isset($detail['shift_start_time']) ? str_replace(':', '', $detail['shift_start_time']) : '';
            $dbId = isset($detail['id']) ? $detail['id'] : $idx;
            
            // Key format: emp_DAY_PREPAREA_EMPLOYEEID_STARTTIME_DBID
            $key = "emp_{$shiftBoxName}_" . ($detail['employee_id'] ?? 0) . "_" . $startTimeKey . "_" . $dbId;
            
            // DEBUG: Track key collisions
            if (isset($allDayRosterData[$key])) {
                $debugKeyCollisions[] = "Key collision at record $idx: $key (emp={$detail['employee_id']}, start={$detail['shift_start_time']}, dbId=$dbId)";
            }

            $dataEmp = [
                'employeeId' => $detail['employee_id'] ?? 0,
                'position_id' => $detail['position_id'] ?? 0,
                'selectedEmpName' => $this->getEmployeeName($detail['employee_id'] ?? 0) ?? 'Unknown',
                'empShiftStartTime' => $detail['shift_start_time'] ?? '',
                'empShiftEndTime' => $detail['shift_end_time'] ?? '',
                'empBreakTime' => $detail['break_start_time'] ?? '',
                'breakType' => $detail['break_type'] ?? '',
                'breakDuration' => $detail['break_duration'] ?? '',
                'taskDescr' => $detail['task_description'] ?? '',
                'rosterDate' => date('d-m-Y', strtotime($detail['roster_date'] ?? date('Y-m-d')))
            ];
            $allDayRosterData[$key] = json_encode($dataEmp, JSON_THROW_ON_ERROR);
        }
    }
    $data['rosterId'] = $rosterId ?? 0;
    $data['allDayRosterData'] = $allDayRosterData;

    // Employees on leave during this roster week
    $data['empLeavesThisWeek'] = [];
    try {
        $this->load->model('Leave_model');
        $weekLeaves = $this->Leave_model->get_leaves_in_range($startDateFormatted, $endDateFormatted, $locationId);
        foreach ($weekLeaves as $wl) {
            if (empty($wl['emp_id'])) { continue; }
            $data['empLeavesThisWeek'][$wl['emp_id']][] = [
                'start_date'   => $wl['start_date'],
                'end_date'     => $wl['end_date'],
                'leave_status' => (int)$wl['leave_status'],
            ];
        }
    } catch (Throwable $e) {
        $data['empLeavesThisWeek'] = [];
    }
    
    // DEBUG: Add collision info
    $data['debugInfo'] = [
        'totalFromDb' => $debugRecordCount,
        'totalAfterProcessing' => count($allDayRosterData),
        'keyCollisions' => $debugKeyCollisions,
        'firstRecordFields' => isset($rosterDetails[0]) ? array_keys($rosterDetails[0]) : [],
        'firstRecordId' => isset($rosterDetails[0]['id']) ? $rosterDetails[0]['id'] : 'NOT_FOUND'
    ];

    if ($returnData) {
        return $data;
    }

    // Load views
    $this->load->view('general/header');
    $this->load->view('roster/roster', $data);
    $this->load->view('general/footer');
}
    
    function rosterList(){
        // Employees see their personal weekly roster instead of the admin list.
        if ((int)$this->roleId === 4) {
            return $this->myRoster();
        }
        $this->session->set_userdata('previous_url', current_url());
     $conditions = array('location_id' => $this->location_id, 'is_deleted' => '0','status'=> 1);
     $data['rosterList'] = $this->common_model->fetchRecordsDynamically('HR_roster','',$conditions);
     $data['roleId'] = $this->roleId;
      $this->load->view('general/header');
	  $this->load->view('roster/rosterList',$data);
	  $this->load->view('general/footer');
    //  echo "<pre>"; print_r($rosterList); exit;
    }

    /**
     * Employee-facing weekly roster ("My Roster").
     * Shows the logged-in employee's published shifts for a chosen week with
     * summary stats, upcoming shifts and a legend. AJAX (?ajax=1) returns just
     * the inner content so the prev/next arrows can swap weeks with a loader.
     */
    public function myRoster()
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $empRow  = $this->common_model->fetchRecordsDynamically('HR_employee', ['emp_id','first_name','last_name'], ['userId' => $user_id]);
        $empId   = isset($empRow[0]['emp_id']) ? $empRow[0]['emp_id'] : '';
        $empName = isset($empRow[0]['first_name']) ? trim($empRow[0]['first_name'].' '.($empRow[0]['last_name'] ?? '')) : '';

        // Week range (default current Mon-Sun, overridable via query params)
        $startParam = $this->input->get('start_date');
        $endParam   = $this->input->get('end_date');
        try {
            if (!empty($startParam)) {
                $weekStart = new DateTime($startParam);
            } else {
                $weekStart = new DateTime('monday this week');
            }
        } catch (Throwable $e) {
            $weekStart = new DateTime('monday this week');
        }
        $weekEnd = (clone $weekStart)->modify('+6 days');
        $startSql = $weekStart->format('Y-m-d');
        $endSql   = $weekEnd->format('Y-m-d');

        // Lookup maps for role (position) and area names
        $posMap = []; $areaMap = [];
        foreach ((array)$this->common_model->fetchRecordsDynamically('HR_emp_position', ['position_id','position_name'], ['is_deleted' => 0]) as $p) {
            $posMap[$p['position_id']] = $p['position_name'];
        }
        foreach ((array)$this->common_model->fetchRecordsDynamically('HR_prepArea', ['id','prep_name'], ['is_deleted' => 0]) as $a) {
            $areaMap[$a['id']] = $a['prep_name'];
        }

        // Published shifts for this employee in the week
        $shifts = [];
        if (!empty($empId)) {
            $shifts = $this->tenantDb->select('rd.*')
                ->from('HR_roster_details rd')
                ->join('HR_roster r', 'r.roster_id = rd.roster_id', 'inner')
                ->where('rd.employee_id', $empId)
                ->where('rd.is_deleted', 0)
                ->where('r.is_deleted', 0)
                ->where('r.is_published', 1)
                ->where('rd.roster_date >=', $startSql)
                ->where('rd.roster_date <=', $endSql)
                ->order_by('rd.roster_date', 'ASC')
                ->order_by('rd.shift_start_time', 'ASC')
                ->get()->result_array();
        }

        // Build day map keyed by date; compute totals
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $d = (clone $weekStart)->modify("+{$i} days");
            $days[$d->format('Y-m-d')] = [];
        }
        $totalSeconds = 0; $shiftCount = 0; $upcoming = [];
        $todaySql = date('Y-m-d');
        foreach ($shifts as $s) {
            $date = $s['roster_date'];
            $st = !empty($s['shift_start_time']) ? $s['shift_start_time'] : '';
            $et = !empty($s['shift_end_time']) ? $s['shift_end_time'] : '';
            $hrs = 0;
            if ($st && $et) {
                $diff = strtotime($date.' '.$et) - strtotime($date.' '.$st);
                // Overnight shift: end time is on the next day (e.g. 5 PM -> 12 AM)
                // if ($diff <= 0) { $diff += 86400; }
                if ($diff > 0) { $totalSeconds += $diff; $shiftCount++; $hrs = $diff/3600; }
            }
            $entry = [
                'start' => $st, 'end' => $et, 'hours' => $hrs,
                'role'  => isset($posMap[$s['position_id']]) ? $posMap[$s['position_id']] : '',
                'area'  => isset($areaMap[$s['prep_area_id']]) ? $areaMap[$s['prep_area_id']] : '',
                'break' => $s['break_duration'] ?? '',
                'notes' => $s['task_description'] ?? '',
            ];
            if (isset($days[$date])) { $days[$date][] = $entry; }
            if ($date >= $todaySql) { $upcoming[] = $entry + ['date' => $date]; }
        }

        $data = [
            'empName'      => $empName,
            'weekStart'    => $startSql,
            'weekEnd'      => $endSql,
            'weekRange'    => $weekStart->format('d') . ' - ' . $weekEnd->format('d M Y'),
            'prevStart'    => (clone $weekStart)->modify('-7 days')->format('Y-m-d'),
            'nextStart'    => (clone $weekStart)->modify('+7 days')->format('Y-m-d'),
            'days'         => $days,
            'totalSeconds' => $totalSeconds,
            'shiftCount'   => $shiftCount,
            'upcoming'     => array_slice($upcoming, 0, 4),
            'locationName' => $this->session->userdata('location_name'),
        ];

        if ($this->input->get('ajax')) {
            $this->load->view('roster/myRoster', $data);
            return;
        }
        $this->load->view('general/header');
        $this->load->view('roster/myRoster', $data);
        $this->load->view('general/footer');
    }
    

// add roster details at the same time populate timesheet table also with the employee from roster table , save roster
   public function addRoster() {
        // Get the posted data
        $empDatas = $this->input->post();
        $parentTimesheetId = null;
        
        // DEBUG: Count how many emp_ keys we received
        $empKeyCount = 0;
        $empKeys = [];
        foreach ($empDatas as $key => $value) {
            if (strpos($key, 'emp_') === 0) {
                $empKeyCount++;
                $empKeys[] = $key;
            }
        }
        error_log("addRoster DEBUG: Received $empKeyCount emp_ keys: " . implode(', ', $empKeys));

        // Parse the week range (e.g., "26 May - 01 Jun")
        $rosterWeek = $this->createDateForRoster($empDatas['week']);
        $startDate = new DateTime($rosterWeek['start_date']);
        $endDate = new DateTime($rosterWeek['end_date']);
        $endDate->modify('+1 day');

        // Prepare roster data for the HR_roster table
        $conditions = [
            'location_id' => $this->location_id,
            'is_deleted' => '0',
            'start_date' => $rosterWeek['start_date']
        ];

        // VALIDATION 1: Check for week conflicts (improved - check entire week overlap)
        $this->tenantDb->where('location_id', $this->location_id);
        $this->tenantDb->where('is_deleted', 0);
        $this->tenantDb->group_start();
        // Check if new roster overlaps with any existing roster
        $this->tenantDb->where('start_date <=', $rosterWeek['end_date']);
        $this->tenantDb->where('end_date >=', $rosterWeek['start_date']);
        $this->tenantDb->group_end();
        $existingRosterOfThisWeek = $this->tenantDb->get('HR_roster')->result_array();
        
        // Check timesheet conflicts
        $this->tenantDb->where('location_id', $this->location_id);
        $this->tenantDb->where('is_deleted', 0);
        $this->tenantDb->group_start();
        $this->tenantDb->where('date_from <=', $rosterWeek['end_date']);
        $this->tenantDb->where('date_to >=', $rosterWeek['start_date']);
        $this->tenantDb->group_end();
        $existingTimesheetOfThisWeek = $this->tenantDb->get('HR_timesheet')->result_array();
        $updateRecord = false;
        $rosterData = [
            'start_date' => $rosterWeek['start_date'],
            'end_date' => $rosterWeek['end_date'],
            'location_id' => $this->location_id,
            'rosterName' => $empDatas['rosterName'] ?: date('d-m-Y', strtotime($rosterWeek['start_date'])) . ' to ' . date('d-m-Y', strtotime($rosterWeek['end_date'])),
            'is_published' => ($empDatas['savetype'] == 'publish' ? 1 : 0),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $timesheetData = [
            'date_from' => $rosterWeek['start_date'],
            'date_to' => $rosterWeek['end_date'],
            'status' => 1,
            'location_id' => $this->location_id,
            'is_published'=>$rosterData['is_published'],
            'is_timesheet_without_roster' => 0,
            ];

        $this->tenantDb->trans_start();
        if (!empty($existingRosterOfThisWeek)) {
            // Check if it's exact same week (update case) or overlapping weeks (error)
            $exactMatch = false;
            foreach ($existingRosterOfThisWeek as $existing) {
                if ($existing['start_date'] === $rosterWeek['start_date'] && 
                    $existing['end_date'] === $rosterWeek['end_date']) {
                    $exactMatch = true;
                    $rosterId = $existing['roster_id'];
                    break;
                }
            }
            
            if (!$exactMatch) {
                echo json_encode(['status' => 'error', 'message' => 'This week overlaps with an existing roster. Please choose a different week.']);
                return;
            }
            
            // Update existing roster case
            $updateRecord = true;
            $this->common_model->commonRecordUpdate('HR_roster', 'roster_id', $rosterId, $rosterData);
            // Change the timesheet status to published as well
            $timesheetUpdateData['is_published'] = $rosterData['is_published'];
            $this->common_model->commonRecordUpdate('HR_timesheet', 'roster_id', $rosterId, $timesheetUpdateData);
            $parentTimesheetId = $existingTimesheetOfThisWeek[0]['id'];
          
        } else if(!empty($existingTimesheetOfThisWeek)){
            // for timesheet created  without roster case
           // timesheet already exist for thhis week so we cannot create another roster/timehseet for this week for this location
           echo json_encode(['status' => 'error', 'message' => 'Roster/Timesheet already exist for this week.']);
            return;
        }else{
            // create fresh new  roster/timesheet case
           $rosterData['created_at'] = date('Y-m-d H:i:s');
            $rosterId = $this->common_model->commonRecordCreate('HR_roster', $rosterData);
              //make entry in timesheet teble so we can show on listing page , added on 25-11-2025 after making updating timesheet table names 
            $timesheetData['roster_id'] = $rosterId;
            $parentTimesheetId = $this->common_model->commonRecordCreate('HR_timesheet', $timesheetData);  
        }

        // Prepare roster details with validation
        $rosterData = [];
        $employeeScheduleCheck = []; // Track employee schedules to prevent duplicates/overlaps
        
        foreach ($empDatas as $key => $value) {
            // Pattern supports optional 4th segment for unique identifier (to allow multiple shifts for same employee/day/prep area)
            // Pattern: emp_DAY_PREPAREA_EMPLOYEEID_STARTTIME_DBID or older formats
            if (!preg_match('/^emp_\d+_\d+_\d+(_\d+)?(_\d+)?$/', $key)) continue;
            $shiftData = json_decode($value, true);
            if (!$shiftData) continue;

            $keyParts = explode('_', $key);
            $prepAreaId = isset($keyParts[2]) ? (int)$keyParts[2] : null;
            if (!$prepAreaId) continue;

            $rosterDate = DateTime::createFromFormat('d-m-Y', $shiftData['rosterDate']);
            if (!$rosterDate) continue;
            $formattedRosterDate = $rosterDate->format('Y-m-d');

            // VALIDATION 2: Validate shift times
            $shiftStartTime = !empty($shiftData['empShiftStartTime']) ? $this->convertTo24HourFormat($shiftData['empShiftStartTime']) : null;
            $shiftEndTime = !empty($shiftData['empShiftEndTime']) ? $this->convertTo24HourFormat($shiftData['empShiftEndTime']) : null;
            $breakStartTime = !empty($shiftData['empBreakTime']) ? $this->convertTo24HourFormat($shiftData['empBreakTime']) : null;
            
            // Get employee name and day name for better error messages
            $employeeName = !empty($shiftData['selectedEmpName']) ? $shiftData['selectedEmpName'] : 'Employee ID: ' . $shiftData['employeeId'];
            $dayName = $rosterDate->format('l'); // Gets day name like "Monday", "Tuesday", etc.
            $dateFormatted = $rosterDate->format('d M Y'); // Gets formatted date like "05 Feb 2026"
            
            // Determine if this is an overnight shift (end time is on the next day)
            // Overnight shift: start time is PM and end time is AM (e.g., 10 PM to 2 AM)
            $isOvernightShift = false;
            if ($shiftStartTime && $shiftEndTime) {
                $startSeconds = strtotime($shiftStartTime);
                $endSeconds = strtotime($shiftEndTime);
                // If end time appears earlier than or equal to start time, it's an overnight shift
                $isOvernightShift = ($endSeconds <= $startSeconds);
            }
            
            // Validate that start and end times are not exactly the same (0 hour shift is invalid)
            if ($shiftStartTime && $shiftEndTime && $shiftStartTime === $shiftEndTime) {
                echo json_encode(['status' => 'error', 'message' => 'Shift start and end time cannot be the same for ' . $employeeName . ' on ' . $dayName . ' (' . $dateFormatted . ')']);
                return;
            }
            
            // Validate break time is within shift hours (handles overnight shifts)
            if ($breakStartTime && $shiftStartTime && $shiftEndTime) {
                $breakSeconds = strtotime($breakStartTime);
                $startSeconds = strtotime($shiftStartTime);
                $endSeconds = strtotime($shiftEndTime);
                
                if ($isOvernightShift) {
                    // For overnight shifts, break is valid if:
                    // - It's after the start time (evening portion), OR
                    // - It's before the end time (morning portion of next day)
                    $isBreakValid = ($breakSeconds >= $startSeconds) || ($breakSeconds <= $endSeconds);
                } else {
                    // For regular shifts, break must be within start and end times
                    $isBreakValid = ($breakSeconds >= $startSeconds && $breakSeconds <= $endSeconds);
                }
                
                if (!$isBreakValid) {
                    echo json_encode(['status' => 'error', 'message' => 'Break time must be within shift hours for ' . $employeeName . ' on ' . $dayName . ' (' . $dateFormatted . ')']);
                    return;
                }
            }
            
            $employeeId = $shiftData['employeeId'];
            
            // VALIDATION 3: Check for time overlaps for same employee on same date (allows multiple non-overlapping shifts)
            $empDateKey = $employeeId . '_' . $formattedRosterDate;
            if (isset($employeeScheduleCheck[$empDateKey]) && $shiftStartTime && $shiftEndTime) {
                foreach ($employeeScheduleCheck[$empDateKey] as $existingShift) {
                    $existingStart = strtotime($existingShift['start']);
                    $existingEnd = strtotime($existingShift['end']);
                    $newStart = strtotime($shiftStartTime);
                    $newEnd = strtotime($shiftEndTime);
                    $existingIsOvernight = ($existingEnd <= $existingStart);
                    
                    // Calculate effective times (add 24 hours for overnight end times for comparison)
                    $effectiveNewEnd = $isOvernightShift ? $newEnd + 86400 : $newEnd;
                    $effectiveExistingEnd = $existingIsOvernight ? $existingEnd + 86400 : $existingEnd;
                    
                    // Check if times overlap using effective end times
                    $noOverlap = ($effectiveNewEnd <= $existingStart) || ($newStart >= $effectiveExistingEnd);
                    
                    if (!$noOverlap) {
                        echo json_encode(['status' => 'error', 'message' => $employeeName . ' has overlapping shifts on ' . $dayName . ' (' . $dateFormatted . ')']);
                        return;
                    }
                }
            }
            
            // Track this schedule for overlap detection
            if (!isset($employeeScheduleCheck[$empDateKey])) {
                $employeeScheduleCheck[$empDateKey] = [];
            }
            $employeeScheduleCheck[$empDateKey][] = [
                'start' => $shiftStartTime,
                'end' => $shiftEndTime,
                'prep_area' => $prepAreaId,
                'is_overnight' => $isOvernightShift
            ];

            $rosterData[] = [
                'roster_id' => $rosterId,
                'employee_id' => $employeeId,
                'position_id' => $shiftData['position_id'] ?: null,
                'prep_area_id' => $prepAreaId,
                'roster_date' => $formattedRosterDate,
                'shift_start_time' => $shiftStartTime,
                'shift_end_time' => $shiftEndTime,
                'break_start_time' => $breakStartTime,
                'break_type' => $shiftData['breakType'],
                'break_duration' => $shiftData['breakDuration'],
                'task_description' => !empty($shiftData['taskDescr']) ? $shiftData['taskDescr'] : null,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        // DEBUG: Log how many roster entries we're about to save
        error_log("addRoster DEBUG: Built " . count($rosterData) . " roster entries to save");
        foreach ($rosterData as $idx => $rd) {
            error_log("addRoster DEBUG: Entry $idx - emp={$rd['employee_id']}, date={$rd['roster_date']}, prep={$rd['prep_area_id']}, start={$rd['shift_start_time']}");
        }

        // Synchronize roster details
        if ($updateRecord) {
            $syncResult = $this->synchronizeRosterDetails($rosterId, $rosterData);
            if (isset($syncResult['status']) && $syncResult['status'] === 'error') {
                $this->tenantDb->trans_rollback();
                echo json_encode($syncResult);
                return;
            }
        } else {
            if (!empty($rosterData)) {
                // Remove 'id' field from each record for new inserts
                foreach ($rosterData as &$record) {
                    unset($record['id']);
                }
                unset($record);
                
                log_message('error', 'addRoster: Creating new roster details - sample data: ' . json_encode($rosterData[0]));
                $this->common_model->commonBulkRecordCreate('HR_roster_details', $rosterData);
                
                // Check for errors
                $dbError = $this->tenantDb->error();
                if (!empty($dbError['code']) && $dbError['code'] != 0) {
                    log_message('error', 'addRoster: New roster insert failed - ' . json_encode($dbError));
                    $this->tenantDb->trans_rollback();
                    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $dbError['message']]);
                    return;
                }
            }
        }

        // Synchronize timesheet
        $timesheetResult = $this->synchronizeTimesheetFromRoster($rosterId,$parentTimesheetId);
        if (isset($timesheetResult['status']) && $timesheetResult['status'] === 'error') {
            $this->tenantDb->trans_rollback();
            echo json_encode($timesheetResult);
            return;
        }

        $this->tenantDb->trans_complete();
        if ($this->tenantDb->trans_status() === FALSE) {
            // Log the actual database error for debugging
            $error = $this->tenantDb->error();
            log_message('error', 'addRoster transaction failed: ' . json_encode($error));
            log_message('error', 'Last query: ' . $this->tenantDb->last_query());
            echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . ($error['message'] ?? 'Unknown error')]);
            return;
        }

        $message = $updateRecord ? 'Roster updated successfully' : 'Roster created successfully';
        // DEBUG: Include count info in response
        $message .= ' (DEBUG: ' . count($rosterData) . ' shifts saved)';
        echo json_encode(['status' => 'success', 'message' => $message, 'debug_shift_count' => count($rosterData)]);
    }
    
// used when we create/edit a roster we have to accordingly make changes in timesheet table
    public function synchronizeRosterDetails($rosterId, $newRosterData) {
        // CRITICAL: Validate input to prevent accidental deletion of all records
        if (empty($rosterId) || !is_numeric($rosterId)) {
            log_message('error', 'synchronizeRosterDetails: Invalid roster_id - ' . $rosterId);
            return ['status' => 'error', 'message' => 'Invalid roster ID'];
        }
        
        // SECURITY: Verify roster belongs to current location
        $rosterCheck = $this->common_model->fetchRecordsDynamically('HR_roster', ['roster_id'], [
            'roster_id' => $rosterId,
            'location_id' => $this->location_id,
            'is_deleted' => 0
        ]);
        
        if (empty($rosterCheck)) {
            log_message('error', 'SECURITY VIOLATION: Attempt to modify roster from different location. Roster ID: ' . $rosterId . ', Location ID: ' . $this->location_id);
            return ['status' => 'error', 'message' => 'Unauthorized roster access'];
        }
        
        // CRITICAL: If newRosterData is empty, DON'T delete everything - this is likely a bug
        if (empty($newRosterData) || !is_array($newRosterData)) {
            log_message('error', 'synchronizeRosterDetails: Empty roster data for roster_id ' . $rosterId . ' - SKIPPING to prevent data loss!');
            return ['status' => 'warning', 'message' => 'No roster data to synchronize'];
        }
        
        // Validate data structure
        foreach ($newRosterData as $data) {
            if (!isset($data['employee_id']) || !isset($data['roster_date']) || empty($data['employee_id'])) {
                log_message('error', 'synchronizeRosterDetails: Invalid data structure - missing employee_id or roster_date for roster_id ' . $rosterId);
                return ['status' => 'error', 'message' => 'Invalid roster data structure'];
            }
        }
        
        log_message('error', 'synchronizeRosterDetails: Processing ' . count($newRosterData) . ' entries for roster_id ' . $rosterId);
        
        // HARD DELETE approach: Delete all existing, then bulk insert all new
        // Soft-delete doesn't work because unique key doesn't include is_deleted column
        
        // Step 1: HARD DELETE all existing roster details for this roster
        $this->tenantDb->where('roster_id', $rosterId);
        $this->tenantDb->delete('HR_roster_details');
        
        log_message('error', 'synchronizeRosterDetails: Hard-deleted existing entries, affected rows: ' . $this->tenantDb->affected_rows());
        
        // Step 2: Bulk insert all new roster details
        if (!empty($newRosterData)) {
            // Ensure all records have the correct roster_id and timestamps
            // Also remove 'id' field if present (let DB auto-increment handle it)
            foreach ($newRosterData as &$record) {
                unset($record['id']); // Remove id to let auto-increment work
                $record['roster_id'] = $rosterId;
                $record['is_deleted'] = 0;
                $record['created_at'] = date('Y-m-d H:i:s');
                $record['updated_at'] = date('Y-m-d H:i:s');
            }
            unset($record);
            
            // Log ALL data for debugging - see what's actually being inserted
            log_message('error', 'synchronizeRosterDetails: Total records to insert: ' . count($newRosterData));
            foreach ($newRosterData as $idx => $rec) {
                log_message('error', 'synchronizeRosterDetails: Record ' . $idx . ': emp=' . $rec['employee_id'] . ', date=' . $rec['roster_date'] . ', prep=' . $rec['prep_area_id'] . ', start=' . $rec['shift_start_time']);
            }
            
            $result = $this->common_model->commonBulkRecordCreate('HR_roster_details', $newRosterData);
            $affectedRows = $this->tenantDb->affected_rows();
            
            // Check for errors after insert
            $dbError = $this->tenantDb->error();
            if (!empty($dbError['code']) && $dbError['code'] != 0) {
                log_message('error', 'synchronizeRosterDetails: Insert failed - ' . json_encode($dbError));
                log_message('error', 'synchronizeRosterDetails: Last query - ' . $this->tenantDb->last_query());
                return ['status' => 'error', 'message' => 'Database insert failed: ' . $dbError['message']];
            }
            
            log_message('error', 'synchronizeRosterDetails: Requested ' . count($newRosterData) . ' inserts, affected_rows=' . $affectedRows);
        }
        
        return ['status' => 'success', 'message' => 'Roster details synchronized successfully'];
    }
    
// used when we create/edit a roster we have to accordingly make changes in timesheet table
    public function synchronizeTimesheetFromRoster($rosterId,$parentTimesheetId='') {
        log_message('error', 'synchronizeTimesheetFromRoster: Starting for roster_id ' . $rosterId);
        
        // Validate roster_id
        if (empty($rosterId) || !is_numeric($rosterId)) {
            log_message('error', 'Invalid roster_id provided to synchronizeTimesheetFromRoster');
            return ['status' => 'error', 'message' => 'Invalid roster ID'];
        }
        
        // SECURITY: Verify roster belongs to current location
        $rosterCheck = $this->common_model->fetchRecordsDynamically('HR_roster', ['roster_id', 'location_id'], [
            'roster_id' => $rosterId,
            'location_id' => $this->location_id,
            'is_deleted' => 0
        ]);
        
        if (empty($rosterCheck)) {
            log_message('error', 'SECURITY VIOLATION: Attempt to access roster from different location. Roster ID: ' . $rosterId . ', Location ID: ' . $this->location_id);
            return ['status' => 'error', 'message' => 'Unauthorized roster access'];
        }

        // Fetch roster details
        $rosterDetails = $this->common_model->fetchRecordsDynamically(
            'HR_roster_details',
            [],
            ['roster_id' => $rosterId, 'is_deleted' => 0]
        );

        // CRITICAL: If no roster details found, DON'T delete all timesheets
        if (empty($rosterDetails)) {
            log_message('error', 'synchronizeTimesheetFromRoster: No roster details for roster_id ' . $rosterId . ' - SKIPPING to prevent timesheet deletion!');
            return ['status' => 'warning', 'message' => 'No roster details found - timesheets preserved'];
        }
        
        log_message('error', 'synchronizeTimesheetFromRoster: Processing ' . count($rosterDetails) . ' roster entries for roster_id ' . $rosterId);

        // Fetch all existing timesheet entries that have clock data (to preserve)
        $existingTimesheets = $this->common_model->fetchRecordsDynamically(
            'HR_timesheet_details',
            ['timesheet_id', 'employee_id', 'roster_date', 'prep_area_id', 'roster_start_time', 'clock_in_time', 'clock_out_time', 'actual_break_duration', 'approval_status', 'is_deleted'],
            ['roster_id' => $rosterId]
        );
        
        // Build a map of timesheet entries that have clock data (employee_id + date + prep_area + start_time)
        // We need to preserve these
        $clockedInTimesheets = [];
        foreach ($existingTimesheets as $ts) {
            if (!empty($ts['clock_in_time']) || !empty($ts['clock_out_time'])) {
                // Store by a composite key that can handle multiple entries
                $clockedInTimesheets[] = [
                    'timesheet_id' => $ts['timesheet_id'],
                    'employee_id' => $ts['employee_id'],
                    'roster_date' => $ts['roster_date'],
                    'prep_area_id' => $ts['prep_area_id'],
                    'roster_start_time' => $ts['roster_start_time'],
                    'clock_in_time' => $ts['clock_in_time'],
                    'clock_out_time' => $ts['clock_out_time'],
                    'actual_break_duration' => $ts['actual_break_duration'],
                    'approval_status' => $ts['approval_status']
                ];
            }
        }
        
        log_message('error', 'synchronizeTimesheetFromRoster: Found ' . count($clockedInTimesheets) . ' entries with clock data to preserve');
        
        // HARD DELETE approach (unique key doesn't include is_deleted)
        // But preserve entries that have clock data
        
        // Step 1: Hard delete timesheet details that DON'T have clock data
        $this->tenantDb->where('roster_id', $rosterId);
        $this->tenantDb->group_start();
        $this->tenantDb->where('clock_in_time IS NULL', null, false);
        $this->tenantDb->where('clock_out_time IS NULL', null, false);
        $this->tenantDb->group_end();
        $this->tenantDb->delete('HR_timesheet_details');
        
        log_message('error', 'synchronizeTimesheetFromRoster: Hard-deleted entries without clock data');

        // Step 2: Process each roster detail
        $recordsToInsert = [];
        $usedClockedTimesheets = []; // Track which clocked timesheets we've used
        
        foreach ($rosterDetails as $detail) {
            $timesheetData = [
                'roster_id' => $rosterId,
                'employee_id' => $detail['employee_id'] ?? 0,
                'prep_area_id' => $detail['prep_area_id'] ?? 0,
                'position_id' => $detail['position_id'] ?? 0,
                'roster_date' => $detail['roster_date'] ?? date('Y-m-d'),
                'roster_start_time' => $detail['shift_start_time'] ?? null,
                'roster_end_time' => $detail['shift_end_time'] ?? null,
                'roster_break_start_time' => $detail['break_start_time'] ?? null,
                'roster_break_duration' => $detail['break_duration'] ?? 0,
                'roster_break_type' => $detail['break_type'] ?? '',
                'task_description' => $detail['task_description'] ?? '',
                'approval_status' => 'pending',
                'is_deleted' => 0,
                'status' => 1,
                'location_id' => $this->location_id,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Check if there's a matching clocked timesheet entry that hasn't been used yet
            // FIX: Don't require roster_start_time to match - the roster time might have changed!
            // Match by employee_id + roster_date first, then optionally by prep_area_id
            $matchingClocked = null;
            foreach ($clockedInTimesheets as $idx => $clocked) {
                if (in_array($idx, $usedClockedTimesheets)) {
                    continue; // Already used this one
                }
                // Primary match: employee_id + roster_date (roster time can change)
                if ($clocked['employee_id'] == $detail['employee_id'] && 
                    $clocked['roster_date'] == $detail['roster_date']) {
                    // If prep_area matches too, prefer this match
                    if ($clocked['prep_area_id'] == $detail['prep_area_id']) {
                        $matchingClocked = $clocked;
                        $usedClockedTimesheets[] = $idx;
                        log_message('error', 'synchronizeTimesheetFromRoster: Found matching clocked timesheet ' . $clocked['timesheet_id'] . ' for employee ' . $detail['employee_id'] . ' on ' . $detail['roster_date']);
                        break;
                    } elseif ($matchingClocked === null) {
                        // Fallback: match by employee + date only (prep area changed)
                        $matchingClocked = $clocked;
                        $usedClockedTimesheets[] = $idx;
                    }
                }
            }
            
            if ($matchingClocked) {
                // Update existing timesheet to restore it and preserve clock data
                $timesheetData['clock_in_time'] = $matchingClocked['clock_in_time'];
                $timesheetData['clock_out_time'] = $matchingClocked['clock_out_time'];
                $timesheetData['actual_break_duration'] = $matchingClocked['actual_break_duration'];
                $timesheetData['approval_status'] = $matchingClocked['approval_status'];
                
                $this->common_model->commonRecordUpdate(
                    'HR_timesheet_details',
                    'timesheet_id',
                    $matchingClocked['timesheet_id'],
                    $timesheetData
                );
                log_message('error', 'synchronizeTimesheetFromRoster: Updated existing timesheet ' . $matchingClocked['timesheet_id'] . ' with new roster times');
            } else {
                // SAFEGUARD: Before inserting new entry, check if one already exists with clock data
                // This prevents duplicates when roster time changes
                $existingWithClock = $this->common_model->fetchRecordsDynamically(
                    'HR_timesheet_details',
                    ['timesheet_id', 'clock_in_time', 'clock_out_time', 'actual_break_duration', 'approval_status'],
                    [
                        'parent_timesheet_id' => $parentTimesheetId,
                        'employee_id' => $detail['employee_id'],
                        'roster_date' => $detail['roster_date'],
                        'is_deleted' => 0
                    ]
                );
                
                // Filter to only get entries with clock data
                $clockedEntry = null;
                foreach ($existingWithClock as $existing) {
                    if (!empty($existing['clock_in_time']) || !empty($existing['clock_out_time'])) {
                        $clockedEntry = $existing;
                        break;
                    }
                }
                
                if ($clockedEntry) {
                    // Update existing entry instead of creating duplicate
                    log_message('error', 'synchronizeTimesheetFromRoster: SAFEGUARD - Found existing clocked timesheet ' . $clockedEntry['timesheet_id'] . ' for employee ' . $detail['employee_id'] . ' on ' . $detail['roster_date'] . '. Updating instead of inserting.');
                    $timesheetData['clock_in_time'] = $clockedEntry['clock_in_time'];
                    $timesheetData['clock_out_time'] = $clockedEntry['clock_out_time'];
                    $timesheetData['actual_break_duration'] = $clockedEntry['actual_break_duration'];
                    $timesheetData['approval_status'] = $clockedEntry['approval_status'];
                    
                    $this->common_model->commonRecordUpdate(
                        'HR_timesheet_details',
                        'timesheet_id',
                        $clockedEntry['timesheet_id'],
                        $timesheetData
                    );
                } else {
                    // No existing entry with clock data - safe to create new
                    $timesheetData['clock_in_time'] = null;
                    $timesheetData['clock_out_time'] = null;
                    $timesheetData['parent_timesheet_id'] = $parentTimesheetId;
                    $timesheetData['actual_break_duration'] = 0;
                    $timesheetData['created_at'] = date('Y-m-d H:i:s');
                    $recordsToInsert[] = $timesheetData;
                }
            }
        }

        // Bulk insert new timesheet entries
        if (!empty($recordsToInsert)) {
            // Remove 'timesheet_id' if present (let DB auto-increment handle it)
            foreach ($recordsToInsert as &$record) {
                unset($record['timesheet_id']);
            }
            unset($record);
            
            log_message('error', 'synchronizeTimesheetFromRoster: Sample timesheet data: ' . json_encode($recordsToInsert[0]));
            
            $this->common_model->commonBulkRecordCreate('HR_timesheet_details', $recordsToInsert);
            
            // Check for errors
            $dbError = $this->tenantDb->error();
            if (!empty($dbError['code']) && $dbError['code'] != 0) {
                log_message('error', 'synchronizeTimesheetFromRoster: Insert failed - ' . json_encode($dbError));
                log_message('error', 'synchronizeTimesheetFromRoster: Last query - ' . $this->tenantDb->last_query());
                return ['status' => 'error', 'message' => 'Timesheet insert failed: ' . $dbError['message']];
            }
            
            log_message('error', 'synchronizeTimesheetFromRoster: Inserted ' . count($recordsToInsert) . ' new timesheet entries successfully');
        }

        return ['status' => 'success', 'message' => 'Timesheet synchronized successfully'];
    }

/**
 * Convert time from 12-hour format (e.g., "9:00 AM") to 24-hour format (e.g., "09:00:00")
 * @param string $time Time in 12-hour format
 * @return string|null Time in 24-hour format or null if invalid
 */
  private function convertTo24HourFormat($time) {
    if (empty($time)) {
        return null;
    }

    // Normalize the input: trim whitespace, convert AM/PM to uppercase
    $time = trim($time);
    $time = preg_replace('/\s+/', ' ', $time); // Ensure single space between time and AM/PM
    $time = str_replace(['am', 'pm'], ['AM', 'PM'], strtolower($time));

    try {
        // First, check if the time is already in 24-hour format (HH:MM:SS)
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            // Validate the time components
            list($hours, $minutes, $seconds) = explode(':', $time);
            if ($hours >= 0 && $hours <= 23 && $minutes >= 0 && $minutes <= 59 && $seconds >= 0 && $seconds <= 59) {
                return $time; // Already in 24-hour format, return as-is
            }
        }

        // Try parsing as 12-hour format
        $dateTime = DateTime::createFromFormat('h:i A', $time); // e.g., "11:00 AM"
        if ($dateTime === false) {
            $dateTime = DateTime::createFromFormat('g:i A', $time); // e.g., "9:00 AM"
        }
        if ($dateTime === false) {
            $dateTime = DateTime::createFromFormat('h:ia', $time); // e.g., "11:00AM"
        }
        if ($dateTime === false) {
            $dateTime = DateTime::createFromFormat('g:ia', $time); // e.g., "9:00AM"
        }

        if ($dateTime === false) {
            log_message('error', "Invalid time format: $time");
            return null;
        }

        return $dateTime->format('H:i:s');
    } catch (Exception $e) {
        log_message('error', "Error converting time: $time, Error: " . $e->getMessage());
        return null;
    }
}
 
  
 
    
   function createDateForRoster($string){
        // Check if the string contains full dates (YYYY-MM-DD format)
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $string)) {
            $parts = explode(" - ", $string);
            $resultArray['start_date'] = trim($parts[0]);
            $resultArray['end_date'] = trim($parts[1]);
            return $resultArray;
        }
        
        // Handle traditional "29 Dec - 04 Jan" format
        $parts = explode(" - ", $string);
        $start_date = trim($parts[0]);
        $end_date = trim($parts[1]);
        
        // Get current year
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // Parse start date
        $start_timestamp = strtotime("$start_date $currentYear");
        $start_month = date('n', $start_timestamp);
        
        // Parse end date - handle year transition
        $end_timestamp = strtotime("$end_date $currentYear");
        $end_month = date('n', $end_timestamp);
        
        // If end month is less than start month, it's likely next year
        if ($end_month < $start_month) {
            $end_timestamp = strtotime("$end_date " . ($currentYear + 1));
        }
        // If we're in December and start date is in December but end date appears to be in January
        elseif ($currentMonth == 12 && $start_month == 12 && $end_month == 1) {
            $end_timestamp = strtotime("$end_date " . ($currentYear + 1));
        }
        
        // Convert timestamps to the desired format
        $start_date_formatted = date('Y-m-d', $start_timestamp);
        $end_date_formatted = date('Y-m-d', $end_timestamp);
        $resultArray['start_date'] = $start_date_formatted;
        $resultArray['end_date'] = $end_date_formatted;
        
        return $resultArray;
    }
    

    // working code
    public function rosterView($roster_id = 0) {
  $data['rosterId'] = $roster_id;
  $data['weekRange'] = $this->input->get('weekRange') ?? '';
  $data['rosterStartDate'] = $this->input->get('rosterStartDate') ?? '';
  
  // Get current user's employee ID and role
  $user_id = $this->ion_auth->user()->row()->id;
  $empData = $this->common_model->fetchRecordsDynamically('HR_employee', ['emp_id'], ['userId'=>$user_id]);
  $currentEmpId = (isset($empData[0]['emp_id']) ? $empData[0]['emp_id'] : '');
  $data['roleId'] = $this->roleId;
  $data['currentEmpId'] = $currentEmpId;
  
  // For employee role (4), only show their own data
  if($this->roleId == 4 && !empty($currentEmpId)){
      $data['empLists'] = $this->employee_model->employeeList('','',true);
      // Filter to only current employee
      $data['empLists'] = array_filter($data['empLists'], function($emp) use ($currentEmpId) {
          return isset($emp['emp_id']) && $emp['emp_id'] == $currentEmpId;
      });
  } else {
      $data['empLists'] =  $this->employee_model->employeeList('','',true);
  }
  
  $data['positionLists'] = $this->common_model->fetchRecordsDynamically('HR_emp_position','', ['is_deleted' => 0]);
  $data['prepAreas'] = $this->common_model->fetchRecordsDynamically('HR_prepArea','', ['is_deleted' => 0]);

  // Fetch roster info
  
  $data['rosterInfo'] = $this->common_model->fetchRecordsDynamically('HR_roster','', ['roster_id' => $roster_id]);
  
  // Fetch roster details and format for localStorage
  // For employee role, only fetch their own shifts
  $rosterConditions = ['roster_id' => $roster_id,'is_deleted' => 0];
  if($this->roleId == 4 && !empty($currentEmpId)){
      $rosterConditions['employee_id'] = $currentEmpId;
  }
  $rosterDetails = $this->common_model->fetchRecordsDynamically('HR_roster_details','', $rosterConditions);
 
  $allDayRosterData = [];
  foreach ($rosterDetails as $idx => $detail) {
    $shiftBoxName = date('d', strtotime($detail['roster_date'])) . '_' . $detail['prep_area_id'];
    
    // Create unique key including shift_start_time and db id to allow multiple shifts per employee
    $startTimeKey = isset($detail['shift_start_time']) ? str_replace(':', '', $detail['shift_start_time']) : '';
    $dbId = isset($detail['id']) ? $detail['id'] : $idx;
    $key = "emp_{$shiftBoxName}_{$detail['employee_id']}_{$startTimeKey}_{$dbId}";
    
    $dataEmp = [
      'employeeId' => $detail['employee_id'],
      'position_id' => $detail['position_id'],
      'selectedEmpName' => $this->getEmployeeName($detail['employee_id']),
      'empShiftStartTime' => $detail['shift_start_time'],
      'empShiftEndTime' => $detail['shift_end_time'],
      'empBreakTime' => $detail['break_start_time'],
      'breakType' => $detail['break_type'],
      'breakDuration' => $detail['break_duration'],
      'taskDescr' => $detail['task_description'],
      'rosterDate' => date('d-m-Y', strtotime($detail['roster_date']))
    ];
    $allDayRosterData[$key] = json_encode($dataEmp);
  }
  $data['allDayRosterData'] = $allDayRosterData;

  // Employees on leave during this roster week
  $data['empLeavesThisWeek'] = [];
  try {
      $leaveStart = '';
      $leaveEnd   = '';
      if (!empty($data['rosterInfo'][0]['start_date'])) {
          $leaveStart = date('Y-m-d', strtotime($data['rosterInfo'][0]['start_date']));
          $leaveEnd   = !empty($data['rosterInfo'][0]['end_date'])
              ? date('Y-m-d', strtotime($data['rosterInfo'][0]['end_date']))
              : date('Y-m-d', strtotime($leaveStart . ' +6 days'));
      } elseif (!empty($data['rosterStartDate'])) {
          $leaveStart = date('Y-m-d', strtotime($data['rosterStartDate']));
          $leaveEnd   = date('Y-m-d', strtotime($leaveStart . ' +6 days'));
      }
      if ($leaveStart && $leaveEnd) {
          $this->load->model('Leave_model');
          $weekLeaves = $this->Leave_model->get_leaves_in_range($leaveStart, $leaveEnd, $this->location_id);
          foreach ($weekLeaves as $wl) {
              if (empty($wl['emp_id'])) { continue; }
              $data['empLeavesThisWeek'][$wl['emp_id']][] = [
                  'start_date'   => $wl['start_date'],
                  'end_date'     => $wl['end_date'],
                  'leave_status' => (int)$wl['leave_status'],
              ];
          }
      }
  } catch (Throwable $e) {
      $data['empLeavesThisWeek'] = [];
  }

      $this->load->view('general/header');
	  $this->load->view('roster/roster',$data);
	  $this->load->view('general/footer');
//   $this->load->view('hr/roster_view', $data);
}




   private function getEmployeeName($emp_id) {
  $employee = $this->common_model->fetchRecordsDynamically('HR_employee', ['first_name', 'last_name'], ['emp_id' => $emp_id]);

  if (isset($employee[0]['first_name'], $employee[0]['last_name'])) {
    return $employee[0]['first_name'] . ' ' . $employee[0]['last_name'];
  }
  return '';
}

    
    // view roster by team member
    function rosterViewByTM($rosterId=''){
        
      $conditionsRoster = array('roster_id' => $rosterId,'is_deleted' => 0);    
      $rosterData = $this->common_model->fetchRecordsDynamically('HR_roster_details','',$conditionsRoster);
     
      $data['rosterId'] = $rosterId;    
     
     
      $conditions = array('location_id' => $this->location_id, 'is_deleted' => '0');

      $data['empLists'] =  $this->employee_model->employeeList('','',true);
      $data['positionLists'] = $this->common_model->fetchRecordsDynamically('HR_emp_position','',$conditions); 
      
      $data['prepAreas'] = $this->common_model->fetchRecordsDynamically('HR_prepArea','',$conditions);
   	  $data['sites'] = $this->common_model->fetchRecordsDynamically('HR_sites','',$conditions);
      $dayName = strtolower(date("l")); // monday, friday etc..
      

      if(isset($rosterData[0][$dayName]) && !empty($rosterData[0][$dayName])){
      $dayDatas = json_decode($rosterData[0][$dayName]);
      $rosterDayWiseData = array();
      
      foreach($dayDatas as $empKey => $dayData){
      $empInfo = explode('_',$empKey);
      $empID =  (isset($empInfo[3]) ? $empInfo[3] : '0');
      $prepID =  (isset($empInfo[2]) ? $empInfo[2] : '0');
      
      $conditions = array('id' => $prepID);    $fieldsToFetch = ['prep_name'];
      $prepName = $this->common_model->fetchRecordsDynamically('HR_prepArea',$fieldsToFetch,$conditions); 
      $decodedRosterValues = json_decode($dayData);
      
      $timeDataDecoded = json_decode($dayData);
      $timeData['workHrs'] = $timeDataDecoded->empShiftStartTime. '- ' .$timeDataDecoded->empShiftEndTime;
      $timeData['breakHrs'] = $timeDataDecoded->empBreakTime ? $timeDataDecoded->empBreakTime :'';
      $timeData['breakDuration'] = $timeDataDecoded->breakDuration ? $timeDataDecoded->breakDuration : '';
      $timeData['prep_name'] = $prepName[0]['prep_name'];
      if(!isset($rosterDayWiseData[$empID][$prepID])){
      $rosterDayWiseData[$empID][$prepID] = $timeData;    
      }
      }
      }
     $data['rosterDayWiseData'] = $rosterDayWiseData;
   
      $this->load->view('general/header');
	  $this->load->view('roster/rosterViewByTM',$data);
	  $this->load->view('general/footer');  
    }
    
    public function rosterViewWTM($rosterId = '') {
    // Validate roster ID
    if (empty($rosterId)) {
        $this->session->set_flashdata('error_message', 'Invalid roster ID.');
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Fetch necessary data
    $conditions = ['location_id' => $this->location_id, 'is_deleted' => '0'];
    $data['empLists'] = $this->employee_model->employeeList('', '', true);
    $data['positionLists'] = $this->common_model->fetchRecordsDynamically('HR_emp_position', '', $conditions);
    $data['prepAreas'] = $this->common_model->fetchRecordsDynamically('HR_prepArea', '', $conditions);

    // Fetch roster metadata
    $rosterConditions = ['roster_id' => $rosterId, 'location_id' => $this->location_id, 'is_deleted' => '0'];
    $rosterInfo = $this->common_model->fetchRecordsDynamically('HR_roster', '', $rosterConditions);
    if (empty($rosterInfo)) {
        $this->session->set_flashdata('error_message', 'Roster not found.');
        redirect($this->session->userdata('previous_url'));
        return;
    }
    $data['rosterInfo'] = $rosterInfo;

    // Fetch roster details
    $rosterDetailConditions = ['roster_id' => $rosterId,'is_deleted' => 0];
    $rosterDetails = $this->common_model->fetchRecordsDynamically('HR_roster_details', '', $rosterDetailConditions);
   
    // log_message('debug', 'Roster Details: ' . json_encode($rosterDetails));

    // Determine the week range
    $startDate = new DateTime($rosterInfo[0]['start_date']);
    $endDate = new DateTime($rosterInfo[0]['end_date']);
    $days = [];
    $currentDate = clone $startDate;
    while ($currentDate <= $endDate) {
        $days[] = [
            'date' => $currentDate->format('Y-m-d'),
            'day' => strtolower($currentDate->format('l')) // e.g., 'monday'
        ];
        $currentDate->modify('+1 day');
    }

    // Organize roster data by employee
    $rosterViewWTM = [];
    if (!empty($rosterDetails)) {
        foreach ($rosterDetails as $detail) {
            $empId = $detail['employee_id'];
            $prepId = $detail['prep_area_id'];
            $rosterDate = new DateTime($detail['roster_date']);
            $dayOfWeek = strtolower($rosterDate->format('l')); // e.g., 'monday'

            // Find employee details
            $empIndex = array_search($empId, array_column($data['empLists'], 'emp_id'));
            if ($empIndex === false) {
                continue; // Skip if employee not found
            }

            // Find prep area details
            $prepIndex = array_search($prepId, array_column($data['prepAreas'], 'id'));
            $prepName = $prepIndex !== false ? $data['prepAreas'][$prepIndex]['prep_name'] : '';

            // Initialize employee entry if not already set
            if (!isset($rosterViewWTM[$empId])) {
                $rosterViewWTM[$empId] = [
                    'emp_name' => $data['empLists'][$empIndex]['name'],
                    'prep_name' => $prepName,
                    'prep_id' => $prepId
                ];
            }

            // Add shift details for the day
            $rosterViewWTM[$empId][$dayOfWeek] = [
                'start_time' => $detail['shift_start_time'] ? date('h:i A', strtotime($detail['shift_start_time'])) : '',
                'end_time' => $detail['shift_end_time'] ? date('h:i A', strtotime($detail['shift_end_time'])) : '',
                'break_time' => $detail['break_start_time'] ? date('h:i A', strtotime($detail['break_start_time'])) : '',
                'break_type' => $detail['break_type'],
                'break_duration' => $detail['break_duration'],
                'task_description' => $detail['task_description']
            ];
        }

        // Ensure all days are initialized for each employee (even if no shift)
        $dayKeys = array_column($days, 'day');
        foreach ($rosterViewWTM as &$empData) {
            foreach ($dayKeys as $day) {
                if (!isset($empData[$day])) {
                    $empData[$day] = [
                        'start_time' => '',
                        'end_time' => '',
                        'break_time' => '',
                        'break_type' => '',
                        'break_duration' => '',
                        'task_description' => ''
                    ];
                }
            }
        }
        unset($empData); // Unset the reference
    }

    // Pass data to the view
    $data['rosterId'] = $rosterId;
    $data['rosterViewWTM'] = $rosterViewWTM;
    $data['days'] = $days; // For displaying the week range in the UI

    $this->load->view('general/header');
    $this->load->view('roster/rosterViewByTM', $data);
    $this->load->view('general/footer');
}
    
    public function recreateRoster() {
    // Get the posted data
    $rosterId = $_POST['roster_id'] ?? '';
    $startDateRaw = $_POST['start_date'] ?? '';
    $endDateRaw = $_POST['end_date'] ?? '';
    
    // Remove commas and convert to Y-m-d format
    $nextMonday = '';
    $nextSunday = '';
    
    if (!empty($startDateRaw)) {
        $startDateRaw = str_replace(',', '', $startDateRaw);
        $startTimestamp = strtotime($startDateRaw);
        $nextMonday = $startTimestamp ? date('Y-m-d', $startTimestamp) : '';
    }
    
    if (!empty($endDateRaw)) {
        $endDateRaw = str_replace(',', '', $endDateRaw);
        $endTimestamp = strtotime($endDateRaw);
        $nextSunday = $endTimestamp ? date('Y-m-d', $endTimestamp) : '';
    }

 

    // Validate input
    if (empty($rosterId) || empty($nextMonday) || empty($nextSunday)) {
        $this->session->set_flashdata('error_message', 'Invalid input. Please provide roster ID, start date, and end date.');
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Create DateTime objects for validation
    $startDate = DateTime::createFromFormat('Y-m-d', $nextMonday);
    $endDate = DateTime::createFromFormat('Y-m-d', $nextSunday);

    // Verify DateTime objects were created successfully
    if (!$startDate || !$endDate) {
        $this->session->set_flashdata('error_message', 'Invalid date format. Please check your dates.');
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Reset time to midnight for accurate comparison
    $startDate->setTime(0, 0, 0);
    $endDate->setTime(0, 0, 0);

    
    // Rule 1: Start must be Monday (N=1)
    if ((int)$startDate->format('N') !== 1) {
        $this->session->set_flashdata(
            'error_message', 
            'Start date must be a Monday. You selected: ' . $startDate->format('l, d M Y')
        );
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Rule 2: End must be Sunday (N=7)
    if ((int)$endDate->format('N') !== 7) {
        $this->session->set_flashdata(
            'error_message', 
            'End date must be a Sunday. You selected: ' . $endDate->format('l, d M Y')
        );
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Rule 3: End date must be exactly 6 days after start date
    $daysDifference = (int)$startDate->diff($endDate)->days;
    
    

    if ($daysDifference !== 6) {
        $this->session->set_flashdata(
            'error_message',
            'Invalid date range. End date must be exactly 6 days after start date (the Sunday of the same week). Days difference: ' . $daysDifference
        );
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Check if a roster or timesheet already exists for the new date range
    $validateCondition = [
        'start_date' => $nextMonday,
        'end_date' => $nextSunday,
        'location_id' => $this->location_id,
        'is_deleted' => 0
    ];
    
    $rosterCheck = $this->common_model->fetchRecordsDynamically('HR_roster', '', $validateCondition);
    
    $validateConditionTS = [
        'date_from' => $nextMonday,
        'date_to' => $nextSunday,
        'location_id' => $this->location_id,
        'is_deleted' => 0
    ];
    
    $timesheetCheck = $this->common_model->fetchRecordsDynamically('HR_timesheet', '', $validateConditionTS);
    
    if (!empty($rosterCheck) || !empty($timesheetCheck)) {
        $this->session->set_flashdata('error_message', 'Roster/Timesheet already exists for the specified week (' . $nextMonday . ' to ' . $nextSunday . ').');
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Fetch the existing roster
    $conditions = [
        'roster_id' => $rosterId,
        'location_id' => $this->location_id,
        'is_deleted' => 0
    ];
    
    $roster = $this->common_model->fetchRecordsDynamically('HR_roster', '', $conditions);
    
    if (empty($roster)) {
        $this->session->set_flashdata('error_message', 'No roster found for the specified criteria.');
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Fetch the roster details for the OLD roster only
    $conditionsRoster = [
        'roster_id' => $rosterId,
        'is_deleted' => 0
    ];
    
    $roster_details = $this->common_model->fetchRecordsDynamically('HR_roster_details', '', $conditionsRoster);
    
    // Additional validation: ensure we ONLY get records from the source roster
    $filteredDetails = [];
    foreach ($roster_details as $detail) {
        // Verify the roster_id matches what we requested
        if ($detail['roster_id'] == $rosterId) {
            $filteredDetails[] = $detail;
        } else {
            log_message('error', 'Found roster detail with wrong roster_id: ' . $detail['roster_id'] . ' (expected: ' . $rosterId . ')');
        }
    }
    $roster_details = $filteredDetails;
    
    if (empty($roster_details)) {
        $this->session->set_flashdata('error_message', 'No roster details found for the specified roster.');
        redirect($this->session->userdata('previous_url'));
        return;
    }
    
    log_message('error', 'Fetched ' . count($roster_details) . ' roster details for roster_id: ' . $rosterId);

    // Create a new roster
    $new_roster_data = [
        'rosterName' => $nextMonday . ' to ' . $nextSunday,
        'start_date' => $nextMonday,
        'end_date' => $nextSunday,
        'status' => $roster[0]['status'] ?? 1,
        'is_published' => $roster[0]['is_published'] ?? 0,
        'location_id' => $roster[0]['location_id'] ?? $this->location_id,
        'is_deleted' => 0
    ];
    
    $newRosterId = $this->common_model->commonRecordCreate('HR_roster', $new_roster_data);
    
    if (!$newRosterId) {
        $this->session->set_flashdata('error_message', 'Failed to create new roster.');
        redirect($this->session->userdata('previous_url'));
        return;
    }

    // Make entry in timesheet table so we can show on listing page
    $timesheetData = [
        'date_from' => $nextMonday,
        'date_to' => $nextSunday,
        'roster_id' => $newRosterId,
        'status' => 1,
        'is_published' => $roster[0]['is_published'] ?? 0,
        'location_id' => $this->location_id,
        'is_timesheet_without_roster' => 0,
    ];
    
    $parentTimesheetId = $this->common_model->commonRecordCreate('HR_timesheet', $timesheetData);

    // Adjust roster details for the new date range
    $originalStartDate = new DateTime($roster[0]['start_date']);
    $newStartDate = new DateTime($nextMonday);
    
    // Critical: Calculate the difference between old and new start dates
    $dateDifference = $originalStartDate->diff($newStartDate);
    $daysToAdd = (int)$dateDifference->days;
    $isNegative = $dateDifference->invert == 1; // Check if we're going backwards

    // Debug logging
    log_message('error', '=== ROSTER RECREATION START ===');
    log_message('error', 'Old Roster ID: ' . $rosterId . ' | New Roster ID: ' . $newRosterId);
    log_message('error', 'Old date range: ' . $roster[0]['start_date'] . ' to ' . $roster[0]['end_date']);
    log_message('error', 'New date range: ' . $nextMonday . ' to ' . $nextSunday);
    log_message('error', 'Days to shift: ' . ($isNegative ? '-' : '+') . $daysToAdd);
    log_message('error', 'Total roster details to copy: ' . count($roster_details));

    // Prepare all records for bulk insert (more efficient and atomic)
    $recordsToInsert = [];
    
    foreach ($roster_details as $detail) {
        // CRITICAL FIX: Create new DateTime from old roster_date and shift by the calculated difference
        $originalRosterDate = new DateTime($detail['roster_date']);
        
        // Clone to avoid reference issues
        $newRosterDate = clone $originalRosterDate;
        
        // Shift the date forward by the number of days between old and new roster start dates
        if ($isNegative) {
            $newRosterDate->sub(new DateInterval('P' . $daysToAdd . 'D'));
        } else {
            $newRosterDate->add(new DateInterval('P' . $daysToAdd . 'D'));
        }
        
        $newDateFormatted = $newRosterDate->format('Y-m-d');
        
        // VALIDATION: Ensure the new date falls within the target week
        if ($newDateFormatted < $nextMonday || $newDateFormatted > $nextSunday) {
            log_message('error', 'SKIPPING - Date out of range! Emp: ' . $detail['employee_id'] . 
                        ', Old Date: ' . $detail['roster_date'] . 
                        ', Calculated New Date: ' . $newDateFormatted . 
                        ' (Expected: ' . $nextMonday . ' to ' . $nextSunday . ')');
            continue; // Skip this entry
        }

        // Prepare the new roster detail with ALL required fields
        $new_detail = [
            'roster_id' => $newRosterId,
            'employee_id' => $detail['employee_id'],
            'position_id' => $detail['position_id'] ?? null,
            'prep_area_id' => $detail['prep_area_id'] ?? null,
            'roster_date' => $newDateFormatted,
            'shift_start_time' => $detail['shift_start_time'] ?? null,
            'shift_end_time' => $detail['shift_end_time'] ?? null,
            'break_start_time' => $detail['break_start_time'] ?? null,
            'break_type' => $detail['break_type'] ?? null,
            'break_duration' => $detail['break_duration'] ?? 0,
            'task_description' => $detail['task_description'] ?? '',
            'is_deleted' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Debug log
        log_message('error', 'Prepared entry - Emp: ' . $detail['employee_id'] . 
                    ' | Old Date: ' . $detail['roster_date'] . 
                    ' → New Date: ' . $newDateFormatted);

        $recordsToInsert[] = $new_detail;
    }
    
    // Insert all records in one batch (atomic operation)
    if (!empty($recordsToInsert)) {
        log_message('error', 'Inserting ' . count($recordsToInsert) . ' roster details in bulk...');
        $this->common_model->commonBulkRecordCreate('HR_roster_details', $recordsToInsert);
        log_message('error', '✓ Successfully inserted ' . count($recordsToInsert) . ' roster details');
    } else {
        log_message('error', '✗ No valid roster details to insert!');
    }
    
    log_message('error', '=== ROSTER RECREATION END ===');

    // Synchronize timesheet from roster
    $this->synchronizeTimesheetFromRoster($newRosterId, $parentTimesheetId);

    // Set success message and redirect
    $this->session->set_flashdata('success_message', 'Roster recreated successfully for the week of ' . $nextMonday . ' to ' . $nextSunday . '.');
    redirect($this->session->userdata('previous_url'));
}
    
    // when recreating , we have to update date for roster as it is in encoded format so wrote seprate method
    function updateRosterDates($roster_details, $start_date) {
   
    $updated_roster = [];
    $updated_DataForTimesheet = [];
   
    $current_date = strtotime($start_date);
   $allDaysname = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    foreach ($roster_details as $key => $value) {
        if(in_array($key,$allDaysname)){
      
        $day_data = json_decode($value, true);
      
        foreach ($day_data as &$employee_data) {
            $employee_data = json_decode($employee_data, true); // Decode nested JSON string to array
            $employee_data['rosterDate'] = date('d-m-Y', $current_date); // Update rosterDate
            $employee_data = json_encode($employee_data); // Encode back to JSON string
        }
       
        $updated_roster[$key] = json_encode($day_data);
        $updated_DataForTimesheet[] = $day_data;
    
        $current_date = strtotime('+1 day', $current_date);
        }
    }
    $result['rosterData'] = $updated_roster;
    $result['dataForTimesheet'] = $updated_DataForTimesheet;
    return $result;
}
    
    function deleteRoster(){
       $data['is_deleted'] = 1; 
	   $this->common_model->commonRecordUpdate('HR_roster','roster_id',$_POST['rosterId'],$data);
	   $this->common_model->commonRecordUpdate('HR_roster_details','roster_id',$_POST['rosterId'],$data);
	   $this->common_model->commonRecordUpdate('HR_timesheet','roster_id',$_POST['rosterId'],$data);
	   $this->common_model->commonRecordUpdate('HR_timesheet_details','roster_id',$_POST['rosterId'],$data);
	   echo "Success"; exit;
    }
    
   
// Add this new method to your Roster controller class

private function calculateWorkedMinutes(array $shift): int
{
    $start = strtotime($shift['shift_start_time']);
    $end   = strtotime($shift['shift_end_time']);

    if (!$start || !$end || $end <= $start) {
        return 0;
    }

    $totalMinutes = ($end - $start) / 60;

    $breakMinutes = !empty($shift['break_duration'])
        ? (int)$shift['break_duration']
        : 0;

    return max(0, $totalMinutes - $breakMinutes);
}


/**
 * Download Roster as PDF
 * Generates a professional PDF of the roster schedule
 */
public function exportRosterPDF()
{
    // Enable debugging only if needed
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Load TCPDF
    require_once(FCPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php');

    $rosterId = $this->input->get('roster_id');

    if (empty($rosterId)) {
        show_error('Invalid roster ID');
    }

    /* ---------- Fetch data ---------- */
    $rosterInfo = $this->common_model->fetchRecordsDynamically(
        'HR_roster', '', ['roster_id' => $rosterId, 'is_deleted' => 0]
    );

    if (empty($rosterInfo)) {
        show_error('Roster not found');
    }

    $rosterDetails = $this->common_model->fetchRecordsDynamically(
        'HR_roster_details', '', ['roster_id' => $rosterId, 'is_deleted' => 0]
    );

    $startDate = new DateTime($rosterInfo[0]['start_date']);
    $endDate   = new DateTime($rosterInfo[0]['end_date']);

    /* ---------- Build days ---------- */
    $days = [];
    $d = clone $startDate;
    while ($d <= $endDate) {
        $days[] = [
            'label' => $d->format('D') . '<br><span style="font-size:10px">' . $d->format('d M') . '</span>',
            'key'   => $d->format('Y-m-d')
        ];
        $d->modify('+1 day');
    }

    /* ---------- Group shifts by employee ---------- */
    $employees = [];

    foreach ($rosterDetails as $shift) {
        $empId = $shift['employee_id'];
        $date  = $shift['roster_date'];

        if (!isset($employees[$empId])) {
            $employees[$empId] = [
                'name' => $this->getEmployeeName($empId),
                'shifts' => [],
                'total_minutes' => 0
            ];
        }

        // Calculate worked minutes (SHIFT − BREAK)
        $workedMinutes = $this->calculateWorkedMinutes($shift);
        $employees[$empId]['total_minutes'] += $workedMinutes;

        $label  = date('h:i A', strtotime($shift['shift_start_time']));
        $label .= ' – ' . date('h:i A', strtotime($shift['shift_end_time']));

        if (!empty($shift['break_duration'])) {
            $label .= '<br><span style="color:#6B7280">Break: '
                   . (int)$shift['break_duration'] . ' min</span>';
        }

        $employees[$empId]['shifts'][$date][] = $label;
    }

    /* ---------- Create PDF ---------- */
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    /* ---------- HTML ---------- */
    $html = '
    <style>
        body { font-family: helvetica, sans-serif; }
        h1 { color:#4F46E5; font-size:26px; text-align:center; margin-bottom:6px; }
        .meta { text-align:center; color:#6B7280; font-size:12px; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; }
        th {
            background:#EEF2FF;
            border:1px solid #CBD5E1;
            padding:10px;
            font-size:12px;
            text-align:center;
        }
        td {
            border:1px solid #E5E7EB;
            padding:12px;
            font-size:11px;
            vertical-align:top;
        }
        .emp-name {
            font-weight:bold;
            font-size:12px;
            color:#111827;
            background:#F9FAFB;
            white-space:nowrap;
        }
        .off {
            color:#9CA3AF;
            font-style:italic;
            text-align:center;
        }
        .total {
            font-weight:bold;
            background:#F3F4F6;
            text-align:center;
        }
    </style>

    <h1>' . htmlspecialchars($rosterInfo[0]['rosterName']) . '</h1>
    <div class="meta">
        Week: ' . $startDate->format('d M Y') . ' – ' . $endDate->format('d M Y') . '<br>
        Generated: ' . date('d M Y H:i') . '
    </div>

    <table>
        <tr>
            <th style="width:14%">Employee</th>';

    foreach ($days as $day) {
        $html .= '<th>' . $day['label'] . '</th>';
    }

    $html .= '<th>Total</th></tr>';

    /* ---------- Rows ---------- */
    foreach ($employees as $emp) {
        $html .= '<tr>';
        $html .= '<td class="emp-name">' . htmlspecialchars($emp['name']) . '</td>';

        foreach ($days as $day) {
            if (!empty($emp['shifts'][$day['key']])) {
                $html .= '<td>' . implode('<hr>', $emp['shifts'][$day['key']]) . '</td>';
            } else {
                $html .= '<td class="off">Off</td>';
            }
        }

        $hours = intdiv($emp['total_minutes'], 60);
        $mins  = $emp['total_minutes'] % 60;

        $html .= '<td class="total">' . $hours . 'h ' . $mins . 'm</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    /* ---------- Render ---------- */
    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = 'Roster_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';
    $pdf->Output($filename, 'D');
}


/**
 * Generate HTML for roster EXCEL days are vertical wise
 */
public function exportRosterExcel()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $rosterId = $this->input->get('roster_id');
    if (!$rosterId) {
        show_error('Invalid roster ID');
    }

    /* ---------- Fetch data ---------- */
    $rosterInfo = $this->common_model->fetchRecordsDynamically(
        'HR_roster', '', ['roster_id' => $rosterId, 'is_deleted' => 0]
    );

    if (empty($rosterInfo)) {
        show_error('Roster not found');
    }

    $rosterDetails = $this->common_model->fetchRecordsDynamically(
        'HR_roster_details', '', ['roster_id' => $rosterId, 'is_deleted' => 0]
    );

    $startDate = new DateTime($rosterInfo[0]['start_date']);
    $endDate   = new DateTime($rosterInfo[0]['end_date']);

    /* ---------- Build days ---------- */
    $days = [];
    $d = clone $startDate;
    while ($d <= $endDate) {
        $days[] = [
            'label' => $d->format('D d M'),
            'key'   => $d->format('Y-m-d')
        ];
        $d->modify('+1 day');
    }

    /* ---------- Group shifts by employee ---------- */
    $employees = [];

    foreach ($rosterDetails as $shift) {
        $empId = $shift['employee_id'];
        $date  = $shift['roster_date'];

        if (!isset($employees[$empId])) {
            $employees[$empId] = [
                'name'          => $this->getEmployeeName($empId),
                'shifts'        => [],
                'total_minutes' => 0
            ];
        }

        // Calculate worked minutes (including break deduction)
        $start = strtotime($shift['shift_start_time']);
        $end   = strtotime($shift['shift_end_time']);
        $minutes = ($end - $start) / 60;

        if (!empty($shift['break_duration'])) {
            $minutes -= (int) $shift['break_duration'];
        }

        $employees[$empId]['total_minutes'] += max(0, $minutes);

        // Shift label (string only)
        $label = date('h:i A', $start) . ' – ' . date('h:i A', $end);

        if (!empty($shift['break_duration'])) {
            $label .= ' (Break: ' . $shift['break_duration'] . ' min)';
        }

        $employees[$empId]['shifts'][$date][] = $label;
    }

    /* ---------- Create Spreadsheet ---------- */
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    /* ---------- Header row ---------- */
    $col = 1;
    $sheet->setCellValueByColumnAndRow($col++, 1, 'Employee');

    foreach ($days as $day) {
        $sheet->setCellValueByColumnAndRow($col++, 1, $day['label']);
    }

    $sheet->setCellValueByColumnAndRow($col, 1, 'Total');

    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => 'center',
            'vertical'   => 'center'
        ],
    ]);

    /* ---------- Data rows ---------- */
    $row = 2;

    foreach ($employees as $emp) {
        $col = 1;
        $sheet->setCellValueByColumnAndRow($col++, $row, $emp['name']);

        foreach ($days as $day) {

            if (!empty($emp['shifts'][$day['key']])) {
                // Convert array → string safely
                $cellValue = implode("\n", (array) $emp['shifts'][$day['key']]);

                $sheet->setCellValueByColumnAndRow($col, $row, $cellValue);
                $sheet->getStyleByColumnAndRow($col, $row)
                      ->getAlignment()
                      ->setWrapText(true);

                $col++;
            } else {
                $sheet->setCellValueByColumnAndRow($col++, $row, 'Off');
            }
        }

        // Total hours
        $hours = intdiv($emp['total_minutes'], 60);
        $mins  = $emp['total_minutes'] % 60;

        $sheet->setCellValueByColumnAndRow($col, $row, "{$hours}h {$mins}m");
        $row++;
    }

    /* ---------- Auto-size columns ---------- */
    foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    /* ---------- Download ---------- */
    $filename = 'Roster_' . $startDate->format('Y-m-d') .
                '_to_' . $endDate->format('Y-m-d') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}



// Export roster end

private function parseDateRange($weekRange) {
    // Parse date range like "08 Jan 2026 - 14 Jan 2026"
    $parts = explode(' - ', $weekRange);
    if (count($parts) !== 2) {
        // Fallback to current week
        $monday = new DateTime('monday this week');
        $sunday = clone $monday;
        $sunday->modify('+6 days');
        return $this->generateWeekDates($monday, $sunday);
    }
    
    $startDate = DateTime::createFromFormat('d M Y', trim($parts[0]));
    $endDate = DateTime::createFromFormat('d M Y', trim($parts[1]));
    
    return $this->generateWeekDates($startDate, $endDate);
}

private function generateWeekDates($startDate, $endDate) {
    $dates = [];
    $current = clone $startDate;
    
    while ($current <= $endDate) {
        $dates[] = [
            'day' => $current->format('l'),
            'date' => $current->format('d/m'),
            'full_date' => $current->format('d-m-Y')
        ];
        $current->modify('+1 day');
    }
    
    return $dates;
}

private function generatePDFHTML($rosterName, $weekRange, $dates, $prepAreas, $shifts) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                margin: 0;
                padding: 15px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header h1 {
                margin: 0 0 5px 0;
                font-size: 20px;
                color: #1f2937;
            }
            .header p {
                margin: 0;
                font-size: 12px;
                color: #6b7280;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            th {
                background-color: #f3f4f6;
                padding: 8px 5px;
                text-align: left;
                border: 1px solid #e5e7eb;
                font-weight: 600;
                color: #374151;
                font-size: 9px;
            }
            th.area-header {
                width: 12%;
            }
            th.day-header {
                width: 12.5%;
                text-align: center;
            }
            td {
                padding: 8px 5px;
                border: 1px solid #e5e7eb;
                vertical-align: top;
            }
            td.area-cell {
                background-color: #f9fafb;
                font-weight: 600;
                color: #1f2937;
            }
            .shift-box {
                background-color: #E6F4EA;
                padding: 6px;
                margin-bottom: 5px;
                border-radius: 4px;
                border: 1px solid #CAEBD0;
                font-size: 8px;
            }
            .shift-name {
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 3px;
            }
            .shift-time {
                color: #4b5563;
                margin-bottom: 2px;
            }
            .shift-break {
                color: #4b5563;
                font-style: italic;
            }
            .no-shifts {
                color: #9ca3af;
                font-style: italic;
                text-align: center;
                font-size: 8px;
            }
            .footer {
                margin-top: 20px;
                padding-top: 10px;
                border-top: 2px solid #e5e7eb;
                text-align: center;
                color: #6b7280;
                font-size: 8px;
            }
            .day-date {
                display: block;
                font-size: 8px;
                color: #6b7280;
                font-weight: normal;
                margin-top: 2px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>' . htmlspecialchars($rosterName) . '</h1>
            <p>' . htmlspecialchars($weekRange) . '</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th class="area-header">Area</th>';
    
    // Add day headers
    foreach ($dates as $date) {
        $html .= '<th class="day-header">' . htmlspecialchars($date['day']) . '<span class="day-date">' . htmlspecialchars($date['date']) . '</span></th>';
    }
    
    $html .= '
                </tr>
            </thead>
            <tbody>';
    
    // Add rows for each preparation area
    foreach ($prepAreas as $area) {
        $html .= '<tr>';
        $html .= '<td class="area-cell">' . htmlspecialchars($area['prep_name']) . '</td>';
        
        // Add cells for each day
        foreach ($dates as $date) {
            $html .= '<td>';
            
            // Find shifts for this area and date
            $dayShifts = $this->getShiftsForAreaAndDate($shifts, $area['id'], $date['full_date']);
            
            if (empty($dayShifts)) {
                $html .= '<div class="no-shifts">No shifts</div>';
            } else {
                foreach ($dayShifts as $shift) {
                    $html .= '<div class="shift-box">';
                    $html .= '<div class="shift-name">' . htmlspecialchars($shift['selectedEmpName']) . '</div>';
                    $html .= '<div class="shift-time">⏰ ' . htmlspecialchars($shift['empShiftStartTime']) . ' - ' . htmlspecialchars($shift['empShiftEndTime']) . '</div>';
                    
                    if (!empty($shift['empBreakTime'])) {
                        $html .= '<div class="shift-break">☕ Break: ' . htmlspecialchars($shift['empBreakTime']) . '</div>';
                    }
                    
                    $html .= '</div>';
                }
            }
            
            $html .= '</td>';
        }
        
        $html .= '</tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="footer">
            <p>Generated on ' . date('d M Y, h:i A') . '</p>
        </div>
    </body>
    </html>';
    
    return $html;
}

private function getShiftsForAreaAndDate($shifts, $areaId, $date) {
    $result = [];
    
    // Extract day number from date (format: dd-mm-yyyy)
    $dayNumber = substr($date, 0, 2);
    
    // Look for shifts matching this area and date
    foreach ($shifts as $key => $value) {
        if (strpos($key, 'emp_') !== 0) continue;
        
        $shiftData = json_decode($value, true);
        if (!$shiftData) continue;
        
        // Parse key: emp_DD_AreaID_EmpID
        $keyParts = explode('_', $key);
        if (count($keyParts) < 3) continue;
        
        $shiftDay = $keyParts[1];
        $shiftAreaId = $keyParts[2];
        
        // Check if this shift matches our area and date
        if ($shiftAreaId == $areaId && $shiftDay == $dayNumber) {
            $result[] = $shiftData;
        }
    }
    
    return $result;
}

private function sanitizeFilename($filename) {
    return preg_replace('/[^a-z0-9_\-]/i', '_', $filename);
}
    
}
    
    ?>