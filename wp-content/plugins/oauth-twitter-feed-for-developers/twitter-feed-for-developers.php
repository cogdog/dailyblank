<?php
/*
Plugin Name: oAuth Twitter Feed for Developers
Description: Twitter API 1.1 compliant plugin that provides a function to get an array of tweets from the auth'd users Twitter feed for use in Daily Blank theme.
Version: 100.2.2.1daily
License: MIT
License URI: http://opensource.org/licenses/MIT
Author: Storm Consultancy (Liam Gladdy), mods by Alan Levine for Daily Blank
Author URI: http://www.stormconsultancy.co.uk
*/


require('StormTwitter.class.php');
require('twitter-feed-for-developers-settings.php');

/* implement getTweets */
function getTweets($username = false, $count = 20, $options = false) {

  $config['key'] = get_option('tdf_consumer_key');
  $config['secret'] = get_option('tdf_consumer_secret');
  $config['token'] = get_option('tdf_access_token');
  $config['token_secret'] = get_option('tdf_access_token_secret');
  $config['screenname'] = get_option('tdf_user_timeline');
  $config['cache_expire'] = intval(get_option('tdf_cache_expire'));
  if ($config['cache_expire'] < 1) $config['cache_expire'] = 3600;
  $config['directory'] = plugin_dir_path(__FILE__);
  
  $obj = new StormTwitter($config);
  $res = $obj->getTweets($username, $count, $options);
  update_option('tdf_last_error',$obj->st_last_error);
  return $res;
  
}

/* Empty the cache for forced re-fetches 
   added by @cogdog for Daily Blank site
*/
function cleanTweetCache() {
	$obj = new StormTwitter();
	$obj->cleanTweetCache();
}

/* We do not want this plugin to autoupdate
   from the repository (besides the change above to a big version number)
   h/t https://www.dessol.com/blog/disable-plugin-updates-specific-plugin-wordpress-3-8/
*/

/*
add_filter('site_transient_update_plugins', 'dailyblank_remove_update_nag');

function dailyblank_remove_update_nag( $value ) {
 unset( $value->response[ plugin_basename(__FILE__) ] );
 return $value;
}
*/