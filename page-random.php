<?php
/*
Template Name: Random Daily Picker

Send viewer to random post optionally, with option within category

*/

// set arguments for WP_Query on published posts to get 1 at random
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby' => 'rand'
);

// check for a category slud passes
$incat =  (isset($wp_query->query_vars['c'])) ? $wp_query->query_vars['c'] : false;

// set argument to limit to category
if ($incat) $args['category_name'] == $incat;

// run query run
$my_random_post = new WP_Query ( $args );

while ( $my_random_post->have_posts () ) {
  $my_random_post->the_post ();
  
  // It's time! Go someplace random, have a great time
  wp_redirect ( get_permalink () );
  exit;
}
?>