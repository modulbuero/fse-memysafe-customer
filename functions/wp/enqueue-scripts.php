<?php 
/**
 * 	Collecttion of Style-/CSS-Files
 */
function mbfse_child_style_Files(){
	$f_ass = '/assets/';
	$f_CSS = $f_ass.'scss/';
	$f_JS  = $f_ass.'js/';

	wp_enqueue_style(
		'memy-child-main',
		get_stylesheet_directory_uri().'/style.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	/*CSS*/
	wp_enqueue_style(
		'memy-variables',
		get_stylesheet_directory_uri().$f_CSS.'variables.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-struktur',
		get_stylesheet_directory_uri().$f_CSS.'struktur.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-fonts',
		get_stylesheet_directory_uri().$f_CSS.'fonts.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-formulare',
		get_stylesheet_directory_uri().$f_CSS.'form.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-elemente',
		get_stylesheet_directory_uri().$f_CSS.'elemente.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-customer',
		get_stylesheet_directory_uri().$f_CSS.'dashboard.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Icons
	wp_enqueue_style(
		'memy-icons',
		get_stylesheet_directory_uri().$f_CSS.'icons.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-exmam-clock',
		get_stylesheet_directory_uri().$f_CSS.'exam-clock.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	$memysafeFrontpage = content_url() . '/themes/fse-memysafe-frontpage/assets/scss/form.css';
    wp_enqueue_style('memysafe-frontpage-formular', $memysafeFrontpage, array(), '1.0.0');

	
	wp_enqueue_style(
		'memy-footer',
		get_stylesheet_directory_uri().$f_CSS.'footer.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'memy-child-projects',
		get_stylesheet_directory_uri().$f_CSS.'projects.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Safe Upload Styles
	wp_enqueue_style(
		'memy-safe-upload',
		get_stylesheet_directory_uri().$f_CSS.'safe.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// First Settings
	wp_enqueue_style(
		'memy-first-steps',
		get_stylesheet_directory_uri().$f_CSS.'first-steps.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	/*JS*/
	wp_enqueue_script(
		'memy-child-menu',
		get_stylesheet_directory_uri().$f_JS.'user-menu.js',
		array('jquery', 'mbfse-main'),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'memy-child-dashnavigation',
		get_stylesheet_directory_uri().$f_JS.'dash-navigation.js',
		array('jquery', 'mbfse-main'),
		wp_get_theme()->get( 'Version' )
	);

	 wp_enqueue_script(
		'message-handler',
		get_stylesheet_directory_uri() . $f_JS.'message-handler.js',
		array('jquery'),
		wp_get_theme()->get( 'Version' )
	);

}
add_action('wp_enqueue_scripts', 'mbfse_child_style_Files', 999);

function mytheme_add_favicon() {
    ?>
    <link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/imgs/MMSI_Favicon_32px.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/imgs/MMSI_Favicon_256px.png">
    <?php
}
add_action('wp_head', 'mytheme_add_favicon');