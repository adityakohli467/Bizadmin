<!-- rosterViewWTM.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <style>
        .shift-time-cell {
            font-size: 12px;
            padding: 8px;
        }
        .time-display {
            display: block;
            margin-bottom: 4px;
        }
        }
        [data-layout-mode=light] thead th {
    color: #1a2f52;
}
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <div class="flex-1 flex flex-col overflow-hidden mt-5">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 py-3 px-4 md:px-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2 md:gap-3">
                        <a onclick="goBack()" class="inline-flex items-center px-3 py-1.5 bg-orange-primary text-white rounded-lg text-sm hover:bg-orange-600 cursor-pointer">
                            <i class="fa-solid fa-arrow-left mr-2"></i>Back
                        </a>
                        
                        <!-- <input type="text" name="rosterName" id="rosterName" placeholder="Roster Name" 
                               class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm w-48"
                               value="<?php echo isset($rosterInfo[0]['rosterName']) ? htmlspecialchars($rosterInfo[0]['rosterName']) : ''; ?>"> -->
                        
                        <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                            <button class="prevWeek px-2 py-1.5 text-gray-500 hover:bg-gray-200 rounded">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <span class="currentWeek px-3 py-1.5 text-sm font-medium text-gray-700 whitespace-nowrap">
                                <?php
                                $startFormatted = date('jS M', strtotime($rosterInfo[0]['start_date']));
                                $endFormatted = date('jS M', strtotime($rosterInfo[0]['end_date']));
                                echo "$startFormatted - $endFormatted";
                                ?>
                            </span>
                            <button class="nextWeek px-2 py-1.5 text-gray-500 hover:bg-gray-200 rounded">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        <div class="relative">
                            <select class="weekAreaAndTeam px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm cursor-pointer">
                                <option value="1">Week by Area</option>
                                <option value="2" selected>Week by Team Member</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <input type="text" id="employeeSearch" 
                               class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm w-48 md:w-64" 
                               placeholder="🔍 Search employees...">
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-1 overflow-auto p-3 md:p-6">
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                    <?php if (empty($rosterViewWTM)) { ?>
                        <div class="p-8 text-center text-gray-500">
                            <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
                            <p class="text-lg">No roster details found for this week.</p>
                        </div>
                    <?php } else { ?>
                        <div class="overflow-auto">
                            <table class="w-full border-collapse mb-5" id="rosterTable">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700 sticky left-0 bg-gray-50 z-10" style="min-width: 180px;">
                                            Employee Name
                                        </th>
                                        <?php foreach ($days as $day) { ?>
                                            <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700" style="min-width: 140px;">
                                                <div><?php echo ucfirst($day['day']); ?></div>
                                                <div class="text-xs font-normal text-gray-500"><?php echo date('d M', strtotime($day['date'])); ?></div>
                                            </th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rosterViewWTM as $empId => $empData) { ?>
                                        <tr class="employee-row border-b border-gray-100 hover:bg-gray-50" 
                                            data-employee-name="<?php echo strtolower($empData['emp_name']); ?>">
                                            <td class="px-4 py-3 sticky left-0 bg-white z-10">
                                                <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($empData['emp_name']); ?></div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($empData['prep_name']); ?>
                                                </div>
                                            </td>
                                            <?php foreach ($days as $day) { ?>
                                                <?php $dayKey = $day['day']; ?>
                                                <td class="px-4 py-3">
                                                    <?php if (!empty($empData[$dayKey]['start_time'])) { ?>
                                                        <div class="space-y-1">
                                                            <div class="flex items-center text-xs text-gray-700">
                                                                <i class="fa-regular fa-clock text-green-600 mr-1"></i>
                                                                <span class="font-medium"><?php echo $empData[$dayKey]['start_time']; ?></span>
                                                            </div>
                                                            <div class="flex items-center text-xs text-gray-700">
                                                                <i class="fa-regular fa-clock text-green-600 mr-1"></i>
                                                                <span class="font-medium"><?php echo $empData[$dayKey]['end_time']; ?></span>
                                                            </div>
                                                            <?php if (!empty($empData[$dayKey]['break_duration'])) { ?>
                                                                <div class="flex items-center text-xs text-gray-500">
                                                                    <i class="fa-solid fa-coffee mr-1"></i>
                                                                    <span><?php echo $empData[$dayKey]['break_duration']; ?> min</span>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <span class="text-gray-400 text-sm">-</span>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

<script>
function goBack() {
    window.history.back();
}

function formatDate(date) {
    return date.getDate() + ' ' + date.toLocaleDateString('en-us', { month: 'short' });
}

function getCurrentWeekStartDate() {
    let startDate = '<?php echo $rosterInfo[0]['start_date']; ?>';
    return new Date(startDate);
}

var currentWeekStartDate = getCurrentWeekStartDate();

function updateCurrentWeekText(fetchRosterData) {
    const endDate = new Date(currentWeekStartDate);
    endDate.setDate(currentWeekStartDate.getDate() + 6);
    const buttonText = formatDate(currentWeekStartDate) + ' - ' + formatDate(endDate);
    const encodedButtonText = encodeURIComponent(buttonText);
    $('.currentWeek').text(buttonText);
    if (fetchRosterData) {
        window.location.href = '/HR/fetchRosterOnArrowClick/' + encodedButtonText + '/WTM';
    }
}

function updatePrevWeekText() {
    currentWeekStartDate.setDate(currentWeekStartDate.getDate() - 7);
    updateCurrentWeekText(true);
}

function updateNextWeekText() {
    currentWeekStartDate.setDate(currentWeekStartDate.getDate() + 7);
    updateCurrentWeekText(true);
}

$(document).ready(function() {
    $('.prevWeek').click(function() {
        updatePrevWeekText();
    });

    $('.nextWeek').click(function() {
        updateNextWeekText();
    });

    // Employee search functionality
    $('#employeeSearch').on('keyup', function() {
        var searchValue = $(this).val().toLowerCase();
        
        $('.employee-row').each(function() {
            var employeeName = $(this).data('employee-name');
            if (employeeName.indexOf(searchValue) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(".weekAreaAndTeam").on('change', function() {
        let rosterId = '<?php echo $rosterId; ?>';
        if (!rosterId || rosterId.trim() === '' || isNaN(parseInt(rosterId))) {
            rosterId = 0;
        }
        rosterId = parseInt(rosterId);

        if ($(this).val() == '1') {
            window.location.href = '/HR/roster/rosterView/' + rosterId;
        } else {
            window.location.href = '/HR/roster/rosterViewWTM/' + rosterId;
        }
    });
});
</script>
</body>
</html>