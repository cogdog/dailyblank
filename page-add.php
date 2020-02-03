<?php
/*
Template Name: Create a New Daily
*/

// ------------------------ defaults ------------------------

$dailykind = dailyblank_option('dailykind');

// amount of characters available, leave 4 for # and tag number
$tweetlength = 140 - ( strlen( dailyblank_option( 'basetag' ) ) + 4 );

// default welcome message
$feedback_msg = 'Enter your idea for a new Daily ' . $dailykind . '.';

// by whom
$wAuthor = "@yourtwittername or Anonymous";

$errors = [];
$wTitle =  $wText = '';

// bonus charm
$exclamations = array('Awesome', 'That rocks', 'You are the best', 'Wonderful', 'Shazam', 'You Rule', 'Aye Carumba', 'Fantastico!', 'Amazing!', 'Wow, can we just say wow', 'We sure appreciate this');

// ------------------------ presets ------------------------

// final status
$is_submitted = false;

// verify that a  form was submitted and it passes the nonce check
if ( isset( $_POST['dailyblank_form_make_submitted'] ) && wp_verify_nonce( $_POST['dailyblank_form_make_submitted'], 'dailyblank_form_make' ) ) {

 		// grab the variables from the form
 		$wTitle = 		sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 		( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';
 		$wText = 		$_POST['wText'];


 		// let's do some validation, store an error message for each problem found
 		$errors = array();

 		if ( $wTitle == '' ) $errors['wTitle'] = '<span class="label label-danger">Tweet Prompt Missing</span> - write the bit that will appear in a tweet.';

if ( strlen( $wTitle ) > $tweetlength ) $errors['wTitle'] = '<span class="label label-danger">Tweet Prompt Too Long</span> - The prompt must be less than ' . $tweetlength . ' characters (10 character tweet length less number of characters for the hash tag)';

 		if ( $wAuthor == '' ) $errors['wAuthor'] = '<span class="label label-danger">Credit Name Missing</span> - enter your name or at least some sort of secret identity to take credit for thos contribution.';

 		// This is for one spammer that has been bothering all these sites.
 		if ( strpos( $wText, 'http://www.FyLitCl7Pf7kjQdDUOLQOuaxTXbj5iNG.com' ) !== false OR $wAuthor == 'Mark'  ) die ('Hey "Mark" stop wasting your time and my time spamming this form. Your link is going nowhere. Please stop.');


 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in the form. Please correct and try again.<ul>';

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



			// twitter name used for author name
			if ( is_twitter_name( $wAuthor )  ) {
				 //add twitter name as a tag
				$w_information['tags_input'] = array('post_tag' => $wAuthor);
				$fb_extra = 'Once published, this will be added to <a href="' . site_url() . '/tag/' . $wAuthor .  '">your collection of published Dailies</a>.' ;
			}

			// insert as a new post
			$post_id = wp_insert_post( $w_information );


			// store the author as post meta data
			add_post_meta($post_id, 'wAuthor', $wAuthor);


			$randy = array_rand($exclamations);

			$feedback_msg = 'Thanks for sharing a new Daily ' . $dailykind .  '. ' . $exclamations[$randy] . '! ' . $fb_extra . ' Do you want to <a href="'. site_url() . '/add">add another one</a>? We hope so.';

			$is_submitted = true;

		} // count errors

} // end form submmitted check
?>
<?php get_header(); ?>

			<div id="content" class="clearfix row">

				<div id="main" class="col-sm-8 col-sm-offset-2 clearfix" role="main">

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
									$box_style = '<div class="alert alert-danger" role="alert">';
								} else {
									$box_style = '<div class="alert alert-success" role="alert">';
								}

		    					echo $box_style . $feedback_msg . '</div>';
		    				?>

							<?php if ( !$is_submitted ) : // show form? ?>
							<form id="dailyform" class="dailyform" method="post" action="">

							<div class="form-group<?php if ( is_array($errors) AND array_key_exists("wTitle", $errors )) echo ' has-error ';?>">
								<label for="wTitle"><?php _e('Tweet Length Prompt', 'wpbootstrap' ) ?></label>
								<span id="wTitleHelpBlock" class="help-block">Write out a Daily <?php echo $dailykind?> as a tweet-length prompt (<strong><span id="wCount">0</span></strong> characters used of <strong><?php echo $tweetlength?></strong>  available)</span>
								<input type="text" name="wTitle" id="wTitle" class="required" value="<?php echo $wTitle; ?>" tabindex="1" aria-describedby="wTitleHelpBlock" />
							</div>


							<div class="form-group<?php if ( is_array($errors) AND  array_key_exists("wAuthor",$errors)) echo ' has-error ';?>">
								<label for="wAuthor"><?php _e('How to Credit Yourself', 'wpbootstrap' ) ?></label>
								<span id="wAuthorHelpBlock" class="help-block">Publish under your name, twitter handle, secret agent name, or remain "Anonymous". If you use a twitter name (including the "@") the site will track your contributions. </span>
								<input type="text" name="wAuthor" id="wAuthor" class="required" value="<?php echo $wAuthor; ?>" tabindex="2" aria-describedby="wAuthorHelpBlock" />
							</div>

							<div class="form-group<?php if ( is_array($errors) AND  array_key_exists("wText",$errors)) echo ' has-error ';?>">
									<label for="wText"><?php _e('Additional Instructions, Details, Description to make it Interesting', 'wpbootstrap') ?></label>
									<span id="wTextHelpBlock" class="help-block">Use the editing area below to add and format that will help explain what to do (no limit) or suggestions for media to use. Images can uploaded (click the image tool and then Upload tab). We will try to use all information you provide to add to the details for the published Daily <?php echo $dailykind?> (we reserve some right to edit).</span>
									<!--textarea name="wText" id="wText" rows="15"  tabindex="9" aria-describedby="wTextHelpBlock"><?php echo stripslashes( $wText );?></textarea -->



								<?php
								// set up for inserting the WP post editor
								$settings = array(
									'textarea_name' => 'wText',
									'media_buttons' => false,
									'textarea_rows' => 15
								);

								wp_editor(  stripslashes( $wText ), 'wTextHTML', $settings );
								?>
							</fieldset>
						</div>


							<fieldset>
								<?php wp_nonce_field( 'dailyblank_form_make', 'dailyblank_form_make_submitted' ); ?>

								<input type="submit" class="btn btn-primary" value="Send it In" id="wPublish" name="wPublish" tabindex="11">
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
					    	<p><?php _e("Oi Vey, we cannot find the content. Is there a Page created?", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>

					<?php endif; ?>

				</div> <!-- end #main -->

			</div> <!-- end #content -->

<?php get_footer(); ?>
