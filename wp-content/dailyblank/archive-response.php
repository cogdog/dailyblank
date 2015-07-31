<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-12" role="main">
				
					<div class="page-header">
						<h1 class="archive_title">Recent <?php echo dailyblank_option('dailykind')?> Responses</h1>
						<p class="centertext">Out of <strong><?php echo getResponseCount()?></strong> total responses</p>
						
					</div>
										
					<div class="clearfix row well">	<!-- begin row for tweeted responses -->
					
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						<div class="col-sm-4">
						<?php echo wp_oembed_get( get_post_meta( $post->ID, 'tweet_url', 1 ) );?>
						</div>
					
					<?php endwhile; ?>	
					
					</div><!-- end row for tweeted responses -->
					<div class="clearfix row">
						
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
					
					
					<?php endif; ?>
			
					</article>
					
					
				</div> <!-- end #main -->
    
    
			</div> <!-- end #content -->

<?php get_footer(); ?>