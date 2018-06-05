<?php
// Template for an item in the hashtags taxonomy

global $wp_query; // give us query

$hashtag_term =	$wp_query->queried_object; // the term being used for this
?>


<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-12" role="main">
				
					<div class="page-header">
					
						<h1 class="archive_title">
					
						<?php 
						// how many items found
						$found_count = $wp_query->found_posts;
						
						
						// first part of title
						echo ' Daily ' .  dailyblank_option('dailykind') . ' Responses' ;
						
						if ( strpos($hashtag_term->name, "@") !== false  ) {
							// this is a tag for a twitter account
							echo  ' Tweeted by ' .  $hashtag_term->name;
					
						} else {
							// normal tag
							echo ' Tagged "' . $hashtag_term->name . '"';
					
						}
						?>
						
						</h1>
						<p class="text-center"><strong><?php echo $found_count?></strong> total responses found</p>
					</div>
					
					
					
					<?php $item_counter = 0; // item counter for row breaks ?>

					<div class="clearfix row well">	<!-- begin row for tweeted responses -->
					
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
						<?php
							// start a new column?
							if ( $item_counter % 3 == 0)  {
								echo '<div class="col-sm-4">'; 
							} 
						
							// bump counter
							$item_counter++;
						
							echo wp_oembed_get( get_post_meta( $post->ID, 'tweet_url', 1 ) );
						
							if ( $item_counter % 3 == 0 ) echo '</div>'; // -- end of row 
						?>
					
					<?php endwhile; ?>	
					
					</div><!-- end row for tweeted responses -->

					<div class="col-sm-12 text-center">
					<?php if (function_exists('wp_bootstrap_page_navi')) { // if expirimental feature is active ?>
						
						<?php wp_bootstrap_page_navi(); // use the page navi function ?>

					<?php } else { // if it is disabled, display regular wp prev & next links ?>
						<nav class="wp-prev-next">
							<ul class="pager">
								<li class="previous"><?php next_posts_link(_e('&laquo; Older Responses', "wpbootstrap")) ?></li>
								<li class="next"><?php previous_posts_link(_e('Newer Responses &raquo;', "wpbootstrap")) ?></li>
							</ul>
						</nav>
					<?php } ?>



					</div>		
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("No Responses Yet", "wpbootstrap"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, What you were looking for is not here.", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
    
			</div> <!-- end #content -->

<?php get_footer(); ?>