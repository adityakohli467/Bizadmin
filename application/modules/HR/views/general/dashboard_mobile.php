<?php
/**
 * Mobile employee dashboard (self-contained).
 * Pixel-matched to bizadmin_mobile_v2 reference. Data is dynamic, mirrors general/dashboard.php.
 */

// ---------- Profile widget data ----------
$w                 = $employeeProfileWidgetData ?? [];
$employee_name     = trim(ucfirst($w['employee_name'] ?? 'User'));
$today_shift       = $w['today_shift'] ?? null;
$shift_started     = $w['shift_started'] ?? false;
$shift_clockin     = $w['shift_clockin_display'] ?? '--:-- --';
$hours_this_week   = $w['hours_this_week'] ?? '0h';
$attendance_rate   = $w['attendance_rate'] ?? 0;

// ---------- Initials ----------
$nameParts = preg_split('/\s+/', $employee_name);
$initials  = strtoupper(substr($nameParts[0] ?? '', 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
if ($initials === '') { $initials = 'U'; }

// ---------- Top counts ----------
$leaveReqCount   = (int) ($leaveRequestCount ?? 0);
$upcomingShifts  = (int) ($upcomingShiftsCount ?? 0);

// ---------- Today's schedule ----------
$schedTime  = '';
$schedSub   = '';
if (!empty($today_shift) && !empty($today_shift['roster_start_time'])) {
    $startTs = strtotime($today_shift['roster_start_time']);
    $endTs   = !empty($today_shift['roster_end_time']) ? strtotime($today_shift['roster_end_time']) : null;
    $schedTime = date('h:i A', $startTs) . ' &rarr; ' . ($endTs ? date('h:i A', $endTs) : '--:-- --');

    $startH = (int) date('G', $startTs);
    $period = $startH < 12 ? 'Morning' : ($startH < 17 ? 'Afternoon' : 'Evening');
    if ($endTs) {
        $hrs = round(($endTs - $startTs) / 3600, 1);
        $hrs = rtrim(rtrim(number_format($hrs, 1), '0'), '.');
        $schedSub = $period . ' shift &middot; ' . $hrs . ' hrs';
    } else {
        $schedSub = $period . ' shift';
    }
}

// ---------- Attendance timeline ----------
if (!isset($attendance) || !is_array($attendance)) {
    $attendance = [
        'clock_in' => '--:--', 'break_start' => '--:--', 'resume' => '--:--',
        'clock_out' => '--:--', 'worked_label' => '0m', 'target_label' => '8h 00m', 'progress_percent' => 0
    ];
}
$clockIn   = $attendance['clock_in']   ?? '--:--';
$breakStart= $attendance['break_start']?? '--:--';
$resume    = $attendance['resume']     ?? '--:--';
$clockOut  = $attendance['clock_out']  ?? '--:--';
$workedLbl = $attendance['worked_label'] ?? '0m';
$targetLbl = $attendance['target_label'] ?? '8h 00m';
$progress  = max(0, min(100, (int) ($attendance['progress_percent'] ?? 0)));

// ---------- Availability ----------
$availWeekly = [];
if (!empty($availability) && isset($availability[0]['weekly_json'])) {
    $availWeekly = json_decode($availability[0]['weekly_json'], true) ?: [];
}
$availDays = ['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'];

$renderTimeSelect = function ($name, $value) {
    $opts = [];
    for ($h = 0; $h < 24; $h++) {
        foreach ([0, 30] as $m) {
            $opts[] = date('h:i A', mktime($h, $m, 0));
        }
    }
    $value = trim((string) $value);
    $display = $value;
    if ($value !== '' && ($ts = strtotime($value)) !== false) { $display = date('h:i A', $ts); }
    $html  = '<select class="time-sel" name="' . htmlspecialchars($name) . '"><option value="">--</option>';
    $matched = false;
    foreach ($opts as $o) {
        $sel  = ($o === $display) ? ' selected' : '';
        if ($sel) $matched = true;
        $html .= '<option' . $sel . ' value="' . htmlspecialchars($o) . '">' . htmlspecialchars($o) . '</option>';
    }
    if (!$matched && $display !== '') {
        $html .= '<option value="' . htmlspecialchars($display) . '" selected>' . htmlspecialchars($display) . '</option>';
    }
    return $html . '</select>';
};

// ---------- Timesheets ----------
$timesheets = $employeeTimesheets ?? [];
$curMon     = strtotime('monday this week');

// ---------- Leave balances ----------
$leaveBalances = $leaveBalances ?? [];

$empId = $empId ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BizAdmin</title>
    <link rel="shortcut icon" href="<?= base_url(); ?>login-assets/img/favicon.jpeg" />
    <link href="<?= base_url('theme-assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/dist/tabler-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:#f1f5f9;}
        .screen{background:#f1f5f9;min-height:100vh;max-width:480px;margin:0 auto;overflow:hidden;}
        .topnav{background:#1a2f52;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;}
        .nav-left{display:flex;align-items:center;gap:10px;}
        .hamburger{color:rgba(255,255,255,.85);font-size:18px;cursor:pointer;background:none;border:none;display:flex;}
        .brand{color:#fff;font-size:14px;font-weight:500;}
        .brand-logo{height:26px;width:auto;display:block;}
        .av-link{display:inline-flex;text-decoration:none;}
        .nav-right{display:flex;align-items:center;gap:10px;}
        .bell{color:rgba(255,255,255,.7);font-size:17px;position:relative;}
        .notif-dot{width:7px;height:7px;background:#ef4444;border-radius:50%;position:absolute;top:-1px;right:-1px;border:1.5px solid #1a2f52;}
        .av-sm{width:28px;height:28px;border-radius:50%;background:#25A69A;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:500;}

        .profile-strip{background:#1a2f52;padding:0 16px 14px;display:flex;align-items:center;gap:12px;}
        .avatar-ring{width:44px;height:44px;border-radius:50%;border:2px solid #25A69A;background:#0F6E56;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:500;flex-shrink:0;position:relative;}
        .online-dot{width:9px;height:9px;background:#22c55e;border-radius:50%;border:2px solid #1a2f52;position:absolute;bottom:1px;right:1px;}
        .p-name{color:#fff;font-size:13px;font-weight:500;}
        .p-sub{color:rgba(255,255,255,.5);font-size:10px;margin-top:2px;}
        .present-badge{margin-left:auto;background:#0F6E56;color:#9FE1CB;font-size:10px;padding:3px 9px;border-radius:20px;font-weight:500;white-space:nowrap;}
        .present-badge.absent{background:#7f1d1d;color:#fca5a5;}

        .content{padding:12px 12px 28px;display:flex;flex-direction:column;gap:10px;}

        .stat-row{display:flex;gap:7px;}
        .stat-card{flex:1;background:#fff;border-radius:10px;border:.5px solid #e2e8f0;padding:10px 7px;text-align:center;}
        .stat-num{font-size:20px;font-weight:500;color:#1a2f52;}
        .stat-label{font-size:10px;color:#64748b;margin-top:2px;line-height:1.3;}
        .stat-present{font-size:13px;font-weight:500;color:#0F6E56;}
        .stat-present.off{color:#94a3b8;}

        .card{background:#fff;border-radius:12px;border:.5px solid #e2e8f0;padding:14px;}
        .card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
        .card-title{font-size:12px;font-weight:500;color:#1e293b;}
        .card-link{font-size:11px;color:#25A69A;font-weight:500;cursor:pointer;text-decoration:none;}

        .sched-time{font-size:13px;font-weight:500;color:#1e293b;}
        .sched-sub{font-size:11px;color:#64748b;margin-top:3px;}
        .clocked-pill{display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;border:.5px solid #bbf7d0;border-radius:20px;padding:4px 10px;font-size:11px;color:#15803d;margin-top:8px;}
        .c-dot{width:5px;height:5px;border-radius:50%;background:#22c55e;flex-shrink:0;}

        .tl-cols{display:flex;justify-content:space-between;margin-bottom:7px;}
        .tl-col{text-align:center;}
        .tl-lbl{font-size:10px;color:#94a3b8;}
        .tl-v{font-size:12px;font-weight:500;color:#25A69A;margin-top:2px;}
        .tl-v.dim{color:#94a3b8;}
        .prog-track{height:6px;background:#f1f5f9;border-radius:10px;overflow:hidden;margin-bottom:7px;}
        .prog-fill{height:100%;background:#25A69A;border-radius:10px;}
        .tl-foot{display:flex;justify-content:space-between;}

        .ts-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:.5px solid #f1f5f9;}
        .ts-row:last-child{border-bottom:none;padding-bottom:0;}
        .ts-dates{font-size:12px;color:#1e293b;}
        .ts-range{font-size:10px;color:#94a3b8;margin-top:2px;}
        .view-btn{background:#E1F5EE;color:#0F6E56;border:none;border-radius:7px;padding:5px 12px;font-size:11px;font-weight:500;cursor:pointer;}
        .ts-empty{font-size:11px;color:#94a3b8;text-align:center;padding:6px 0;}

        .avail-sub{font-size:10px;color:#94a3b8;margin-top:-6px;margin-bottom:8px;}
        .avail-day-row{display:flex;align-items:center;gap:6px;padding:7px 0;border-bottom:.5px solid #f8fafc;}
        .avail-day-row:last-of-type{border-bottom:none;}
        .day-name{font-size:11px;font-weight:500;color:#1e293b;width:30px;flex-shrink:0;}
        .time-sel{flex:1;background:#f8fafc;border:.5px solid #e2e8f0;border-radius:7px;padding:5px 7px;font-size:11px;color:#1e293b;appearance:none;-webkit-appearance:none;cursor:pointer;}
        .to-txt{font-size:10px;color:#94a3b8;flex-shrink:0;}
        .off-tag{flex:1;background:#f1f5f9;border-radius:7px;padding:5px 8px;font-size:11px;color:#94a3b8;text-align:center;}
        .save-avail-btn{width:100%;background:#25A69A;border:none;border-radius:8px;padding:9px;font-size:12px;color:#fff;font-weight:500;margin-top:10px;cursor:pointer;}
        .add-unavail{font-size:11px;color:#25A69A;font-weight:500;margin-top:6px;display:block;cursor:pointer;}
        .avail-msg{font-size:11px;margin-top:8px;display:none;}
        .same-all-toggle{display:inline-flex;align-items:center;gap:6px;cursor:pointer;user-select:none;}
        .same-all-toggle input{display:none;}
        .same-all-toggle .sat-slider{width:32px;height:18px;background:#cbd5e1;border-radius:20px;position:relative;transition:background .2s;flex-shrink:0;}
        .same-all-toggle .sat-slider::after{content:'';position:absolute;top:2px;left:2px;width:14px;height:14px;background:#fff;border-radius:50%;transition:transform .2s;}
        .same-all-toggle input:checked + .sat-slider{background:#25A69A;}
        .same-all-toggle input:checked + .sat-slider::after{transform:translateX(14px);}
        .same-all-toggle .sat-txt{font-size:10px;color:#64748b;font-weight:500;white-space:nowrap;}

        .qs-row{display:flex;justify-content:space-between;padding:5px 0;border-bottom:.5px solid #f8fafc;}
        .qs-row:last-child{border-bottom:none;}
        .qs-label{font-size:11px;color:#64748b;}
        .qs-val{font-size:11px;font-weight:500;color:#1e293b;}
        .qs-val.green{color:#0F6E56;}

        .leave-card{background:#0F6E56;border-radius:12px;padding:14px;}
        .lc-title{color:#9FE1CB;font-size:12px;font-weight:500;margin-bottom:10px;}
        .lc-row{margin-bottom:9px;}
        .lc-row:last-of-type{margin-bottom:0;}
        .lc-hdr{display:flex;justify-content:space-between;margin-bottom:5px;}
        .lc-name{font-size:11px;color:#E1F5EE;}
        .lc-val{font-size:11px;color:#fff;font-weight:500;}
        .lc-track{height:4px;background:rgba(255,255,255,.15);border-radius:10px;overflow:hidden;}
        .lc-fill{height:100%;background:#5DCAA5;border-radius:10px;}
        .apply-btn{width:100%;background:rgba(255,255,255,.1);border:.5px solid rgba(255,255,255,.2);border-radius:8px;padding:8px;font-size:11px;color:#E1F5EE;font-weight:500;margin-top:10px;text-align:center;cursor:pointer;}

        .offcanvas-nav .offcanvas-header{background:#1a2f52;color:#fff;}
        .offcanvas-nav .nav-link{color:#1e293b;font-size:14px;padding:12px 4px;border-bottom:.5px solid #f1f5f9;display:flex;align-items:center;gap:10px;}
        .offcanvas-nav .nav-link i{color:#25A69A;width:18px;text-align:center;}

        /* Timesheet details modal - matched to dashboard UI */
        #timesheetDetailsModal .modal-content{border:none;border-radius:16px;overflow:hidden;font-family:'Inter',sans-serif;}
        #timesheetDetailsModal .modal-header{background:#1a2f52;color:#fff;border-bottom:none;padding:14px 18px;}
        #timesheetDetailsModal .modal-title{font-size:15px;font-weight:600;}
        #timesheetDetailsModal .modal-body{padding:16px 18px;background:#f8fafc;}
        #timesheetDetailsModal .modal-footer{border-top:.5px solid #e2e8f0;padding:10px 18px;background:#fff;}
        .ts-loading{text-align:center;padding:40px 0;}
        .ts-loading .spinner-border{color:#25A69A;}
        .ts-loading p{margin-top:12px;font-size:12px;color:#94a3b8;}
        .ts-week-banner{background:#E1F5EE;border:.5px solid #9FE1CB;border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:#0F6E56;}
        .ts-week-banner span{font-weight:600;text-transform:uppercase;letter-spacing:.4px;font-size:11px;margin-right:4px;}
        .ts-week-banner strong{color:#0F6E56;font-weight:600;}
        .ts-table-wrap{background:#fff;border:.5px solid #e2e8f0;border-radius:12px;overflow:hidden;}
        .ts-detail-tbl{width:100%;border-collapse:collapse;}
        .ts-detail-tbl thead th{background:#f8fafc;color:#64748b;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;text-align:left;padding:10px 12px;border-bottom:.5px solid #e2e8f0;}
        .ts-detail-tbl tbody td{font-size:12px;color:#1e293b;padding:10px 12px;border-bottom:.5px solid #f1f5f9;}
        .ts-detail-tbl tbody tr:last-child td{border-bottom:none;}
        .ts-detail-tbl tbody td.ts-nobreak{color:#94a3b8;}
        .ts-detail-tbl tfoot td{font-size:12px;font-weight:600;color:#1a2f52;padding:11px 12px;background:#f8fafc;border-top:.5px solid #e2e8f0;}
        .ts-detail-tbl tfoot td:first-child{text-align:right;}
        .ts-note{background:#fffbeb;border:.5px solid #fde68a;border-radius:10px;padding:10px 14px;font-size:12px;color:#92400e;margin-top:12px;}
        .ts-error{background:#fef2f2;border:.5px solid #fecaca;border-radius:10px;padding:10px 14px;font-size:12px;color:#b91c1c;}
        .btn-close-soft{background:#f1f5f9;border:none;border-radius:8px;padding:8px 18px;font-size:12px;font-weight:500;color:#475569;cursor:pointer;}
        .btn-close-soft:hover{background:#e2e8f0;}
    </style>
</head>
<body>

<div class="screen">

    <div class="topnav">
        <div class="nav-left">
            <button class="hamburger" type="button" aria-label="Menu" data-bs-toggle="offcanvas" data-bs-target="#mobileNav"><i class="fa-solid fa-bars"></i></button>
            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="BizAdmin" class="brand-logo">
        </div>
        <div class="nav-right">
            <div class="bell" aria-label="Notifications"><i class="fa-solid fa-bell"></i><div class="notif-dot"></div></div>
            <a href="https://bizadmin.com.au/HR/employees" class="av-link" aria-label="Employees"><div class="av-sm"><?= htmlspecialchars($initials) ?></div></a>
        </div>
    </div>

    <div class="profile-strip">
        <div class="avatar-ring"><span><?= htmlspecialchars($initials) ?></span><div class="online-dot"></div></div>
        <div>
            <div class="p-name"><?= htmlspecialchars($employee_name) ?></div>
            <div class="p-sub"><?= date('D, d F Y') ?></div>
        </div>
        <?php if ($shift_started): ?>
            <div class="present-badge">Present</div>
        <?php else: ?>
            <div class="present-badge absent">Absent</div>
        <?php endif; ?>
    </div>

    <div class="content">

        <div class="stat-row">
            <div class="stat-card"><div class="stat-num"><?= $leaveReqCount ?></div><div class="stat-label">Leave requests</div></div>
            <div class="stat-card"><div class="stat-num"><?= $upcomingShifts ?></div><div class="stat-label">Upcoming shifts</div></div>
            <div class="stat-card">
                <div class="stat-present <?= $shift_started ? '' : 'off' ?>"><?= $shift_started ? 'Active' : 'Off' ?></div>
                <div class="stat-label">Attendance today</div>
            </div>
        </div>

        <div class="card">
            <div class="card-hdr"><div class="card-title">Today's schedule</div><a class="card-link" href="<?= base_url('HR/roster') ?>">View roster</a></div>
            <?php if ($schedTime !== ''): ?>
                <div class="sched-time"><?= $schedTime ?></div>
                <?php if ($schedSub !== ''): ?><div class="sched-sub"><?= $schedSub ?></div><?php endif; ?>
                <?php if ($shift_started): ?>
                    <div class="clocked-pill"><div class="c-dot"></div>Clocked in <?= htmlspecialchars($shift_clockin) ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="sched-sub">No shift scheduled today.</div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-hdr"><div class="card-title">Today's attendance</div></div>
            <div class="tl-cols">
                <div class="tl-col"><div class="tl-lbl">Clock in</div><div class="tl-v <?= $clockIn === '--:--' || $clockIn === '--:-- --' ? 'dim' : '' ?>"><?= htmlspecialchars($clockIn) ?></div></div>
                <div class="tl-col"><div class="tl-lbl">Break</div><div class="tl-v <?= $breakStart === '--:--' || $breakStart === '--:-- --' ? 'dim' : '' ?>"><?= htmlspecialchars($breakStart) ?></div></div>
                <div class="tl-col"><div class="tl-lbl">Resume</div><div class="tl-v <?= $resume === '--:--' || $resume === '--:-- --' ? 'dim' : '' ?>"><?= htmlspecialchars($resume) ?></div></div>
                <div class="tl-col"><div class="tl-lbl">Clock out</div><div class="tl-v <?= $clockOut === '--:--' || $clockOut === '--:-- --' ? 'dim' : '' ?>"><?= htmlspecialchars($clockOut) ?></div></div>
            </div>
            <div class="prog-track"><div class="prog-fill" style="width:<?= $progress ?>%;"></div></div>
            <div class="tl-foot"><span style="font-size:11px;font-weight:500;color:#1e293b;"><?= htmlspecialchars($workedLbl) ?> worked</span><span style="font-size:11px;color:#94a3b8;">Target: <?= htmlspecialchars($targetLbl) ?></span></div>
        </div>

        <div class="card">
            <div class="card-hdr"><div class="card-title">My timesheets</div><span class="card-link">View all</span></div>
            <?php if (!empty($timesheets)): ?>
                <?php foreach ($timesheets as $t):
                    $rowMon   = strtotime('monday this week', strtotime($t['date_from']));
                    $weekDiff = (int) round(($curMon - $rowMon) / (7 * 86400));
                    $rangeLbl = $weekDiff <= 0 ? 'Current week' : ($weekDiff === 1 ? 'Last week' : $weekDiff . ' weeks ago');
                ?>
                <div class="ts-row">
                    <div>
                        <div class="ts-dates"><?= date('M d', strtotime($t['date_from'])) ?> &ndash; <?= date('M d, Y', strtotime($t['date_to'])) ?></div>
                        <div class="ts-range"><?= $rangeLbl ?></div>
                    </div>
                    <button class="view-btn view-timesheet-details"
                            data-week-start="<?= $t['date_from'] ?>"
                            data-week-end="<?= $t['date_to'] ?>"
                            data-emp-id="<?= htmlspecialchars($empId) ?>">View</button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="ts-empty">No timesheets found.</div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-hdr">
                <div class="card-title">My availability</div>
                <label class="same-all-toggle">
                    <input type="checkbox" id="mSameAllDays">
                    <span class="sat-slider"></span>
                    <span class="sat-txt">Same for all days</span>
                </label>
            </div>
            <div class="avail-sub">Set your weekly availability</div>
            <form id="mobileAvailabilityForm">
                <input type="hidden" name="emp_id" value="<?= htmlspecialchars($empId) ?>">
                <input type="hidden" name="same_hours" value="0">
                <?php foreach ($availDays as $key => $label):
                    $start = trim((string) ($availWeekly[$key]['start'] ?? ''));
                    $end   = trim((string) ($availWeekly[$key]['end'] ?? ''));
                ?>
                <div class="avail-day-row">
                    <div class="day-name"><?= $label ?></div>
                    <?= $renderTimeSelect("weekly[$key][start]", $start) ?>
                    <div class="to-txt">to</div>
                    <?= $renderTimeSelect("weekly[$key][end]", $end) ?>
                </div>
                <?php endforeach; ?>
                <span class="add-unavail" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">+ Add availability</span>
                <div class="avail-msg" id="mobileAvailMsg"></div>
                <button type="submit" class="save-avail-btn">Save availability</button>
            </form>
        </div>

        <div class="card">
            <div class="card-hdr"><div class="card-title">Quick stats</div></div>
            <div class="qs-row"><span class="qs-label">Hours this week</span><span class="qs-val"><?= htmlspecialchars($hours_this_week) ?></span></div>
            <div class="qs-row"><span class="qs-label">Attendance rate</span><span class="qs-val green"><?= (int) $attendance_rate ?>%</span></div>
        </div>

        <div class="leave-card">
            <div class="lc-title">Leave balance</div>
            <?php if (!empty($leaveBalances)): ?>
                <?php foreach ($leaveBalances as $lb):
                    $entitlement = (float) ($lb['entitlements'] ?? 0);
                    $used        = (float) ($lb['used_days'] ?? 0);
                    $percent     = $entitlement > 0 ? min(100, round(($used / $entitlement) * 100)) : 0;
                ?>
                <div class="lc-row">
                    <div class="lc-hdr"><span class="lc-name"><?= htmlspecialchars($lb['leaveTypeName']) ?></span><span class="lc-val"><?= $used ?> / <?= $entitlement ?> days</span></div>
                    <div class="lc-track"><div class="lc-fill" style="width:<?= $percent ?>%;"></div></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="lc-name">No leave types configured.</div>
            <?php endif; ?>
            <div class="apply-btn" data-bs-toggle="modal" data-bs-target="#requestLeaveModal">Apply for leave</div>
        </div>

    </div>
</div>

<!-- NAV OFFCANVAS (shared dynamic role/system menu) -->
<?php
$navHomeUrl = base_url('HR/' . $this->session->userdata('system_id'));
include(APPPATH . 'views/general/mobile_nav_offcanvas.php');
?>

<!-- LEAVE REQUEST MODAL -->
<div class="modal fade" id="requestLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a2f52;color:#fff;">
                <h5 class="modal-title">Request Leave</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success d-none" id="leaveSuccessAlert">Leave request submitted successfully!</div>
                <div class="alert alert-danger d-none" id="leaveErrorAlert"></div>
                <form id="newLeaveRequestForm" enctype="multipart/form-data">
                    <input type="hidden" name="emp_id" value="<?= htmlspecialchars($empId) ?>">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="leave_start_date" name="start_date" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="leave_end_date" name="end_date" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type" required>
                                <option value="">Select Leave Type</option>
                                <?php if (!empty($leaveTypes)): ?>
                                    <?php foreach ($leaveTypes as $type): ?>
                                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['leaveTypeName']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 d-none" id="medicalCertificateField">
                            <label class="form-label">Medical Certificate <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="medical_certificate" name="userfile[]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple>
                            <small class="text-muted">Accepted: PDF, JPG, PNG, DOC, DOCX (Max 8MB)</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Comments</label>
                            <textarea class="form-control" id="leave_comments" name="leaveComments" rows="3" placeholder="Enter reason for leave..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:#25A69A;" id="submitLeaveRequest">
                    <span class="btn-text">Submit Request</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TIMESHEET DETAILS MODAL -->
<div class="modal fade" id="timesheetDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Timesheet details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="timesheet-loading" class="ts-loading">
                    <div class="spinner-border" role="status"></div>
                    <p>Loading timesheet data...</p>
                </div>
                <div id="timesheet-content" class="d-none">
                    <div class="ts-week-banner"><span>Week</span><strong id="modal-date-range"></strong></div>
                    <div class="ts-table-wrap">
                        <table class="ts-detail-tbl">
                            <thead>
                                <tr><th>Date</th><th>In</th><th>Out</th><th>Break</th><th>Hours</th></tr>
                            </thead>
                            <tbody id="timesheet-table-body"></tbody>
                            <tfoot>
                                <tr><td colspan="4">Total</td><td id="total-hours-worked">0h 0m</td></tr>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="<?= base_url('theme-assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script>
$(function () {

    // ---------- Save availability ----------
    $('#mobileAvailabilityForm').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var btn  = form.find('.save-avail-btn');
        var msg  = $('#mobileAvailMsg');
        btn.prop('disabled', true).text('Saving...');
        msg.hide();

        $.ajax({
            url: '<?= base_url('HR/Employees/save_availability') ?>',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    msg.css('color', '#15803d').text(res.message || 'Availability updated successfully').show();
                } else {
                    msg.css('color', '#b91c1c').text(res.message || 'Failed to update availability').show();
                }
            },
            error: function () {
                msg.css('color', '#b91c1c').text('Something went wrong. Please try again.').show();
            },
            complete: function () {
                btn.prop('disabled', false).text('Save availability');
            }
        });
    });

    // ---------- Same-for-all-days availability toggle ----------
    function applyMobileSameForAll() {
        var $f = $('#mobileAvailabilityForm');
        var ms = $f.find('select[name="weekly[mon][start]"]').val();
        var me = $f.find('select[name="weekly[mon][end]"]').val();
        ['tue','wed','thu','fri','sat','sun'].forEach(function(d) {
            $f.find('select[name="weekly[' + d + '][start]"]').val(ms);
            $f.find('select[name="weekly[' + d + '][end]"]').val(me);
        });
    }
    $('#mSameAllDays').on('change', function() {
        if ($(this).is(':checked')) { applyMobileSameForAll(); }
    });
    $('#mobileAvailabilityForm').on('change', 'select[name="weekly[mon][start]"], select[name="weekly[mon][end]"]', function() {
        if ($('#mSameAllDays').is(':checked')) { applyMobileSameForAll(); }
    });

    // ---------- Timesheet details ----------
    $('.view-timesheet-details').on('click', function () {
        var btn = $(this);
        var weekStart = btn.data('week-start');
        var weekEnd   = btn.data('week-end');
        var empId     = btn.data('emp-id');

        $('#timesheet-loading').removeClass('d-none');
        $('#timesheet-content').addClass('d-none');
        $('#timesheet-error').addClass('d-none');

        var modal = new bootstrap.Modal(document.getElementById('timesheetDetailsModal'));
        modal.show();

        $.ajax({
            url: '<?= base_url('HR/Home/getTimesheetDetails') ?>',
            method: 'POST',
            data: { week_start: weekStart, week_end: weekEnd, emp_id: empId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var startDate = new Date(weekStart).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                    var endDate   = new Date(weekEnd).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                    $('#modal-date-range').text(startDate + ' - ' + endDate);

                    var tbody = $('#timesheet-table-body');
                    tbody.empty();

                    if (response.data && response.data.length > 0) {
                        var totalMinutes = 0;
                        response.data.forEach(function (record) {
                            var breakCls = /no break/i.test(record.break_info) ? ' class="ts-nobreak"' : '';
                            tbody.append(
                                '<tr><td>' + record.date + '</td><td>' + record.clock_in + '</td><td>' +
                                record.clock_out + '</td><td' + breakCls + '>' + record.break_info + '</td><td>' + record.total_hours + '</td></tr>'
                            );
                            var parts = record.total_hours.match(/(\d+)h\s*(\d+)m/);
                            if (parts) { totalMinutes += parseInt(parts[1]) * 60 + parseInt(parts[2]); }
                        });
                        $('#total-hours-worked').text(Math.floor(totalMinutes / 60) + 'h ' + (totalMinutes % 60) + 'm');
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
            error: function () {
                $('#timesheet-loading').addClass('d-none');
                $('#timesheet-error').removeClass('d-none');
                $('#error-message').text('An error occurred while loading timesheet data. Please try again.');
            }
        });
    });

    // ---------- Leave request ----------
    $('#leave_type').on('change', function () {
        var isSick = /sick/i.test($(this).find('option:selected').text());
        $('#medicalCertificateField').toggleClass('d-none', !isSick);
        $('#medical_certificate').attr('required', isSick);
        if (!isSick) { $('#medical_certificate').val(''); }
    });

    var today = new Date().toISOString().split('T')[0];
    $('#leave_start_date, #leave_end_date').attr('min', today);
    $('#leave_start_date').on('change', function () {
        $('#leave_end_date').attr('min', $(this).val());
        if ($('#leave_end_date').val() && $('#leave_end_date').val() < $(this).val()) {
            $('#leave_end_date').val($(this).val());
        }
    });

    $('#submitLeaveRequest').on('click', function () {
        var form = $('#newLeaveRequestForm')[0];
        if (!form.checkValidity()) { form.reportValidity(); return; }

        var isSick  = /sick/i.test($('#leave_type option:selected').text());
        var hasFile = $('#medical_certificate')[0].files.length > 0;
        if (isSick && !hasFile) {
            $('#leaveErrorAlert').removeClass('d-none').text('Medical certificate is required for sick leave.');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true);
        btn.find('.btn-text').addClass('d-none');
        btn.find('.spinner-border').removeClass('d-none');
        $('#leaveSuccessAlert, #leaveErrorAlert').addClass('d-none');

        $.ajax({
            url: '<?= base_url('HR/Leaves/requestLeave') ?>',
            type: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function (response) {
                if (response === 'success' || (response.success !== undefined && response.success)) {
                    $('#leaveSuccessAlert').removeClass('d-none');
                    form.reset();
                    $('#medicalCertificateField').addClass('d-none');
                    setTimeout(function () {
                        bootstrap.Modal.getInstance(document.getElementById('requestLeaveModal')).hide();
                        location.reload();
                    }, 1500);
                } else {
                    $('#leaveErrorAlert').removeClass('d-none').text(response.message || 'Failed to submit leave request. Please try again.');
                }
            },
            error: function () {
                $('#leaveErrorAlert').removeClass('d-none').text('An error occurred. Please try again.');
            },
            complete: function () {
                btn.prop('disabled', false);
                btn.find('.btn-text').removeClass('d-none');
                btn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    $('#requestLeaveModal').on('hidden.bs.modal', function () {
        $('#newLeaveRequestForm')[0].reset();
        $('#leaveSuccessAlert, #leaveErrorAlert').addClass('d-none');
        $('#medicalCertificateField').addClass('d-none');
    });
});
</script>
</body>
</html>
