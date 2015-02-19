<?php get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-md-8 col-md-offset-2  clearfix" role="main">
				
					<div class="page-header">
					<?php if (is_category()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("Daily Blanks Categorized:", "wpbootstrap"); ?></span> <?php single_cat_title(); ?>
						</h1>
					<?php } elseif (is_tag()) { ?> 
						<h1 class="archive_title h2">
							<span><?php _e("Daily Blanks Tagged:", "wpbootstrap"); ?></span> <?php single_tag_title(); ?>
						</h1>
					<?php } elseif (is_author()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("Daily Blanks By:", "wpbootstrap"); ?></span> <?php get_the_author_meta('display_name'); ?>
						</h1>
					<?php } elseif (is_day()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("Daily Blank Daily Archives:", "wpbootstrap"); ?></span> <?php the_time('l, F j, Y'); ?>
						</h1>
					<?php } elseif (is_month()) { ?>
					    <h1 class="archive_title h2">
					    	<span><?php _e(" Daily Blank Monthly Archives:", "wpbootstrap"); ?>:</span> <?php the_time('F Y'); ?>
					    </h1>
					<?php } elseif (is_year()) { ?>
					    <h1 class="archive_title h2">
					    	<span><?php _e("Daily Blank Yearly Archives:", "wpbootstrap"); ?>:</span> <?php the_time('Y'); ?>
					    </h1>
					<?php } ?>
					</div>

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
						
						<header>
							
							<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
							
								<p class="meta"><?php _e("This " . dailyblank_option('dailykind') .   " was published", "wpbootstrap"); ?> <strong><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time></strong> 
								<?php 
								if ( get_post_meta( $post->ID, 'wAuthor', 1 ) ) {
									echo 'shared by <strong>' .  get_post_meta( $post->ID, 'wAuthor', 1 ) . '</strong>';
								}
									
								?>
								
								</p>

						
						</header> <!-- end article header -->
					
						<section class="post_content">
						
								<?php the_content(); ?>
								
								<a class="btn btn-primary btn-medium align-center" href="<?php the_permalink()?>" >Respond to This <?php echo dailyblank_option('dailykind') ?></a>
							
								
					
						</section> <!-- end article section -->
						
						<footer>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
					<?php if (function_exists('page_navi')) { // if expirimental feature is active ?>
						
						<?php page_navi(); // use the page navi function ?>

					<?php } else { // if it is disabled, display regular wp prev & next links ?>
						<nav class="wp-prev-next">
							<ul class="pager">
								<li class="previous"><?php next_posts_link(_e('&laquo; Older Daily Blanks', "wpbootstrap")) ?></li>
								<li class="next"><?php previous_posts_link(_e('Newer Daily Blanks &raquo;', "wpbootstrap")) ?></li>
							</ul>
						</nav>
					<?php } ?>
								
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("No Posts Yet", "wpbootstrap"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, What you were looking for is not here.", "wpbootstrap"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
    
			</div> <!-- end #content -->

<?php get_footer(); ?>