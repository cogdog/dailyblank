<p>Follow these steps, oh ye intrepid Daily ______ creator, and your site shall run like butter (no guarantee). If you have gotten this far, you have unraveled the theme files, and set them up to get this far. Proceed!</p>

<p>In running the site, you will want to generate a pre-write list of Daily Blanks, they are saved as scheduled posts that are automatically published the same time every day. The twitter account is checked for replies to the account you are using and also must contain the hashtag associated with the Daily Blank.</p>


<p>Information for the matching tweets are saved locally as "Responses" (a custom post type) and identified with a custom taxonomy (hashtags).</p>

<h2>Set Up a Twitter Account and Get the Keys</h2>
<p>You will need a new or existing twitter account that acts as the recipient of tweeted responses. It can be an account you use for other purposes. With that account log in to <a href="https://apps.twitter.com" target="_blank">Twitter Apps</a> to create a new one that will provide the necessary API keys. The access is okay with Read Only permissions.</p>

<p>Four of the keys are necessary to configure this site to be able to read the accounts mentions, two Application Keys and Two Access Tokens:</p>

<ul>
	<li>	Consumer Key (API Key)</li>
	<li>	Consumer Secret (API Secret)</li>
	<li>	Access Token</li>
	<li>	Access Token Secret</li>
</ul>

<h2>Configure the Twitter Oauth Plugin</h2>
<p>The Daily Blank uses Twitter Oauth For Developers plugin to access twitter and read mentions of your account; however you must use the modified version that comes with the Daily Blank theme, not the one from Wordpress.org. When the plugin is uploaded, look under Settings for <a href="<?php echo admin_url( 'admin.php?page=options-general.php?page=tdf_settings')?>" target="_blank">Twitter Feed Auth</a>.</p>

Enter your own credentials in the four fields (in general the default cache expiry of 3600, 1 hour, is fine for most sites):

<img width="600" height="208" alt="twitter-auth-settings" src="<?php echo get_stylesheet_directory_uri(); ?>/images/twitter-auth-settings.jpg">


<h2>Useful General Setup Items</h2>
<p>Time is important to the Daily, so make sure the <a href="<?php echo admin_url( 'admin.php?page=options-general.php')?>" target="_blank">Time Zone settings</a>  match the reference time you want your site to be publishing new items in.</p>

<p>Now is also a good time to <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=category')?>" target="_blank">create/customize the Default Category</a> (and optionally and other categories) you will want to use for your Daily Blank. The Default category will be applied to ever new Daily Blank, serving as a way to show a complete archive.</p>


<h2>Set Daily Blank Options</h2>
<p>Access the Options at any time from the <strong>Daily Blank Options</strong> link from the top admin bar or also under the <strong>Appearance</strong> menu. Here comes the explanation for all settings!</p>

<p><em>(The values in the fields come from the instance of this running as the <a href="https://udg.theagoraonline.net/daily" target="_blank">UdG Agora Daily Try site</a>.</em></p>

<img width="600" height="288" alt="general-settings-1" src="<?php echo get_stylesheet_directory_uri(); ?>/images/general-settings-1.jpg">

<ul>
<li><strong>Name For What is Done Daily</strong> (capitalized first letter) This should be the kind of daily activity, written as singular, and without "Daily" in front of it. The example shown is for the Daily Try, so each one is a "Try".</li>
<li><strong>Twitter Account</strong> This is the user name account you are using as a receiver of responses; enter it without the "@". The text below will provide an indicator if the Twitter Oauth plugin is installed and set up correctly.</li>
<li><strong>Default Tweet</strong> provides the basic language inserted for the twitter button. The tags will be added automatically, this is just the prompt for a default response.</li>
<li><strong>Base Name for Tags</strong> (lowercase) forms the unique hashtags tags applied to each item, in the example showsn the tags will be #agoratry1, #agoratry2, .... #agoratry11000, etc. A base tag is best if short but you may want to test of it is used elsewhere.</li>
<li><strong>Category for All Daily Blanks</strong> Set to the default category you edited above.</li>
<li><strong>Start Tag Numbers at</strong> in almost all cases this should be "1", but you might want to start your tags maybe a different number. Once the first Daily Blank is published, they will be increased sequentially.</li>
</ul>

<p>Following this is a media selector used to create a background image shown in the front page- the most recently published Daily Blank will be displayed in a translucent box overlay.</p>

<p>Then two more settings...</p>

<img width="600" height="195" alt="general-settings-2" src="<?php echo get_stylesheet_directory_uri(); ?>/images/general-settings-2.jpg">
<ul>
<li><strong>Number of Responses to Display at a Time</strong> For a new site, this can be ignored. If your Daily Blank site starts to get regularly more than 10 responses, you may want to consider installing the <a href="https://wordpress.org/plugins/ajax-load-more/" target="_blank">Ajax Load More plugin</a> (see below for configuration details). This allows the number specified by the value of the field to be loaded, and subsequent sets are appended by ajax.</li>
<li><strong>When to Publish</strong> is the local time (relevant to your site's time zone setting) when items are posted. Enter a value for hour and minute. A new item will be published if they have been pre-written (the site saves them as scheduled posts).</li>
</ul>

<p>Notice also the link for <strong>Look for Tweets</strong> -- the site is set up with its own timer to check twitter once an hour. This is driven by visits to the site; so use this link to force an instant check for new mentions to your account. This also clears the local cache.</p>

<h2>Using the Ajax Load More Plugin</h2>
<p>Install this plugin to create paginate loadings of twitter responses for busier sites (regularly receiving more than 10 responses). The only critical setting is to customize the <a href="<?php echo admin_url( 'admin.php?page=ajax-load-more-repeaters')?>" target="_blank">Repeater Template</a> so it embeds the tweets the same way the theme does:</p>

<img width="600" height="312" alt="ajax-load-more-templates" src="<?php echo get_stylesheet_directory_uri(); ?>/images/ajax-load-more-templates.jpg">

<p>Replace the default code with:</p>
<pre>
&lt;div&gt;
&lt;?php echo wp_oembed_get( get_post_meta( get_the_ID(), 'tweet_url', 1 ) );?&gt;					
&lt;/div&gt;
</pre>


<h2>Creating New Daily Blanks</h2>
<p>Write new items as normal posts (in the Dashboard they are referred to as "Daily Blanks"). Keep the title short enough to allow for the inserting of the incremental hashtag:</p>

<img width="600" height="327" alt="create-daily-blank" src="<?php echo get_stylesheet_directory_uri(); ?>/images/create-daily-blank.jpg">

<p>The box in the upper right should never need to be edited. It will let you know the next tag available that is used for your daily blank; in this case, "agoratry69" will be used internally as a tag, and the hashtag "#agoratry69" will be added to the title when saved.</p>

<p>Use the body of the post to add anything that might explain the item in more detail, including media.</p>

<p>Always <strong>Save Draft</strong> first. This does a number of things:</p>

<ul>
<li>The unique tag is added to the Daily Blank and inserted before the title as a twitter hashtag.</li>
<li>Instructions are appended to the Daily Blank indicating the name of the twitter account to reply to and the hashtag</li>
<li>The Daily Blank is scheduled to be published 24 hours after the most recently published or scheduled one-- the idea is to schedule these out days or weeks into the future so they are automatically published (check the first one published; a persistent bug is that the very first one on a site is somehow set to be January 1970! The schedule date can always be edited)</li>
</ul>

<p>Scheduled or published Daily Blanks can be edited like any other Wordpress post, at any time. Review the queued ones via <p>Submitted items end up as drafts on the site, which you can review via the <strong>Daily Blanks</strong> menu -&gt; <strong><a href="<?php echo admin_url( 'edit.php?post_status=future&post_type=post')?>" target="_blank">Scheduled Daily Blanks</a></strong>

<h2>Daily Blank Public Submission Form</h2>
<p>If you create a new Wordpress Page with a permalink of <strong>add</strong> it will be published as a form for your site visitors to suggest new Daily Blanks. Any text in the body of the page appears as a prompt.</p>

<p>Submitted items end up as drafts on the site, which you can review via the <strong>Daily Blanks</strong> menu -&gt; <strong><a href="<?php echo admin_url( 'edit.php?post_status=draft&post_type=post')?>" target="_blank">Submitted Daily Blanks</a></strong>. To activate one, simple edit it, save as draft, and it becomes the next one in line.</p>

<h2>Random Daily Blank</h2>
<p>Create a Wordpress page with a permalink of <strong>random</strong> (it needs no content); when opened, it rediects to a randomly chosen  published Daily Blank.</p>

<h2>All Responses</h2>
<p>Append <strong>/response/</strong> to your site's URL to see a display of all tweeted responses from the most recent listed first.</p>

<h2>Leaderboard Shortcode</h2>
<p>These codes can be used in posts or widgets to list the most active participants:</p>

<p>List all respondents in order of most active to least</p>
<pre>[dailyleaders]</pre>


<p>List the top 10 respondents</p>
<pre>[dailyleaders number="10"]</pre>

<p>List the top 10 respondents and exclude the ones identified in the hashtag taxonomy as ids 8 and 10</p>
<pre>[dailyleaders number="10" exclude="8,10"]</pre>

<p>List all the twitter names that have contributed new Daily Blanks via the submission form </p>
<pre>[dailyleaders type="contributors"]</pre>

<h2>Automatic Tweeting</h2>
<p>The site does NOT automatically tweet new Daily Blanks. Numerous plugins offer a capability to autotweet new posts; this is not built into the site.</p>

<p>The best tool for doing this we have found is to set up a routing on  <a href="https://dlvr.it/" target="_blank">dlvr.it</a> a free web service can be triggered to publish to a twitter account via an RSS feed from your site. Your mileage may/will vary. The examples below are for the <a href="http://daily.ds106.us/" target="_blank">DS106 Daily Create</a> which sends out its creative activities via <a href="https://twitter.com/ds106dc" target="_blank">@ds106dc</a>.</p>

<p>To use <a href="https://dlvr.it/" target="_blank">dlvr.it</a>, first create an account on the site. You will create a <strong>Route</strong> which has a source (in this case an RSS Feed) and a <strong>Destination</strong> (in this case a twitter account). <a href="https://app.dlvr.it/deliveries/routes" target="_blank">Go to the routes editor</a> and click <strong>+Add Route</strong>. 

<p>Under Sources on the left, click <strong>+add</strong>, then click the RSS icon (first on left), then the <strong>Connect Feed</strong> button. This opens the Source Feed Details tab. Enter the RSS feed for your site, which is the full URL of the site with <code>/feed</code> appended to it and give it a name.</p>


<img width="600" height="350" alt="Feed details" src="<?php echo get_stylesheet_directory_uri(); ?>/images/dlvrit-source-feed-details.jpg">

<p>On the next tab, <strong>Feed Update</strong> enter the following settings to make sure it does one per day.</p>

<img width="600" height="451" alt="Feed Update Settings" src="<?php echo get_stylesheet_directory_uri(); ?>/images/dlvrit-feed-update.jpg">

<p>And lastly, on the <strong>Item text tab</strong> you can insert any specific extra text to add to the tweets, e.g. hashtags for all tweets.</p>

<img width="600" height="487" alt="Extra Item Text for Feeds" src="<?php echo get_stylesheet_directory_uri(); ?>/images/dlvrit-item-text.jpg">

<p>No other settings are required for the feed. Now on the right side, under destinations, click <strong>+add</strong>.  Click the twitter icon, and then authenticate with the account you want to be sending out the tweets.</p>

<p>Under the <strong>Post Content</strong> tab, we suggest tweeting the title and the link:</p>

<img width="600" height="515" alt="dlvrit-twitter-destination-account" src="<?php echo get_stylesheet_directory_uri(); ?>/images/dlvrit-twitter-destination-account.jpg">

<p>This is the only setting you should need to make. Your twitter account should automatically send out a tweet in an hour after your next scheduled Daily Blank is published.</p>










