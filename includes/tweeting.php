<?php
# -----------------------------------------------------------------
# Twitter Stuff
# -----------------------------------------------------------------

function dailyblank_twitter_auth() {
	// Status check for the Oauth Twitter Feed for developers
	if ( function_exists('getTweets' ) ) {
		$fb_str = 'Twitter Oauth plugin <strong>is installed</strong>. ';

		// check for the custom function added for this theme.
		if ( ! (function_exists('cleanTweetCache' ) ) ) {
			$fb_str .=  'But this is not the special version required for the Daily Blank Theme. Please install the plugin version from the <a href="https://github.com/cogdog/dailyblank" target="_blank">Theme github repo</a>';
		}

		// ping twitter for current user name
		$test_tweets = getTweets( dailyblank_option('twitteraccount'), 1 );

		if ( sizeof($test_tweets) == 0  ){
			$fb_str = 'getTweets() returns null';
		} else {
			if ( $test_tweets["error"] ) {
				$fb_str .= 'Uh oh, we have a problem Houston accessing tweets from @' . dailyblank_option('twitteraccount') . ': ' .  $test_tweets["error"] . ' Maybe check the <a href="' . admin_url( 'admin.php?page=options-general.php?page=tdf_settings') .'">Twitter Oauth Settings</a>?'; ;
			} else {
				$fb_str .= 'Successful connection to collect tweets replied to <a href="http://twitter.com/' . dailyblank_option('twitteraccount') . '"  target="_blank">@' . dailyblank_option('twitteraccount') . '</a>. This site is ready to collect responses.';
			}
		}


		return ( $fb_str );
	} else {
		return ('Twitter Oauth plugin <strong>is NOT installed</strong>. To enable the twitter responder for this site, install the <a href="https://github.com/cogdog/dailyblank" target="_blank">oAuth Twitter Feed for Developers</a> (use the special version that comes with this theme, not the one from Wordpress). Check the documentation tab for information on how to configure the plugin.');
	}
}

function dailyblank_tag_in_hashtags( $hashtags, $basetag) {
// runs check on all input tags from twitter API ("#" not include) for match on the base tag
// pattern (e.g. if the base tag is "tdb" it looks for any tag like "tdb100" or "tdb45")

	// make sure the pattern to look for is lower case
	$matchtag = strtolower($basetag);

	foreach ($hashtags as $atag) {
		if ( strpos( strtolower($atag), $matchtag ) !== false ) return true;
	}

	return false;

}

function extract_hashtags ( $hashtags ) {
// yank out all hashtags from twitter api object array ( 'text'=> 'tag' , 'indices' = >array..)
// to simple array

	$taglist = array();

	// walk the twitter hash tag array, and pluck out the values for 'text'
	// set 'em all to lowercase
	foreach ( $hashtags as $tagdata ) {
		$taglist[] = strtolower( $tagdata['text'] );
	}

	return ( $taglist );
}

function dailyblank_twitter_button ( $postid ) {
	$dailyblank_tag = get_post_meta( $postid, 'dailyblank_tag', 1 );
	$tweet_text = '#' . $dailyblank_tag   . rawurlencode( ' ' . dailyblank_option('tweetstr') );

	echo '<p style="text-align:center;"><a href=\"https://twitter.com/intent/tweet?screen_name=' .  dailyblank_option('twitteraccount') . '&text=' . $tweet_text . ' class="twitter-mention-button" data-size="large">Tweet your response to ' .  dailyblank_option('twitteraccount'). "</a>\n
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></p>";

}


// custom action triggered by event for checking for new tweets
add_action( 'dailyblank_hello_twitter', 'dailyblank_get_tweets', 10, 0);

function dailyblank_get_tweets( $show_fb = false, $manual_mode = false ) {
	 // fetch the twitter account timeline for replies, grab 100 at a time (200 is max), we want replies and user deets

	 // exit stage left if we are in standby mode and not called by admin request
	 if ( dailyblank_option('standby') == 'on' AND !$manual_mode ) return;


	 $tweets = getTweets( dailyblank_option('twitteraccount'), 150,  array('exclude_replies'=>false, 'trim_user' => false ) );

		// set up an array to hold responses
		$new_responses = array();

		// walk through the tweets
		foreach($tweets as $tweet) {

				// array for hashtags
				$hashtags = extract_hashtags( $tweet['entities']['hashtags'] );

				// We want only replies with hashtags in 'em
				if ( $hashtags ) {

					// check for hashtag match against our dailyblank base
					if ( dailyblank_tag_in_hashtags( $hashtags, dailyblank_option('basetag')  ) ) {

						// bingo we got a winner;
						// form URL for the tweet
						$t_url = 'https://twitter.com/' . $tweet['user']['screen_name'] . '/status/' . $tweet['id_str'];
						// build data array for each response found

						$new_responses[] = array(
							'id_str' => $tweet['id_str'], // twitter id for tweet
							'tweeter' => $tweet['user']['screen_name'], // tweet author
							'full_text' => $tweet['full_text'], // da tweet
							'url' => $t_url, // full url
							'tstamp' => $tweet['created_at'], // timestamp
							'tags' => $hashtags // gimme tags
						);

					}
				}
		}

		$new_tweets = add_dailyblank_responses( $new_responses );

		// save a times stamp for when this happened
		update_option( "dailyblank_twitter_check", current_time( 'timestamp' ) );

		if ($show_fb) {
			echo 'Cowabunga! we managed to add <strong>' . $new_tweets . '</strong> fresh ones out of <strong>'  .  count( $new_responses  ) . '</strong> found tweets.';
		}
}

function add_dailyblank_responses( $responses ) {
/*
Utility to add new items to custom post types that represent tweeted responses. Input array includes
	'id_str' => twitter ID
	'url'	=> link to tweet
	'full_text' => text of the tweet
	'tweeter' => screen name of tweet author
	'tstamp' => time stamp converted to unix time
	'tags' => simple array of hash tags

*/

	$new_ones = 0;

	foreach ($responses as $tweet) {

		// Skip existing responses if they match by the slug (post_name) matching twitter ID
		if ( the_slug_exists( $tweet['id_str'] ) ) continue;

		 $new_ones++;

		// append the tweet username as a tag
		$tweet['tags'][] = '@' . $tweet['tweeter'];

		// Create post object
		$response_type = array(
			'post_type' 	=> 'response',
			'post_title'    => $tweet['full_text'], // use the entire tweet as title
			'post_name'		=> $tweet['id_str'], // use twitter id as slug
			'post_date_gmt' => date( 'Y-m-d H:i:s', strtotime( $tweet['tstamp'] ) ),
			'post_date'		=> iso8601_gmt_to_local( $tweet['tstamp'], $format = 'Y-m-d H:i:s'),
			'post_status'   => 'publish',
			'post_author'   => 1,
		);

		// Insert the new content type into  database, store it's ID
		$post_id = wp_insert_post( $response_type, $wp_error );

		// add tags to hashtags taxonomy
		wp_set_post_terms( $post_id, $tweet['tags'], 'hashtags' );

		// add custom field data for the tweet's URL
		update_post_meta($post_id, 'tweet_url', $tweet['url']);

		// add custom field data for the tweet's URL
		update_post_meta($post_id, 'tweet_by', $tweet['tweeter']);
	}

	return ($new_ones);
}


add_action( 'admin_post_seek_tweets', 'prefix_admin_seek_tweets' );


function prefix_admin_seek_tweets() {

	// clean the cache via the Twitter Oauth Developers plugin
	cleanTweetCache();

	// go get some tweets
	dailyblank_get_tweets(true, true);
}


function getResponseCount() {
	return wp_count_posts('response')->publish;
}

function is_twitter_name( $str ) {
	return ( $str[0] == '@' );
}

?>
