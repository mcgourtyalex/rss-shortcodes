<?php
function responsive_child_widgets_init() {

        register_sidebar(array(
            'name' => __('Main', 'responsive'),
            'description' => __('Area One - sidebar-home-widgets-main.php', 'responsive'),
            'id' => 'main',
            'before_title' => '<div id="widget-title-one" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));

        register_sidebar(array(
            'name' => __('Technical Resources', 'responsive'),
            'description' => __('Area Nine - sidebar-home-widgets-tech.php', 'responsive'),
            'id' => 'technical-resources',
            'before_title' => '<div id="widget-title-one" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));

        register_sidebar(array(
            'name' => __('Useful Links', 'responsive'),
            'description' => __('Area Ten - sidebar-home-widgets-links.php', 'responsive'),
            'id' => 'useful-links',
            'before_title' => '<div id="widget-title-one" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));

		register_sidebar(array(
            'name' => __('RSS Feed', 'responsive'),
            'description' => __('Area Eleven - sidebar-home-widgets-rss-feeds.php', 'responsive'),
            'id' => 'rss-feeds',
            'before_title' => '<div id="widget-title-one" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));
		
        register_sidebar(array(
            'name' => __('News Content 1', 'responsive'),
            'description' => __('Area Twelve - sidebar-home-widgets-news.php', 'responsive'),
            'id' => 'news-content-1',
            'before_title' => '<div id="widget-title-one" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));

        register_sidebar(array(
            'name' => __('News Content 2', 'responsive'),
            'description' => __('Area Thirteen - sidebar-home-widgets-news.php', 'responsive'),
            'id' => 'news-content-2',
            'before_title' => '<div id="widget-title-two" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));

        register_sidebar(array(
            'name' => __('News Content 3', 'responsive'),
            'description' => __('Area Fourteen - sidebar-home-widgets-news.php', 'responsive'),
            'id' => 'news-content-3',
            'before_title' => '<div id="widget-title-two" class="widget-title-home"><h3>',
            'after_title' => '</h3></div>',
            'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
            'after_widget' => '</div>'
        ));

}
add_action( 'widgets_init', 'responsive_child_widgets_init' );

// add_action('init', 'ehp_responsive_scripts');
// function ehp_responsive_scripts() {
//     if (is_admin()) return;
//     wp_register_script('ehp-responsive-script', get_stylesheet_directory_uri() . '/script.js');
//     wp_enqueue_script('ehp-responsive-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'));
// }

function ehp_responsive_scripts() {
	wp_register_script('ehp-responsive-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'));
	wp_enqueue_script('ehp-responsive-script');
}
add_action('wp_enqueue_scripts', 'ehp_responsive_scripts');

if (function_exists('yoast_analytics')) :
function ehp_responsive_analytics_scripts() {
	wp_register_script('ehp-responsive-analytics-script', yoast_analytics(), array('jquery'));
	wp_enqueue_script('ehp-responsive-analytics-script');
}
add_action('wp_enqueue_scripts', 'ehp_responsive_analytics_scripts');
endif;

if (!function_exists('ehp_responsive_header_menu')) :
function ehp_responsive_header_menu() {
	/**
	 * Sets the Header Menu to the network menu on theme activation.
	 */
	global $ehp_menu_name;
	$locations = get_nav_menu_locations();

	// // Return if Header Menu is already set to a valid menu
	// if (has_nav_menu("header-menu") && wp_get_nav_menu_items($locations["header-menu"]))
	//	return;

	$ehp_header_menu = wp_get_nav_menu_object($ehp_menu_name);

	if (!$ehp_header_menu) return;

	$ehp_header_menu_id = $ehp_header_menu->term_id;
	$locations["header-menu"] = $ehp_header_menu_id;
	set_theme_mod("nav_menu_locations", $locations);
}
add_action('after_switch_theme', 'ehp_responsive_header_menu', 11);
endif;

if (!function_exists('ehp_responsive_top_menu')) :
function ehp_responsive_top_menu() {
	/**
	 * Creates the Top Menu on theme activation.
	 */
	$locations = get_nav_menu_locations();

	// Return if Top Menu is already set to a valid menu.
	if (has_nav_menu("top-menu") && wp_get_nav_menu_items($locations["top-menu"]))
		return;

	$ehp_top_menu = wp_get_nav_menu_object("Top");
	if ($ehp_top_menu) {
		$ehp_top_menu_id = $ehp_top_menu->term_id;
	} else {
		// Create the menu if it doesn't exist already
		$ehp_top_menu_id = wp_create_nav_menu("Top");
		wp_update_nav_menu_item($ehp_top_menu_id, 0, array(
			"menu-item-title"	=> __("Feedback"),
			"menu-item-url"		=> "mailto:engineers.home.page@autodesk.com?subject=SiteFeedback",
			"menu-item-status"	=> "publish"
		));
		wp_update_nav_menu_item($ehp_top_menu_id, 0, array(
			"menu-item-title"	=> __("Engineering Helpdesk"),
			"menu-item-url"		=>	"https://share.autodesk.com/sites/bre/Pages/BREActionRequest.aspx",
			"menu-item-status"	=> "publish"
		));
	}

	$locations["top-menu"] = $ehp_top_menu_id;
	set_theme_mod("nav_menu_locations", $locations);
}
add_action('after_switch_theme', 'ehp_responsive_top_menu', 11);
endif;

if (!function_exists('ehp_default_logo')) :
function ehp_default_logo() {
	// Removes the logo on theme activation
	set_theme_mod("header_image", "remove-header");
}
add_action('after_switch_theme', 'ehp_default_logo');
endif;

/*
function html5_video($atts, $content = null) {
	extract(shortcode_atts(array(
		"src" => '',
		"width" => '',
		"height" => ''
	), $atts));
	return '<video src="'.$src.'" width="'.$width.'" height="'.$height.'" controls autobuffer>';
}
add_shortcode('video5', 'html5_video');
*/

add_filter( 'allow_dev_auto_core_updates', '__return_false' );

//Enable Automatic plugins updates
//add_filter( 'auto_update_plugin', '__return_true' );

//Enable Automatic theme updates
//add_filter( 'auto_update_theme', '__return_true' );

function custom_clean_head() {
	remove_action('wp_head', 'wp_print_scripts');
	remove_action('wp_head', 'wp_print_head_scripts', 9);
	remove_action('wp_head', 'wp_enqueue_scripts', 1);
}
add_action( 'wp_enqueue_scripts', 'custom_clean_head' );


function print_menu_shortcode($atts, $content = null) {
    extract(shortcode_atts(array( 'name' => null, ), $atts));
    return wp_nav_menu( array( 'menu' => $name, 'menu_class' => 'main-menu2', 'echo' => false ) );
}
add_shortcode('menu', 'print_menu_shortcode');

?>