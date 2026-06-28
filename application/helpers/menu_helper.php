<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// always try to pass user_id and role_id so that if menu is defined at user_leve it will priortized else role level menu will be fetched with 
// selected = selected for those indexes wch has been assigned to this role_id or user_id
if (!function_exists('fetch_render_menu')) {
    // $callType is to decide if this mthod has been called by ajax or normal call, incase of ajax it will return json_encoded object
    function fetch_render_menu($system_id,$user_id='',$role_id='',$callType='') {
        $CI = &get_instance();
        $CI->load->database();
        $CI->load->library('session');
        $CI->load->library('ion_auth');

        // Fetch menu items for this particular system like HR, Supplier etc.
        $menu_for = $system_id;
        $CI->db->where('menu_for', $menu_for);
        $CI->db->where('is_deleted', 0);
        $CI->db->where('status', 1);
        $CI->db->order_by('sort_order', 'ASC'); 
        
        $query = $CI->db->get('menu');
        $menu_items = $query->result();
      

        // Fetch sub menu items for each menu item
        foreach ($menu_items as $menu_item) {
            $menu_item->sub_menu = get_sub_menu_by_parent_id($menu_item->menu_id);
        }
        
        if($role_id == 1){
            // for superadmin
         return  addSelectedToMenu($menu_items);
        }
      
    //   NOTE : if menu is assigned at user level to user A, than if u assign menu at role level , even though user A has that role, lets say Admin
    //   he cannot see those menus assigned to role Admin, u have to assign all menu at user level , if u want user A to see any menu
    //   but vice versa will work that is if 5 menus are assigned to role Admin and if u want to user A to restrict and see 3 menus only u can do so at user level
    //   menu configuration
    
        // filter only those menu Out of all menu which are assigned to this user for this system
        $userMenus = array(); $userSubMenus = array();
        if($user_id != ''){
           
         $user = $CI->ion_auth->user($user_id)->row();
         $overwriteRoleLevelMenu = $user->overwriteRoleLevelMenu;
         
         $userMenus = (($user) ? unserialize($user->menu_ids) : array());   
         $userSubMenus = (($user) ? unserialize($user->sub_menu_ids) : array());
          $userMenus = (isset($userMenus[$system_id]) ? $userMenus[$system_id] : array());
         $userSubMenus = (isset($userSubMenus[$system_id]) ? $userSubMenus[$system_id] : array());
        }
       // basically we have a checkbox in configure menu wch decide if we want to hide all menus from a specific user and even menu assigned at role level, wilnot be vis
    //   visible
        
        if(empty($userMenus) && (isset($overwriteRoleLevelMenu) && $overwriteRoleLevelMenu == 0)){
         $role = $CI->ion_auth->group($role_id)->row();;
         
         $userMenus = (($role) ? unserialize($role->menu_ids) : array());   
    //  
         $userSubMenus = (($role) ? unserialize($role->sub_menu_ids) : array()); 
         $userMenus = (isset($userMenus[$system_id]) ? $userMenus[$system_id] : array());
         $userSubMenus = (isset($userSubMenus[$system_id]) ? $userSubMenus[$system_id] : array());
        }
     
         if(!empty($userMenus)){
          $filteredMenus = array();
          $filteredMenus = array_filter($menu_items, function ($menu) use ($userMenus, $userSubMenus) {
           return in_array($menu->menu_id, $userMenus) &&
           count(array_filter($menu->sub_menu, function ($subMenu) use ($userSubMenus) {
               return in_array($subMenu->id, $userSubMenus);
           })) > 0;
         });
      
          // add "selected" index for those menus or submenus wch are assigned to this user_id or role_id for rest dont add index
          $filteredMenus = array_map(function ($menu) use ($userMenus, $userSubMenus) {
          $menu->selected = in_array($menu->menu_id, $userMenus);
          $menu->sub_menu = array_map(function ($subMenu) use ($userSubMenus) {
          $subMenu->selected = in_array($subMenu->id, $userSubMenus);
            return $subMenu;
           }, $menu->sub_menu);
             return $menu;
            }, $menu_items);   
  
         } else{
             return $menu_items;
         }
      
      
         return $filteredMenus;
    }
    
       function addSelectedToMenu( $menuArray){
        if (empty($menuArray)) {
            return [];
        }

        $modifiedArray = [];
        foreach ($menuArray as $index => $menu) {
            // Ensure menu is an object or array with required fields
            if (!is_object($menu) && !is_array($menu)) {
                continue;
            }

            // Convert to stdClass if array
            $menuObj = is_array($menu) ? (object) $menu : clone $menu;
            
            // Add selected => true to menu
            $menuObj->selected = true;
            
            // Initialize sub_menu if not set
            $menuObj->sub_menu = isset($menuObj->sub_menu) && is_array($menuObj->sub_menu) 
                ? $menuObj->sub_menu 
                : [];
            
            // Process sub-menus
            $modifiedSubMenu = [];
            foreach ($menuObj->sub_menu as $subIndex => $subMenu) {
                if (!is_object($subMenu) && !is_array($subMenu)) {
                    continue;
                }
                
                // Convert to stdClass if array
                $subMenuObj = is_array($subMenu) ? (object) $subMenu : clone $subMenu;
                
                // Add selected => true to sub-menu
                $subMenuObj->selected = true;
                
                $modifiedSubMenu[$subIndex] = $subMenuObj;
            }
            
            // Ensure sub_menu is numerically indexed
            $menuObj->sub_menu = array_values($modifiedSubMenu);
            
            $modifiedArray[$index] = $menuObj;
        }
        
        // Ensure top-level array is numerically indexed
        return array_values($modifiedArray);
    }
    
    function fetch_render_menu_for_setting($system_id,$user_id='',$role_id='',$callType='') {
        $CI = &get_instance();
        $CI->load->database();
        $CI->load->library('session');
        $CI->load->library('ion_auth');

        // Fetch menu items for this particular system like HR, Supplier etc.
        $menu_for = $system_id;
        $CI->db->where('menu_for', $menu_for);
        $CI->db->where('is_deleted', 0);
        $CI->db->where('status', 1);
        $CI->db->order_by('sort_order', 'ASC'); 
        
        $query = $CI->db->get('menu');
        $menu_items = $query->result();
      

        // Fetch sub menu items for each menu item
        foreach ($menu_items as $menu_item) {
            $menu_item->sub_menu = get_sub_menu_by_parent_id($menu_item->menu_id);
        }
      
  
    
        // filter only those menu Out of all menu which are assigned to this user for this system
        $userMenus = array(); $userSubMenus = array();
        if($user_id != ''){
           
         $user = $CI->ion_auth->user($user_id)->row();
         $overwriteRoleLevelMenu = $user->overwriteRoleLevelMenu;
         
         $userMenus = (($user) ? unserialize($user->menu_ids) : array());   
         $userSubMenus = (($user) ? unserialize($user->sub_menu_ids) : array());
          $userMenus = (isset($userMenus[$system_id]) ? $userMenus[$system_id] : array());
         $userSubMenus = (isset($userSubMenus[$system_id]) ? $userSubMenus[$system_id] : array());
        }
       // basically we have a checkbox in configure menu wch decide if we want to hide all menus from a specific user and even menu assigned at role level, wilnot be vis
    //   visible
        
        if(empty($userMenus)){
         $role = $CI->ion_auth->group($role_id)->row();;
        //  echo $system_id;
         $userMenus = (($role) ? unserialize($role->menu_ids) : array());   
        //   echo "<pre>"; print_r($userMenus); exit;
         $userSubMenus = (($role) ? unserialize($role->sub_menu_ids) : array()); 
         $userMenus = (isset($userMenus[$system_id]) ? $userMenus[$system_id] : array());
         $userSubMenus = (isset($userSubMenus[$system_id]) ? $userSubMenus[$system_id] : array());
        }
     
         if(!empty($userMenus)){
             
          $filteredMenus = array();
          $filteredMenus = array_filter($menu_items, function ($menu) use ($userMenus, $userSubMenus) {
           return in_array($menu->menu_id, $userMenus) &&
           count(array_filter($menu->sub_menu, function ($subMenu) use ($userSubMenus) {
               return in_array($subMenu->id, $userSubMenus);
           })) > 0;
         });
      
          // add "selected" index for those menus or submenus wch are assigned to this user_id or role_id for rest dont add index
          $filteredMenus = array_map(function ($menu) use ($userMenus, $userSubMenus) {
          $menu->selected = in_array($menu->menu_id, $userMenus);
          $menu->sub_menu = array_map(function ($subMenu) use ($userSubMenus) {
          $subMenu->selected = in_array($subMenu->id, $userSubMenus);
            return $subMenu;
           }, $menu->sub_menu);
             return $menu;
            }, $menu_items);   
  
         } else{
             return $menu_items;
         }
      
      
         return $filteredMenus;
    }

    function get_sub_menu_by_parent_id($parent_menu_id) {
        $CI = &get_instance();
        $CI->load->database(); // Load the database library if not already loaded

        // Fetch sub menu items based on 'parent_menu_id'
         $CI->db->where('is_deleted', 0);
        $CI->db->where('status', 1);
        $CI->db->where('parent_menu_id', $parent_menu_id);
        $CI->db->order_by('sort_order', 'ASC');
        $query = $CI->db->get('sub_menu');
        return $query->result();
    }
}

if (!function_exists('bizadmin_menu_icon')) {
    /**
     * Map a menu/sub-menu name to an icon class for the mobile navigation.
     * $set: 'fa' (Font Awesome) or 'bx' (Boxicons).
     */
    function bizadmin_menu_icon($menuName, $set = 'fa') {
        $key = strtolower(trim((string) $menuName));
        $map = array(
            'dashboard'   => array('fa' => 'fa-house',           'bx' => 'bx-home-alt'),
            'profile'     => array('fa' => 'fa-user',            'bx' => 'bx-user'),
            'employee'    => array('fa' => 'fa-users',           'bx' => 'bx-group'),
            'employees'   => array('fa' => 'fa-users',           'bx' => 'bx-group'),
            'staff'       => array('fa' => 'fa-users',           'bx' => 'bx-group'),
            'leave'       => array('fa' => 'fa-calendar-minus',  'bx' => 'bx-calendar-minus'),
            'roster'      => array('fa' => 'fa-calendar-days',   'bx' => 'bx-calendar'),
            'shift'       => array('fa' => 'fa-calendar-days',   'bx' => 'bx-calendar'),
            'timesheet'   => array('fa' => 'fa-clock',           'bx' => 'bx-time-five'),
            'attendance'  => array('fa' => 'fa-clock',           'bx' => 'bx-time-five'),
            'compliance'  => array('fa' => 'fa-shield-halved',   'bx' => 'bx-shield'),
            'memo'        => array('fa' => 'fa-bullhorn',        'bx' => 'bx-broadcast'),
            'task'        => array('fa' => 'fa-list-check',      'bx' => 'bx-list-check'),
            'tasks'       => array('fa' => 'fa-list-check',      'bx' => 'bx-list-check'),
            'document'    => array('fa' => 'fa-file-lines',      'bx' => 'bx-file'),
            'documents'   => array('fa' => 'fa-file-lines',      'bx' => 'bx-file'),
            'hiring'      => array('fa' => 'fa-user-plus',       'bx' => 'bx-user-plus'),
            'recruit'     => array('fa' => 'fa-user-plus',       'bx' => 'bx-user-plus'),
            'incident'    => array('fa' => 'fa-triangle-exclamation', 'bx' => 'bx-error'),
            'injury'      => array('fa' => 'fa-kit-medical',     'bx' => 'bx-plus-medical'),
            'resignation' => array('fa' => 'fa-file-signature',  'bx' => 'bx-file'),
            'reimburse'   => array('fa' => 'fa-receipt',         'bx' => 'bx-receipt'),
            'performance' => array('fa' => 'fa-chart-line',      'bx' => 'bx-line-chart'),
            'report'      => array('fa' => 'fa-chart-column',    'bx' => 'bx-bar-chart-alt-2'),
            'setting'     => array('fa' => 'fa-gear',            'bx' => 'bx-cog'),
            'cash'        => array('fa' => 'fa-money-bill',      'bx' => 'bx-money'),
            'supplier'    => array('fa' => 'fa-truck',           'bx' => 'bx-package'),
            'recipe'      => array('fa' => 'fa-utensils',        'bx' => 'bx-bowl-hot'),
            'clean'       => array('fa' => 'fa-broom',           'bx' => 'bx-spray-can'),
            'catering'    => array('fa' => 'fa-bell-concierge',  'bx' => 'bx-restaurant'),
        );
        $default = array('fa' => 'fa-angle-right', 'bx' => 'bx-chevron-right');
        $icon = $default;
        foreach ($map as $needle => $icons) {
            if (strpos($key, $needle) !== false) { $icon = $icons; break; }
        }
        $cls = isset($icon[$set]) ? $icon[$set] : $default[$set];
        return ($set === 'bx') ? ('bx ' . $cls) : ('fa-solid ' . $cls);
    }
}
