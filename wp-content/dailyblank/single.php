<?php 
	get_header(); 
	// flag if the Ajax Load More plugin is loaded
	$use_ajax_load_more = ( function_exists('alm_install' ) ) ? true : false;
?>
			
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
							<div class="col-sm-2 btnnav">
								<?php 
									if ($prev_post_id) {
										echo '<a href="' . get_permalink($prev_post_id). '" class="btn btn-primary btn-medium"><span class="glyphicon glyphicon-hand-left" aria-hidden="true"></span> Previous ' . dailyblank_option('dailykind') . '</a>';
									}
								?>
							</div>
						
							<div class="col-sm-8 page-header">
								<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>
							
								<p class="meta"><?php _e("This Daily " . dailyblank_option('dailykind') .   " published", "wpbootstrap"); ?> <strong><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time></strong> 
								<?php 
								
								$wAuthor = get_post_meta( $post->ID, 'wAuthor', 1 );
								if ( $wAuthor ) {
									// link to tag archive if twitter name
									$credits = ( is_twitter_name( $wAuthor ) ) ? '<a href="https://twitter.com/' . $wAuthor . '">' . $wAuthor . '</a>' : $wAuthor;
									echo '-- shared by <strong>' .  $credits . '</strong>-- ';
								}
								
								// get the right tag so we can find the responses
								$dailyblank_tag = get_post_meta( $post->ID, 'dailyblank_tag', 1 ); 

								// query to get sll responses
								$responses_count_query = new WP_Query( 
									array(
										'posts_per_page' =>'-1', 
										'post_type' => 'response',
										'hashtags'=> $dailyblank_tag, 
									)
								);

								// store total count of all responses
								$response_count = $responses_count_query->post_count;
								// because grammar
								$r_plural = ( $response_count == 1) ? '' : 's';
								
								// update pronto
								dailyblank_update_meta( $post->ID, $response_count);

								// get view count
								$view_count = ( get_post_meta($post->ID, 'daily_visits', 1)) ? get_post_meta($post->ID, 'daily_visits', 1) : 0;
								
								// more grammar
								$v_plural = ( $view_count == 1) ? '' : 's';
								
								// add response and visit counts
								echo ' has <strong>' . $response_count . '</strong> response' . $r_plural . ' and <strong>' . $view_count . '</strong> view' . $v_plural;
									
								?>
								
								</p>
							</div>
						
							<div class="col-sm-2 btnnav">
								<?php 
									if ($next_post_id) {
										echo '<a href="' . get_permalink($next_post_id). '" class="btn btn-primary btn-medium nav-right">Next ' . dailyblank_option('dailykind') . ' <span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span> </a>';
									}
								?>
							</div>
						</div>
						
						
						</header> <!-- end article header -->
					
						<div class="clearfix row">
							<div class="col-sm-offset-2 col-sm-8">
							<section class="post_content clearfix" itemprop="articleBody">
								<?php the_content(); ?>
								
								<?php dailyblank_twitter_button( $post->ID );?>	
								
								
								<?php
									// try loading any recycled IDs
									$wRecycled = get_post_meta( $post->ID, 'wRecycled', 1 ); 
									
									// if we have recycled is, lets set them up for sitting out
									if ( ! empty ( $wRecycled ) ) {
										echo '<h4 class="text-center"><br /><br /></strong>This Daily ' . dailyblank_option('dailykind') . ' has been recycled from previously published ones:</h4><p class="text-center">';
										
										
										// pop the into an array
										$recycle_ids = explode(',', $wRecycled );
										
										// walk the ids after sorting them
										sort (  $recycle_ids ) ;
										foreach ( $recycle_ids as $rid ) {
											echo '&bull; <a href="' . get_permalink( $rid ) . '">' . get_the_title( $rid ) . '</a> (' . get_the_date( 'M j, Y', $rid ) . ")<br />\n";
										}
										echo '</p>';
									}

								?>
	

							</section> <!-- end article section -->	
							</div>
						
						</div>
						
							
							<footer>
							
							<div class="clearfix row">	<!-- begin row for tweeted responses -->
								<div class="col-sm-offset-3 col-sm-6">
								<h3><?php echo $response_count?> Response<?php echo $r_plural?> Tweeted for this Daily <?php echo dailyblank_option('dailykind')?></h3>
								</div>
							
							
								<?php if ($use_ajax_load_more) : //use the Ajax Load More Plugin
								
									// how many tweets to show at a time
									$tweets_per_view = dailyblank_option('tweetperview');

									// query to get first set of responses
									$responses_query = new WP_Query( 
										array(
											'posts_per_page' => $tweets_per_view, 
											'post_type' => 'response',
											'hashtags'=> $dailyblank_tag, 
										)
									);
								?>
							
									<?php while ( $responses_query->have_posts() ) : $responses_query->the_post();?>
										<div class="col-sm-offset-3 col-sm-6">
										<?php echo wp_oembed_get( get_post_meta( $post->ID, 'tweet_url', 1 ) );?>
										</div>
									<?php endwhile; ?>
							
									<?php if ( $response_count > $tweets_per_view ) :?>
							
										<div class="col-sm-offset-3 col-sm-6">	
										<?php echo do_shortcode ('[ajax_load_more post_type="response" taxonomy="hashtags" taxonomy_terms="' . $dailyblank_tag . '" offset="' . $tweets_per_view . '" posts_per_page="' . $tweets_per_view . '" scroll="false" pause="true" transition="fade"  button_label="See More Responses"]');?>
										</div>
									<?php endif ?>
								
								<?php else: // just list all resposnes (no ajax)?>
									<?php while ( $responses_count_query->have_posts() ) : $responses_count_query->the_post();?>
										<div class="col-sm-offset-3 col-sm-6">
										<?php echo wp_oembed_get( get_post_meta( $post->ID, 'tweet_url', 1 ) );?>
										</div>
									<?php endwhile; ?>

								<?php endif?>
								
									
							</div><!-- end row for tweeted responses -->
							
						</footer> <!-- end article footer -->
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
					<?php 
					
					wp_reset_query(); 
					
					comments_template('', true); 
					
					?>		
					
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