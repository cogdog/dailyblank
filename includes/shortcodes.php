<?php

# -----------------------------------------------------------------
# Shortcodes
# -----------------------------------------------------------------

// ----- short code for number of published Daily Blanks (just count posts)
add_shortcode('dailycount', 'getdailyCount');

function getdailyCount( $since = 0 ) {

	if ( $since ) {
		// to find number of posts since a date, we need a query.
		
		$args = array(
			'date_query' => array(
				array(
					'after'    => array(
						'year'  => date("Y", $since) ,
						'month' => date("n", $since),
						'day'   => date("j", $since),
					),
					'inclusive' => true,
				),
			),
			'posts_per_page' => -1,
		);
		
		$dailies = new WP_Query( $args );
		
		return $dailies->found_posts;
	
	} else {
	
		// total count is easy, eh?
		return wp_count_posts('post')->publish;
		
	}
}

function getDraftCount() {
	return wp_count_posts('post')->draft;
}

function getScheduledCount() {
	return wp_count_posts('post')->future;
}



// ----- short code for number of responses in the site
add_shortcode('responsecount', 'getResponseCount');

// ----- short code for number of people who have responded
add_shortcode('peoplecount', 'getPeopleCount');

function getPeopleCount() {

	$args = array(
		'number' => $number,
		'name__like' => '@'
	);
	
	$terms = get_terms('hashtags',  $args );

	return count($terms);
}


/* ----- shortcode to generate lists of top contributors -------- */
add_shortcode("dailyleaders", "dailyblank_leaders");  

function dailyblank_leaders ( $atts ) {  

	// return a list of the top responders to dailies
		
	// get the value of any passed attributes to our function
	// we want a number of results we should return (0=all)
	// and an indicator if we are looking for responders (hashtag taxonony) or contributors (tag tax)
	// Allow for exclusion based on ID of the hashtag taxonony
 	extract( shortcode_atts( array( "number" => 0,  "type" => 'responders' , "exclude" => "", "showbars" => 0, "barstyle" => 3, "since" => '' ), $atts ) );  

	// the class for the bars, must be 1, 2, or 3
	$barstyle = ($barstyle > 3 or $barstyle < 1) ? 3 : $barstyle;


	if ( empty( $since) ) {
	
		// no date cut off, so use wp functions for total counts on terms
		
		// temp filter to use name as secondary sort (first by tag count, then by name)
		add_filter( 'terms_clauses', 'dailyblank_second_orderby', 10, 3 );

		// Arguments to search hashtag terms
		// search for @ in order of highest frequency
		$args = array(
			'number' => $number,
			'exclude' =>  explode(",", $exclude),
			'name__like' => '@'
		);
		
		$dailycount = getdailyCount();
	
		if ( $type == 'contributors') {
			// search for terms in the custom taxonomy for regular tags
			$terms = get_tags( $args );
			$taxpath = 'tag';
		} else {
			// search for terms in the custom taxonomy for response tags
			$terms = get_terms('hashtags',  $args );
			$taxpath = 'hashtags';
		}
	

	
		// clean up after ourselves
		remove_filter( 'terms_clauses', 'dailyblank_second_orderby', 10, 3 );
	
	
		// here come the leaders!
	
		if ( $showbars) {
		
			if ( $number ) {
				$out = '<p>Here are the top <strong>' .  $number . '</strong> all time responders to the ';
				
			} else {
				$out = '<p>So far <strong>' . count($terms) . '</strong> people have responded to ';
			}

			$out .= '<strong>' . $dailycount . '</strong> dailies since <strong>' . dailyblank_first_date() . '</strong>.</p> <ul class="leader-list">';
			
			foreach ( $terms as $term) { 
			
				$mycount = min( $term->count, $dailycount );
				
				$percent = intval( $term->count / $dailycount * 100); 
			
				 $out .= '<li class="leader"><h3><a href="' . site_url() . "/$taxpath/" . $term->slug  . '">' . $term->name . ' (' . $mycount . ')</a></h3><progress class="leader-' . $barstyle . '" max="100" value="' . $percent . '"><strong>Completion Level: ' . $percent . '%</strong></progress></li>';
			}
			$out .= '</ul>';
	
		} else {
			// no progress bars
			$out = '<ol>';
			foreach ( $terms as $term) { 
			
				$mycount = min( $term->count, $dailycount );
				
				$out .= '<li><a href="' . site_url() . "/$taxpath/" . $term->slug  . '">' . $term->name . ' (' . $mycount . ')</a></li>';
			}
			$out .= '</ol>';
		}
		
	} else {
	
		// we need to do a custom query to get counts since a certain date
	
		global $wpdb;
		
		
		if ( $number ) {
			$limitparam = "LIMIT $number";
			$limitstr = " Here are the top $number responders.";
		}
		
		// convert datestring to time
		
		if ($since == 'year') {
			$timestamp = strtotime('first day of january this year');
		} else {
			$timestamp = strtotime($since);
		}
		
		// number of dailies since date
		$dailycount = getdailyCount( $timestamp );
		
		// convert time to SQL date; if it is invalid we useall
		
		$since_str = ( $timestamp ) ?  "AND p.post_date > '" . date('Y-m-d', $timestamp) . "'" : '';
		
		if ( $type == 'contributors') {
			// search for terms in the custom taxonomy for regular tags
			$taxpath = 'tag';
			$ptype = 'post';
		} else {
			// search for terms in the custom taxonomy for response tags
			$taxpath = 'hashtags';
			$ptype = 'response';
		}
		
		
		$exclude_cond  = '';
		
		if ($exclude != '' ) {
			
			$exclude_array = explode(",", $exclude);

			foreach ($exclude_array as $exid) {
				$exclude_cond  .=  " and t.term_id !='" . $exid . "' ";
			}
		}
			 
		
		$leaderstuff = $wpdb->get_results( 
			"
			SELECT t.name, t.slug, count(*) as cnt 
			FROM $wpdb->posts p 
			JOIN $wpdb->term_relationships r 
				ON p.id=r.object_id 
			JOIN $wpdb->terms t 
				ON r.term_taxonomy_id=t.term_id 
			WHERE p.post_status='publish' and p.post_type='$ptype' and t.name LIKE '@%%' $since_str  $exclude_cond
			GROUP by t.name 
			ORDER by cnt DESC
			$limitparam
			"
		);
		
		if ( $leaderstuff ) {
		
				// here come the leaders!
	
				if ( $showbars ) {
				
					if ( $number ) {
						$out = '<p>Here are the top <strong>' .  $number . '</strong> responders to the ';
					} else {
						$out = '<p>So far <strong>' . $wpdb->num_rows . '</strong> people have responded to ';
					}
					$out .= '<strong>' . $dailycount . '</strong> dailies since <strong>' . date('F j, Y' , $timestamp) . '</strong>.</p> <ul class="leader-list">';
					
					foreach ( $leaderstuff as $leader) { 
					
					
						$percent = intval( $leader->cnt / $dailycount * 100); 
						
						$mycount = min( $leader->cnt, $dailycount );
			
						 $out .= '<li class="leader"><h3><a href="' . site_url() . "/$taxpath/" . $leader->slug  . '">' . $leader->name . ' (' . $mycount . ')</a></h3><progress class="leader-' . $barstyle . '" max="100" value="' . $percent . '"><strong>Completion Level: ' . $percent . '%</strong></progress></li>';
					}
					$out .= '</ul>';
	
				} else {
					// no progress bars
					$out = '<ol>';
					foreach ( $leaderstuff as $leader) { 
					
						$mycount = min( $leader->cnt, $dailycount );
						
						$out .= '<li><a href="' . site_url() . "/$taxpath/" . $leader->slug  . '">' . $leader->name . ' (' . $mycount . ')</a></li>';
					}
					$out .= '</ol>';
				}		
		}
	}
	
	// here ya go!
	return ($out);

}


// adds name as secondary order by term for get terms 
// h/t https://wordpress.org/support/topic/get_terms-multiple-order_by-options?replies=2#post-7396104
//   & https://wordpress.org/support/topic/get_terms-multiple-order_by-options?replies=5#post-7401630

function dailyblank_second_orderby( $pieces, $taxonomies, $args ) {
	$pieces['orderby'] = 'ORDER BY tt.count DESC,t.name';
	return $pieces;
}

// ----- short code to list the top dailies according to numbers of views and number of response
//       run  a get_posts based in

add_shortcode('dailytops', 'get_daily_tops');

function get_daily_tops( $atts ) {

		extract( shortcode_atts( array( "number" => 10,  "type" => 'responses' , "showcount" => 1, "showdate" => 1), $atts ) ); 
	
		// set metakey to look for known info
		$metakey = (  $type == 'responses' ) ? 'response_count' : 'daily_visits';
		
		// start empty
		$str = '<ol>';
		
		// set up arguments to get posts
		$args = array(
			'posts_per_page'   => $number,
			'post_type'        => 'post',
			'post_status'      => 'publish',
			'meta_key'		   =>  $metakey,
			'orderby'		   => 'meta_value_num',
		);

		$myposts = get_posts( $args );
		
		foreach ( $myposts as $daily ) {
			setup_postdata( $daily );
			
			$str .= '<li><a href="' . get_permalink($daily->ID) . '">' . 
$daily->post_title . '</a> ';
			if ( $showcount ) $str .=   ' ('. get_post_meta($daily->ID, $metakey, 1) . ') ';
			if ( $showdate ) $str .= get_the_date( get_option( 'date_format' ), $daily->ID);
			$str .=  '</li>';				
		}
		
		$str .= '</ol>';
		
		return ( $str );	 
}

?>