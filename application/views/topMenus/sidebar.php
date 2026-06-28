<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <?php $roleId = $this->ion_auth->get_users_groups()->row()->id;  ?>
      <?php $allMenus = fetch_render_menu($this->session->userdata('system_id'),$this->session->userdata('user_id'),$roleId,'frontSites');   ?>
     
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                  <?php if(!empty($allMenus)) {   ?>
                  <?php $tb_hideProfile = ($this->ion_auth->is_admin() || $this->ion_auth->in_group(['manager'])); ?>
                      <?php foreach($allMenus as $allMenu) {  ?> 
                      <?php if($tb_hideProfile && strcasecmp(trim((string)$allMenu->menu_name), 'Profile') === 0) { continue; } ?>
                      <?php // echo "<pre>"; print_r($allMenu); exit; ?>
                      <?php if(isset($allMenu->sub_menu) && !empty($allMenu->sub_menu) && isset($allMenu->selected) && $allMenu->selected !='')  { ?>
                      
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAccount" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps"> <span data-key="t-landing"><?php echo $allMenu->menu_name; ?></span></a>
                           <div class=" menu-dropdown show" id="sidebarAccount" style="left: 0% !important;right: 0% !important;">
                                <ul class="nav nav-sm flex-column">
                                  <?php foreach($allMenu->sub_menu as $subMenu) {  ?> 
                                  <?php if(isset($subMenu->selected) && $subMenu->selected !='') {  ?>
                                   <li class="nav-item"><a href="<?php echo $subMenu->sub_menu_url; ?>" class="nav-link" data-key="t-calendar"><?php echo $subMenu->sub_menu_name; ?> </a></li>
                                 <?php }  ?>
                                 <?php }  ?>
                                </ul>
                            </div>
                        </li>
                        <?php  } else { ?>
                        <?php if(isset($allMenu->selected) && $allMenu->selected !='') {  ?>
                        <li class="nav-item">
                           <a href="<?php echo $allMenu->menu_url; ?>" class="nav-link" data-key="t-calendar"><?php echo $allMenu->menu_name; ?></a>
                        </li>
                         <?php }  ?>
                         <?php }  ?>
                        <?php }  ?>
                        <?php }  ?>
                        
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
