<?php /* Employee dashboard: rendered inside the shared Velzon layout (header > content > footer). Page-specific assets only. */ ?>
<link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
<?php $this->load->view('general/tailwind_common_assets'); ?>
    <style>
    .empv3{font-family:'Inter',system-ui,sans-serif;}
    .empv3 *{box-sizing:border-box;}
    .empv3 .db{background:#f1f5f9;border-radius:0;overflow:hidden;border:.5px solid #e2e8f0;}
    .empv3 .layout{display:flex;min-height:700px;}
    .empv3 .sidebar{width:260px;flex-shrink:0;background:#fff;border-right:.5px solid #e2e8f0;padding:20px;display:flex;flex-direction:column;gap:18px;}
    .empv3 .profile-block{text-align:center;padding-bottom:16px;border-bottom:.5px solid #f1f5f9;}
    .empv3 .av-wrap{position:relative;width:64px;height:64px;margin:0 auto 10px;}
    .empv3 .av-circle{width:64px;height:64px;border-radius:50%;border:2.5px solid #25A69A;background:#0F6E56;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:600;}
    .empv3 .online-dot{width:12px;height:12px;background:#22c55e;border-radius:50%;border:2.5px solid #fff;position:absolute;bottom:2px;right:2px;}
    .empv3 .online-dot.off{background:#cbd5e1;}
    .empv3 .p-name{font-size:15px;font-weight:600;color:#1e293b;}
    .empv3 .p-sub{font-size:12px;color:#94a3b8;margin-top:3px;}
    .empv3 .present-pill{display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;border:.5px solid #bbf7d0;border-radius:20px;padding:4px 12px;font-size:11px;color:#15803d;margin-top:8px;font-weight:600;}
    .empv3 .present-pill.absent{background:#fef2f2;border-color:#fecaca;color:#b91c1c;}
    .empv3 .g-dot{width:6px;height:6px;border-radius:50%;background:#22c55e;}
    .empv3 .present-pill.absent .g-dot{background:#ef4444;}
    .empv3 .block-title{font-size:12px;font-weight:600;color:#1e293b;margin-bottom:8px;}
    .empv3 .sched-card{background:#f8fafc;border-radius:0;padding:12px 14px;border:.5px solid #e2e8f0;}
    .empv3 .sched-time{font-size:14px;font-weight:600;color:#1e293b;}
    .empv3 .sched-sub{font-size:12px;color:#64748b;margin-top:3px;}
    .empv3 .clocked{display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:.5px solid #bbf7d0;border-radius:20px;padding:5px 10px;font-size:11px;color:#15803d;margin-top:8px;font-weight:600;}
    .empv3 .c-dot{width:6px;height:6px;border-radius:50%;background:#22c55e;flex-shrink:0;}
    .empv3 .qs-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:.5px solid #f1f5f9;}
    .empv3 .qs-row:last-child{border-bottom:none;}
    .empv3 .qs-label{font-size:12px;color:#64748b;}
    .empv3 .qs-val{font-size:13px;font-weight:600;color:#1e293b;}
    .empv3 .qs-val.teal{color:#0F6E56;}
    .empv3 .leave-card{background:#0F6E56;border-radius:0;padding:16px;}
    .empv3 .lc-title{color:#9FE1CB;font-size:12px;font-weight:600;margin-bottom:12px;}
    .empv3 .lc-row{margin-bottom:10px;}
    .empv3 .lc-row:last-of-type{margin-bottom:0;}
    .empv3 .lc-hdr{display:flex;justify-content:space-between;margin-bottom:5px;}
    .empv3 .lc-name{font-size:12px;color:#E1F5EE;}
    .empv3 .lc-val{font-size:12px;color:#fff;font-weight:600;}
    .empv3 .lc-bar{height:5px;background:rgba(255,255,255,.15);border-radius:10px;overflow:hidden;}
    .empv3 .lc-fill{height:100%;background:#5DCAA5;border-radius:10px;}
    .empv3 .apply-btn{display:block;width:100%;background:rgba(255,255,255,.1);border:.5px solid rgba(255,255,255,.25);border-radius:8px;padding:9px;font-size:12px;color:#E1F5EE;font-weight:600;margin-top:12px;text-align:center;cursor:pointer;}
    .empv3 .apply-btn:hover{background:rgba(255,255,255,.18);}
    .empv3 .cal-wrap{background:#fff;border-radius:0;border:.5px solid #e2e8f0;padding:18px 20px;}
    .empv3 .cal-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;}
    .empv3 .cal-month{font-size:13px;font-weight:600;color:#1e293b;}
    .empv3 .cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;text-align:center;}
    .empv3 .cal-dh{font-size:10px;color:#94a3b8;padding:3px 0;font-weight:600;}
    .empv3 .cal-d{font-size:12px;color:#64748b;padding:5px 3px;border-radius:6px;}
    .empv3 .cal-d.today{background:#25A69A;color:#fff;font-weight:600;border-radius:50%;}
    .empv3 .cal-d.leave{color:#25A69A;font-weight:600;}
    .empv3 .cal-d.empty{opacity:0;}
    .empv3 .cal-legend{display:flex;gap:14px;margin-top:10px;}
    .empv3 .leg{display:flex;align-items:center;gap:5px;font-size:11px;color:#64748b;}
    .empv3 .leg-d{width:8px;height:8px;border-radius:50%;}
    .empv3 .main{flex:1;padding:0 20px 20px 20px;display:flex;flex-direction:column;gap:16px;min-width:0;}
    .empv3 .stat-row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
    .empv3 .stat-card{background:#fff;border-radius:0;border:.5px solid #e2e8f0;padding:16px 18px;}
    .empv3 .stat-num{font-size:28px;font-weight:600;color:#1a2f52;}
    .empv3 .stat-label{font-size:12px;color:#64748b;margin-top:3px;}
    .empv3 .stat-present{font-size:18px;font-weight:600;color:#0F6E56;}
    .empv3 .stat-present.absent{color:#b91c1c;}
    .empv3 .two-col{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .empv3 .card{background:#fff;border-radius:0;border:.5px solid #e2e8f0;padding:18px 20px;}
    .empv3 .card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;}
    .empv3 .card-title{font-size:13px;font-weight:600;color:#1e293b;}
    .empv3 .card-link{font-size:12px;color:#25A69A;font-weight:600;cursor:pointer;background:none;border:none;padding:0;}
    .empv3 .tl-row{display:flex;justify-content:space-between;margin-bottom:10px;}
    .empv3 .tl-col{text-align:center;}
    .empv3 .tl-lbl{font-size:11px;color:#94a3b8;}
    .empv3 .tl-v{font-size:13px;font-weight:600;color:#25A69A;margin-top:3px;}
    .empv3 .tl-v.dim{color:#94a3b8;}
    .empv3 .tl-v.orange{color:#F29A6E;}
    .empv3 .prog-track{height:7px;background:#f1f5f9;border-radius:10px;overflow:hidden;margin-bottom:8px;}
    .empv3 .prog-fill{height:100%;background:#25A69A;border-radius:10px;}
    .empv3 .tl-foot{display:flex;justify-content:space-between;}
    .empv3 .ts-tbl{width:100%;table-layout:fixed;border-collapse:collapse;}
    .empv3 .ts-tbl th{font-size:11px;color:#94a3b8;font-weight:600;text-align:left;padding:0 0 10px;text-transform:uppercase;letter-spacing:.4px;}
    .empv3 .ts-tbl td{font-size:12px;color:#1e293b;padding:9px 0;border-bottom:.5px solid #f1f5f9;vertical-align:middle;}
    .empv3 .ts-tbl tr:last-child td{border-bottom:none;}
    .empv3 .view-btn{background:#E1F5EE;color:#0F6E56;border:none;border-radius:7px;padding:5px 14px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
    .empv3 .avail-card{background:#fff;border-radius:0;border:.5px solid #e2e8f0;padding:20px 22px;}
    .empv3 .avail-hdr{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px;gap:12px;}
    .empv3 .avail-sub{font-size:12px;color:#94a3b8;margin-top:4px;}
    .empv3 .save-btn{background:#25A69A;border:none;border-radius:9px;padding:9px 22px;font-size:13px;color:#fff;font-weight:600;cursor:pointer;white-space:nowrap;}
    .empv3 .save-btn:hover{background:#0F6E56;}
    .empv3 .days-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:12px;}
    .empv3 .day-col{display:flex;flex-direction:column;gap:8px;}
    .empv3 .day-name{font-size:13px;font-weight:600;color:#1e293b;margin-bottom:2px;}
    .empv3 .time-label{font-size:11px;color:#94a3b8;margin-bottom:2px;}
    .empv3 .time-sel{width:100%;background:#f8fafc;border:.5px solid #cbd5e1;border-radius:8px;padding:8px 10px;font-size:13px;color:#1e293b;cursor:pointer;appearance:none;-webkit-appearance:none;}
    .empv3 .time-sel:focus{outline:none;border-color:#25A69A;background:#fff;}
    .empv3 .to-divider{text-align:center;font-size:12px;color:#94a3b8;padding:2px 0;}
    .empv3 .same-all-toggle{display:inline-flex;align-items:center;gap:8px;cursor:pointer;user-select:none;}
    .empv3 .same-all-toggle input{display:none;}
    .empv3 .same-all-toggle .sat-slider{width:36px;height:20px;background:#cbd5e1;border-radius:20px;position:relative;transition:background .2s;flex-shrink:0;}
    .empv3 .same-all-toggle .sat-slider::after{content:'';position:absolute;top:2px;left:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:transform .2s;}
    .empv3 .same-all-toggle input:checked + .sat-slider{background:#25A69A;}
    .empv3 .same-all-toggle input:checked + .sat-slider::after{transform:translateX(16px);}
    .empv3 .same-all-toggle .sat-txt{font-size:12px;color:#64748b;font-weight:500;white-space:nowrap;}
    @media(max-width:1100px){.empv3 .layout{flex-direction:column;}.empv3 .sidebar{width:100%;border-right:none;border-bottom:.5px solid #e2e8f0;}.empv3 .two-col{grid-template-columns:1fr;}.empv3 .days-grid{grid-template-columns:repeat(4,1fr);}}
    @media(max-width:560px){.empv3 .stat-row{grid-template-columns:1fr;}.empv3 .days-grid{grid-template-columns:repeat(2,1fr);}}
    /* Common system loader overlay */
    #ts-loader-overlay{display:none;position:fixed;inset:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:99999;justify-content:center;align-items:center;}
    #ts-loader-overlay.show{display:flex;}
    #ts-loader-overlay .ts-spinner{width:130px;height:130px;border:3px solid #f3f3f3;border-top:3px solid #1a2f52;border-radius:50%;animation:tsspin 1s linear infinite;}
    @keyframes tsspin{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
    /* Timesheet details modal - matched to dashboard UI */
    #timesheetDetailsModal .modal-content{border:none;border-radius:16px;overflow:hidden;font-family:'Inter',sans-serif;}
    #timesheetDetailsModal .modal-header{background:#1a2f52;color:#fff;border-bottom:none;padding:16px 22px;}
    #timesheetDetailsModal .modal-title{font-size:16px;font-weight:600;}
    #timesheetDetailsModal .modal-body{padding:20px 22px;background:#f8fafc;}
    #timesheetDetailsModal .modal-footer{border-top:.5px solid #e2e8f0;padding:12px 22px;background:#fff;}
    #timesheetDetailsModal .ts-week-banner{background:#E1F5EE;border:.5px solid #9FE1CB;border-radius:10px;padding:11px 16px;margin-bottom:16px;font-size:13px;color:#0F6E56;}
    #timesheetDetailsModal .ts-week-banner span{font-weight:600;text-transform:uppercase;letter-spacing:.4px;font-size:11px;margin-right:6px;}
    #timesheetDetailsModal .ts-week-banner strong{color:#0F6E56;font-weight:600;}
    #timesheetDetailsModal .ts-table-wrap{background:#fff;border:.5px solid #e2e8f0;border-radius:12px;overflow:hidden;}
    #timesheetDetailsModal table{width:100%;border-collapse:collapse;margin:0;}
    #timesheetDetailsModal thead th{background:#f8fafc;color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;text-align:left;padding:12px 14px;border-bottom:.5px solid #e2e8f0;}
    #timesheetDetailsModal tbody td{font-size:13px;color:#1e293b;padding:12px 14px;border-bottom:.5px solid #f1f5f9;vertical-align:middle;}
    #timesheetDetailsModal tbody tr:last-child td{border-bottom:none;}
    #timesheetDetailsModal tbody small{color:#94a3b8;font-size:11px;}
    #timesheetDetailsModal tfoot td{font-size:13px;font-weight:600;color:#1a2f52;padding:13px 14px;background:#f8fafc;border-top:.5px solid #e2e8f0;}
    #timesheetDetailsModal .badge{font-weight:600;font-size:11px;padding:5px 10px;border-radius:20px;}
    #timesheetDetailsModal .ts-note{background:#fffbeb;border:.5px solid #fde68a;border-radius:10px;padding:11px 16px;font-size:13px;color:#92400e;margin-top:14px;}
    #timesheetDetailsModal .ts-error{background:#fef2f2;border:.5px solid #fecaca;border-radius:10px;padding:11px 16px;font-size:13px;color:#b91c1c;}
    #timesheetDetailsModal .btn-close-soft{background:#f1f5f9;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:500;color:#475569;cursor:pointer;}
    #timesheetDetailsModal .btn-close-soft:hover{background:#e2e8f0;}
    </style>
<div class="bg-[#F4F6F9] font-inter">


<main class="w-full pb-8" style="padding-top:90px;">
    <?php
    $w = $employeeProfileWidgetData ?? [];
    $employee_name     = ucfirst($w['employee_name'] ?? 'User');
    $employee_position = $w['employee_position'] ?? '';
    $today_shift       = $w['today_shift'] ?? null;
    $shift_started     = $w['shift_started'] ?? false;
    $shift_clockin     = $w['shift_clockin_display'] ?? '--:-- --';
    $hours_this_week   = $w['hours_this_week'] ?? '0h';
    $attendance_rate   = $w['attendance_rate'] ?? 0;

    $nameParts = preg_split('/\s+/', trim($employee_name));
    $initials  = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

    $taskDays = $taskDays ?? [];
    $today    = $today ?? (int) date('d');
    $year     = $year ?? date('Y');
    $month    = $month ?? date('m');
    $sbFirstDow = date('w', strtotime("$year-$month-01"));
    $sbTotal    = date('t', strtotime("$year-$month-01"));
    $calWeeks = []; $calWeek = [];
    for ($i = 0; $i < $sbFirstDow; $i++) { $calWeek[] = ''; }
    for ($d = 1; $d <= $sbTotal; $d++) { $calWeek[] = $d; if (count($calWeek) == 7) { $calWeeks[] = $calWeek; $calWeek = []; } }
    if (!empty($calWeek)) { while (count($calWeek) < 7) $calWeek[] = ''; $calWeeks[] = $calWeek; }
    ?>

    <div class="empv3">
     <div class="db">
      <div class="layout">

       <aside class="sidebar">

        <div class="profile-block">
         <div class="av-wrap">
          <div class="av-circle"><?= htmlspecialchars($initials) ?></div>
          <div class="online-dot <?= $shift_started ? '' : 'off' ?>"></div>
         </div>
         <div class="p-name"><?= htmlspecialchars($employee_name) ?></div>
         <div class="p-sub"><?= $employee_position !== '' ? htmlspecialchars($employee_position) : 'Welcome to portal !!' ?></div>
         <?php if ($shift_started): ?>
          <div class="present-pill"><span class="g-dot"></span>Present</div>
         <?php else: ?>
          <div class="present-pill absent"><span class="g-dot"></span>Absent</div>
         <?php endif; ?>
        </div>

        <div>
         <div class="block-title">Today's schedule</div>
         <div class="sched-card">
          <?php if (!empty($today_shift) && !empty($today_shift['roster_start_time'])): ?>
           <div class="sched-time">
            <?= date('h:i A', strtotime($today_shift['roster_start_time'])) ?> &rarr;
            <?= !empty($today_shift['roster_end_time']) ? date('h:i A', strtotime($today_shift['roster_end_time'])) : '--:-- --' ?>
           </div>
           <?php if ($shift_started): ?>
            <div class="clocked"><span class="c-dot"></span>Clocked in <?= htmlspecialchars($shift_clockin) ?></div>
           <?php else: ?>
            <div class="sched-sub">Shift not started</div>
           <?php endif; ?>
          <?php else: ?>
           <div class="sched-sub">No shift scheduled today</div>
          <?php endif; ?>
         </div>
        </div>

        <div>
         <div class="block-title">Quick stats</div>
         <div class="qs-row"><span class="qs-label">Hours this week</span><span class="qs-val"><?= htmlspecialchars($hours_this_week) ?></span></div>
         <div class="qs-row"><span class="qs-label">Attendance rate</span><span class="qs-val teal"><?= htmlspecialchars($attendance_rate) ?>%</span></div>
        </div>

        <?php $leaveBalances = $leaveBalances ?? []; ?>
        <div class="leave-card" id="leave-balance-widget">
         <div class="lc-title">Leave balance</div>
         <?php if (!empty($leaveBalances)): ?>
          <?php foreach ($leaveBalances as $lb):
              $entitlement = (float) ($lb['entitlements'] ?? 0);
              $used        = (float) ($lb['used_days'] ?? 0);
              $percent     = $entitlement > 0 ? min(100, round(($used / $entitlement) * 100)) : 0;
          ?>
          <div class="lc-row">
           <div class="lc-hdr"><span class="lc-name"><?= htmlspecialchars($lb['leaveTypeName']) ?></span><span class="lc-val"><?= $used ?> / <?= $entitlement ?> days</span></div>
           <div class="lc-bar"><div class="lc-fill" style="width:<?= $percent ?>%"></div></div>
          </div>
          <?php endforeach; ?>
         <?php else: ?>
          <div class="lc-name">No leave types configured.</div>
         <?php endif; ?>
         <button type="button" class="apply-btn" data-bs-toggle="modal" data-bs-target="#requestLeaveModal">Apply for leave</button>
        </div>

        <div class="cal-wrap">
         <div class="cal-hdr">
          <div class="cal-month"><?= date('F Y', strtotime("$year-$month-01")) ?></div>
         </div>
         <div class="cal-grid">
          <?php foreach (['Su','Mo','Tu','We','Th','Fr','Sa'] as $dh): ?>
           <div class="cal-dh"><?= $dh ?></div>
          <?php endforeach; ?>
          <?php foreach ($calWeeks as $cw): foreach ($cw as $cd): ?>
           <?php if ($cd === ''): ?>
            <div class="cal-d empty"></div>
           <?php else: $isToday = ($cd == $today); $hasTask = isset($taskDays[$cd]); ?>
            <div class="cal-d <?= $isToday ? 'today' : ($hasTask ? 'leave' : '') ?>"><?= $cd ?></div>
           <?php endif; ?>
          <?php endforeach; endforeach; ?>
         </div>
         <div class="cal-legend">
          <div class="leg"><span class="leg-d" style="background:#25A69A;"></span>Leave / Today</div>
         </div>
        </div>

       </aside>



       <div class="main">

        <div class="stat-row">
         <a href="<?= base_url('HR/myLeaves') ?>" class="stat-card" style="text-decoration:none;color:inherit;cursor:pointer;"><div class="stat-num"><?= (int) ($leaveRequestCount ?? 0) ?></div><div class="stat-label">Leave requests <i class="fa-solid fa-arrow-right" style="font-size:10px;margin-left:4px;"></i></div></a>
         <div class="stat-card"><div class="stat-num"><?= (int) ($upcomingShiftsCount ?? 0) ?></div><div class="stat-label">Upcoming shifts</div></div>
         <div class="stat-card"><div class="stat-present <?= $shift_started ? '' : 'absent' ?>"><?= $shift_started ? 'Present' : 'Absent' ?></div><div class="stat-label">Attendance today</div></div>
        </div>

         
    
        <div class="two-col">

         <?php
         if (!isset($attendance) || !is_array($attendance)) {
             $attendance = ['clock_in'=>'--:-- --','break_start'=>'--:-- --','resume'=>'--:-- --','clock_out'=>'--:-- --','worked_label'=>'0m','target_label'=>'8h 00m','progress_percent'=>0];
         }
         $clockIn = $attendance['clock_in'] ?? '--:-- --';
         $breakStart = $attendance['break_start'] ?? '--:-- --';
         $resume = $attendance['resume'] ?? '--:-- --';
         $clockOut = $attendance['clock_out'] ?? '--:-- --';
         $workedLabel = $attendance['worked_label'] ?? '0m';
         $targetLabel = $attendance['target_label'] ?? '8h 00m';
         $progressPercent = max(0, min(100, (int) ($attendance['progress_percent'] ?? 0)));
         ?>
         <div class="card" id="attendance-timeline">
          <div class="card-hdr"><div class="card-title">Today's attendance</div></div>
          <div class="tl-row">
           <div class="tl-col"><div class="tl-lbl">Clock in</div><div class="tl-v <?= ($clockIn==='--:-- --')?'dim':'' ?>"><?= htmlspecialchars($clockIn) ?></div></div>
           <div class="tl-col"><div class="tl-lbl">Break</div><div class="tl-v <?= ($breakStart==='--:-- --')?'dim':'orange' ?>"><?= htmlspecialchars($breakStart) ?></div></div>
           <div class="tl-col"><div class="tl-lbl">Resume</div><div class="tl-v <?= ($resume==='--:-- --')?'dim':'orange' ?>"><?= htmlspecialchars($resume) ?></div></div>
           <div class="tl-col"><div class="tl-lbl">Clock out</div><div class="tl-v <?= ($clockOut==='--:-- --')?'dim':'' ?>"><?= htmlspecialchars($clockOut) ?></div></div>
          </div>
          <div class="prog-track"><div class="prog-fill" style="width:<?= $progressPercent ?>%"></div></div>
          <div class="tl-foot"><span style="font-size:12px;font-weight:600;color:#1e293b;"><?= htmlspecialchars($workedLabel) ?> worked</span><span style="font-size:12px;color:#94a3b8;">Target: <?= htmlspecialchars($targetLabel) ?></span></div>
         </div>

         <?php $timesheets = $employeeTimesheets ?? []; $tsTodayTs = strtotime(date('Y-m-d')); ?>
         <div class="card" id="latest-timesheet-section">
          <div class="card-hdr"><div class="card-title">My timesheets</div><button type="button" class="card-link">View all</button></div>
          <table class="ts-tbl">
           <thead><tr><th style="width:64%;">Period</th><th></th></tr></thead>
           <tbody>
           <?php if (!empty($timesheets)): ?>
            <?php foreach ($timesheets as $t):
                $fromTs = strtotime($t['date_from']);
                $diffDays = floor(($tsTodayTs - $fromTs) / 86400);
                if ($diffDays < 0)      $wkLabel = 'Upcoming';
                elseif ($diffDays < 7)  $wkLabel = 'Current week';
                elseif ($diffDays < 14) $wkLabel = 'Last week';
                else                    $wkLabel = floor($diffDays / 7) . ' weeks ago';
            ?>
            <tr>
             <td>
              <div style="font-size:12px;color:#1e293b;"><?= date('M d', $fromTs) ?> &ndash; <?= date('M d, Y', strtotime($t['date_to'])) ?></div>
              <div style="font-size:11px;color:#94a3b8;margin-top:2px;"><?= $wkLabel ?></div>
             </td>
             <td style="text-align:right;">
              <button type="button" class="view-btn view-timesheet-details"
                      data-week-start="<?= $t['date_from'] ?>" data-week-end="<?= $t['date_to'] ?>" data-emp-id="<?= $empId ?>">
               <span class="btn-text">View</span>
               <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
             </td>
            </tr>
            <?php endforeach; ?>
           <?php else: ?>
            <tr><td colspan="2" style="text-align:center;color:#94a3b8;padding:14px 0;">No timesheets found.</td></tr>
           <?php endif; ?>
           </tbody>
          </table>
         </div>

        </div>
            
        <?php
        $availWeekly = [];
        if (!empty($availability) && isset($availability[0]['weekly_json'])) {
            $availWeekly = json_decode($availability[0]['weekly_json'], true) ?: [];
        }
        $availVisible = ['mon'=>'Monday','tue'=>'Tuesday','wed'=>'Wednesday','thu'=>'Thursday','fri'=>'Friday','sat'=>'Saturday','sun'=>'Sunday'];
        $renderTimeSelect = function ($name, $value) {
            $value = trim((string) $value);
            $display = $value;
            if ($value !== '' && ($ts = strtotime($value)) !== false) { $display = date('h:i A', $ts); }
            $opts = [];
            for ($h = 0; $h < 24; $h++) { foreach ([0, 30] as $m) { $opts[] = date('h:i A', mktime($h, $m, 0)); } }
            $html = '<select class="time-sel" name="' . htmlspecialchars($name) . '"><option value="">--</option>';
            $matched = false;
            foreach ($opts as $o) {
                $sel = ($o === $display) ? ' selected' : '';
                if ($sel) $matched = true;
                $html .= '<option value="' . htmlspecialchars($o) . '"' . $sel . '>' . htmlspecialchars($o) . '</option>';
            }
            if (!$matched && $display !== '') {
                $html .= '<option value="' . htmlspecialchars($display) . '" selected>' . htmlspecialchars($display) . '</option>';
            }
            return $html . '</select>';
        };
        ?>
        <div class="avail-card" id="availability-widget">
         <div class="avail-hdr">
          <div>
           <div class="card-title" style="font-size:14px;">My availability</div>
           <div class="avail-sub">Set your weekly availability</div>
          </div>
          <div style="display:flex;align-items:center;gap:18px;">
           <label class="same-all-toggle">
            <input type="checkbox" id="sameAllDays">
            <span class="sat-slider"></span>
            <span class="sat-txt">Same for all days</span>
           </label>
           <button type="submit" form="dashboardAvailabilityForm" class="save-btn">
            <span class="dash-avail-text">Update availability</span>
            <span class="spinner-border spinner-border-sm d-none" id="dashAvailLoader" role="status" aria-hidden="true"></span>
           </button>
          </div>
         </div>
         <form id="dashboardAvailabilityForm">
          <input type="hidden" name="emp_id" value="<?= htmlspecialchars($empId ?? '') ?>">
          <input type="hidden" name="same_hours" value="0">
          <div class="days-grid">
           <?php foreach ($availVisible as $key => $label): ?>
            <div class="day-col">
             <div class="day-name"><?= $label ?></div>
             <div class="time-label">From</div>
             <?= $renderTimeSelect("weekly[$key][start]", $availWeekly[$key]['start'] ?? '') ?>
             <div class="to-divider">to</div>
             <div class="time-label">Until</div>
             <?= $renderTimeSelect("weekly[$key][end]", $availWeekly[$key]['end'] ?? '') ?>
            </div>
           <?php endforeach; ?>
          </div>
          <div id="dashAvailMsg" class="text-xs mt-3 d-none"></div>
         </form>
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
     
     <!-- Common system loader -->
     <div id="ts-loader-overlay"><div class="ts-spinner"></div></div>

     <!-- Timesheet Details Modal -->
     <div class="modal fade" id="timesheetDetailsModal" tabindex="-1" aria-labelledby="timesheetDetailsModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="timesheetDetailsModalLabel">Timesheet details</h5>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div id="timesheet-content">
                         <div class="ts-week-banner"><span>Week</span><strong id="modal-date-range"></strong></div>
                         <div class="ts-table-wrap">
                             <table>
                                 <thead>
                                     <tr>
                                         <th>Date</th>
                                         <th>Clock In</th>
                                         <th>Clock Out</th>
                                         <th>Break</th>
                                         <th>Hours</th>
                                         <th>Location</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody id="timesheet-table-body"></tbody>
                                 <tfoot>
                                     <tr>
                                         <td colspan="4" style="text-align:right;">Total hours</td>
                                         <td id="total-hours-worked">0h 0m</td>
                                         <td colspan="2"></td>
                                     </tr>
                                 </tfoot>
                             </table>
                         </div>
                         <div id="no-timesheet-data" class="ts-note d-none">No timesheet records found for this week.</div>
                     </div>
                     <div id="timesheet-error" class="ts-error d-none"><span id="error-message"></span></div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn-close-soft" data-bs-dismiss="modal">Close</button>
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
             
             // Reset modal content state
             $('#timesheet-content').removeClass('d-none');
             $('#timesheet-error').addClass('d-none');
             $('#no-timesheet-data').addClass('d-none');
             
             // Show common system loader before modal opens
             $('#ts-loader-overlay').addClass('show');
             
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
                         
                         $('#timesheet-content').removeClass('d-none');
                         $('#timesheet-error').addClass('d-none');
                     } else {
                         $('#timesheet-content').addClass('d-none');
                         $('#timesheet-error').removeClass('d-none');
                         $('#error-message').text(response.message || 'Failed to load timesheet data');
                     }
                     // Open modal now that content is ready
                     (new bootstrap.Modal(document.getElementById('timesheetDetailsModal'))).show();
                 },
                 error: function(xhr, status, error) {
                     $('#timesheet-content').addClass('d-none');
                     $('#timesheet-error').removeClass('d-none');
                     $('#error-message').text('An error occurred while loading timesheet data. Please try again.');
                     (new bootstrap.Modal(document.getElementById('timesheetDetailsModal'))).show();
                     console.error('Error:', error);
                 },
                 complete: function() {
                     // Hide common loader + reset button
                     $('#ts-loader-overlay').removeClass('show');
                     btn.prop('disabled', false);
                     btn.find('.btn-text').text('View');
                     btn.find('.spinner-border').addClass('d-none');
                 }
             });
         });
         
         // Same-for-all-days availability toggle
         function applySameForAllDays() {
             var $f = $('#dashboardAvailabilityForm');
             var ms = $f.find('select[name="weekly[mon][start]"]').val();
             var me = $f.find('select[name="weekly[mon][end]"]').val();
             ['tue','wed','thu','fri','sat','sun'].forEach(function(d) {
                 $f.find('select[name="weekly[' + d + '][start]"]').val(ms);
                 $f.find('select[name="weekly[' + d + '][end]"]').val(me);
             });
         }
         $('#sameAllDays').on('change', function() {
             if ($(this).is(':checked')) { applySameForAllDays(); }
         });
         $('#dashboardAvailabilityForm').on('change', 'select[name="weekly[mon][start]"], select[name="weekly[mon][end]"]', function() {
             if ($('#sameAllDays').is(':checked')) { applySameForAllDays(); }
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
                     label.text('Update availability');
                 }
             });
         });
     });
     </script>
     
</main>

</div>