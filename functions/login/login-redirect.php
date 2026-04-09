<?php
// Login-Seite unter /einloggen anzeigen
add_action('init', function () {
    // Der gewünschte Slug
    $custom_login_slug = 'einloggen';

    // Erlaubt nur exakte URL, keine Subpages
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request_uri = trim($request_uri, '/');

    // Nur wenn exakter Slug, dann wp-login.php laden
    if ($request_uri === $custom_login_slug) {
        define('WP_USE_THEMES', false); // verhindert Theme-Header-Laden
        define('DONOTCACHEPAGE', true); // verhindert Caching
        require_once ABSPATH . 'wp-login.php';
        exit;
    }
});