<style>
    div#notificationWrap a.dropdown-item {
        display: flex;
        align-items: flex-start;
        white-space: break-spaces;
        font-size: 13px;
    }
    div#notificationWrap {
        min-width: 320px;
    }
    #notificationWrap i {
        color: #864868 !important;
    }
</style>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    
                    <a href="/Catering/<?php echo $this->session->userdata('system_id'); ?>" class="logo logo-dark">
                        
                        <span class="logo-lg">
                            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="" height="30">
                        </span>
                    </a>

                    <a href="/Catering/<?php echo $this->session->userdata('system_id'); ?>" class="logo logo-light">
                       
                        <span class="logo-lg">
                            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="" height="30">
                        </span>
                        
                    </a>
                </div>
               
                 <?php 
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

                <!-- App Search-->
                
            </div>

            <div class="d-flex align-items-center">
            

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"   class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <a href="/Catering/<?php echo $this->session->userdata('system_id'); ?>" class="text-white"><i class='bx bxs-home fs-22'></i></a>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <a href="/auth/checklist" class="text-white"><i class='bx bx-laptop fs-22'></i></a>
                    </button>
                </div>
                
                 <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
        <i class='bx bx-bell fs-22'></i>
        <?php 
        // Fetch notification count and data
        $query = $this->tenantDb->query("SELECT id, description, orderID, date_added, time_added FROM Catering_notification ORDER BY date_added DESC, time_added DESC LIMIT 5");
        $notifications = $query->result_array();
        $notification_count = count($notifications);
        ?>
        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><?php echo $notification_count; ?><span class="visually-hidden">unread messages</span></span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

        <div class="dropdown-head bg-dark bg-pattern rounded-top">
            <div class="p-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                    </div>
                    <div class="col-auto dropdown-tabs">
                        <span class="badge bg-light-subtle text-body fs-13"><?php echo $notification_count; ?> New</span>
                    </div>
                </div>
            </div>

            <div class="px-2 pt-2">
                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab" aria-selected="true">
                            All (<?php echo $notification_count; ?>)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content position-relative" id="notificationItemsTabContent">
            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    <?php foreach ($notifications as $notification): ?>
                    <div class="text-reset notification-item d-block dropdown-item position-relative">
    <div class="d-flex">
        <div class="avatar-xs me-3 flex-shrink-0">
            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                <i class="bx bx-badge-check"></i>
            </span>
        </div>
        <div class="flex-grow-1">
            <a href="#!" class="stretched-link">
                <h6 class="mt-0 mb-2 lh-base text-black">
                    <?php echo isset($notification['description']) ? $notification['description'] : ''; ?>
                </h6>
            </a>
            <p class="mb-0 fs-11 fw-medium text-uppercase text-black">
                <span>
                    <i class="mdi mdi-clock-outline"></i>
                    <?php 
    echo isset($notification['date_added']) 
        ? date('d-m-Y', strtotime($notification['date_added'])) 
        : '';

    echo ' ';

    echo isset($notification['time_added']) 
        ? date('h:i A', strtotime($notification['time_added'])) 
        : '';
?>s

                </span>
            </p>
        </div>
        <div class="px-2 fs-15">
            <div class="form-check notification-check">
                <input class="form-check-input" type="checkbox" value="" id="all-notification-check<?php echo isset($notification['id']) ? $notification['id'] : ''; ?>">
                <label class="form-check-label" for="all-notification-check<?php echo isset($notification['id']) ? $notification['id'] : ''; ?>"></label>
            </div>
        </div>
    </div>
</div>

                    <?php endforeach; ?>

                    <!--<div class="my-3 text-center view-all">-->
                    <!--    <button type="button" class="btn btn-sm btn-soft-success waves-effect waves-light">View All Notifications <i class="ri-arrow-right-line align-middle"></i></button>-->
                    <!--</div>-->
                </div>
            </div>

            <div class="notification-actions" id="notification-actions">
                <div class="d-flex text-black justify-content-center">
                    Select <div id="select-content" class="text-body fw-semibold px-1">0</div> Result <button type="button" class="btn btn-link link-danger p-0 ms-3" data-bs-toggle="modal" data-bs-target="#removeNotificationModal">Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>

              
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="/theme-assets/images/users/avatar-1.jpg"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo ($this->session->userdata('username') !='' ? $this->session->userdata('username') : '') ; ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-white user-name-sub-text">
                                   <?php if($this->session->userdata('location_name') !=''){ ?>
                                    <i class="bx bx-map"></i> <?php echo $this->session->userdata('location_name') ; ?>
                                    <?php } ?>
                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-white user-name-sub-text">
                                   <?php if($this->session->userdata('system_id') !=''){ ?>
                                    <i class=" bx bx-laptop"></i> <?php echo fetchSystemNameFromId($this->session->userdata('system_id')); ?>
                                    <?php } ?>
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end bg-primary">
                        <!-- item-->
                    
                        <h6 class="dropdown-header">Welcome <?php echo ($this->session->userdata('username') !='' ? $this->session->userdata('username') :''); ?>!</h6>
                        <!--<a class="dropdown-item" href="/profile/<?php echo ($this->session->userdata('user_id') !='' ? $this->session->userdata('user_id') :''); ?>"><i-->
                        <!--        class="mdi mdi-account-circle text-black fs-16 align-middle me-1"></i> <span-->
                        <!--        class="align-middle">Profile</span></a>-->
                        
                        
                        
                 <?php if ($this->session->userdata('is_admin')){ ?>
                          <a class="dropdown-item" target="_blank" href="<?= base_url('Catering/saveSettings') ?>"><i class="mdi mdi-store-cog text-muted fs-16 align-middle me-1"></i> 
                           <span class="align-middle">Settings</span></a>   
                       <?php  }  ?>
                       
                        
                        <a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
                        <i class="mdi mdi-logout text-black fs-16 align-middle me-1"></i>
                         <span class="align-middle" data-key="t-logout">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

