<?php
/* =====================================================================
 * BizAdmin shared EMPLOYEE mobile header (topnav + profile strip).
 * Single source of truth for the employee-portal mobile header so it is
 * identical on the dashboard AND every other employee page.
 *
 * Optional inputs (set before include):
 *   $ehName         - full employee name        (auto-derived if empty)
 *   $ehInitials     - initials                   (auto-derived if empty)
 *   $ehShowBadge    - bool, show Present/Absent badge (dashboard only)
 *   $ehShiftStarted - bool, Present(true)/Absent(false) state for badge
 *   $ehHomeUrl      - logo link url
 *   $ehAvatarUrl    - avatar link url
 *
 * Requires Font Awesome on the page (already loaded by the theme).
 * ===================================================================== */
if (!isset($ehName) || trim((string) $ehName) === '') {
    $ehName = trim((string) $this->session->userdata('username'));
    $ehUid  = $this->session->userdata('user_id');
    if ($ehUid && isset($this->common_model)) {
        try {
            $ehEmp = $this->common_model->fetchRecordsDynamically('HR_employee', ['first_name', 'last_name'], ['userId' => $ehUid]);
            if (!empty($ehEmp[0])) {
                $ehFull = trim(($ehEmp[0]['first_name'] ?? '') . ' ' . ($ehEmp[0]['last_name'] ?? ''));
                if ($ehFull !== '') { $ehName = $ehFull; }
            }
        } catch (Exception $ehEx) { /* fall back to username */ }
    }
}
$ehName = trim(ucwords((string) $ehName));
if ($ehName === '') { $ehName = 'User'; }

if (!isset($ehInitials) || trim((string) $ehInitials) === '') {
    $ehParts    = preg_split('/\s+/', $ehName);
    $ehInitials = strtoupper(substr($ehParts[0] ?? '', 0, 1) . (isset($ehParts[1]) ? substr($ehParts[1], 0, 1) : ''));
    if ($ehInitials === '') { $ehInitials = 'U'; }
}

if (!isset($ehShowStrip))    { $ehShowStrip = false; }
if (!isset($ehShowBadge))    { $ehShowBadge = false; }
if (!isset($ehShiftStarted)) { $ehShiftStarted = false; }
if (!isset($ehHomeUrl) || $ehHomeUrl === '') {
    $ehHomeUrl = base_url('HR/' . $this->session->userdata('system_id'));
}
if (!isset($ehAvatarUrl) || $ehAvatarUrl === '') {
    $ehAvatarUrl = base_url('HR/employees');
}
?>
<style>
    .emp-mhead{font-family:'Inter',sans-serif;}
    .emp-mhead .topnav{background:#1a2f52;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;}
    .emp-mhead .nav-left{display:flex;align-items:center;gap:10px;}
    .emp-mhead .hamburger{color:rgba(255,255,255,.85);font-size:18px;cursor:pointer;background:none;border:none;display:flex;padding:0;line-height:1;}
    .emp-mhead .brand-logo{height:26px;width:auto;display:block;}
    .emp-mhead .nav-right{display:flex;align-items:center;gap:10px;}
    .emp-mhead .bell{color:rgba(255,255,255,.7);font-size:17px;position:relative;display:flex;}
    .emp-mhead .notif-dot{width:7px;height:7px;background:#ef4444;border-radius:50%;position:absolute;top:-1px;right:-1px;border:1.5px solid #1a2f52;}
    .emp-mhead .av-link{display:inline-flex;text-decoration:none;}
    .emp-mhead .av-sm{width:30px;height:30px;border-radius:50%;border:2px solid #25A69A;background:#0F6E56;display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:500;position:relative;flex-shrink:0;}
    .emp-mhead .av-sm .online-dot{width:8px;height:8px;bottom:0;right:0;border-width:1.5px;}
    .emp-mhead .profile-strip{background:#1a2f52;padding:0 16px 14px;display:flex;align-items:center;gap:12px;}
    .emp-mhead .avatar-ring{width:44px;height:44px;border-radius:50%;border:2px solid #25A69A;background:#0F6E56;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:500;flex-shrink:0;position:relative;}
    .emp-mhead .online-dot{width:9px;height:9px;background:#22c55e;border-radius:50%;border:2px solid #1a2f52;position:absolute;bottom:1px;right:1px;}
    .emp-mhead .p-name{color:#fff;font-size:13px;font-weight:500;}
    .emp-mhead .p-sub{color:rgba(255,255,255,.5);font-size:10px;margin-top:2px;}
    .emp-mhead .present-badge{margin-left:auto;background:#0F6E56;color:#9FE1CB;font-size:10px;padding:3px 9px;border-radius:20px;font-weight:500;white-space:nowrap;}
    .emp-mhead .present-badge.absent{background:#7f1d1d;color:#fca5a5;}
</style>
<div class="emp-mhead">
    <div class="topnav">
        <div class="nav-left">
            <button class="hamburger" type="button" aria-label="Menu" data-bs-toggle="offcanvas" data-bs-target="#mobileNav"><i class="fa-solid fa-bars"></i></button>
            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="BizAdmin" class="brand-logo">
        </div>
        <div class="nav-right">
            <div class="bell" aria-label="Notifications"><i class="fa-solid fa-bell"></i><div class="notif-dot"></div></div>
            <a href="<?= $ehAvatarUrl ?>" class="av-link" aria-label="Profile"><div class="av-sm"><span><?= htmlspecialchars($ehInitials) ?></span><div class="online-dot"></div></div></a>
        </div>
    </div>
    <?php if ($ehShowStrip): ?>
    <div class="profile-strip">
        <div class="avatar-ring"><span><?= htmlspecialchars($ehInitials) ?></span><div class="online-dot"></div></div>
        <div>
            <div class="p-name"><?= htmlspecialchars($ehName) ?></div>
            <div class="p-sub"><?= date('D, d F Y') ?></div>
        </div>
        <?php if ($ehShowBadge): ?>
            <div class="present-badge<?= $ehShiftStarted ? '' : ' absent' ?>"><?= $ehShiftStarted ? 'Present' : 'Absent' ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
