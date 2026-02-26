<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Tasks – Prep Area View</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .collapse-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
        .collapse-content.active { max-height: 3000px; }
        .day-cell:hover { transform: scale(1.08); }
        .task-popup {
            display: none; position: absolute; z-index: 1000; background: white;
            border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; min-width: 240px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .day-cell{
           min-width: 80px;  
        }
       [data-layout-mode=light] thead th {
    color: #303030 !important;
}

        .task-popup.active { display: block; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="bg-gray-50">


<main class="max-w-7xl mx-auto px-4 py-20">

    <!-- Search Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 mb-6 no-print">
        <div class="flex gap-4 items-center px-3">


            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="search-employee" placeholder="Search employee name..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:outline-none"
                       onkeyup="filterEmployees()">
            </div>
            
            <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200">
    <button class="px-4 py-2 hover:bg-gray-100 rounded-l-lg transition prevWeek">
        <i class="fa-solid fa-chevron-left text-gray-600"></i>
    </button>
    <div class="px-6 py-1 border-x border-gray-200">
        <div class="text-xs text-gray-500 mb-0.5">Week</div>
        <div class="text-sm font-semibold text-gray-800 currentWeek">
            <?php echo $displayText; ?>
        </div>
    </div>
    <button class="px-4 py-2 hover:bg-gray-100 rounded-r-lg transition nextWeek">
        <i class="fa-solid fa-chevron-right text-gray-600"></i>
    </button>
</div>
                <a href="/HR/timesheetWithoutRoster" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-orange-600"> <i class="fa-solid fa-arrow-left text-white"></i> Back</a>
                
        </div>
        
       
    </div>

    <!-- Prep Areas Container -->
    <div id="prep-areas-container" class="space-y-6">
        <?php 
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $area_index = 0;
        $current_day = date('D');
        
        foreach ($prep_areas_data as $area): 
            $area_index++;
        ?>
        <div class="prep-area-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" data-area-name="<?= strtolower($area['name']) ?>">
             
            
            <!-- Header -->
            <div class="px-6 py-3 border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition">
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="<?php echo $area['color'] ?> text-white px-4 py-1.5 rounded-full font-semibold text-sm">
                            <?= htmlspecialchars($area['name']) ?>
                        </span>
                        <span class="text-gray-500"><?= count($area['employees']) ?> employee<?= count($area['employees']) > 1 ? 's' : '' ?></span>
                    </div>
                    <i id="icon-<?= $area_index ?>" class="fa-solid fa-chevron-down text-gray-500 transition-transform <?= $area_index === 1 ? 'rotate-180' : '' ?>"></i>
                </div>
            </div>

            <!-- Content -->
            <div id="content-<?= $area_index ?>">
                <div class="p-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="text-left py-3 px-4 sticky left-0 bg-white z-10 min-w-[220px]">Employee</th>
                                <?php foreach($days as $day): ?>
                                    <th class="text-center py-2 px-2 <?= ($day === $current_day) ? 'bg-gray-100' : '' ?>">
                                        <?= substr($day, 0, 3) ?>
                                    </th>
                                <?php endforeach; ?>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            
                            foreach ($area['employees'] as $emp): 
                                $total_tasks = 0;
                                $has_tasks = false;
                                $overloaded = false;
                                foreach ($emp['tasks'] as $day_tasks) {
                                    $count = is_array($day_tasks) ? count($day_tasks) : 0;
                                    $total_tasks += $count;
                                    if ($count > 0) $has_tasks = true;
                                    if ($count >= 6) $overloaded = true;
                                }
                                $row_class = $total_tasks == 0 ? 'bg-red-50' : ($overloaded ? 'bg-amber-50' : '');
                            ?>
                            <tr class="border-b bg-gray-50 hover:bg-gray-100 transition employee-row <?= $row_class ?>" data-employee-name="<?= strtolower($emp['name']) ?>">
                                
                                
                                <td class="py-2 px-4 sticky left-0 bg-inherit z-10">
                                    <div class="flex items-center gap-3">
                                        
                                        <div>
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($emp['name']) ?></div>
                                            <div class="text-xs text-gray-500">Staff</div>
                                        </div>
                                    </div>
                                </td>

                                <?php
                                
                                foreach($days as $day):
                                    $tasks = $emp['tasks'][$day] ?? [];
                                    $count = is_array($tasks) ? count($tasks) : 0;
                                    if ($count === 0): ?>
                                        <td class="text-center py-2 <?= ($day === $current_day) ? 'bg-gray-100' : '' ?>">
                                            <span class="text-gray-300 text-sm">—</span>
                                        </td>
                                    <?php else: 
                                        $color = 'bg-green-100 text-green-700 hover:bg-green-200';
                                    ?>
                                        <td class="text-center py-2 <?= ($day === $current_day) ? 'bg-gray-100' : '' ?>">
                                            <span class="inline-flex items-center justify-center w-9 h-8 rounded-full <?= $color ?> font-semibold text-sm cursor-pointer day-cell"
                                                  onclick='showTaskPopup(event, <?= json_encode($tasks) ?>)'>
                                                On Shift
                                            </span>
                                        </td>
                                    <?php endif; endforeach; ?>

                                
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Task Popup -->
<div id="taskPopup" class="task-popup"></div>

<script>


// Show Task Details Popup
function showTaskPopup(e, tasks) {
    e.stopPropagation();
    const popup = document.getElementById('taskPopup');
    popup.classList.remove('active');

    let html = '<div class="font-bold text-gray-800 mb-2">Tasks:</div>';
    tasks.forEach(t => {
        html += `<div class="py-1 text-gray-700 border-b border-gray-100 last:border-0">• ${t.note}</div>`;
    });
    popup.innerHTML = html;

    const rect = e.target.getBoundingClientRect();
    popup.style.top = (rect.bottom + window.scrollY + 8) + 'px';
    popup.style.left = (rect.left + window.scrollX + rect.width/2 - popup.offsetWidth/2) + 'px';
    popup.classList.add('active');

    setTimeout(() => document.addEventListener('click', () => popup.classList.remove('active')), 10);
}

// Live Search Filter
function filterEmployees() {
    const query = document.getElementById('search-employee').value.toLowerCase();
    const rows = document.querySelectorAll('.employee-row');
    const cards = document.querySelectorAll('.prep-area-card');

    let visibleCount = 0;

    rows.forEach(row => {
        const name = row.getAttribute('data-employee-name');
        const shouldShow = name.includes(query);
        row.style.display = shouldShow ? '' : 'none';
        if (shouldShow) visibleCount++;
    });

    // Hide prep area if no employees visible
    cards.forEach(card => {
        const visibleInArea = card.querySelectorAll('.employee-row[style=""]').length > 0 ||
                              card.querySelectorAll('.employee-row:not([style])').length > 0;
        card.style.display = visibleInArea || query === '' ? '' : 'none';
    });
}
</script>
</body>
</html>