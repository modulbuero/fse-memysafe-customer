<?php 

// add_filter( 'theme_file_path', function( $path, $theme ) {
//     return $path;
// }, 10, 2 );

// //Entwicklung
// add_action( 'wp_loaded', function() {
//     wp_cache_flush();
// });


add_filter('pre_get_document_title', function ($title) {

    $title = 'Me My Safe and I';

    $query = new WP_User_Query([
        'number'  => 1,
        'orderby' => 'registered',
        'order'   => 'ASC',
    ]);

    $users = $query->get_results();

    if (!empty($users)) {
        $adminuser  = $users[0];
        $title     = $adminuser->first_name . ' ' . $adminuser->last_name;
    }

    return $title;
});

/**
 * Fügt die 'darkmode'-Klasse zur Body-Klasse hinzu, wenn der Benutzer-Meta 'mmsi_darkmode' auf '1' gesetzt ist.
 */
add_filter('body_class', function ($classes) {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $darkmode_setting = get_user_meta($user_id, 'mmsi_darkmode', true);
        if ($darkmode_setting === '1') {
            $classes[] = 'darkmode';
        }
    }
    return $classes;
});

/**
 *  Kommentare deaktivieren
 */ 
add_action('init', function () {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);
    add_filter('comments_array', '__return_empty_array', 10, 2);

});


/**
 * Diskussionseinstellungen sperren
 */
add_action('admin_init', function () {
    global $pagenow;
    if (
        is_admin()
        && !is_network_admin()
        && isset($pagenow)
        && $pagenow === 'options-discussion.php'
    ) {
        wp_safe_redirect(admin_url());
        exit;
    }
});


/**
 * Kommentar-Menüs entfernen
 */
add_action('admin_menu', function () {
    if (!is_network_admin()) {
        remove_menu_page('edit-comments.php');
        remove_submenu_page(
            'options-general.php',
            'options-discussion.php'
        );
    }
}, 999);


/**
 *  Nachricht "Email geändert"
 */
add_filter( 'email_change_email', 'mmsi_email_change_email', 10, 3 );
function mmsi_email_change_email( $email_change_email, $user, $userdata ) {

    // ── Betroffener Benutzer (dessen E-Mail geändert wurde) ──────────────
    $affected_user_id    = $user['ID'];
    $affected_userdata   = get_userdata( $affected_user_id );
    $affected_first_name = $affected_userdata->first_name;
    $old_email           = $user['user_email'];
    $new_email           = $userdata['user_email'];

    // ── Änderer (aktuell eingeloggter Benutzer) ──────────────────────────
    $editor = wp_get_current_user();
    $editor_first_name   = $editor->first_name; // Vorname Änderer

    // ── Seiteninfos ──────────────────────────────────────────────────────
    $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

    // ── E-Mail zusammenbauen ─────────────────────────────────────────────
    $email_change_email['subject'] = '[MMSI] Deine E-Mail-Adresse wurde geändert';
    $email_change_email['message'] =
        'Hallo ' . $affected_first_name . ',' . "\r\n\r\n" .
        'deine E-Mail-Adresse auf MMSI wurde geändert.' . "\r\n\r\n" .
        'Alte Adresse  : ' . $old_email . "\r\n" .
        'Neue Adresse : ' . $new_email . "\r\n\r\n" .
        'Geändert von : ' . $site_name . "\r\n\r\n" .
        'Falls dies ein Fehler war, wende dich bitte sofort an '.$editor_first_name . "\r\n\r\n".
        'Dein Team von Me, My Safe and I';

    return $email_change_email;
}