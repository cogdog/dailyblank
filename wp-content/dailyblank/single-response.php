<?php

/*	Redirection to the source of a tweeted response */

 if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php 
		
		// get the tweet link from meta data 
		if ( get_post_meta($post->ID, 'tweet_url', true) ) {
			$tweetlink =  get_post_meta($post->ID, 'tweet_url', $single = true); 
		}

	?>
<?php endwhile; endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>connecting to tweet</title>
	<meta http-equiv="refresh" content="0;url=<?php echo $tweetlink?>">
</head>
<body>

</body>
</html>

