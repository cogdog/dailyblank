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
									
								<ol>	
								<?php	
								
								// fix for missing author tags in dailies!				

								$args = array(
									'posts_per_page'   => -1,
									'meta_key'         => 'wAuthor',
									'post_type'        => 'post',
									'post_status'      => 'publish',
								);

								$myposts = get_posts( $args );
								foreach ( $myposts as $post ) {
									setup_postdata( $post );

									// do we have an author?
									$wAuthor = get_post_meta( $post->ID, 'wAuthor', 1 );
	
									echo '<li>Fixing tags for "' . $post->post_title . '" adding tag for ' . $wAuthor . '</li>';
	
									// add author to new terms if we have one, otherwise just use the tag
									$newterms = ( $wAuthor ) ? $post->post_name . ',' .  $wAuthor : $post->post_name;
	
									wp_set_post_terms( $post->ID, $newterms, 'post_tag' );

									wp_reset_postdata();
								}
								?>
								</ol>


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