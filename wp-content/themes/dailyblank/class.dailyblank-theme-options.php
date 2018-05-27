<?php
// manages all of the theme options
// heavy lifting via http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
// Revision July 26, 2016 as jQuery update killed TAB UI

class dailyblank_Theme_Options {

	/* Array of sections for the theme options page */
	private $sections;
	private $checkboxes;
	private $settings;

	/* Initialize */
	function __construct() {

		// This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		
		//$this->bank106_init();
		$this->get_settings();
		
		$this->sections['general'] = __( 'General Settings' );
		$this->sections['reset']   = __( 'Reset Options to Defaults' );

		// create a colllection of callbacks for each section heading
		foreach ( $this->sections as $slug => $title ) {
			$this->section_callbacks[$slug] = 'display_' . $slug;
		}

		// enqueue scripts for media uploader
        add_action( 'admin_enqueue_scripts', 'dailyblank_enqueue_options_scripts' );
		
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'dailyblank_options' ) )
			$this->initialize_settings();
	}

	/* Add page(s) to the admin menu */
	public function add_pages() {
		$admin_page = add_theme_page( 'Daily Blank Options', 'Daily Blank Options', 'manage_options', 'dailyblank-options', array( &$this, 'display_page' ) );
		
		// documents page, but don't add to menu		
		$docs_page = add_theme_page( 'Daily Blank Documentation', '', 'manage_options', 'dailyblank-docs', array( &$this, 'display_docs' ) );
		
	}

	/* HTML to display the theme options page */
	public function display_page() {
		echo '<div class="wrap">
			  <h1>Daily Blank Options</h1>';
		
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {
			echo '<div class="notice notice-success"><p>' . __( 'Theme options updated.' ) . '</p></div>';
		}
		
		echo '<form action="options.php" method="post" enctype="multipart/form-data">';
			
		settings_fields( 'dailyblank_options' );
		
		
		echo  '<h2 class="nav-tab-wrapper"><a class="nav-tab nav-tab-active" href="?page=ds106bank-options">Settings</a>
		<a class="nav-tab" href="?page=dailyblank-docs">Documentation</a></h2>';

		do_settings_sections( $_GET['page'] );
		
			echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>

		</div>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});
			
			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});
			
			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
		});
		</script>';	
	}
			
	/*  display documentation in a tab */
	public function display_docs() {	
		// This displays on the "Documentation" tab. 
		
	 	echo '<div class="wrap">
		<h1>Daily Blank Documentation</h1>
		<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="?page=dailyblank-options">Settings</a>
		<a class="nav-tab nav-tab-active" href="?page=dailyblank-docs">Documentation</a></h2>';
		
		include( get_stylesheet_directory() . '/includes/dailyblank-theme-options-docs.php');
		
		echo '</div>';		
	}




	/* Define all settings and their defaults */
	public function get_settings() {
	
		/* General Settings
		===========================================*/


		$this->settings['dailykind'] = array(
			'title'   => __( 'Name For What is Done Daily' ),
			'desc'    => __( 'What is the thing you are asking people to do? e.g. "Create", "Challenge" (omit "The Daily" we will add that for you)' ),
			'std'     => 'Blank',
			'type'    => 'text',
			'section' => 'general'
		);

	
		$this->settings['twitteraccount'] = array(
			'title'   => __( 'Twitter Account' ),
			'desc'    => __( 'User name (without the @) that replies will be sent to.' . dailyblank_twitter_auth() ),
			'std'     => 'dailyblank',
			'type'    => 'text',
			'section' => 'general'
		);
		
		$this->settings['tweetstr'] = array(
			'title'   => __( 'Default Tweet' ),
			'desc'    => __( 'Default text for twitter button (hash tag will be added)' ),
			'std'     => 'My response for today\'s Daily Blank is (add your URL) ',
			'type'    => 'text',
			'section' => 'general'
		);
		
		$this->settings['basetag'] = array(
			'section' => 'general',
			'title'   => __( 'Base Name for Tags' ),
			'desc'    => __( 'Used for identifying each challenge like \'daily112\' as tags on this site and hashtags in twitter, so keep it short (do not include #). Keep them lower case (or we will lower it for you)' ),
			'std'     => 'td',
			'type'    => 'text',
			'section' => 'general'
		);	
		
  		// Build array to hold options for select, an array of post categories
		// Walk those cats, store as array index=ID 
	  	$all_cats = get_categories('hide_empty=0'); 
		foreach ( $all_cats as $item ) {
  			$cat_options[$item->term_id] =  $item->name;
  		}
 
		$this->settings['all_cat'] = array(
			'section' => 'general',
			'title'   => __( 'Category for All Daily Blanks'),
			'desc'    => 'Choose a category to apply to all Daily Blanks (so you can have a category archive)',
			'type'    => 'select',
			'std'     => 0,
			'choices' => $cat_options
		);	
 
		$this->settings['startnum'] = array(
			'section' => 'general',
			'title'   => __( 'Start Tag Numbers at' ),
			'desc'    => __( 'Start daily tags at number? This wil only be put into use the first time you create a Daily item.' ),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'general'
		);	
		
		$this->settings['frontimg'] = array(
			'title'   => __( 'Front Image' ),
			'desc'    => __( 'Used on home page as background for listing of recent item. Upload an image at least 640 x 311' ),
			'std'     => '0',
			'type'    => 'medialoader',
			'section' => 'general'
		);

		$this->settings['tweetperview'] = array(
			'section' => 'general',
			'title'   => __( 'Number of Responses to Display at a Time' ),
			'desc'    => __( 'How many to show per click of \'More\' button. ' . dailyblank_alm_installed() ),
			'std'     => '8',
			'type'    => 'text',
			'section' => 'general'
		);	
			

		$this->settings['dailytime'] = array(
			'section' => 'general',
			'title'   => __( 'When to Publish' ),
			'desc'    => __( 'Enter the time of day (hours and minutes) to publish a new item. You can enter \'07:30\' or \'7:30am\' for each day at 7:30. Note that this is relevant to your <a href="' . admin_url( 'admin.php?page=options-general.php')  . '">Wordpress timezone settings</a>. According to your current timezone settings, the time now is <strong>' . current_time('M d Y h:m:a') . '</strong>' ),
			'std'     => '08:00',
			'type'    => 'text',
			'section' => 'general'
		);	


		$standby_status = ( dailyblank_option('standby') == 'on') ? '<span style="color:red">Tweet Checking Paused</span>' : 'Checking tweets every hour';
		
		// look for date of last twitter check
		$last_twitter_check =  get_option( 'dailyblank_twitter_check' );
		
		
		
		
	
		$twitter_stamp = ( $last_twitter_check ) ? 'Twitter last checked for tweets ' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) , $last_twitter_check ) . '. ' : 'Twitter has not been checked yet for tweets to count. ';

		// ------- give some button power	
		$this->settings['seeker'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Seek Some Tweets',
			'std'    => '<strong>STATUS:</strong> ' . $standby_status . '<br /><strong>LAST CHECK:</strong>' .  $twitter_stamp . '<br /><a href="' . admin_url('admin-post.php?action=seek_tweets') . '" target="_blank" class="button-secondary">Look for tweets</a>',
			'type'    => 'heading'
		);
		
		$this->settings['standby'] = array(
			'section' => 'general',
			'title'   => __( 'Standby Mode' ),
			'desc'    => __( 'For an inactive or paused site turning this ON stops it from checking for tweets every hour. It can still be checked manually using the button above' ),
			'type'    => 'radio',
			'std'     => 'off',
			'choices' => array(
				'off' => 'Off',
				'on' => 'On',
			)
		);	

		$this->settings['supply'] = array(
			'section' => 'general',
			'title'   => __( 'Low Daily Supply Warning Level'),
			'desc'    => __( 'Send an email notification to addresses below when the supply of dailies reaches this level'),
			'type'    => 'select',
			'std'     => 0,
			'choices' => array(
				'0' => 'do not notify',
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',	
				'5' => '5',	
				'6' => '6',
				'7' => '7'	
			)
		);	
		
		$this->settings['notify'] = array(
			'title'   => __( 'Notification Emails' ),
			'desc'    => __( 'Send notifications to these addresses when the supply reaches the levels above. Separate multiple address with commas.' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);
		
		
		/* Reset
		===========================================*/
		
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset Options' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset bank options to their defaults.' )
		);

		
	}
	
	public function display_general() {
		// section heading for general setttings
	
		echo '<p>These settings manaage the behavior and appearance of your Daily Blank site. There are quite a few of them!</p>';		
	}


	public function display_reset() {
		// section heading for reset section setttings
	}
	
	/* HTML output for individual settings */
	public function display_setting( $args = array() ) {

		extract( $args );

		$options = get_option( 'dailyblank_options' );

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$options['new_types'] = 'New Type Name'; // always reset
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
			
			
		switch ( $type ) {
		
			case 'heading':
				echo '<tr><td colspan="2" class="alternate"><h3>' . $desc . '</h3><p>' . $std . '</p></td></tr>';
				break;

			case 'checkbox':

				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="dailyblank_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';

				break;

			case 'select':
				echo '<select class="select' . $field_class . '" name="dailyblank_options[' . $id . ']">';

				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';

				echo '</select>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'radio':
			
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span><br /><br />';
			
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="dailyblank_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}

				break;

			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="dailyblank_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;
				
			case 'medialoader':
			
				echo '<div id="uploader_' . $id . '">';
				
				if ( $options[$id] )  {
					$front_img = wp_get_attachment_image_src( $options[$id], 'wpbs-featured-home' );
					echo '<img id="previewimage_' . $id . '" src="' . $front_img[0] . '" width="640" height="311" alt="default thumbnail" />';
				} else {
					echo '<img id="previewimage_' . $id . '" src="https://placehold.it/640x311" alt="default front image" />';
				}

				echo '<input type="hidden" name="dailyblank_options[' . $id . ']" id="' . $id . '" value="' . $options[$id]  . '" />
  <br /><input type="button" class="upload_image_button button-primary" name="_dailyblank_button' . $id .'" id="_dailyblank_button' . $id .'" data-options_id="' . $id  . '" data-uploader_title="Set Front Image" data-uploader_button_text="Select Image" value="Set/Change Image" />
</div><!-- uploader -->';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="dailyblank_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'text':
			default:
				echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="dailyblank_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

				if ( $desc != '' ) {
				
					if ($id == 'def_thumb') $desc .= '<br /><a href="' . $options[$id] . '" target="_blank"><img src="' . $options[$id] . '" style="overflow: hidden;" width="' . $options["index_thumb_w"] . '"></a>';
					echo '<br /><span class="description">' . $desc . '</span>';
				}

				break;
		}
	}	
			



	/* Initialize settings to their default values */
	public function initialize_settings() {
	
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
	
		update_option( 'dailyblank_options', $default_settings );
	
	}


	/* Register settings via the WP Settings API */
	public function register_settings() {

		register_setting( 'dailyblank_options', 'dailyblank_options', array ( &$this, 'validate_settings' ) );

		foreach ( $this->sections as $slug => $title ) {
		
			add_settings_section( $slug, $title, array( &$this, $this->section_callbacks[$slug] ), 'dailyblank-options' );
		}

		$this->get_settings();
	
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}

	}
	
	
	/* tool to create settings fields */
	public function create_setting( $args = array() ) {

		$defaults = array(
			'id'      => 'default_field',
			'title'   => 'Default Field',
			'desc'    => 'This is a default description.',
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);

		extract( wp_parse_args( $args, $defaults ) );

		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);

		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
				

		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'dailyblank-options', $section, $field_args );

	}
		
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'dailyblank_options' );
				
			if ( $input['dailytime'] !=  $options['dailytime']  ) {
				// time setting change, let's format it nicely
				
				$timeofday = strtotime( $input['dailytime'] );
				
				if ($timeofday) {
					// valid time of day 				
					$input['dailytime'] = date('H:i', $timeofday);
				} else {
					$input['dailytime'] = 'Invalid format! try \'13:00\' or \'1pm\' for 1 o\'clock';
				}
				
			}
			
			if ( $input['standy'] == 'on' ) {
				// standby mode enabled, turn off schedulers
				// from https://codex.wordpress.org/Function_Reference/wp_unschedule_event
				
				// Get the timestamp for the next event.
				$timestamp = wp_next_scheduled( 'dailyblank_hello_twitter' );
				
				// cancel the hourly checks for tweets
				wp_unschedule_event( $timestamp, 'dailyblank_hello_twitter');
				
				// Get the timestamp for the next event.
				$timestamp = wp_next_scheduled( 'dailyblank_low_supply' );

				// cancel the daily checks for low suplly
				wp_unschedule_event( $timestamp, 'dailyblank_low_supply');

			}
			
			// make sure the basetag is lower case
			$input['basetag']  =  strtolower( $input['basetag'] );
				
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		
		return false;
		
	}
 }
 
$theme_options = new dailyblank_Theme_Options();

function dailyblank_option( $option ) {
	$options = get_option( 'dailyblank_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}
?>