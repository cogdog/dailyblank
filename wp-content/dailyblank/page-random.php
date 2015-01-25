<?php

/*

Random Daily Post Picker
Send viewer to random post optionally

*/


// load wordpress functionality so we can do stuff
require( 'wp-load.php' );

// set arguments for WP_Query on published posts to get 1 at random
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'rand'
);

// It's time! Go get a random post from the database
$my_random_post = new WP_Query ( $args );

while ( $my_random_post->have_posts () ) {
  $my_random_post->the_post ();
  
  // get permalink
  $gotolink = get_permalink();

}	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>connecting to a random daily</title>
	<meta http-equiv="refresh" content="0;url=<?php echo $gotolink?>">
</head>
<body>

</body>
</html>

