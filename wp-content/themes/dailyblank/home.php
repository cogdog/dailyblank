<?php
	// one post only, please!
	$args = array(
		'posts_per_page' => 1
	);
	
	$home_query = new WP_Query( $args );

?>


<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<?php if ( $home_query->have_posts() ) : while( $home_query->have_posts() ) : $home_query->the_post(); ?>	
				
									
					<?php 
						// previous post ids for navigation purposes						
						$prev_post_id = get_previous_post()->ID;
						
						// get response count
						$response_count = ( get_post_meta($post->ID, 'response_count', 1)) ? get_post_meta($post->ID, 'response_count', 1) : 0;
						$r_plural = ( $response_count == 1) ? '' : 's';
					?>
									
				<div class="col-sm-2 btnnav">
					<?php 
						if ($prev_post_id) {
							echo '<a href="' . get_permalink($prev_post_id). '" class="btn btn-primary btn-medium"><span class="glyphicon glyphicon-hand-left" aria-hidden="true"></span> Previous ' . dailyblank_option('dailykind') . '</a>';
						}
					?>
				</div>

				
    			<div id="main" class="col-sm-8  centertext" role="main">
    			<h1><?php bloginfo(); ?></h1>
    			<p><?php bloginfo( 'description' ); ?></p>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
						
						<header>
						
							<?php 
								// use the image specified from options
								$featured_src = wp_get_attachment_image_src( dailyblank_option('frontimg'), 'wpbs-featured-home' );
							?>

							<div class="jumbotron" style="background-image: url('<?php echo $featured_src[0]; ?>'); background-repeat: no-repeat; background-position: 50% 0;">
				
							
							<div class="page-header"><h2 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2></div>
							
							<p class="meta"><?php _e("published", "wpbootstrap"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time> &bull; <?php echo $response_count?> response<?php echo $r_plural?></p>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix">
							<?php the_content( __("But wait, there's more &raquo;","wpbootstrap") ); ?>
							
														
							<?php //dailyblank_twitter_button( $post->ID );?>


						</section> <!-- end article section -->
						
						<footer>
						<br />
						<a class="btn btn-primary btn-large" href="<?php the_permalink()?>" ><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Go to Today's Daily <?php echo dailyblank_option('dailykind') ?></a>

						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("No Daily " . dailyblank_option('dailykind') . "s Found!", "wpbootstrap"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("You ought to create the first one, ok?", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
			</div> <!-- end #content -->

<?php get_footer(); ?>