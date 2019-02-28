# The Daily Blank Wordpress Theme
by Alan Levine http://cog.dog/ or http://cogdogblog.com/

-----
*If this kind of stuff has any value to you, please consider supporting me so I can do more!*

[![Support me on Patreon](http://cogdog.github.io/images/badge-patreon.png)](https://patreon.com/cogdog) [![Support me on via PayPal](http://cogdog.github.io/images/badge-paypal.png)](https://paypal.me/cogdog)

----- 

![](images/dailyblank-example.jpg "Example Daily Blank")

## What is this?
A Wordpress Theme for a site that works like the [ds106 Daily Create](http://tdc.ds106.us) Unlike thay original TDC where responses needed to be posted to social media sites, for the Daily Blank participation is via sending a tweet to a dedicated account with a specific hashtag for each day's challenge. 

See the [DS106 Daily Create #TDC1667](http://daily.ds106.us/tdc1667/) shown in the image above.

## These Sites Are Doing It Daily

* [The (new) DS106 Daily Create](http://daily.ds106.us/) DS106 spawn of the [original TDC](http://tdc.ds106.us)
* [Daily Doodle](http://edmedia.tlc.sfu.ca/dailyblank/) Simon Fraser University
* [Daily Digital Alchemy](http://daily.arganee.world) for the Networked Narratives open course
* [Daily Extend](https://extend-daily.ecampusontario.ca/) for the eCampus Ontario Expanding Capacity project
* [FLN Book Club Slow Chat](https://bookclub.flippedlearning.org/slowchat/) online book reading club for flipped learning educators
* [The Daily Opener](https://muraludg.org/daily) Mural UDG Project at the University of Guadalajara 
* [The Daily Stillness](http://daily.stillweb.org/)
* [UdG Agora Daily Try](http://udg.theagoraonline.net/daily)
* [Una foto cada día](http://daily.inf115.com/) Daily Photo Challenges for inf115
* [The Daily Headline](http://jmc3353.adamcroom.com/dailyheadline/) University of Oklahoma
* [The Daily Blank (first prototype as a SPLOT)](http://splot.ca/dailyblank) (not active)
* [The You Show Daily](http://thedaily.trubox.ca/)  (not active)


You can also find some long-winded code laden blog posts on this theme's development http://cogdogblog.com/tag/dailyblank/


## New Features

### v1.2

* Daily too much to handle? A new theme option feature lets you schedule your blanks to be every 2,3,4,5,6 days, or every 1 or 2 weeks
* For only reasons no one else cares about, the long functions.php file is now broken up into smaller bit size includes.

### v1.1
* New admin option to add any text to the title of all published dailies, primarily if the site needs to publish with some other hash tag, and it's easier if these are in the title
* Reorganized repo to be the theme only, moved the parent theme and twitter auth plugin to separate repos

### v1.0
* Theme options to trigger email notification when scheduled supply is below  selected threshold; also a standby mode to pause hourly twitter API checks


### v0.9

* CSS support for [User Interface Options plugin](https://github.com/fluid-project/uio-wordpress-plugin)

### older 

* New options for leaderboard shortcode to restrict to tweets after a specified date
* Post Author is never used (the theme automatically assigns author to user id=0 which is typoically the first admin user) so Author column now removed from dashboard listings of Daily Blanks
* Fixed a bug in the twitter API calls that was resulting in missing tweets (added `tweet_mode=extended`) because API was returning truncated tweets, hence missing hashtags. This can be fixed by updated the plugin [installable-oauth-twitter-feed-for-developers.zip](https://github.com/cogdog/dailyblank/blob/master/installable-oauth-twitter-feed-for-developers.zip) 
* Categories listed under singly daily metadata (July 28, 2017)
* New shortcodes for count of people participating and number of responses. Also added an admin dashboard widget that shows activity stats on the site (July 3, 2017)
* Set up installable zips for both parent and child theme, and the customized plugin (August 3, 2016)
* Updated display of admin options to actually use its busted tabs (July 26, 2016)
* Response and visit counts tracked and displayed, shortcodes to display in pages/widgets (May 23, 2016)

## To Be Done (one day)
* Move theme options to Customizer
* Since the parent theme is no longer updated, merge them together to work as single theme (allowing options for your own child theme overrides)


## How to Manually Install The Daily Blank Theme

I will make the big leap of assumption that you have a self-hosted Wordpress site and that you can install themes by uploading files.

1. The Daily Blank is a child theme based on [Wordpress Bootstrap](https://github.com/320press/wordpress-bootstrap) (sadly no longer maintained, but it works solidly). This theme can be [downloaded as a zip from its repo GitHub](https://github.com/320press/wordpress-bootstrap), then installed into wordpress by uploaded the zip file as a new theme.

2. In addition, you must upload a modified version of the Twitter Oauth For Developers plugin; it has modifications critical to make this site work (it looks for mentions rather than timelines, and has a custom function for clearing the cache).  I have moved this plugin to [it's own Github repository](https://github.com/cogdog/oath-twitter-splot), where you should download it as a ZIP file. From the plugins area of your Wordpress dashboard, click the **Upload Plugin** button and select that zip file to upload, and activate the plugin.

3. Finally, download the ZIP file for the Daily Blank Theme (click the green **Clone of Download** button and chose the ZIP option.  From the themes area of your Wordpress dashboard, click the **Upload Theme** button,  then select this zip file to upload, and finally  activate the theme.

## WP Pusher Install Option

If all this downloading and uploading is making you dizzy, we are recommending the [WP Pusher plugin](https://wppusher.com/) which makes it easier to install themes and plugins that are published in GitHub. You will need to have or create an account on [GitHub](https://github.com) (free).

You will have to [download this plugin](https://wppusher.com/download) as a ZIP file. From the plugins area of your Wordpress dashboard, click the **Upload Plugin** button, select that zip file to upload, and activate the plugin.

First click the **WP Pusher** option in your Wordpress Dashboard, and then click the **GitHub** tab. Next click the **Obtain a GitHub Token** button to get an authentication token. Copy the one that is generated, paste into the field for it, and finally, click **Save GitHub Token**.

For the first theme this site needs, click the option under **WP Pusher** for **Install Theme**. In the form that appears, for Theme Repository, enter `320press/wordpress-bootstrap` for the WP-Bootstrap theme, then click **Install Theme**.

Next return to  the option under **WP Pusher** for **Install Theme**. Now, for Theme Repository, enter `cogdog/dailyblank` for theDaily Blank theme, also check  the option for **Push-to-Deploy** (this will automatically update your site when the theme is updated, and once more  click **Install Theme**.

Finally, click the option under **WP Pusher** for **Install Plugin**. For the field labeled **Plugin Repository** enter `cogdog/oath-twitter-splot`, then click **Install Plugin**.



###  Documentation

Follow these steps, oh ye intrepid Daily ______ creator, and your site shall run like butter (no guarantee). 

In running the site, you will want to generate a pre-written list of Daily Blanks, they are saved as scheduled posts that are automatically published the same time every day. The twitter account is checked for replies to the account you are using and also must contain the hashtag associated with the Daily Blank.

Information for the matching tweets are saved locally as "Responses" (a custom post type) and identified with a custom taxonomy (hashtags).

#### Set Up a Twitter Account and Get the Keys
You will need a new or existing twitter account that acts as the recipient of tweeted responses. It can be an account you use for other purposes. With that account log in to [Twitter Apps](https://apps.twitter.com) to create a new one that will provide the necessary API keys. The access is okay with Read Only permissions.

Four of the keys are necessary to configure this site to be able to read the accounts mentions, two Application Keys and Two Access Tokens:


*	Consumer Key (API Key)
*	Consumer Secret (API Secret)
*	Access Token
*	Access Token Secret


#### Configure the Twitter Oauth Plugin
The Daily Blank uses Twitter Oauth For Developers plugin to access twitter and read mentions of your account; however you must use the [modified version that comes with the Daily Blank theme]((https://github.com/cogdog/dailyblank/blob/master/installable-oauth-twitter-feed-for-developers.zip) 
), not the one from Wordpress.org. When the plugin is uploaded, look under Settings for `Twitter Feed Auth`.

Enter your own credentials in the four fields (in general the default cache expiry of 3600, 1 hour, is fine for most sites):

![](images/twitter-auth-settings.jpg "Twitter Oauth Settings")

#### Useful General Setup Items
Time is important to the Daily, so make sure the `Time Zone settings`  match the reference time you want your site to be publishing new items in.

Now is also a good time to `create/customize the Default Category` (and optionally and other categories) you will want to use for your Daily Blank. The Default category will be applied to ever new Daily Blank, serving as a way to show a complete archive.

It's best to set your `Permalink` structure  (under `Settings`) to be Custom (the last option) and use for the setting field

    /%postname%/

#### Set Daily Blank Options
Access the Options at any time from the `Daily Blank Options` link from the top admin bar or also under the `Appearance` menu. Here comes the explanation for all settings!

*(The values in the fields come from the instance of this running as the [UdG Agora Daily Try site](http://udg.theagoraonline.net/daily)*

![](images/general-settings-1.jpg "First Part General Settings")

* **Name For What is Done Daily** (capitalized first letter) This should be the kind of daily activity, written as singular, and without "Daily" in front of it. The example shown is for the Daily Try, so each one is a "Try".
* **Twitter Account** This is the user name account you are using as a receiver of responses; enter it without the "@". The text below will provide an indicator if the Twitter Oauth plugin is installed and set up correctly.
* **Add to Tweets** anything else that should be added to the post title so it gets included when auto tweeted (e.g. a hashtag)
* **Default Tweet** provides the basic language inserted for the twitter button. The tags will be added automatically, this is just the prompt for a default response.
* **Base Name for Tags** (lowercase) forms the unique hashtags tags applied to each item, in the example showsn the tags will be #agoratry1, #agoratry2, .... #agoratry11000, etc. A base tag is best if short but you may want to test of it is used elsewhere.
* **Category for All Daily Blanks** Set to the default category you edited above.
* **Start Tag Numbers at** in almost all cases this should be "1", but you might want to start your tags maybe a different number. Once the first Daily Blank is published, they will be increased sequentially.


Following this is a media selector used to create a background image shown in the front page- the most recently published Daily Blank will be displayed in a translucent box overlay.

Then two more settings...

![](images/general-settings-2.jpg "Second Part General Settings")


* **Number of Responses to Display at a Time** For a new site, this can be ignored. If your Daily Blank site starts to get regularly more than 10 responses, you may want to consider installing the [Ajax Load More plugin](https://wordpress.org/plugins/ajax-load-more/) (see below for configuration details). This allows the number specified by the value of the field to be loaded, and subsequent sets are appended by ajax.
* **When to Publish** is the local time (relevant to your site's time zone setting) when items are posted. Enter a value for hour and minute. A new item will be published if they have been pre-written (the site saves them as scheduled posts).

And a new feature allows you to schedule less frequently than every day, it can push out the scheduling to every 2,3,4,5, or 6 days, or even to every 1 or 2 weeks (this is how much later a new Daily Blank is set to publish after the last scheduled one).

![](images/frequency.jpg "Adjust frequency of scheduling")


## Look For Tweets
This section provides a status and it's twitter checking activity. Under regular use, a daily blank site will check the Twitter API once an hour to look for matching tweets. If your site is no longer publishing new challenges, you can put it in "Standby' mode. This turns off regular hourly checking of twitter, but the button allows for a manual check.

![](images/standby-mode-on.jpg "Standby Mode")

When not in standby mode, you will see an indicator that it is checking every hour, as well as the date of the last date/time it checked twitter (date and time are relevant to the local site options for time zone).

In addition, you can now select a value (1-7) of when to send an email notification when the supply of scheduled dailies falls below a given threshold.

![](images/notify-supply.jpg "Low Supply warning setting")




#### Using the Ajax Load More Plugin
Install this plugin to create paginate loadings of twitter responses for busier sites (regularly receiving more than 10 responses). The only critical setting is to customize the `Repeater Template` so it embeds the tweets the same way the theme does:

![](images/ajax-load-more-templates.jpg "Ajax Load More Template Settings")


Replace the default code with:

	<div>
	<?php echo wp_oembed_get( get_post_meta( get_the_ID(), 'tweet_url', 1 ) );?>					
	</div>


#### Creating Daily Blanks

You can add to your queue of dailies several ways:

* Create a new one from scratch
* Editing one submitted via your public form
* Recycling previously published ones

Write new dailies as normal posts (in the Dashboard they are referred to as "Daily Blanks"). Keep the title short enough to allow for the inserting of the incremental hashtag:

![](images/create-daily-blank.jpg "Creating a Daily Blank")


The box in the upper right should never need to be edited. It will let you know the next tag available that is used for your daily blank; in this case, "agoratry69" will be used internally as a tag, and the hashtag "#agoratry69" will be added to the title when saved.

Use the body of the post to add anything that might explain the item in more detail, including media.

Always *Save Draft* first. This does a number of things:

* The unique tag is added to the Daily Blank and inserted before the title as a twitter hashtag.
* Instructions are appended to the Daily Blank indicating the name of the twitter account to reply to and the hashtag
* The Daily Blank is scheduled to be published 24 hours after the most recently published or scheduled one-- the idea is to schedule these out days or weeks into the future so they are automatically published (check the first one published; a persistent bug is that the very first one on a site is somehow set to be January 1970! The schedule date can always be edited)

Scheduled or published Daily Blanks can be edited like any other Wordpress post, at any time. Review the queued ones via Submitted items end up as drafts on the site, which you can review via the `Daily Blanks` menu -&gt; `Scheduled Daily Blanks`

![](images/submitted-daily-blanks.jpg "Find submitted Daily Blanks")

Daily blanks added via your site's submissions end up as Wordpress Drafts on your site, which you can review via the `Daily Blanks` menu -&gt; `Submitted Daily Blanks`. To activate one, simple edit it, save as draft, and it becomes the next one in line.

And finally recycle your past gems! In the dashboard, when viewing a published Daily Blank, look for the recycle link when you hover on a title.

![](images/recycle-link.jpg "Recycle link")

Or, when viewing any published daily, look on the Admin Menu under **Edit Daily Blank** for a **Recycle This Daily Blank** item.

Either way, this will create a new draft for a Daily Blank copying the content, but with new tags. You can modify the content, save, and get it in the queue.  When published a new daily create recycled from a previous one will include a reference to it's source (see the credit on the [DS106 TDC2031](http://daily.ds106.us/tdc2031/) which was recycled from [TDC 1578](http://daily.ds106.us/tdc1578/):

![](images/recycled.jpg "Recycled credit link")


#### Attributing Daily Blanks

If an item is published after being submitted through the add form, it will already be setup with all the data to credit the source. To add to edit the person a Daily Blank is attributed to, click **Screen Options** in the top right of the Wordpress dashboard and make sure `Custom Fields` is checked.

The credit is given by twitter handle in the wAuthor custom field (with a "@"). If not present, select `wAuthor` from the bottom menu, and enter the twitter handle in the value field:

![](images/author-fields.jpg "Editing the Author credits in a Daily Blank")

Add the twitter name too as a tag; this will make sure they get credit on the leaderboard.

#### Adding / Editing Responses

The tweets found as replies to the designated twitter account and having at least one hashtag are stored on the site as a Custom Post type called `Responses`. To examine all fields as shown below, click **Screen Options** in the top right of the Wordpress dashboard and make sure `Custom Fields` is checked.

![](images/response-parts.jpg "Parts of a Daily Blank Response")	

In some cases a person correctly responded to the correct account, but forgot to provide the hashtag. This is fixed by editing the response, and adding it to the Hashtags taxonomy (without the `#`):


![](images/add-tag.jpg "Add missing hashtag")	

If a tweet intended for the site never appears (likely missing the correct mention), you can create  anew response, and edit all info as shown above.
	
#### Daily Blank Public Submission Form
If you create a new Wordpress Page with a permalink of `add` it will be published as a form for your site visitors to suggest new Daily Blanks. Any text in the body of this Wordpress Page appears as a prompt for people (see the one for the [DS106 Daily Create](http://daily.ds106.us/add/)).

#### Random Daily Blank
Create a Wordpress page with a permalink of *random* (it needs no content); when opened, it rediects to a randomly chosen  published Daily Blank.

#### All Responses
Append `/response/` to your site's URL to see a display of all tweeted responses from the most recent listed first.

#### Total Shortcodes
These are available to use in any Wordpress post, page, widget.

This will generate the total number of Daily Blanks published to date:

	[dailycount]

And this will generate the total of responses Daily Blanks received to date:

	[responsecount]
	
This one shows the unique number of people who responded

	[peoplecount]

#### Admin Dashboard widget

This gives you a top glance at the activity on your site from the entry to the Admin Dashboard, with links to edit each kind. It will be added to the bottom of the admin dashboard but you can drag it higher in the dashboard. This screenshot is from the [DS106 Daily Create](http://daily.ds106.us/) -- someone needs to go through those submitted items!

![](images/admin-widget.jpg "Admin Dashboard Widget")

#### Leaderboard Shortcode
These codes can be used in posts or widgets to list the most active participants. 

List all respondents in order of most active to least, for all time

	[dailyleaders]
	
The default display is a plain list. To use CSS to style the list with a color bar to represent the individual count as a percentage of all dailies.

![](images/leaderboard-bars.jpg "Leaderboard with bar styles")

	[dailyleaders showbars="1" barstyle="1"]

*  barstyle="1" orange
*  barstyle="2" green
*  barstyle="3" red

List the top 10 respondents

	[dailyleaders number="10" showbars="1" barstyle="2"]

List the top 10 respondents and exclude the ones identified in the hashtag taxonomy as ids 8 and 10

	[dailyleaders number="10" exclude="8,10" showbars="1" barstyle="2"]
	
List all respondents since a given date (any date expression should work, most reliable is `YYYY-MM-DD`).

	[dailyleaders number="10" since="2017-07-01" showbars="1" barstyle="2"]
	
Special value to list all submitted since the beginning of the year

	[dailyleaders number="10" since="year" showbars="1" barstyle="2"]
	
List all the twitter names that have contributed new Daily Blanks via the submission form 

	[dailyleaders type="contributors" showbars="0"]


#### Top Dailies Shortcode
New code was added (May 23, 2016) to add to each daily meta data that records (and updates) the number of responses and views for each one.  And with that, a new shortcode that you can use to display the most "popular" ones (most responses and/or most views) in a page or a widget. See the Debug Page hacks below for a method to back fill existing items for sites that existed before this featured was added 

List the 10 dailies that have the most responses

	[topdaily]

or specify the number you would like to list

	[topdaily number="20"]
	
Suppress the display of the count

	[topdaily showcount="0"]
	
Suppress the display of the date of each

	[topdaily showdate="0"]
	
List the ones that have the most views (and also add the parameters above):

	[topdaily type="visits"]




#### Automatic Tweeting With dlvr.it

** Note: As of November 6, 2018, we no longer recommend using this service. They have continued to diminish functionalty of the free service in attempts to upsell to a paid one **

The site does NOT automatically tweet new Daily Blanks. Numerous plugins offer a capability to autotweet new posts; this is not built into the site.

One tool for doing this we have found is to set up a routing on  [dlvr.it](https://dlvr.it/) a free web service can be triggered to publish to a twitter account via an RSS feed from your site. Your mileage may/will vary. The examples below were for the [DS106 Daily Create](http://daily.ds106.us/)  which sends out its creative activities via [@ds106dc](https://twitter.com/ds106dc).

To use dlvr.it, first create an account on the site. You will create a *Route* which has a source (in this case an RSS Feed) and a *Destination* (in this case a twitter account).  [Go to the routes editor](https://app.dlvr.it/deliveries/routes) and click *+Add Route*. 

Under Sources on the left, click *+add*, then click the RSS icon (first on left), then the *Connect Feed* button. This opens the Source Feed Details tab. Enter the RSS feed for your site, which is the full URL of the site with <code>/feed</code> appended to it and give it a name.

![](images/dlvrit-source-feed-details.jpg "Feed details")


On the next tab, *Feed Update* enter the following settings to make sure it does one per day.

![](images/dlvrit-feed-update.jpg "Feed Update Settings")

Note: previously you could add a hashtag to all tweets, but the Twitter API took this away. You can now add them as a Theme Option of the Daily Blank (this adds the tag to each item's titlw).

No other settings are required for the feed. Now on the right side, under destinations, click *+add*.  Click the twitter icon, and then authenticate with the account you want to be sending out the tweets.

Under the *Post Content* tab, we suggest tweeting the title and the link:

![](images/dlvrit-twitter-destination-account.jpg "Twitter Destination Account")

This is the only setting you should need to make. Your twitter account should automatically send out a tweet in an hour after your next scheduled Daily Blank is published.

#### Automatic Tweeting With JetPack

The [Publicize module of the JetPack plugin](https://jetpack.com/support/publicize/) is currently managing the tweeting of scheduled items for the [DS106 Daily Create](http://daily.ds106.us/). When adding the twitter account to your Social Connections, be sure to enable the option to Allow All Users to use the connection.

#### Automatic Tweeting with IFTTT

As an alternative, you can [try a recipe we made in IFTTT](https://ifttt.com/recipes/413425-tweeting-daily-blank-challenges) where you can enter your site's RSS feed, any hashtags you want to use. You wiull have to use an IFTTT account authorized to post to the twitter account you want to be tweeting for you.

#### Extras / Addons

This theme's stylesheet has been set up to work with the Fluid Project [User Interface Options Wordpress plugin](https://github.com/fluid-project/uio-wordpress-plugin) which adds a series of accessibility options to a site. This are only made available when the plugin is activated.

![](images/daily-ui-options.gif "UI Options features")

#### Debugging, One Time Code Fixes
Most sites will never need this, but in adding features to new sites, sometimes the site may need a one time nudge to update itself, or to do some debugging. I often use this for testing new features. The template `page-debug.php` does absolutely nothing. But if you need to do any of these tasks, get the code listed in the gists below, and insert into the noted places. Upload to your server. Create a page called "Debug" (it needs no content, just a slug of debug. Then go to the URL: http://mydailyblanksite.something/debug and let the script do its thing. When done, remove the code or the template from the theme.

Existing code bits. I wish I thought of doing this earlier, I lost a few handy ones. Oh well...

* Just see what tweets are fetched (used this when first building the site, a way to check if the code is talking to the twitter API [dailyblank-get-tweets gist](https://gist.github.com/cogdog/2934cddc5e1f4f446ff84a44618fff82)
* A fix for an issue (pre February 2016) where the tags for people who added Daily Blanks were not properly tagged in each post [dailyblank-author-tags gist](https://gist.github.com/cogdog/cf75335cc5d217e1f39382c756c97091)
* Seed the response counts and generate some random visit counts (for sites before this feature was added, May 23, 2016) [dailyblank-seed-counts gist](https://gist.github.com/cogdog/35d2b8c377b568bd7b161316828f89f4)


