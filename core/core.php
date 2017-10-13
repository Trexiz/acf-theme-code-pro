<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class ACFTCP_Core {

	public static $plugin_path = '';
	public static $plugin_url = '';
	public static $plugin_version = '';
	public static $db_table = '';
	public static $indent_repeater = 2;
	public static $indent_flexible_content = 3;
	public static $basic_types = array(
		'text',
		'textarea',
		'number',
		'email',
		'url',
		'color_picker',
		'wysiwyg',
		'oembed',
		'radio',
		'range'
	);

	// Field types supported by TC Pro
	public static $tc_pro_field_types = array(
		'flexible_content',
		'repeater',
		'gallery',
		'clone',
		'font-awesome',
		'google_font_selector',
		'rgba_color',
		'image_crop',
		'markdown',
		'nav_menu',
		'smart_button',
		'sidebar_selector',
		'tablepress_field',
		'table',
		'address',
		'acf_code_field',
		'posttype_select',
		'link_picker',
		'youtubepicker',
		'number_slider',
		'link',
		'group',
		'focal_point',
		'button_group'
	);

	/**
	 * ACFTCP_Core constructor
	 */
	public function __construct( $plugin_path, $plugin_url, $plugin_version ) {

		// Paths, URLS and plugin version
		self::$plugin_path = $plugin_path;
		self::$plugin_url = $plugin_url;
		self::$plugin_version = $plugin_version;

		// Hooks
		add_action( 'admin_init', array($this, 'set_db_table') );
		add_action( 'add_meta_boxes', array($this, 'register_meta_boxes') );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );

	}


	/**
	 * Set the DB Table (as this changes between version 4 and 5)
	*  So we need to check if we're using version 4 or version 5 of ACF
	 * This includes ACF 5 in a theme or ACF 4 or 5 installed via a plugin
	 */
	public function set_db_table() {

		// If we can't detect ACF
		if ( ! class_exists( 'acf' )  ) {

			// bail early
			return;

		 }

		// Check for the function acf_get_setting - this came in version 5
		if ( function_exists( 'acf_get_setting' ) ) {

			// Get the version to be sure
			// This will return a srting of the version eg '5.0.0'
			$version = acf_get_setting( 'version' );

		} else {

			// Use the version 4 logic to get the version
			// This will return a string if the plugin is active eg '4.4.11'
			// This will retrn the string 'version' if the plugin is not active
			$version = apply_filters( 'acf/get_info', 'version' );

		}

		// Get only the major version from the version string (the first character)
		$major_version = substr( $version, 0 , 1 );

		// If the major version is 5
		if( $major_version == '5' ) {

			// Set the db table to posts
			self::$db_table = 'posts';

		// If the major version is 4
		} elseif( $major_version == '4' ) {

			// Set the db table to postmeta
			self::$db_table = 'postmeta';

		}

	}


	/**
	 * Register meta box
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'acftc-meta-box',
			__( 'Theme Code', 'acf_theme_code_pro' ),
			array( $this, 'display_callback'),
			array( 'acf', 'acf-field-group' )
		);

	}


	/**
	 * Meta box display callback
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function display_callback( $field_group_post_obj ) {

		$locations_ui = new ACFTCP_Locations( $field_group_post_obj );
		$locations_ui->render_locations();

	}


	// load scripts and styles
	public function enqueue( $hook ) {

		// grab the post type
		global $post_type;

		// if post type is an ACF field group
		if( 'acf-field-group' == $post_type || 'acf' == $post_type ) {

			// Plugin styles
			wp_enqueue_style( 'acftc_css', self::$plugin_url . 'assets/acf-theme-code.css', '' , self::$plugin_version);

			// Prism (code formatting)
			wp_enqueue_style( 'acftc_prism_css', self::$plugin_url . 'assets/prism.css', '' , self::$plugin_version);
			wp_enqueue_script( 'acftc_prism_js', self::$plugin_url . 'assets/prism.js', '' , self::$plugin_version);

			// Clipboard
			wp_enqueue_script( 'acftc_clipboard_js', self::$plugin_url . 'assets/clipboard.min.js', '' , self::$plugin_version);

			// Plugin js
			wp_enqueue_script( 'acftc_js', self::$plugin_url . 'assets/acf-theme-code.js', array( 'acftc_clipboard_js' ), '', self::$plugin_version, true );

		}

	}

}
