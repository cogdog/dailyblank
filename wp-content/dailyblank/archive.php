<?php 
$dailykind = dailyblank_option('dailykind');

get_header(); ?>
			
			<div id="content" class="clearfix row">
			
				<div id="main" class="col-sm-8  clearfix" role="main">
				
					<div class="page-header">
					<?php if (is_category()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("The Daily " . $dailykind . " Categorized:", "wpbootstrap"); ?></span> <?php single_cat_title(); ?>
						</h1>		
						<p><?php echo category_description( ); ?></p>
						<p class="centertext"><a class="btn btn-info" href="?random" >Gimme a Random One</a></p>
						
					<?php } elseif (is_tag()) { ?> 
						<h1 class="archive_title h2"><span>The Daily 
						
							<?php 
							
							echo $dailykind;
							// fetch the tag in question
							$the_tag = single_term_title( '', false);
						
							if ( $the_tag[0] == '@' ) {
								// this is a twitter tag
								echo ": Ones Contributed by</span> $the_tag ";
							} else {
								// just a regular tag
								echo ' Tagged:</span> "' . $the_tag . '"';
							}
							?>
						
						</h1>
					<?php } elseif (is_author()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("The Daily " . $dailykind . " By:", "wpbootstrap"); ?></span> <?php get_the_author_meta('display_name'); ?>
						</h1>
					<?php } elseif (is_day()) { ?>
						<h1 class="archive_title h2">
							<span><?php _e("The Daily " . $dailykind . " Archives by Day:", "wpbootstrap"); ?></span> <?php the_time('l, F j, Y'); ?>
						</h1>
					<?php } elseif (is_month()) { ?>
					    <h1 class="archive_title h2">
					    	<span><?php _e("The Daily " . $dailykind . " Archives by Month:", "wpbootstrap"); ?>:</span> <?php the_time('F Y'); ?>
					    </h1>
					<?php } elseif (is_year()) { ?>
					    <h1 class="archive_title h2">
					    	<span><?php _e("The Daily " . $dailykind . " Archives by Year:", "wpbootstrap"); ?>:</span> <?php the_time('Y'); ?>
					    </h1>
					<?php } ?>
					</div>

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
						
						<header>
							
							<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
							
								<p class="meta"><?php _e("This Daily " . dailyblank_option('dailykind') .   " was published", "wpbootstrap"); ?> <strong><time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php echo get_the_date('F jS, Y', '','', FALSE); ?></time></strong> 
								<?php 
								if ( get_post_meta( $post->ID, 'wAuthor', 1 ) ) {
									echo 'shared by <strong>' .  get_post_meta( $post->ID, 'wAuthor', 1 ) . '</strong>';
								}
									
								?>
								
								</p>

						
						</header> <!-- end article header -->
					
						<section class="post_content">
						
								<?php //the_excerpt(); ?>
								
								<p class="alignright">
								<a class="btn btn-primary btn-medium align-center" href="<?php the_permalink()?>" >Respond to This Daily <?php echo dailyblank_option('dailykind') ?></a>
								</p>
							
								
					
						</section> <!-- end article section -->
						
						<footer>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
					<div class="col-sm-12 text-center">
					<?php if (function_exists('wp_bootstrap_page_navi')) { // if expirimental feature is active ?>
						
						<?php wp_bootstrap_page_navi(); // use the page navi function ?>

					<?php } else { // if it is disabled, display regular wp prev & next links ?>
						<nav class="wp-prev-next">
							<ul class="pager">
								<li class="previous"><?php next_posts_link(_e('&laquo; Older Dailies', "wpbootstrap")) ?></li>
								<li class="next"><?php previous_posts_link(_e('Newer Responses &raquo;', "wpbootstrap")) ?></li>
							</ul>
						</nav>
					<?php } ?>



					</div>		
								
					
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
    
    		<?php get_sidebar(); // sidebar 1 ?>
    		
			</div> <!-- end #content -->

<?php get_footer(); ?>