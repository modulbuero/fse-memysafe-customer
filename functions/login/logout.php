<?php
/**
 * WordPress Multisite Login Redirect
 * Leitet alle Login-Anfragen in einer Multisite zur zentralen Login-Seite der Hauptseite um
 * 
 * Füge diesen Code in die functions.php deines Themes oder als Plugin ein
 */

// Verhindert direkten Zugriff
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Redirect zu zentraler Login-Seite
 */
function multisite_central_login_redirect() {
    // Nur in Multisite-Umgebung aktiv
    if (!is_multisite()) {
        return;
    }
    
    // Hauptseite URL ermitteln
    $main_site_url = network_site_url();
    
    // Custom Login-Seite Slug (anpassen!)
    $custom_login_slug = 'login';
    
    // Zentrale Login-URL
    $central_login_url = $main_site_url . $custom_login_slug . '/';
    
    // Aktuelle Seite prüfen
    $current_site_id = get_current_blog_id();
    $main_site_id    = get_main_site_id();
    
    // Nur umleiten wenn nicht bereits auf der Hauptseite
    if ($current_site_id != $main_site_id) {
        
        // Standard WordPress Login-Seiten abfangen
        if (is_page('wp-login') || 
            (isset($_GET['action']) && $_GET['action'] == 'login') ||
            strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false ||
            strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false && !is_user_logged_in()) {
            
            // Redirect-URL mit Return-Parameter für Rückleitung nach Login
            $return_url = urlencode(get_site_url() . $_SERVER['REQUEST_URI']);
            $redirect_url = $central_login_url . '?return_to=' . $return_url;
            
            wp_redirect($redirect_url, 302);
            exit;
        }
    }
}

// Hook für frühe Ausführung
add_action('init', 'multisite_central_login_redirect', 1);

/**
 * Alternative: Redirect über wp-login.php Hook
 */
function redirect_login_page() {
    if (!is_multisite()) {
        return;
    }
    
    $main_site_url = network_site_url();
    $custom_login_slug = 'login'; // Anpassen!
    $current_site_id = get_current_blog_id();
    $main_site_id = get_main_site_id();
    
    if ($current_site_id != $main_site_id) {
        $return_url = urlencode(get_site_url());
        $redirect_url = $main_site_url . $custom_login_slug . '/?return_to=' . $return_url;
        
        wp_redirect($redirect_url, 302);
        exit;
    }
}

// Aktiviere einen der folgenden Hooks je nach Bedarf:
add_action('login_init', 'redirect_login_page');

/**
 * Login URL Filter für alle internen WordPress Links
 */
function filter_login_url($login_url, $redirect, $force_reauth) {
    if (!is_multisite()) {
        return $login_url;
    }
    
    $current_site_id = get_current_blog_id();
    $main_site_id = get_main_site_id();
    
    if ($current_site_id != $main_site_id) {
        $main_site_url = network_site_url();
        $custom_login_slug = 'login'; // Anpassen!
        
        $central_login_url = $main_site_url . $custom_login_slug . '/';
        
        if ($redirect) {
            $central_login_url .= '?return_to=' . urlencode($redirect);
        }
        
        return $central_login_url;
    }
    
    return $login_url;
}

#add_filter('login_url', 'filter_login_url', 10, 3);

/**
 * Logout URL anpassen (optional)
 */
function filter_logout_url($logout_url, $redirect) {
    if (!is_multisite()) {
        return $logout_url;
    }
    
    $current_site_id = get_current_blog_id();
    $main_site_id = get_main_site_id();
    
    if ($current_site_id != $main_site_id) {
        $main_site_url = network_site_url();
        $logout_url = $main_site_url . 'wp-login.php?action=logout';
        
        if ($redirect) {
            $logout_url .= '&redirect_to=' . urlencode($redirect);
        }
        
        return $logout_url;
    }
    
    return $logout_url;
}

add_filter('logout_url', 'filter_logout_url', 10, 2);

/**
 * Admin URL Redirect für nicht eingeloggte Benutzer
 */
function redirect_admin_access() {
    if (!is_multisite() || is_user_logged_in()) {
        return;
    }
    
    if (is_admin() && !wp_doing_ajax()) {
        $current_site_id = get_current_blog_id();
        $main_site_id = get_main_site_id();
        
        if ($current_site_id != $main_site_id) {
            $main_site_url = network_site_url();
            $custom_login_slug = 'login'; // Anpassen!
            $return_url = urlencode(admin_url());
            
            $redirect_url = $main_site_url . $custom_login_slug . '/?return_to=' . $return_url;
            
            wp_redirect($redirect_url, 302);
            exit;
        }
    }
}

add_action('admin_init', 'redirect_admin_access');

/**
 * Hilfsfunktion: Nach Login zurück zur ursprünglichen Seite
 * Füge dies zu deiner Custom Login-Seite hinzu
 */
function handle_return_redirect() {
    if (is_user_logged_in() && isset($_GET['return_to'])) {
        $return_url = urldecode($_GET['return_to']);
        
        // Sicherheitsprüfung
        if (wp_validate_redirect($return_url)) {
            wp_redirect($return_url);
            exit;
        }
    }
}

// Aktiviere Return-Redirect auf der Login-Seite
// add_action('wp_loaded', 'handle_return_redirect');

/**
 * Konfiguration
 * ============
 * 
 * 1. Ändere $custom_login_slug zu deinem tatsächlichen Login-Seiten-Slug
 * 2. Erstelle eine Custom Login-Seite auf deiner Hauptseite
 * 3. Aktiviere die gewünschten Hooks je nach Anforderung
 * 4. Teste alle Login/Logout-Flows
 * 
 * Verwendung:
 * - Speichere als Plugin oder füge in functions.php ein
 * - Alle Subsites leiten zur zentralen Login-Seite um
 * - Nach Login erfolgt Rückleitung zur ursprünglichen Seite
 */