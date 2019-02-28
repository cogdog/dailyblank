<?php
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


// edit the post editing admin messages for Custom Post Types
// h/t http://www.joanmiquelviade.com/how-to-change-the-wordpress-post-updated-messages-of-the-edit-screen/

function dailyblank_response_updated_messages ( $msg ) {
    $msg[ 'response' ] = array (
     0 => '', // Unused. Messages start at index 1.
	 1 => "Response updated.",
	 2 => 'Custom field updated.',  // Probably better do not touch
	 3 => 'Custom field deleted.',  // Probably better do not touch

	 4 => "Response updated.",
	 5 => "Response restored to revision",
	 6 => "Response published.",

	 7 => "Response saved.",
	 8 => "Response submitted.",
	 9 => "Response scheduled.",
	10 => "Response draft updated.",
    );
    return $msg;
}

add_filter( 'post_updated_messages', 'dailyblank_response_updated_messages', 10, 1 );


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

?>