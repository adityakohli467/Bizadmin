<?php /* Manager / Admin dashboard: rendered inside the shared Velzon layout (header > content > footer).
        Visual language matched to the employee dashboard (.empv3). Sections & data unchanged. */ ?>
<link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
<?php $this->load->view('general/tailwind_common_assets'); ?>
<style>
    .mgrv3{font-family:'Inter',system-ui,sans-serif;}
    .mgrv3 *{box-sizing:border-box;}
    .mgrv3 .db{background:#f1f5f9;border-radius:16px;overflow:hidden;border:.5px solid #e2e8f0;}
    .mgrv3 .layout{display:flex;min-height:700px;}
    .mgrv3 .sidebar{width:260px;flex-shrink:0;background:#fff;border-right:.5px solid #e2e8f0;padding:20px 20px 20px 20px;display:flex;flex-direction:column;gap:18px;}
    .mgrv3 .profile-block{text-align:center;padding-bottom:16px;border-bottom:.5px solid #f1f5f9;}
    .mgrv3 .av-wrap{position:relative;width:64px;height:64px;margin:0 auto 10px;}
    .mgrv3 .av-circle{width:64px;height:64px;border-radius:50%;border:2.5px solid #1D9E75;background:#0F6E56;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:600;}
    .mgrv3 .online-dot{width:12px;height:12px;background:#22c55e;border-radius:50%;border:2.5px solid #fff;position:absolute;bottom:2px;right:2px;}
    .mgrv3 .p-name{font-size:15px;font-weight:600;color:#1e293b;}
    .mgrv3 .p-sub{font-size:12px;color:#94a3b8;margin-top:3px;}
    .mgrv3 .role-pill{display:inline-flex;align-items:center;gap:5px;background:#E1F5EE;border:.5px solid #9FE1CB;border-radius:20px;padding:4px 12px;font-size:11px;color:#0F6E56;margin-top:8px;font-weight:600;}
    .mgrv3 .block-title{font-size:12px;font-weight:600;color:#1e293b;margin-bottom:8px;}
    .mgrv3 .qs-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:.5px solid #f1f5f9;}
    .mgrv3 .qs-row:last-child{border-bottom:none;}
    .mgrv3 .qs-label{font-size:12px;color:#64748b;}
    .mgrv3 .qs-val{font-size:13px;font-weight:600;color:#1e293b;}
    .mgrv3 .qs-val.green{color:#0F6E56;}
    .mgrv3 .qs-val.orange{color:#c2410c;}
    .mgrv3 .qs-val.blue{color:#1a2f52;}
    .mgrv3 .act-btn{display:flex;align-items:center;gap:10px;width:100%;background:#f8fafc;border:.5px solid #e2e8f0;border-radius:9px;padding:10px 12px;font-size:12px;font-weight:600;color:#1e293b;cursor:pointer;text-decoration:none;transition:background .15s,border-color .15s;}
    .mgrv3 .act-btn:hover{background:#E1F5EE;border-color:#9FE1CB;color:#0F6E56;}
    .mgrv3 .act-btn i{width:18px;text-align:center;color:#1D9E75;font-size:14px;}
    .mgrv3 .act-btn.danger i{color:#ef4444;}
    .mgrv3 .act-btn.danger:hover{background:#fef2f2;border-color:#fecaca;color:#b91c1c;}
    .mgrv3 .main{flex:1;padding:20px 20px 20px 20px;display:flex;flex-direction:column;gap:16px;min-width:0;}
    .mgrv3 .stat-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;}
    .mgrv3 .stat-card{background:#fff;border-radius:12px;border:.5px solid #e2e8f0;padding:16px 18px;}
    .mgrv3 .stat-card.alert{border:.5px solid #e2e8f0;}
    .mgrv3 .stat-ic{font-size:20px;height:24px;line-height:24px;margin-bottom:8px;display:block;}
    .mgrv3 .stat-num{font-size:28px;font-weight:600;color:#1a2f52;}
    .mgrv3 .stat-num.red{color:#dc2626;}
    .mgrv3 .stat-label{font-size:12px;color:#64748b;margin-top:3px;}
    .mgrv3 .two-col{display:grid;grid-template-columns:3fr 2fr;gap:14px;}
    .mgrv3 .card{background:#fff;border-radius:12px;border:.5px solid #e2e8f0;padding:18px 20px;}
    .mgrv3 .card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px;}
    .mgrv3 .card-title{font-size:13px;font-weight:600;color:#1e293b;}
    .mgrv3 .card-link{font-size:12px;color:#1D9E75;font-weight:600;cursor:pointer;background:none;border:none;padding:0;text-decoration:none;}
    .mgrv3 .feed{display:flex;flex-direction:column;gap:10px;max-height:340px;overflow-y:auto;}
    .mgrv3 .feed-item{display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border-radius:10px;border-left:3px solid #cbd5e1;background:#f8fafc;}
    .mgrv3 .feed-item.red{background:#fef2f2;border-left-color:#ef4444;}
    .mgrv3 .feed-item.orange{background:#fff7ed;border-left-color:#f97316;}
    .mgrv3 .feed-item.purple{background:#faf5ff;border-left-color:#a855f7;}
    .mgrv3 .feed-item i{margin-top:2px;font-size:13px;}
    .mgrv3 .feed-item.red i{color:#ef4444;}
    .mgrv3 .feed-item.orange i{color:#f97316;}
    .mgrv3 .feed-item.purple i{color:#a855f7;}
    .mgrv3 .feed-t{font-size:12px;font-weight:600;color:#1e293b;}
    .mgrv3 .feed-s{font-size:11px;color:#94a3b8;margin-top:2px;}
    .mgrv3 .feed-empty{font-size:12px;color:#94a3b8;text-align:center;padding:24px 0;}
    .mgrv3 .add-btn{display:block;width:100%;background:#1D9E75;border:none;border-radius:9px;padding:9px;font-size:12px;color:#fff;font-weight:600;margin-top:12px;text-align:center;cursor:pointer;text-decoration:none;}
    .mgrv3 .add-btn:hover{background:#0F6E56;color:#fff;}
    .mgrv3 .ts-tbl{width:100%;border-collapse:collapse;}
    .mgrv3 .ts-tbl th{font-size:11px;color:#94a3b8;font-weight:600;text-align:left;padding:0 12px 10px;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;}
    .mgrv3 .ts-tbl th.c,.mgrv3 .ts-tbl td.c{text-align:center;}
    .mgrv3 .ts-tbl td{font-size:12px;color:#1e293b;padding:11px 12px;border-bottom:.5px solid #f1f5f9;vertical-align:middle;white-space:nowrap;}
    .mgrv3 .ts-tbl tbody tr:last-child td{border-bottom:none;}
    .mgrv3 .ts-tbl tbody tr:hover td{background:#f8fafc;}
    .mgrv3 .badge-pill{display:inline-block;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;}
    .mgrv3 .badge-pill.present{background:#f0fdf4;color:#15803d;}
    .mgrv3 .badge-pill.absent{background:#fef2f2;color:#b91c1c;}
    .mgrv3 .tbl-empty{font-size:12px;color:#94a3b8;text-align:center;padding:24px 0;}
    @media(max-width:1100px){.mgrv3 .layout{flex-direction:column;}.mgrv3 .sidebar{width:100%;border-right:none;border-bottom:.5px solid #e2e8f0;}.mgrv3 .two-col{grid-template-columns:1fr;}.mgrv3 .stat-row{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:560px){.mgrv3 .stat-row{grid-template-columns:1fr;}}
</style>
<div class="bg-[#F4F6F9] font-inter">

<?php
    // ---------- Manager identity (session-backed, with safe fallbacks) ----------
    $mgrName     = trim((string) $this->session->userdata('username')) ?: 'Manager';
    $mgrLocation = trim((string) $this->session->userdata('location_name'));
    $mgrRole     = $this->ion_auth->in_group('admin') ? 'Admin' : 'Manager';
    $mgrParts    = preg_split('/\s+/', $mgrName);
    $mgrInitials = strtoupper(substr($mgrParts[0] ?? 'M', 0, 1) . (isset($mgrParts[1]) ? substr($mgrParts[1], 0, 1) : ''));
    if ($mgrInitials === '') { $mgrInitials = 'M'; }

    // ---------- Section data (unchanged) ----------
    $birthdays_today         = $birthdays_today ?? [];
    $pending_leaves          = $pending_leaves ?? [];
    $task_summary            = $task_summary ?? ['completed_today' => 0, 'in_progress' => 0];
    $employee_on_break_count = $employee_on_break_count ?? 0;
    $attendance_today        = $attendance_today ?? [];
    $present_today           = $present_today ?? 0;
    $total_employees         = $total_employees ?? 0;
    $incident_reports        = $incident_reports ?? [];
    $injury_reports          = $injury_reports ?? [];
    $total_team_hours        = $total_team_hours ?? 0;

    $feedCount = count($incident_reports) + count($injury_reports) + count($pending_leaves) + count($birthdays_today);
?>

<main class="max-w-[1920px] mx-auto px-6 pb-8" style="padding-top:90px;">
 <div class="mgrv3">
  <div class="db">
   <div class="layout">

    <!-- ============ SIDEBAR ============ -->
    <aside class="sidebar">

     <div class="profile-block">
      <div class="av-wrap">
       <div class="av-circle"><?= htmlspecialchars($mgrInitials) ?></div>
       <div class="online-dot"></div>
      </div>
      <div class="p-name"><?= htmlspecialchars(ucfirst($mgrName)) ?></div>
      <div class="p-sub"><?= $mgrLocation !== '' ? htmlspecialchars($mgrLocation) : date('D, d F Y') ?></div>
      <div class="role-pill"><i class="fa-solid fa-user-shield"></i><?= $mgrRole ?></div>
     </div>

     <div>
      <div class="block-title">Cafe Staff Today's Status</div>
      <div class="qs-row"><span class="qs-label">Total Members</span><span class="qs-val blue"><?= (int) $total_employees ?></span></div>
      <div class="qs-row"><span class="qs-label">Present Today</span><span class="qs-val green"><?= (int) $present_today ?></span></div>
      <div class="qs-row"><span class="qs-label">On Leave</span><span class="qs-val orange"><?= count($pending_leaves) ?></span></div>
     </div>

     <div>
      <div class="block-title">Quick Actions</div>
      <div style="display:flex;flex-direction:column;gap:8px;">
       <a class="act-btn danger" href="<?= base_url('HR/leaveDashbaord') ?>"><i class="fa-solid fa-check-double"></i> Approve Leaves</a>
       <a class="act-btn" href="<?= base_url('HR/timesheetWithoutRoster') ?>"><i class="fa-solid fa-clock-rotate-left"></i> Approve Timesheets</a>
       <a class="act-btn" href="<?= base_url('HR/employees') ?>"><i class="fa-solid fa-list-check"></i> View All Employees</a>
       <a class="act-btn" href="<?= base_url('HR/memo') ?>"><i class="fa-solid fa-bullhorn"></i> Send Memo</a>
      </div>
     </div>

    </aside>

    <!-- ============ MAIN ============ -->
    <div class="main">

     <!-- Quick glance stat cards -->
     <div class="stat-row">
      <div class="stat-card">
       <i class="fa-solid fa-cake-candles stat-ic" style="color:#a855f7;"></i>
       <div class="stat-num"><?= count($birthdays_today) ?></div>
       <div class="stat-label">Today's Birthdays</div>
      </div>
      <div class="stat-card">
       <i class="fa-solid fa-circle-check stat-ic" style="color:#22c55e;"></i>
       <div class="stat-num"><?= (int) ($task_summary['completed_today'] ?? 0) ?></div>
       <div class="stat-label">Tasks Completed</div>
      </div>
      <div class="stat-card">
       <i class="fa-solid fa-mug-hot stat-ic" style="color:#f97316;"></i>
       <div class="stat-num"><?= (int) $employee_on_break_count ?></div>
       <div class="stat-label">Employee on Break</div>
      </div>
      <div class="stat-card ">
       <i class="fa-solid fa-calendar-minus stat-ic" style="color:#ef4444;"></i>
       <div class="stat-num red"><?= count($pending_leaves) ?></div>
       <div class="stat-label">Leave Requests</div>
      </div>
     </div>

     <!-- What's Happening + Team Tasks -->
     <div class="two-col">

      <div class="card">
       <div class="card-hdr"><div class="card-title">What's Happening</div></div>
       <div class="feed">
        <?php if ($feedCount > 0): ?>
         <?php foreach ($incident_reports as $inc): ?>
          <div class="feed-item red">
           <i class="fa-solid fa-triangle-exclamation"></i>
           <div><div class="feed-t">New incident report filed</div><div class="feed-s"><?= htmlspecialchars(trim(($inc->first_name ?? '') . ' ' . ($inc->last_name ?? ''))) ?></div></div>
          </div>
         <?php endforeach; ?>
         <?php foreach ($injury_reports as $inj): ?>
          <div class="feed-item red">
           <i class="fa-solid fa-triangle-exclamation"></i>
           <div><div class="feed-t">New injury report filed</div><div class="feed-s"><?= htmlspecialchars(trim(($inj->first_name ?? '') . ' ' . ($inj->last_name ?? ''))) ?></div></div>
          </div>
         <?php endforeach; ?>
         <?php foreach ($pending_leaves as $lv): ?>
          <div class="feed-item orange">
           <i class="fa-solid fa-hourglass-half"></i>
           <div><div class="feed-t">Leave request: <?= htmlspecialchars($lv->start_date ?? '') ?></div><div class="feed-s"><?= htmlspecialchars(trim(($lv->first_name ?? '') . ' ' . ($lv->last_name ?? ''))) ?></div></div>
          </div>
         <?php endforeach; ?>
         <?php foreach ($birthdays_today as $b): ?>
          <div class="feed-item purple">
           <i class="fa-solid fa-cake-candles"></i>
           <div><div class="feed-t">Birthday today</div><div class="feed-s"><?= htmlspecialchars(trim(($b->first_name ?? '') . ' ' . ($b->last_name ?? ''))) ?></div></div>
          </div>
         <?php endforeach; ?>
        <?php else: ?>
         <div class="feed-empty">Nothing new to report today.</div>
        <?php endif; ?>
       </div>
       <a class="add-btn" href="<?= base_url('HR/memo') ?>"><i class="fa-solid fa-plus me-1"></i> Add Memo</a>
      </div>

      <div class="card">
       <div class="card-hdr">
        <div class="card-title">Team Tasks Overview</div>
        <a class="card-link" href="#attendance-timeline">Today's Attendance</a>
       </div>
       <div class="qs-row"><span class="qs-label">Completed Today</span><span class="qs-val green" style="font-size:16px;"><?= (int) ($task_summary['completed_today'] ?? 0) ?></span></div>
       <div class="qs-row"><span class="qs-label">In Progress</span><span class="qs-val blue" style="font-size:16px;"><?= (int) ($task_summary['in_progress'] ?? 0) ?></span></div>
       <div class="qs-row"><span class="qs-label">Total Team Hours</span><span class="qs-val green" style="font-size:16px;"><?= htmlspecialchars((string) $total_team_hours) ?>h</span></div>
      </div>

     </div>

     <!-- Today's Team Attendance Timeline -->
     <section id="attendance-timeline" class="card">
      <div class="card-hdr">
       <div class="card-title">Today's Team Attendance Timeline</div>
       <div class="card-link" style="cursor:default;">Total Team Hours: <?= htmlspecialchars((string) $total_team_hours) ?>h</div>
      </div>
      <div style="overflow-x:auto;">
       <table class="ts-tbl">
        <thead>
         <tr>
          <th>Employee</th>
          <th class="c">Prep Area</th>
          <th class="c">Clock In</th>
          <th class="c">Break</th>
          <th class="c">Clock Out</th>
          <th class="c">Total Hours</th>
          <th class="c">Status</th>
         </tr>
        </thead>
        <tbody>
         <?php if (!empty($attendance_today)): ?>
          <?php foreach ($attendance_today as $row): ?>
           <?php
            $status    = ($row->clock_in_time != '') ? 'Present' : 'Absent';
            $statusCls = ($row->clock_in_time != '') ? 'present' : 'absent';
            $totalHrs  = '-';
            if (!empty($row->clock_in_time) && !empty($row->clock_out_time)) {
                $inTs  = strtotime($row->clock_in_time);
                $outTs = strtotime($row->clock_out_time);
                if ($outTs <= $inTs) { $outTs += 86400; }
                $totalHrs = round(($outTs - $inTs) / 3600, 2) . 'h';
            }
           ?>
           <tr>
            <td><span style="font-weight:600;color:#1e293b;"><?= htmlspecialchars(trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''))) ?></span></td>
            <td class="c"><?= htmlspecialchars($row->prep_name ?? '-') ?></td>
            <td class="c"><?= $row->clock_in_time ? date('h:i A', strtotime($row->clock_in_time)) : '-' ?></td>
            <td class="c"><?= htmlspecialchars($row->roster_break_start_time ?: '-') ?></td>
            <td class="c"><?= $row->clock_out_time ? date('h:i A', strtotime($row->clock_out_time)) : '-' ?></td>
            <td class="c" style="font-weight:600;"><?= $totalHrs ?></td>
            <td class="c"><span class="badge-pill <?= $statusCls ?>"><?= $status ?></span></td>
           </tr>
          <?php endforeach; ?>
         <?php else: ?>
          <tr><td colspan="7" class="tbl-empty">No attendance records for today.</td></tr>
         <?php endif; ?>
        </tbody>
       </table>
      </div>
     </section>

    </div>
   </div>
  </div>
 </div>
</main>

</div>
