<?php
/* =====================================================================
 * BizAdmin common shared topbar (web + mobile)
 * Reference UI: employee dashboard.
 * - Desktop: same Velzon horizontal header + dynamic role/system menu
 *   (sidebar.php -> fetch_render_menu). Behaviour unchanged.
 * - Mobile: employee-dashboard-style bar with an offcanvas that reuses
 *   the SAME dynamic menu so menu functionality is preserved.
 * Used by every module's partials/topbar.php via include().
 * ===================================================================== */
$ci_tb =& get_instance();

$currentModule = '';
if (isset($ci_tb->router) && method_exists($ci_tb->router, 'fetch_module')) {
    $currentModule = $ci_tb->router->fetch_module();
}
if (empty($currentModule)) {
    $currentModule = $ci_tb->uri->segment(1);
}

// CateringBackup links to the /Catering/ controller (matches original markup).
$linkModule = $currentModule;
if (strcasecmp((string)$currentModule, 'CateringBackup') === 0) {
    $linkModule = 'Catering';
}

$tb_systemId = $this->session->userdata('system_id');
$tb_homeUrl  = '/' . $linkModule . '/' . $tb_systemId;
$isCatering  = (strcasecmp((string)$currentModule, 'Catering') === 0 || strcasecmp((string)$currentModule, 'CateringBackup') === 0);

// Per-system settings route (each module exposes its own settings controller/method).
$tb_settingsRoutes = array(
    'HR'         => 'HR/configuresubmit',
    'Temp'       => 'Temp/settings',
    'Cash'       => 'Cash/configureFloats',
    'Catering'   => 'Catering/saveSettings',
    'Compliance' => 'Compliance/settings',
    'Clean'      => 'Clean/settings',
    'Dms'        => 'Dms/settings',
    'Shifts'     => 'Shifts/settings',
    'Supplier'   => 'Supplier/configuresubmit',
);
$tb_settingsUrl = '';
foreach ($tb_settingsRoutes as $tb_k => $tb_v) {
    if (strcasecmp((string)$linkModule, $tb_k) === 0) { $tb_settingsUrl = $tb_v; break; }
}
$tb_isHr = (strcasecmp((string)$linkModule, 'HR') === 0);

// Dynamic menu for the mobile offcanvas (same source as the desktop horizontal menu).
$tb_roleId = $this->ion_auth->get_users_groups()->row()->id;
$tb_menus  = fetch_render_menu($tb_systemId, $this->session->userdata('user_id'), $tb_roleId, 'frontSites');
?>
<style>
    /* ===== BizAdmin common header ===== */
    div#notificationWrap a.dropdown-item { display: flex; align-items: flex-start; white-space: break-spaces; font-size: 13px; }
    div#notificationWrap { min-width: 320px; }
    #notificationWrap i { color: #864868 !important; }

    #page-topbar { background-color: #1a2f52 !important; }
    #page-topbar .navbar-header { background-color: transparent !important; }
    /* user box must blend with the navy header (was old indigo --vz-topbar-user-bg) */
    #page-topbar .topbar-user { background-color: transparent !important; }
    #page-topbar .user-name-text { color: #ffffff !important; }
    #page-topbar .user-name-sub-text,
    #page-topbar .user-name-sub-text i { color: rgba(255, 255, 255, 0.7) !important; }
    #page-topbar .hamburger-icon span { background-color: #ffffff !important; }

    /* Logo: always render the white logo regardless of Velzon's dark/light logo toggles */
    #page-topbar .horizontal-logo { display: block !important; }
    #page-topbar .horizontal-logo .logo-dark { display: inline-block !important; }
    #page-topbar .horizontal-logo .logo-light { display: none !important; }
    #page-topbar .horizontal-logo .logo-lg { display: inline-block !important; }
    #page-topbar .logo-lg img { display: inline-block !important; height: 30px !important; width: auto !important; max-width: none !important; }

    /* Velzon's built-in hamburger is unused; the offcanvas toggle drives mobile/ipad nav */
    #page-topbar #topnav-hamburger-icon { display: none !important; }

    /* Desktop / laptop: no hamburger of any kind, logo flush to the left */
    @media (min-width: 1025px) {
        #page-topbar .biz-mobile-toggle { display: none !important; }
        #page-topbar .navbar-header { padding-left: 0 !important; }
        #page-topbar .horizontal-logo { padding-left: 16px !important; }
    }

    /* mobile + ipad only: show offcanvas toggle, hide the inline horizontal menu */
    .biz-mobile-toggle { display: none; }
    @media (max-width: 1024.98px) {
        .biz-mobile-toggle { display: inline-flex !important; align-items: center; justify-content: center; }
        #page-topbar .app-menu.navbar-menu { display: none !important; }
        #page-topbar .horizontal-logo { display: block !important; }
        #page-topbar .logo-lg img { margin-left: 0 !important; margin-top: 0 !important; height: 28px !important; width: auto !important; }
        #page-topbar .navbar-header { padding-left: 8px; }
    }
</style>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- Mobile menu toggle (opens offcanvas) -->
                <button type="button" class="btn btn-sm px-3 fs-22 header-item biz-mobile-toggle shadow-none text-white"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav">
                    <i class="bx bx-menu"></i>
                </button>

                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="<?php echo $tb_homeUrl; ?>" class="logo logo-dark">
                        <span class="logo-lg"><img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="BizAdmin" height="30"></span>
                    </a>
                    <a href="<?php echo $tb_homeUrl; ?>" class="logo logo-light">
                        <span class="logo-lg"><img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="BizAdmin" height="30"></span>
                    </a>
                </div>

                <?php
                // Dynamic horizontal role/system menu (desktop) - unchanged behaviour.
                $common_view_path = APPPATH . 'views/topMenus/sidebar.php';
                include($common_view_path);
                ?>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <a href="<?php echo $tb_homeUrl; ?>"><i class='bx bxs-home fs-22 text-white'></i></a>
                    </button>
                </div>

                <?php if ($tb_settingsUrl !== ''): ?>
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <a href="<?php echo base_url($tb_settingsUrl); ?>" title="Settings"><i class='bx bx-cog fs-22 text-white'></i></a>
                    </button>
                </div>
                <?php endif; ?>

                <?php if ($isCatering): ?>
                <!-- Catering notifications bell (preserved) -->
                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22 text-white'></i>
                        <?php
                        $tb_notifications = array();
                        if (isset($this->tenantDb)) {
                            $tb_q = $this->tenantDb->query("SELECT id, description, orderID, date_added, time_added FROM Catering_notification ORDER BY date_added DESC, time_added DESC LIMIT 5");
                            if ($tb_q) { $tb_notifications = $tb_q->result_array(); }
                        }
                        $tb_notification_count = count($tb_notifications);
                        ?>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><?php echo $tb_notification_count; ?><span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                        <div class="dropdown-head bg-dark bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col"><h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6></div>
                                    <div class="col-auto dropdown-tabs"><span class="badge bg-light-subtle text-body fs-13"><?php echo $tb_notification_count; ?> New</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content position-relative">
                            <div class="tab-pane fade show active py-2 ps-2">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php foreach ($tb_notifications as $tb_n): ?>
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3 flex-shrink-0"><span class="avatar-title bg-info-subtle text-info rounded-circle fs-16"><i class="bx bx-badge-check"></i></span></div>
                                            <div class="flex-grow-1">
                                                <a href="#!" class="stretched-link"><h6 class="mt-0 mb-2 lh-base text-black"><?php echo isset($tb_n['description']) ? $tb_n['description'] : ''; ?></h6></a>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-black"><span><i class="mdi mdi-clock-outline"></i> <?php echo isset($tb_n['date_added']) ? date('d-m-Y', strtotime($tb_n['date_added'])) : ''; ?></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="/theme-assets/images/users/avatar-1.jpg" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo ($this->session->userdata('username') != '' ? $this->session->userdata('username') : ''); ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">
                                    <?php if ($this->session->userdata('location_name') != '') { ?><i class="bx bx-map"></i> <?php echo $this->session->userdata('location_name'); ?><?php } ?>
                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">
                                    <?php if ($this->session->userdata('system_id') != '') { ?><i class=" bx bx-laptop"></i> <?php echo fetchSystemNameFromId($this->session->userdata('system_id')); ?><?php } ?>
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end bg-primary">
                        <h6 class="dropdown-header">Welcome <?php echo ($this->session->userdata('username') != '' ? $this->session->userdata('username') : ''); ?>!</h6>
                        <?php if ($this->ion_auth->is_admin() || $this->ion_auth->in_group(['manager'])) { ?>
                            <?php if ($tb_settingsUrl !== '') { ?>
                            <a class="dropdown-item" href="<?= base_url($tb_settingsUrl) ?>"><i class="mdi mdi-store-cog text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                            <?php } ?>
                            <?php if ($tb_isHr) { ?>
                            <a class="dropdown-item" href="<?= base_url('HR/sites') ?>"><i class="mdi mdi-store-cog text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Create Sites</span></a>
                            <a class="dropdown-item" href="<?= base_url('HR/prep') ?>"><i class="mdi mdi-store-cog text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Create Prep Area</span></a>
                            <?php } ?>
                        <?php } ?>
                        <a class="dropdown-item" href="<?= base_url('auth/logout') ?>"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ===== Shared mobile offcanvas navigation (dynamic role/system menu) ===== -->
<?php
$navHomeUrl = $tb_homeUrl;
$navMenus   = $tb_menus;
include(APPPATH . 'views/general/mobile_nav_offcanvas.php');
?>
