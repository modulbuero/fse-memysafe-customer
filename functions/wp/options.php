<?php 

add_filter( 'theme_file_path', function( $path, $theme ) {
    return $path;
}, 10, 2 );

//Entwicklung
add_action( 'wp_loaded', function() {
    wp_cache_flush();
});