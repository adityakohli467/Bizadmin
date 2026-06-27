<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
</head>
<body class="bg-[#F4F6F9] font-inter">


<main class="max-w-[1920px] mx-auto px-6 py-8 mt-5">
    <div class="grid grid-cols-12 gap-6">
        
      <?php
$w = $employeeProfileWidgetData ?? [];

// Extract values safely
$employee_name       = ucfirst($w['employee_name']) ?? ' User';
$employee_position   = $w['employee_position'] ?? '';
$today_shift         = $w['today_shift'] ?? null;
$shift_started       = $w['shift_started'] ?? false;
$shift_clockin       = $w['shift_clockin_display'] ?? '--:-- --';
$hours_this_week     = $w['hours_this_week'] ?? '0h';
$tasks_completed     = $w['tasks_completed'] ?? 0;
$tasks_total         = $w['tasks_total'] ?? 0;
$attendance_rate     = $w['attendance_rate'] ?? 0;
?>
<aside id="profile-section" class="col-span-12 lg:col-span-3">
    <div class="bg-white rounded-[20px] p-6 shadow-lg">
        <div class="flex flex-col items-center mb-6">
            <div class="relative mb-4">
                <img src="https://bizadmin.com.au/theme-assets/images/users/avatar-1.jpg"
                     alt="Profile"
                     class="w-20 h-20 rounded-full border-2 border-teal">
                     <?php if($shift_started) {  ?>
                <div class="absolute bottom-0 right-0 w-5 h-5 bg-green-500 rounded-full border-2 border-white"></div>
                <?php } else{ ?>
                <div class="absolute bottom-0 right-0 w-5 h-5 bg-gray-300 rounded-full border-2 border-white"></div>
                <?php } ?>
            </div>

            <h2 class="text-xl font-bold text-primary mb-1"><?= htmlspecialchars($employee_name) ?></h2>
            <p class="text-sm text-gray-500 mb-4"><?= htmlspecialchars($employee_position) ?></p>

            <!-- TODAY'S SCHEDULE -->
            <div class="w-full bg-neutralgray rounded-xl p-4 mb-2">
                <p class="text-sm text-gray-600 font-medium mb-2">Today's Schedule</p>

                <?php if (!empty($today_shift) && !empty($today_shift['roster_start_time'])): ?>

                    <p class="text-sm text-gray-700">
                        <?= date('h:i A', strtotime($today_shift['roster_start_time'])) ?>
                         &mdash; 
                        <?= !empty($today_shift['roster_end_time'])
                              ? date('h:i A', strtotime($today_shift['roster_end_time']))
                              : '--:-- --' ?>
                    </p>

                    <?php if ($shift_started): ?>
                        <p class="text-xs text-green-600 mt-1">
                            Shift started at <?= htmlspecialchars($shift_clockin) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-xs text-gray-500 mt-1">Shift not started</p>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="text-xs text-gray-500"></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QUICK STATS -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-semibold text-primary mb-4">Quick Stats</h3>
            <div class="space-y-3">

                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Hours This Week</span>
                    <span class="text-sm font-bold text-primary"><?= htmlspecialchars($hours_this_week) ?></span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Tasks Completed</span>
                    <span class="text-sm font-bold text-teal">
                        <?= $tasks_completed ?>/<?= $tasks_total ?>
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Attendance Rate</span>
                    <span class="text-sm font-bold text-green-600"><?= $attendance_rate ?>%</span>
                </div>

            </div>
        </div>
    </div>

    <!-- LEAVE BALANCE -->
    <div id="leave-balance-widget"
         class="bg-gradient-to-br from-teal to-primary rounded-[20px] p-6 shadow-lg mt-6 text-white">
        <h3 class="text-lg font-bold mb-1">Leave Balance</h3>
        <p class="text-xs text-white/70 mb-4">Your current leave entitlements</p>

        <div class="space-y-4">
            <?php
            $leaveBalances = $leaveBalances ?? [];
            ?>
            <?php if (!empty($leaveBalances)): ?>
                <?php foreach ($leaveBalances as $lb):
                    $entitlement = (float) ($lb['entitlements'] ?? 0);
                    $used        = (float) ($lb['used_days'] ?? 0);
                    $percent     = $entitlement > 0 ? min(100, round(($used / $entitlement) * 100)) : 0;
                ?>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm"><?= htmlspecialchars($lb['leaveTypeName']) ?></span>
                        <span class="text-sm font-bold"><?= $used ?>/<?= $entitlement ?> days</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2" style="width: <?= $percent ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-sm text-white/70">No leave types configured.</p>
            <?php endif; ?>

            <button class="w-full bg-white text-teal hover:bg-gray-100 font-semibold py-2 px-4 rounded-lg transition-all mt-4" 
                    data-bs-toggle="modal" 
                    data-bs-target="#requestLeaveModal">
                Apply Leave
            </button>
        </div>
    </div>
</aside>



        <div class="col-span-12 lg:col-span-9">
            
            <div id="insights-row" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-[16px] p-5 shadow-md hover:shadow-xl transition-shadow border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-primary"><?= (int)($leaveRequestCount ?? 0) ?></span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Leave Requests</p>
                </div>
                <div class="bg-white rounded-[16px] p-5 shadow-md hover:shadow-xl transition-shadow border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl font-bold text-primary"><?= (int)($upcomingShiftsCount ?? 0) ?></span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Upcoming Shifts</p>
                </div>
                <div class="bg-white rounded-[16px] p-5 shadow-md hover:shadow-xl transition-shadow border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <?php if($shift_started) {  ?>
                         <span class="text-3xl font-bold text-green-600">Present</span>
                         <?php } else{ ?>
                         <span class="text-3xl font-bold text-red-600">Absent</span>
                         <?php }  ?>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Attendance Today</p>
                </div>
            </div>

         
    
            <!-- ROW 1: My Timesheets (50%) + My Availability (50%) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                
                <!-- MY TIMESHEETS -->
                <div id="latest-timesheet-section" class="bg-white rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-primary">
                            My Timesheets
                        </h3>
                        <button class="text-teal hover:text-primary text-sm font-medium transition-colors">View All</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                   
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-black uppercase">Start Date</th>
                                    <th class="text-left py-3 px-4 text-xs font-semibold text-black uppercase">End Date</th>
                                    <th class="text-center py-3 px-4 text-xs font-semibold text-black uppercase">Action</th>
                                </tr>
                            </thead>
                            <?php $timesheets = $employeeTimesheets ?? []; ?>

                            <tbody>
                            <?php if (!empty($timesheets)): ?>
                                
                                <?php foreach ($timesheets as $t): ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">

                                        <td class="py-4 px-4 text-sm text-gray-600">
                                            <?= date("F d, Y", strtotime($t['date_from'])) ?>
                                        </td>

                                        <td class="py-4 px-4 text-sm text-gray-600">
                                            <?= date("F d, Y", strtotime($t['date_to'])) ?>
                                        </td>

                                        <td class="py-4 px-4 text-center">
                                            <button type="button"
                                                    data-week-start="<?= $t['date_from'] ?>"
                                                    data-week-end="<?= $t['date_to'] ?>"
                                                    data-emp-id="<?= $empId ?>"
                                                    class="view-timesheet-details bg-teal hover:bg-primary text-white px-4 py-2 rounded-[10px] text-xs font-medium transition-colors inline-flex items-center gap-2">
                                                <span class="btn-text">View</span>
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            </button>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-gray-400 text-sm">
                                        No timesheets found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
               
                <!-- MY AVAILABILITY -->
                <?php
                $availWeekly = [];
                if (!empty($availability) && isset($availability[0]['weekly_json'])) {
                    $availWeekly = json_decode($availability[0]['weekly_json'], true) ?: [];
                }
                $availDays = [
                    "mon" => "Monday",
                    "tue" => "Tuesday",
                    "wed" => "Wednesday",
                    "thu" => "Thursday",
                    "fri" => "Friday",
                    "sat" => "Saturday",
                    "sun" => "Sunday",
                ];
                ?>

                <div id="availability-widget" class="bg-white rounded-[20px] p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-primary">My Availability</h3>
                            <p class="text-xs text-gray-500 mt-1">Set your weekly availability</p>
                        </div>
                        <button type="submit" form="dashboardAvailabilityForm"
                                class="bg-teal hover:bg-primary text-white px-4 py-2 rounded-[12px] text-sm font-medium transition-colors">
                            <span class="dash-avail-text">Save Availability</span>
                            <span class="spinner-border spinner-border-sm d-none" id="dashAvailLoader" role="status" aria-hidden="true"></span>
                        </button>
                    </div>

                    <form id="dashboardAvailabilityForm">
                        <input type="hidden" name="emp_id" value="<?= htmlspecialchars($empId ?? '') ?>">
                        <input type="hidden" name="same_hours" value="0">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <?php foreach ($availDays as $key => $label): ?>
                                <div class="bg-gray-50 rounded-[12px] p-3">
                                    <p class="text-xs font-semibold text-gray-700 mb-2"><?= $label ?></p>
                                    <div class="flex items-center gap-2">
                                        <input type="time"
                                               name="weekly[<?= $key ?>][start]"
                                               value="<?= htmlspecialchars($availWeekly[$key]['start'] ?? '') ?>"
                                               class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-teal">
                                        <span class="text-xs text-gray-400">to</span>
                                        <input type="time"
                                               name="weekly[<?= $key ?>][end]"
                                               value="<?= htmlspecialchars($availWeekly[$key]['end'] ?? '') ?>"
                                               class="w-full text-sm border border-gray-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-teal">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="dashAvailMsg" class="text-xs mt-3 d-none"></div>
                    </form>
                </div>

            </div>
            
            <!-- ROW 2: Today's Attendance Timeline (50%) + Upcoming Schedule (50%) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                
                <!-- TODAY'S ATTENDANCE TIMELINE -->
                <?php
                // $attendance should be provided by controller (array). Example shown in your message.
                if (!isset($attendance) || !is_array($attendance)) {
                    // safe default when attendance not available
                    $attendance = [
                        'date' => date('Y-m-d'),
                        'clock_in' => '--:-- --',
                        'break_start' => '--:-- --',
                        'resume' => '--:-- --',
                        'clock_out' => '--:-- --',
                        'worked_seconds' => 0,
                        'worked_label' => '0m',
                        'target_label' => '8h 00m',
                        'progress_percent' => 0
                    ];
                }

                $clockIn = $attendance['clock_in'] ?? '--:-- --';
                $breakStart = $attendance['break_start'] ?? '--:-- --';
                $resume = $attendance['resume'] ?? '--:-- --';
                $clockOut = $attendance['clock_out'] ?? '--:-- --';
                $workedLabel = $attendance['worked_label'] ?? '0m';
                $targetLabel = $attendance['target_label'] ?? '8h 00m';
                $progressPercent = (int) ($attendance['progress_percent'] ?? 0);

                // keep percent inside 0-100
                $progressPercent = max(0, min(100, $progressPercent));
                ?>

                <div id="attendance-timeline" class="bg-white rounded-[20px] p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-primary mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-teal"></i>
                        Today's Attendance Timeline
                    </h3>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Clock In</p>
                                <p class="text-sm font-bold text-green-600"><?= htmlspecialchars($clockIn) ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Break</p>
                                <p class="text-sm font-bold text-orange-500"><?= htmlspecialchars($breakStart) ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Resume</p>
                                <p class="text-sm font-bold text-orange-500"><?= htmlspecialchars($resume) ?></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Clock Out</p>
                                <p class="text-sm font-bold text-gray-400"><?= htmlspecialchars($clockOut) ?></p>
                            </div>
                        </div>

                        <div class="relative w-full h-3 bg-neutralgray rounded-full overflow-hidden">
                            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-green-500 to-teal rounded-full"
                                 style="width: <?= $progressPercent ?>%"></div>
                        </div>

                        <div class="flex justify-between items-center mt-3">
                            <p class="text-sm text-gray-600">Total Hours: <span class="font-bold text-primary"><?= htmlspecialchars($workedLabel) ?></span></p>
                            <p class="text-sm text-gray-600">Target: <span class="font-bold text-primary"><?= htmlspecialchars($targetLabel) ?></span></p>
                        </div>
                    </div>
                </div>

                <!-- UPCOMING SCHEDULE (CALENDAR) -->
                <?php
                // calender widget related code
                $taskDays = $taskDays ?? [];
                $today    = $today ?? (int) date("d");
                $year     = $year ?? date("Y");
                $month    = $month ?? date("m");

                // Calculate calendar structure
                $firstDayOfMonth = date("w", strtotime("$year-$month-01")); // 0=Sun
                $totalDays       = date("t", strtotime("$year-$month-01"));

                $weeks = [];
                $week  = [];

                // Fill initial blanks for first week
                for ($i = 0; $i < $firstDayOfMonth; $i++) {
                    $week[] = "";
                }

                // Fill days
                for ($d = 1; $d <= $totalDays; $d++) {
                    $week[] = $d;

                    if (count($week) == 7) {
                        $weeks[] = $week;
                        $week = [];
                    }
                }

                // Last week padding
                if (!empty($week)) {
                    while (count($week) < 7) $week[] = "";
                    $weeks[] = $week;
                }
                ?>

                <div id="calendar-widget" class="bg-white rounded-[20px] p-6 shadow-lg">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                            <i class="fa-solid fa-calendar-alt text-teal"></i>
                            Upcoming Schedule
                        </h3>
                        <a class="text-sm text-teal hover:text-primary font-medium" href="#tasks-widget">View Tasks</a>
                    </div>

                    <!-- WEEKDAYS -->
                    <div class="grid grid-cols-7 gap-2 mb-4">
                        <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                            <div class="text-center text-xs font-semibold text-gray-500 py-2"><?= $day ?></div>
                        <?php endforeach; ?>

                        <!-- DAYS -->
                        <?php foreach ($weeks as $week): ?>
                            <?php foreach ($week as $day): ?>
                                
                                <?php if ($day === ""): ?>
                                    <div class="text-center text-xs text-gray-400 py-2"></div>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <?php
                                $isToday = ($day == $today);
                                $hasTask = isset($taskDays[$day]);
                                ?>

                                <div class="text-center text-xs py-2 relative
                                    <?= $isToday ? 'bg-teal text-white rounded-full font-bold' : 'text-gray-700' ?>
                                ">
                                    <?= $day ?>

                                    <?php if ($hasTask): ?>
                                        <div class="w-1 h-1 bg-orange rounded-full absolute bottom-0 left-1/2 transform -translate-x-1/2"></div>
                                    <?php endif; ?>
                                </div>

                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Legend -->
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-orange rounded-full"></div>
                            <span class="text-gray-600">Tasks</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-gray-600">Leave</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-teal rounded-full"></div>
                            <span class="text-gray-600">Today</span>
                        </div>
                    </div>

                </div>      
                 
            </div>

        </div>
    </div>
    
     <?php $this->load->view('unavailabilityCanvas'); ?>
     
     <!-- Leave Request Modal -->
     <div class="modal fade" id="requestLeaveModal" tabindex="-1" aria-labelledby="requestLeaveModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content">
                 <div class="modal-header bg-teal text-white">
                     <h5 class="modal-title" id="requestLeaveModalLabel">
                         <i class="fa-solid fa-calendar-plus me-2"></i>
                         Request Leave
                     </h5>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="alert alert-success d-none" id="leaveSuccessAlert">
                         <i class="fa-solid fa-check-circle me-2"></i>
                         Leave request submitted successfully!
                     </div>
                     <div class="alert alert-danger d-none" id="leaveErrorAlert"></div>
                     
                     <form id="newLeaveRequestForm" enctype="multipart/form-data">
                         <input type="hidden" name="emp_id" value="<?= $empId ?? '' ?>">
                         
                         <div class="row g-3">
                             <div class="col-md-6">
                                 <label for="leave_start_date" class="form-label">
                                     Start Date <span class="text-danger">*</span>
                                 </label>
                                 <input type="date" 
                                        class="form-control" 
                                        id="leave_start_date" 
                                        name="start_date" 
                                        required>
                             </div>
                             
                             <div class="col-md-6">
                                 <label for="leave_end_date" class="form-label">
                                     End Date <span class="text-danger">*</span>
                                 </label>
                                 <input type="date" 
                                        class="form-control" 
                                        id="leave_end_date" 
                                        name="end_date" 
                                        required>
                             </div>
                             
                             <div class="col-md-6">
                                 <label for="leave_type" class="form-label">
                                     Leave Type <span class="text-danger">*</span>
                                 </label>
                                 <select class="form-select" id="leave_type" name="leave_type" required>
                                     <option value="">Select Leave Type</option>
                                     <?php if(isset($leaveTypes) && !empty($leaveTypes)): ?>
                                         <?php foreach($leaveTypes as $type): ?>
                                             <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['leaveTypeName']) ?></option>
                                         <?php endforeach; ?>
                                     <?php endif; ?>
                                 </select>
                             </div>
                             
                             <div class="col-md-6 d-none" id="medicalCertificateField">
                                 <label for="medical_certificate" class="form-label">
                                     Medical Certificate <span class="text-danger">*</span>
                                 </label>
                                 <input type="file" 
                                        class="form-control" 
                                        id="medical_certificate" 
                                        name="userfile[]" 
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                        multiple>
                                 <small class="text-muted">Accepted: PDF, JPG, PNG, DOC, DOCX (Max 8MB)</small>
                             </div>
                             
                             <div class="col-12">
                                 <label for="leave_comments" class="form-label">Comments</label>
                                 <textarea class="form-control" 
                                           id="leave_comments" 
                                           name="leaveComments" 
                                           rows="3" 
                                           placeholder="Enter reason for leave..."></textarea>
                             </div>
                         </div>
                     </form>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                         <i class="fa-solid fa-xmark me-1"></i>
                         Cancel
                     </button>
                     <button type="button" class="btn btn-success" id="submitLeaveRequest">
                         <span class="btn-text">
                             <i class="fa-solid fa-paper-plane me-1"></i>
                             Submit Request
                         </span>
                         <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                     </button>
                 </div>
             </div>
         </div>
     </div>
     
     <!-- Timesheet Details Modal -->
     <div class="modal fade" id="timesheetDetailsModal" tabindex="-1" aria-labelledby="timesheetDetailsModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl modal-dialog-scrollable">
             <div class="modal-content">
                 <div class="modal-header bg-teal text-white">
                     <h5 class="modal-title" id="timesheetDetailsModalLabel">
                         <i class="fa-solid fa-clock-rotate-left me-2"></i>
                         Timesheet Details
                     </h5>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div id="timesheet-loading" class="text-center py-5">
                         <div class="spinner-border text-teal" role="status">
                             <span class="visually-hidden">Loading...</span>
                         </div>
                         <p class="mt-3 text-gray-600">Loading timesheet data...</p>
                     </div>
                     
                     <div id="timesheet-content" class="d-none">
                         <!-- Date Range Header -->
                         <div class="alert alert-info mb-4">
                             <i class="fa-solid fa-calendar-days me-2"></i>
                             <strong>Week:</strong> <span id="modal-date-range"></span>
                         </div>
                         
                         <!-- Timesheet Table -->
                         <div class="table-responsive">
                             <table class="table table-hover">
                                 <thead class="table-light">
                                     <tr>
                                         <th>Date</th>
                                         <th>Clock In</th>
                                         <th>Clock Out</th>
                                         <th>Break Duration</th>
                                         <th>Hours Worked</th>
                                         <th>Location</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody id="timesheet-table-body">
                                     <!-- Data will be populated via AJAX -->
                                 </tbody>
                                 <tfoot class="table-light fw-bold">
                                     <tr>
                                         <td colspan="4" class="text-end">Total Hours:</td>
                                         <td id="total-hours-worked">0h 0m</td>
                                         <td colspan="2"></td>
                                     </tr>
                                 </tfoot>
                             </table>
                         </div>
                         
                         <!-- No Data Message -->
                         <div id="no-timesheet-data" class="alert alert-warning d-none">
                             <i class="fa-solid fa-exclamation-triangle me-2"></i>
                             No timesheet records found for this week.
                         </div>
                     </div>
                     
                     <div id="timesheet-error" class="alert alert-danger d-none">
                         <i class="fa-solid fa-exclamation-circle me-2"></i>
                         <span id="error-message"></span>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 </div>
             </div>
         </div>
     </div>
     
     <script>
     $(document).ready(function() {
         // Handle View button click
         $('.view-timesheet-details').on('click', function() {
             const btn = $(this);
             const weekStart = btn.data('week-start');
             const weekEnd = btn.data('week-end');
             const empId = btn.data('emp-id');
             
             // Show button loader
             btn.prop('disabled', true);
             btn.find('.btn-text').text('Loading...');
             btn.find('.spinner-border').removeClass('d-none');
             
             // Reset modal state
             $('#timesheet-loading').removeClass('d-none');
             $('#timesheet-content').addClass('d-none');
             $('#timesheet-error').addClass('d-none');
             
             // Show modal
             const modal = new bootstrap.Modal(document.getElementById('timesheetDetailsModal'));
             modal.show();
             
             // Fetch timesheet data
             $.ajax({
                 url: '<?= base_url("HR/Home/getTimesheetDetails") ?>',
                 method: 'POST',
                 data: {
                     week_start: weekStart,
                     week_end: weekEnd,
                     emp_id: empId
                 },
                 dataType: 'json',
                 success: function(response) {
                     if (response.success) {
                         // Format date range
                         const startDate = new Date(weekStart).toLocaleDateString('en-US', { 
                             year: 'numeric', month: 'long', day: 'numeric' 
                         });
                         const endDate = new Date(weekEnd).toLocaleDateString('en-US', { 
                             year: 'numeric', month: 'long', day: 'numeric' 
                         });
                         $('#modal-date-range').text(startDate + ' - ' + endDate);
                         
                         // Populate table
                         const tbody = $('#timesheet-table-body');
                         tbody.empty();
                         
                         if (response.data && response.data.length > 0) {
                             let totalHoursText = '0h 0m';
                             
                             response.data.forEach(function(record) {
                                 // Create status badge
                                 const statusBadge = `<span class="badge bg-${record.status_class}">${record.status}</span>`;
                                 
                                 const row = `
                                     <tr>
                                         <td>${record.date}</td>
                                         <td>
                                             ${record.clock_in}
                                             ${record.location && record.location !== 'N/A' ? '<br><small class="text-muted"><i class="fa-solid fa-location-dot"></i> ' + record.location + '</small>' : ''}
                                         </td>
                                         <td>${record.clock_out}</td>
                                         <td>${record.break_info}</td>
                                         <td>${record.total_hours}</td>
                                         <td>${record.location !== 'N/A' ? record.location : '-'}</td>
                                         <td>${statusBadge}</td>
                                     </tr>
                                 `;
                                 tbody.append(row);
                             });
                             
                             // Calculate total from all records
                             let totalMinutes = 0;
                             response.data.forEach(function(record) {
                                 const parts = record.total_hours.match(/(\d+)h\s*(\d+)m/);
                                 if (parts) {
                                     totalMinutes += parseInt(parts[1]) * 60 + parseInt(parts[2]);
                                 }
                             });
                             const totalHours = Math.floor(totalMinutes / 60);
                             const remainingMinutes = totalMinutes % 60;
                             $('#total-hours-worked').text(totalHours + 'h ' + remainingMinutes + 'm');
                             
                             $('#no-timesheet-data').addClass('d-none');
                         } else {
                             $('#no-timesheet-data').removeClass('d-none');
                         }
                         
                         $('#timesheet-loading').addClass('d-none');
                         $('#timesheet-content').removeClass('d-none');
                     } else {
                         $('#timesheet-loading').addClass('d-none');
                         $('#timesheet-error').removeClass('d-none');
                         $('#error-message').text(response.message || 'Failed to load timesheet data');
                     }
                 },
                 error: function(xhr, status, error) {
                     $('#timesheet-loading').addClass('d-none');
                     $('#timesheet-error').removeClass('d-none');
                     $('#error-message').text('An error occurred while loading timesheet data. Please try again.');
                     console.error('Error:', error);
                 },
                 complete: function() {
                     // Hide button loader
                     btn.prop('disabled', false);
                     btn.find('.btn-text').text('View');
                     btn.find('.spinner-border').addClass('d-none');
                 }
             });
         });
         
         // Leave Request Modal Functionality
         $('#leave_type').on('change', function() {
             const selectedText = $(this).find('option:selected').text();
             const isSickLeave = /sick/i.test(selectedText);
             
             if (isSickLeave) {
                 $('#medicalCertificateField').removeClass('d-none');
                 $('#medical_certificate').attr('required', true);
             } else {
                 $('#medicalCertificateField').addClass('d-none');
                 $('#medical_certificate').attr('required', false);
                 $('#medical_certificate').val('');
             }
         });
         
         // Set minimum date to today
         const today = new Date().toISOString().split('T')[0];
         $('#leave_start_date, #leave_end_date').attr('min', today);
         
         // Validate end date is after start date
         $('#leave_start_date').on('change', function() {
             const startDate = $(this).val();
             $('#leave_end_date').attr('min', startDate);
             
             const endDate = $('#leave_end_date').val();
             if (endDate && endDate < startDate) {
                 $('#leave_end_date').val(startDate);
             }
         });
         
         // Submit leave request
         $('#submitLeaveRequest').on('click', function() {
             const form = $('#newLeaveRequestForm')[0];
             
             if (!form.checkValidity()) {
                 form.reportValidity();
                 return;
             }
             
             // Validate sick leave has attachment
             const selectedLeaveType = $('#leave_type option:selected').text();
             const isSickLeave = /sick/i.test(selectedLeaveType);
             const hasFile = $('#medical_certificate')[0].files.length > 0;
             
             if (isSickLeave && !hasFile) {
                 $('#leaveErrorAlert').removeClass('d-none').html(
                     '<i class="fa-solid fa-exclamation-circle me-2"></i>Medical certificate is required for sick leave.'
                 );
                 return;
             }
             
             // Show loading state
             const btn = $(this);
             btn.prop('disabled', true);
             btn.find('.btn-text').addClass('d-none');
             btn.find('.spinner-border').removeClass('d-none');
             
             $('#leaveSuccessAlert, #leaveErrorAlert').addClass('d-none');
             
             // Prepare form data
             const formData = new FormData(form);
             
             // Submit via AJAX
             $.ajax({
                 url: '<?= base_url("HR/Leaves/requestLeave") ?>',
                 type: 'POST',
                 data: formData,
                 processData: false,
                 contentType: false,
                 success: function(response) {
                     if (response === 'success' || (response.success !== undefined && response.success)) {
                         $('#leaveSuccessAlert').removeClass('d-none');
                         $('#newLeaveRequestForm')[0].reset();
                         $('#medicalCertificateField').addClass('d-none');
                         
                         setTimeout(function() {
                             $('#requestLeaveModal').modal('hide');
                             location.reload();
                         }, 2000);
                     } else {
                         $('#leaveErrorAlert').removeClass('d-none').html(
                             '<i class="fa-solid fa-exclamation-circle me-2"></i>' + 
                             (response.message || 'Failed to submit leave request. Please try again.')
                         );
                     }
                 },
                 error: function(xhr, status, error) {
                     $('#leaveErrorAlert').removeClass('d-none').html(
                         '<i class="fa-solid fa-exclamation-circle me-2"></i>An error occurred. Please try again.'
                     );
                     console.error('Error:', error);
                 },
                 complete: function() {
                     btn.prop('disabled', false);
                     btn.find('.btn-text').removeClass('d-none');
                     btn.find('.spinner-border').addClass('d-none');
                 }
             });
         });
         
         // Reset modal on close
         $('#requestLeaveModal').on('hidden.bs.modal', function() {
             $('#newLeaveRequestForm')[0].reset();
             $('#leaveSuccessAlert, #leaveErrorAlert').addClass('d-none');
             $('#medicalCertificateField').addClass('d-none');
             $('#medical_certificate').attr('required', false);
         });

         // Save My Availability (dashboard widget)
         $('#dashboardAvailabilityForm').on('submit', function(e) {
             e.preventDefault();

             const form = $(this);
             const loader = $('#dashAvailLoader');
             const label = $('.dash-avail-text');
             const msg = $('#dashAvailMsg');

             loader.removeClass('d-none');
             label.text('Saving...');
             msg.addClass('d-none');

             $.ajax({
                 url: '<?= base_url("HR/Employees/save_availability") ?>',
                 type: 'POST',
                 data: form.serialize(),
                 dataType: 'json',
                 success: function(res) {
                     if (res.status === 'success') {
                         msg.removeClass('d-none text-red-600').addClass('text-green-600')
                            .text(res.message || 'Availability updated successfully');
                     } else {
                         msg.removeClass('d-none text-green-600').addClass('text-red-600')
                            .text(res.message || 'Failed to update availability');
                     }
                 },
                 error: function() {
                     msg.removeClass('d-none text-green-600').addClass('text-red-600')
                        .text('Something went wrong. Please try again.');
                 },
                 complete: function() {
                     loader.addClass('d-none');
                     label.text('Save Availability');
                 }
             });
         });
     });
     </script>
     
</main>

</body>
</html>