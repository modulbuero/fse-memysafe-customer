<?php 
/**
 * Title: User Menu
 * Slug: fse-memysafe-customer/user-menu
 * Inserter: no
 * Benutzt in user-menu.html
 * Author: Modulbüro
 */
?>

    <div id="menu">
        
        <!-- wp:list -->
        <ul class="wp-block-list">

        <?php if( get_user_meta(get_current_user_id(), 'first_settings', true)) : ?>
        <!-- wp:list-item -->
        <li id="memy-menu-dashboard"><i class="mmsi-icon dashboard"></i> <span>Dashboard</span></li>
        <!-- /wp:list-item -->
        <?php endif; ?>


        <?php if( get_user_meta(get_current_user_id(), 'first_settings', true) && current_user_can('administrator')) : ?>            
        <!-- wp:list-item -->
        <li id="memy-menu-einstellungen" data-goto="memy-settings" class="dash-goto-btn"><i class="mmsi-icon setting"></i> <span>Einstellungen</span></li>
        <!-- /wp:list-item -->

        <!-- wp:list-item -->
        <li id="memy-menu-abo" class="disabled"><i class="mmsi-icon abo"></i> Abo: That's me</li>
        <!-- /wp:list-item -->

        <!-- wp:list-item -->
        <li id="memy-menu-profile" data-goto="memy-profile" class="dash-goto-btn"><i class="mmsi-icon profil"></i> <span>Profil</span></li>
        <!-- /wp:list-item -->
        <?php endif; ?>
        
        <form method="post" action="/wp-login.php" id="logout-form">
            <input type="hidden" name="action" value="logout">
            <input type="hidden" name="redirect_to" value="/">
            <i class="mmsi-icon logout"></i> <input type="submit" class="icon-logout-btn" value="Abmelden">
        </form>

        </ul>
        <!-- /wp:list -->
    </div>
    

    