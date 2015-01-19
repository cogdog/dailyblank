<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-8 clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<div class="page-header"><h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1></div>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">
							<?php the_content(); ?>
							
							
							
							
							Get mentions?
							
							<?php 
							// fetch the twitter account timeline for replies
 						 		$tweets = getTweets( dailyblank_option('twitteraccount'), 40,  array('exclude_replies'=>false, 'trim_user' => false ) );
 						 
 						 		  // cogdogbug( $tweets );
 						 		  
 						 	?>
 						 	
 						 	
 						 	<?php // set up an array to hold responses
							$new_responses = array();
		
							// walk through the tweets
							foreach($tweets as $tweet) {
		
									// array for hashtags
									$hashtags = extract_hashtags( $tweet['entities']['hashtags'] );
				
									// We want only replies with hashtags and URLs in 'em
									if ( $hashtags AND $tweet['entities']['urls']  ) {
				
										// check for hashtag match against our dailyblank base
										if ( dailyblank_tag_in_hashtags( $hashtags, dailyblank_option('basetag')  ) ) {

											$t_url = 'https://twitter.com/' . $tweet['user']['screen_name'] . '/status/' . $tweet['id_str'];

					
											echo wp_oembed_get( $t_url );				
										}
									}
							}
		
		?>
 						 	
						</section> <!-- end article section -->
						
						<footer>
			
							<?php the_tags('<p class="tags"><span class="tags-title">' . __("Tags","wpbootstrap") . ':</span> ', ', ', '</p>'); ?>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php comments_template('',true); ?>
					
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
    
				<?php get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>