<?php
# -----------------------------------------------------------------
# Make the Dailies Recyclable -- as requested by @mdvfunes
# -----------------------------------------------------------------

// set up admin action 
add_action( 'admin_post_dailyblank_recycle', 'prefix_admin_dailyblank_recycle' );


function prefix_admin_dailyblank_recycle() {
// looks for a post= value in URL and makes a copy of Daily Blank with that ID as a new one

	// make sure we got something to work with
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'dailyblank_recyle' == $_REQUEST['action'] ) ) ) {
		wp_die('Aaack. Missing id for Daily to copy.');
	}
 
	// get the original post id
	$post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);

	// and get all the original post data
	$post = get_post( $post_id );
	
 	// use same author
	$new_post_author = $post->post_author;
 
	// if post data exists, create the new duplicate
	if (isset( $post ) && $post != null) {
		
		// remove first word of title, which has the old hashtag in it
		$new_title = substr( strstr( $post->post_title," " ), 1 );
		
		$old_content = $post->post_content;
		
		// find the char position of the tweet instructions in original daily
		$end_of_content = strrpos( $old_content,  '<p class="tweet-deets' );
	
		// get all content up to the tweet instructions
		$new_content = strstr ( $old_content, '<p class="tweet-deets', true );
	
		// new post data array
		$args = array(
			'post_author'    => $new_post_author,
			'post_content'   => $new_content,
			'post_status'    => 'draft',
			'post_title'     => $new_title,
			'post_type'      => $post->post_type,
		);
 
		// make us a post!
		$new_post_id = wp_insert_post( $args );
 
		// get the existing categories an stuff 'em in an array
		$categories = get_the_category( $post_id );
		
		foreach ($categories as $cat) {
			$newcats[] = $cat->term_id;
		}
		
		// assign categories to the new post
		wp_set_post_categories( $new_post_id, $newcats );
		
		// see if there is an author in the post meta
		$wAuthor =  get_post_meta($post_id, 'wAuthor', 1 );
		
		// got one? add to new post
		if ( $wAuthor  ) add_post_meta( $new_post_id, 'wAuthor', $wAuthor );
		
		// see if there is a recycled list already
		$oldRecycled = get_post_meta($post_id, 'wRecycled', 1 );
		
		// if its been recycled before, append the existing post if, otherwise, just add it
		$wRecycled = ( $oldRecycled  ) ? $oldRecycled . ',' . $post_id : $post_id;
		
		// add post meta for recycled list
		add_post_meta( $new_post_id, 'wRecycled', $wRecycled );
		
		// let's do some editing! do a redirect, send a flag for the admin notices
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id . '&recycled=1' ) );
		exit;
		
	} else {
		wp_die('Daily Duplication failed, could not find original daily: ' . $post_id);
	}
}


// displays a notice for recycled new posts
function dailyblank_recycled_admin_notice() {

	if ( !isset ($_GET['recycled']) ) return;
	if ( $_GET['recycled'] == 1 ) {
		echo
    	' <div class="updated notice is-dismissible"><p>This is a newly recycled daily blank. Edit as if it were new!</p></div>';
    }
}

add_action( 'admin_notices', 'dailyblank_recycled_admin_notice' );



// Add a recycling link for hovers on posts that are published
add_filter( 'post_row_actions', 'dailyblank_duplicate_post_link', 10, 2 );

function dailyblank_duplicate_post_link( $actions, $post ) {
	if ( current_user_can('edit_posts') and  $post->post_status == 'publish') {
		$actions['recycle'] = '<a href="admin-post.php?action=dailyblank_recycle&amp;post=' . $post->ID . '" title="Recycle this Daily" rel="permalink">Recycle</a>';
	}
	return $actions;
}


add_action( 'admin_bar_menu', 'dailyblank_recycle_adminbar', 999 );

function dailyblank_recycle_adminbar( $wp_admin_bar ) {

	global $post;
	
	if ( is_single () and $post->post_status == 'publish' ) {
		
		$args = array(
			'id'    => 'recycle',
			'title' => 'Recycle This Daily Blank',
			'href'  => site_url() . '/wp-admin/admin-post.php?action=dailyblank_recycle&amp;post=' . $post->ID,
			'parent' => 'edit',
			'meta' => array( 'class' => ''),

		);
		$wp_admin_bar->add_node( $args );
	}
}

?>