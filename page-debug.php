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
									
							<!-- here is the place to insert some diagnostic or one time functional code to do stuff -->
							
							<?php 
							
							$tweets = getTweets( dailyblank_option('twitteraccount'), 200,  array('exclude_replies'=>false, 'trim_user' => false ) );

						// walk through the tweets
						foreach($tweets as $tweet) {

								/*
								echo '<pre>';
								var_dump($tweet);
								echo '</pre>';
								*/
								
								
								// array for hashtags
								$hashtags = extract_hashtags( $tweet['entities']['hashtags'] );

								// We want only replies with hashtags and URLs in 'em
								if ( $hashtags ) {

									// check for hashtag match against our dailyblank base
									if ( dailyblank_tag_in_hashtags( $hashtags, dailyblank_option('basetag')  ) ) {

										// bingo we got a winner; 
										// form URL for the tweet
										$t_url = 'https://twitter.com/' . $tweet['user']['screen_name'] . '/status/' . $tweet['id_str'];
										echo wp_oembed_get( $t_url );
										echo 'Text?' . $tweet['text'];

									}
								}
						}						
							
							
							
							
							/*
							// args for query to get all dailies with a custom field of being recycled
							 $args = array (
							 	 'post_type' => 'post', 
								 'post_status' => 'publish',
								 'posts_per_page'=> -1,
								 'meta_key' => 'wRecycled',
								 'orderby' => 'ID', 
								 'order' => 'ASC'
							 );
							 
							$the_query = new WP_Query($args);
							

						if ( $the_query->have_posts() ) {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								
								$post_id = get_the_ID();
								
								// string of recycled history
								$recycled = get_post_meta( $post_id, 'wRecycled', true );
								
								// make an array
								$relist = explode(",", $recycled);
								
								
								//start output
								$res = '<ul>';
								foreach ($relist as $source_id) {
									// walk each one
									
									// get any values alreayd entered for being recycled TO
									$oldRecycledTo = get_post_meta($source_id, 'wRecycledAs', 1 );
		
									// if its been recycled before, append the new post if, otherwise, just add it
									$wRecycledTo = ( $oldRecycledTo == '' ) ? $source_id : $oldRecycledTo . ',' . $source_id;
		
									// update post meta for recycled list
									update_post_meta( $source_id, 'wRecycledAs', $wRecycledTo);
									
									$res .= '<li><a href="' . get_permalink($source_id) . '">' . get_the_title($source_id) . '</a></li>';
								}
								
								$res .= '</ul>';
								
								echo '<li><a href="' . get_permalink($post_id) . '">' . get_the_title() . '</a> published ' . get_the_date() . ' recycled registered to ' . $res .  '</li>';
							}
							echo '</ul>';
							wp_reset_postdata();
						} else {
							// no posts found
						}		
						
						*/			
							
														?>
							
							<!-- end, hey stop that coding -->


						</section> <!-- end article section -->
						
						<footer>							
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