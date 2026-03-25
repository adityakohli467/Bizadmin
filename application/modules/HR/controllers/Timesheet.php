<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 use Aws\Rekognition\RekognitionClient;
use Aws\S3\S3Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class Timesheet extends MY_Controller {

    function __construct() {
        parent::__construct();
        !$this->ion_auth->logged_in() ? redirect('auth/login', 'refresh') : '';
        // $this->load->helper('notification');
		$this->load->model('timesheet_model');
	    $this->load->model('common_model');
	    $this->load->model('employee_model');
        $this->location_id = $this->session->userdata('location_id') ? $this->session->userdata('location_id') : ($this->session->userdata('User_location_ids') ? $this->session->userdata('User_location_ids')[0] : null);
        $this->tenantIdentifier = $this->session->userdata('tenantIdentifier');
        $this->roleId = get_logged_in_user_role($this->ion_auth,'id');
        $this->roleName =  get_logged_in_user_role($this->ion_auth,'name');
    }
    
    public function index(){
        
        
    }
    
    public function searchEmployees() {
    $query = $this->input->post('query');
    $location_id = $this->location_id;

    $result = $this->timesheet_model->searchEmployees($query, $this->location_id);

    echo json_encode($result);
}

   

    public function timesheetList() {
        $conditions = ['location_id' => $this->location_id, 'is_deleted' => 0, 'status' => 1];
         $conditionsGeneralConfigTimesheetWORoster = array('location' => $this->location_id, 'configureFor' => 'timesheetWORoster_toggle'); 
        $toggleConfigTWOR = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], $conditionsGeneralConfigTimesheetWORoster);
        if(isset($toggleConfigTWOR[0]['data']) && $toggleConfigTWOR[0]['data'] !='') {
            $generalConfigDataTWOR = json_decode($toggleConfigTWOR[0]['data'], true);
            $data['generalConfigData']['timesheetWORoster_toggle'] = isset($generalConfigDataTWOR['value']) ? $generalConfigDataTWOR['value'] : '0';
        }
        
        
        $data['timesheets'] = $this->common_model->fetchRecordsDynamically('HR_timesheet', '', $conditions, 'id DESC');
        // echo "<pre>"; print_r($data['timesheets']); exit;
        
        $this->load->view('general/header');
        $this->load->view('timesheet/timesheetList', $data);
        $this->load->view('general/footer');
    }
    
   // timesheet with roster
    public function timesheetView($timesheetId) {
        // Fetch timesheet details
        $conditions = ['timesheet_id' => $timesheetId, 'is_deleted' => 0];
        $fieldsToFetch = [
            'employee_id', 'roster_date', 'prep_area_id', 'position_id',
            'clock_in_time', 'clock_out_time', 'actual_break_duration',
            'approval_status', 'task_description',
            'CONCAT(e.first_name, " ", e.last_name) as name',
            'p.prep_name', 'pos.position_name'
        ];
        $timesheetDetails = $this->common_model->fetchRecordsDynamically(
            'HR_timesheet_details',
            $fieldsToFetch,
            $conditions,
            ['JOIN HR_employee e ON HR_timesheet_details.employee_id = e.id'],
            ['JOIN 	HR_prepArea p ON HR_timesheet_details.prep_area_id = p.id'],
            ['LEFT JOIN HR_emp_position pos ON HR_timesheet_details.position_id = pos.id']
        );

        // Group data by employee and date
        $groupedData = [];
        foreach ($timesheetDetails as $record) {
            $empId = $record['employee_id'];
            $positionId = $record['position_id'] ?? 'none';
            $date = $record['roster_date'];

            if (!isset($groupedData[$empId])) {
                $groupedData[$empId] = [
                    'name' => $record['name'],
                    'positions' => []
                ];
            }

            if (!isset($groupedData[$empId]['positions'][$positionId])) {
                $groupedData[$empId]['positions'][$positionId] = [
                    'position_name' => $record['position_name'] ?? 'None',
                    'prep_name' => $record['prep_name'],
                    'dates' => []
                ];
            }

            $groupedData[$empId]['positions'][$positionId]['dates'][$date] = [
                'clock_in_time' => $record['clock_in_time'],
                'clock_out_time' => $record['clock_out_time'],
                'actual_break_duration' => $record['actual_break_duration'],
                'approval_status' => $record['approval_status'],
                'task_description' => $record['task_description']
            ];
        }

        $data['employee_weekly_timesheet_details'] = $groupedData;
        $this->load->view('general/header');
        $this->load->view('timesheet/edit_timesheetWithoutRoster', $data);
        $this->load->view('general/footer');
    }

    // Placeholder for createDateFormat
    private function createDateFormat($dateRange) {
        // Replace with your actual implementation
        $dates = explode(' - ', $dateRange);
        return [
            'start_date' => date('Y-m-d', strtotime($dates[0])),
            'end_date' => date('Y-m-d', strtotime($dates[1]))
        ];
    }
    
   function viewWeeklyTimesheet($start_date = null, $end_date = null) {
    $start_date = $start_date ?? date('Y-m-d', strtotime('monday this week'));
    $end_date = $end_date ?? date('Y-m-d', strtotime('sunday this week'));
    
    $conditions = ['location_id' => $this->location_id];
    $data['prepAreaLists'] = $this->common_model->fetchRecordsDynamically('HR_prepArea', '', $conditions);
    $user = $this->ion_auth->user()->row();
    $data['can_approve_timesheet'] = $this->ion_auth->is_admin() || !empty($user->allow_timesheetapproval);

    
    $data['timesheets'] = $this->timesheet_model->get_timesheets_by_date_range($start_date, $end_date, $this->location_id);
    
    // Fetch ALL roster shifts for employees in this date range (for multi-shift display)
    $data['allRosterShifts'] = $this->getRosterShiftsForDateRange($start_date, $end_date);
    
    $data['start_date'] = $start_date;
    $data['end_date'] = $end_date;
    
    $this->load->view('general/header');
    $this->load->view('timesheet/weeklyTimesheet', $data);
    $this->load->view('general/footer');
}

/**
 * Get all roster shifts grouped by employee_id and date
 * Returns array: [employee_id][date] => [array of shifts]
 */
private function getRosterShiftsForDateRange($start_date, $end_date) {
    $this->tenantDb->select([
        'rd.employee_id',
        'rd.roster_date',
        'rd.shift_start_time',
        'rd.shift_end_time',
        'p.prep_name'
    ])
    ->from('HR_roster_details rd')
    ->join('HR_roster r', 'rd.roster_id = r.roster_id', 'inner')
    ->join('HR_prepArea p', 'rd.prep_area_id = p.id', 'left')
    ->where('rd.roster_date >=', $start_date)
    ->where('rd.roster_date <=', $end_date)
    ->where('rd.is_deleted', 0)
    ->where('r.location_id', $this->location_id)
    ->where('r.is_deleted', 0)
    ->order_by('rd.roster_date', 'ASC')
    ->order_by('rd.shift_start_time', 'ASC');
    
    $results = $this->tenantDb->get()->result_array();
    
    // Group by employee_id and date
    $grouped = [];
    foreach ($results as $row) {
        $empId = $row['employee_id'];
        $date = $row['roster_date'];
        if (!isset($grouped[$empId])) {
            $grouped[$empId] = [];
        }
        if (!isset($grouped[$empId][$date])) {
            $grouped[$empId][$date] = [];
        }
        $grouped[$empId][$date][] = [
            'start' => $row['shift_start_time'],
            'end' => $row['shift_end_time'],
            'prep_name' => $row['prep_name'] ?? ''
        ];
    }
    
    return $grouped;
}

// Export timesheet to excel file 

public function exportTimesheetExcel($start_date, $end_date)
{
    

    ini_set('memory_limit', '512M');

    // Fetch timesheets
    $timesheets = $this->timesheet_model->get_timesheets_by_date_range($start_date, $end_date, $this->location_id,true);
        

    if (empty($timesheets)) {
        show_error('No timesheet data found');
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Timesheets');

    /* ================= HEADER ================= */
    $headers = [
        'Employee Name',
        'Date',
        'Start Time',
        'Finish Time',
        'Break (mins)',
        'Total Hours'
    ];

    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    $sheet->getStyle('A1:F1')->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => 'center'],
    ]);

    /* ================= DATA ================= */
    $row = 2;

    foreach ($timesheets as $ts) {

        $clockIn  = $ts['clock_in_time'];
        $clockOut = $ts['clock_out_time'];

        if (empty($clockIn) || empty($clockOut)) {
            continue; // skip invalid entries
        }

        // Use helper function to calculate worked seconds (handles overnight shifts)
        $workedSeconds = $this->timesheet_model->calculateWorkedSeconds($clockIn, $clockOut, $ts['roster_date']);
        $totalHoursWorked = $workedSeconds / 3600;

        // Break in minutes → seconds
        $breakMinutes = (int) ($ts['total_break_duration'] ?? 0);
        
        // Check for manual break override first
        $manualOverride = isset($ts['manual_break_override']) && $ts['manual_break_override'] == 1;
        $manualBreakMinutes = isset($ts['manual_break_minutes']) ? (int)$ts['manual_break_minutes'] : null;
        
        if ($manualOverride && $manualBreakMinutes !== null) {
            // Use manual break override
            $breakMinutes = $manualBreakMinutes;
        } elseif ($breakMinutes == 0) {
            // Apply automatic break logic if no break recorded and no manual override
            if ($totalHoursWorked > 10) {
                $breakMinutes = 60; // 60 mins for 10+ hours
            } elseif ($totalHoursWorked > 5) {
                $breakMinutes = 30; // 30 mins for 5-10 hours
            }
        }
        
        $breakHours   = round($breakMinutes / 60, 2);

        $workedSeconds -= ($breakMinutes * 60);

        if ($workedSeconds < 0) {
            $workedSeconds = 0;
        }

        // Convert to decimal hours (IMPORTANT FIX)
        $decimalHours = round($workedSeconds / 3600, 2);

        $sheet->setCellValue('A' . $row, $ts['employee_name']);
        $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($ts['roster_date'])));
        $sheet->setCellValue('C' . $row, date('h:i A', strtotime($clockIn)));
        $sheet->setCellValue('D' . $row, date('h:i A', strtotime($clockOut)));
        $sheet->setCellValue('E' . $row, $breakHours);
        $sheet->setCellValue('F' . $row, $decimalHours);

        $row++;
    }

    /* ================= FORMAT ================= */
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Force decimal format
    $sheet->getStyle('F2:F' . $row)
        ->getNumberFormat()
        ->setFormatCode('0.00');

    /* ================= DOWNLOAD ================= */
    $filename = "Timesheet_{$start_date}_to_{$end_date}.xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Export timesheet in TX format (TXT file with CSV format)
public function exportTimesheetTX($start_date, $end_date)
{
    ini_set('memory_limit', '512M');
    
    // Get accounting software setting
    $superConfig = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], ['location' => $this->location_id, 'configureFor' => 'superannuation']);
    $accounting_software = 'myob'; // default
    if(isset($superConfig[0]['data']) && $superConfig[0]['data'] !='') {
        $superConfigData = json_decode($superConfig[0]['data'], true);
        $accounting_software = isset($superConfigData['accounting_software']) ? $superConfigData['accounting_software'] : 'myob';
    }
    
    // Get all dates in the range
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1D');
    $dateRange = new DatePeriod($start, $interval, $end->modify('+1 day'));
    
    $allDates = [];
    foreach ($dateRange as $date) {
        $allDates[] = $date->format('Y-m-d');
    }
    
    // Check if tier-based payroll is enabled
    $tierBasedEnabled = isset($superConfigData['enable_tier_payroll']) && $superConfigData['enable_tier_payroll'] == '1';
    
    // Fetch all employees who have timesheets in this range (both approved and non-approved)
    $this->tenantDb->distinct()
        ->select('e.emp_id, e.first_name, e.last_name, e.employee_type, pos.position_name')
        ->from('HR_timesheet_details td')
        ->join('HR_employee e', 'td.employee_id = e.emp_id', 'inner')
        ->join('HR_emp_position pos', 'td.position_id = pos.position_id', 'left')
        ->where('td.roster_date >=', $start_date)
        ->where('td.roster_date <=', $end_date)
        ->where('td.location_id', $this->location_id)
        ->where('td.is_deleted', 0);
    
    // Only export Tier 1 employees when tier-based payroll is enabled
    if ($tierBasedEnabled) {
        $this->tenantDb->where('e.tier', '1');
    }
    
    $this->tenantDb->order_by('e.first_name', 'ASC')
        ->order_by('e.last_name', 'ASC');
    
    $employees = $this->tenantDb->get()->result_array();
    
    if (empty($employees)) {
        show_error('No timesheet data found');
        return;
    }
    
    // Check accounting software format
    if ($accounting_software === 'reckon') {
        // RECKON FORMAT: employee name, date, total hours in decimals (hours worked - total break)
        // Fetch all timesheets (approved only)
        $timesheets = $this->timesheet_model->get_timesheets_by_date_range($start_date, $end_date, $this->location_id, true);
        
        // Get public holidays from config for Reckon rate determination
        $publicHolidays = [];
        if (isset($superConfigData['public_holidays']) && !empty($superConfigData['public_holidays'])) {
            $holidayDates = explode(',', $superConfigData['public_holidays']);
            $publicHolidays = array_map('trim', $holidayDates);
        }
        
        // Organize timesheets by employee and date
        $timesheetsByEmpDate = [];
        foreach ($timesheets as $ts) {
            if (strtolower($ts['approval_status']) === 'approved') {
                $empId = $ts['employee_id'];
                $date = $ts['roster_date'];
                $timesheetsByEmpDate[$empId][$date] = $ts;
            }
        }
        
        $output_rows = [];
        
        // Track processed employees to avoid duplicates
        $processedEmployees = [];
        
        // Process each employee for all dates (grouped by employee)
        foreach ($employees as $employee) {
            $empId = $employee['emp_id'];
          
                
            // Skip if this employee was already processed (avoid duplicates from position join)
            if (isset($processedEmployees[$empId])) {
                continue;
            }
            $processedEmployees[$empId] = true;
            
            $employeeName = trim($employee['first_name'] . ' ' . $employee['last_name']);
            
            // Process each date for this employee
            foreach ($allDates as $dateStr) {
                // Only add row if employee worked this day
                if (isset($timesheetsByEmpDate[$empId][$dateStr])) {
                    $ts = $timesheetsByEmpDate[$empId][$dateStr];
                    
                    $clockIn = $ts['clock_in_time'];
                    $clockOut = $ts['clock_out_time'];
                    
                    if (!empty($clockIn) && !empty($clockOut)) {
                        // Use helper function to calculate worked seconds (handles overnight shifts)
                        $workedSeconds = $this->timesheet_model->calculateWorkedSeconds($clockIn, $clockOut, $dateStr);
                        $totalHoursWorked = $workedSeconds / 3600;
                        
                        // Get break duration using rounded break start/end times
                        // This ensures rounding even for older records that predate real-time rounding
                        $breakMinutes = $this->getRoundedBreakMinutes($ts['timesheet_id'], $ts['employee_id']);
                        
                        // Check for manual break override first
                        $manualOverride = isset($ts['manual_break_override']) && $ts['manual_break_override'] == 1;
                        $manualBreakMinutes = isset($ts['manual_break_minutes']) ? (int)$ts['manual_break_minutes'] : null;
                        
                        if ($manualOverride && $manualBreakMinutes !== null) {
                            // Use manual break override
                            $breakMinutes = $manualBreakMinutes;
                        } elseif ($breakMinutes == 0) {
                            // Apply automatic break logic if no break recorded and no manual override
                            if ($totalHoursWorked > 10) {
                                $breakMinutes = 60; // 60 mins for 10+ hours
                            } elseif ($totalHoursWorked > 5) {
                                $breakMinutes = 30; // 30 mins for 5-10 hours
                            }
                        }
                        
                        $breakSeconds = $breakMinutes * 60;
                        $netSeconds = max(0, $workedSeconds - $breakSeconds);
                        $decimalHours = round($netSeconds / 3600, 2);
                        
                        $formattedDate = date('m/d/y', strtotime($dateStr));
                        
                        // Determine correct service item and payroll item based on day type
                        // Check if it's a public holiday first
                        $isPublicHoliday = in_array($dateStr, $publicHolidays);
                        $dayOfWeek = date('N', strtotime($dateStr)); // 1=Mon, 7=Sun
                        
                        if ($isPublicHoliday) {
                            $serviceItem = 'Pub Hol';
                            $payrollItem = 'Pub Hol';
                        } elseif ($dayOfWeek == 6) {
                            // Saturday
                            $serviceItem = 'Sat Rate';
                            $payrollItem = 'Sat Rate';
                        } elseif ($dayOfWeek == 7) {
                            // Sunday
                            $serviceItem = 'Sun Rate';
                            $payrollItem = 'Sun Rate';
                        } else {
                            // Monday to Friday
                            $serviceItem = 'M-F Rate';
                            $payrollItem = 'M-F Rate';
                        }
                        
                        $output_rows[] = [
                            $employeeName,
                            $formattedDate,
                            $serviceItem,
                            $payrollItem,
                            number_format($decimalHours, 2, '.', '')
                        ];
                    }
                }
            }
            
           
        }
        
        // Generate IIF file with Reckon format
        // Generate REAL IIF file for Reckon
$filename = "Reckon_timesheet_{$start_date}_to_{$end_date}.iif";

header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

// TAB delimiter for IIF
$tab = "\t";

// IIF HEADER (VERY IMPORTANT)
fwrite($output, "!TIMEACT\tDATE\tEMP\tITEM\tPITEM\tDURATION\n");

// DATA ROWS
foreach ($output_rows as $row) {

    $employeeName = $row[0];
    $date         = $row[1];
    $serviceItem  = $row[2];
    $payrollItem  = $row[3];
    $hours        = $row[4];

    fwrite(
        $output,
        "TIMEACT{$tab}{$date}{$tab}{$employeeName}{$tab}{$serviceItem}{$tab}{$payrollItem}{$tab}{$hours}\n"
    );
}

fclose($output);
exit;

    }
    
    // MYOB FORMAT (Original format)
    // Fetch all timesheets (approved only)
    $timesheets = $this->timesheet_model->get_timesheets_by_date_range($start_date, $end_date, $this->location_id, true);
    
    // Organize timesheets by employee and date
    $timesheetsByEmpDate = [];
    foreach ($timesheets as $ts) {
        if (strtolower($ts['approval_status']) === 'approved') {
            $empId = $ts['employee_id'];
            $date = $ts['roster_date'];
            $timesheetsByEmpDate[$empId][$date] = $ts;
        }
    }
    
    // Process each employee for all dates (grouped by employee)
    $output_rows = [];
    
    foreach ($employees as $employee) {
        $empId = $employee['emp_id'];
        $firstName = $employee['first_name'];
        $lastName = $employee['last_name'];
        $position = $employee['position_name'] ?? '';
        
        // Determine weekend category based on employee position
        $weekendCategory = 'Intro Weekend';
        if (stripos($position, '16') !== false) {
            $weekendCategory = '16 Yrs Intro Weekend';
        } elseif (stripos($position, '19') !== false) {
            $weekendCategory = '19 Yrs Intro Weekend';
        } elseif (stripos($position, 'Level 2') !== false || stripos($position, 'level 2') !== false) {
            $weekendCategory = 'Level 2 Weekends';
        }
        
        // Process each date for this employee
        foreach ($allDates as $dateStr) {
            $formattedDate = date('d/m/Y', strtotime($dateStr));
            $dayOfWeek = date('N', strtotime($dateStr)); // 1=Mon, 7=Sun
            $isWeekend = ($dayOfWeek >= 6);
            
            // Check if employee worked this day
            if (isset($timesheetsByEmpDate[$empId][$dateStr])) {
                $ts = $timesheetsByEmpDate[$empId][$dateStr];
                
                // Calculate worked hours using helper function (handles overnight shifts)
                $clockIn = $ts['clock_in_time'];
                $clockOut = $ts['clock_out_time'];
                
                if (!empty($clockIn) && !empty($clockOut)) {
                    $workedSeconds = $this->timesheet_model->calculateWorkedSeconds($clockIn, $clockOut, $dateStr);
                    $totalHoursWorked = $workedSeconds / 3600;
                    
                    // Get break duration - check if it's TIME format (HH:MM:SS) or numeric minutes
                    $breakMinutes = 0;
                    if (!empty($ts['total_break_duration'])) {
                        if (strpos($ts['total_break_duration'], ':') !== false) {
                            // TIME format HH:MM:SS
                            $breakParts = explode(':', $ts['total_break_duration']);
                            $breakMinutes = ((int)$breakParts[0] * 60) + (int)$breakParts[1];
                        } else {
                            // Numeric minutes
                            $breakMinutes = (int)$ts['total_break_duration'];
                        }
                    }
                    
                    // Check for manual break override first
                    $manualOverride = isset($ts['manual_break_override']) && $ts['manual_break_override'] == 1;
                    $manualBreakMinutes = isset($ts['manual_break_minutes']) ? (int)$ts['manual_break_minutes'] : null;
                    
                    if ($manualOverride && $manualBreakMinutes !== null) {
                        // Use manual break override
                        $breakMinutes = $manualBreakMinutes;
                    } elseif ($breakMinutes == 0) {
                        // Apply automatic break logic if no break recorded and no manual override
                        if ($totalHoursWorked > 10) {
                            $breakMinutes = 60; // 60 mins for 10+ hours
                        } elseif ($totalHoursWorked > 5) {
                            $breakMinutes = 30; // 30 mins for 5-10 hours
                        }
                    }
                    
                    $breakSeconds = $breakMinutes * 60;
                    $netSeconds = max(0, $workedSeconds - $breakSeconds);
                    $decimalHours = $netSeconds / 3600;
                    
                    // Check for early start (before 7 AM)
                    $clockInHour = (int)date('H', strtotime($clockIn));
                    $earlyStartHours = 0;
                    $regularHours = $decimalHours;
                    
                    // Calculate clock in/out timestamps for early start calculations
                    $clockInTimestamp = strtotime($dateStr . ' ' . $clockIn);
                    $clockOutTimestamp = strtotime($dateStr . ' ' . $clockOut);
                    // Handle overnight - if clockOut is before clockIn, add a day
                    if ($clockOutTimestamp <= $clockInTimestamp) {
                        $clockOutTimestamp += 86400;
                    }
                    
                    if ($clockInHour < 7 && !$isWeekend) {
                        $sevenAM = strtotime(date('Y-m-d 07:00:00', $clockInTimestamp));
                        if ($clockOutTimestamp > $sevenAM) {
                            $earlyStartSeconds = $sevenAM - $clockInTimestamp;
                            $earlyStartHours = $earlyStartSeconds / 3600;
                            $regularHours = ($netSeconds - $earlyStartSeconds) / 3600;
                        } else {
                            $earlyStartHours = $decimalHours;
                            $regularHours = 0;
                        }
                    }
                    
                    // Add early start row if applicable
                    if ($earlyStartHours > 0) {
                        $output_rows[] = [
                            $formattedDate,
                            $firstName,
                            $lastName,
                            'Early Start',
                            number_format($earlyStartHours, 2, '.', '')
                        ];
                    }
                    
                    // Add base/weekend hours row (only if regularHours > 0 or no early start)
                    if ($regularHours > 0 || $earlyStartHours == 0) {
                        $category = $isWeekend ? $weekendCategory : 'Base Hourly';
                        $output_rows[] = [
                            $formattedDate,
                            $firstName,
                            $lastName,
                            $category,
                            number_format($regularHours, 2, '.', '')
                        ];
                    }
                    
                    // Add uniform allowance for worked days
                    $output_rows[] = [
                        $formattedDate,
                        $firstName,
                        $lastName,
                        'Uniform Allowance',
                        '1.00'
                    ];
                }
            }
        }
    }
    
    // Generate TXT file with CSV format
    $filename = "MYOB_timesheet_{$start_date}_to_{$end_date}.txt";
    
    header('Content-Type: text/plain; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');
    
    $output = fopen('php://output', 'w');
    
    // Write header
    fputcsv($output, ['Date', 'First Name', 'Last Name', 'Payroll Category', 'Units']);
    
    // Write data rows
    foreach ($output_rows as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

/**
 * Export timesheet TX for multiple selected weeks (bulk download).
 * Accepts POST data with arrays of date_from[] and date_to[].
 * Finds the overall min start and max end date, then delegates
 * to the existing exportTimesheetTX() method.
 */
public function exportTimesheetTXBulk()
{
    $dateFromArr = $this->input->post('date_from');
    $dateToArr   = $this->input->post('date_to');

    if (empty($dateFromArr) || empty($dateToArr) || count($dateFromArr) !== count($dateToArr)) {
        show_error('No timesheets selected for export');
        return;
    }

    // Find the overall start and end dates from all selected ranges
    $overallStart = min($dateFromArr);
    $overallEnd   = max($dateToArr);

    // Reuse existing export method with the combined date range
    $this->exportTimesheetTX($overallStart, $overallEnd);
}

/**
 * Download Weekly Timesheet Report
 * Shows employee hours breakdown: weekday/weekend hours with costs
 * and daily clock-in/out vs roster times
 * 
 * @param string $start_date Format: YYYY-MM-DD
 * @param string $end_date Format: YYYY-MM-DD
 */
public function downloadWeeklyReport($start_date, $end_date)
{
    ini_set('memory_limit', '512M');
    
    // Fetch timesheet data with hourly rates
    $timesheets = $this->timesheet_model->getWeeklyReportData($start_date, $end_date, $this->location_id);
    
    if (empty($timesheets)) {
        show_error('No timesheet data found for this period');
    }
    
    // Fetch roster details for the date range
    $rosterData = $this->timesheet_model->getRosterDetailsForReport($start_date, $end_date, $this->location_id);
    
    // Generate all dates for the week (Monday to Sunday)
    $weekDates = [];
    $currentDate = new DateTime($start_date);
    $endDateObj = new DateTime($end_date);
    while ($currentDate <= $endDateObj) {
        $weekDates[] = $currentDate->format('Y-m-d');
        $currentDate->modify('+1 day');
    }
    
    // Group timesheets by employee
    $employeeData = [];
    foreach ($timesheets as $ts) {
        $empId = $ts['employee_id'];
        $empName = $ts['employee_name'];
        
        if (!isset($employeeData[$empId])) {
            $employeeData[$empId] = [
                'name' => $empName,
                'weekday_hours' => 0,
                'weekday_cost' => 0,
                'weekend_hours' => 0,
                'weekend_cost' => 0,
                'daily' => []
            ];
        }
        
        $date = $ts['roster_date'];
        $dayOfWeek = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
        
        // Calculate hours worked using helper function (handles overnight shifts)
        $clockIn = $ts['clock_in_time'];
        $clockOut = $ts['clock_out_time'];
        
        if (empty($clockIn) || empty($clockOut)) {
            // Store entry even if no clock times (for roster display)
            if (!isset($employeeData[$empId]['daily'][$date])) {
                $employeeData[$empId]['daily'][$date] = [
                    'clock_in' => null,
                    'clock_out' => null,
                    'break_minutes' => 0,
                    'roster_start' => $ts['roster_start_time'] ? date('H:i', strtotime($ts['roster_start_time'])) : null,
                    'roster_end' => $ts['roster_end_time'] ? date('H:i', strtotime($ts['roster_end_time'])) : null,
                    'hours' => 0
                ];
            }
            continue;
        }
        
        $workedSeconds = $this->timesheet_model->calculateWorkedSeconds($clockIn, $clockOut, $date);
        $totalHoursWorked = $workedSeconds / 3600;
        
        // Handle break deduction
        $breakMinutes = (int)($ts['total_break_duration'] ?? 0);
        $manualOverride = isset($ts['manual_break_override']) && $ts['manual_break_override'] == 1;
        $manualBreakMinutes = isset($ts['manual_break_minutes']) ? (int)$ts['manual_break_minutes'] : null;
        
        if ($manualOverride && $manualBreakMinutes !== null) {
            $breakMinutes = $manualBreakMinutes;
        } elseif ($breakMinutes == 0) {
            // Apply automatic break logic
            if ($totalHoursWorked > 10) {
                $breakMinutes = 60;
            } elseif ($totalHoursWorked > 5) {
                $breakMinutes = 30;
            }
        }
        
        $workedSeconds -= ($breakMinutes * 60);
        if ($workedSeconds < 0) $workedSeconds = 0;
        
        $hoursWorked = round($workedSeconds / 3600, 2);
        
        // Determine rate based on day of week
        $weekdayRate = floatval($ts['weekday_rate'] ?? 0);
        $satRate = floatval($ts['Saturday_rate'] ?? $weekdayRate);
        $sunRate = floatval($ts['Sunday_rate'] ?? $weekdayRate);
        
        // Calculate cost
        if ($dayOfWeek == 6) { // Saturday
            $rate = $satRate;
            $employeeData[$empId]['weekend_hours'] += $hoursWorked;
            $employeeData[$empId]['weekend_cost'] += ($hoursWorked * $rate);
        } elseif ($dayOfWeek == 7) { // Sunday
            $rate = $sunRate;
            $employeeData[$empId]['weekend_hours'] += $hoursWorked;
            $employeeData[$empId]['weekend_cost'] += ($hoursWorked * $rate);
        } else { // Weekday
            $rate = $weekdayRate;
            $employeeData[$empId]['weekday_hours'] += $hoursWorked;
            $employeeData[$empId]['weekday_cost'] += ($hoursWorked * $rate);
        }
        
        // Store daily data
        $employeeData[$empId]['daily'][$date] = [
            'clock_in' => date('H:i', strtotime($clockIn)),
            'clock_out' => date('H:i', strtotime($clockOut)),
            'break_minutes' => $breakMinutes,
            'roster_start' => $ts['roster_start_time'] ? date('H:i', strtotime($ts['roster_start_time'])) : null,
            'roster_end' => $ts['roster_end_time'] ? date('H:i', strtotime($ts['roster_end_time'])) : null,
            'hours' => $hoursWorked
        ];
    }
    
    // Add roster data for days without timesheet entries
    foreach ($employeeData as $empId => &$empData) {
        foreach ($weekDates as $date) {
            if (!isset($empData['daily'][$date])) {
                // Check if there's roster data for this day
                $rosterStart = null;
                $rosterEnd = null;
                if (isset($rosterData[$empId][$date]) && !empty($rosterData[$empId][$date])) {
                    $rosterEntry = $rosterData[$empId][$date][0]; // Get first roster entry
                    $rosterStart = $rosterEntry['shift_start_time'] ? date('H:i', strtotime($rosterEntry['shift_start_time'])) : null;
                    $rosterEnd = $rosterEntry['shift_end_time'] ? date('H:i', strtotime($rosterEntry['shift_end_time'])) : null;
                }
                
                $empData['daily'][$date] = [
                    'clock_in' => null,
                    'clock_out' => null,
                    'break_minutes' => 0,
                    'roster_start' => $rosterStart,
                    'roster_end' => $rosterEnd,
                    'hours' => 0
                ];
            }
        }
        // Sort daily entries by date
        ksort($empData['daily']);
    }
    
    // Create Excel Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Weekly Timesheet Report');
    
    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(25);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    
    $row = 1;
    
    foreach ($employeeData as $empId => $data) {
        // Employee Name Header (spanning 3 columns)
        $sheet->setCellValue('A' . $row, $data['name']);
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F3A61']
            ],
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center']
        ]);
        $row++;
        
        // Hours Summary Section
        // Headers for hours summary
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Total Hours');
        $sheet->setCellValue('C' . $row, 'Cost ($)');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E5E5']
            ],
            'alignment' => ['horizontal' => 'center']
        ]);
        $row++;
        
        // Weekday Hours Row
        $sheet->setCellValue('A' . $row, 'Weekday Hours');
        $sheet->setCellValue('B' . $row, round($data['weekday_hours'], 2));
        $sheet->setCellValue('C' . $row, '$' . number_format($data['weekday_cost'], 2));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        // Weekend Hours Row
        $sheet->setCellValue('A' . $row, 'Weekend Hours (Sat/Sun)');
        $sheet->setCellValue('B' . $row, round($data['weekend_hours'], 2));
        $sheet->setCellValue('C' . $row, '$' . number_format($data['weekend_cost'], 2));
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        // Blank row
        $row++;
        
        // Daily Breakdown Header
        $sheet->setCellValue('A' . $row, 'Day');
        $sheet->setCellValue('B' . $row, 'Timesheet (Clock In-Out)');
        $sheet->setCellValue('C' . $row, 'Roster (Start-End)');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D0D0D0']
            ],
            'alignment' => ['horizontal' => 'center'],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);
        $row++;
        
        // Daily entries (Mon-Sun)
        foreach ($data['daily'] as $date => $dayData) {
            $dayName = date('l', strtotime($date));
            $dateFormatted = date('d/m', strtotime($date));
            
            $sheet->setCellValue('A' . $row, $dayName . ' (' . $dateFormatted . ')');
            
            // Timesheet times with break duration
            if ($dayData['clock_in'] && $dayData['clock_out']) {
                $timesheetValue = $dayData['clock_in'] . ' - ' . $dayData['clock_out'];
                // Add break duration in brackets if > 0
                if (isset($dayData['break_minutes']) && $dayData['break_minutes'] > 0) {
                    $timesheetValue .= ' (' . $dayData['break_minutes'] . 'm break)';
                }
            } else {
                $timesheetValue = '-';
            }
            $sheet->setCellValue('B' . $row, $timesheetValue);
            
            // Roster times
            if ($dayData['roster_start'] && $dayData['roster_end']) {
                $rosterValue = $dayData['roster_start'] . ' - ' . $dayData['roster_end'];
            } else {
                $rosterValue = '-';
            }
            $sheet->setCellValue('C' . $row, $rosterValue);
            
            // Add borders
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ]
                ]
            ]);
            
            $row++;
        }
        
        // Add 2 blank rows between employees
        $row += 2;
    }
    
    // Set print area and page setup
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
    $sheet->getPageSetup()->setFitToWidth(1);
    
    // Download file
    $filename = "Weekly_Timesheet_Report_{$start_date}_to_{$end_date}.xlsx";
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// public function exportTimesheetExcel($start_date, $end_date)
// {
//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
//     ini_set('memory_limit', '512M');

//     // Fetch timesheets
//     $timesheets = $this->timesheet_model->get_timesheets_by_date_range($start_date, $end_date, $this->location_id);

//     if (empty($timesheets)) {
//         show_error('No timesheet data found');
//     }

//     // Group timesheets by employee and date
//     $grouped = [];
//     foreach ($timesheets as $ts) {
//         $empName = $ts['employee_name'];
//         $date    = $ts['roster_date'];

//         if (!isset($grouped[$empName])) {
//             $grouped[$empName] = [
//                 'dates' => [],
//                 'total_hours' => 0.0
//             ];
//         }

//         $clockIn  = strtotime($ts['clock_in_time']);
//         $clockOut = strtotime($ts['clock_out_time']);

//         if (!$clockIn || !$clockOut) {
//             continue; // Skip invalid
//         }

//         // Worked seconds
//         $workedSeconds = $clockOut - $clockIn;

//         // Break in seconds
//         $breakMinutes = (int) ($ts['total_break_duration'] ?? 0);
//         $breakSeconds = $breakMinutes * 60;

//         // Net seconds
//         $netSeconds = max(0, $workedSeconds - $breakSeconds);

//         // Decimal hours
//         $decimalHours = round($netSeconds / 3600, 2);

//         // Display time with break
//         $displayTime = date('H:i', $clockIn) . '-' . date('H:i', $clockOut) . "\nBreak: {$breakMinutes}m";

//         // Store per date
//         $grouped[$empName]['dates'][$date] = $displayTime;

//         // Accumulate total
//         $grouped[$empName]['total_hours'] += $decimalHours;
//     }

//     // Create Spreadsheet
//     $spreadsheet = new Spreadsheet();
//     $sheet = $spreadsheet->getActiveSheet();
//     $sheet->setTitle('Timesheets');

//     // Headers: Employee | Monday | Tuesday | ... | Sunday | Total Hours
//     $col = 'A';
//     $sheet->setCellValue($col++ . '1', 'Employee Name');

//     // Get unique dates and sort them (assume dates are in range)
//     $allDates = [];
//     foreach ($grouped as $data) {
//         $allDates = array_merge($allDates, array_keys($data['dates']));
//     }
//     $allDates = array_unique($allDates);
//     sort($allDates);

//     $dateColumns = [];
//     foreach ($allDates as $date) {
//         $dayLabel = date('l', strtotime($date)); // e.g., Monday
//         $sheet->setCellValue($col . '1', $dayLabel);
//         $dateColumns[$date] = $col++;
//     }

//     $sheet->setCellValue($col . '1', 'Total Hours');

//     // Style header
//     $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
//         'font' => ['bold' => true],
//         'alignment' => ['horizontal' => 'center'],
//     ]);

//     // Enable text wrapping for multi-line cells
//     $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

//     // Data rows
//     $row = 2;
//     foreach ($grouped as $empName => $data) {
//         $col = 'A';
//         $sheet->setCellValue($col++ . $row, $empName);

//         // Fill dates
//         foreach ($dateColumns as $date => $colLetter) {
//             $content = $data['dates'][$date] ?? 'Off';
//             $sheet->setCellValue($colLetter . $row, $content);
//         }

//         // Total hours (decimal)
//         $sheet->setCellValue($col . $row, round($data['total_hours'], 2));

//         // Set row height for multi-line
//         $sheet->getRowDimension($row)->setRowHeight(40); // Adjust as needed

//         $row++;
//     }

//     // Format total hours column as decimal
//     $totalCol = $sheet->getHighestColumn();
//     $sheet->getStyle($totalCol . '2:' . $totalCol . ($row - 1))
//           ->getNumberFormat()
//           ->setFormatCode('0.00');

//     // Auto size columns
//     foreach (range('A', $sheet->getHighestColumn()) as $colID) {
//         $sheet->getColumnDimension($colID)->setAutoSize(true);
//     }

//     // Download
//     $filename = "Timesheet_{$start_date}_to_{$end_date}.xlsx";
//     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//     header("Content-Disposition: attachment; filename=\"$filename\"");
//     header('Cache-Control: max-age=0');

//     $writer = new Xlsx($spreadsheet);
//     $writer->save('php://output');
//     exit;
// }







    
    public function update_timesheet() {
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    $rolesToAccess = ['Manager', 'Admin'];
    if (!in_array($this->roleName, $rolesToAccess) && $this->roleId != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized Access']);
        return;
    }
    
    $timesheet_id = $this->input->post('timesheet_id');
    $clock_in_time = $this->input->post('clock_in_time');
    $clock_out_time = $this->input->post('clock_out_time');
    
    if (empty($timesheet_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Timesheet ID is required']);
        return;
    }
    
    if (empty($clock_in_time) && empty($clock_out_time)) {
        echo json_encode(['status' => 'error', 'message' => 'At least one time field is required']);
        return;
    }
    
    $result = $this->timesheet_model->update_timesheet_times($timesheet_id, $clock_in_time, $clock_out_time);
    
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Timesheet updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update timesheet']);
    }
}

/**
 * Approve a single timesheet entry
 * Called via AJAX
 */
public function approve_single_timesheet() {
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    $rolesToAccess = ['Manager', 'Admin'];
    
    if (!in_array($this->roleName, $rolesToAccess) && $this->roleId != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized Access']);
        return;
    }
    
    $timesheet_id = $this->input->post('timesheet_id');
    
    if (empty($timesheet_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Timesheet ID is required']);
        return;
    }
    
    $result = $this->timesheet_model->approve_single_timesheet($timesheet_id);
    
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Timesheet approved successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to approve timesheet']);
    }
}

/**
 * Set manual break override for a timesheet entry
 * Called via AJAX by managers to override automatic break calculation
 */
public function set_manual_break_override() {
    if (!$this->input->is_ajax_request()) {
        show_404();
    }

    $rolesToAccess = ['Manager', 'Admin'];
    
    if (!in_array($this->roleName, $rolesToAccess) && $this->roleId != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized Access']);
        return;
    }
    
    $timesheet_id = $this->input->post('timesheet_id');
    $break_minutes = $this->input->post('break_minutes');
    
    if (empty($timesheet_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Timesheet ID is required']);
        return;
    }
    
    // If break_minutes is empty string, remove manual override (set to automatic)
    if ($break_minutes === '' || $break_minutes === null) {
        $updateData = [
            'manual_break_override' => 0,
            'manual_break_minutes' => null
        ];
    } else {
        // Set manual override with specified break duration
        $updateData = [
            'manual_break_override' => 1,
            'manual_break_minutes' => (int)$break_minutes
        ];
    }
    
    try {
        $result = $this->common_model->commonRecordUpdate('HR_timesheet_details', 'timesheet_id', $timesheet_id, $updateData);
        
        if ($result) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Break override updated successfully',
                'break_minutes' => $break_minutes === '' ? 'auto' : (int)$break_minutes
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update break override']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * Save manager comment for a timesheet entry
 * Called via AJAX by managers to add notes/comments
 */
public function save_manager_comment() {
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    $rolesToAccess = ['Manager', 'Admin'];
    
    if (!in_array($this->roleName, $rolesToAccess) && $this->roleId != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized Access']);
        return;
    }
    
    $timesheet_id = $this->input->post('timesheet_id');
    $comment = $this->input->post('comment');
    
    if (empty($timesheet_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Timesheet ID is required']);
        return;
    }
    
    // Allow empty comment to clear existing comment
    $updateData = [
        'manager_comment' => trim($comment)
    ];
    
    try {
        $result = $this->common_model->commonRecordUpdate('HR_timesheet_details', 'timesheet_id', $timesheet_id, $updateData);
        
        if ($result) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Comment saved successfully',
                'comment' => trim($comment)
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save comment']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
    
    public function verifyFace()
    {
    $input = json_decode(file_get_contents('php://input'), true);
    $employeeId = $input['employee_id'];
    $base64Image = $input['captured_image'];

    if (!$employeeId || !$base64Image) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        return;
    }

    $tempFile = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
    file_put_contents($tempFile, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image)));

    require_once FCPATH . 'vendor/autoload.php'; 
   

    $bucket = 'bizadmin-hr-employee-images';
    $storedKey = 'employee_faces/' . $employeeId . '.jpg';

    $rekognition = new RekognitionClient([
        'region' => 'ap-southeast-2',
        'version' => 'latest',
        'credentials' => [
            'key'    => 'AKIAXZ3XPGYZLXYPII56',
            'secret' => '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',
        ]
    ]);

    try {
        $result = $rekognition->compareFaces([
            'SourceImage' => [
                'S3Object' => [
                    'Bucket' => $bucket,
                    'Name'   => $storedKey,
                ]
            ],
            'TargetImage' => [
                'Bytes' => file_get_contents($tempFile)
            ],
            'SimilarityThreshold' => 85
        ]);

        if (!empty($result['FaceMatches']) && $result['FaceMatches'][0]['Similarity'] >= 85) {
                echo json_encode(['status' => 'success', 'similarity' => $result['FaceMatches'][0]['Similarity']]);
               
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Face does not match,Please contact Admin/Manager']);
            }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Face does not match or exist,Please contact Admin/Manager']);
    } finally {
        if (file_exists($tempFile)) unlink($tempFile);
    }
}

      public function verifyPin() {
        $employeeId = $this->input->post('employee_id');
        $pin = $this->input->post('pin');

        if (!$employeeId || !$pin) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        $employee = $this->common_model->fetchRecordsDynamically( 'HR_employee',  ['pin'],['emp_id' => $employeeId, 'pin' => $pin]);
           
         if (isset($employee) && !empty($employee)) {
            echo json_encode(['status' => 'success']);
          } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid PIN']);
         }   
            
    }

     // for timesheet portal only , where employee can clockin and clockout
    public function clock_action()
    {
    
        $timesheetId = $this->input->post('timesheet_id');
        $employeeId = $this->input->post('employee_id');
        $action = $this->input->post('action');
        
        // Get location data if provided
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $address = $this->input->post('address');

        if (!$employeeId || !$action) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            exit;
        }

        // Validate: Prevent clock_in if another shift is active for this employee on today
        if ($action === 'clock_in') {
            $activeShift = $this->tenantDb
                ->select('timesheet_id')
                ->from('HR_timesheet_details')
                ->where([
                    'employee_id' => $employeeId,
                    'roster_date' => date('Y-m-d'),
                    'is_deleted' => 0,
                    'location_id' => $this->location_id
                ])
                ->where('clock_in_time IS NOT NULL', NULL, FALSE)
                ->where('clock_out_time IS NULL', NULL, FALSE)
                ->get()
                ->row_array();
            
            if ($activeShift && $activeShift['timesheet_id'] != $timesheetId) {
                echo json_encode(['status' => 'error', 'message' => 'Please clock out from current shift before clocking in to another shift']);
                exit;
            }
        }

        $this->tenantDb->trans_start();

        if ($timesheetId == 0 && in_array($action, ['clock_in'])) {
            $clockInTime = $this->roundClockTime(date('Y-m-d H:i:s'));
            $timesheetData = [
                'roster_id' => null,
                'employee_id' => $employeeId,
                'prep_area_id' => $this->input->post('prep_area_id') ?? 0,
                'position_id' => null,
                'roster_date' => date('Y-m-d'),
                'roster_start_time' => null,
                'roster_end_time' => null,
                'roster_break_start_time' => null,
                'roster_break_type' => null,
                'roster_break_duration' => 0,
                'task_description' => null,
                'clock_in_time' => ($action === 'clock_in' ? $clockInTime : null),
                'clock_out_time' => null,
                'actual_break_duration' => 0,
                'approval_status' => 'pending',
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'location_id' => $this->location_id
            ];
            
            // Add location data for clock in
            if ($latitude && $longitude) {
                $timesheetData['clock_in_latitude'] = $latitude;
                $timesheetData['clock_in_longitude'] = $longitude;
                $timesheetData['clock_in_address'] = $address;
            }
            
            try {
                $timesheetId = $this->common_model->commonRecordCreate('HR_timesheet_details', $timesheetData);
            } catch (Exception $e) {
                $this->tenantDb->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to create timesheet: ' . $e->getMessage()]);
                exit;
            }
        }

        // Handle timesheet actions
        $updateData = ['updated_at' => date('Y-m-d H:i:s')];
        $responseData = [];
        if ($action === 'clock_in') {
            $timesheet = $this->common_model->fetchRecordsDynamically('HR_timesheet_details', ['timesheet_id', 'clock_in_time'], ['timesheet_id' => $timesheetId, 'is_deleted' => 0]);
            if (!empty($timesheet) && $timesheet[0]['clock_in_time']) {
                echo json_encode(['status' => 'error', 'message' => 'Employee already clocked in']);
                exit;
            }
            $roundedClockIn = $this->roundClockTime(date('Y-m-d H:i:s'));
            $updateData['clock_in_time'] = $roundedClockIn;
            
            // Add location data for clock in
            if ($latitude && $longitude) {
                $updateData['clock_in_latitude'] = $latitude;
                $updateData['clock_in_longitude'] = $longitude;
                $updateData['clock_in_address'] = $address;
            }
            
            $responseData['clock_in_time'] = date('h:i A', strtotime($roundedClockIn));
            try {
                $this->common_model->commonRecordUpdate('HR_timesheet_details', 'timesheet_id', $timesheetId, $updateData);
            } catch (Exception $e) {
                $this->tenantDb->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to update timesheet: ' . $e->getMessage()]);
                exit;
            }
        } elseif ($action === 'clock_out') {
            $timesheet = $this->common_model->fetchRecordsDynamically('HR_timesheet_details', ['timesheet_id', 'clock_in_time'], ['timesheet_id' => $timesheetId, 'is_deleted' => 0]);
            if (empty($timesheet) || !$timesheet[0]['clock_in_time']) {
                echo json_encode(['status' => 'error', 'message' => 'Employee must clock in first']);
                exit;
            }
            // add 30 mins and 60 mins break if emp worked for 5 hrs or 10 hrs accordingly
            $clockOutTime = $this->roundClockTime(date('Y-m-d H:i:s'));
           
$updateData['clock_out_time'] = $clockOutTime;

// Add location data for clock out
if ($latitude && $longitude) {
    $updateData['clock_out_latitude'] = $latitude;
    $updateData['clock_out_longitude'] = $longitude;
    $updateData['clock_out_address'] = $address;
}

$responseData['clock_out_time'] = date('h:i A', strtotime($clockOutTime));

// Fetch the full timesheet row (for clock_in_time and roster_date)
$timesheetFull = $this->common_model->fetchRecordsDynamically('HR_timesheet_details', ['clock_in_time', 'roster_date'], ['timesheet_id' => $timesheetId, 'is_deleted' => 0]);
$clockInTime = $timesheetFull[0]['clock_in_time'] ?? null;
$rosterDate = $timesheetFull[0]['roster_date'] ?? date('Y-m-d');

if ($clockInTime) {
    // Use helper function to calculate worked seconds (handles overnight shifts)
    $workedSeconds = $this->timesheet_model->calculateWorkedSeconds($clockInTime, $clockOutTime, $rosterDate);
    $workedHours = $workedSeconds / 3600;

    // Get total break duration for this timesheet
    $existingBreakMinutes = $this->timesheet_model->getBreakDurationForTimesheet($timesheetId,$employeeId);

    if ($workedHours > 10 && $existingBreakMinutes < 60) {
        $addBreak = 60 - $existingBreakMinutes;
    } elseif ($workedHours > 5 && $existingBreakMinutes < 30) {
        $addBreak = 30 - $existingBreakMinutes;
    } else {
        $addBreak = 0;
    }

    // Add missing break as a closed break entry
    if ($addBreak > 0) {
        $autoBreakStart = date('Y-m-d H:i:s', strtotime($clockOutTime) - ($addBreak * 60));
        $autoBreakEnd   = $clockOutTime;
        $this->common_model->commonRecordCreate('HR_timesheet_breaks', [
            'timesheet_id'     => $timesheetId,
            'employee_id'     => $employeeId,
            'break_start_time' => $autoBreakStart,
            'break_end_time'   => $autoBreakEnd,
            'break_duration'   => $addBreak,
            'is_deleted'       => 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        // Update actual_break_duration in timesheet
        $totalBreak = $existingBreakMinutes + $addBreak;
        $this->common_model->commonRecordUpdate('HR_timesheet_details', 'timesheet_id', $timesheetId, [
            'actual_break_duration' => $totalBreak
        ]);
    }
}

            try {
                $this->common_model->commonRecordUpdate('HR_timesheet_details', 'timesheet_id', $timesheetId, $updateData);
            } catch (Exception $e) {
                $this->tenantDb->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to update timesheet: ' . $e->getMessage()]);
                exit;
            }
        } elseif ($action === 'break_start') {
            $timesheet = $this->common_model->fetchRecordsDynamically('HR_timesheet_details', ['timesheet_id', 'clock_in_time'], ['timesheet_id' => $timesheetId, 'is_deleted' => 0]);
            if (empty($timesheet) || !$timesheet[0]['clock_in_time']) {
                echo json_encode(['status' => 'error', 'message' => 'Employee must clock in first']);
                exit;
            }
            // Check if there's an open break
            $latestBreak = $this->timesheet_model->getLatestBreak($timesheetId, $employeeId);
            if ($latestBreak && !$latestBreak['break_end_time']) {
                echo json_encode(['status' => 'error', 'message' => 'A break is already in progress']);
                exit;
            }
            // Create new break record with rounded time
            $roundedBreakStart = $this->roundClockTime(date('Y-m-d H:i:s'));
            $breakData = [
                'timesheet_id' => $timesheetId,
                'employee_id' => $employeeId,
                'break_start_time' => $roundedBreakStart,
                'break_end_time' => null,
                'break_duration' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            try {
                $breakId = $this->common_model->commonRecordCreate('HR_timesheet_breaks', $breakData);
                $responseData['break_start_time'] = date('h:i A', strtotime($roundedBreakStart));
            } catch (Exception $e) {
                $this->tenantDb->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to start break: ' . $e->getMessage()]);
                exit;
            }
        } elseif ($action === 'break_end') {
            $latestBreak = $this->timesheet_model->getLatestBreak($timesheetId, $employeeId);
            if (!$latestBreak || $latestBreak['break_end_time']) {
                echo json_encode(['status' => 'error', 'message' => 'No active break found']);
                exit;
            }
            $breakEndTime = $this->roundClockTime(date('Y-m-d H:i:s'));
            $breakDuration = max(0, (strtotime($breakEndTime) - strtotime($latestBreak['break_start_time'])) / 60);
            $breakUpdateData = [
                'break_end_time' => $breakEndTime,
                'break_duration' => $breakDuration,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            try {
                $this->common_model->commonRecordUpdate('HR_timesheet_breaks', 'break_id', $latestBreak['break_id'], $breakUpdateData);
                // Update actual_break_duration in HR_timesheet
                $totalBreakDuration = $this->timesheet_model->getBreakDurationForTimesheet($timesheetId, $employeeId);
                $this->common_model->commonRecordUpdate('HR_timesheet_details', 'timesheet_id', $timesheetId, ['actual_break_duration' => $totalBreakDuration]);
                $responseData['break_duration'] = $totalBreakDuration;
                $responseData['break_end_time'] = date('h:i A', strtotime($breakEndTime));
            } catch (Exception $e) {
                $this->tenantDb->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Failed to end break: ' . $e->getMessage()]);
                exit;
            }
        }

        $responseData['status'] = 'success';
        $responseData['message'] = ucfirst(str_replace('_', ' ', $action)) . ' successful';
        $this->tenantDb->trans_complete();

        if ($this->tenantDb->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
            exit;
        }

        echo json_encode($responseData);
        exit;
    }

    /**
     * Round clock time to nearest 15-minute quarter
     * If more than 5 minutes past a quarter (0, 15, 30, 45), round up to next quarter
     * If 5 minutes or less past a quarter, round back to that quarter
     * 
     * Examples:
     * 6:04 -> 6:00, 6:06 -> 6:15, 6:11 -> 6:15
     * 15:18 -> 15:15, 15:22 -> 15:30, 15:28 -> 15:30
     * 
     * @param string $datetime DateTime string
     * @return string Rounded datetime
     */
    private function roundClockTime($datetime) {
        $timestamp = strtotime($datetime);
        $minutes = (int)date('i', $timestamp);
        
        // Calculate minutes past the last quarter hour (0, 15, 30, 45)
        $minutesPastQuarter = $minutes % 15;
        
        // If more than 5 minutes past quarter, round up to next quarter
        // If 5 minutes or less, round back to current quarter
        if ($minutesPastQuarter > 5) {
            $roundedMinutes = $minutes + (15 - $minutesPastQuarter);
        } else {
            $roundedMinutes = $minutes - $minutesPastQuarter;
        }
        
        // Handle hour overflow
        $hours = (int)date('H', $timestamp);
        if ($roundedMinutes >= 60) {
            $hours++;
            $roundedMinutes -= 60;
        }
        
        // Handle day overflow (if hours >= 24)
        $date = date('Y-m-d', $timestamp);
        if ($hours >= 24) {
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
            $hours -= 24;
        }
        
        // Return rounded datetime
        return $date . ' ' . sprintf('%02d:%02d:00', $hours, $roundedMinutes);
    }

    /**
     * Calculate total break minutes using rounded break start/end times.
     * Fetches individual break records, rounds each to nearest quarter hour,
     * then computes and sums durations from rounded times.
     */
    private function getRoundedBreakMinutes($timesheetId, $employeeId) {
        $breaks = $this->tenantDb->select('break_start_time, break_end_time')
            ->from('HR_timesheet_breaks')
            ->where(['timesheet_id' => $timesheetId, 'employee_id' => $employeeId, 'is_deleted' => 0])
            ->get()->result_array();

        $totalMinutes = 0;
        foreach ($breaks as $break) {
            if (!empty($break['break_start_time']) && !empty($break['break_end_time'])) {
                $roundedStart = $this->roundClockTime($break['break_start_time']);
                $roundedEnd = $this->roundClockTime($break['break_end_time']);
                $duration = max(0, (strtotime($roundedEnd) - strtotime($roundedStart)) / 60);
                $totalMinutes += $duration;
            }
        }

        return (int) $totalMinutes;
    }

    // public function autoApproveTimesheet($rosterId) {
        
    //     if (empty($rosterId) || !is_numeric($rosterId)) {
    //         return ['status' => 'error', 'message' => 'Invalid roster ID'];
    //     }
  
    //     // fetch pending timesheets
    //     $where = ['roster_id' => $rosterId, 'is_deleted' => 0, 'approval_status' => 'pending'];
    //     $timesheets = $this->common_model->fetchRecordsDynamically('HR_timesheet_details', '', $where);

    //     if (empty($timesheets)) {
    //         return ['status' => 'error', 'message' => 'No pending timesheets found'];
    //     }

    //     $updatedTimesheets = [];
    //     $timeTolerance = 15 * 60; 
    //     $breakTolerance = 5; // 5 minutes

    //     foreach ($timesheets as $ts) {
    //         if (empty($ts['clock_in_time']) || empty($ts['clock_out_time'])) {
    //             continue;
    //         }

    //         try {
    //             $rosterStart = DateTime::createFromFormat('H:i:s', $ts['roster_start_time'] ?? '00:00:00');
    //             $rosterEnd = DateTime::createFromFormat('H:i:s', $ts['roster_end_time'] ?? '00:00:00');
    //             $clockIn = DateTime::createFromFormat('H:i:s', $ts['clock_in_time']);
    //             $clockOut = DateTime::createFromFormat('H:i:s', $ts['clock_out_time']);

    //             if (!$rosterStart || !$rosterEnd || !$clockIn || !$clockOut) {
    //                 continue;
    //             }

    //             $startDiff = abs($rosterStart->getTimestamp() - $clockIn->getTimestamp());
    //             $endDiff = abs($rosterEnd->getTimestamp() - $clockOut->getTimestamp());
    //             $breakDiff = abs(($ts['roster_break_duration'] ?? 0) - ($ts['actual_break_duration'] ?? 0));

    //             if ($startDiff <= $timeTolerance && $endDiff <= $timeTolerance && $breakDiff <= $breakTolerance) {
    //                 $updatedTimesheets[] = [
    //                     'timesheet_id' => $ts['timesheet_id'],
    //                     'approval_status' => 'approved'
    //                 ];
    //             }
    //         } catch (Exception $e) {
    //             continue;
    //         }
    //     }

    //     if (!empty($updatedTimesheets)) {
    //         $this->tenantDb->update_batch('HR_timesheet_details', $updatedTimesheets, 'timesheet_id');
    //         return [
    //             'status' => 'success',
    //             'message' => count($updatedTimesheets) . ' timesheets auto-approved'
    //         ];
    //     }

    //     return ['status' => 'success', 'message' => 'No timesheets eligible for auto-approval'];
    // }
    
    
    
    // function for  manual approval of timesheet on click of button
    
    public function approve_employee_timesheets() {
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    $rolesToAccess = ['Manager', 'Admin'];
    
    if (!in_array($this->roleName, $rolesToAccess) && $this->roleId != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized Access']);
        return;
    }
    
    $employee_id = $this->input->post('employee_id');
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');
    
    if (empty($employee_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID is required']);
        return;
    }
    
    if (empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Start date and end date are required']);
        return;
    }
    
    $result = $this->timesheet_model->approve_employee_timesheets($employee_id, $start_date, $end_date);
    
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'All timesheets for employee approved successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to approve timesheets']);
    }
}
    
    // add task for emp in timesheet without roster , all methods below are related to timesheet without roster
    
public function get_employee_tasks() 
{
    $emp_id = $this->input->post('employee_id');
    $date   = $this->input->post('task_date');

    $row = $this->tenantDb->get_where('HR_tasks', ['employee_id' => $emp_id,'task_date'   => $date])->row_array();
    
    if (!$row) {
        echo json_encode(['success' => true, 'tasks' => []]);
        return;
    }

    // Decode JSON tasks
    $tasksArray = !empty($row['task_description'])  ? json_decode($row['task_description'], true) : [];
       
    echo json_encode([
        'success'      => true,
        'tasks'        => $tasksArray,          // array of tasks
        'prep_area_id' => $row['prep_area_id'], // single value now
        'position_id'  => $row['position_id']
    ]);
}


   public function save_employee_tasks() 
   {
    $this->output->set_content_type('application/json');
    $input = json_decode($this->input->raw_input_stream, true);

    $employee_id   = $input['employee_id']   ?? null;
    $task_date     = $input['task_date']     ?? null;
    $prep_area_id  = $input['prep_area_id']  ?? null;
    $position_id   = $input['empPositionId'] ?? null;
    $tasks         = $input['tasks']         ?? []; // array of tasks

    if (!$employee_id || !$task_date) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        return;
    }

    // Create timesheet entry if not exists
    $timesheetId = $this->create_timesheet_entry($input);

    // Encode tasks as JSON
    $jsonTasks = json_encode($tasks, JSON_UNESCAPED_UNICODE);

    // Check if entry already exists
    $existing = $this->common_model->fetchRecordsDynamically('HR_tasks','',  ['employee_id' => $employee_id,'task_date'   => $task_date,'prep_area_id'  => $prep_area_id]);


    $data = [
        'employee_id'  => $employee_id,
        'location_id'  => $this->location_id,
        'task_date'    => $task_date,
        'prep_area_id' => $prep_area_id,
        'position_id'  => $position_id,
        'task_description'   => $jsonTasks,
        'timesheet_id' => $timesheetId,
        'updated_at'   => date('Y-m-d H:i:s')
    ];

    if ($existing) {
        // Update
        $this->common_model->commonRecordUpdateMultipleConditions('HR_tasks', ['employee_id' => $employee_id,'task_date'   => $task_date,'prep_area_id'  => $prep_area_id], $data);
    } else {
        // Insert
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->tenantDb->insert('HR_tasks', $data);
    }
    
    
     // DELETE existing daily task status for this user & date
     $this->tenantDb->where(['emp_id' => $employee_id,'date'   => $task_date]);
     $this->tenantDb->delete('hr_task_daily_status');

// INSERT each task as separate row
foreach ($tasks as $taskNote) {

    // Clean task text again for DB safety
    $taskNote = trim($taskNote);

    if ($taskNote === '') continue;

    $dailyData = [
        'emp_id'                => $employee_id,
        'date'                  => $task_date,
        'task_descr'            => $taskNote,
        'status'                => 0, // 0 = pending, 1 = completed
        'time_task_completed_at'=> null
    ];

    $this->tenantDb->insert('hr_task_daily_status', $dailyData);
    
   
}
 echo json_encode(['success' => true]);
   }
   
   public function create_timesheet_entry($postedData) {
    
    $employee_id   = $postedData['employee_id']   ?? null;
    $timesheet_date     = $postedData['task_date']     ?? null;
    $prep_area_id  = $postedData['prep_area_id']  ?? null;
    $position_id   = $postedData['empPositionId'] ?? null;
    $tasks         = $postedData['tasks']         ?? []; // array of tasks
    
    $monday = date('Y-m-d', strtotime('monday this week', strtotime($timesheet_date))); // start date
    $sunday = date('Y-m-d', strtotime('sunday this week', strtotime($timesheet_date))); // end date

    $week_start = date('Y-m-d', strtotime($monday));
    $week_end = date('Y-m-d', strtotime($sunday));
  
    // --------------------------------
    // 2. Check if parent timesheet exists
    // --------------------------------
    $conditionsTimesheet = ['location_id' => $this->location_id, 'date_from' => $week_start];
    $timesheetExists = $this->common_model->fetchRecordsDynamically('HR_timesheet',['id'],  $conditionsTimesheet);
  
    if(!$timesheetExists){
        $timesheetData = array(
            'date_from'   => $week_start,
            'date_to'     => $week_end,
            'location_id' => $this->location_id,
            'status'      => 1,
            'date_added'  => date('Y-m-d H:i:s')
        );

        $timesheetId = $this->common_model->commonRecordCreate('HR_timesheet', $timesheetData);
    } else {
        $timesheetId = $timesheetExists[0]['id'];
    }


       // Check if detail entry exists (HR_timesheet_details)
   $exists = $this->common_model->fetchRecordsDynamically( 'HR_timesheet_details',[], [ 'employee_id' => $employee_id,'roster_date' => $timesheet_date,'is_deleted'  => 0],'',1);


                 
        if (!$exists) {

            $detail = array(
                'parent_timesheet_id' => $timesheetId,
                'location_id' => $this->location_id,
                'employee_id'          => $employee_id,
                'prep_area_id'         => $prep_area_id,
                'position_id'          => $position_id,
                'roster_date'          => $timesheet_date,
                'roster_start_time'    => null,
                'roster_end_time'      => null,
                'task_description'     => 'Tasks assigned timesheet without roster',
                'approval_status'      => 'pending',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'status'               => 0
            );

            $this->common_model->commonRecordCreate('HR_timesheet_details', $detail);
            $inserted++;
        }
        
   return $timesheetId;
      
    

    
}
  // remove employee from timesheet
  public function removeEmployeeFromTimesheet()
  {
    $employee_id    = $this->input->post('employee_id');
    $timesheet_week = $this->input->post('timesheet_week'); // "01 Dec - 07 Dec"
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    if (!$employee_id || !$timesheet_week) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        return;
    }

    // -----------------------------
    // 1. Parse week string
    // -----------------------------
    $parts = array_map('trim', explode('-', $timesheet_week));
    if (count($parts) !== 2) {
        echo json_encode(['success' => false, 'message' => 'Invalid week format']);
        return;
    }

    $year = date('Y');
    $startDate = date('Y-m-d', strtotime($parts[0] . ' ' . $year));
    $endDate   = date('Y-m-d', strtotime($parts[1] . ' ' . $year));

    // -----------------------------
    // 2. Build full date range
    // -----------------------------
    $period = new DatePeriod(
        new DateTime($startDate),
        new DateInterval('P1D'),
        (new DateTime($endDate))->modify('+1 day')
    );

    $dates = [];
    foreach ($period as $d) {
        $dates[] = $d->format('Y-m-d');
    }

    if (empty($dates)) {
        echo json_encode(['success' => false, 'message' => 'No dates found']);
        return;
    }

    // -----------------------------
    // 3. Find dates that HAVE clock-in or clock-out (we must preserve these)
    // -----------------------------
   $rowsWithClock = $this->tenantDb
    ->distinct()
    ->select('roster_date')
    ->from('HR_timesheet_details')
    ->where('employee_id', $employee_id)
    ->where_in('roster_date', $dates)
    ->group_start()
        ->where('clock_in_time IS NOT NULL', null, false)
        ->where('clock_in_time !=', '')
        ->or_group_start()
            ->where('clock_out_time IS NOT NULL', null, false)
            ->where('clock_out_time !=', '')
        ->group_end()
    ->group_end()
    ->get()
    ->result_array();
    $datesWithClock = array_column($rowsWithClock, 'roster_date');

    // -----------------------------
    // 4. Delete HR_tasks only for dates WITHOUT clock data
    // -----------------------------
    // Build delete candidate query: employee_id + task_date in $dates AND NOT IN $datesWithClock
    $deleteTaskDates = array_diff($dates, $datesWithClock);

    $deletedTaskCount = 0;
    if (!empty($deleteTaskDates)) {
        $taskRows = $this->tenantDb
            ->select('task_id')
            ->where('employee_id', $employee_id)
            ->where_in('task_date', $deleteTaskDates)
            ->get('HR_tasks')
            ->result_array();

        foreach ($taskRows as $row) {
            // use your common delete helper
            $this->common_model->commonRecordDelete('HR_tasks', $row['task_id'], 'task_id');
            $deletedTaskCount++;
        }
    }

    // -----------------------------
    // 5. Delete HR_timesheet_details only for rows that have BOTH clock fields blank
    // -----------------------------
    // We want: clock_in_time IS NULL OR ''  AND  clock_out_time IS NULL OR ''
    $detailRows = $this->tenantDb
        ->select('timesheet_id, roster_date')
        ->where('employee_id', $employee_id)
        ->where_in('roster_date', $dates)
        ->group_start()
            ->where('clock_in_time', NULL)
            ->or_where('clock_in_time', '')
        ->group_end()
        ->group_start()
            ->where('clock_out_time', NULL)
            ->or_where('clock_out_time', '')
        ->group_end()
        ->get('HR_timesheet_details')
        ->result_array();

    $deletedDetailCount = 0;
    $keptDatesFromDetails = []; // dates we did NOT delete from details because clock exists
    foreach ($detailRows as $row) {
        $this->common_model->commonRecordDelete('HR_timesheet_details', $row['timesheet_id'], 'timesheet_id');
        $deletedDetailCount++;
    }

    // Note: If you also want to preserve HR_tasks for dates where details had clock entries,
    // we've already excluded those via $datesWithClock above.

    echo json_encode([
        'success' => true,
        'message' => 'Employee removed successfully.',
        'deleted_tasks' => $deletedTaskCount,
        'deleted_details' => $deletedDetailCount,
        'preserved_dates_with_clock' => array_values($datesWithClock)
    ]);
}


   

// update status to 1 for the employee whose task has been added
 public function publishEmployeetimesheet()
 {
    $employee_id = $this->input->post('employee_id');
    $weekString  = $this->input->post('timesheetWeek');  // "01 Dec - 07 Dec"

    if (empty($employee_id) || empty($weekString)) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        return;
    }

//   Extract start & end from string
    list($start, $end) = array_map('trim', explode('-', $weekString));

    // Append year if missing (from your view you don't have year)
    $currentYear = date('Y');
    $startDate = date('Y-m-d', strtotime($start . ' ' . $currentYear));
    $endDate   = date('Y-m-d', strtotime($end   . ' ' . $currentYear));

//   Generate all dates in this week
    $period = new DatePeriod(
        new DateTime($startDate),
        new DateInterval('P1D'),
        (new DateTime($endDate))->modify('+1 day')
    );

    // Update status = 1 for each date
    foreach ($period as $date) {
        $task_date = $date->format("Y-m-d");
        $this->tenantDb
            ->where([
                'employee_id' => $employee_id,
                'roster_date' => $task_date
            ])
            ->update('HR_timesheet_details', ['status' => 1]);
    }

    echo json_encode(['success' => true]);
}

  // for adding and editing employee and their task to timesheet without roster
    public function timesheetWithoutRoster($startDate='',$endDate='',$timesheetId = '') {
        // Determine date range
        $data['edit'] = false;
        if (!empty($startDate) && !empty($endDate)) {
        $weekStart = $startDate;
        $weekEnd   = $endDate;
        $timesheetDateRange = date("d M", strtotime($weekStart)) . " - " . date("d M", strtotime($weekEnd));
        $dateRange = $this->createDateFormat($timesheetDateRange);
    
        }else if ($timesheetId) {
            // fetch DB dates in case of edit timesheet
    $conditionsTimesheet = ['id' => $timesheetId];
    $currentTimesheetInfo = $this->common_model->fetchRecordsDynamically('HR_timesheet',['date_from','date_to'],  $conditionsTimesheet);
     
    $weekStart = $currentTimesheetInfo[0]['date_from'];
    $weekEnd   = $currentTimesheetInfo[0]['date_to'];
    $data['edit'] = true;
    // FORMAT EXACTLY LIKE ELSE PART:
    $timesheetDateRange = date("d M", strtotime($weekStart)) . " - " . date("d M", strtotime($weekEnd));
    $dateRange = $this->createDateFormat($timesheetDateRange);
        } else {
            $dateRange = [
                'start_date' => date('Y-m-d', strtotime('monday this week')),
                'end_date' => date('Y-m-d', strtotime('sunday this week'))
            ];
            
       $monday = new DateTime('monday this week');
       $weekStart = $monday->format('Y-m-d');
       $weekEnd   = $monday->modify('+6 days')->format('Y-m-d');
       $monday->modify('-6 days'); // reset

        }
        $data['displayText'] = date("d M", strtotime($weekStart)) . " - " . date("d M", strtotime($weekEnd));
        // Fetch employee and position lists
        $conditions = ['location_id' => $this->location_id];
        $data['empLists']  = $this->employee_model->employeeList('', '', true);
    
        $data['positionLists'] = $this->common_model->fetchRecordsDynamically('HR_emp_position', '', $conditions);
        $data['prepAreaLists'] = $this->common_model->fetchRecordsDynamically('HR_prepArea', '', $conditions);
        $data['positions'] = $this->common_model->fetchRecordsDynamically('HR_emp_position', '', $conditions);
        $data['dateRange'] = $dateRange;
        $data['timesheetId'] = $timesheetId;
        
        // fetch current week added timesheet
        // echo "<pre>"; print_r($data['empLists']); exit;
      


// Fetch all timesheet entries for this week
$timesheetEntries = $this->timesheet_model->timesheetEntryThisweekForTimesheetWithoutRoster($weekStart,$weekEnd);
$timesheetEmployeeIds = array_column($timesheetEntries, 'employee_id');

// Fetch all tasks for this week (for task count)
$allTasks = $this->timesheet_model->taskThisweekForTimesheetWithoutRoster($weekStart,$weekEnd);


$tasks_by_emp_date = [];
foreach ($allTasks as $t) {
    $key = $t['employee_id'] . '_' . $t['task_date'];
    $tasks_by_emp_date[$key][] = $t;
}
// echo "<pre>"; print_r($tasks_by_emp_date); exit;

$data['tasks_by_emp_date'] = $tasks_by_emp_date;
$data['timesheetEmployeeIds'] = $timesheetEmployeeIds;


        // Load views
        $this->load->view('general/header');
        $this->load->view('timesheet/timesheetWithoutRoster', $data);
        $this->load->view('general/footer');
    }
    
 // view timesheet without roster 
   public function viewTimesheetWithoutRoster($timesheetId)
   {
    $data = [];

    /* ------------------------------------
     * 1. VALIDATE timesheetId
     * ------------------------------------ */
    if (empty($timesheetId) || !is_numeric($timesheetId)) {
        show_error("Invalid timesheet ID", 400);
        return;
    }

    /* ------------------------------------
     * 2. Fetch week start & end safely
     * ------------------------------------ */
    $conditions = ['id' => $timesheetId];
    $startEndDate = $this->common_model->fetchRecordsDynamically('HR_timesheet', ['date_from', 'date_to'],$conditions);
        

    // Default fallbacks
    $defaultWeekStart = date('Y-m-d');
    $defaultWeekEnd   = date('Y-m-d', strtotime('+6 days'));

    if (!empty($startEndDate) && isset($startEndDate[0])) {
        $data['week_start'] = !empty($startEndDate[0]['date_from']) ? $startEndDate[0]['date_from'] : $defaultWeekStart;
         $data['week_end'] = !empty($startEndDate[0]['date_to']) ? $startEndDate[0]['date_to'] : $defaultWeekEnd;

    } else {
        // If no record found, use fallback
        $data['week_start'] = $defaultWeekStart;
        $data['week_end']   = $defaultWeekEnd;
    }

    /* ------------------------------------
     * 3. Fetch weekly tasks safely
     * ------------------------------------ */
     $conditionsPrep = ['location_id' => $this->location_id];
      $data['prep_areas'] = $this->common_model->fetchRecordsDynamically('HR_prepArea', '', $conditionsPrep);
      
    $data['prep_areas_data'] = $this->timesheet_model->get_weekly_tasks_by_prep_area($timesheetId, $data['week_start']);
        $data['displayText'] = date("d M", strtotime($data['week_start'])) . " - " . date("d M", strtotime($data['week_end']));

    // Guarantee array exists
    if (!is_array($data['prep_areas_data'])) {
        $data['prep_areas_data'] = [];
    }
    // echo $data['week_start']; 
    // echo "<pre>"; print_r($data['prep_areas_data']); exit;

    /* ------------------------------------
     * 4. Page title
     * ------------------------------------ */
    $data['page_title'] = 'Weekly Tasks – Prep Area View';
    
    // echo "<pre>"; print_r($data); exit;

    /* ------------------------------------
     * 5. Load views
     * ------------------------------------ */
    $this->load->view('general/header');
    $this->load->view('timesheet/viewTimesheetWithoutRoster', $data);
    $this->load->view('general/footer');
}

   // AJAX method called on Recreate button click
    public function recreateTimesheet()
    {
    // Only allow AJAX
    if (!$this->input->is_ajax_request()) {
        show_error('Invalid request', 400);
    }

    $recreate_timesheet_id = $this->input->post('recreate_timesheet_id');
    $start_date            = $this->input->post('start_date'); // e.g., 01 Jan, 2025
    $end_date              = $this->input->post('end_date');

    // Convert to MySQL date
    $date_from = date('Y-m-d', strtotime($start_date));
    $date_to   = date('Y-m-d', strtotime($end_date));

    if (!$recreate_timesheet_id || !$date_from || !$date_to) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
        exit;
    }

    // Begin transaction
    $this->tenantDb->trans_start();

    // Step 1: Check if timesheet already exists for this date range + location
    $existing = $this->common_model->fetchRecordsDynamically(
        'HR_timesheet',
        [], // fields (empty = all)
        [
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'is_deleted'  => 0,
            'location_id'  => $this->location_id
        ]
    );

    if (!empty($existing)) {
        $this->tenantDb->trans_rollback();
        echo json_encode([
            'status'  => 'error',
            'message' => 'Timesheet already exists for this week!'
        ]);
        exit;
    }

    // Step 2: Fetch original timesheet (as array)
    $original_list = $this->common_model->fetchRecordsDynamically(
        'HR_timesheet',
        [],
        ['id' => $recreate_timesheet_id]
    );

    if (empty($original_list)) {
        $this->tenantDb->trans_rollback();
        echo json_encode(['status' => 'error', 'message' => 'Original timesheet not found']);
        exit;
    }

    $original_timesheet = $original_list[0]; // first row
    $location_id = $this->location_id; // fallback if needed
    $weekOffsetDays = (strtotime($date_from) - strtotime($original_timesheet['date_from'])) / 86400;

    // Step 3: Create new HR_timesheet
    $new_timesheet_data = [
        'date_from'                   => $date_from,
        'date_to'                     => $date_to,
        'is_timesheet_without_roster' => 1,
        'is_published'                => 1,
        'roster_id'                   => $original_timesheet['roster_id'] ?? null,
        'status'                      => $original_timesheet['status'] ?? 0,
        'is_deleted'                  => 0,
        'location_id'                 => $location_id,
        'date_added'                  => date('Y-m-d H:i:s'),
        'date_modified'               => date('Y-m-d H:i:s')
    ];

    $new_timesheet_id = $this->common_model->commonRecordCreate('HR_timesheet', $new_timesheet_data);

    if (!$new_timesheet_id) {
        $this->tenantDb->trans_rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to create new timesheet']);
        exit;
    }

    // Step 4: Copy HR_timesheet_details
    $details = $this->common_model->fetchRecordsDynamically(
        'HR_timesheet_details',
        [],
        [
            'parent_timesheet_id' => $recreate_timesheet_id,
            'is_deleted'          => 0
        ]
    );

    foreach ($details as $detail) {
        $new_detail = $detail; // already array
        unset($new_detail['timesheet_id']); // remove old reference (auto increment)

        $new_detail['parent_timesheet_id'] = $new_timesheet_id;
        $new_detail['location_id'] = $this->location_id;
        $new_detail['roster_date'] = date('Y-m-d', strtotime($detail['roster_date'] . " + $weekOffsetDays days"));
        $new_detail['created_at']          = date('Y-m-d H:i:s');
        $new_detail['updated_at']          = date('Y-m-d H:i:s');
        
        $this->common_model->commonRecordCreate('HR_timesheet_details', $new_detail);
    }

    // Step 5: Copy HR_tasks
    $tasks = $this->common_model->fetchRecordsDynamically(
        'HR_tasks',
        [],
        [
            'timesheet_id' => $recreate_timesheet_id,
            'is_deleted'   => 0
        ]
    );

    foreach ($tasks as $task) {
        $new_task = $task;
        unset($new_task['task_id']);
        $new_task['task_date'] = date('Y-m-d', strtotime($task['task_date'] . " + $weekOffsetDays days"));
        $new_task['timesheet_id']   = (int)$new_timesheet_id;
        $new_task['created_at']     = date('Y-m-d H:i:s');
        $new_task['updated_at']     = date('Y-m-d H:i:s');

        $this->common_model->commonRecordCreate('HR_tasks', $new_task);
    }

    // Complete transaction
    $this->tenantDb->trans_complete();
    
    if ($this->tenantDb->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to recreate timesheet. Transaction rolled back.']);
    } else {
        echo json_encode([
            'status'   => 'success',
            'message'  => 'Timesheet recreated successfully!',
            'redirect' => base_url('HR/timesheetWithoutRoster')
        ]);
    }
    exit;
}




// ==========================================
// Timesheet Payroll superannuation calculations
// ==========================================

public function payrollCalculation($timesheet_id) {
  
    
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    // Get timesheet info
    $timesheet = $this->common_model->fetchRecordsDynamically(
        'HR_timesheet',
        ['id', 'date_from', 'date_to', 'location_id'],
        ['id' => $timesheet_id, 'is_deleted' => 0]
    );
    
    if (empty($timesheet)) {
        show_404();
        return;
    }
    
    $data['timesheet'] = $timesheet[0];
    $data['timesheet_id'] = $timesheet_id;
    
    // Get superannuation config
    $superConfig = $this->common_model->fetchRecordsDynamically('HR_configuration',['data'], ['location' => $this->location_id, 'configureFor' => 'superannuation']);
        
    if (isset($superConfig[0]['data'])) {
        $data['superConfig'] = json_decode($superConfig[0]['data'], true);
    } else {
        $data['superConfig'] = [
            'super_percentage' => '11.5',
            'enable_tier_payroll' => '0',
            'payroll_tax_rate' => '5.45',
            'public_holidays' => ''
        ];
    }
    
    // Get existing calculation if any
    $existing = $this->common_model->fetchRecordsDynamically(
        'HR_payroll_calculations',
        [],
        ['timesheet_id' => $timesheet_id, 'is_deleted' => 0]
    );
    
    $data['existingCalculation'] = !empty($existing) ? $existing[0] : null;
    
    // Calculate net income from timesheet details
    $netIncome = $this->calculateNetIncome($timesheet_id, $data['timesheet'], $data['superConfig']);
    $data['calculated_net_income'] = $netIncome;
    
    // Get public holidays in date range
    $data['public_holidays'] = $this->getPublicHolidaysInRange(
        $data['timesheet']['date_from'],
        $data['timesheet']['date_to'],
        $data['superConfig']['public_holidays'] ?? ''
    );
    
    $this->load->view('general/header');
    $this->load->view('timesheet/payroll_calculation', $data);
    $this->load->view('general/footer');
}

private function calculateNetIncome($timesheet_id, $timesheet, $superConfig) {
    // Get all timesheet details for this timesheet
    $details = $this->tenantDb
        ->select('td.*, e.tier, etp.rate, etp.Saturday_rate, etp.Sunday_rate, etp.holiday_rate')
        ->from('HR_timesheet_details td')
        ->join('HR_employee e', 'e.emp_id = td.employee_id', 'left')
        ->join('HR_emp_to_position etp', 'etp.emp_id = td.employee_id AND etp.position_id = td.position_id', 'left')
        ->where('td.parent_timesheet_id', $timesheet_id)
        ->where('td.is_deleted', 0)
        ->where('td.status', 1)
        ->get()
        ->result_array();
    
    $tierBasedEnabled = isset($superConfig['enable_tier_payroll']) && $superConfig['enable_tier_payroll'] == '1';
    $publicHolidays = $this->getPublicHolidaysArray($superConfig['public_holidays'] ?? '');
    
    $totalCost = 0;
    $employeeBreakdown = [];
    
    // echo "<pre>"; print_r($details); exit;
    
    foreach ($details as $detail) {
        // Skip if tier-based is enabled and employee is not tier 1
        if ($tierBasedEnabled && $detail['tier'] != 1) {
            continue;
        }
        
        // Calculate hours worked
        if (empty($detail['clock_in_time']) || empty($detail['clock_out_time'])) {
            continue;
        }
        
        $clockIn = new DateTime($detail['roster_date'] . ' ' . $detail['clock_in_time']);
        $clockOut = new DateTime($detail['roster_date'] . ' ' . $detail['clock_out_time']);
        
        $hoursWorked = ($clockOut->getTimestamp() - $clockIn->getTimestamp()) / 3600;
        
        // Subtract break duration (in minutes)
        if (!empty($detail['actual_break_duration'])) {
            $hoursWorked -= ($detail['actual_break_duration'] / 60);
        }
        
        // Determine which rate to use
        $dayOfWeek = date('w', strtotime($detail['roster_date']));
        $isPublicHoliday = in_array($detail['roster_date'], $publicHolidays);
        
        $rate = $detail['rate'];
        if ($isPublicHoliday && !empty($detail['holiday_rate'])) {
            $rate = $detail['holiday_rate'];
        } elseif ($dayOfWeek == 0 && !empty($detail['Sunday_rate'])) { // Sunday
            $rate = $detail['Sunday_rate'];
        } elseif ($dayOfWeek == 6 && !empty($detail['Saturday_rate'])) { // Saturday
            $rate = $detail['Saturday_rate'];
        }
        
        $cost = $hoursWorked * $rate;
        $totalCost += $cost;
        
        // Store breakdown
        $employeeBreakdown[] = [
            'employee_id' => $detail['employee_id'],
            'date' => $detail['roster_date'],
            'hours' => round($hoursWorked, 2),
            'rate' => $rate,
            'cost' => round($cost, 2),
            'is_public_holiday' => $isPublicHoliday,
            'day_type' => $isPublicHoliday ? 'Public Holiday' : ($dayOfWeek == 0 ? 'Sunday' : ($dayOfWeek == 6 ? 'Saturday' : 'Weekday'))
        ];
    }
    
    return [
        'total' => round($totalCost, 2),
        'breakdown' => $employeeBreakdown
    ];
}

private function getPublicHolidaysArray($holidaysString) {
    if (empty($holidaysString)) {
        return [];
    }
    
    $dates = explode(',', $holidaysString);
    return array_map('trim', $dates);
}

private function getPublicHolidaysInRange($dateFrom, $dateTo, $holidaysString) {
    $allHolidays = $this->getPublicHolidaysArray($holidaysString);
    
    $inRange = array_filter($allHolidays, function($date) use ($dateFrom, $dateTo) {
        return $date >= $dateFrom && $date <= $dateTo;
    });
    
    return array_values($inRange);
}

public function savePayrollCalculation() {
    $response = ['status' => 'error', 'message' => 'Invalid request'];
    
    if ($this->input->post()) {
        $timesheet_id = $this->input->post('timesheet_id');
        $x_labour_cost = $this->input->post('x_labour_cost');
        $net_income = $this->input->post('net_income');
        $superannuation = $this->input->post('superannuation');
        $super_rate = $this->input->post('super_rate');
        $cost_inc_super = $this->input->post('cost_inc_super');
        $payroll_tax = $this->input->post('payroll_tax');
        $payroll_tax_rate = $this->input->post('payroll_tax_rate');
        $cost_inc_payroll = $this->input->post('cost_inc_payroll');
        $final_percentage = $this->input->post('final_percentage');
        $tier_based_enabled = $this->input->post('tier_based_enabled') ? 1 : 0;
        
        // Get timesheet info
        $timesheet = $this->common_model->fetchRecordsDynamically(
            'HR_timesheet',
            ['date_from', 'date_to', 'location_id'],
            ['id' => $timesheet_id]
        );
        
        if (empty($timesheet)) {
            $response['message'] = 'Timesheet not found';
            echo json_encode($response);
            return;
        }
        
        $data = [
            'timesheet_id' => $timesheet_id,
            'location_id' => $timesheet[0]['location_id'],
            'date_from' => $timesheet[0]['date_from'],
            'date_to' => $timesheet[0]['date_to'],
            'x_labour_cost' => $x_labour_cost,
            'net_income' => $net_income,
            'superannuation' => $superannuation,
            'super_rate' => $super_rate,
            'cost_inc_super' => $cost_inc_super,
            'payroll_tax' => $payroll_tax,
            'payroll_tax_rate' => $payroll_tax_rate,
            'cost_inc_payroll' => $cost_inc_payroll,
            'final_percentage' => $final_percentage,
            'tier_based_enabled' => $tier_based_enabled,
            'created_by' => $this->session->userdata('user_id')
        ];
        
        // Check if exists
        $existing = $this->common_model->fetchRecordsDynamically(
            'HR_payroll_calculations',
            ['id'],
            ['timesheet_id' => $timesheet_id, 'is_deleted' => 0]
        );
        
        if (!empty($existing)) {
            // Update
            $this->common_model->commonRecordUpdate(
                'HR_payroll_calculations',
                'id',
                $existing[0]['id'],
                $data
            );
        } else {
            // Insert
            $this->common_model->commonRecordCreate('HR_payroll_calculations', $data);
        }
        
        $response = ['status' => 'success', 'message' => 'Payroll calculation saved successfully'];
    }
    
    echo json_encode($response);
}




    
}
    
    ?>