<?php
/* =====================================================================
 * BizAdmin shared mobile navigation offcanvas.
 * Used by the common header (topbar_common.php) AND the mobile
 * dashboards so the mobile nav is identical everywhere.
 *
 * Optional inputs (set before include):
 *   $navHomeUrl - URL for the logo link (default '#').
 *   $navMenus   - pre-fetched menu array; otherwise fetched here.
 *
 * Requires Boxicons CSS (theme-assets/css/icons.min.css) on the page.
 * ===================================================================== */
if (!isset($navHomeUrl)) { $navHomeUrl = '#'; }
if (!isset($navMenus)) {
    $navMenus = fetch_render_menu(
        $this->session->userdata('system_id'),
        $this->session->userdata('user_id'),
        $this->ion_auth->get_users_groups()->row()->id,
        'frontSites'
    );
}
?>
<style>
    #mobileNav { background: #ffffff; color: #1e293b; max-width: 280px; }
    #mobileNav .offcanvas-header { background: #1a2f52; color: #fff; }
    #mobileNav .offcanvas-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    #mobileNav .menu-title { color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; padding: 14px 16px 6px; }
    #mobileNav .biz-nav-link { display: flex; align-items: center; gap: 10px; color: #1e293b; padding: 13px 16px; text-decoration: none; font-size: 14px; border-bottom: .5px solid #f1f5f9; }
    #mobileNav .biz-nav-link:hover,
    #mobileNav .biz-nav-link:focus { background: #f4f6f9; color: #1a2f52; }
    #mobileNav .biz-nav-link i { color: #25A69A; width: 18px; text-align: center; font-size: 16px; flex-shrink: 0; }
    #mobileNav .biz-nav-group > a { justify-content: space-between; }
    #mobileNav .biz-nav-group > a .biz-caret { color: #94a3b8; margin-left: auto; transition: transform .2s; }
    #mobileNav .biz-nav-group > a[aria-expanded="true"] .biz-caret { transform: rotate(180deg); }
    #mobileNav .biz-subnav { background: #f8fafc; }
    #mobileNav .biz-subnav .biz-nav-link { font-size: 13px; color: #475569; padding-left: 44px; }
    #mobileNav .biz-subnav .biz-nav-link i { font-size: 13px; }
</style>
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header">
        <a href="<?php echo $navHomeUrl; ?>" class="d-flex align-items-center text-decoration-none">
            <img src="/theme-assets/images/logo/BizAdminLogo_White.png" alt="BizAdmin" height="26">
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="menu-title">Menu</div>
        <?php if (!empty($navMenus)) { $nav_i = 0; foreach ($navMenus as $nav_menu) { $nav_i++; ?>
            <?php if (isset($nav_menu->sub_menu) && !empty($nav_menu->sub_menu) && isset($nav_menu->selected) && $nav_menu->selected != '') { ?>
                <div class="biz-nav-group">
                    <a class="biz-nav-link" data-bs-toggle="collapse" href="#navSub<?php echo $nav_i; ?>" role="button" aria-expanded="false">
                        <i class="<?php echo bizadmin_menu_icon($nav_menu->menu_name, 'bx'); ?>"></i>
                        <span><?php echo $nav_menu->menu_name; ?></span>
                        <i class="bx bx-chevron-down biz-caret"></i>
                    </a>
                    <div class="collapse biz-subnav" id="navSub<?php echo $nav_i; ?>">
                        <?php foreach ($nav_menu->sub_menu as $nav_sub) { if (isset($nav_sub->selected) && $nav_sub->selected != '') { ?>
                            <a class="biz-nav-link" href="<?php echo $nav_sub->sub_menu_url; ?>"><i class="<?php echo bizadmin_menu_icon($nav_sub->sub_menu_name, 'bx'); ?>"></i> <span><?php echo $nav_sub->sub_menu_name; ?></span></a>
                        <?php } } ?>
                    </div>
                </div>
            <?php } else { if (isset($nav_menu->selected) && $nav_menu->selected != '') { ?>
                <a class="biz-nav-link" href="<?php echo $nav_menu->menu_url; ?>"><i class="<?php echo bizadmin_menu_icon($nav_menu->menu_name, 'bx'); ?>"></i> <span><?php echo $nav_menu->menu_name; ?></span></a>
            <?php } } ?>
        <?php } } ?>
        <a class="biz-nav-link" href="<?php echo base_url('auth/logout'); ?>"><i class="bx bx-log-out"></i> <span>Logout</span></a>
    </div>
</div>
