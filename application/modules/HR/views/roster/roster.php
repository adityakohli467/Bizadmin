<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>window.FontAwesomeConfig = { autoReplaceSvg: 'nest'};</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Select2 for dropdown search -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;500;600;700;800;900&display=swap">
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
        }
        .fa, .fas, .far, .fal, .fab {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
        }
        ::-webkit-scrollbar {
            display: none;
        }
        html, body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .dragEmployeeBox div {
            cursor: pointer;
        }
        
        /* Mobile Sidebar Styles */
        #employee-sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        @media (max-width: 768px) {
            #employee-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }
            
            #employee-sidebar.show {
                transform: translateX(0);
            }
            
            .mobile-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .mobile-overlay.show {
                display: block;
            }
        }
        
        /* Mobile responsive grid */
        @media (max-width: 640px) {
            .schedule-grid-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .schedule-grid-content {
                min-width: 800px;
            }
        }
        
        /* Drag and Drop Styles */
        .employee-div {
            cursor: move;
        }
        
        .employee-div.dragging {
            opacity: 0.7;
        }
        
        .drag-helper {
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .dragEmployeeBox.drop-zone-active {
            border: 2px dashed #4F46E5;
            background-color: rgba(79, 70, 229, 0.05);
            border-radius: 6px;
        }
        
        .dragEmployeeBox.drop-zone-hover {
            border: 2px solid #4F46E5;
            background-color: rgba(79, 70, 229, 0.1);
            border-radius: 6px;
        }
        
        .drop-area-container.drop-zone-hover {
            background-color: rgba(79, 70, 229, 0.05);
            border-radius: 6px;
        }
        
        .drop-area-container.drop-zone-active {
            background-color: rgba(79, 70, 229, 0.03);
        }
        
        /* Make entire employee box draggable */
        .employee-div span {
            cursor: move;
        }
        
        /* Select2 dropdown styling */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
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
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
            }
            
            .modal-content {
                max-height: 90vh;
                overflow-y: auto;
            }
        }
     
    </style>
    <script>
        tailwind.config = {
            "theme": {
                "extend": {
                    "colors": {
                        "primary": "#4F46E5",
                        "primary-light": "#EEF2FF",
                        "shift-green": "#E6F4EA",
                        "shift-border": "#CAEBD0"
                    },
                    "fontFamily": {
                        "sans": ["Inter", "sans-serif"]
                    }
                },
                "fontFamily": {
                    "sans": ["Inter", "sans-serif"]
                }
            }
        };
    </script>
    
    <style>
    
    #loader-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex; /* Ensure Flexbox is applied */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
    }
    .spinner {
        width: 130px; 
        height: 130px;
        border: 3px solid #f3f3f3; 
        border-top: 3px solid #172153;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    </style>
</head>
<body class="bg-gray-50 font-sans mb-5 mt-5">
    <div id="loader-overlay">
    <div class="spinner"></div>
</div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobile-overlay"></div>
    
    <div class="flex h-screen overflow-hidden">
    <!-- Left Sidebar (Employee List) -->
    <?php if(!isset($roleId) || $roleId != 4) { ?>
    <div id="employee-sidebar" class="w-72 bg-white border-r border-gray-200 flex flex-col mt-3">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg text-black font-semibold text-gray-800">Team Members</h2>
                <!-- Close button for mobile -->
                <button class="md:hidden text-gray-600 hover:text-gray-800" id="close-sidebar">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
                <span class="badge bg-primary text-white px-2 py-1 rounded-full hidden md:inline">
                    <?php echo (isset($empLists) && is_array($empLists)) ? count($empLists) : 0; ?> members
                </span>
            </div>
            <div class="relative mb-3">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search employees" class="filterEmployeeLeftPanel w-full pl-10 pr-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
            </div>
        </div>
        <div class="overflow-y-auto flex-grow">
            <ul id="employee-list" class="px-2">
                <?php 
                $avatar_colors = ['bg-primary', 'bg-green-600', 'bg-purple-600', 'bg-amber-600', 'bg-rose-600', 'bg-blue-600'];
                $color_index = 0;
                ?>
                <?php if (isset($empLists) && !empty($empLists)) { ?>
                    <?php foreach ($empLists as $empList) { ?>
                        <li class="my-1.5">
                            <a class="text-reset employee-div dragSourceElement" 
                               data-employee-name="<?php echo isset($empList['name']) ? htmlspecialchars($empList['name']) : ''; ?>" 
                               data-bs-toggle="collapse" 
                               href="#collapse<?php echo isset($empList['emp_id']) ? htmlspecialchars($empList['emp_id']) : ''; ?>" 
                               aria-expanded="false" 
                               aria-controls="collapse<?php echo isset($empList['emp_id']) ? htmlspecialchars($empList['emp_id']) : ''; ?>">
                                <span class="flex items-center p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <?php
                                    $avatar_color = $avatar_colors[$color_index % count($avatar_colors)];
                                    $color_index++;
                                    $is_active = $color_index === 1 ? 'border-primary-500' : 'border-transparent';
                                    ?>
                                    
                                    
                                    <?php
$showTier = ($tierBasedEnabled === 1 && !empty($empList['tier']));
$avatarText = $showTier ? 'T' . htmlspecialchars($empList['tier']) : (!empty($empList['name']) ? strtoupper(substr(htmlspecialchars($empList['name']), 0, 2)) : '??'  );

?>

                                    <div class="w-10 h-10 rounded-full bg-orange-300 text-white flex items-center justify-center font-medium mr-3">
                                        <?php echo $avatarText; ?>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <p class="text-sm font-medium text-gray-800">
                                            <?php echo isset($empList['name']) ? htmlspecialchars($empList['name']) : ''; ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                             <?php echo isset($empList['primary_position_name']) ? htmlspecialchars($empList['primary_position_name']) : ''; ?>
                                           
                                        </p>
                                        
                                        <p class="text-xs text-gray-500">
<?php echo isset($empList['employee_type']) ? ($empList['employee_type']==1?'Full Time':($empList['employee_type']==2?'Part Time':($empList['employee_type']==3?'Casual':''))) : ''; ?>
                                           
                                        </p>
                                        
                                        
                                        <input type="hidden" class="position_id" value="<?php echo isset($empList['position_id']) ? htmlspecialchars($empList['position_id']) : ''; ?>">
                                        <input type="hidden" class="empId" value="<?php echo isset($empList['emp_id']) ? htmlspecialchars($empList['emp_id']) : ''; ?>">
                                        <input type="hidden" class="empName" value="<?php echo isset($empList['name']) ? htmlspecialchars($empList['name']) : ''; ?>">
                                    </div>
                                    <span class="ml-auto text-xs font-medium bg-green-100 text-green-800 py-1 px-2 rounded-full">
                                        <?php $timesheets = isset($empList['timesheets']) ? $empList['timesheets'] : 0; ?>
                                        <?php echo htmlspecialchars($timesheets); ?>h
                                    </span>
                                </span>
                            </a>
                            <div class="collapse mx-3 bg-gray-100 fs-12 py-2" id="collapse<?php echo isset($empList['emp_id']) ? htmlspecialchars($empList['emp_id']) : ''; ?>"></div>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php } ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden mt-3">
        <!-- Top Bar (Header Section) -->
        <header id="header" class="bg-white border-b border-gray-200 py-3 px-4 md:px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <!-- Left Section -->
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Hamburger Menu for Mobile -->
                    <button class="md:hidden hamburger-menu" id="toggle-sidebar">
                        <div></div>
                        <div></div>
                        <div></div>
                    </button>
                    <div class="flex items-center bg-white border border-gray-300 rounded-lg overflow-hidden">
                        <button class="prevWeek px-2 py-1.5 text-gray-500 hover:bg-gray-100">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="currentWeek px-2 py-1.5 font-medium text-gray-800 text-sm md:text-base cursor-pointer" onclick="toggleDatePicker()">

                            <?php
                            if (isset($weekRange) && $weekRange != '') {
                                $date_text = $weekRange;
                            } else {
                                $monday = new DateTime('monday this week');
                                 $sunday = clone $monday;
                                $sunday->modify('+6 days');
                                $date_text = $monday->format('d M Y') . ' - ' . $sunday->format('d M Y');
                            }
                            
                            if (isset($rosterInfo[0]['start_date']) && !empty($rosterInfo[0]['start_date'])) {
                                $sdate = $rosterInfo[0]['start_date'];
                                $endDate = isset($rosterInfo[0]['end_date']) ? $rosterInfo[0]['end_date'] : '';
                                if (!empty($endDate)) {
                                    $startDateTime = new DateTime($sdate);
                                    $endDateTime = new DateTime($endDate);
                                    $startFormatted = $startDateTime->format('d M Y');
                                    $endFormatted = $endDateTime->format('d M Y');
                                    $date_text = "$startFormatted - $endFormatted";
                                }
                            } else if (isset($rosterStartDate) && $rosterStartDate != '') {
                                $sdate = $rosterStartDate;
                                
                                 if (isset($rosterEndDate) && $rosterEndDate != '') {
                                    $startDateTime = new DateTime($rosterStartDate);
                                    $endDateTime = new DateTime($rosterEndDate);
                                    $startFormatted = $startDateTime->format('d M Y');
                                    $endFormatted = $endDateTime->format('d M Y');
                                    $date_text = "$startFormatted - $endFormatted";
                                }
                            } else {
                                $cdate = date('Y-m-d');
                                $timestamp = strtotime($cdate);
                                $dayOfWeek = date("N", $timestamp);
                                $daysToMonday = $dayOfWeek - 1;
                                $sdate = date("Y-m-d", strtotime("-$daysToMonday days", $timestamp));
                            }
                            
                            echo htmlspecialchars($date_text);
                            ?>
                             <i class="fa-solid fa-calendar-days ml-1 text-xs"></i>
                        </div>
                        <button class="nextWeek px-2 py-1.5 text-gray-500 hover:bg-gray-100">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <select class="weekAreaAndTeam px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm">
                            <option selected value="1">Week by Area</option>
                            <option value="2">Week by Team Member</option>
                        </select>
                    </div>
                    <!-- <div class="relative flex-1 md:flex-none">
                        <input type="text" name="rosterName" id="rosterName" placeholder="Roster Name" 
                               class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm" 
                               value="<?php echo (isset($rosterInfo[0]['rosterName']) && !empty($rosterInfo[0]['rosterName'])) ? htmlspecialchars($rosterInfo[0]['rosterName']) : ''; ?>">
                    </div> -->
                    
                    <div id="datePickerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;" onclick="closeDatePickerOnOutsideClick(event)">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-black mb-4">Select Date Range</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" id="startDatePicker" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                               value="<?php echo isset($rosterStartDate) ? $rosterStartDate : date('Y-m-d', strtotime('monday this week')); ?>">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="date" id="endDatePicker" class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                               value="<?php echo isset($rosterEndDate) ? $rosterEndDate : date('Y-m-d', strtotime('sunday this week')); ?>">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button" onclick="closeDatePicker()" 
                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                                    <button type="button" onclick="applyDateRange()" 
                                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Section -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-center md:justify-end">
                    <button onclick="window.location.href='/HR/roster'" class="px-2 md:px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 text-xs md:text-sm hover:bg-gray-200">
                        <i class="fa-solid fa-arrow-left mr-1"></i> <span class="hidden sm:inline">Back</span>
                    </button>
                    <?php if(!isset($roleId) || $roleId != 4) { ?>
                    <button id="clearCopiedBtn" onclick="clearCopiedData()" class="px-3 py-1.5 bg-orange-100 border border-orange-300 rounded-lg text-orange-700 text-sm hover:bg-orange-200" style="display: none;">
                        <i class="fa-solid fa-eraser mr-1"></i> Clear Copied
                    </button>
                    <button data-bs-toggle="modal" 
                            onclick="showRosterRecreateModal(<?php echo isset($rosterId) ? htmlspecialchars($rosterId) : 0; ?>)" 
                            class="px-2 md:px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs md:text-sm hover:bg-gray-50">
                        <i class="fa-solid fa-rotate mr-1"></i> <span class="hidden sm:inline">Recreate</span>
                    </button>
                    <button onclick="publishRoster('save')" class="px-2 md:px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs md:text-sm hover:bg-gray-50">
                        <i class="fa-regular fa-save mr-1"></i> <span class="hidden sm:inline">Save</span>
                    </button>
                    <button onclick="publishRoster('publish')" class="px-2 md:px-3 py-1.5 bg-primary text-white rounded-lg text-xs md:text-sm hover:bg-primary/90">
                        <i class="fa-solid fa-paper-plane mr-1"></i> <span class="hidden sm:inline">Publish</span>
                    </button>
                    <a href="<?php echo base_url('HR/roster/exportRosterPDF'); ?>?roster_id=<?php echo isset($rosterId) ? (int)$rosterId : 0; ?>" class="inline-flex items-center px-2 md:px-3 py-1.5 bg-purple-100 border border-purple-300 rounded-lg text-purple-700 text-xs md:text-sm hover:bg-purple-200">
                        <i class="fa-solid fa-file-pdf mr-1"></i> <span class="hidden sm:inline">PDF</span>
                    </a>
                    <a href="<?php echo base_url('HR/roster/exportRosterExcel'); ?>?roster_id=<?php echo isset($rosterId) ? (int)$rosterId : 0; ?>" class="inline-flex items-center px-2 md:px-3 py-1.5 bg-green-100 border border-green-300 rounded-lg text-green-700 text-xs md:text-sm hover:bg-green-200">
                        <i class="fa-solid fa-file-excel mr-1"></i> <span class="hidden sm:inline">Excel</span>
                    </a>

<?php } ?>
                </div>
            </div>
        </header>

        <!-- Middle Body Section (Schedule Grid) -->
        <div id="schedule-grid" class="flex-1 overflow-auto p-3 md:p-6">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="schedule-grid-wrapper">
                    <div class="schedule-grid-content">
                <!-- Table Header -->
                <div class="grid grid-cols-8 border-b border-gray-200">
                    <div class="px-4 py-3 font-medium text-gray-500 text-sm bg-gray-50 border-r border-gray-200">Area</div>
                    <?php
                    // Ensure $sdate is set before using it
                    if (!isset($sdate)) {
                        $cdate = date('Y-m-d');
                        $timestamp = strtotime($cdate);
                        $dayOfWeek = date("N", $timestamp);
                        $daysToMonday = $dayOfWeek - 1;
                        $sdate = date("Y-m-d", strtotime("-$daysToMonday days", $timestamp));
                    }
                    
                    $currentMonday = date('Y-m-d', strtotime($sdate));
                    $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                    
                    foreach ($days as $day) { ?>
                        <div class="px-4 py-3 font-medium text-gray-800 text-sm text-center">
                            <div><?php echo htmlspecialchars($day); ?></div>
                            <div class="text-gray-500 text-xs"><?php echo htmlspecialchars(date('d/m', strtotime($currentMonday))); ?></div>
                        </div>
                        <?php $currentMonday = date('Y-m-d', strtotime('+1 day', strtotime($currentMonday))); ?>
                    <?php } ?>
                </div>

                <!-- Dynamic Areas -->
                <?php if (isset($prepAreas) && !empty($prepAreas)) { ?>
                    <?php foreach ($prepAreas as $prepArea) { ?>
                        <div id="area-<?php echo isset($prepArea['id']) ? htmlspecialchars($prepArea['id']) : ''; ?>">
                            <div class="grid grid-cols-8 border-b border-gray-200">
                                <div class="px-4 py-3 font-medium text-gray-800 bg-gray-50 border-r border-gray-200 flex items-center justify-between">
                                    <span><?php echo isset($prepArea['prep_name']) ? htmlspecialchars($prepArea['prep_name']) : ''; ?></span>
                                    <button class="text-gray-500 hover:text-gray-700" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#prep_<?php echo isset($prepArea['id']) ? htmlspecialchars($prepArea['id']) : ''; ?>">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </button>
                                </div>
                                <?php
                                $currentMonday = date('Y-m-d', strtotime($sdate));
                                foreach ($days as $day) {
                                    $dayName = strtolower($day);
                                    $dateNumber = date('d', strtotime($currentMonday));
                                    $shiftBoxName = $dateNumber . '_' . (isset($prepArea['id']) ? $prepArea['id'] : '');
                                ?>
                                    <div class="p-2 drop-area-container <?php echo $day !== 'Sunday' ? 'border-r border-gray-200' : ''; ?>">
                                        <div class="allocatedEmpShift_<?php echo htmlspecialchars($shiftBoxName); ?> dragEmployeeBox min-h-[60px] relative"></div>
                                        <div class="add-shift-container">
                                            <button class="addShiftForPrep w-full py-1 text-xs text-gray-500 hover:bg-gray-50 rounded border border-dashed border-gray-300" 
                                                    data-shiftBoxName="<?php echo htmlspecialchars($shiftBoxName); ?>" 
                                                    data-date="<?php echo htmlspecialchars(date('d-m-Y', strtotime($currentMonday))); ?>" 
                                                    data-prepArea="<?php echo isset($prepArea['prep_name']) ? htmlspecialchars($prepArea['prep_name']) : ''; ?>" 
                                                    data-prepAreaId="<?php echo isset($prepArea['id']) ? htmlspecialchars($prepArea['id']) : ''; ?>">
                                                <i class="fa-solid fa-plus mr-1"></i> Add shift
                                            </button>
                                            <button class="pasteShift w-full py-1 text-xs text-green-600 hover:bg-green-50 rounded border border-dashed border-green-300 mt-1" 
                                                    data-shiftBoxName="<?php echo htmlspecialchars($shiftBoxName); ?>" 
                                                    data-date="<?php echo htmlspecialchars(date('d-m-Y', strtotime($currentMonday))); ?>" 
                                                    data-prepArea="<?php echo isset($prepArea['prep_name']) ? htmlspecialchars($prepArea['prep_name']) : ''; ?>" 
                                                    data-prepAreaId="<?php echo isset($prepArea['id']) ? htmlspecialchars($prepArea['id']) : ''; ?>" 
                                                    style="display: none;">
                                                <i class="fa-solid fa-paste mr-1"></i> Paste shift
                                            </button>
                                        </div>
                                    </div>
                                    <?php $currentMonday = date('Y-m-d', strtotime('+1 day', strtotime($currentMonday))); ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Add/Edit Shift Modal -->
    <div class="modal fade" id="addShift-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-gray-100">
                    <h5 class="modal-title text-black" id="modal-title">Create Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body p-4">
                    <form class="needs-validation" name="shift-form" id="form-shift" novalidate>
                        <input type="hidden" id="localStorageKey">
                        <div class="text-end">
                            <a href="#" class="btn btn-sm btn-soft-primary d-none" id="edit-shift-btn" data-id="edit-shift" onclick="editshift(this)" role="button">Edit</a>
                        </div>
                        <div class="shift-details d-block">
                            <div class="d-flex mb-2 gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fa-regular fa-calendar text-black text-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="d-block fw-semibold mb-0 text-black" id="shift-start-date-tag"></h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fa-solid fa-map-pin text-black text-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="d-block fw-semibold mb-0 text-black">
                                            <span id="shift-location-tag" class="text-black"></span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row shift-form">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="hidden" id="empPositionId">
                                    <label class="form-label text-sm font-medium">Employees</label>
                                    <select class="form-select w-full border border-gray-300 rounded-lg p-2 text-sm" name="empName-shift" id="empName-shift">
                                        <option value="">Select an employee...</option>
                                        <?php if (isset($empLists) && !empty($empLists)) { ?>
                                            <?php foreach ($empLists as $empList) { ?>
                                                <option value="<?php echo $empList['emp_id']; ?>"><?php echo $empList['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12" id="shift-time">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-medium">Start Time</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control timeInput empShiftStartTime border-gray-300 rounded-lg p-2 text-sm" placeholder="Select time">
                                                <small class="text-xs text-gray-500">choose or manually enter time in 12 hrs format (e.g., 9:00 AM)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-medium">End Time</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control timeInput empShiftEndTime border-gray-300 rounded-lg p-2 text-sm" placeholder="Select time">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 addBreakText icon-demo-content">
                                <div class="mb-3">
                                    <i class="fa-solid fa-mug-hot text-green-500 text-lg mt-2"></i>
                                    <span class="text-sm text-black font-semibold"> Add Break </span>
                                </div>
                            </div>
                            <div class="col-12 addBreakTimes d-none" id="breakTime">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-medium">Break Type</label>
                                            <select class="form-select border-gray-300 rounded-lg p-2 text-sm" name="breakType">
                                                <option value="unpaid" selected>Unpaid Break</option>
                                                <option value="paid">Paid Break</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-medium">Break</label>
                                            <select class="form-select border-gray-300 rounded-lg p-2 text-sm" name="breakDuration">
                                                <option value="15">15 Mins</option>
                                                <option value="30">30 Mins</option>
                                                <option value="45">45 Mins</option>
                                                <option value="60">60 Mins</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-medium">Break Time</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control timeInput empBreakTime border-gray-300 rounded-lg p-2 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2 mt-4">
                                        <i class="fa-solid fa-trash text-red-500 text-lg deleteBreak cursor-pointer"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label text-sm font-medium">Shift Tasks</label>
                                    <textarea class="form-control border-gray-300 rounded-lg p-2 text-sm" placeholder="Enter shift task" name="taskDescr" id="shift-note"></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="shiftBoxName" name="shiftBoxName" value="" />
                        </div>
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-soft-danger px-3 py-1.5 text-sm" id="btn-delete-shift" data-bs-dismiss="modal">
                                <i class="fa-solid fa-times align-bottom mr-1"></i> Close
                            </button>
                            <button type="button" class="btn btn-success btnAddShift px-3 py-1.5 text-sm" onclick="addEmpShift()">
                                Add Shift
                            </button>
                            <button type="button" class="btn btn-success btnUpdateShift px-3 py-1.5 text-sm d-none" onclick="updateEmpShift()">
                                Update Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recreate Roster Modal -->
    <div class="modal fade" id="recreateRosterModal" tabindex="-1" aria-labelledby="recreateRoster" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recreateRoster">Select date for roster</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo base_url('/HR/recreateRoster'); ?>" method="post" id="recreateRosterForm">
                    <div class="modal-body">
                        <input type="hidden" name="roster_id" class="recreate_roster_id">
                        <div class="mb-3">
                            <label for="startDate" class="col-form-label">Roster Start Date:</label>
                            <input type="text" name="start_date" id="startdatepicker" class="form-control flatpickr-input border-gray-300 rounded-lg p-2 text-sm" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="col-form-label">Roster End Date:</label>
                            <input type="text" name="end_date" id="enddatepicker" class="form-control flatpickr-input border-gray-300 rounded-lg p-2 text-sm" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light px-3 py-1.5 text-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success px-3 py-1.5 text-sm">Recreate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global variable to store copied shift data
        var copiedShiftData = null;
        
        // Mobile Sidebar Toggle
        $(document).ready(function() {
            $('#toggle-sidebar').on('click', function() {
                $('#employee-sidebar').addClass('show');
                $('#mobile-overlay').addClass('show');
            });
            
            $('#close-sidebar, #mobile-overlay').on('click', function() {
                $('#employee-sidebar').removeClass('show');
                $('#mobile-overlay').removeClass('show');
            });
        });
        
        document.addEventListener("DOMContentLoaded", function () {
            var addshift = new bootstrap.Modal(document.getElementById('addShift-modal'), {
                keyboard: false
            });

            // Open modal when clicking on "Add Shift" area
            $(".addShiftForPrep").on('click', function() {
                let shiftDate = $(this).data('date');
                let prepAreaName = $(this).data('preparea');
                let prepAreaId = $(this).data('prepareaid');
                let shiftBoxName = $(this).data('shiftboxname');
                $("#shift-start-date-tag").html(shiftDate);
                $("#shiftBoxName").val(shiftBoxName);
                $("#shift-location-tag").html(prepAreaName);
                $(".btnUpdateShift").addClass('d-none');
                $(".btnAddShift").removeClass('d-none');
                
                // Clear and reinitialize employee dropdown
                $('#empName-shift').val('').trigger('change');
                $("#empName-shift").prop('disabled', false);
                
                // Initialize Select2 if not already done
                if (!$('#empName-shift').hasClass('select2-hidden-accessible')) {
                    $('#empName-shift').select2({
                        placeholder: 'Search and select employee',
                        allowClear: true,
                        dropdownParent: $('#addShift-modal'),
                        width: '100%'
                    });
                }
                
                addshift.show();
            });

            // Toggle break time section
            $(".addBreakText").on('click', function() {
                $(".addBreakTimes").removeClass('d-none');
                $(".addBreakText").addClass('d-none');
            });

            $(".deleteBreak").on('click', function() {
                $(this).parents('.addBreakTimes').addClass('d-none');
                $(".addBreakText").removeClass('d-none');
                $(".empBreakTime").val('');
            });

            // Initialize time picker
            $('.timeInput').datetimepicker({
                format: 'hh:mm A',
                icons: {
                    up: 'fa-solid fa-chevron-up',
                    down: 'fa-solid fa-chevron-down',
                },
                useCurrent: false
            });

            // Filter employees in the left panel
            $('.filterEmployeeLeftPanel').on('input', function() {
                let inputText = $(this).val().trim().toLowerCase();
                $('.employee-div').hide().filter(function() {
                    return $(this).data('employee-name').toLowerCase().includes(inputText);
                }).show();
            });

            // Load existing roster data into localStorage
            var allDayRosterData = <?php echo json_encode($allDayRosterData ?? [], JSON_UNESCAPED_SLASHES); ?>;
            
            // DEBUG MARKER v2 - if you see this, PHP cache is cleared
            console.log('=== DEBUG MARKER v2 - CODE UPDATED ===');
            console.log('=== CODE VERSION: <?php echo $codeVersion ?? "NOT_SET"; ?> ===');
            
            // DEBUG: Show server-side processing info
            var debugInfo = <?php echo json_encode($debugInfo ?? ['error' => 'debugInfo not set'], JSON_UNESCAPED_SLASHES); ?>;
            console.log('DEBUG: Full debugInfo:', debugInfo);
            console.log('DEBUG: Server info - Total from DB:', debugInfo.totalFromDb, '| After processing:', debugInfo.totalAfterProcessing);
            console.log('DEBUG: Key collisions:', debugInfo.keyCollisions);
            console.log('DEBUG: First record fields:', debugInfo.firstRecordFields);
            console.log('DEBUG: First record ID value:', debugInfo.firstRecordId);
            console.log('DEBUG: allDayRosterData has ' + Object.keys(allDayRosterData).length + ' entries');
            console.log('DEBUG: allDayRosterData keys:', Object.keys(allDayRosterData));
            
            for (var key in allDayRosterData) {
                if (allDayRosterData.hasOwnProperty(key)) {
                    localStorage.setItem(key, allDayRosterData[key]);
                }
            }

            // Display roster data from localStorage on page load
            loadRosterFromLocalStorage();

            // Drag and Drop Functionality - removed old implementation as new one is implemented below
            // $('.dragSourceElement').draggable({ ... }) - this is handled by the comprehensive drag and drop section below
            // $('.addShiftForPrep').droppable({ ... }) - this is replaced by .dragEmployeeBox droppable below

            // Edit shift by clicking on the shift box
            $(document).on('click', '.dragEmployeeBox div[id^="emp_"]', function() {
                let employeeIdPrepId = $(this).attr('id');
                let formDataS = localStorage.getItem(employeeIdPrepId);
                let formData = JSON.parse(formDataS);

                $("#empName-shift").val(formData.employeeId);
                $("#empName-shift").prop('disabled', true);
                $(".empShiftStartTime").val(formData.empShiftStartTime);
                $(".empShiftEndTime").val(formData.empShiftEndTime);
                $(".empBreakTime").val(formData.empBreakTime);
                $('[name="breakType"]').val(formData.breakType);
                $('[name="breakDuration"]').val(formData.breakDuration);
                $("#shift-note").val(formData.taskDescr);
                $("#localStorageKey").val(employeeIdPrepId);
                $("#shift-start-date-tag").html(formData.rosterDate);
                $("#shiftBoxName").val(employeeIdPrepId.split('_')[1] + '_' + employeeIdPrepId.split('_')[2]);
                $("#shift-location-tag").html($(this).closest('.addShiftForPrep').data('preparea'));

                $(".btnUpdateShift").removeClass('d-none');
                $(".btnAddShift").addClass('d-none');
                addshift.show();
            });
        });

        function loadRosterFromLocalStorage() {
            for (var key in localStorage) {
                if (localStorage.hasOwnProperty(key) && key.startsWith("emp_")) {
                    let formDataS = localStorage.getItem(key);
                    let formData = JSON.parse(formDataS);

                    formData.empShiftStartTime = convertTo12HourFormat(formData.empShiftStartTime);
                    formData.empShiftEndTime = convertTo12HourFormat(formData.empShiftEndTime);
                    formData.empBreakTime = formData.empBreakTime ? convertTo12HourFormat(formData.empBreakTime) : '';

                    localStorage.setItem(key, JSON.stringify(formData));

                    let shiftBoxName = key.split('_')[1] + '_' + key.split('_')[2];
                    let shiftHtml = '';
                    shiftHtml += '<div class="bg-shift-green p-2 rounded-lg border border-shift-border mb-2" id="' + key + '">';
                    shiftHtml += '<div class="flex justify-between items-start">';
                    shiftHtml += '<span class="font-medium text-sm">' + formData.selectedEmpName + '</span>';
                    shiftHtml += '<div class="flex gap-1">';
                    shiftHtml += '<button class="text-blue-500 hover:text-blue-700 text-sm" onclick="copyShift(\'' + key + '\', event)" title="Copy Shift">';
                    shiftHtml += '<i class="fa-solid fa-copy text-sm"></i>';
                    shiftHtml += '</button>';
                    shiftHtml += '<button class="text-gray-400 hover:text-red-500 text-sm" onclick="clearStorage(\'' + key + '\', event, this)" title="Delete Shift">';
                    shiftHtml += '<i class="fa-solid fa-times text-sm"></i>';
                    shiftHtml += '</button>';
                    shiftHtml += '</div>';
                    shiftHtml += '</div>';
                    shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
                    shiftHtml += '<i class="fa-regular fa-clock mr-1"></i>' + formData.empShiftStartTime + ' - ' + formData.empShiftEndTime;
                    shiftHtml += '</div>';
                    if (formData.empBreakTime && formData.empBreakTime !== '') {
                        shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
                        shiftHtml += '<i class="fa-solid fa-mug-hot mr-1"></i> Break: ' + formData.empBreakTime;
                        shiftHtml += '</div>';
                    }
                    shiftHtml += '</div>';

                    let boxName = ".allocatedEmpShift_" + shiftBoxName;
                    $(boxName).append(shiftHtml);
                }
            }
        }

        function convertTo12HourFormat(time) {
            if (!time || !time.match(/^\d{2}:\d{2}:\d{2}$/)) {
                return time;
            }

            let [hours, minutes] = time.split(':');
            hours = parseInt(hours, 10);
            let period = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${hours}:${minutes} ${period}`;
        }

        function addEmpShift() {
            let shiftBoxName = $("#shiftBoxName").val();
            let empId = $("#empName-shift").val();
            let rosterDate = $("#shift-start-date-tag").text().trim();
            
            // Validate required fields
            if (!empId || !shiftBoxName) {
                alert('Please select an employee and ensure shift details are complete.');
                return;
            }
            
            let formData = {
                employeeId: empId,
                position_id: $("#empPositionId").val(),
                selectedEmpName: $("#empName-shift option:selected").text(),
                empShiftStartTime: $(".empShiftStartTime").val(),
                empShiftEndTime: $(".empShiftEndTime").val(),
                empBreakTime: $(".empBreakTime").val(),
                breakType: $('[name="breakType"]').val(),
                breakDuration: $('[name="breakDuration"]').val(),
                taskDescr: $("#shift-note").val(),
                rosterDate: rosterDate
            };

            let formDataS = JSON.stringify(formData);
            // Add timestamp to make key unique for multiple shifts of same employee on same day/prep area
            let uniqueId = Date.now();
            let keyForStorage = 'emp_' + shiftBoxName + '_' + formData.employeeId + '_' + uniqueId;
            
            saveInLocalStorage(keyForStorage, formDataS);

            let shiftHtml = '';
            shiftHtml += '<div class="bg-shift-green p-2 rounded-lg border border-shift-border mb-2" id="' + keyForStorage + '">';
            shiftHtml += '<div class="flex justify-between items-start">';
            shiftHtml += '<span class="font-medium text-sm">' + formData.selectedEmpName + '</span>';
            shiftHtml += '<div class="flex gap-1">';
            shiftHtml += '<button class="text-blue-500 hover:text-blue-700 text-sm" onclick="copyShift(\'' + keyForStorage + '\', event)" title="Copy Shift">';
            shiftHtml += '<i class="fa-solid fa-copy text-sm"></i>';
            shiftHtml += '</button>';
            shiftHtml += '<button class="text-gray-400 hover:text-red-500 text-sm" onclick="clearStorage(\'' + keyForStorage + '\', event, this)" title="Delete Shift">';
            shiftHtml += '<i class="fa-solid fa-times text-sm"></i>';
            shiftHtml += '</button>';
            shiftHtml += '</div>';
            shiftHtml += '</div>';
            shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
            shiftHtml += '<i class="fa-regular fa-clock mr-1"></i>' + formData.empShiftStartTime + ' - ' + formData.empShiftEndTime;
            shiftHtml += '</div>';
            if (formData.empBreakTime && formData.empBreakTime !== '') {
                shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
                shiftHtml += '<i class="fa-solid fa-mug-hot mr-1"></i> Break: ' + formData.empBreakTime;
                shiftHtml += '</div>';
            }
            shiftHtml += '</div>';

            let boxName = ".allocatedEmpShift_" + shiftBoxName;
            $(boxName).append(shiftHtml);
            $("#addShift-modal").modal('hide');
            $("#form-shift")[0].reset();
            $(".addBreakTimes").addClass('d-none');
            $(".addBreakText").removeClass('d-none');
        }

        function updateEmpShift() {
            let storageKey = $("#localStorageKey").val();
            let formData = {
                employeeId: $("#empName-shift").val(),
                position_id: $("#empPositionId").val(),
                selectedEmpName: $("#empName-shift option:selected").text(),
                empShiftStartTime: $(".empShiftStartTime").val(),
                empShiftEndTime: $(".empShiftEndTime").val(),
                empBreakTime: $(".empBreakTime").val(),
                breakType: $('[name="breakType"]').val(),
                breakDuration: $('[name="breakDuration"]').val(),
                taskDescr: $("#shift-note").val(),
                rosterDate: $("#shift-start-date-tag").text()
            };

            localStorage.setItem(storageKey, JSON.stringify(formData));

            let shiftHtml = '';
            shiftHtml += '<div class="bg-shift-green p-2 rounded-lg border border-shift-border mb-2" id="' + storageKey + '">';
            shiftHtml += '<div class="flex justify-between items-start">';
            shiftHtml += '<span class="font-medium text-sm">' + formData.selectedEmpName + '</span>';
            shiftHtml += '<div class="flex gap-1">';
            shiftHtml += '<button class="text-blue-500 hover:text-blue-700 text-sm" onclick="copyShift(\'' + storageKey + '\', event)" title="Copy Shift">';
            shiftHtml += '<i class="fa-solid fa-copy text-sm"></i>';
            shiftHtml += '</button>';
            shiftHtml += '<button class="text-gray-400 hover:text-red-500 text-sm" onclick="clearStorage(\'' + storageKey + '\', event, this)" title="Delete Shift">';
            shiftHtml += '<i class="fa-solid fa-times text-sm"></i>';
            shiftHtml += '</button>';
            shiftHtml += '</div>';
            shiftHtml += '</div>';
            shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
            shiftHtml += '<i class="fa-regular fa-clock mr-1"></i>' + formData.empShiftStartTime + ' - ' + formData.empShiftEndTime;
            shiftHtml += '</div>';
            if (formData.empBreakTime && formData.empBreakTime !== '') {
                shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
                shiftHtml += '<i class="fa-solid fa-mug-hot mr-1"></i> Break: ' + formData.empBreakTime;
                shiftHtml += '</div>';
            }
            shiftHtml += '</div>';

            $("#" + storageKey).replaceWith(shiftHtml);
            $("#addShift-modal").modal('hide');
            $("#form-shift")[0].reset();
            $(".addBreakTimes").addClass('d-none');
            $(".addBreakText").removeClass('d-none');
        }

        function saveInLocalStorage(key, value) {
            localStorage.setItem(key, value);
        }

        function clearStorage(keyStorage, event, clickedElement) {
            // Find the entire shift container by ID and remove it completely
            $('#' + keyStorage).remove();
            localStorage.removeItem(keyStorage);
            
            // Also clean any corrupted versions of this key
            for (var key in localStorage) {
                if (localStorage.hasOwnProperty(key) && key.includes(keyStorage) && key !== keyStorage) {
                    localStorage.removeItem(key);
                }
            }
            
            event.stopPropagation();
        }

        function publishRoster(savetype = 'save') {
            var empData = {};
            $('#loader-overlay').show();
            
            // Properly filter and collect localStorage data
            for (var key in localStorage) {
                if (localStorage.hasOwnProperty(key) && key.startsWith("emp_")) {
                    // Clean the key - only keep keys that match the expected pattern
                    var cleanKey = key;
                    
                    // Check if key matches expected pattern: emp_DAY_PREPAREA_EMPLOYEEID or with optional STARTTIME and/or DBID
                    // e.g., emp_29_1_52 or emp_29_1_52_090000 or emp_29_1_52_090000_123
                    var keyPattern = /^emp_\d+_\d+_\d+(_\d+)?(_\d+)?$/;
                    
                    if (keyPattern.test(key)) {
                        // Key is already clean, use as-is
                        empData[key] = localStorage.getItem(key);
                    } else if (key.includes('_') && key.startsWith('emp_')) {
                        // Try to extract clean key from corrupted key
                        var keyParts = key.split(' ');
                        var potentialKey = keyParts[0]; // Take the first part before any space
                        
                        // If first part doesn't match, try to reconstruct
                        if (!keyPattern.test(potentialKey)) {
                            // Try to find the employee ID at the end
                            var lastUnderscore = key.lastIndexOf('_');
                            if (lastUnderscore > 0) {
                                var empId = key.substring(lastUnderscore + 1);
                                var beforeEmpId = key.substring(0, lastUnderscore);
                                
                                // Remove any non-digit, non-underscore characters from before empId
                                var cleanBeforeEmpId = beforeEmpId.replace(/[^emp_\d]/g, '');
                                potentialKey = cleanBeforeEmpId + '_' + empId;
                            }
                        }
                        
                        // Validate and use the reconstructed key
                        if (keyPattern.test(potentialKey)) {
                            empData[potentialKey] = localStorage.getItem(key);
                        }
                    }
                }
            }
            
            empData.week = $('.currentWeek').text();
            empData.rosterName = $('#rosterName').val();
            empData.savetype = savetype;

            if (Object.keys(empData).length > 3) { // More than just week, rosterName, savetype
                $.ajax({
                    type: "POST",
                    url: "/HR/roster/addRoster",
                    data: empData,
                    success: function(response) {
                        var res = JSON.parse(response);
                        $('#loader-overlay').hide();
                        if (res.status === 'success') {
                            alert(res.message);
                            if (savetype === 'publish') {
                                for (var key in localStorage) {
                                    if (localStorage.hasOwnProperty(key) && key.startsWith("emp_")) {
                                        localStorage.removeItem(key);
                                    }
                                }
                                window.location.href = '/HR/roster';
                            }
                        } else {
                            $('#loader-overlay').hide();
                            alert('Error: ' + (res.message || 'Unknown error'));
                            
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error posting data:", error);
                        $('#loader-overlay').hide();
                        alert('Failed to save roster. Please try again.');
                    }
                });
            } else {
                $('#loader-overlay').hide();
                alert("No shift data to save.");
            }
        }

        function showRosterRecreateModal(roster_id) {
            $(".recreate_roster_id").val(roster_id);
            $("#recreateRosterModal").modal("show");
        }

        function formatDate(date) {
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('en-GB', options).replace(/ /g, ' ');
        }
        
         function toggleDatePicker() {
            document.getElementById('datePickerModal').style.display = 'block';
        }
        
        function closeDatePicker() {
            document.getElementById('datePickerModal').style.display = 'none';
        }
        
        function closeDatePickerOnOutsideClick(event) {
            if (event.target === document.getElementById('datePickerModal')) {
                closeDatePicker();
            }
        }
        
        function applyDateRange() {
            const startDate = document.getElementById('startDatePicker').value;
            const endDate = document.getElementById('endDatePicker').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date');
                return;
            }
            
            // Clear localStorage
            for (var key in localStorage) {
                if (localStorage.hasOwnProperty(key) && key.startsWith("emp_")) {
                    localStorage.removeItem(key);
                }
            }
            
            // Update display
            const start = new Date(startDate);
            const end = new Date(endDate);
            const buttonText = formatDate(start) + ' - ' + formatDate(end);
            
            // Redirect with new date range
            const encodedButtonText = encodeURIComponent(buttonText);
            const encodedStartDate = encodeURIComponent(startDate);
            const encodedEndDate = encodeURIComponent(endDate);
            
            window.location.href = '/HR/roster/fetchRosterByWeek?weekRange=' + encodedButtonText + '&rosterStartDate=' + encodedStartDate + '&rosterEndDate=' + encodedEndDate;
        }

        function getCurrentWeekStartDate() {
            let rosterStartDate = '<?php echo isset($rosterStartDate) ? $rosterStartDate : ''; ?>';
            let today = rosterStartDate ? new Date(rosterStartDate) : new Date();
            const currentDay = today.getDay();
            const monday = new Date(today);
            monday.setDate(today.getDate() - currentDay + (currentDay === 0 ? -6 : 1));
            return monday;
        }

        var currentWeekStartDate = getCurrentWeekStartDate();

        function updateCurrentWeekText(fetchRosterData) {
            const endDate = new Date(currentWeekStartDate);
            endDate.setDate(currentWeekStartDate.getDate() + 6);
            const buttonText = formatDate(currentWeekStartDate) + ' - ' + formatDate(endDate);
            const encodedButtonText = encodeURIComponent(buttonText);
            const encodedStartDate = encodeURIComponent(currentWeekStartDate.toISOString().split('T')[0]);
            const encodedEndDate = encodeURIComponent(endDate.toISOString().split('T')[0]);

            $('.currentWeek').text(buttonText);

            if (fetchRosterData) {
                window.location.href = '/HR/roster/fetchRosterByWeek?weekRange=' + encodedButtonText + '&rosterStartDate=' + encodedStartDate + '&rosterEndDate=' + encodedEndDate;
            }
        }

        function updatePrevWeekText() {
            currentWeekStartDate.setDate(currentWeekStartDate.getDate() - 7);
             currentWeekEndDate.setDate(currentWeekEndDate.getDate() - 7);
            updateCurrentWeekText(true);
        }

        function updateNextWeekText() {
            currentWeekStartDate.setDate(currentWeekStartDate.getDate() + 7);
            currentWeekEndDate.setDate(currentWeekEndDate.getDate() + 7);
            updateCurrentWeekText(true);
        }

        $('.prevWeek').click(function() {
            for (var key in localStorage) {
                if (localStorage.hasOwnProperty(key) && key.startsWith("emp_")) {
                    localStorage.removeItem(key);
                }
            }
            updatePrevWeekText();
        });

        $('.nextWeek').click(function() {
            for (var key in localStorage) {
                if (localStorage.hasOwnProperty(key) && key.startsWith("emp_")) {
                    localStorage.removeItem(key);
                }
            }
            updateNextWeekText();
        });

        $(".weekAreaAndTeam").on('change', function() {
            let rosterId = '<?php echo isset($rosterId) ? $rosterId : 0; ?>';
            if (!rosterId || rosterId.trim() === '' || isNaN(parseInt(rosterId))) {
                rosterId = 0;
            }
            rosterId = parseInt(rosterId);

            if ($(this).val() == '2') {
                window.location.href = '/HR/roster/rosterViewWTM/' + rosterId;
            } else {
                window.location.href = '/HR/roster/rosterView/' + rosterId;
            }
        });

        $(document).ready(function() {
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 4000);

            flatpickr("#startdatepicker", {
                dateFormat: "d M, Y",
                disable: [
                    function(date) {
                        return (date.getDay() !== 1);
                    }
                ]
            });

            flatpickr("#enddatepicker", {
                dateFormat: "d M, Y",
                disable: [
                    function(date) {
                        return (date.getDay() !== 0);
                    }
                ]
            });

            $('#recreateRosterForm').on('submit', function() {
                $('#loaderContainer').show();
            });
        });
        
        // Drag and Drop Functionality
        $(document).ready(function() {
            <?php if(isset($roleId) && $roleId == 4) { ?>
            // Disable drag and drop for employee role
            return;
            <?php } ?>
            // Make employee divs draggable
            $(".employee-div").draggable({
                helper: function() {
                    // Get the correct employee data from the current dragged element
                    var employeeName = $(this).find('.empName').val();
                    var employeeId = $(this).find('.empId').val();
                    var positionId = $(this).find('.position_id').val();
                    
                    // Create helper element with correct employee info
                    var helper = $('<div class="drag-helper bg-primary text-white p-2 rounded shadow-lg"></div>');
                    helper.text(employeeName);
                    helper.data('employeeId', employeeId);
                    helper.data('employeeName', employeeName);
                    helper.data('positionId', positionId);
                    return helper;
                },
                appendTo: "body",
                zIndex: 9999,
                revert: "invalid",
                cursor: "move",
                cursorAt: { left: 10, top: 10 },
                start: function(event, ui) {
                    // Store the dragged employee data
                    var employeeData = {
                        id: $(this).find('.empId').val(),
                        name: $(this).find('.empName').val(),
                        positionId: $(this).find('.position_id').val()
                    };
                    $(this).data('draggedEmployee', employeeData);
                    
                    // Add visual feedback
                    $(this).addClass('dragging');
                    $('.drop-area-container').addClass('drop-zone-active');
                },
                stop: function(event, ui) {
                    // Remove visual feedback
                    $(this).removeClass('dragging');
                    $('.drop-area-container').removeClass('drop-zone-active');
                }
            });
            
            // Add touch event support for mobile drag and drop
            function initTouchEvents() {
                $('.employee-div').each(function() {
                    var element = this;
                    var startX, startY;
                    var isDragging = false;
                    var clone;
                    
                    // Remove existing listeners
                    element.removeEventListener('touchstart', element._touchStartHandler);
                    element.removeEventListener('touchmove', element._touchMoveHandler);
                    element.removeEventListener('touchend', element._touchEndHandler);
                    
                    element._touchStartHandler = function(e) {
                        var touch = e.touches[0];
                        startX = touch.clientX;
                        startY = touch.clientY;
                        isDragging = false;
                    };
                    
                    element._touchMoveHandler = function(e) {
                        if (!isDragging) {
                            var touch = e.touches[0];
                            var diffX = Math.abs(touch.clientX - startX);
                            var diffY = Math.abs(touch.clientY - startY);
                            
                            if (diffX > 10 || diffY > 10) {
                                isDragging = true;
                                
                                // Create visual clone
                                clone = $(element).clone();
                                clone.css({
                                    position: 'fixed',
                                    zIndex: 9999,
                                    opacity: 0.7,
                                    pointerEvents: 'none',
                                    width: $(element).width() + 'px'
                                });
                                $('body').append(clone);
                                
                                // Add dragging class
                                $(element).addClass('dragging');
                            }
                        }
                        
                        if (isDragging && clone) {
                            e.preventDefault();
                            var touch = e.touches[0];
                            clone.css({
                                left: touch.clientX - (clone.width() / 2) + 'px',
                                top: touch.clientY - 20 + 'px'
                            });
                            
                            // Highlight drop zones
                            $('.dragEmployeeBox').removeClass('drop-zone-hover');
                            var elementAtPoint = document.elementFromPoint(touch.clientX, touch.clientY);
                            var dropZone = $(elementAtPoint).closest('.dragEmployeeBox');
                            if (dropZone.length) {
                                dropZone.addClass('drop-zone-hover');
                            }
                        }
                    };
                    
                    element._touchEndHandler = function(e) {
                        if (isDragging && clone) {
                            var touch = e.changedTouches[0];
                            var elementAtPoint = document.elementFromPoint(touch.clientX, touch.clientY);
                            var dropZone = $(elementAtPoint).closest('.dragEmployeeBox');
                            
                            if (dropZone.length) {
                                // Trigger the drop logic
                                var empId = $(element).find('.empId').val();
                                var empName = $(element).find('.empName').val();
                                var positionId = $(element).find('.position_id').val();
                                var shiftBoxName = dropZone.data('shiftboxname');
                                var shiftDate = dropZone.data('date');
                                
                                // Open the modal with pre-filled data
                                $("#shift-start-date-tag").html(shiftDate);
                                $("#shiftBoxName").val(shiftBoxName);
                                $("#shift-location-tag").html(dropZone.data('preparea'));
                                $("#empName-shift").val(empId).trigger('change');
                                $("#empName-shift").prop('disabled', true);
                                $(".btnUpdateShift").addClass('d-none');
                                $(".btnAddShift").removeClass('d-none');
                                
                                var addshift = new bootstrap.Modal(document.getElementById('addShift-modal'));
                                addshift.show();
                            }
                            
                            // Cleanup
                            clone.remove();
                            $(element).removeClass('dragging');
                            $('.dragEmployeeBox').removeClass('drop-zone-hover');
                        }
                        isDragging = false;
                    };
                    
                    element.addEventListener('touchstart', element._touchStartHandler, {passive: true});
                    element.addEventListener('touchmove', element._touchMoveHandler, {passive: false});
                    element.addEventListener('touchend', element._touchEndHandler, {passive: true});
                });
            }
            
            // Initialize touch events on page load
            initTouchEvents();
            
            // Re-initialize draggable when employees are filtered
            $('.filterEmployeeLeftPanel').on('input', function() {
                setTimeout(function() {
                    // Re-attach draggable to visible employee divs
                    $(".employee-div:visible").draggable("destroy").draggable({
                        helper: function() {
                            var employeeName = $(this).find('.empName').val();
                            var employeeId = $(this).find('.empId').val();
                            var positionId = $(this).find('.position_id').val();
                            
                            var helper = $('<div class="drag-helper bg-primary text-white p-2 rounded shadow-lg"></div>');
                            helper.text(employeeName);
                            helper.data('employeeId', employeeId);
                            helper.data('employeeName', employeeName);
                            helper.data('positionId', positionId);
                            return helper;
                        },
                        appendTo: "body",
                        zIndex: 9999,
                        revert: "invalid",
                        cursor: "move",
                        cursorAt: { left: 10, top: 10 },
                        start: function(event, ui) {
                            var employeeData = {
                                id: $(this).find('.empId').val(),
                                name: $(this).find('.empName').val(),
                                positionId: $(this).find('.position_id').val()
                            };
                            $(this).data('draggedEmployee', employeeData);
                            $(this).addClass('dragging');
                            $('.dragEmployeeBox').addClass('drop-zone-active');
                        },
                        stop: function(event, ui) {
                            $(this).removeClass('dragging');
                            $('.dragEmployeeBox').removeClass('drop-zone-active');
                        }
                    });
                    
                    // Re-initialize touch events for filtered employees
                    initTouchEvents();
                }, 100);
            });
            
            // Make drop zones droppable - target entire container for better UX
            $(".drop-area-container").droppable({
                accept: ".employee-div",
                hoverClass: "drop-zone-hover",
                tolerance: "pointer",
                drop: function(event, ui) {
                    var draggedElement = ui.draggable;
                    var employeeData = draggedElement.data('draggedEmployee');
                    
                    if (!employeeData) {
                        // Fallback: get data from helper or dragged element directly
                        employeeData = {
                            id: ui.helper.data('employeeId') || draggedElement.find('.empId').val(),
                            name: ui.helper.data('employeeName') || draggedElement.find('.empName').val(),
                            positionId: ui.helper.data('positionId') || draggedElement.find('.position_id').val()
                        };
                    }
                    
                    // Find the dragEmployeeBox within this container
                    var dragEmployeeBox = $(this).find('.dragEmployeeBox');
                    var shiftBoxName = dragEmployeeBox.attr('class').match(/allocatedEmpShift_([^\s]+)/);
                    
                    if (shiftBoxName && shiftBoxName[1] && employeeData && employeeData.id) {
                        // Find the add shift button to get the data attributes
                        var addShiftBtn = $(this).find('.addShiftForPrep');
                        var shiftDate = addShiftBtn.data('date');
                        var prepAreaName = addShiftBtn.data('preparea');
                        
                        // Pre-fill the modal with employee and location data
                        $("#shift-start-date-tag").html(shiftDate);
                        $("#shiftBoxName").val(shiftBoxName[1]);
                        $("#shift-location-tag").html(prepAreaName);
                        
                        // Pre-select the employee and set position
                        $("#empName-shift").val(employeeData.id);
                        $("#empPositionId").val(employeeData.positionId);
                        
                        // Clear and reset form for new entry
                        $("#form-shift")[0].reset();
                        $("#empName-shift").val(employeeData.id);
                        $("#empPositionId").val(employeeData.positionId);
                        $("#shift-start-date-tag").html(shiftDate);
                        $("#shiftBoxName").val(shiftBoxName[1]);
                        $("#shift-location-tag").html(prepAreaName);
                        
                        // Set modal buttons correctly
                        $(".btnUpdateShift").addClass('d-none');
                        $(".btnAddShift").removeClass('d-none');
                        
                        // Initialize Select2 if not already done
                        if (!$('#empName-shift').hasClass('select2-hidden-accessible')) {
                            $('#empName-shift').select2({
                                placeholder: 'Search and select employee',
                                allowClear: true,
                                dropdownParent: $('#addShift-modal'),
                                width: '100%'
                            });
                        }
                        
                        // Enable dropdown and set employee after Select2 is ready
                        $("#empName-shift").prop('disabled', false);
                        $('#empName-shift').val(employeeData.id).trigger('change');
                        
                        // Show modal using Bootstrap 5 method
                        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addShift-modal'));
                        modal.show();
                    }
                    
                    // Remove visual feedback
                    $('.drop-area-container').removeClass('drop-zone-active drop-zone-hover');
                }
            });
        });
        
        // Copy and Paste Functions
        function copyShift(shiftKey, event) {
            event.stopPropagation();
            
            let shiftData = localStorage.getItem(shiftKey);
            if (shiftData) {
                copiedShiftData = {
                    originalKey: shiftKey,
                    data: JSON.parse(shiftData)
                };
                
                // Show success message
                showNotification('Shift copied! Click paste on any day to duplicate this shift.', 'success');
                
                // Show all paste buttons
                $('.pasteShift').show();
                
                // Show clear copied button
                $('#clearCopiedBtn').show();
                
                // Add visual indicator that we have copied data
                $('.pasteShift').removeClass('border-gray-300 text-gray-500').addClass('border-green-300 text-green-600');
            }
        }
        
        // Add paste shift event handler
        $(document).on('click', '.pasteShift', function() {
            if (!copiedShiftData) {
                showNotification('No shift data copied. Please copy a shift first.', 'error');
                return;
            }
            
            let shiftBoxName = $(this).data('shiftboxname');
            let shiftDate = $(this).data('date');
            let prepAreaName = $(this).data('preparea');
            
            // Create new shift data with updated location and date info
            let newShiftData = Object.assign({}, copiedShiftData.data);
            newShiftData.rosterDate = shiftDate;
            
            // Generate new storage key with unique timestamp to allow multiple shifts
            let uniqueId = Date.now();
            let newStorageKey = 'emp_' + shiftBoxName + '_' + newShiftData.employeeId + '_' + uniqueId;
            
            // Save to localStorage
            localStorage.setItem(newStorageKey, JSON.stringify(newShiftData));
            
            // Create and display the shift
            let shiftHtml = '';
            shiftHtml += '<div class="bg-shift-green p-2 rounded-lg border border-shift-border mb-2" id="' + newStorageKey + '">';
            shiftHtml += '<div class="flex justify-between items-start">';
            shiftHtml += '<span class="font-medium text-sm">' + newShiftData.selectedEmpName + '</span>';
            shiftHtml += '<div class="flex gap-1">';
            shiftHtml += '<button class="text-blue-500 hover:text-blue-700 text-sm" onclick="copyShift(\'' + newStorageKey + '\', event)" title="Copy Shift">';
            shiftHtml += '<i class="fa-solid fa-copy text-sm"></i>';
            shiftHtml += '</button>';
            shiftHtml += '<button class="text-gray-400 hover:text-red-500 text-sm" onclick="clearStorage(\'' + newStorageKey + '\', event, this)" title="Delete Shift">';
            shiftHtml += '<i class="fa-solid fa-times text-sm"></i>';
            shiftHtml += '</button>';
            shiftHtml += '</div>';
            shiftHtml += '</div>';
            shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
            shiftHtml += '<i class="fa-regular fa-clock mr-1"></i>' + newShiftData.empShiftStartTime + ' - ' + newShiftData.empShiftEndTime;
            shiftHtml += '</div>';
            if (newShiftData.empBreakTime && newShiftData.empBreakTime !== '') {
                shiftHtml += '<div class="text-xs text-gray-600 mt-1">';
                shiftHtml += '<i class="fa-solid fa-mug-hot mr-1"></i> Break: ' + newShiftData.empBreakTime;
                shiftHtml += '</div>';
            }
            shiftHtml += '</div>';
            
            // Add to the correct container
            let targetContainer = '.allocatedEmpShift_' + shiftBoxName;
            $(targetContainer).append(shiftHtml);
            
            showNotification('Shift pasted successfully!', 'success');
        });
        
        // Clear copied data function
        function clearCopiedData() {
            copiedShiftData = null;
            $('.pasteShift').hide();
            $('#clearCopiedBtn').hide();
            $('.pasteShift').removeClass('border-green-300 text-green-600').addClass('border-gray-300 text-gray-500');
            showNotification('Copied shift data cleared.', 'success');
        }
        
        // Show notification function
        function showNotification(message, type) {
            // Create notification element
            let notificationClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-red-100 text-red-800 border-red-300';
            let notification = $('<div class="fixed top-4 right-4 px-4 py-2 rounded-lg border z-50 ' + notificationClass + '">' + message + '</div>');
            
            // Add to body
            $('body').append(notification);
            
            // Auto remove after 3 seconds
            setTimeout(function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
        
        
        document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("loader-overlay").style.display = "none";
        
        // Restrict startDatePicker to Mondays only
        const startDatePicker = document.getElementById('startDatePicker');
        if (startDatePicker) {
            startDatePicker.addEventListener('input', function(e) {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay();
                
                // Check if selected day is Monday (1)
                if (dayOfWeek !== 1) {
                    alert('Please select a Monday for the start date.');
                    this.value = '';
                }
            });
        }
        
        // Restrict endDatePicker to Sundays only
        const endDatePicker = document.getElementById('endDatePicker');
        if (endDatePicker) {
            endDatePicker.addEventListener('input', function(e) {
                const selectedDate = new Date(this.value);
                const dayOfWeek = selectedDate.getDay();
                
                // Check if selected day is Sunday (0)
                if (dayOfWeek !== 0) {
                    alert('Please select a Sunday for the end date.');
                    this.value = '';
                }
            });
        }
    });
    
   
    </script>
    
    <script>
function exportRosterToPDF() {
    var rosterId = <?php echo isset($rosterId) ? (int)$rosterId : 0; ?>;

    if (!rosterId) {
        alert('Roster ID not found. Please save the roster first.');
        return;
    }

    window.location.href =
        "<?php echo base_url('HR/roster/exportRosterPDF'); ?>?roster_id=" + rosterId;
}
</script>

<?php $this->load->view('components/scroll_to_top'); ?>
</body>
</html>