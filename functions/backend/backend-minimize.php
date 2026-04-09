<?php 
/**
 * Entfernt Menüpunkte, die nicht benötigt werden
 */
function remove_menu_items_for_custom_author() {
    $current_user = wp_get_current_user();
    
    if (in_array('custom_author', $current_user->roles)) {
        // Entfernt verschiedene Admin-Menüpunkte
        remove_menu_page('edit-comments.php');     // Kommentare
        remove_menu_page('themes.php');            // Design
        remove_menu_page('plugins.php');           // Plugins
        remove_menu_page('users.php');             // Benutzer
        remove_menu_page('tools.php');             // Werkzeuge
        remove_menu_page('options-general.php');   // Einstellungen
        remove_menu_page('edit.php?post_type=page'); // Seiten
        
        // Entfernt Untermenü-Punkte von Beiträgen
        remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
        remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    }
}
add_action('admin_menu', 'remove_menu_items_for_custom_author');


/**
 * Versteckt die Admin-Bar-Elemente, die nicht benötigt werden
 */
function hide_admin_bar_items($wp_admin_bar) {
    $current_user = wp_get_current_user();
    
    if (in_array('custom_author', $current_user->roles)) {
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('new-page');
        $wp_admin_bar->remove_node('themes');
        $wp_admin_bar->remove_node('widgets');
        $wp_admin_bar->remove_node('customize');
    }
}
add_action('admin_bar_menu', 'hide_admin_bar_items', 999);