<?php
/**
 * Mobile manager / admin dashboard (self-contained).
 * Visual language matched to the employee mobile dashboard (dashboard_mobile.php).
 * Sections & data mirror general/dashboard_manager.php. Data is dynamic.
 */

// ---------- Manager identity ----------
$mgrName     = trim((string) $this->session->userdata('username')) ?: 'Manager';
$mgrLocation = trim((string) $this->session->userdata('location_name'));
$mgrRole     = $this->ion_auth->in_group('admin') ? 'Admin' : 'Manager';
$mgrParts    = preg_split('/\s+/', $mgrName);
$mgrInitials = strtoupper(substr($mgrParts[0] ?? 'M', 0, 1) . (isset($mgrParts[1]) ? substr($mgrParts[1], 0, 1) : ''));
if ($mgrInitials === '') { $mgrInitials = 'M'; }

// ---------- Section data ----------
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BizAdmin</title>
    <link rel="shortcut icon" href="<?= base_url(); ?>login-assets/img/favicon.jpeg" />
    <link href="<?= base_url('theme-assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
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
        .nav-right{display:flex;align-items:center;gap:10px;}
        .bell{color:rgba(255,255,255,.7);font-size:17px;position:relative;}
        .notif-dot{width:7px;height:7px;background:#ef4444;border-radius:50%;position:absolute;top:-1px;right:-1px;border:1.5px solid #1a2f52;}
        .av-sm{width:28px;height:28px;border-radius:50%;background:#1D9E75;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:500;}

        .profile-strip{background:#1a2f52;padding:0 16px 14px;display:flex;align-items:center;gap:12px;}
        .avatar-ring{width:44px;height:44px;border-radius:50%;border:2px solid #1D9E75;background:#0F6E56;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:500;flex-shrink:0;position:relative;}
        .online-dot{width:9px;height:9px;background:#22c55e;border-radius:50%;border:2px solid #1a2f52;position:absolute;bottom:1px;right:1px;}
        .p-name{color:#fff;font-size:13px;font-weight:500;}
        .p-sub{color:rgba(255,255,255,.5);font-size:10px;margin-top:2px;}
        .role-badge{margin-left:auto;background:#0F6E56;color:#9FE1CB;font-size:10px;padding:3px 9px;border-radius:20px;font-weight:500;white-space:nowrap;}

        .content{padding:12px 12px 28px;display:flex;flex-direction:column;gap:10px;}

        .stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px;}
        .stat-card{background:#fff;border-radius:10px;border:.5px solid #e2e8f0;padding:11px 12px;display:flex;align-items:center;gap:10px;}
        .stat-card.alert{border:1px solid #fecaca;}
        .stat-ic{font-size:18px;width:24px;text-align:center;flex-shrink:0;}
        .stat-num{font-size:18px;font-weight:600;color:#1a2f52;line-height:1;}
        .stat-num.red{color:#dc2626;}
        .stat-label{font-size:10px;color:#64748b;margin-top:3px;line-height:1.2;}

        .card{background:#fff;border-radius:12px;border:.5px solid #e2e8f0;padding:14px;}
        .card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
        .card-title{font-size:12px;font-weight:600;color:#1e293b;}
        .card-link{font-size:11px;color:#1D9E75;font-weight:500;cursor:pointer;text-decoration:none;}

        .qs-row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:.5px solid #f8fafc;}
        .qs-row:last-child{border-bottom:none;}
        .qs-label{font-size:11px;color:#64748b;}
        .qs-val{font-size:11px;font-weight:600;color:#1e293b;}
        .qs-val.green{color:#0F6E56;}
        .qs-val.orange{color:#c2410c;}

        .feed-item{display:flex;align-items:flex-start;gap:9px;padding:9px 10px;border-radius:9px;border-left:3px solid #cbd5e1;background:#f8fafc;margin-bottom:7px;}
        .feed-item:last-child{margin-bottom:0;}
        .feed-item.red{background:#fef2f2;border-left-color:#ef4444;}
        .feed-item.orange{background:#fff7ed;border-left-color:#f97316;}
        .feed-item.purple{background:#faf5ff;border-left-color:#a855f7;}
        .feed-item i{margin-top:2px;font-size:12px;}
        .feed-item.red i{color:#ef4444;}
        .feed-item.orange i{color:#f97316;}
        .feed-item.purple i{color:#a855f7;}
        .feed-t{font-size:11px;font-weight:600;color:#1e293b;}
        .feed-s{font-size:10px;color:#94a3b8;margin-top:2px;}
        .feed-empty{font-size:11px;color:#94a3b8;text-align:center;padding:14px 0;}

        .att-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:.5px solid #f1f5f9;}
        .att-row:last-child{border-bottom:none;}
        .att-name{font-size:12px;font-weight:500;color:#1e293b;}
        .att-sub{font-size:10px;color:#94a3b8;margin-top:2px;}
        .badge-pill{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600;white-space:nowrap;}
        .badge-pill.present{background:#f0fdf4;color:#15803d;}
        .badge-pill.absent{background:#fef2f2;color:#b91c1c;}

        .act-btn{display:flex;align-items:center;gap:9px;width:100%;background:#f8fafc;border:.5px solid #e2e8f0;border-radius:9px;padding:10px 12px;font-size:12px;font-weight:600;color:#1e293b;text-decoration:none;margin-bottom:7px;}
        .act-btn:last-child{margin-bottom:0;}
        .act-btn i{width:18px;text-align:center;color:#1D9E75;}
        .act-btn.danger i{color:#ef4444;}

        .offcanvas-nav .offcanvas-header{background:#1a2f52;color:#fff;}
        .offcanvas-nav .nav-link{color:#1e293b;font-size:14px;padding:12px 4px;border-bottom:.5px solid #f1f5f9;display:flex;align-items:center;gap:10px;}
        .offcanvas-nav .nav-link i{color:#1D9E75;width:18px;text-align:center;}
    </style>
</head>
<body>

<div class="screen">

    <div class="topnav">
        <div class="nav-left">
            <button class="hamburger" type="button" aria-label="Menu" data-bs-toggle="offcanvas" data-bs-target="#mobileNav"><i class="fa-solid fa-bars"></i></button>
            <div class="brand">BizAdmin</div>
        </div>
        <div class="nav-right">
            <div class="bell" aria-label="Notifications"><i class="fa-solid fa-bell"></i><?php if ($feedCount > 0): ?><div class="notif-dot"></div><?php endif; ?></div>
            <div class="av-sm"><?= htmlspecialchars($mgrInitials) ?></div>
        </div>
    </div>

    <div class="profile-strip">
        <div class="avatar-ring"><span><?= htmlspecialchars($mgrInitials) ?></span><div class="online-dot"></div></div>
        <div>
            <div class="p-name"><?= htmlspecialchars(ucfirst($mgrName)) ?></div>
            <div class="p-sub"><?= $mgrLocation !== '' ? htmlspecialchars($mgrLocation) . ' &middot; ' : '' ?><?= date('D, d F Y') ?></div>
        </div>
        <div class="role-badge"><?= $mgrRole ?></div>
    </div>

    <div class="content">

        <!-- Quick glance stats -->
        <div class="stat-grid">
            <div class="stat-card">
                <i class="fa-solid fa-cake-candles stat-ic" style="color:#a855f7;"></i>
                <div><div class="stat-num"><?= count($birthdays_today) ?></div><div class="stat-label">Today's birthdays</div></div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-circle-check stat-ic" style="color:#22c55e;"></i>
                <div><div class="stat-num"><?= (int) ($task_summary['completed_today'] ?? 0) ?></div><div class="stat-label">Tasks completed</div></div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-mug-hot stat-ic" style="color:#f97316;"></i>
                <div><div class="stat-num"><?= (int) $employee_on_break_count ?></div><div class="stat-label">Employee on break</div></div>
            </div>
            <div class="stat-card alert">
                <i class="fa-solid fa-file-circle-exclamation stat-ic" style="color:#ef4444;"></i>
                <div><div class="stat-num red"><?= count($pending_leaves) ?></div><div class="stat-label">Leave requests</div></div>
            </div>
        </div>

        <!-- Cafe staff status -->
        <div class="card">
            <div class="card-hdr"><div class="card-title">Cafe Staff Today's Status</div></div>
            <div class="qs-row"><span class="qs-label">Total Members</span><span class="qs-val"><?= (int) $total_employees ?></span></div>
            <div class="qs-row"><span class="qs-label">Present Today</span><span class="qs-val green"><?= (int) $present_today ?></span></div>
            <div class="qs-row"><span class="qs-label">On Leave</span><span class="qs-val orange"><?= count($pending_leaves) ?></span></div>
        </div>

        <!-- What's happening -->
        <div class="card">
            <div class="card-hdr"><div class="card-title">What's Happening</div></div>
            <?php if ($feedCount > 0): ?>
                <?php foreach ($incident_reports as $inc): ?>
                    <div class="feed-item red"><i class="fa-solid fa-triangle-exclamation"></i><div><div class="feed-t">New incident report</div><div class="feed-s"><?= htmlspecialchars(trim(($inc->first_name ?? '') . ' ' . ($inc->last_name ?? ''))) ?></div></div></div>
                <?php endforeach; ?>
                <?php foreach ($injury_reports as $inj): ?>
                    <div class="feed-item red"><i class="fa-solid fa-triangle-exclamation"></i><div><div class="feed-t">New injury report</div><div class="feed-s"><?= htmlspecialchars(trim(($inj->first_name ?? '') . ' ' . ($inj->last_name ?? ''))) ?></div></div></div>
                <?php endforeach; ?>
                <?php foreach ($pending_leaves as $lv): ?>
                    <div class="feed-item orange"><i class="fa-solid fa-hourglass-half"></i><div><div class="feed-t">Leave request: <?= htmlspecialchars($lv->start_date ?? '') ?></div><div class="feed-s"><?= htmlspecialchars(trim(($lv->first_name ?? '') . ' ' . ($lv->last_name ?? ''))) ?></div></div></div>
                <?php endforeach; ?>
                <?php foreach ($birthdays_today as $b): ?>
                    <div class="feed-item purple"><i class="fa-solid fa-cake-candles"></i><div><div class="feed-t">Birthday today</div><div class="feed-s"><?= htmlspecialchars(trim(($b->first_name ?? '') . ' ' . ($b->last_name ?? ''))) ?></div></div></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="feed-empty">Nothing new to report today.</div>
            <?php endif; ?>
        </div>

        <!-- Team tasks -->
        <div class="card">
            <div class="card-hdr"><div class="card-title">Team Tasks Overview</div></div>
            <div class="qs-row"><span class="qs-label">Completed Today</span><span class="qs-val green"><?= (int) ($task_summary['completed_today'] ?? 0) ?></span></div>
            <div class="qs-row"><span class="qs-label">In Progress</span><span class="qs-val"><?= (int) ($task_summary['in_progress'] ?? 0) ?></span></div>
            <div class="qs-row"><span class="qs-label">Total Team Hours</span><span class="qs-val green"><?= htmlspecialchars((string) $total_team_hours) ?>h</span></div>
        </div>

        <!-- Attendance timeline -->
        <div class="card">
            <div class="card-hdr"><div class="card-title">Today's Team Attendance</div></div>
            <?php if (!empty($attendance_today)): ?>
                <?php foreach ($attendance_today as $row):
                    $status    = ($row->clock_in_time != '') ? 'Present' : 'Absent';
                    $statusCls = ($row->clock_in_time != '') ? 'present' : 'absent';
                    $cin  = $row->clock_in_time  ? date('h:i A', strtotime($row->clock_in_time))  : '-';
                    $cout = $row->clock_out_time ? date('h:i A', strtotime($row->clock_out_time)) : '-';
                ?>
                <div class="att-row">
                    <div>
                        <div class="att-name"><?= htmlspecialchars(trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''))) ?></div>
                        <div class="att-sub"><?= $cin ?> &ndash; <?= $cout ?><?= !empty($row->prep_name) ? ' &middot; ' . htmlspecialchars($row->prep_name) : '' ?></div>
                    </div>
                    <span class="badge-pill <?= $statusCls ?>"><?= $status ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="feed-empty">No attendance records for today.</div>
            <?php endif; ?>
        </div>

        <!-- Quick actions -->
        <div class="card">
            <div class="card-hdr"><div class="card-title">Quick Actions</div></div>
            <a class="act-btn danger" href="<?= base_url('HR/leaveDashbaord') ?>"><i class="fa-solid fa-check-double"></i> Approve Leaves</a>
            <a class="act-btn" href="<?= base_url('HR/timesheetWithoutRoster') ?>"><i class="fa-solid fa-clock-rotate-left"></i> Approve Timesheets</a>
            <a class="act-btn" href="<?= base_url('HR/employees') ?>"><i class="fa-solid fa-list-check"></i> View All Employees</a>
            <a class="act-btn" href="<?= base_url('HR/memo') ?>"><i class="fa-solid fa-bullhorn"></i> Send Memo</a>
        </div>

    </div>
</div>

<!-- NAV OFFCANVAS -->
<div class="offcanvas offcanvas-start offcanvas-nav" tabindex="-1" id="mobileNav" style="max-width:280px;">
    <div class="offcanvas-header">
        <h6 class="mb-0">BizAdmin</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <a class="nav-link" href="<?= base_url('HR/' . $this->session->userdata('system_id')) ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a class="nav-link" href="<?= base_url('HR/employees') ?>"><i class="fa-solid fa-users"></i> Employees</a>
        <a class="nav-link" href="<?= base_url('HR/roster') ?>"><i class="fa-solid fa-calendar-days"></i> Roster</a>
        <a class="nav-link" href="<?= base_url('HR/leaveDashbaord') ?>"><i class="fa-solid fa-calendar-check"></i> Approve Leaves</a>
        <a class="nav-link" href="<?= base_url('HR/timesheetWithoutRoster') ?>"><i class="fa-solid fa-clock-rotate-left"></i> Approve Timesheets</a>
        <a class="nav-link" href="<?= base_url('HR/memo') ?>"><i class="fa-solid fa-bullhorn"></i> Send Memo</a>
        <a class="nav-link" href="<?= base_url('auth/logout') ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="<?= base_url('theme-assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>
