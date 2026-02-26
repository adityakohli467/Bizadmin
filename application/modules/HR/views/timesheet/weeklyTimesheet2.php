<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { display: none; }
        .highlighted-section {
            outline: 2px solid #3F20FB;
            background-color: rgba(63, 32, 251, 0.1);
        }
        .edit-button {
            position: absolute;
            z-index: 1000;
        }
        html, body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;500;600;700;800;900&display=swap">
</head>
<body>
    <div id="layout-wrapper">
        <div class="main-content">
            <div class="page-content custom-page-content">
                <div class="custom-container">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Top Navigation -->
                            <div id="top-nav" class="bg-white border-b border-gray-200 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <select class="border rounded-lg px-3 py-2 text-sm">
                                            <option>Kitchen</option>
                                            <option>Front Desk</option>
                                        </select>
                                        <button class="border rounded-lg px-3 py-2 text-sm flex items-center space-x-2">
                                            <i class="fa-regular fa-calendar"></i>
                                            <span><?php echo date('D d M', strtotime($start_date)) . ' - ' . date('D d M', strtotime($end_date)); ?></span>
                                        </button>
                                        <select class="border rounded-lg px-3 py-2 text-sm">
                                            <option>Group by team member</option>
                                            <option>Group by location</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                                            <i class="fa-solid fa-sliders"></i>
                                        </button>
                                        <button class="bg-primary text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                                            <i class="fa-solid fa-plus"></i>
                                            <span>Add Timesheet</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex space-x-6 mt-6">
                                    <button class="px-3 py-2 text-primary border-b-2 border-primary">All</button>
                                    <button class="px-3 py-2 text-gray-500 hover:text-gray-700">Pending</button>
                                    <button class="px-3 py-2 text-gray-500 hover:text-gray-700">Approved</button>
                                   
                                </div>
                            </div>

                            <!-- Timesheet Content -->
                            <div id="timesheet-content" class="overflow-auto">
                                <?php
                                // Group timesheets by employee
                                $employee_timesheets = [];
                                foreach ($timesheets as $timesheet) {
                                    $employee_timesheets[$timesheet['employee_id']][] = $timesheet;
                                }

                                // Generate array of dates for the week
                                $start = new DateTime($start_date);
                                $end = new DateTime($end_date);
                                $interval = new DateInterval('P1D');
                                $date_range = new DatePeriod($start, $interval, $end->modify('+1 day'));
                                $week_dates = [];
                                foreach ($date_range as $date) {
                                    $week_dates[] = $date->format('Y-m-d');
                                }

                                // Render each employee
                                foreach ($employee_timesheets as $employee_id => $timesheets) {
                                    $employee_name = $timesheets[0]['employee_name'];
                                    $total_hours = array_sum(array_column($timesheets, 'total_hours_sec'));
                                    $timesheet_count = count($timesheets);
                                ?>
                                <div id="timesheet-<?php echo $employee_id; ?>" class="bg-white rounded-lg shadow-sm mb-4">
                                    <div class="p-4 border-b flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center text-sm font-medium">
                                                <?php echo strtoupper(substr($employee_name, 0, 2)); ?>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-black"><?php echo $employee_name; ?></h3>
                                                <p class="text-sm text-gray-500">Total: <?php echo gmdate('H\hi', $total_hours); ?> | <?php echo $timesheet_count; ?> timesheets</p>
                                            </div>
                                        </div>
                                         <div class="flex items-center space-x-2">
                                          <button onclick="approveEmployee(<?php echo $employee_id; ?>, '<?php echo $start_date; ?>', '<?php echo $end_date; ?>')" data-emp-id="<?php echo $employee_id; ?>" class="bg-success text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-2 approve-btn">
                                           <i class="fa-solid fa-check"></i>
                                            <span>Approve</span>
                                          </button>


                                            <button class="text-gray-400 hover:text-gray-600">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-7 gap-4 p-4">
                                        <?php
                                        // Create an array of timesheets indexed by date
                                        $timesheet_by_date = [];
                                        foreach ($timesheets as $ts) {
                                            $timesheet_by_date[$ts['roster_date']] = $ts;
                                        }

                                        // Render a card for each day of the week
                                        foreach ($week_dates as $index => $date) {
                                            $day_name = date('D', strtotime($date));
                                            $timesheet = isset($timesheet_by_date[$date]) ? $timesheet_by_date[$date] : null;
                                        ?>
                                        <div class="space-y-2 p-3 rounded-lg border hover:shadow-sm">
    <div class="text-sm font-medium"><?php echo isset($day_name) ? $day_name : ''; ?></div>

    <?php if (!empty($timesheet)): ?>
        <div class="text-xs text-gray-500">
            <span 
                onclick="editTime(
                    <?php echo $timesheet['timesheet_id']; ?>, 
                    'clock_in_time', 
                    '<?php echo isset($timesheet['clock_in_time']) ? $timesheet['clock_in_time'] : ''; ?>'
                )" 
                class="cursor-pointer hover:underline">
                <?php echo isset($timesheet['clock_in_time']) && !empty($timesheet['clock_in_time']) ? date('h:i A', strtotime($timesheet['clock_in_time'])) : ''; ?>
            </span> - 
            <span 
                onclick="editTime(
                    <?php echo $timesheet['timesheet_id']; ?>, 
                    'clock_out_time', 
                    '<?php echo isset($timesheet['clock_out_time']) ? $timesheet['clock_out_time'] : ''; ?>'
                )" 
                class="cursor-pointer hover:underline">
                <?php echo isset($timesheet['clock_out_time']) && !empty($timesheet['clock_out_time']) ? date('h:i A', strtotime($timesheet['clock_out_time'])) : ''; ?>
            </span>
        </div>

        <div class="text-xs text-gray-500">
            Roster: 
            <?php echo isset($timesheet['shift_start_time']) && !empty($timesheet['shift_start_time']) ? date('h:i A', strtotime($timesheet['shift_start_time'])) : '--'; ?> - 
            <?php echo isset($timesheet['shift_end_time']) && !empty($timesheet['shift_end_time']) ? date('h:i A', strtotime($timesheet['shift_end_time'])) : '--'; ?>
        </div>

        <div class="flex items-center text-xs text-gray-500">
            <i class="fa-solid fa-coffee mr-1"></i> 
            <?php echo isset($timesheet['total_break_duration']) ? gmdate('i\m', $timesheet['total_break_duration']) : '--'; ?>
        </div>

        <div class="flex items-center text-xs text-gray-500">
            <i class="fa-regular fa-clock mr-1"></i> 
            <?php echo isset($timesheet['total_hours']) && !empty($timesheet['total_hours']) ? $timesheet['total_hours'] : '--'; ?>
        </div>

        <div class="text-xs text-gray-500">
            <?php echo (isset($timesheet['prep_name']) ? $timesheet['prep_name'] : '') . ', ' . (isset($timesheet['position_name']) ? $timesheet['position_name'] : ''); ?>
        </div>

        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
            bg-<?php echo (isset($timesheet['approval_status']) && $timesheet['approval_status'] == 'approved') ? 'success' : 'warning'; ?>/10 
            text-<?php echo (isset($timesheet['approval_status']) && $timesheet['approval_status'] == 'approved') ? 'success' : 'warning'; ?>">
            <?php echo isset($timesheet['approval_status']) ? $timesheet['approval_status'] : ''; ?>
        </span>
    <?php else: ?>
        <div class="text-xs text-gray-500">No Data</div>
    <?php endif; ?>
</div>

                                        <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <!-- Bottom Warning Section -->
                            <div id="bottom-warning" class="bg-warning/10 p-4 border-t">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2 text-warning">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span><?php echo count($timesheets); ?> timesheets found</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span>Setup tasks completed</span>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <i class="fa-regular fa-circle-question"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editTime(timesheetId, field, currentValue) {
            let newValue = prompt(`Enter new ${field.replace('_', ' ')}:`, currentValue);
            if (newValue) {
                $.ajax({
                    url: '/HR/timesheet/update_timesheet',
                    type: 'POST',
                    data: {
                        timesheet_id: timesheetId,
                        [field]: newValue
                    },
                    success: function(response) {
                        let result = JSON.parse(response);
                        alert(result.message);
                        if (result.status === 'success') {
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Error updating timesheet');
                    }
                });
            }
        }
        
        function approveEmployee(employeeId, startDate, endDate) {
    if (confirm('Are you sure you want to approve all timesheets for this employee?')) {
        // Find the button using employeeId
        let $btn = $('[data-emp-id="' + employeeId + '"].approve-btn');
        let originalHtml = $btn.html(); // Store original content

        // Show Tailwind CSS spinner (using a simple inline SVG for consistency with Tailwind)
        $btn.html('<svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Approving...')
            .prop('disabled', true);

        $.ajax({
            url: '/HR/timesheet/approve_employee_timesheets',
            type: 'POST',
            data: {
                employee_id: employeeId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                let result = JSON.parse(response);
                alert(result.message);
                if (result.status === 'success') {
                    location.reload();
                }
            },
            error: function() {
                alert('Error approving timesheets');
            },
            complete: function() {
                // Reset button to original state
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    }
}

    </script>
</body>
</html>