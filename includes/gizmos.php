<?php
# -----------------------------------------------------------------
# Plugin Detectors
# -----------------------------------------------------------------

function dailyblank_alm_installed() {
	// return status for Ajax Load More Plugin
	if ( function_exists('alm_install' ) ) {
		return ('The Ajax Load More plugin <strong>is installed</strong> and will be used to sequentially load responses (with the value entered) if there are many of them. Check documentation tab for details on setting up the custom template in the plugin.'); 
		
	} else {
		return ('Ajax Load More plugin <strong>is not installed</strong>. This means all tweet responses will be loaded on a single Daily Blank and the number entered is ignored. If you start getting many responses, you may want to install this plugin. '); 
	}
}


# -----------------------------------------------------------------
# Spanners and gizmos- misc functions
# ----------------------------------------------------------------- 

function dailyblank_update_meta($id, $response_count) {
// update custom post meta to track the views and the number of responses done for each daily
// called on each view of a single daily

	// get current value for the visit count post meta, if it does not exist, start with 0
	$visit_count = ( get_post_meta($id, 'daily_visits', true) ) ? get_post_meta($id, 'daily_visits', true) : 0; 
	$visit_count++;
	
	//update visit counts
	update_post_meta($id,  'daily_visits', $visit_count);
	
	// now update the number of responses IF it is more than the current value
	if ( $response_count > get_post_meta($post->ID, 'response_count', 1) ) {
		update_post_meta($id,  'response_count', $response_count);
	}
	
}

/**
 * Get First Post Date Function
 *
 * @param  $format Type of date format to return, using PHP date standard, default Y-m-d
 * @return Date of first post
 * ------ h/t http://alex.leonard.ie/2010/07/27/wordpress-tip-get-the-date-of-your-first-post/
 * ------ but nearly every line was improved by me.
 */
function dailyblank_first_date( $format = 'F j, Y' ) {
 // Setup get_posts arguments

 $args = array(
	'numberposts' => 1,
	'post_status' => 'publish',
	'orderby' => 'date',
	'order' => 'ASC'
);

 // Get all posts in order of first to last
 $dailies = get_posts( $args );

 // return date in required format
 return ( date( $format, strtotime( $dailies[0]->post_date ) ) );

}

function dailyblank_first_response( $hashtag ) {
 // Setup get_posts arguments

 $args = array(
	'numberposts' => 1,
	'post_type' => 'response',
	'tax_query' => array(
		array(
			'taxonomy' => 'hashtags',
			'field'    => 'slug',
			'terms'    => $hashtag,
		),
	),	
	'post_status' => 'publish',
	'orderby' => 'date',
	'order' => 'ASC'
);

 // Get all posts in order of first to last
 $responses = get_posts( $args );

 // return date in required format
 return ( get_the_date( '',  $responses[0]->ID ) );

}



function dailyblank_mce_buttons( $buttons ) {	
	/**
	 * Add in a core button that's disabled by default
	 */
	$buttons[] = 'image';

	return $buttons;
}

add_filter( 'mce_buttons', 'dailyblank_mce_buttons' );

if ( false === function_exists( 'lcfirst' ) ) {
/*
 * Make a string's first character lowercase need for older PHP versions w/o function
 * hat tip Matt Croslin 
*/
 
    function lcfirst( $str ) {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}


function the_slug_exists( $slug ) {
// find out of post of a $post_type exists based on slug ($post_name)
//-- ht/t http://wordpress.stackexchange.com/a/144439/14945

	global $wpdb;
	
	$post_exists= $wpdb->get_var("SELECT count(post_title) FROM $wpdb->posts WHERE post_name like '" . $slug. "'");
    
    if ( $post_exists > 0)  {
        return true;
    } else {
        return false;
    }
}

/**
 * Changes an ISO 8601 formatted date from GMT/UTC (+00:00) to WordPress' set time or the timezone supplied.
 *
 * @param string $date_string ISO 8601 formatted datetime in GMT/UTC
 * @param string $format The format you want the date to be returned in, defaults to ISO 8601. See http://php.net/manual/en/function.date.php for formatting options
 * @param string|null $tzID The timezone identifier of the timezone to convert to, see http://php.net/manual/en/timezones.php, defaults to what is set in the WP admin
 * @return string Returns formatted date string in either WordPress' local time or the specified offset
 */
 
//-- h/t https://gist.github.com/dfwood90/6196705

function iso8601_gmt_to_local( $date_string, $format = 'c', $tzID = null ) {
    if ( null === $tzID ) {
        $tzID = get_option( 'timezone_string' );
    }
    if ( null === $tzID ) {
        $offset = ( get_option( 'gmt_offset' ) * -1 );
        $tzID = 'Etc/GMT' . ( ( 0 > $offset ) ? $offset : '+' . $offset ); // This line is here for compatibility reasons
    }
    $tz = new DateTimeZone( $tzID );
    $date = DateTime::createFromFormat( 'U', strtotime( $date_string ) );
    $date->setTimezone( $tz );
    return $date->format( $format );
}
?>