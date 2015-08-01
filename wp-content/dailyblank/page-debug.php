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
									
									
							<?php					
							// test gettweets

	 // fetch the twitter account timeline for replies, grab 100 at a time (200 is max), we want replies and user deets
	 
	 
	$tweets = getTweets( dailyblank_option('twitteraccount'), 100,  array('exclude_replies'=>false, 'trim_user' => false ) );
	 	
	 	cogdogbug ( $tweets );
	 
		// set up an array to hold responses
		/*
		$new_responses = array();
		
		// walk through the tweets
		foreach($tweets as $tweet) {
		
				// array for hashtags
				$hashtags = extract_hashtags( $tweet['entities']['hashtags'] );
				
				// We want only replies with hashtags and URLs in 'em
				if ( $hashtags AND $tweet['entities']['urls']  ) {
				
					// check for hashtag match against our dailyblank base
					if ( dailyblank_tag_in_hashtags( $hashtags, dailyblank_option('basetag')  ) ) {

						// bingo we got a winner; 
						// form URL for the tweet
						$t_url = 'https://twitter.com/' . $tweet['user']['screen_name'] . '/status/' . $tweet['id_str'];
						// build data array for each response found
					
						$new_responses[] = array(
							'id_str' => $tweet['id_str'], // twitter id for tweet
							'tweeter' => $tweet['user']['screen_name'], // tweet author
							'text' => $tweet['text'], // da tweet
							'url' => $t_url, // full url
							'tstamp' => $tweet['created_at'], // timestamp
							'tags' => $hashtags // gimme tags
						);
					
					}
				}
		}
		
		
		add_dailyblank_responses( $new_responses );							
		*/					
							
							?>


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