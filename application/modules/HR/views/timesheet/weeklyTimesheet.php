<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        window.FontAwesomeConfig = { autoReplaceSvg: 'nest' };
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"]
                    },
                    colors: {
                        primary: {
                            50: "#f0f9ff",
                            100: "#e0f2fe",
                            500: "#0ea5e9",
                            600: "#0284c7",
                            700: "#0369a1"
                        },
                        success: "#10B981",
                        warning: "#F59E0B"
                    }
                }
            }
        };
    </script>
   
 
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
        
        /* Mobile Sidebar Styles */
        #employee-sidebar-timesheet {
            transition: transform 0.3s ease-in-out;
        }
        
        @media (max-width: 768px) {
            #employee-sidebar-timesheet {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }
            
            #employee-sidebar-timesheet.show {
                transform: translateX(0);
            }
            
            .mobile-overlay-timesheet {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .mobile-overlay-timesheet.show {
                display: block;
            }
        }
        
        /* Hamburger Menu */
        .hamburger-menu {
            cursor: pointer;
            padding: 8px;
        }
        
        .hamburger-menu div {
            width: 25px;
            height: 3px;
            background-color: #333;
            margin: 5px 0;
            transition: 0.3s;
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .timesheet-item {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .timesheet-item > div {
                width: 100%;
            }
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
                    <!-- Mobile Overlay -->
                    <div class="mobile-overlay-timesheet" id="mobile-overlay-timesheet"></div>
                    
                    <div class="flex h-screen overflow-hidden">
                        <!-- Sidebar -->
                        <aside id="employee-sidebar-timesheet" class="w-72 bg-white border-r border-gray-200 overflow-y-auto">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">Team Members</h2>
                                    <!-- Close button for mobile -->
                                    <button class="md:hidden text-gray-600 hover:text-gray-800" id="close-sidebar-timesheet">
                                        <i class="fa-solid fa-times text-xl"></i>
                                    </button>
                                </div>
                                <div class="relative">
                                    <input type="text" id="member-search" placeholder="Search members..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>
                            <div class="px-4 py-2">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-gray-500 uppercase">Team Members</span>
                                    <span class="text-xs text-gray-500">
                                        <?php 
                                        echo (isset($timesheets) && is_array($timesheets)) 
                                            ? count(array_unique(array_column($timesheets, 'employee_id'))) 
                                            : 0; 
                                        ?> members
                                    </span>
                                </div>
                            </div>
                            <ul id="team-members-list" class="space-y-1">
                                <?php
                                $employee_timesheets = [];
                                if (isset($timesheets) && is_array($timesheets) && !empty($timesheets)) {
                                    foreach ($timesheets as $timesheet) {
                                        if (isset($timesheet['employee_id'])) {
                                            $employee_timesheets[$timesheet['employee_id']][] = $timesheet;
                                        }
                                    }
                                }
                                
                                $avatar_colors = ['bg-primary-600', 'bg-green-600', 'bg-purple-600', 'bg-amber-600', 'bg-rose-600', 'bg-blue-600'];
                                $color_index = 0;
                                
                                foreach ($employee_timesheets as $employee_id => $employee_ts) {
                                    $employee_name = isset($employee_ts[0]['employee_name']) ? $employee_ts[0]['employee_name'] : 'Unknown';
                                    $total_hours = 0;
                                  
                                    foreach ($employee_ts as $ts) {
                                        if (isset($ts['total_hours']) && !empty($ts['total_hours'])) {
                                            list($hours, $minutes, $seconds) = explode(':', $ts['total_hours']);
                                            $h = is_numeric($hours) ? (int)$hours : 0;
                                            $m = is_numeric($minutes) ? (int)$minutes : 0;
                                            $s = is_numeric($seconds) ? (int)$seconds : 0;
                                            $total_hours += ($h * 3600) + ($m * 60) + $s;
                                        }
                                    }
                                    
                                    // Calculate total break with auto-break logic applied per timesheet entry
                                    $total_break = 0;
                                    foreach ($employee_ts as $ts) {
                                        $ts_break = isset($ts['total_break_duration']) ? (int)$ts['total_break_duration'] : 0;
                                        
                                        // Apply auto-break logic if break is 0
                                        if ($ts_break == 0 && isset($ts['total_hours']) && !empty($ts['total_hours'])) {
                                            list($h, $m, $s) = explode(':', $ts['total_hours']);
                                            $day_seconds = ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                                            $day_hours = $day_seconds / 3600;
                                            
                                            if ($day_hours >= 10) {
                                                $ts_break = 60;
                                            } elseif ($day_hours >= 5) {
                                                $ts_break = 30;
                                            }
                                        }
                                        
                                        $total_break += $ts_break;
                                    }
                                    
                                    if ($total_hours > 0) {
                                        $net_seconds = $total_hours - ($total_break * 60);
                                        // Convert to total minutes and round
                                        $total_minutes = round($net_seconds / 60);
                                        $hours = floor($total_minutes / 60);
                                        $minutes = $total_minutes % 60;
                                        $formatted_hours = "{$hours} hrs {$minutes} min";
                                    } else {
                                        $formatted_hours = "0 hrs 0 min";
                                    }
                                    
                                    $timesheet_count = count($employee_ts);
                                    $avatar_color = $avatar_colors[$color_index % count($avatar_colors)];
                                    $color_index++;
                                    $is_active = $color_index === 1 ? 'border-primary-500' : 'border-transparent';
                                ?>
                                <li class="px-3 py-2 hover:bg-gray-50 cursor-pointer border-l-4 <?php echo htmlspecialchars($is_active); ?> employee-item" 
                                    data-employee-id="<?php echo htmlspecialchars($employee_id); ?>" 
                                    data-employee-name="<?php echo htmlspecialchars(strtolower($employee_name)); ?>">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full <?php echo htmlspecialchars($avatar_color); ?> flex items-center justify-center text-white font-medium flex-shrink-0">
                                            <?php echo strtoupper(substr($employee_name, 0, 2)); ?>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex justify-between">
                                                <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($employee_name); ?></span>
                                                <span class="text-xs text-gray-500"><?php echo htmlspecialchars($formatted_hours); ?></span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo htmlspecialchars($timesheet_count); ?> | <?php echo htmlspecialchars($formatted_hours); ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php } ?>
                            </ul>
                        </aside>

                        <!-- Main Content -->
                        <div id="main-content" class="flex-1 overflow-y-auto">
                            <!-- Filter Bar -->
                            <div id="filter-bar" class="bg-white border-b border-gray-200 p-3 md:p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3 md:gap-4">
                                    <div class="flex flex-wrap items-center gap-2 md:gap-3 w-full md:w-auto">
                                        <!-- Hamburger Menu for Mobile -->
                                        <button class="md:hidden hamburger-menu" id="toggle-sidebar-timesheet">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </button>
                                        <div class="relative flex-1 md:flex-none">
                                            <select id="status-filter" class="appearance-none bg-white border border-gray-300 rounded-md pl-8 md:pl-10 pr-8 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full md:min-w-[180px]">
                                                <option value="all">All Status</option>
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                            </select>
                                            <i class="fa-solid fa-filter absolute left-2 md:left-3 top-2.5 text-gray-400 text-xs md:text-sm"></i>
                                            <i class="fa-solid fa-chevron-down absolute right-3 top-2.5 text-gray-400 text-xs"></i>
                                        </div>
                                        <div class="relative flex-1 md:flex-none">
                                            <button class="flex items-center bg-white border border-gray-300 rounded-md pl-8 md:pl-10 pr-3 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full md:min-w-[220px]">
                                                <span class="truncate">
                                                    <?php 
                                                    echo isset($start_date) && isset($end_date) 
                                                        ? htmlspecialchars(date('D d M', strtotime($start_date)) . ' - ' . date('D d M', strtotime($end_date))) 
                                                        : 'Select Date Range'; 
                                                    ?>
                                                </span>
                                                <i class="fa-solid fa-calendar ml-2 text-gray-400"></i>
                                            </button>
                                            <i class="fa-solid fa-calendar-days absolute left-3 top-2.5 text-gray-400"></i>
                                        </div>
                                        <div class="relative flex-1 md:flex-none">
                                            <select id="prep-area-filter" class="appearance-none bg-white border border-gray-300 rounded-md pl-8 md:pl-10 pr-8 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full md:min-w-[200px]">
                                                <option value="all">All Preps</option>
                                                <?php if (isset($prepAreaLists) && !empty($prepAreaLists)) { ?>
                                                    <?php foreach ($prepAreaLists as $prepAreaList) { ?>
                                                        <option value="<?php echo isset($prepAreaList['id']) ? htmlspecialchars($prepAreaList['id']) : ''; ?>">
                                                            <?php echo isset($prepAreaList['prep_name']) ? htmlspecialchars($prepAreaList['prep_name']) : ''; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <i class="fa-solid fa-layer-group absolute left-2 md:left-3 top-2.5 text-gray-400 text-xs md:text-sm"></i>
                                            <i class="fa-solid fa-chevron-down absolute right-3 top-2.5 text-gray-400 text-xs"></i>
                                        </div>
                                        <button class="p-2 text-gray-500 hover:text-gray-700 border border-gray-300 rounded-md bg-white hidden md:block">
                                            <i class="fa-solid fa-sliders"></i>
                                        </button>
                                    </div>
                                    <div class="flex flex-wrap gap-2 w-full md:w-auto">
                                        <a href="/HR/timesheetWithoutRoster" class="px-2 md:px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs md:text-sm font-medium transition flex items-center justify-center flex-1 md:flex-none">
                                            <i class="fa-solid fa-arrow-left mr-1 md:mr-2"></i>
                                            <span class="hidden sm:inline">Back</span>
                                        </a>
                               
                                        <a href="<?php echo base_url('HR/timesheet/exportTimesheetExcel/' . $start_date . '/' . $end_date); ?>" class="inline-flex items-center justify-center px-2 md:px-3 py-1.5 md:py-2 bg-green-100 border border-green-300 rounded-lg text-green-700 text-xs md:text-sm hover:bg-green-200 flex-1 md:flex-none">
                                            <i class="fa-solid fa-file-excel mr-1"></i> <span class="hidden sm:inline">Export </span>Excel
                                        </a>
                                    </div>

<a href="<?php echo base_url('HR/timesheet/exportTimesheetTX/' . $start_date . '/' . $end_date); ?>" class="px-3 py-1.5 bg-blue-100 border border-blue-300 rounded-lg text-blue-700 text-sm hover:bg-blue-700">
<i class="fa-solid fa-download mr-1"></i> Download TX
</a>


                                </div>
                            </div>
                            
                            <!-- Calculate total hours from all employees -->
                            <?php
                            $total_seconds = 0;
                            $total_approved = 0;
                            $total_pending = 0;

                            foreach ($employee_timesheets as $ts) {
                                if (isset($ts[0]['approval_status'])) {
                                    if ($ts[0]['approval_status'] === 'approved') {
                                        $total_approved++;
                                    } else {
                                        $total_pending++;
                                    }
                                }

                                if (isset($ts[0]['total_hours']) && !empty($ts[0]['total_hours'])) {
                                    list($h, $m, $s) = explode(':', $ts[0]['total_hours']);
                                    $h = is_numeric($h) ? (int)$h : 0;
                                    $m = is_numeric($m) ? (int)$m : 0;
                                    $s = is_numeric($s) ? (int)$s : 0;
                                    $total_seconds += ($h * 3600) + ($m * 60) + $s;
                                }
                            }

                            // Convert to total minutes and round
                            $total_minutes = round($total_seconds / 60);
                            $hours = floor($total_minutes / 60);
                            $minutes = $total_minutes % 60;
                            $total_hours_formatted = "{$hours} hrs {$minutes} min";
                            ?>

                            <!-- Timesheet Summary -->
                            <div id="timesheet-summary" class="p-3 md:p-4">
                                <div class="bg-white mb-2">
                                    <div class="p-3 md:p-4">
                                        <div class="flex flex-wrap items-center">
                                            <div>
                                                <h2 class="text-base md:text-lg font-medium text-gray-800 text-black">Timesheets Summary</h2>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 mt-4 w-full">
                                                <!-- Total Hours -->
                                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-4">
                                                    <div class="bg-blue-500 text-white w-12 h-12 flex items-center justify-center rounded-full text-xl">
                                                        <i class="fa-regular fa-clock"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600">Total Hours</p>
                                                        <p class="text-lg font-semibold text-blue-700"><?php echo htmlspecialchars($total_hours_formatted); ?></p>
                                                    </div>
                                                </div>

                                                <!-- Approved Timesheets -->
                                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-4">
                                                    <div class="bg-green-500 text-white w-12 h-12 flex items-center justify-center rounded-full text-xl">
                                                        <i class="fa-solid fa-check"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600">Approved Timesheets</p>
                                                        <p class="text-lg font-semibold text-green-700"><?php echo htmlspecialchars($total_approved); ?></p>
                                                    </div>
                                                </div>

                                                <!-- Pending Timesheets -->
                                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-center gap-4">
                                                    <div class="bg-amber-500 text-white w-12 h-12 flex items-center justify-center rounded-full text-xl">
                                                        <i class="fa-solid fa-hourglass-half"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600">Pending Timesheets</p>
                                                        <p class="text-lg font-semibold text-amber-700"><?php echo htmlspecialchars($total_pending); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timesheet Groups -->
                                <?php
                                $color_index = 0;
                                foreach ($employee_timesheets as $employee_id => $employee_ts) {
                                    // Sort timesheets by roster_date (Monday to Sunday)
                                    usort($employee_ts, function($a, $b) {
                                        return strtotime($a['roster_date']) - strtotime($b['roster_date']);
                                    });
                                    
                                    $employee_name = isset($employee_ts[0]['employee_name']) ? $employee_ts[0]['employee_name'] : 'Unknown';
                                    $employee_type = (isset($employee_ts[0]['employee_type']) && !empty($employee_ts[0]['employee_type'])) 
                                        ? " ({$employee_ts[0]['employee_type']})" 
                                        : '';
                                    $total_hours = 0;
                                  
                                    foreach ($employee_ts as $ts) {
                                        if (isset($ts['total_hours']) && !empty($ts['total_hours'])) {
                                            list($hours, $minutes, $seconds) = explode(':', $ts['total_hours']);
                                            $h = is_numeric($hours) ? (int)$hours : 0;
                                            $m = is_numeric($minutes) ? (int)$minutes : 0;
                                            $s = is_numeric($seconds) ? (int)$seconds : 0;
                                            $total_hours += ($h * 3600) + ($m * 60) + $s;
                                        }
                                    }
                                    
                                    // Calculate total break with auto-break logic applied per timesheet entry
                                    $total_break = 0;
                                    foreach ($employee_ts as $ts) {
                                        $ts_break = isset($ts['total_break_duration']) ? (int)$ts['total_break_duration'] : 0;
                                        
                                        // Apply auto-break logic if break is 0
                                        if ($ts_break == 0 && isset($ts['total_hours']) && !empty($ts['total_hours'])) {
                                            list($h, $m, $s) = explode(':', $ts['total_hours']);
                                            $day_seconds = ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
                                            $day_hours = $day_seconds / 3600;
                                            
                                            if ($day_hours >= 10) {
                                                $ts_break = 60;
                                            } elseif ($day_hours >= 5) {
                                                $ts_break = 30;
                                            }
                                        }
                                        
                                        $total_break += $ts_break;
                                    }
                                    
                                    if ($total_hours > 0) {
                                        $net_seconds = $total_hours - ($total_break * 60);
                                        // Convert to total minutes and round
                                        $total_minutes = round($net_seconds / 60);
                                        $hours = floor($total_minutes / 60);
                                        $minutes = $total_minutes % 60;
                                        $formatted_hours = "{$hours} hrs {$minutes} min";
                                    } else {
                                        $formatted_hours = "0 hrs 0 min";
                                    }
                                    
                                    $timesheet_count = count($employee_ts);
                                    $avatar_color = $avatar_colors[$color_index % count($avatar_colors)];
                                    $color_index++;
                                    
                                    $all_approved = true;
                                    foreach ($employee_ts as $ts) {
                                        if (!isset($ts['approval_status']) || strtolower($ts['approval_status']) !== 'approved') {
                                            $all_approved = false;
                                            break;
                                        }
                                    }
                                ?>
                                <div id="timesheet-group-<?php echo htmlspecialchars($employee_id); ?>" 
                                     class="bg-white rounded-lg border border-gray-200 mb-4 md:mb-6 overflow-hidden employee-group" 
                                     data-employee-id="<?php echo htmlspecialchars($employee_id); ?>" 
                                     data-employee-name="<?php echo htmlspecialchars(strtolower($employee_name)); ?>">
                                    <div class="flex items-center justify-between p-3 md:p-4 bg-gray-50 border-b border-gray-200 cursor-pointer" 
                                         onclick="toggleSection('timesheet-<?php echo htmlspecialchars($employee_id); ?>')">
                                        <div class="flex items-center flex-1 min-w-0">
                                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full <?php echo htmlspecialchars($avatar_color); ?> flex items-center justify-center text-white font-medium mr-2 md:mr-3 flex-shrink-0">
                                                <?php echo strtoupper(substr($employee_name, 0, 2)); ?>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h3 class="text-sm md:text-base font-medium text-gray-800 text-black truncate">
                                                    <?php echo htmlspecialchars($employee_name . $employee_type); ?>
                                                </h3>
                                                <p class="text-xs md:text-sm text-gray-500 truncate">
                                                    <?php echo htmlspecialchars($timesheet_count); ?> timesheets, <?php echo htmlspecialchars($formatted_hours); ?> total
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <?php if (!$all_approved): ?>
                                            <?php if ($can_approve_timesheet): ?>
                                            <button onclick="event.stopPropagation(); approveEmployee(<?php echo htmlspecialchars($employee_id); ?>, '<?php echo isset($start_date) ? htmlspecialchars($start_date) : ''; ?>', '<?php echo isset($end_date) ? htmlspecialchars($end_date) : ''; ?>')" 
                                                    data-emp-id="<?php echo htmlspecialchars($employee_id); ?>" 
                                                    class="bg-success text-white px-2 md:px-3 py-1 md:py-1.5 rounded-md text-xs md:text-sm flex items-center space-x-1 md:space-x-2 approve-btn">
                                                <i class="fa-solid fa-check"></i>
                                                <span class="hidden sm:inline">Approve </span><span>All</span>
                                            </button>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <i id="timesheet-<?php echo htmlspecialchars($employee_id); ?>-chevron" class="fa-solid fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div id="timesheet-<?php echo htmlspecialchars($employee_id); ?>">
                                        <?php foreach ($employee_ts as $timesheet): ?>
                                        <div class="p-4 border-b border-gray-200 hover:bg-gray-50 timesheet-item" 
                                             data-status="<?php echo isset($timesheet['approval_status']) ? htmlspecialchars($timesheet['approval_status']) : 'pending'; ?>"
                                             data-prep-area-id="<?php echo isset($timesheet['prep_area_id']) ? htmlspecialchars($timesheet['prep_area_id']) : ''; ?>">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <span class="text-sm font-medium text-gray-800 mr-2">
                                                            <?php 
                                                            echo isset($timesheet['roster_date']) 
                                                                ? htmlspecialchars(date('l, d M Y', strtotime($timesheet['roster_date']))) 
                                                                : 'N/A'; 
                                                            ?>
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium status-badge
                                                              bg-<?php echo (isset($timesheet['approval_status']) && strtolower($timesheet['approval_status']) == 'approved') ? 'success' : 'danger'; ?>-subtle 
                                                              text-<?php echo (isset($timesheet['approval_status']) && strtolower($timesheet['approval_status']) == 'approved') ? 'success' : 'danger'; ?>">
                                                            <?php echo isset($timesheet['approval_status']) ? htmlspecialchars(ucfirst($timesheet['approval_status'])) : 'Pending'; ?>
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-wrap gap-4">
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fa-regular fa-clock mr-1.5"></i>
                                                            
                                                            <!-- Editable Clock In Time -->
                                                            <span class="editable-time cursor-pointer hover:bg-blue-50 px-2 py-1 rounded border border-transparent hover:border-blue-300 transition-all"
                                                                  data-timesheet-id="<?php echo isset($timesheet['timesheet_id']) ? htmlspecialchars($timesheet['timesheet_id']) : ''; ?>"
                                                                  data-field="clock_in_time"
                                                                  data-current-value="<?php echo isset($timesheet['clock_in_time']) ? htmlspecialchars($timesheet['clock_in_time']) : ''; ?>"
                                                                  onclick="makeEditable(this)">
                                                                <?php 
                                                                echo (isset($timesheet['clock_in_time']) && !empty($timesheet['clock_in_time']) && strtotime($timesheet['clock_in_time'])) 
                                                                    ? htmlspecialchars(date('h:i A', strtotime($timesheet['clock_in_time']))) 
                                                                    : '<span class="text-gray-400">--:--</span>';
                                                                ?>
                                                            </span>
                                                            
                                                            <span class="mx-1">–</span>
                                                            
                                                            <!-- Editable Clock Out Time -->
                                                            <span class="editable-time cursor-pointer hover:bg-blue-50 px-2 py-1 rounded border border-transparent hover:border-blue-300 transition-all"
                                                                  data-timesheet-id="<?php echo isset($timesheet['timesheet_id']) ? htmlspecialchars($timesheet['timesheet_id']) : ''; ?>"
                                                                  data-field="clock_out_time"
                                                                  data-current-value="<?php echo isset($timesheet['clock_out_time']) ? htmlspecialchars($timesheet['clock_out_time']) : ''; ?>"
                                                                  onclick="makeEditable(this)">
                                                                <?php 
                                                                echo (isset($timesheet['clock_out_time']) && !empty($timesheet['clock_out_time']) && strtotime($timesheet['clock_out_time'])) 
                                                                    ? htmlspecialchars(date('h:i A', strtotime($timesheet['clock_out_time']))) 
                                                                    : '<span class="text-gray-400">--:--</span>';
                                                                ?>
                                                            </span>
                                                        </div>

                                                        <!--calculate employee hrs for each day FIRST to determine auto-break-->
                                                        
                                                        <?php 
                                          $total_hours_for_each_day = 0; 
                                         
                                        if (isset($timesheet['total_hours']) && !empty($timesheet['total_hours'])) {
                                            list($hours, $minutes, $seconds) = explode(':', $timesheet['total_hours']);
                                            $h = is_numeric($hours) ? (int)$hours : 0;
                                            $m = is_numeric($minutes) ? (int)$minutes : 0;
                                            $s = is_numeric($seconds) ? (int)$seconds : 0;
                                            $total_hours_for_each_day += ($h * 3600) + ($m * 60) + $s;
                                        }
                                        
                                        // Calculate break duration with automatic break logic
                                        $break_duration = isset($timesheet['total_break_duration']) ? (int)$timesheet['total_break_duration'] : 0;
                                        
                                        // Check if manual break override is set
                                        $manual_override = isset($timesheet['manual_break_override']) && $timesheet['manual_break_override'] == 1;
                                        $manual_break_minutes = isset($timesheet['manual_break_minutes']) ? (int)$timesheet['manual_break_minutes'] : null;
                                        
                                        // Use manual break if override is set, otherwise apply automatic logic
                                        if ($manual_override && $manual_break_minutes !== null) {
                                            $break_duration = $manual_break_minutes;
                                        } elseif ($break_duration == 0 && $total_hours_for_each_day > 0) {
                                            // Auto-add break if not already recorded and not manually overridden
                                            $hours_worked = $total_hours_for_each_day / 3600;
                                            if ($hours_worked > 10) {
                                                $break_duration = 60; // 60 mins for 10+ hours
                                            } elseif ($hours_worked > 5) {
                                                $break_duration = 30; // 30 mins for 5-10 hours
                                            }
                                        }
                                        ?>
                                        
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fa-solid fa-coffee mr-1.5"></i>
                                            <span>
                                                <?php 
                                                echo $break_duration > 0 
                                                    ? htmlspecialchars($break_duration) . ' Mins' 
                                                    : '00 min'; 
                                                ?>
                                            </span>
                                            <?php if ($can_approve_timesheet): ?>
                                            <select class="ml-2 text-xs border border-gray-300 rounded px-2 py-1 break-override-dropdown"
                                                    data-timesheet-id="<?php echo isset($timesheet['timesheet_id']) ? htmlspecialchars($timesheet['timesheet_id']) : ''; ?>"
                                                    data-employee-id="<?php echo htmlspecialchars($employee_id); ?>"
                                                    data-current-override="<?php echo $manual_override ? '1' : '0'; ?>">
                                                <option value="" <?php echo !$manual_override ? 'selected' : ''; ?>>Auto</option>
                                                <option value="0" <?php echo ($manual_override && $manual_break_minutes == 0) ? 'selected' : ''; ?>>0 min</option>
                                                <option value="15" <?php echo ($manual_override && $manual_break_minutes == 15) ? 'selected' : ''; ?>>15 mins</option>
                                                <option value="30" <?php echo ($manual_override && $manual_break_minutes == 30) ? 'selected' : ''; ?>>30 mins</option>
                                                <option value="45" <?php echo ($manual_override && $manual_break_minutes == 45) ? 'selected' : ''; ?>>45 mins</option>
                                                <option value="60" <?php echo ($manual_override && $manual_break_minutes == 60) ? 'selected' : ''; ?>>60 mins</option>
                                            </select>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php 
                                         if ($total_hours_for_each_day) {
                                        $break_minutes = $break_duration;
                                        $net_seconds = $total_hours_for_each_day - ($break_minutes * 60);
                                        // Convert to total minutes and round
                                        $total_minutes = round($net_seconds / 60);
                                        $hours = floor($total_minutes / 60);
                                        $minutes = $total_minutes % 60;
                                        $formatted_hours_for_each_day = "{$hours} hrs {$minutes} min";
                                    } else {
                                        $formatted_hours_for_each_day = "0 hrs 0 min";
                                    }
                                                         ?>
                                                        
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fa-regular fa-clock mr-1.5"></i>
                                                            <span><?php echo htmlspecialchars($formatted_hours_for_each_day); ?></span>
                                                        </div>
                                                        
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fa-solid fa-building mr-1.5"></i>
                                                            <span>
                                                                <?php 
                                                                $prep_name = isset($timesheet['prep_name']) ? $timesheet['prep_name'] : '';
                                                                $position_name = isset($timesheet['position_name']) ? $timesheet['position_name'] : '';
                                                                echo htmlspecialchars($prep_name . ($prep_name && $position_name ? ', ' : '') . $position_name); 
                                                                ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- ALL Roster Shifts for this employee on this day -->
                                                        <?php 
                                                        $emp_id_roster = isset($timesheet['employee_id']) ? $timesheet['employee_id'] : '';
                                                        $roster_date_check = isset($timesheet['roster_date']) ? $timesheet['roster_date'] : '';
                                                        $all_shifts = isset($allRosterShifts[$emp_id_roster][$roster_date_check]) ? $allRosterShifts[$emp_id_roster][$roster_date_check] : [];
                                                        
                                                        if (!empty($all_shifts) && count($all_shifts) > 1): ?>
                                                        <!-- Multiple Shifts Display -->
                                                        <div class="flex items-start text-sm text-gray-600">
                                                            <i class="fa-regular fa-calendar mr-1.5 mt-0.5"></i>
                                                            <div class="flex flex-col">
                                                                <span class="font-medium text-gray-700 mb-1">Roster Shifts (<?php echo count($all_shifts); ?>):</span>
                                                                <div class="flex flex-wrap gap-1.5">
                                                                <?php foreach ($all_shifts as $shift_idx => $shift): ?>
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                                        <span class="mr-1"><?php echo ($shift_idx + 1); ?>.</span>
                                                                        <?php 
                                                                        echo isset($shift['start']) && $shift['start'] 
                                                                            ? htmlspecialchars(date('h:i A', strtotime($shift['start']))) 
                                                                            : '-'; 
                                                                        ?> – 
                                                                        <?php 
                                                                        echo isset($shift['end']) && $shift['end'] 
                                                                            ? htmlspecialchars(date('h:i A', strtotime($shift['end']))) 
                                                                            : '-'; 
                                                                        ?>
                                                                        <?php if (!empty($shift['prep_name'])): ?>
                                                                        <span class="ml-1 text-blue-500">(<?php echo htmlspecialchars($shift['prep_name']); ?>)</span>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php elseif (!empty($all_shifts) && count($all_shifts) == 1): ?>
                                                        <!-- Single Shift Display -->
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fa-regular fa-calendar mr-1.5"></i>
                                                            <span>Roster: 
                                                                <?php 
                                                                echo isset($all_shifts[0]['start']) && $all_shifts[0]['start'] 
                                                                    ? htmlspecialchars(date('h:i A', strtotime($all_shifts[0]['start']))) 
                                                                    : '-'; 
                                                                ?> – 
                                                                <?php 
                                                                echo isset($all_shifts[0]['end']) && $all_shifts[0]['end'] 
                                                                    ? htmlspecialchars(date('h:i A', strtotime($all_shifts[0]['end']))) 
                                                                    : '-'; 
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <?php else: ?>
                                                        <!-- Fallback to original roster data from join -->
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fa-regular fa-calendar mr-1.5"></i>
                                                            <span>Roster: 
                                                                <?php 
                                                                echo (isset($timesheet['shift_start_time']) && $timesheet['shift_start_time']) 
                                                                    ? htmlspecialchars(date('h:i A', strtotime($timesheet['shift_start_time']))) 
                                                                    : '-'; 
                                                                ?> – 
                                                                <?php 
                                                                echo (isset($timesheet['shift_end_time']) && $timesheet['shift_end_time']) 
                                                                    ? htmlspecialchars(date('h:i A', strtotime($timesheet['shift_end_time']))) 
                                                                    : '-'; 
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2"> 
                                                    <!-- Comment Button (only for managers) -->
                                                    <?php if ($can_approve_timesheet): ?>
                                                    <button onclick="openCommentModal(<?php echo isset($timesheet['timesheet_id']) ? htmlspecialchars($timesheet['timesheet_id']) : 0; ?>, '<?php echo isset($timesheet['manager_comment']) ? htmlspecialchars(addslashes($timesheet['manager_comment'])) : ''; ?>')" 
                                                            class="text-blue-600 hover:text-blue-800 px-2 py-1 text-xs border border-blue-300 rounded hover:bg-blue-50 relative" 
                                                            title="Add/View comment">
                                                       <i class="fa-solid fa-comment mr-1"></i>
                                                       <span class="hidden sm:inline">Comment</span>
                                                       <?php if (isset($timesheet['manager_comment']) && !empty($timesheet['manager_comment'])): ?>
                                                       <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">
                                                           <i class="fa-solid fa-check" style="font-size: 8px;"></i>
                                                       </span>
                                                       <?php endif; ?>
                                                    </button>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Approve Button (only show if not approved) -->
                                                    <?php if (!isset($timesheet['approval_status']) || strtolower($timesheet['approval_status']) !== 'approved'): ?>
                                                    <?php if ($can_approve_timesheet): ?>
                                                    <button onclick="approveSingle(<?php echo isset($timesheet['timesheet_id']) ? htmlspecialchars($timesheet['timesheet_id']) : 0; ?>)" 
                                                            data-timesheet-id="<?php echo isset($timesheet['timesheet_id']) ? htmlspecialchars($timesheet['timesheet_id']) : 0; ?>"
                                                            class="text-green-600 hover:text-green-800 px-2 md:px-3 py-1 single-approve-btn btn-md text-xs md:text-sm border border-green-300 rounded hover:bg-green-50" 
                                                            title="Approve timesheet">
                                                       <i class="fa-solid fa-clipboard-check mr-1"></i>
                                                       <span class="hidden sm:inline">Approve</span>

                                                    </button>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                    
                                                    <button class="text-gray-500 hover:text-gray-700 p-1">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manager Comment Modal -->
    <div id="commentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Manager Comment</h3>
                <button onclick="closeCommentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-2">
                <textarea id="commentText" 
                          rows="5" 
                          class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500" 
                          placeholder="Enter your comment or notes here..."></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="closeCommentModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button onclick="saveComment()" 
                        id="saveCommentBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fa-solid fa-save mr-1"></i>
                    Save Comment
                </button>
            </div>
        </div>
    </div>

    <!-- Break Update Loader -->
    <div id="breakUpdateLoader" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 shadow-2xl flex flex-col items-center">
            <svg class="animate-spin h-16 w-16 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-lg font-medium text-gray-700">Updating break override...</p>
            <p class="text-sm text-gray-500 mt-2">Please wait while we refresh the timesheet</p>
        </div>
    </div>

    <style>
        .editable-time-input {
            border: 2px solid #3b82f6;
            padding: 4px 8px;
            border-radius: 4px;
            min-width: 80px;
            outline: none;
            background: #eff6ff;
            font-size: 14px;
        }
        
        .editable-time-saving {
            background: #fee2e2;
            border-color: #fca5a5;
        }
    </style>

    <script>
    
    function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed top-20 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    toast.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(function() {
            document.body.removeChild(toast);
        }, 300);
    }, 2000);
}

        let currentEditingElement = null;
        let currentCommentTimesheetId = null;

        // Comment Modal Functions
        function openCommentModal(timesheetId, existingComment) {
            currentCommentTimesheetId = timesheetId;
            document.getElementById('commentText').value = existingComment || '';
            document.getElementById('commentModal').classList.remove('hidden');
        }

        function closeCommentModal() {
            document.getElementById('commentModal').classList.add('hidden');
            document.getElementById('commentText').value = '';
            currentCommentTimesheetId = null;
        }

        function saveComment() {
            if (!currentCommentTimesheetId) {
                alert('Error: No timesheet selected');
                return;
            }

            const comment = document.getElementById('commentText').value.trim();
            const $saveBtn = $('#saveCommentBtn');
            const originalHtml = $saveBtn.html();

            // Disable button and show loading
            $saveBtn.html('<svg class="animate-spin h-4 w-4 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...').prop('disabled', true);

            $.ajax({
                url: '<?php echo base_url("HR/timesheet/save_manager_comment"); ?>',
                type: 'POST',
                data: {
                    timesheet_id: currentCommentTimesheetId,
                    comment: comment
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status === 'success') {
                        showToast('Comment saved successfully', 'success');
                        closeCommentModal();
                        
                        // Reload page to update comment indicator
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert(result.message || 'Error saving comment');
                        $saveBtn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error saving comment. Please try again.');
                    $saveBtn.html(originalHtml).prop('disabled', false);
                }
            });
        }

        // Close modal when clicking outside
        document.getElementById('commentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCommentModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('commentModal').classList.contains('hidden')) {
                closeCommentModal();
            }
        });

        function toggleSection(id) {
            const section = document.getElementById(id);
            const chevron = document.getElementById(id + '-chevron');
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            } else {
                section.classList.add('hidden');
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            }
        }

        /**
         * Parse user input and convert to 24-hour time format
         * Examples: "13" -> "13:00", "1" -> "01:00", "1pm" -> "13:00", "830" -> "08:30"
         */
        function parseTimeInput(input) {
            if (!input) return null;
            
            input = input.trim().toLowerCase();
            
            // Check for AM/PM
            let isPM = input.includes('pm') || input.includes('p');
            let isAM = input.includes('am') || input.includes('a');
            
            // Remove AM/PM indicators and non-numeric characters except colon
            input = input.replace(/[^0-9:]/g, '');
            
            if (!input) return null;
            
            let hours, minutes;
            
            if (input.includes(':')) {
                // Format: "1:30" or "13:30"
                [hours, minutes] = input.split(':');
                hours = parseInt(hours);
                minutes = parseInt(minutes) || 0;
            } else if (input.length <= 2) {
                // Format: "1" or "13" (just hours)
                hours = parseInt(input);
                minutes = 0;
            } else if (input.length === 3) {
                // Format: "130" -> "1:30"
                hours = parseInt(input.substring(0, 1));
                minutes = parseInt(input.substring(1, 3));
            } else if (input.length === 4) {
                // Format: "0830" -> "08:30"
                hours = parseInt(input.substring(0, 2));
                minutes = parseInt(input.substring(2, 4));
            } else {
                return null;
            }
            
            // Handle PM conversion
            if (isPM && hours < 12) {
                hours += 12;
            }
            
            // Handle AM midnight (12am = 00:00)
            if (isAM && hours === 12) {
                hours = 0;
            }
            
            // Validate
            if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
                return null;
            }
            
            // Format to HH:MM:SS
            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':00';
        }

        function makeEditable(element) {
            // Don't create multiple inputs
            if (currentEditingElement) {
                return;
            }
            
            currentEditingElement = element;
            const timesheetId = element.dataset.timesheetId;
            const field = element.dataset.field;
            const currentValue = element.dataset.currentValue;
            
            // Store original HTML
            const originalHTML = element.innerHTML;
            
            // Create input
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'editable-time-input';
            input.placeholder = 'e.g. 9, 13, 9:30, 1pm';
            input.value = '';
            
            // Replace element content with input
            element.innerHTML = '';
            element.appendChild(input);
            element.style.display = 'inline-block';
            
            // Focus and select
            input.focus();
            
            // Handle blur (save)
            input.addEventListener('blur', function() {
                saveInlineEdit(element, timesheetId, field, input.value, originalHTML);
            });
            
            // Handle Enter key
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur(); // Trigger save
                } else if (e.key === 'Escape') {
                    // Cancel edit
                    element.innerHTML = originalHTML;
                    element.style.display = '';
                    currentEditingElement = null;
                }
            });
        }

        function saveInlineEdit(element, timesheetId, field, inputValue, originalHTML) {
            const parsedTime = parseTimeInput(inputValue);
            
            if (!inputValue.trim()) {
                // Empty input - just restore
                element.innerHTML = originalHTML;
                element.style.display = '';
                currentEditingElement = null;
                return;
            }
            
            if (!parsedTime) {
                alert('Invalid time format. Please use formats like: 9, 13, 9:30, 1pm, 830, etc.');
                element.innerHTML = originalHTML;
                element.style.display = '';
                currentEditingElement = null;
                return;
            }
            
            // Show saving state
            element.innerHTML = '<svg class="animate-spin h-4 w-4 text-blue-600 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            element.classList.add('editable-time-saving');
            
            // Prepare data
            let data = {
                timesheet_id: timesheetId
            };
            data[field] = parsedTime;
            
            $.ajax({
                url: '<?php echo base_url("HR/timesheet/update_timesheet"); ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(result) {
    if (result.status === 'success') {
        // Update the display without reloading
        const timeObj = new Date('2000-01-01 ' + parsedTime);
        const displayTime = timeObj.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
        
        element.innerHTML = displayTime;
        element.dataset.currentValue = parsedTime;
        element.style.display = '';
        
        // Optional: Show success feedback
        element.classList.add('bg-green-100');
        setTimeout(function() {
            element.classList.remove('bg-green-100');
        }, 1500);
        
        // Show success message
        showToast('Time updated successfully', 'success');
    } else {
        alert(result.message || 'Error updating timesheet');
        element.innerHTML = originalHTML;
        element.style.display = '';
    }
},
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error updating timesheet. Please try again.');
                    element.innerHTML = originalHTML;
                    element.style.display = '';
                },
                complete: function() {
                    element.classList.remove('editable-time-saving');
                    currentEditingElement = null;
                }
            });
        }

        // Handle manual break override dropdown
        function handleBreakOverride(selectElement) {
            const timesheetId = $(selectElement).data('timesheet-id');
            const breakMinutes = $(selectElement).val();
            const $select = $(selectElement);
            const originalValue = $select.data('original-value') || $select.val();
            
            // Store original value for rollback on error
            $select.data('original-value', originalValue);
            
            // Show full-screen loader immediately
            $('#breakUpdateLoader').removeClass('hidden');
            
            // Disable dropdown while processing
            $select.prop('disabled', true).addClass('opacity-50');
            
            $.ajax({
                url: '<?php echo base_url("HR/timesheet/set_manual_break_override"); ?>',
                type: 'POST',
                data: {
                    timesheet_id: timesheetId,
                    break_minutes: breakMinutes
                },
                dataType: 'json',
                success: function(result) {
                    if (result.status === 'success') {
                        // Keep loader visible and reload page
                        // The loader will stay visible until the new page loads
                        location.reload();
                    } else {
                        // Hide loader on error
                        $('#breakUpdateLoader').addClass('hidden');
                        alert(result.message || 'Error updating break override');
                        $select.val(originalValue);
                        $select.prop('disabled', false).removeClass('opacity-50');
                    }
                },
                error: function(xhr, status, error) {
                    // Hide loader on error
                    $('#breakUpdateLoader').addClass('hidden');
                    console.error('Error:', error);
                    alert('Error updating break override. Please try again.');
                    $select.val(originalValue);
                    $select.prop('disabled', false).removeClass('opacity-50');
                }
                // Note: No complete callback - we want the loader to stay visible during reload
            });
        }

        function approveEmployee(employeeId, startDate, endDate) {
            if (confirm('Are you sure you want to approve all timesheets for this employee?')) {
                let $btn = $('[data-emp-id="' + employeeId + '"].approve-btn');
                let originalHtml = $btn.html();
                $btn.html('<svg class="animate-spin h-5 w-5 mr-2 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Approving...')
                    .prop('disabled', true);
                $.ajax({
                    url: '<?php echo base_url("HR/timesheet/approve_employee_timesheets"); ?>',
                    type: 'POST',
                    data: {
                        employee_id: employeeId,
                        start_date: startDate,
                        end_date: endDate
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.status === 'success') {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message || 'Error approving timesheets');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error approving timesheets. Please try again.');
                    },
                    complete: function() {
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                });
            }
        }

        function approveSingle(timesheetId) {
    if (confirm('Are you sure you want to approve this timesheet?')) {
        let $btn = $('[data-timesheet-id="' + timesheetId + '"].single-approve-btn');
        let $timesheetItem = $btn.closest('.timesheet-item');
        let originalHtml = $btn.html();
        
        $btn.html('<svg class="animate-spin h-4 w-4 text-green-600 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>')
            .prop('disabled', true);
        
        $.ajax({
            url: '<?php echo base_url("HR/timesheet/approve_single_timesheet"); ?>',
            type: 'POST',
            data: {
                timesheet_id: timesheetId
            },
            dataType: 'json',
            success: function(result) {
                if (result.status === 'success') {
                    // Update status badge (use .status-badge class for reliable selection)
                    let $statusBadge = $timesheetItem.find('.status-badge');
                    $statusBadge
                        .removeClass('bg-danger-subtle text-danger')
                        .addClass('bg-success-subtle text-success')
                        .text('Approved');
                    
                    // Update data attribute
                    $timesheetItem.attr('data-status', 'approved');
                    
                    // Remove approve button with fade out
                    $btn.fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Show success toast
                    showToast('Timesheet approved successfully', 'success');
                    
                    // Check if all timesheets for this employee are now approved
                    let $employeeGroup = $timesheetItem.closest('.employee-group');
                    let allApproved = true;
                    $employeeGroup.find('.timesheet-item').each(function() {
                        if ($(this).data('status') !== 'approved') {
                            allApproved = false;
                            return false;
                        }
                    });
                    
                    // If all approved, remove the "Approve All" button
                    if (allApproved) {
                        $employeeGroup.find('.approve-btn').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                    
                    // Update summary counts
                    updateSummaryCounts();
                    
                } else {
                    alert(result.message || 'Error approving timesheet');
                    $btn.html(originalHtml).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error approving timesheet. Please try again.');
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    }
}

// Helper function to update summary counts
function updateSummaryCounts() {
    let totalApproved = 0;
    let totalPending = 0;
    
    $('.timesheet-item').each(function() {
        let status = $(this).data('status');
        if (status === 'approved') {
            totalApproved++;
        } else {
            totalPending++;
        }
    });
    
    // Update the summary cards
    $('.text-green-700').last().text(totalApproved);
    $('.text-amber-700').last().text(totalPending);
}

        $(document).ready(function() {
            // Break override dropdown change handler
            $(document).on('change', '.break-override-dropdown', function() {
                handleBreakOverride(this);
            });
            
            // Mobile Sidebar Toggle
            $('#toggle-sidebar-timesheet').on('click', function() {
                $('#employee-sidebar-timesheet').addClass('show');
                $('#mobile-overlay-timesheet').addClass('show');
            });
            
            $('#close-sidebar-timesheet, #mobile-overlay-timesheet').on('click', function() {
                $('#employee-sidebar-timesheet').removeClass('show');
                $('#mobile-overlay-timesheet').removeClass('show');
            });
            
            // Employee search functionality
            $('#member-search').on('input', function() {
                let searchTerm = $(this).val().toLowerCase().trim();
                $('.employee-item').each(function() {
                    let employeeName = $(this).data('employee-name');
                    let matches = searchTerm === '' || employeeName.includes(searchTerm);
                    $(this).toggle(matches);
                });
                $('.employee-group').each(function() {
                    let employeeName = $(this).data('employee-name');
                    let matches = searchTerm === '' || employeeName.includes(searchTerm);
                    $(this).toggle(matches);
                });
            });

            // Status filter functionality
            $('#status-filter').on('change', function() {
                let status = $(this).val();
                $('.employee-group').each(function() {
                    let $group = $(this);
                    let $timesheets = $group.find('.timesheet-item');
                    let hasMatchingTimesheet = false;
                    $timesheets.each(function() {
                        let timesheetStatus = $(this).data('status');
                        if (status === 'all' || timesheetStatus === status) {
                            hasMatchingTimesheet = true;
                        }
                    });
                    $group.toggle(hasMatchingTimesheet);
                });
                
                // Update sidebar
                $('.employee-item').each(function() {
                    let employeeId = $(this).data('employee-id');
                    let $group = $('#timesheet-group-' + employeeId);
                    $(this).toggle($group.is(':visible'));
                });
            });

            // Prep area filter functionality
            $('#prep-area-filter').on('change', function() {
                let prepAreaId = $(this).val();
                
                $('.employee-group').each(function() {
                    let $group = $(this);
                    let $timesheets = $group.find('.timesheet-item');
                    let hasMatchingTimesheet = false;
                    
                    $timesheets.each(function() {
                        let timesheetPrepArea = $(this).data('prep-area-id');
                        if (prepAreaId === 'all' || timesheetPrepArea == prepAreaId) {
                            hasMatchingTimesheet = true;
                        }
                    });
                    
                    $group.toggle(hasMatchingTimesheet);
                });
                
                // Update sidebar
                $('.employee-item').each(function() {
                    let employeeId = $(this).data('employee-id');
                    let $group = $('#timesheet-group-' + employeeId);
                    $(this).toggle($group.is(':visible'));
                });
            });
        });
    </script>

<?php $this->load->view('components/scroll_to_top'); ?>
</body>
</html>