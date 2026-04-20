<?php
/**
 * WordPress Multisite Login-Schutz
 * Diese Funktion leitet alle nicht eingeloggten Benutzer zur Login-Seite weiter
 */
// Funktion zur Überprüfung des Login-Status und Weiterleitung
function multisite_force_login() {
    $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $login_url = wp_login_url($redirect_url);
    
    // Fremde
    if (!is_user_logged_in()) {

        $current_site_id = get_current_blog_id();
        
        // Ausnahmen definieren - Seiten die öffentlich bleiben sollen
        $public_pages = array(
            'wp-admin/admin-ajax.php',
        );
        
        // Aktuelle Seite ermitteln
        $current_page = basename($_SERVER['REQUEST_URI']);
        $script_name = basename($_SERVER['SCRIPT_NAME']);
        
        // Prüfen ob wir uns bereits auf einer Login-Seite befinden
        if (in_array($script_name, $public_pages) || 
            in_array($current_page, $public_pages) ||
            strpos($_SERVER['REQUEST_URI'], 'wp-login') !== false ||
            strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false ||
            isset($_GET['accept_invitation'])) {
            return; // Keine Weiterleitung bei Login-Seiten oder Einladungsannahme
        }
        
        // Weiterleitung durchführen
        wp_redirect($login_url);
        exit;
    }else{
        // Bekannte
        
        // Aktuelle Site-ID abrufen
        $current_site_id = get_current_blog_id();
        #$current_user   = get_current_user_id();

        // Site-Eigentümer abrufen
        $site_owner_id = get_network_option($current_site_id, 'admin_user_id');
        if (!$site_owner_id) {
            // Fallback: Ersten Admin der Site finden
            $admins = get_users(array(
                'blog_id' => $current_site_id,
                'role'    => 'administrator',
                'number'  => 1,
                'fields'  => 'ID'
            ));
            $site_owner_id = !empty($admins) ? $admins[0] : false;
        }
        
        // Aktuellen Benutzer abrufen
        $current_user_id = get_current_user_id();
        $current_user = wp_get_current_user();

        // Subscriber dürfen helpermodus aufrufen
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/helpermodus') !== false && in_array('subscriber', (array) $current_user->roles, true)) {
            return;
        }

        // Prüfen ob Benutzer eingeloggt und Eigentümer ist
        $is_owner = ($current_user_id && $current_user_id == $site_owner_id);
        
        // Super-Admins haben immer Zugriff
        $is_super_admin = is_super_admin($current_user_id);
        
        // Zusätzliche Ausnahmen definieren (optional)
        $allowed_paths = array(
            '/wp-json/',
        );
        
        $current_path = $_SERVER['REQUEST_URI'];
        $is_allowed_path = false;
        foreach ($allowed_paths as $path) {
            if (strpos($current_path, $path) !== false) {
                $is_allowed_path = true;
                break;
            }
        }
        
        // Wenn nicht Eigentümer, nicht Super-Admin und nicht auf erlaubtem Pfad
        if (!$is_owner && !$is_super_admin && !$is_allowed_path) {
            // Optional: Log für Debugging
            error_log("Frontend access denied for user ID: $current_user_id on site ID: $current_site_id");
            
            // Weiterleitung mit 302 Status
            wp_redirect($login_url, 302);
            exit;
        }
    }
}

// Hook für alle Frontend-Requests
add_action('template_redirect', 'multisite_force_login');

/**
 * Alternative Implementierung mit init Hook (falls template_redirect nicht funktioniert)
 * Kommentiere die obige Zeile aus und verwende diese stattdessen:
 */
// add_action('init', 'multisite_force_login');

/**
 * Erweiterte Version mit zusätzlichen Optionen
 */
function multisite_force_login_advanced() {
    // Nur im Frontend anwenden, nicht im Admin-Bereich
    if (is_admin()) {
        return;
    }
    
    // Prüfen ob Benutzer eingeloggt ist
    if (!is_user_logged_in()) {
        
        // Erweiterte Ausnahmen
        $public_endpoints = array(
            'wp-login.php',
            'wp-register.php',
            'wp-admin/admin-ajax.php',
            'xmlrpc.php',
            'wp-cron.php',
            'wp-json', // REST API
            'feed',
            'rdf',
            'rss',
            'rss2',
            'atom'
        );
        
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Prüfen auf Ausnahmen
        foreach ($public_endpoints as $endpoint) {
            if (strpos($request_uri, $endpoint) !== false) {
                return;
            }
        }
        
        // Spezielle WordPress-Requests ausschließen
        if (defined('DOING_CRON') && DOING_CRON) return;
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) return;
        
        // Aktuelle URL für Redirect speichern
        $current_url = home_url(add_query_arg(null, null));
        
        // Zur Login-Seite weiterleiten
        auth_redirect();
    }
}

// Verwende die erweiterte Version (kommentiere aus falls gewünscht)
// add_action('template_redirect', 'multisite_force_login_advanced');

/**
 * Netzwerkweite Aktivierung für alle Sites in der Multisite
 * Diese Funktion muss in wp-config.php oder in einem Must-Use Plugin verwendet werden
 */
function activate_login_protection_networkwide() {
    if (is_multisite()) {
        // Für alle Sites im Netzwerk aktivieren
        $sites = get_sites();
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);
            
            // Login-Schutz für diese Site aktivieren
            add_action('template_redirect', 'multisite_force_login');
            
            restore_current_blog();
        }
    } else {
        // Für Single-Site Installation
        add_action('template_redirect', 'multisite_force_login');
    }
}

// Netzwerkweite Aktivierung
add_action('plugins_loaded', 'activate_login_protection_networkwide');

/**
 * Optional: Custom Login-Seite für bessere UX
 */
function custom_login_redirect($redirect_to, $request, $user) {
    // Nach erfolgreichem Login zur ursprünglich angeforderten Seite weiterleiten
    if (isset($request) && !empty($request)) {
        return $request;
    }
    
    // Fallback zur Startseite
    return home_url();
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);

?>