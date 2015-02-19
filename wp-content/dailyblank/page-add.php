<?php
// ------------------------ defaults ------------------------

$dailykind = dailyblank_option('dailykind');

// amount of characters available, leave 4 for # and tag number
$tweetlength = 140 - ( dailyblank_option('basetag') + 4);

// default welcome message
$feedback_msg = 'Enter your idea for a new ' . $dailykind . '.';

// by who
$wAuthor = "Anonymous";

// bonus charm
$exclamations = array('Awesome', 'That rocks', 'You are the best', 'Wonderful', 'Shazam', 'You Rule');

// ------------------------ presets ------------------------

// final status
$is_submitted = false;

// verify that a  form was submitted and it passes the nonce check
if ( isset( $_POST['dailyblank_form_make_submitted'] ) && wp_verify_nonce( $_POST['dailyblank_form_make_submitted'], 'dailyblank_form_make' ) ) {
 
 		// grab the variables from the form
 		$wTitle = 					sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 					( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';		
 		$wText = 					$_POST['wText'];
 		
 		
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
 		
 		if ( $wTitle == '' ) $errors[] = '<strong>Challenge Instructions Missing</strong> - write the challenge!.'; 
 		
 		if ( strlen( $wTitle ) > $tweetlength ) $errors[] = '<strong>Challenge Instructions too long</strong> - The challenge must be less than ' . $tweetlength . ' characters (10 character tweet length less number of characters for the hash tag)'; 	
 		 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in your entry. Please correct and try again.<ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul>';
 			
 		} else {
 			
 			// good enough, let's make a post! 
 			 			
			$w_information = array(
				'post_title' => $wTitle,
				'post_content' => $wText,
				'post_status' => 'draft',
			);

			
			// insert as a new post
			$post_id = wp_insert_post( $w_information );
				
			// store the author as post meta data
			add_post_meta($post_id, 'wAuthor', $wAuthor);
			
			$randy = array_rand($exclamations);
				
			$feedback_msg = 'Thanks for sharing a new ' . $dailykind .  '. ' . $exclamations[$randy] . '! Do you want to <a href="'. site_url() . '/add">add another one</a>?';
	
			$is_submitted = true;
			 	
		} // count errors		
								
} // end form submmitted check
?>
<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-md-8 col-md-offset-2 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<div class="page-header"><h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1></div>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">
							<?php the_content(); ?>
							
							<?php  
								// set up box code colors CSS

								if ( count( $errors ) ) {
									$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
								} else {
									$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';
								} 
		    			    	
		    					echo $box_style . $feedback_msg . '</div>';
		    				?>   

							<?php if ( !$is_submitted ) : // show form? ?>							
							<form  id="dailyform" class="dailyform" method="post" action="">
		
			
							<fieldset>
								<label for="wTitle"><?php _e('The Challenge', 'wpbootstrap' ) ?></label><br />
								<p>Write out the <?php echo $dailykind?> as a tweet-length challenge (<strong><span id="wCount">0</span></strong> characters used of <strong><?php echo $tweetlength?></strong>  available)</p>
								<input type="text" name="wTitle" id="wTitle" class="required" value="<?php echo $wTitle; ?>" tabindex="1" />
							</fieldset>	
			

							<fieldset>
								<label for="wAuthor"><?php _e('How to Credit Yourself', 'wpbootstrap' ) ?></label><br />
								<p>Publish under your name, twitter handle, secret agent name, or remain "Anonymous".</p>
								<input type="text" name="wAuthor" id="wAuthor" class="required" value="<?php echo $wAuthor; ?>" tabindex="2" />
							</fieldset>	
				
							<fieldset>
									<label for="wText"><?php _e('Additional Instructions, Details', 'wpbootstrap') ?></label>
									<p>Use the editing area below to add any other instructions that will help explain what to do (no limit). Contents may be edited, but will appear on the published <?php echo $dailykind?>.</p>
									<textarea name="wText" id="wText" rows="15"  tabindex="9"><?php echo stripslashes( $wText );?></textarea>
							</fieldset>
							
			
							<fieldset>
								<?php wp_nonce_field( 'dailyblank_form_make', 'dailyblank_form_make_submitted' ); ?>
				
								<input type="submit" class="pretty-button pretty-button-green" value="Submit" id="wPublish" name="wPublish" tabindex="11"> 
							</fieldset>
						
						</form>
						<?php endif?>


						</section> <!-- end article section -->					
					</article> <!-- end article -->
					
					<?php endwhile; ?>		
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("Not Found", "wpbootstrap"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, but the requested resource was not found on this site.", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
			</div> <!-- end #content -->

<?php get_footer(); ?>