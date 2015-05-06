<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-lg-12" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					
					<?php 
						// get next / previous post ids for navigation purposes
						$next_post_id = get_next_post()->ID;
						$prev_post_id = get_previous_post()->ID;
					?>

					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
						
						<div class="clearfix row">
							<div class="col-md-2 btnnav">
								<?php 
									if ($prev_post_id) {
										echo '<a href="' . get_permalink($prev_post_id). '" class="btn btn-primary btn-medium"><span class="fa fa-chevron-circle-left fa-lg"></span> Previous ' . dailyblank_option('dailykind') . '</a>';
									}
								?>
							</div>
						
							<div class="col-md-8 page-header">
								<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>
							
								<p class="meta"><?php _e("This " . dailyblank_option('dailykind') .   " was published", "wpbootstrap"); ?> <strong><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time></strong> 
								<?php 
								if ( get_post_meta( $post->ID, 'wAuthor', 1 ) ) {
									echo 'shared by <strong>' .  get_post_meta( $post->ID, 'wAuthor', 1 ) . '</strong>';
								}
									
								?>
								
								</p>
							</div>
						
							<div class="col-md-2 btnnav">
								<?php 
									if ($next_post_id) {
										echo '<a href="' . get_permalink($next_post_id). '" class="btn btn-primary btn-medium nav-right">Next ' . dailyblank_option('dailykind') . ' <span class="fa fa-chevron-circle-right fa-lg"></span></a>';
									}
								?>
							</div>
						</div>
						
						
						</header> <!-- end article header -->
					
						<div class="clearfix row">
							<div class="col-md-offset-2 col-md-8">
							<section class="post_content clearfix" itemprop="articleBody">
								<?php the_content(); ?>
							
								<?php dailyblank_twitter_button( $post->ID );?>	

							</section> <!-- end article section -->	
							</div>
						
						</div>
						
							
							<?php
							// get the right tag so we can find the responses
							$dailyblank_tag = get_post_meta( $post->ID, 'dailyblank_tag', 1 ); 
							
							
							// how many tweets to show at a time (using ajax loader for more)
							$tweets_per_view = dailyblank_option('tweetperview');
							
							// First get total count of all responses
							
							// query to get responses
							$responses_count_query = new WP_Query( 
								array(
									'posts_per_page' =>'-1', 
									'post_type' => 'response',
									'hashtags'=> $dailyblank_tag, 
								)
							);
						
							$response_count = $responses_count_query->post_count;
							
							$plural = ( $response_count == 1) ? '' : 's';
							
							// query to get first set of responses
							$responses_query = new WP_Query( 
								array(
									'posts_per_page' => $tweets_per_view, 
									'post_type' => 'response',
									'hashtags'=> $dailyblank_tag, 
								)
							);
							
						
							?>
							<footer>
							
							<div class="clearfix row">	<!-- begin row for tweeted responses -->
							<div class="col-md-offset-3 col-md-6">
							<h3><?php echo $response_count?> Response<?php echo $plural?> Tweeted for this <?php echo dailyblank_option('dailykind')?></h3>
							</div>
							
							<?php while ( $responses_query->have_posts() ) : $responses_query->the_post();?>
								<div class="col-md-offset-3 col-md-6">
								<?php echo wp_oembed_get( get_post_meta( $post->ID, 'tweet_url', 1 ) );?>
								</div>
							<?php endwhile; ?>
							
							
													
							
							
							
							
							<?php if ( $response_count > $tweets_per_view ) :?>
							
							<div class="col-md-offset-3 col-md-6">	
							<?php echo do_shortcode ('[ajax_load_more" post_type="response" taxonomy="hashtags" taxonomy_terms="' . $dailyblank_tag . '" offset="' . $tweets_per_view . '" posts_per_page="' . $tweets_per_view . '" scroll="false" pause="true" transition="fade"  button_label="More Responses"]');?>
							</div>
							<?php endif ?>
									
							</div><!-- end row for tweeted responses -->

							
							<?php 
							// only show edit button if user has permission to edit posts
							if( $user_level > 0 ) { 
							?>
							<a href="<?php echo get_edit_post_link(); ?>" class="btn btn-success edit-post"><i class="icon-pencil icon-white"></i> <?php _e("Edit","wpbootstrap"); ?></a>
							<?php } ?>
							
						</footer> <!-- end article footer -->
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
					<?php comments_template('', true); ?>		
					
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