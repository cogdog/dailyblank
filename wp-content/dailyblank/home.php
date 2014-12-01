<?php

	$args = array(
		'posts_per_page' => 1
	);
	
	$home_query = new WP_Query( $args );

?>


<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
				
    			<div id="main" class="col-lg-8 col-lg-offset-2 centertext" role="main">
    			<h1><?php bloginfo(); ?></h1>
    			<p><?php bloginfo( 'description' ); ?></p>
    						
					<?php while( $home_query->have_posts() ) : $home_query->the_post(); ?>
					
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