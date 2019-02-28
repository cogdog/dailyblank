<?php
# -----------------------------------------------------------------
# Editing Meta Box - The thing that makes the daily magic
# -----------------------------------------------------------------

// Adds the interface to generate new Dailies with proper tags 'n stuff

// hook to add the box to admin interface
add_action( 'add_meta_boxes', 'dailyblank_meta_box_add' );  

function dailyblank_get_next_tag()
// get the next tag available from the database, these are in the form of [base tag name from options]123 e.g. dailypuzzle123
{
	global $wpdb;
		
	// query to get the last tag used in the database
		
	$tag_query = "SELECT $wpdb->terms.name AS tag_name
	FROM $wpdb->terms, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->posts
	WHERE $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id	
		AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
		AND $wpdb->term_relationships.object_id = $wpdb->posts.ID  
		AND $wpdb->term_taxonomy.taxonomy = 'post_tag' 
		AND $wpdb->terms.name LIKE '" . dailyblank_option('basetag') . "%%' 
		AND ($wpdb->posts.post_status='publish' or $wpdb->posts.post_status='future') 
		AND $wpdb->posts.post_type ='post'
	ORDER BY $wpdb->posts.post_date DESC
	LIMIT 1";
			
	$tags = $wpdb->get_results($tag_query); 
		
	// we loop although its only one time!
	if ($tags) {
		
		foreach ($tags as $checktag) {
			$last_tag = substr( $checktag->tag_name, strlen( dailyblank_option('basetag') ));
		}
		
		$last_tag++; // increment tag number
		
		// join the number with the alpha base
		
		return( dailyblank_option('basetag') . $last_tag);
	} else {
		// first time through we make a tag from the base and the defined startnumber
		return( dailyblank_option('basetag') . dailyblank_option('startnum') );
	} 
}

function dailyblank_get_last_date() {
	// Used to get the date of the last scheduled daily blank
	
	// we will query to get all future dated posts
	$args = array( 'numberposts' => 1, 'post_status'=> 'future' );
	$lastposts = get_posts( $args );
		
	if ( count($lastposts) ) {
		// simple loop to get the post_date for the last scheduled post
		// and return that date
		foreach( $lastposts as $post ) {
			return  ( $post->post_date );
		}
		
	} else {
	
		//  no scheduled posts, look for most recent published post that has a post meta used by daily blanks
		$args = array( 'numberposts' => 1, 'post_status'=> 'publish', 'meta_key' => 'dailyblank_tag' );
		$lastposts = get_posts( $args );
		
		
		if ( count( $lastposts ) ) {
			foreach( $lastposts as $post ) {
				setup_postdata( $post );
				return ( $post->post_date );
			}
			
		} else {
		
			// return today's date at the designated time of day (stored as option like "05:00" for 5am)
			return ( strtotime('today+' . dailyblank_option('dailytime')  ) );
		}
		
	}
}
	
	
function dailyblank_meta_box_add()  
// meta box insertion to right side of post editor, at top
{ 
	add_meta_box( 'dailyblank-presets', 'Daily Blank Setup', 'dailyblank_meta_box_cb', 'post', 'side', 'high' );
}

function dailyblank_meta_box_cb($post) {

	// escape for published or scheduled posts, once set we do not present the box
	if ($post->post_status == 'publish' OR $post->post_status == 'future') return; 
    
	// we need access to post meta
	$values = get_post_custom( $post->ID ); 
	
	// the daily tag 
	$last_tag = isset( $values['dailyblank_tag'] ) ? esc_attr( $values['dailyblank_tag'][0] ) : dailyblank_option('basetag'); 

	
	if ($last_tag == dailyblank_option('basetag') ) {
		// this is a new post and has not been assigned a tag, get the next one available
		
		$last_tag = dailyblank_get_next_tag();
		
		// flag for new tdc or not
		$dailyblank_is_new = 'on';
	} else {
		$dailyblank_is_new = 'off';
	}
	
	// the tag to use
	$dailyblank_tag = isset( $values['dailyblank_tag'] ) ? esc_attr( $values['dailyblank_tag'][0] ) : $last_tag; 
	
	// get unix time for last published  daily blank
	$last_dailyblank_date = strtotime( dailyblank_get_last_date() );
	
	
	// first do offset  (every X days)
	
	$daily_offset = 3600 * 24 * dailyblank_option('frequency');
	

	/* if it's been more than 24 hours since the last published daily blank, 
	   set the next date to be on the  day after current; otherwise, set the 
	   next one to be 24 hours after the last published one.
	   
	   Factor in offset for less frequent than daily
	*/
	
	$next_dailyblank_date = ( ( time() - $last_dailyblank_date ) > 3600*24* dailyblank_option('frequency')) ? strtotime('today+' . dailyblank_option('dailytime') ) + $daily_offset :  $last_dailyblank_date + $daily_offset;
	
	
	// Output form, including nonce field   
    wp_nonce_field( 'dailyblank_meta_box_setting', 'dailyblank_settings_nonce' );  
	?>
		<p>You must <strong>Save as Draft</strong> first to enable as a Daily Blank. Delete tag below to just make a regular post. </p>
		<input type="hidden" name="dailyblank_new" id="dailyblank_new" value="<?php echo $dailyblank_is_new?>">
		<input type="hidden" name="dailyblank_date" id="dailyblank_date" value="<?php echo $next_dailyblank_date?>">
    
	<p>  
    <label for="dailyblank_tag">Tag</label>  
    <input type="text" name="dailyblank_tag" id="dailyblank_tag" value="<?php echo $dailyblank_tag; ?>" />  
    </p>  
    
    <p> 
        <input type="checkbox" id="dailyblank_is_new" name="dailyblank_is_new" <?php checked( $dailyblank_is_new, 'on' ); ?> />  
        <label for="dailyblank_is_new">Confirm this action?</label>  
    </p>  
    <?php  	
}

function dailyblank_update_post( $post_id, $dailyblank_tag, $dailyblank_date ) {
// update a post using the provided type and tag

	if( isset( $_POST['dailyblank_tag'] ) )
		update_post_meta( $post_id, 'dailyblank_tag', $dailyblank_tag ); 
	
	// do we have an author?
	$wAuthor = get_post_meta( $post_id, 'wAuthor', 1 );
	
	// add author to new terms if we have one, otherwise just use the tag
	$newterms = ( $wAuthor ) ? $dailyblank_tag . ',' .  $wAuthor : $dailyblank_tag;
		
	// set the tag that identifies this dailyblank
	wp_set_post_terms( $post_id, $newterms, 'post_tag', true );
	
	// assign the category to mark all the daily blanks
	wp_set_post_categories( $post_id, array( dailyblank_option('all_cat') ), true );

	// Update post content with templates for each TDC type
  	$dailyblank_post = array();
  	$dailyblank_post['ID'] = $post_id;
  	
  	// append the hash tag to the title if it's not there
  	$ptitle = get_the_title( $post_id );
  	
  	if ( strpos( $ptitle, ' #' . $dailyblank_tag) === false ) { 
  		// put hashtag in front of title and add any twitter extras
  	    $add_to_title = ( dailyblank_option('twitterextra') != '' ) ? dailyblank_option('twitterextra') . ' ': '';
  	    
  		$dailyblank_post['post_title'] =  '#' . $dailyblank_tag . ' ' . $add_to_title .  $ptitle;
  	}
  	
  	// use the tag for the slug
  	$dailyblank_post['post_name'] = $dailyblank_tag;
  	
  	// set the clock, jaque
  	$dailyblank_post['edit_date'] = true; 
  	$dailyblank_post['post_date'] = date('Y-m-d', $dailyblank_date) . ' ' . dailyblank_option('dailytime') . ':00';
  	$dailyblank_post['post_date_gmt'] = gmdate('Y-m-d', $dailyblank_date) . ' ' . dailyblank_option('dailytime') .':00';
  	
  	// make it a scheduled post
  	$dailyblank_post['post_status'] = 'future';
  	
  	// set the post author to be user id 1, the site admin
  	// This can be made a future admin option?
  	$dailyblank_post['post_author' ] = 1;
  	
  	// get post info so we can get any content submitted in the draft
  	$postinfo = get_post( $post_id ); 
  	$dailyblank_post['post_content'] = $postinfo->post_content . "\n\n";
  	
  	// now append post content with directions
  	
  	$dailyblank_post['post_content'] .= '<p class="tweet-deets">Tweet your response to <a href="https://twitter.com/' . dailyblank_option('twitteraccount') . '">@' . dailyblank_option('twitteraccount') . '</a> and be sure to include the hashtag <a href="https://twitter.com/hashtag/' . $dailyblank_tag .'">#' . $dailyblank_tag . '</a></p>';
  	
	// Update the post into the database
  	wp_update_post( $dailyblank_post );
}



add_action( 'save_post', 'dailyblank_settings_save' );

function dailyblank_settings_save( $post_id )
{
	
	if ( isset($_POST['dailyblank_tag']) AND $_POST['dailyblank_tag'] == '') return; // skip saving if the box not checked, saves over writing
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['dailyblank_settings_nonce'] ) || !wp_verify_nonce( $_POST['dailyblank_settings_nonce'], 'dailyblank_meta_box_setting' ) ) return;
	
	// unhook this function so it doesn't loop infinitely
	remove_action('save_post', 'dailyblank_settings_save');
	
	// send type and tag to function to update post
	dailyblank_update_post( $post_id, esc_attr( $_POST['dailyblank_tag'] ), esc_attr( $_POST['dailyblank_date'] ) );

  	// re-hook this function
	add_action('save_post', 'dailyblank_settings_save');

}

add_action( 'dailyblank_low_supply', 'dailyblank_check_supply', 10, 0);

function dailyblank_check_supply() {

	// no checks if the option set for no reply or if we are in standy mode
	if ( dailyblank_option('supply') == 0 or dailyblank_option('standby') == 'on' ) return;
	
	$scheduled_dailies = getScheduledCount();
	
	// if we are at critical level
	if ( $scheduled_dailies <= dailyblank_option('supply') ) dailyblank_notify_low_supply( $scheduled_dailies );
}

	
function dailyblank_notify_low_supply( $current ) {

	// Let's do some EMAIL! 

	// who gets mail? They do.
	$to_recipients = explode( "," ,  dailyblank_option( 'notify' ) );
	
	// what's it say?
	$subject = get_bloginfo('name') . ' is getting low on scheduled dailies';
	
	$message = 'The number of scheduled dailies on your site '  . get_bloginfo('name') . ' is currently at ' . $current .  ' and the settings are to warn you when the supply level is at ' . dailyblank_option('supply') . '. Surely you do not want to let it lapse! (And yes, I will keep calling you Shirley).
	
Check the queue of scheduled dailies:
' . admin_url( 'edit.php?post_status=future&post_type=post') . '

You may want to create a few new ones ASAP!

' . admin_url( 'post-new.php');

	if ( getDraftCount() > 0 ) $message.= '

Also there are currently ' . getDraftCount() . ' ones to possibly deploy in the submitted  dailies:
' .

admin_url( 'edit.php?post_status=draft&post_type=post');

	// mail it!
	wp_mail( $to_recipients, $subject, $message );

}

?>