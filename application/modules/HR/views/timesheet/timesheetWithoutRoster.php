<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
 
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Timesheet </title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <style>
    
    [data-layout-mode=light] thead th {
    color: #374151 !important;
}

[data-layout-mode=light] h1{
    color: #283057 !important;
}

tfoot {
    backdrop-filter: blur(4px);
}
</style>
</head>
<body class=" font-sans">



<main id="main-content" class="px-6 py-6 mt-5">
    <?php $monday = new DateTime('monday this week'); ?>
  <?php $date_text = $monday->format('d M') . ' - ' . $monday->modify('+6 days')->format('d M'); ?>  
   <!-- PAGE HEADING -->
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
        Manage Employees Timesheet
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        assign and update weekly timesheet for all employees.
    </p>
</div>

<!-- TOP CONTROLS -->
<div id="top-controls" class="bg-white rounded-2xl shadow-sm border border-orange-100 p-2 mb-2">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-4">
           <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200">
    <button class="px-4 py-2 hover:bg-gray-100 rounded-l-lg transition prevWeek">
        <i class="fa-solid fa-chevron-left text-gray-600"></i>
    </button>
    <div class="px-6 py-2 border-x border-gray-200">
        <div class="text-xs text-gray-500 mb-0.5">Week</div>
        <div class="text-sm font-semibold text-gray-800 currentWeek">
            <?php echo $displayText; ?>
        </div>
    </div>
    <button class="px-4 py-2 hover:bg-gray-100 rounded-r-lg transition nextWeek">
        <i class="fa-solid fa-chevron-right text-gray-600"></i>
    </button>
</div>

<!-- IMPORTANT: Add this hidden data so JS knows current week -->
<script>
    var currentWeekStart = "<?php echo $dateRange['start_date']; ?>";
    var currentWeekEnd   = "<?php echo $dateRange['end_date']; ?>";
</script>
        </div>

        <div class="flex items-center space-x-3">
           <?php if(isset($prepAreaLists) && !empty($prepAreaLists)) { ?>
    <select id="emp_prep_area_filter" class="w-48 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-cafe-500 focus:border-transparent">
        <option value="0">Select Prep area </option>
        <?php foreach ($prepAreaLists as $prepAreaList) { ?>
            <option value="<?php echo $prepAreaList['id']; ?>">
                <?php echo $prepAreaList['prep_name']; ?>
            </option>
        <?php } ?>
    </select>
<?php } ?>




            <div class="relative w-64">
        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

        <input type="text" id="employeeSearch" placeholder="Search employee..."
           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm
                  focus:outline-none focus:ring-2 focus:ring-cafe-500 focus:border-transparent">

    </div>
    
    
    <button id="resetFilterBtn" class="p-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full transition" title="Reset Filters"><i class="fa-solid fa-rotate-right"></i>
    </button>
    
          
<button class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium text-sm transition shadow-sm" onclick="showTimesheetRecreateModal(<?php echo isset($timesheetId) ? $timesheetId : 0; ?>)">
                        <i class="fa-solid fa-copy mr-2"></i>
                        Recreate
                    </button>
           
<!-- Help Icon Button (Top Right) -->

            <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition" onclick="document.getElementById('help-modal').classList.remove('hidden'); document.getElementById('help-modal').classList.add('flex')" >
               
                <i class="fa-solid fa-circle-info"></i> Help
            </button>
            
             <a href="/HR/timesheetWithoutRoster" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

   
</div>

    
 <?php
// Ensure values exist
$weekStart = isset($dateRange['start_date']) ? $dateRange['start_date'] : date('Y-m-d');
$weekEnd   = isset($dateRange['end_date'])   ? $dateRange['end_date']   : date('Y-m-d');

// Start from the week selected (passed by controller)
$monday = new DateTime($weekStart);

$weekDays = [];
for ($i = 0; $i < 7; $i++) {
    $day = clone $monday;
    $day->modify("+$i days");

    $weekDays[] = [
        'label' => $day->format('D'),      // Mon, Tue, Wed...
        'date'  => $day->format('M d'),    // May 15
        'full'  => $day->format('Y-m-d'),  // 2025-05-15
    ];
}
?>



    <div class="flex gap-6">
        <div id="timesheet-table-container" class="flex-1">
            <div class="bg-white rounded-2xl shadow-sm border border-orange-100 overflow-hidden">
                <div class="overflow-x-auto max-w-full">
                    <table class="w-full">
                       <thead class="bg-gradient-to-r from-sky-200 to-blue-200 sticky top-0 z-20">
    <tr>
        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sticky left-0 bg-gradient-to-r from-sky-200 to-blue-200 z-10 min-w-[240px]">
            Employee
        </th>

        <?php foreach ($weekDays as $day) { ?>
            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider min-w-[100px]">
                <div><?php echo $day['label']; ?></div>
                <div class="text-[10px] text-gray-500 font-normal mt-0.5">
                    <?php echo $day['date']; ?>
                </div>
            </th>
        <?php } ?>

        <!--<th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider min-w-[100px]">-->
        <!--    Tasks-->
        <!--</th>-->

        <!--<th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider min-w-[200px]">-->
        <!--    Role-->
        <!--</th>-->

        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider min-w-[120px]">
            Actions
        </th>
    </tr>
</thead>
        <!--Employee rows ------------------------------->
        <?php
$weekDays = [
    'mon' => 'Mon',
    'tue' => 'Tue',
    'wed' => 'Wed',
    'thu' => 'Thu',
    'fri' => 'Fri',
    'sat' => 'Sat',
    'sun' => 'Sun'
];
?>
        
                        <tbody class="divide-y divide-gray-100">
                           <?php if (isset($empLists) && !empty($empLists)) { ?>
    <?php foreach ($empLists as $empList) { ?>

        <?php
       $position = isset($empList['positions'][0]) ? $empList['positions'][0] : null;


        $empName = ($empList['name'] ?? '');
        ?>

        <tr class="hover:bg-orange-25 transition group empRow empDiv" id="employee-row-<?php echo $empList['emp_id']; ?>"
            data-prepId="<?php echo $empList['emp_prep_area'] ?>"
            data-id="<?php echo $empList['emp_id']; ?>">

            <!-- EMPLOYEE COLUMN -->
            <td class="px-6 py-4 sticky left-0 bg-white group-hover:bg-orange-25 z-10">
    <div class="flex items-start space-x-3 relative">

        <!-- Name + Position -->
        <div>
            <div class="font-semibold text-gray-800 flex items-center space-x-2">
                <span><?php echo $empName ?: $empList['email']; ?></span>

                <!-- INFO ICON -->
                <?php if (!empty($empList['positions'])): ?>
                <i class="fa-solid fa-circle-info text-blue-500 cursor-pointer text-sm"
                   onclick="togglePositions(<?php echo $empList['emp_id']; ?>)"></i>
                <?php endif; ?>
            </div>

            <div class="text-xs text-gray-500">
                <?php echo $position['position_name'] ?? ''; ?>
            </div>
        </div>

        <!-- POSITIONS DROPDOWN -->
        <?php if (!empty($empList['positions'])): ?>
       <div id="pos-<?php echo $empList['emp_id']; ?>"
     class="hidden absolute top-10 left-0 bg-white shadow-xl border border-gray-200 rounded-lg p-3 w-60 z-50">


            <h4 class="font-semibold text-gray-700 mb-2 text-sm">Employee Positions</h4>

            <?php foreach ($empList['positions'] as $pos): ?>
                <div class="border-b pb-2 mb-2">
                    <div class="text-gray-800 text-sm font-medium">
                        <?php echo $pos['position_name']; ?>
                    </div>
                    <div class="text-xs text-gray-500">
                        Rate: $<?php echo $pos['rate']; ?>  
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>

    </div>
</td>

<?php 

$monday = new DateTime('monday this week');
$weekStart = $dateRange['start_date'];
$weekEnd   = $monday->modify('+6 days')->format('Y-m-d');
$monday->modify('-6 days'); 
?>
           <?php foreach ($weekDays as $dayKey => $dayLabel): ?>
<?php 
    $dayOffset = array_search($dayKey, ['mon','tue','wed','thu','fri','sat','sun']);
    $currentDate = date('Y-m-d', strtotime($weekStart . " +{$dayOffset} days"));
    // echo $currentDate; exit;
    $taskKey = $empList['emp_id'] . '_' . $currentDate;
    $taskCount = isset($tasks_by_emp_date[$taskKey]) ? count($tasks_by_emp_date[$taskKey]) : 0;
?>
<td class="px-2 py-4">
    <div class="relative group">
        <div onclick="openTaskModal(<?php echo $empList['emp_id']; ?>, '<?php echo addslashes(htmlspecialchars($empName)); ?>', '<?php echo $dayKey; ?>', '<?php echo $currentDate; ?>','<?php echo $empList['emp_prep_area'] ?>')"
             class="day-input w-full px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-center text-sm font-medium cursor-pointer transition hover:bg-green-100 hover:border-green-300 min-h-[40px] flex items-center justify-center">
            <?php if ($taskCount > 0): ?>
                <span class="text-green-800 font-bold">
                    <i class="fa-solid fa-eye mr-1"></i> On Shift
                </span>
            <?php else: ?>
                <span class="text-green-600">Add Task</span>
            <?php endif; ?>
        </div>

        <?php if ($taskCount > 0): ?>
        <button type="button" onclick="event.stopPropagation(); deleteAllTasks(<?php echo $empList['emp_id']; ?>, '<?php echo $currentDate; ?>')"
                class="absolute -top-2 -right-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-full p-1 shadow opacity-0 group-hover:opacity-100 transition">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
        <?php endif; ?>
    </div>
</td>
<?php endforeach; ?>


          
        
           <td class="px-4 py-4">
    <div class="flex items-center justify-center space-x-2">
        <?php 
        $isAdded = in_array($empList['emp_id'], $timesheetEmployeeIds);
        ?>
       <?php  
        if($edit){  ?>
        <button 
            class="save-row px-4 py-2 rounded-lg text-white font-medium text-sm transition <?php echo $isAdded ? 'bg-green-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'; ?>"
            data-id="<?php echo $empList['emp_id']; ?>" >
           
            <i class="fa-solid fa-plus mr-1"></i> Update
        </button>
       <?php  }else{ ?>
       <button 
            class="save-row px-4 py-2 rounded-lg text-white font-medium text-sm transition <?php echo $isAdded ? 'bg-gray-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'; ?>"
            data-id="<?php echo $empList['emp_id']; ?>"
            <?php echo $isAdded ? 'disabled' : ''; ?>>
            <?php echo $isAdded ? '<i class="fa-solid fa-check mr-1"></i> Added' : '<i class="fa-solid fa-plus mr-1"></i> Add'; ?>
        </button>
       <?php   }  ?>
       
        

        <button class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs transition remove-row"
                data-id="<?php echo $empList['emp_id']; ?>">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
</td>

        </tr>

    <?php } ?>
<?php } ?>
                            
                         
                        </tbody>
                        <tfoot class="sticky bottom-0 bg-gradient-to-r from-cafe-100 to-amber-100 border-t-2 border-cafe-200 text-sm">
    <tr>
        <td class="px-6 py-3 sticky left-0 bg-gradient-to-r from-cafe-100 to-amber-100 z-10 font-bold text-gray-800">
            TOTAL EMPLOYEES
        </td>

        <?php 
        foreach ($weekDays as $dayKey => $dayLabel) {
            echo '<td class="px-4 py-3 text-center font-bold text-gray-800">'
                . (isset($totalEmployeesPerDay[$dayKey]) ? $totalEmployeesPerDay[$dayKey] : 0)
                . '</td>';
        }
        ?>

        <!-- Total All Employees -->
        <td class="px-4 py-3 text-center">
            <div class="inline-flex items-center justify-center px-4 py-2 bg-cafe-600 text-white rounded-lg font-bold">
                <?php echo count($empLists); ?> Employees
            </div>
        </td>

        <td></td>
    </tr>
</tfoot>

                    </table>
                </div>
            </div>

           
        </div>

    </div>
</main>

<div id="toast-notification" class="hidden fixed bottom-6 right-6 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 z-50 min-w-[320px]">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
            <i class="fa-solid fa-check text-green-600"></i>
        </div>
        <div class="flex-1">
            <div class="font-semibold text-gray-800 text-sm">Success!</div>
            <div class="text-xs text-gray-600">Timesheet saved successfully</div>
        </div>
        <button class="text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>


<!--Modals  code-->
<div id="add-task-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="z-index: 9999;">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
        
        <!-- HEADER -->
        <div class="bg-indigo-500 text-white px-6 py-4 rounded-t-xl flex items-center justify-between flex-shrink-0">
            <h3 id="taskModalTitle" class="text-xl font-semibold">Add Tasks</h3>
            <button class="close-modal text-white hover:text-gray-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- ROW: Prep Area + Employee Position -->
        <div class="p-6 flex-shrink-0 border-b border-gray-200">
            
            
            <div id="availability-info" class="px-6 pt-4"></div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Prep Area -->
                <div>
                    <label for="prep-area-select" class="block mb-2 text-sm font-semibold text-gray-700">
                        Prep Area
                    </label>

                    <div class="relative">
                        <select 
                            id="prep-area-select"
                            class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm text-gray-700 
                                   focus:outline-none focus:ring-2 focus:ring-orange-500 w-full"
                        >
                            <option value="" disabled selected hidden>Select Prep Area</option>

                            <?php if(isset($prepAreaLists) && !empty($prepAreaLists)) { ?>
                                <?php foreach($prepAreaLists as $prepAreaList) { ?>
                                    <option value="<?= $prepAreaList['id'] ?>">
                                        <?= $prepAreaList['prep_name'] ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>

                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Employee Position -->
                <div>
                    <label for="emp-position-select" class="block mb-2 text-sm font-semibold text-gray-700">
                        Employee Position
                    </label>

                    <div class="relative">
                        <select 
                            id="emp-position-select"
                            class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm text-gray-700 
                                   focus:outline-none focus:ring-2 focus:ring-orange-500 w-full"
                        >
                            <option value="" disabled selected hidden>Select Employee Position</option>

                            <?php if(isset($positions) && !empty($positions)) { ?>
                                <?php foreach($positions as $position) { ?>
                                    <option value="<?= $position['position_id'] ?>">
                                        <?= $position['position_name'] ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>

                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

            </div>
        </div>

        <!-- BODY - Scrollable -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="tasks-container" class="space-y-4">
                <!-- Tasks will be injected here -->
            </div>

            <!-- Add More Task Button -->
            <button id="addMoreTaskBtn" class="mt-4 w-full px-4 py-2.5 border-2 border-dashed border-gray-300 text-gray-600 rounded-lg 
                                               font-medium hover:border-orange-500 hover:text-orange-500 hover:bg-orange-50 
                                               transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Add More Task
            </button>
        </div>

        <!-- FOOTER -->
        <div class="p-6 flex-shrink-0 border-t border-gray-200">
            <div class="flex justify-end space-x-3">
                <button class="close-modal px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                    Cancel
                </button>
                <button id="saveTaskBtn" class="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium">
                    Save Tasks
                </button>
            </div>
        </div>

    </div>
</div>


<!--help modal-->


<!-- HELP MODAL -->
<div id="help-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-8 py-6 rounded-t-2xl sticky top-0 z-10 flex items-center justify-between">
            <h2 class="text-2xl font-bold flex items-center gap-3">
                <i class="fa-solid fa-circle-question text-3xl"></i>
                How to Use Weekly Task Planner
            </h2>
            <button onclick="document.getElementById('help-modal').classList.add('hidden'); document.getElementById('help-modal').classList.remove('flex')"
                    class="text-white hover:text-gray-200 text-3xl">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-8 space-y-8 text-gray-700 text-base leading-relaxed">
            <!-- Step 1 -->
            <div class="flex gap-5">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold">1</div>
                <div>
                    <h3 class="font-bold text-lg text-indigo-600 mb-2">Add Employee to This Week's Timesheet</h3>
                    <p>Click the green <strong>Add</strong> button next to each employee.</p>
                    <p class="text-sm text-gray-500 mt-1">This creates their official timesheet for the week (Mon–Sun). Once added, the button turns gray and says <strong>Added</strong>.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-5">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold">2</div>
                <div>
                    <h3 class="font-bold text-lg text-indigo-600 mb-2">Assign Daily Tasks</h3>
                    <p>Click on any green day box (Mon, Tue, etc.) for an employee.</p>
                    <p class="text-sm text-gray-500 mt-1">A modal will open where you can:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                        <li>Select the <strong>Prep Area</strong> (applies to all tasks that day)</li>
                        <li>Type up to 3 (or more) tasks in the boxes</li>
                        <li>Click <strong>+ Add More Task</strong> if needed</li>
                        <li>Click <strong>Save Tasks</strong> when done</li>
                    </ul>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-5">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold">3</div>
                <div>
                    <h3 class="font-bold text-lg text-indigo-600 mb-2">View or Edit Tasks Later</h3>
                    <p>Once tasks are saved, the day box will show:</p>
                    <p class="bg-green-50 text-green-800 font-bold inline-block px-3 py-1 rounded-lg text-sm mt-2">3 Tasks</p>
                    <p class="text-sm text-gray-500 mt-2">Click it again anytime to view or edit the tasks.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex gap-5">
                <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-2xl font-bold">4</div>
                <div>
                    <h3 class="font-bold text-lg text-indigo-600 mb-2">Remove Tasks (Optional)</h3>
                    <p>Hover over a day with tasks → a small red × button appears in the top-right corner.</p>
                    <p class="text-sm text-gray-500 mt-1">Click it to delete all tasks for that day (with confirmation).</p>
                </div>
            </div>

            <!-- Final Note -->
            <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-6 mt-8">
                <h3 class="font-bold text-black mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-star text-xl"></i> Important Notes
                </h3>
                <ul class="space-y-2 text-sm text-orange-800">
                    <li><strong>Tasks are saved instantly</strong> — no need to refresh the page</li>
                    <li>You can edit tasks anytime by clicking the day box again</li>
                    <li>Employees must be "Added" first to appear in reports & clock-in system</li>
                    <li>All changes are saved automatically — no "Submit Week" needed</li>
                </ul>
            </div>

            <div class="text-center pt-6">
                <button onclick="document.getElementById('help-modal').classList.add('hidden'); document.getElementById('help-modal').classList.remove('flex')"
                        class="px-8 py-3 bg-indigo-700 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-lg">
                    Got it! Close Help
                </button>
            </div>
        </div>
    </div>
</div>


 <div class="modal fade" id="recreateTimesheetModal" tabindex="-1" aria-labelledby="recreateRoster" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recreateRoster">Select date for timesheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form  method="post" id="recreateTimesheetForm">
                    <div class="modal-body">
                        <input type="hidden" name="recreate_timesheet_id" class="recreate_timesheet_id">
                        <div class="mb-3">
                            <label for="startDate" class="col-form-label"> Start Date:</label>
                            <input type="text" name="start_date" id="startdatepicker" class="form-control flatpickr-input border-gray-300 rounded-lg p-2 text-sm" data-provider="flatpickr" data-date-format="d M, Y" readonly="readonly">
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="col-form-label"> End Date:</label>
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
    
<!-- Confirmation Modal (append once to body) -->
<div id="confirmRemoveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
    <div class="p-4 border-b">
      <h3 class="text-lg font-semibold text-black">Confirm remove</h3>
    </div>
    <div class="p-4">
      <p id="confirmRemoveMessage" class="text-sm text-gray-700">
        Are you sure you wish to remove this employee from the current timesheet week?
      </p>
    </div>
    <div class="p-4 border-t flex justify-end gap-3">
      <button id="confirmRemoveCancel" class="px-4 py-2 rounded border border-gray-300">Cancel</button>
      <button id="confirmRemoveYes" class="px-4 py-2 bg-red-500 text-white rounded">Yes, remove</button>
    </div>
  </div>
</div>

<script>

function showTimesheetRecreateModal(timesheetId) {
            $(".recreate_timesheet_id").val(timesheetId);
            $("#recreateTimesheetModal").modal("show");
        }
        
function togglePositions(empId) {
    console.log("empid=",empId)
    const box = document.getElementById("pos-" + empId);
    box.classList.toggle("hidden");
}
</script>

<script>
$(document).ready(function () {

    $("#employeeSearch").on("keyup", function () {
        let value = $(this).val().toLowerCase().trim();

        $(".empRow").filter(function () {

            // Get employee name + email text inside row
            let rowText = $(this).find("td:first").text().toLowerCase();

            // Show if match, hide otherwise
            $(this).toggle(rowText.indexOf(value) > -1);

        });
    });

});


// employee search filter by prep area

document.getElementById("emp_prep_area_filter").addEventListener("change", function () {
    let selectedPrepId = this.value;
    let rows = document.querySelectorAll(".empRow");

    rows.forEach(row => {
        let rowPrepId = row.getAttribute("data-prepId");

        if (selectedPrepId === "" || selectedPrepId === rowPrepId) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});

// reset filter


document.getElementById("resetFilterBtn").addEventListener("click", function () {
    // Reset dropdown
    document.getElementById("emp_prep_area_filter").value = "0";

    // Reset search
    document.getElementById("employeeSearch").value = "";

    // Show all rows
    document.querySelectorAll(".empRow").forEach(row => {
        row.style.display = "";
    });
});


</script>


<script>
window.addEventListener('load', function() {
   
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-notification');
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    const saveButtons = document.querySelectorAll('button:has(.fa-save)');
    saveButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            showToast('Timesheet row saved successfully', 'success');
        });
    });
});



  
</script>

<script>
  // Function to format date in "dd Mmm" format
  function formatDate(date) {
    return date.getDate() + ' ' + date.toLocaleDateString('en-us', { month: 'short' });
  }

  
  


  
  // show task modal
  
  document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("add-task-modal");
    const modalTitle = document.getElementById("taskModalTitle");

    // Open modal
    document.querySelectorAll(".add-task").forEach(btn => {
        btn.addEventListener("click", function () {

            let empName = this.dataset.employeename ?? "";
            let empId = this.dataset.emp;

            // Set dynamic title
            modalTitle.textContent = `Add task for ${empName}`;

            // Store employee ID for saving
            modal.dataset.emp = empId;

            // Show modal
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        });
    });

    // Close modal
    document.querySelectorAll(".close-modal").forEach(btn => {
        btn.addEventListener("click", function () {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        });
    });

});






// ADD EMPLOYEE TO TABLE
$(document).on("click", ".add-emp-btn", function () {
    let empId = $(this).data("emp-id");

    $.ajax({
        url: base_url + "timesheet/getEmployeeRow",
        type: "POST",
        data: { emp_id: empId },
        success: function (rowHtml) {
            $("#timesheet-body").append(rowHtml); // append employee row to table
            $("#search-dropdown").addClass("hidden");
            $("#employeeSearch").val("");
        }
    });
});



// save button click functinality

// When SAVE is clicked fpr that row

// Handle "Add" button in Actions column
$(document).on('click', '.save-row', function() {
    const empId = $(this).data('id');
    const timesheetWeek = $(".currentWeek").html();

    const btn = this;
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Adding...';
    btn.disabled = true;

    $.post(base_url + 'HR/timesheet/publishEmployeetimesheet', {
        employee_id: empId,
         timesheetWeek: timesheetWeek
    }, function(res) {
        if (res.success) {
            btn.innerHTML = '<i class="fa-solid fa-check mr-1"></i> Added';
            btn.classList.remove('bg-green-500', 'hover:bg-green-600');
            btn.classList.add('bg-gray-500', 'cursor-not-allowed');
            btn.disabled = true;
            showToast('Timesheet entry created!');
        }
    }, 'json').always(function() {
        setTimeout(() => {
            btn.innerHTML = original;
            btn.disabled = false;
        }, 2000);
    });
});






  
</script>  




<script>
// Fix base_url if not defined
var base_url = typeof base_url !== 'undefined' ? base_url : '<?php echo base_url(); ?>';

let currentEmployeeId = null;
let currentTaskDate = null;

function openTaskModal(empId, empName, dayKey, taskDate,prepId,positionId) {
    currentEmployeeId = empId;
    currentTaskDate = taskDate;
   $('#loaderContainer').show();
    document.getElementById('taskModalTitle').textContent = `Tasks for ${empName} - ${dayKey.toUpperCase()}`;

// --- NEW AVAILABILITY CHECK ---
fetch(base_url + "HR/Employees/get_availability_for_day", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "emp_id=" + empId + "&day_key=" + dayKey
})
.then(r => r.json())
.then(av => {

    const box = document.getElementById("availability-info");

    if (!av.available) {
        // RED WARN
        box.innerHTML = `
            <div class="p-3 mb-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm font-semibold">
                ⚠ Employee is <strong>NOT AVAILABLE</strong> on this day.
            </div>
        `;

        highlightUnavailableCell(empId, taskDate);
    } else {
        // GREEN INFO
        box.innerHTML = `
            <div class="p-3 mb-4 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
                <strong>Available:</strong> ${av.start} – ${av.end}
            </div>
        `;
    }

});


    // Reset modal
    document.getElementById('prep-area-select').value = prepId;
    document.getElementById('emp-position-select').value = positionId;
    document.getElementById('tasks-container').innerHTML = '';

    // Load existing tasks
   fetch(base_url + 'HR/timesheet/get_employee_tasks', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'employee_id=' + empId + '&task_date=' + taskDate
})
.then(response => response.json())
.then(data => {
    $('#loaderContainer').hide();

    if (data.success && data.tasks.length > 0) {

        // Set dropdown values
        document.getElementById('prep-area-select').value = data.prep_area_id || '';
        document.getElementById('emp-position-select').value = data.position_id || '';

        // Add each task field
        data.tasks.forEach(task => addTaskField(task));
    } else {

        // Default: show 3 blank task fields
        for (let i = 0; i < 3; i++) {
            addTaskField('');
        }
    }

    // Show modal
    const modal = document.getElementById('add-task-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
});

}

// Add new task textarea
function addTaskField(value = '') {
    const index = document.querySelectorAll('.task-item').length + 1;
    const html = `
        <div class="task-item">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Task ${index}</label>
                ${index > 3 ? `<button type="button" onclick="this.closest('.task-item').remove()" class="text-red-500 hover:text-red-700 text-xs"><i class="fa-solid fa-trash"></i> Remove</button>` : ''}
            </div>
            <textarea rows="4" placeholder="Add task notes..." class="resize task-textarea w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 overflow-auto">${value}</textarea>
        </div>`;
    document.getElementById('tasks-container').insertAdjacentHTML('beforeend', html);
}

// Add More Button
document.getElementById('addMoreTaskBtn').onclick = () => addTaskField();

// Save All Tasks
document.getElementById('saveTaskBtn').onclick = function() {
    const btn = this;
    const originalText = btn.innerHTML;
    const timesheetId = <?php echo json_encode($timesheetId ?? 0); ?>;
    
    // Show loader inside button
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...';
    btn.disabled = true;

    const prepArea = document.getElementById('prep-area-select').value;
    const empPositionId = document.getElementById('emp-position-select').value;
    if (!prepArea) {
        alert('Please select a Prep Area');
        btn.innerHTML = originalText;
        btn.disabled = false;
        return;
    }

    const textareas = document.querySelectorAll('#tasks-container .task-textarea');
    const tasks = [];
    

    textareas.forEach(ta => {
    let val = ta.value.trim();

    // Remove single quotes and double quotes
    val = val.replace(/['"]/g, "");

    if (val !== '') {
       
        tasks.push(val);
    }
});



    fetch(base_url + 'HR/timesheet/save_employee_tasks', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            employee_id: currentEmployeeId,
            task_date: currentTaskDate,
            prep_area_id: prepArea,
            empPositionId: empPositionId,
            tasks: tasks,
            timesheetId:timesheetId
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            // SUCCESS: Update the cell WITHOUT refresh
            const cell = document.querySelector(`[onclick*="${currentEmployeeId}"][onclick*="${currentTaskDate}"]`);
            if (cell) {
                const displayDiv = cell.querySelector('.day-input > span') || cell.querySelector('.day-input');
                displayDiv.innerHTML = `<i class="fa-solid fa-check-circle mr-1"></i> ${tasks.length} Task${tasks.length > 1 ? 's' : ''}`;
                displayDiv.classList.add('text-green-800', 'font-bold');
            }

            // Close modal
            document.getElementById('add-task-modal').classList.add('hidden');
            document.getElementById('add-task-modal').classList.remove('flex');

            showToast('Tasks saved successfully!');
        } else {
            alert('Error saving tasks');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network error');
    })
    .finally(() => {
        // Restore button
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
};

// Close modal
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.onclick = () => {
        document.getElementById('add-task-modal').classList.add('hidden');
        document.getElementById('add-task-modal').classList.remove('flex');
    };
});

// Close on backdrop
document.getElementById('add-task-modal').onclick = function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        this.classList.remove('flex');
    }
};
</script>

<script>
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-6 right-6 bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-pulse';
    toast.innerHTML = `<i class="fa-solid fa-check-circle mr-2"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}


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

           
        });
        
</script>

<script>
// Recreate timesheet 
$(document).ready(function() {
    $('#recreateTimesheetForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: '<?php echo base_url("HR/Timesheet/recreateTimesheet"); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.status === 'error') {
                    showToast(res.message, 'error');
                } else if (res.status === 'success') {
                    showToast('Timesheet recreated successfully', 'success');
                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 1000);
                }
            },
            error: function() {
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });

   
});
</script>

<script>
// Base URL of your controller method
var baseUrl = "<?php echo base_url('/HR/addTimesheetWithoutRoster'); ?>";

document.querySelector('.nextWeek').addEventListener('click', function() {
    var nextMonday = new Date(currentWeekStart);
    nextMonday.setDate(nextMonday.getDate() + 7);

    var nextSunday = new Date(nextMonday);
    nextSunday.setDate(nextSunday.getDate() + 6);

    var start = formatDate(nextMonday);
    var end   = formatDate(nextSunday);

    // Go to next week
    window.location.href = baseUrl + '/' + start + '/' + end;
});

document.querySelector('.prevWeek').addEventListener('click', function() {
    var prevMonday = new Date(currentWeekStart);
    prevMonday.setDate(prevMonday.getDate() - 7);

    var prevSunday = new Date(prevMonday);
    prevSunday.setDate(prevSunday.getDate() + 6);

    var start = formatDate(prevMonday);
    var end   = formatDate(prevSunday);

    // Go to previous week
    window.location.href = baseUrl + '/' + start + '/' + end ;
});

function formatDate(date) {
    return date.getFullYear() + '-' +
           ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
           ('0' + date.getDate()).slice(-2);
}

// delete employee from timesheet
$(document).ready(function () {

    let pendingEmpId = null;

    function getTimesheetWeek() {
        return $(".currentWeek").text().trim();
    }

    function openConfirmModal(message) {
        $("#confirmRemoveMessage").text(message);
        $("#confirmRemoveModal").removeClass("hidden").addClass("flex");
    }

    function closeConfirmModal() {
        $("#confirmRemoveModal").removeClass("flex").addClass("hidden");
    }

    // Click handler for remove button
    $(document).on("click", ".remove-row", function () {
        pendingEmpId = $(this).data("id");
        const empName = $(this).data("name") || "";
        const week = getTimesheetWeek();

        const msg = empName
            ? `Are you sure you want to remove ${empName} from timesheet week ${week}?`
            : `Are you sure you want to remove this employee from timesheet week ${week}?`;

        openConfirmModal(msg);
    });

    // Cancel remove
    $("#confirmRemoveCancel").click(function () {
        pendingEmpId = null;
        closeConfirmModal();
    });

    // Confirm remove → send AJAX
    $("#confirmRemoveYes").click(function () {

        if (!pendingEmpId) {
            closeConfirmModal();
            return;
        }

        const timesheetWeek = getTimesheetWeek();
        const $btn = $(this);

        $btn.prop("disabled", true).text("Removing...");

        $.ajax({
            url: base_url + "HR/Timesheet/removeEmployeeFromTimesheet",
            type: "POST",
            data: {
                employee_id: pendingEmpId,
                timesheet_week: timesheetWeek
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {

                  location.reload();

                    alert(res.message || "Employee removed successfully.");
                } else {
                    alert(res.message || "Failed to remove employee.");
                }
            },
            error: function () {
                alert("Network error. Please try again.");
            },
            complete: function () {
                $btn.prop("disabled", false).text("Yes, Remove");
                pendingEmpId = null;
                closeConfirmModal();
            }
        });
    });

});



</script>
<script>

function highlightUnavailableCell(empId, taskDate) {
    // Find the day-cell element
    const cell = document.querySelector(
        `[onclick*="${empId}"][onclick*="${taskDate}"]`
    ).closest(".day-input");

    cell.classList.remove("bg-green-50", "border-green-200");
    cell.classList.add(
        "bg-red-50",
        "border-red-300",
        "text-red-700",
        "font-bold"
    );
}
</script>


</body>
</html>