<?php
# -----------------------------------------------------------------
# Set up the table and put the napkins out
# -----------------------------------------------------------------

add_action( 'init', 'dailyblank_make_post_types' );
add_action( 'init', 'dailyblank_load_theme_options' );
add_action( 'init', 'wp_bootstrap_head_cleanup' );

// override what the parent theme is doing -- it strips RSS feeeds from the head, bad mojo

if( !function_exists( "wp_bootstrap_head_cleanup" ) ) {  
  function wp_bootstrap_head_cleanup() {
    // remove header links
    remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
    remove_action( 'wp_head', 'index_rel_link' );                         // index link
    remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
    remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
    remove_action( 'wp_head', 'wp_generator' );                           // WP version
  }
}

// ----- run re-writes on theme switch
add_action( 'after_switch_theme', 'dailyblank_rewrite_flush' );

function dailyblank_rewrite_flush() {
    flush_rewrite_rules();  
}


// set up front page query to show most recent post only

add_action( 'pre_get_posts', 'dailyblank_query_mods' );

function dailyblank_query_mods( $query ) {
    
    if ( is_archive('response') ) {
        // Display  12 results for response archive
        $query->set( 'posts_per_page', 12 );
        return;
    }    
}

// change the name of admin menu items from "New Posts" to "New Daily Blank" etc
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
 
// Add some admin menus for scheduled and drafts, because these are handy to have
function dailyblank_scheduled_menu() {
	add_submenu_page('edit.php', 'Scheduled Daily Blanks', 'Scheduled Daily Blanks', 'edit_pages', 'edit.php?post_status=future&post_type=post' ); 
	add_submenu_page('edit.php', 'Submitted/Draft Daily Blanks', 'Submitted Daily Blanks', 'edit_pages', 'edit.php?post_status=draft&post_type=post' ); 
}

// edit the post editing admin messages to reflect use of Daily Blank
// h/t http://www.joanmiquelviade.com/how-to-change-the-wordpress-post-updated-messages-of-the-edit-screen/

function dailyblank_post_updated_messages ( $msg ) {
    $msg[ 'post' ] = array (
     0 => '', // Unused. Messages start at index 1.
	 1 => "Daily Blank updated.",
	 2 => 'Custom field updated.',  // Probably better do not touch
	 3 => 'Custom field deleted.',  // Probably better do not touch

	 4 => "Daily Blank updated.",
	 5 => "Daily Blank restored to revision",
	 6 => "Daily Blank published.",

	 7 => "Daily Blank saved.",
	 8 => "Daily Blank submitted.",
	 9 => "Daily Blank scheduled.",
	10 => "Daily Blank draft updated.",
    );
    return $msg;
}

add_filter( 'post_updated_messages', 'dailyblank_post_updated_messages', 10, 1 );


// There was a reason for this, now I forget. #senility
add_filter( 'wp_title', 'dailyblank_wp_title_for_home' );

function dailyblank_wp_title_for_home( $title )
{
  if( empty( $title ) && ( is_home() || is_front_page() ) ) {
    return __( 'The Daily ', 'wp_bootstrap' ) . ' | ' . get_bloginfo( 'description' );
  }
  return $title;
}


// Modify the comment form to be relevant for single posts
add_filter('comment_form_defaults', 'dailyblank_comment_mod');

function dailyblank_comment_mod( $defaults ) {
	$defaults['logged_in_as'] = '';
	
	if ( is_single() ) {
		$defaults['title_reply'] = "Don't Want to Tweet Your Response? Really?";
		$defaults['title_reply_to'] = 'Add a response ';
	
		$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x( 'This site works best when you tweet your response as instructed above, but if you prefer you can enter it below as a comment ', 'wordpress-bootstrap' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
	
		$defaults['label_submit'] = 'Post Response';
	}
	
	return $defaults;
}

/*
Remove author from the posts display; by default Daily blanks are associated with
the author id=1, the first admin user. We never use the author, so no need to display

h/t
https://webdevdoor.com/wordpress/removing-columns-pages-posts

*/

add_filter( 'manage_posts_columns', 'dailyblank_post_columns', 10, 2 );

function dailyblank_post_columns( $columns ) {
  unset(
    $columns['author']
  );
 
  return $columns;
}


# -----------------------------------------------------------------
# Admin Dashboard Widget
# -----------------------------------------------------------------


add_action('wp_dashboard_setup', 'dailyblank_dashboard_widgets');
 
function dailyblank_dashboard_widgets() {

	wp_add_dashboard_widget('dailyblank_admin', 'Stats on the Dailies', 'dailyblank_make_dashboard_widget');
}

function dailyblank_make_dashboard_widget() {
	echo '<p>Currently on this site:</p>
	<ul>
		<li>There are <strong>' . getdailyCount() . '</strong> <a href="' . admin_url( 'edit.php?post_status=publish&post_type=post') . '">published dailies</a></li>
		<li>In the queue are <strong>' . getScheduledCount() . '</strong> <a href="' . admin_url( 'edit.php?post_status=future&post_type=post') . '">scheduled dailies</a></li>
		<li>Waiting for review are <strong>' . getDraftCount() . '</strong> <a href="' . admin_url( 'edit.php?post_status=draft&post_type=post' ) . '">submitted new dailies</a></li>
		<li>Participation in this site includes <strong>' . getResponseCount() . '</strong> <a href="' . admin_url( 'edit.php?post_type=response' ) . '">responses to dailies</a> from <strong>' . getPeopleCount() . '</strong> unique individuals</li>	
	 </ul>';
}


# -----------------------------------------------------------------
# For the Form
# -----------------------------------------------------------------

add_action('wp_enqueue_scripts', 'add_dailyblank_scripts');

function add_dailyblank_scripts() {	 
 
 	if ( is_page('add') ) { // use on just our form page
 		
    	// custom jquery for the uploader on the form
		wp_register_script( 'jquery.dailyblank' , get_stylesheet_directory_uri() . '/js/jquery.add-daily.js', null , '1.0', TRUE );
		wp_enqueue_script( 'jquery.dailyblank' );
		
		 // admin styles for editor
 		wp_enqueue_style( 'wp-admin' );
	}

}


?>