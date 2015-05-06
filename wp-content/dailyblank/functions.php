<?php


# -----------------------------------------------------------------
# Set up the table and put the napkins out
# -----------------------------------------------------------------

add_action( 'init', 'dailyblank_make_post_types' );
add_action( 'init', 'dailyblank_load_theme_options' );


// ----- run re-writes on theme switch
add_action( 'after_switch_theme', 'dailyblank_rewrite_flush' );

function dailyblank_rewrite_flush() {
    flush_rewrite_rules();  
}



// set up front page query to show most recent post only

add_action( 'pre_get_posts', 'dailyblank_query_mods' );

function dailyblank_query_mods( $query ) {

   // if ( is_admin() || ! $query->is_main_query() )
   //     return;
    
    if ( is_archive('response') ) {
        // Display  12 results for response archive
        $query->set( 'posts_per_page', 12 );
        return;
    }    
}

// change the name of admin menu items from "New Posts"
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
// and of course the Codex http://codex.wordpress.org/Function_Reference/add_submenu_page

add_action( 'admin_menu', 'dailyblank_change_post_label' );
add_action( 'init', 'dailyblank_change_post_object' );
add_action('admin_menu', 'dailyblank_scheduled_menu');


function dailyblank_change_post_label() {
    global $menu;
    global $submenu;
    
    $daily_blank_thing = 'Daily Blank';
    
    $menu[5][0] = $daily_blank_thing . 's';
    $submenu['edit.php'][5][0] = 'All ' . $daily_blank_thing . 's';
    $submenu['edit.php'][10][0] = 'Add ' . $daily_blank_thing;
    $submenu['edit.php'][15][0] = $daily_blank_thing .' Categories';
    $submenu['edit.php'][16][0] = $daily_blank_thing .' Tags';
    echo '';
}
function dailyblank_change_post_object() {

    $daily_blank_thing = 'Daily Blank';

    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name =  $daily_blank_thing;
    $labels->singular_name =  $daily_blank_thing;
    $labels->add_new = 'Add ' . $daily_blank_thing;
    $labels->add_new_item = 'Add ' . $daily_blank_thing;
    $labels->edit_item = 'Edit ' . $daily_blank_thing;
    $labels->new_item =  $daily_blank_thing;
    $labels->view_item = 'View ' . $daily_blank_thing;
    $labels->search_items = 'Search ' . $daily_blank_thing;
    $labels->not_found = 'No ' . $daily_blank_thing . ' found';
    $labels->not_found_in_trash = 'No ' .  $daily_blank_thing . ' found in Trash';
    $labels->all_items = 'All ' . $daily_blank_thing;
    $labels->menu_name =  $daily_blank_thing;
    $labels->name_admin_bar =  $daily_blank_thing;
}
 

function dailyblank_scheduled_menu() {
	add_submenu_page('edit.php', 'Scheduled Daily Blanks', 'Scheduled Daily Blanks', 'manage_options', 'edit.php?post_status=future&post_type=post' ); 
	add_submenu_page('edit.php', 'Submitted/Draft Daily Blanks', 'Submitted Daily Blanks', 'manage_options', 'edit.php?post_status=draft&post_type=post' ); 
}


add_filter( 'wp_title', 'dailyblank_wp_title_for_home' );

function dailyblank_wp_title_for_home( $title )
{
  if( empty( $title ) && ( is_home() || is_front_page() ) ) {
    return __( 'The Daily ', 'wp_bootstrap' ) . ' | ' . get_bloginfo( 'description' );
  }
  return $title;
}


add_filter('comment_form_defaults', 'dailyblank_comment_mod');

function dailyblank_comment_mod( $defaults ) {
	$defaults['logged_in_as'] = '';
	$defaults['title_reply'] = 'Respond Here';
	$defaults['title_reply_to'] = 'Add a comment ';
	
	$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x( 'If twitter does not work or you want to respond directly, enter below ', 'wordpress-bootstrap' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
    
	$defaults['comment_notes_after'] = '<p>If you place a flickr, twitter, YouTube URL on a blank line, it should be automatically embedded when you submit your response</p>';
	$defaults['label_submit'] = 'Post Response';
	return $defaults;
}




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
	wp_register_script( 'dailyblank_options_js' , get_stylesheet_directory_uri() . '/js/jquery.options.js', array( 'jquery' ), '1.0', TRUE );
	wp_enqueue_script( 'dailyblank_options_js' );
}

function dailyblank_load_theme_options() {
	// load theme options Settings

	if ( file_exists( get_stylesheet_directory()  . '/class.dailyblank-theme-options.php' ) ) {
		include_once( get_stylesheet_directory()  . '/class.dailyblank-theme-options.php' );
	}
}


# -----------------------------------------------------------------
# For the Form
# -----------------------------------------------------------------

add_action('wp_enqueue_scripts', 'add_dailyblank_scripts');

function add_dailyblank_scripts() {	 
 
 	if ( is_page('add') ) { // use on just our form page
    		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.dailyblank' , get_stylesheet_directory_uri() . '/js/jquery.add-thing.js', null , '1.0', TRUE );
		wp_enqueue_script( 'jquery.dailyblank' );
	}

}

# -----------------------------------------------------------------
# Custom Content Types
# -----------------------------------------------------------------



function dailyblank_make_post_types() {
	// create post type for responses to a Dailyblank, they will come from
	// harvesting twitter api, each tweet stored as content type
	// URL, twitter username will be stored as custom field

	register_post_type(
		'response', 
		array(
				'labels' => array(
						'name' => __( 'Responses'),
						'singular_name' => __('Response'),
						'add_new' => 'Add New',
						'add_new_item' => 'Add New Response',
						'edit_item' => 'Edit Response',
						'new_item' => 'New Response',
						'all_items' => 'All Responses',
						'view_item' => 'View Response',
						'search_items' => 'Search Responses',
						'not_found' =>  'No responses to do found',
						'not_found_in_trash' => 'No responses found in Trash', 

						),
						'description' => __('Responses via Twitter'),
						'public' => true,
						'show_ui' => true,
						'menu_position' => 5,
						'show_in_nav_menus' => true,
						'supports'  => array(
									'title',
									'custom-fields',
						),
						'has_archive' => true,
						'rewrite' => true,
						'taxonomies' => array(
							'hashtags',
						),
		)
	);
	
	
	// Lets add a tag taxonomy for these, will match with tweeted hash tags
	
	register_taxonomy(
		'hashtags', // Taxonomy name
		array( 'response') , // Post Types
		array( 
			'labels' => array(
						'name' => __( 'Hashtags'),
						'singular_name' => __('Hashtag'),
						'search_items'               => __( 'Search Hashtags' ),
						'all_items'                  => __( 'All Hashtags' ),
						'edit_item'                  => __( 'Edit Hashtag' ),
						'update_item'                => __( 'Update Hashtag' ),
						'add_new_item'               => __( 'Add New Hashtag' ),
						'new_item_name'              => __( 'New Hashtag' ),
						'separate_items_with_commas' => __( 'Separate Hashtags with commas' ),
						'add_or_remove_items'        => __( 'Add or remove hashtags' ),
						'choose_from_most_used'      => __( 'Choose from the most used hashtags' ),
						'not_found'                  => __( 'No Hashtags found.' ),

						),
			'show_ui' => true,
			'show_admin_column' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
		)
	);

	
}

// modify the listings to include custom columns
add_filter( 'manage_edit-responses_columns', 'dailyblank_set_custom_edit_responses_columns' );
add_action( 'manage_responses_posts_custom_column' , 'dailyblank_custom_responses_column', 10, 2 );
 

function dailyblank_set_custom_edit_responses_columns( $columns ) {
// Add a column for the twitter author, insert after the hashtags column 
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels

	$modcols = array();
	
	foreach( $columns as $key => $title ) {
		if ($key == 'date') {
			// Put the new column before the Date column
			$modcols['response'] = 'Tweeter';
		}
		$modcols[$key] = $title;
	}
	
	return $modcols;

}

function dailyblank_custom_responses_column( $column, $post_id ) {
	switch ( $column ) {
        case 'response' :
        	// get the twitter author
        	$tweeter = get_post_meta( $post_id, 'tweet_by', 1);
        	
        	if ( $tweeter ) {
        		echo '<a href="https://twitter.com/' . $tweeter . '" target="_blank">@' . $tweeter . '</a>';
        	} else {
        		echo '--';
        	}
        	 break;
    }
        
}



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
		
	if ($lastposts) {
		// simple loop to get the post_date for the last scheduled post
		foreach($lastposts as $post) {
			//setup_postdata($post);
			return ($post->post_date);
		}
	} else {
	
		//  no scheduled posts, look for most recent published post that has a post meta used by daily blanks
		$args = array( 'numberposts' => 1, 'post_status'=> 'publish', 'meta_key' => 'dailyblank_tag' );
		$lastposts = get_posts( $args );
		
		if ($lastposts) {
			foreach($lastposts as $post) {
				setup_postdata($post);
				return ($post->post_date);
			}
			
		} else {
		
			// return today's date at the designated time of day (stored as option like "05:00" for 5am)
			return ( strtotime('today + ' . dailyblank_option('dailytime') ) );
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
	
	
	
	
	// add a day to the last tdc
	$next_dailyblank_date = strtotime( dailyblank_get_last_date() ) + 3600*24;
	
	
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

function dailyblank_update_post($post_id, $dailyblank_tag, $dailyblank_date)
// update a post using the provided type and tag
{

	if( isset( $_POST['dailyblank_tag'] ) )
		update_post_meta( $post_id, 'dailyblank_tag', $dailyblank_tag ); 
		
	// set the tag that identifies this dailyblank
	wp_set_post_terms( $post_id, $dailyblank_tag, 'post_tag' );
	
	// assign the category to mark all the daily blanks
	wp_set_post_categories( $post_id, array( dailyblank_option('all_cat') ), true );

	// Update post content with templates for each TDC type
  	$dailyblank_post = array();
  	$dailyblank_post['ID'] = $post_id;
  	
  	// append the hash tag to the title if it's not there
  	$ptitle = get_the_title( $post_id );
  	
  	if ( strpos( $ptitle, ' #' . $dailyblank_tag) === false ) { 
  		$dailyblank_post['post_title'] = $ptitle . ' #' . $dailyblank_tag;
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
  	
  	$dailyblank_post['post_content'] .= '<p class="tweet-deets">Tweet a link for your response to <a href="https://twitter.com/' . dailyblank_option('twitteraccount') . '">@' . dailyblank_option('twitteraccount') . '</a> and be sure to include the hashtag <a href="https://twitter.com/hashtag/' . $dailyblank_tag .'">#' . $dailyblank_tag . '</a></p>';
  	
	// Update the post into the database
  	wp_update_post( $dailyblank_post );
}



add_action( 'save_post', 'dailyblank_settings_save' );

function dailyblank_settings_save( $post_id )
{
	
	if ($_POST['dailyblank_tag'] == '') return; // skip saving if the box not checked, saves over writing
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['dailyblank_settings_nonce'] ) || !wp_verify_nonce( $_POST['dailyblank_settings_nonce'], 'dailyblank_meta_box_setting' ) ) return;
	
	// unhook this function so it doesn't loop infinitely
	remove_action('save_post', 'dailyblank_settings_save');
	

	// send type and tag to function to update post
	dailyblank_update_post( $post_id, esc_attr( $_POST['dailyblank_tag'] ), esc_attr( $_POST['dailyblank_date'] ) );


  	// re-hook this function
	add_action('save_post', 'dailyblank_settings_save');

}



# -----------------------------------------------------------------
# Twitter Stuff
# -----------------------------------------------------------------
function dailyblank_twitter_auth() {
	// Status check for the Oauth Twitter Feed for developers
	if ( function_exists('getTweets' ) ) {
		$fb_str = ('Twitter Oauth plugin <strong>is installed</strong>. '); 
		
		// ping twitter for current user name
		$test_tweets = getTweets( dailyblank_option('twitteraccount'), 1 );
	
		if ( $test_tweets["error"] ) {
			$fb_str .= 'Uh oh, we have a problem Houston accessing tweets from @' . dailyblank_option('twitteraccount') . ': ' .  $test_tweets["error"];
		} else {
			$fb_str .= 'Successful connection to tweets from @' . dailyblank_option('twitteraccount');
		}
		
		
		return ( $fb_str );	
	} else {
		return ('Twitter Oauth plugin <strong>is NOT installed</strong>. To enable the twitter responder for this site, install the <a href="https://wordpress.org/plugins/oauth-twitter-feed-for-developers/" target="_blank">oAuth Twitter Feed for Developers</a> via the Add New Plugin interface. Check the documentation tab for information on how to set this up.'); 
	}
}

function dailyblank_tag_in_hashtags( $hashtags, $basetag) {
// runs check on all input tags from twitter API ("#" not include) for match on the base tag
// pattern (e.g. if the base tag is "tdb" it looks for any tag like "tdb100" or "tdb45")

	// make sure the pattern to look for is lower case
	$matchtag = strtolower($basetag);
	
	foreach ($hashtags as $atag) {
		if ( strpos( strtolower($atag), $matchtag ) !== false ) return true;
	}
	
	return false;

}

function extract_hashtags ( $hashtags ) {
// yank out all hashtags from twitter api object array ( 'text'=> 'tag' , 'indices' = >array..)
// to simple array 

	$taglist = array();
	
	// walk the twitter hash tag array, and pluck out the values for 'text'
	// set 'em all to lowercase
	foreach ( $hashtags as $tagdata ) {
		$taglist[] = strtolower( $tagdata['text'] );
	}
	
	return ( $taglist );
}

function dailyblank_twitter_button ( $postid ) {
	$dailyblank_tag = get_post_meta( $postid, 'dailyblank_tag', 1 );						
	$tweet_text = '#' . $dailyblank_tag   . rawurlencode( ' ' . dailyblank_option('tweetstr') );							
	
	echo '<p style="text-align:center;"><a href=\"https://twitter.com/intent/tweet?screen_name=' .  dailyblank_option('twitteraccount') . '&text=' . $tweet_text . ' class="twitter-mention-button" data-size="large">Tweet your response to ' .  dailyblank_option('twitteraccount'). "</a>\n
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></p>";

}


// add a scheduler to check for tweets

if ( ! wp_next_scheduled( 'dailyblank_hello_twitter' ) ) {
	$dt = new DateTime();
	wp_schedule_event( $dt->getTimestamp(), 'hourly', 'dailyblank_hello_twitter');
}

// custom action triggered by event
add_action( 'dailyblank_hello_twitter', 'dailyblank_get_tweets', 10, 1);


function dailyblank_get_tweets( $show_fb = false ) {
	 // fetch the twitter account timeline for replies, grab 100 at a time (200 is max), we want replies and user deets
	 
	 $tweets = getTweets( dailyblank_option('twitteraccount'), 150,  array('exclude_replies'=>false, 'trim_user' => false ) );
	 	
		// set up an array to hold responses
		$new_responses = array();
		
		// walk through the tweets
		foreach($tweets as $tweet) {
		
				// array for hashtags
				$hashtags = extract_hashtags( $tweet['entities']['hashtags'] );
				
				// We want only replies with hashtags in 'em
				if ( $hashtags ) {
				
					// check for hashtag match against our dailyblank base
					if ( dailyblank_tag_in_hashtags( $hashtags, dailyblank_option('basetag')  ) ) {

						// bingo we got a winner; 
						// form URL for the tweet
						$t_url = 'https://twitter.com/' . $tweet['user']['screen_name'] . '/status/' . $tweet['id_str'];
						// build data array for each response found
					
						$new_responses[] = array(
							'id_str' => $tweet['id_str'], // twitter id for tweet
							'tweeter' => $tweet['user']['screen_name'], // tweet author
							'text' => $tweet['text'], // da tweet
							'url' => $t_url, // full url
							'tstamp' => $tweet['created_at'], // timestamp
							'tags' => $hashtags // gimme tags
						);
					
					}
				}
		}
				
		$new_tweets = add_dailyblank_responses( $new_responses );
		
		if ($show_fb) {
			echo 'Cowabunga! we managed to add <strong>' . $new_tweets . '</strong> fresh ones out of <strong>'  .  count( $new_responses  ) . '</strong> found tweets.';
			
			
			
		}
}


function add_dailyblank_responses( $responses ) {
/* 
Utility to add new items to custom post types that represent tweeted responses. Input array includes
	'id_str' => twitter ID
	'url'	=> link to tweet
	'text' => text of the tweet
	'tweeter' => screen name of tweet author
	'tstamp' => time stamp converted to unix time
	'tags' => simple array of hash tags

*/

	$new_ones = 0;
	
	foreach ($responses as $tweet) {
	
		// Skip existing responses if they match by the slug (post_name) matching twitter ID
		if ( the_slug_exists( $tweet['id_str'] ) ) continue;
		 
		 $new_ones++;
		 
		// append the tweet username as a tag
		$tweet['tags'][] = '@' . $tweet['tweeter'];
		
		// Create post object
		$response_type = array(
			'post_type' 	=> 'response',
			'post_title'    => $tweet['text'], // use the entire tweet as title
			'post_name'		=> $tweet['id_str'], // use twitter id as slug
			'post_date_gmt' => date( 'Y-m-d H:i:s', strtotime( $tweet['tstamp'] ) ),
			'post_date'		=> iso8601_gmt_to_local( $tweet['tstamp'], $format = 'Y-m-d H:i:s'),
			'post_status'   => 'publish',
			'post_author'   => 1,
		);

		// Insert the new content type into  database, store it's ID
		$post_id = wp_insert_post( $response_type, $wp_error );
		
		// add tags to hashtags taxonomy
		wp_set_post_terms( $post_id, $tweet['tags'], 'hashtags' );
			
		// add custom field data for the tweet's URL
		update_post_meta($post_id, 'tweet_url', $tweet['url']);	
		
		// add custom field data for the tweet's URL
		update_post_meta($post_id, 'tweet_by', $tweet['tweeter']);	
	}
	
	return ($new_ones);
}


add_action( 'admin_post_seek_tweets', 'prefix_admin_seek_tweets' );


function prefix_admin_seek_tweets() {
	// go get some tweets
	dailyblank_get_tweets(true);
}


function getResponseCount() {
	return wp_count_posts('response')->publish;
}

# -----------------------------------------------------------------
# Spanners and gizmos- misc functions
# -----------------------------------------------------------------


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


// handy dandy debug dumper.
function cogdogbug($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
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