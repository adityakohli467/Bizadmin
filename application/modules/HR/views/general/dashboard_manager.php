<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <style>
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .table-responsive table {
                min-width: 800px;
            }
        }
    </style>
    <style>
    h3, thead th{
        color:#1f2937 !important;
    }
    </style>
</head>
<body class="bg-[#F4F6F9] font-inter">



<!-- Main Dashboard Content -->
<main class="max-w-[1920px] mx-auto px-3 md:px-6 py-4 md:py-8 mt-5">
     
    <!-- Quick Glance Cards Row -->
    <section id="quick-glance-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
        
        <!-- Today's Birthdays -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition cursor-pointer">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-cake-candles text-2xl md:text-3xl text-purple-500"></i>
            </div>
           <div class="text-2xl md:text-3xl font-bold text-gray-800"> <?= count($birthdays_today) ?></div>
    

            <div class="text-xs text-gray-500 mt-1">Today's Birthdays</div>
        </div>

        <!-- Tasks Completed -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition cursor-pointer">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-circle-check text-2xl md:text-3xl text-green-500"></i>
            </div>
            <div class="text-2xl md:text-3xl font-bold text-gray-800"><?= $task_summary['completed_today'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Tasks Completed</div>
        </div>

     

        <!-- Pending Timesheets -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition cursor-pointer">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-clock text-2xl md:text-3xl text-orange-500"></i>
            </div>
            <div class="text-2xl md:text-3xl font-bold text-gray-800"><?= $employee_on_break_count ?> </div>
            <div class="text-xs text-gray-500 mt-1">Employee on break</div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition cursor-pointer border-2 border-red-500">
            <div class="flex items-center justify-between mb-2">
                <i class="fa-solid fa-file-circle-exclamation text-2xl md:text-3xl text-red-500"></i>
            </div>
            <div class="text-2xl md:text-3xl font-bold text-red-600"><?= count($pending_leaves) ?></div>
            <div class="text-xs text-gray-500 mt-1">Leaves Requests</div>
        </div>

       

       

    </section>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6">
        
        <!-- Left Column -->
        <div id="left-column" class="lg:col-span-3 space-y-4 md:space-y-6">
            
            <!-- Team Overview Card -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-center mb-4 md:mb-6">
                    <div class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-teal to-blue-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-users text-white text-3xl md:text-4xl"></i>
                    </div>
                </div>
                <h3 class="text-base md:text-lg font-bold text-gray-800 text-center mb-4">Cafe Staff Today's Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Members</span>
                        <span class="font-bold text-gray-800"><?= $total_employees ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Present Today</span>
                        <span class="font-bold text-green-600"><?= $present_today ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">On Leave</span>
                        <span class="font-bold text-orange-600"><?= count($pending_leaves) ?></span>
                    </div>
                    
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
    <h3 class="text-base md:text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>

    <div class="space-y-3">

        <!-- APPROVE LEAVES -->
        <a href="<?= base_url('HR/leaveDashbaord') ?>"
           class="block text-center bg-gradient-to-r from-red-500 to-orange-500 text-white py-3 rounded-lg hover:shadow-lg transition font-medium">
           <i class="fa-solid fa-check-double mr-2"></i>Approve Leaves
        </a>

        <!-- APPROVE TIMESHEETS -->
        <a href="<?= base_url('HR/timesheetWithoutRoster') ?>"
           class="block text-center bg-gradient-to-r from-orange-500 to-yellow-500 text-white py-3 rounded-lg hover:shadow-lg transition font-medium">
           <i class="fa-solid fa-clock-rotate-left mr-2"></i>Approve Timesheets
        </a>

        <!-- VIEW ALL EMPLOYEES -->
        <a href="<?= base_url('HR/employees') ?>"
           class="block text-center bg-gradient-to-r from-blue-500 to-purple-500 text-white py-3 rounded-lg hover:shadow-lg transition font-medium">
           <i class="fa-solid fa-list-check mr-2"></i>View All Employees
        </a>

        <!-- SEND MEMO -->
        <a href="<?= base_url('HR/memo') ?>"
           class="block text-center bg-gradient-to-r from-teal to-teal-dark text-white py-3 rounded-lg hover:shadow-lg transition font-medium">
           <i class="fa-solid fa-bullhorn mr-2"></i>Send Memo
        </a>

    </div>
</div>


        </div>

        <!-- Center Column -->
        <div id="center-column" class="lg:col-span-5 space-y-4 md:space-y-6">
            
            <!-- Team Feed -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <h3 class="text-base md:text-lg font-bold text-gray-800 mb-4">What's Happening</h3>
               <div class="space-y-4">
<?php if(isset($incident_reports) && !empty($incident_reports)) {  ?>
    <?php foreach ($incident_reports as $inc): ?>
        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg border-l-4 border-red-500">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mt-1"></i>
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-800">
                   New incident Report Filed
                </div>
                <div class="text-xs text-gray-500">
                    <?= $inc->first_name . ' ' . $inc->last_name ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php  } ?>
    
    <?php if(isset($injury_reports) && !empty($injury_reports)) {  ?>
    <?php foreach ($injury_reports as $inc): ?>
        <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg border-l-4 border-red-500">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mt-1"></i>
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-800">
                    New injury Report Filed
                </div>
                <div class="text-xs text-gray-500">
                    <?= $inc->first_name . ' ' . $inc->last_name ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php  } ?>
    

 <?php if(isset($pending_leaves) && !empty($pending_leaves)) {  ?>
    <?php foreach ($pending_leaves as $lv): ?>
        <div class="flex items-start space-x-3 p-3 bg-orange-50 rounded-lg border-l-4 border-orange-500">
            <i class="fa-solid fa-hourglass-half text-orange-500 mt-1"></i>
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-800">
                    Leave Request: <?= $lv->start_date ?>
                </div>
                <div class="text-xs text-gray-500">
                    <?= $lv->first_name . ' ' . $lv->last_name ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php  } ?>

 <?php if(isset($birthdays_today) && !empty($birthdays_today)) {  ?>
    <?php foreach ($birthdays_today as $b): ?>
        <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg border-l-4 border-purple-500">
            <i class="fa-solid fa-cake-candles text-purple-500 mt-1"></i>
            <div class="flex-1">
                <div class="text-sm font-semibold text-gray-800">
                    Birthday Today
                </div>
                <div class="text-xs text-gray-500">
                    <?= $b->first_name . ' ' . $b->last_name ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php  } ?>
</div>

                <button onclick="window.location.href='<?= base_url('HR/memo') ?>'" class="w-full mt-4 bg-teal text-white py-2.5 rounded-lg hover:bg-teal-dark transition font-medium">
    <i class="fa-solid fa-plus mr-2"></i>Add Memo
</button>
            </div>

           

        </div>

        <!-- Right Column -->
        <div id="right-column" class="lg:col-span-4 space-y-4 md:space-y-6">
            
            <!-- Team Tasks Overview -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
               
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-2">
            <h3 class="text-base md:text-lg font-bold text-gray-800">Team Tasks Overview</h3>
            <div class="text-xs md:text-sm text-gray-600">
                <a href="#attendance-timeline" class="font-bold text-green-600">View Today's Attendance</a>
            </div>
        </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed Today</span>
                        <span class="font-bold text-green-600 text-lg"><?= $task_summary['completed_today'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">In Progress</span>
                        <span class="font-bold text-blue-600 text-lg"><?= $task_summary['in_progress'] ?></span>
                    </div>
                    

                </div>
            </div>

            

        </div>

    </div>

    <!-- Today's Team Attendance Timeline -->
    <section id="attendance-timeline" class="mt-4 md:mt-6 bg-white rounded-xl shadow-sm p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-2">
            <h3 class="text-base md:text-lg font-bold text-gray-800">Today's Team Attendance Timeline</h3>
            <div class="text-xs md:text-sm text-gray-600">
                Total Team Hours: <span class="font-bold text-teal"><?= $total_team_hours ?>h</span>
            </div>
        </div>
        <div class="overflow-x-auto table-responsive">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Employee</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Prep Area</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Clock In</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Break</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Clock Out</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Total Hours</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
               <tbody>
 <?php if(isset($attendance_today) && !empty($attendance_today)) {  ?>                  
<?php foreach ($attendance_today as $row): ?>
    <tr class="border-b border-gray-100 hover:bg-gray-50">
        <td class="py-3 px-4">
            <span class="text-sm font-medium text-gray-800">
                <?= $row->first_name . " " . $row->last_name ?>
            </span>
        </td>

        <td class="text-center py-3 px-4 text-sm text-gray-700">
            <?= $row->prep_name ?>
        </td>

        <td class="text-center py-3 px-4 text-sm text-gray-700">
            <?= $row->clock_in_time ? date("h:i A", strtotime($row->clock_in_time)) : "-" ?>
        </td>

        <td class="text-center py-3 px-4 text-sm text-gray-700">
            <?= $row->roster_break_start_time ?: "-" ?>
        </td>

        <td class="text-center py-3 px-4 text-sm text-gray-700">
            <?= $row->clock_out_time ? date("h:i A", strtotime($row->clock_out_time)) : "-" ?>
        </td>

        <td class="text-center py-3 px-4 text-sm font-semibold text-gray-800">
            <?php 
            if ($row->clock_in_time && $row->clock_out_time) {
                $clockInTs = strtotime($row->clock_in_time);
                $clockOutTs = strtotime($row->clock_out_time);
                // Handle overnight shift (clock_out appears earlier than clock_in)
                if ($clockOutTs <= $clockInTs) {
                    $clockOutTs += 86400; // Add 24 hours
                }
                echo round(($clockOutTs - $clockInTs) / 3600, 2) . "h";
            } else {
                echo "-";
            }
            ?>
        </td>

      <?php 
$status      = ($row->clock_in_time != '' ? 'Present' : 'Absent');
$badge_class = ($row->clock_in_time != '' 
                ? 'bg-green-100 text-green-700' 
                : 'bg-red-100 text-red-700');
?>

<td class="text-center py-3 px-4">
    <span class="<?= $badge_class ?> px-3 py-1 rounded-full text-xs font-medium">
        <?= $status ?>
    </span>
</td>


    </tr>
<?php endforeach; ?>
<?php  } ?>
</tbody>

            </table>
        </div>
    </section>

</main>



</body>
</html>