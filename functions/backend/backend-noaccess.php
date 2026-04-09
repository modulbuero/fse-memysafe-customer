<?php
// WordPress Backend für alle außer Super-Admins sperren
// Füge diesen Code in die functions.php deines aktiven Themes ein

// Backend-Zugang für Nicht-Super-Admins blockieren
function restrict_admin_access() {
    // Prüfe ob wir im Admin-Bereich sind
    if (is_admin() && !wp_doing_ajax()) {
        if (!is_super_admin()) {
            // Weiterleitung zur Startseite
            wp_redirect(home_url());
            exit();
        }
    }
}
add_action('admin_init', 'restrict_admin_access');

// Admin-Bar für alle außer Super-Admins entfernen
function hide_admin_bar_for_non_super_admins() {
    if (!is_super_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_non_super_admins');


// Zusätzlich: Admin-Bar CSS entfernen wenn sie versteckt ist
function remove_admin_bar_css() {
    if (!is_super_admin()) {
        remove_action('wp_head', '_admin_bar_bump_cb');
    }
}
add_action('get_header', 'remove_admin_bar_css');

// Optional: Dashboard-Widgets für Nicht-Super-Admins entfernen
function remove_dashboard_widgets_for_non_super_admins() {
    if (!is_super_admin()) {
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_secondary', 'dashboard', 'side');
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    }
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets_for_non_super_admins');
?>