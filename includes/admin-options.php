<?php
# -----------------------------------------------------------------
# Options Panel for Admin
# -----------------------------------------------------------------

// -----  Add admin menu link for Theme Options
add_action( 'wp_before_admin_bar_render', 'dailyblank_options_to_admin' );

function dailyblank_options_to_admin() {
    global $wp_admin_bar;

    // we can add a submenu item too
    $wp_admin_bar->add_menu( array(
        'parent' => '',
        'id' => 'bank-options',
        'title' => __('Daily Blank Options'),
        'href' => admin_url( 'themes.php?page=dailyblank-options')
    ) );
}


function dailyblank_enqueue_options_scripts() {
	// Set up javascript for the theme options interface

	// media scripts needed for wordpress media uploaders
	wp_enqueue_media();

	// custom jquery for the options admin screen
	wp_register_script( 'dailyblank_options_js' , get_template_directory_uri() . '/js/jquery.options.js', array( 'jquery' ), '1.0', TRUE );
	wp_enqueue_script( 'dailyblank_options_js' );
}

function dailyblank_load_theme_options() {
	// load theme options Settings

	if ( file_exists( get_template_directory()  . '/class.dailyblank-theme-options.php' ) ) {
		include_once( get_template_directory()  . '/class.dailyblank-theme-options.php' );
	}

	// add a scheduler to check for tweets if not in standby mode
	// -- okay to use seed time in UTC as it is just an hourly trigger

	if ( ! wp_next_scheduled( 'dailyblank_hello_twitter' ) AND dailyblank_option('standby') == 'off' ) {
		 wp_schedule_event( time(), 'hourly', 'dailyblank_hello_twitter');
	}

	// add a schedule to check for low supply of dailies, set the time to be 2 hours after the scheduled time

	if ( ! wp_next_scheduled( 'dailyblank_low_supply' ) AND dailyblank_option('standby') == 'off' )  {
		 wp_schedule_event( strtotime( 'today+' . dailyblank_option('dailytime') ) +  3600*2, 'daily', 'dailyblank_low_supply');
	}
}
?>
