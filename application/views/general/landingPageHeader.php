<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm-hover-active" data-sidebar-image="none" data-preloader="disable">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Bizadmin</title>
  <link rel="shortcut icon" href="<?php echo base_url();?>login-assets/img/favicon.jpeg" />
  
 
    
   <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
 // we have a common header to import all css for all the Apps like HR, Cash , Supplier etc
     $common_view_path = APPPATH . 'views/general/header.php';
      $canvas_view_path = APPPATH . 'views/general/systemCanvasForTabView.php';
     include($common_view_path);
     
?>
   
  

</head>
<body data-base-url="<?php echo base_url(); ?>">
<?php include($canvas_view_path); ?>

    <div id="layout-wrapper">
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
<header id="page-topbar" style="background-color: #1a2f52;">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                   <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                     data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <div class="navbar-brand-box horizontal-logo">
                    
                    
                    <a href="/auth/checklist" class="logo logo-dark">
                        
                        <span class="logo-lg">
                            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="" height="30">
                        </span>
                    </a>

                    <a href="/auth/checklist" class="logo logo-light">
                       
                        <span class="logo-lg">
                            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="" height="30">
                        </span>
                    </a>
                </div>
                <!--dont delete this UL tag it will cause Js  issue-->
                <ul class="navbar-nav" id="navbar-nav">
                    </ul>
               
             

                <!-- App Search-->
                
            </div>
       <?php $showL = true; if(isset($hideLocationIcon) && $hideLocationIcon== true) {  $showL= false;  } ?>
            <div class="d-flex align-items-center">
                <?php if($showL) {   ?>
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"   class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <a href="/auth/dashboard"><i class='bx bxs-map fs-22 text-white'></i></a>
                    </button>
                </div>
                  <?php } ?>
                 <?php $show = true; if(isset($hideNotification) && $hideNotification== true) {  $show= false;  } ?>
                 <?php if($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('manager')) {}else { $show = false; } ?>
                 <?php if($show) {   ?>
                 <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class='bx bx-bell fs-22 text-white'></i>
                        <?php if(isset($allNotificationsCount) && $allNotificationsCount !='') { ?>
                        <span  class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                            <?php echo $allNotificationsCount; ?><span class="visually-hidden">unread messages</span>
                            </span>
                                <?php } ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" style="width:340px" aria-labelledby="page-header-notifications-dropdown">
                        

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge badge-soft-light fs-13"> <?php echo (isset($allNotificationsCount) && $allNotificationsCount !='' ? $allNotificationsCount.' New' : '') ?> </span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                    id="notificationItemsTab" role="tablist">
                                  
                                    
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                                            aria-selected="false">
                                            Alerts
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a class="nav-link " data-bs-toggle="tab" href="#messages-tab" role="tab"
                                            aria-selected="false">
                                            Messages
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>

                        <div class="tab-content" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="alerts-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                   <?php if(!empty($allNotifications)) {  ?>
                                   <p class="mx-3"><a href="#" class="link-danger link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover" onclick="markAllread('alert')">Mark all as read</a></p>
                                   <?php foreach($allNotifications as $allNotification) { ?>
                                     <?php if(!empty($allNotification['notification_type'] == 'alert')) {  ?>
                                     <input type="hidden" id="notification_Alert" name="notification_Alert[]" value="<?php echo $allNotification['id'] ?>">
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                           <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                                                    <i class="bx bx-message-square-dots"></i>
                                                </span>
                                            </div>
                                           <div class="flex-1">
                                                <a href="#!" class="stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold text-black"><?php echo fetchSystemNameFromId($allNotification['system_id']);  ?></h6>
                                                </a>
                                                <div class="fs-13 text-faded">
                                                    <p class="mb-1">
                                        <?php echo $allNotification['title']; ?>                
                                                        </p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-black">
                                                    <span><i class="mdi mdi-clock-outline"></i> <?php echo date('h:i A',strtotime($allNotification['time'])); ?></span>
                                                    
                                                </p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <?php } } ?>
                                    
                                       
                                  <?php } else{  ?>
                                  
                                    <div class="w-25 w-sm-50 pt-3 mx-auto">
                                    <img src="/theme-assets/images/svg/bell.svg" class="img-fluid" alt="user-pic">
                                </div>
                                <div class="text-center pb-5 mt-2">
                                    <h6 class="fs-18 fw-semibold lh-base text-black">Hey! You have no any notifications </h6>
                                </div>
                                <?php } ?>

                                  <div class="my-3 text-center">
                                        <a href="/notification/notification" class="btn btn-soft-success waves-effect waves-light">View
                                            All Notifications <i class="ri-arrow-right-line align-middle"></i></a>
                                    </div>
                                   
                                </div>

                            </div>

                          <div class="tab-pane fade  py-2 ps-2" id="messages-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                   <?php if(!empty($allNotifications)) {  ?>
                                   <p class="mx-3"><a href="#" class="link-danger link-offset-2 text-decoration-underline link-underline-opacity-25 link-underline-opacity-100-hover" onclick="markAllread('msg')">Mark all as read</a></p>
                                   <?php foreach($allNotifications as $allNotification) { ?>
                                     <?php if(!empty($allNotification['notification_type'] == 'msg')) {  ?>
                                      <input type="hidden" id="notification_Msg" name="notification_Msg[]" value="<?php echo $allNotification['id'] ?>">
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                           <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                                                    <i class="bx bx-message-square-dots"></i>
                                                </span>
                                            </div>
                                           <div class="flex-1">
                                                <a href="#!" class="stretched-link">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold text-black"> <?php echo fetchSystemNameFromId($allNotification['system_id']);  ?></h6>
                                                </a>
                                                <div class="fs-13 text-faded">
                                                    <p class="mb-1">
                                        <?php echo $allNotification['title']; ?>                
                                                        </p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-black">
                                                    <span><i class="mdi mdi-clock-outline"></i> <?php echo date('h:i A',strtotime($allNotification['time'])); ?></span>
                                                    
                                                </p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <?php } } ?>
                                    
                                       
                                  <?php } else{  ?>
                                  
                                    <div class="w-25 w-sm-50 pt-3 mx-auto">
                                    <img src="/theme-assets/images/svg/bell.svg" class="img-fluid" alt="user-pic">
                                </div>
                                <div class="text-center pb-5 mt-2">
                                    <h6 class="fs-18 fw-semibold lh-base text-black">Hey! You have no any notifications </h6>
                                </div>
                                <?php } ?>

                                  <div class="my-3 text-center">
                                        <a href="/notification/notification" class="btn btn-soft-success waves-effect waves-light">View
                                            All Notifications <i class="ri-arrow-right-line align-middle"></i></a>
                                    </div>
                                   
                                </div>

                            </div>
                           
                        </div>
                    </div>
                </div>
                 <?php } ?>
              
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="/theme-assets/images/users/user-dummy-img.jpg"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 text-white fw-medium user-name-text"><?php echo ($this->session->userdata('username') !='' ? $this->session->userdata('username') : '') ; ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-white user-name-sub-text">
                                   <?php if($this->session->userdata('location_name') !=''){ ?>
                                    <i class="bx bx-map"></i> <?php echo $this->session->userdata('location_name') ; ?>
                                    <?php } ?>
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end bg-primary">
                        <!-- item-->
                    
                        <h6 class="dropdown-header">Welcome <?php echo ($this->session->userdata('username') !='' ? $this->session->userdata('username') :''); ?>!</h6>
                        <!--<a class="dropdown-item" href="<?= base_url('auth/change_password'); ?>"><i-->
                        <!--        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span-->
                        <!--        class="align-middle">Profile</span></a>-->
                          <?php if ($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('manager')): ?>
                           <a class="dropdown-item"  href="<?= base_url('auth/group_listing') ?>">
                             <i class="mdi mdi-account-plus text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">Roles</span>
                        </a>
                        
                         <a class="dropdown-item" href="<?= base_url('auth/userListing') ?>">
                             <i class="mdi mdi-account-multiple-plus text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">Users</span>
                        </a>
                        <?php if($this->session->userdata('location_id') !='' && $this->uri->uri_string() != 'auth/dashboard'){ ?>
                         <a class="dropdown-item" href="<?= base_url('checklist/checklistListing') ?>">
                             <i class="mdi mdi-alarm-check text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">Checklist</span>
                        </a>
                        <?php } ?>
                        
                      <?php endif  ?>
                   
                        <a class="dropdown-item" href="<?= base_url('auth/logout') ?>"><i
                                class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">Logout</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
</div>
<script>
$(document).ready(function(){
    $(".hamburger-icon").removeClass("open");
         
})

function markAllread(typeOfNotification){
    var values = [];
   
    if(typeOfNotification == 'msg'){
     $('input[name="notification_Msg[]"]').each(function() {
       values.push($(this).val());
       });   
    }else{
     $('input[name="notification_Alert[]"]').each(function() {
       values.push($(this).val());
       });   
    }
      
           $.ajax({
            type: "POST",
            url: "/Auth/markAllNotificationAsread", // Replace with your controller's URL
            data: { values: values },
            success: function (response) {
               location.reload();
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
}
</script>

