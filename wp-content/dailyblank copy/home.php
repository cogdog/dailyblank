<?php

	$args = array(
		'posts_per_page' => 1
	);
	
	$home_query = new WP_Query( $args );

?>


<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<?php while( $home_query->have_posts() ) : $home_query->the_post(); ?>	
				
									
					<?php 
						// previous post ids for navigation purposes						
						$prev_post_id = get_previous_post()->ID;
					?>
									
				<div class="col-lg-2 btnnav">
					<?php 
						if ($prev_post_id) {
							echo '<a href="' . get_permalink($prev_post_id). '" class="btn btn-primary btn-medium"><span class="fa fa-chevron-circle-left fa-lg"></span> Previous ' . dailyblank_option('dailykind') . '</a>';
						}
					?>
				</div>

				
    			<div id="main" class="col-lg-8  centertext" role="main">
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
							
							<p class="meta"><?php _e("Published", "wpbootstrap"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time></p>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix">
							<?php the_content( __("Read more &raquo;","wpbootstrap") ); ?>
							
														
							<?php dailyblank_twitter_button( $post->ID );?>


						</section> <!-- end article section -->
						
						<footer>
						See all responses for<br />
						<a class="btn btn-primary btn-medium" href="<?php the_permalink()?>" >Today's <?php echo dailyblank_option('dailykind') ?></a>

						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
			
				</div> <!-- end #main -->
			</div> <!-- end #content -->

<?php get_footer(); ?>