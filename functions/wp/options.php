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