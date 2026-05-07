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