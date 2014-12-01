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
							<div class="col-md-2 center-block">
								<?php 
									if ($prev_post_id) {
										echo '<a href="' . get_permalink($prev_post_id). '" class="btn btn-primary btn-medium">Previous ' . dailyblank_option('dailykind') . '</a>';
									}
								?>
							</div>
						
							<div class="col-md-8 page-header">
								<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>
							
								<p class="meta"><?php _e("This " . dailyblank_option('dailykind') .   " was published", "wpbootstrap"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time></p>
							</div>
						
							<div class="col-md-2 center-block">
								<?php 
									if ($next_post_id) {
										echo '<a href="' . get_permalink($next_post_id). '" class="btn btn-primary btn-medium">Next ' . dailyblank_option('dailykind') . '</a>';
									}
								?>
							</div>
						</div>
						
						
						</header> <!-- end article header -->
					
						<div class="clearfix row">
							<div class=" col-md-offset-2 col-md-8">
							<section class="post_content clearfix" itemprop="articleBody">
								<?php the_content(); ?>
							
								<?php dailyblank_twitter_button( $post->ID );?>	

							</section> <!-- end article section -->	
							</div>
						
						</div>
						
							
							<?php
							// get the right tag so we can find the responses
							$dailyblank_tag = get_post_meta( $post->ID, 'dailyblank_tag', 1 ); 
							
							// query to get responses
							$responses_query = new WP_Query( 
								array(
									'posts_per_page' =>'-1', 
									'post_type' => 'response',
									'hashtags'=> $dailyblank_tag, 
								)
							);
						
							$response_count = $responses_query->post_count;
							$plural = ( $response_count == 1) ? '' : 's';
						
							$item_counter = 0;
							?>
							<footer>
							
							<div class="clearfix row">	<!-- begin row for tweeted responses -->
							
							<h3><?php echo $response_count?> Response<?php echo $plural?> Tweeted for this <?php echo dailyblank_option('dailykind')?></h3>
							
							<?php while ( $responses_query->have_posts() ) : $responses_query->the_post();?>
							
								<?php
									// start a new column?
									if ( $item_counter % 3 == 0)  {
										echo '<div class="col-md-4">'; 
									} 
						
									// bump counter
									$item_counter++;
						
									echo wp_oembed_get( get_post_meta( $post->ID, 'tweet_url', 1 ) );
						
									if ( $item_counter % 3 == 0 ) echo '</div>'; // -- end of row 
								?>

							<?php endwhile; ?>
							
							</div><!-- end row for tweeted responses -->
															
							<?php wp_reset_query(); ?>
							
							<?php 
							// only show edit button if user has permission to edit posts
							if( $user_level > 0 ) { 
							?>
							<a href="<?php echo get_edit_post_link(); ?>" class="btn btn-success edit-post"><i class="icon-pencil icon-white"></i> <?php _e("Edit","wpbootstrap"); ?></a>
							<?php } ?>
							
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
			</div> <!-- end #content -->

<?php get_footer(); ?>