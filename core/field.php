<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class for field functionality
 */
class ACFTCP_Field {

	private $render_partial;

	private $nesting_level;
	private $indent_count;
	private $indent = '';

	private $quick_link_id = '';
	private $the_field_method = 'the_field';
	private $get_field_method = 'get_field';
	private $get_field_object_method = 'get_field_object';

	private $id = null; // only used if posts table and required for flexible layouts
	private $label;
	private $name;
	private $type;
	private $var_name;

	/**
	 * All unserialized field data to be used in partials for edge cases.
	 * Needs to be public to sort fields.
	 */
	public $settings;

	private $clone = false;

	private $location_val; // TODO: Need a comment defining this variable


	/**
	 * Constructor
	 *
	 * @param $nesting_level					int
	 * @param $indent_count						int
	 * @param $location							string
	 * @param $field_data_obj					object
	 * @param $clone_parent_acftcp_field_ref	object ref
	 */
	function __construct( $nesting_level = 0, $indent_count = 0 , $location_val = '', $field_data_obj = null, &$clone_parent_acftcp_field_ref = null ) {

		$this->nesting_level = $nesting_level;
		$this->indent_count = $indent_count;

		$this->location = $location_val;
		$this->location = $this->get_location_param();

		// If field is nested
		if ( 0 < $this->nesting_level ) {

			// Calc indent string
			$this->indent = $this->get_indent();

			// Use ACF sub field methods instead
			$this->the_field_method = 'the_sub_field';
			$this->get_field_method = 'get_sub_field';
			$this->get_field_object_method = 'get_sub_field_object';

		}

		if ( "postmeta" == ACFTCP_Core::$db_table ) {
			$this->construct_from_postmeta_table( $field_data_obj );
		} elseif ( "posts" == ACFTCP_Core::$db_table ) {
			$this->construct_from_posts_table( $field_data_obj );
		}

		// variable name that is used in code rendered
		$this->var_name = $this->get_var_name( $this->name );

		// cloned fields
		if ( $clone_parent_acftcp_field_ref ) {

			$this->clone = true;
			$clone_settings = $clone_parent_acftcp_field_ref->settings;

			// reset the location
			$this->location = $location_val;

			if ( 1 === $clone_settings['prefix_name'] ) {
				$this->name = $clone_parent_acftcp_field_ref->name . '_' . $this->name;
			}

		}

		// partial to use for rendering
		$this->render_partial = $this->get_render_partial();

	}

	// Set field properties using data from postmeta table
	private function construct_from_postmeta_table( $field_data_obj ) {

		if ( empty( $field_data_obj ) ) {
			return false;
		}

		// if repeater add on is used, field data will be in an array
		if ( is_array( $field_data_obj ) ) {

			// Put all field data including sub fields in settings.
			// This is necessary to support nested repeaters created with the
			// Repeater Add On and is only done is this case.
			$this->settings = $field_data_obj;

			// to do : note absence of ID property here
			$this->label = $field_data_obj['label'];
			$this->name = $field_data_obj['name'];
			$this->type = $field_data_obj['type'];

		}
		// field data is an object
		else {

			// unserialize meta values
			$this->settings = unserialize( $field_data_obj->meta_value );

			// to do : note absence of ID property here
			$this->label = $this->settings['label'];
			$this->name = $this->settings['name'];
			$this->type = $this->settings['type'];

		}

		// if field is not nested
		if ( 0 == $this->nesting_level ) {

			// get quick link id
			$this->quick_link_id = $this->settings['key'];

		}

	}


	// Set field properties using data from posts table
	private function construct_from_posts_table( $field_data_obj ) {

		if ( empty( $field_data_obj ) ) {
			return false;
		}

		// unserialize content
		$this->settings = unserialize( $field_data_obj->post_content );

		$this->id = $field_data_obj->ID; // required for flexible layout
		$this->label = $field_data_obj->post_title;
		$this->name = $field_data_obj->post_excerpt;
		$this->type = $this->settings['type'];

		// if field is not nested
		if ( 0 == $this->nesting_level ) {

			// get quick link id
			$this->quick_link_id = $this->id;

		}

	}


	// Get indent string for nested fields
	private function get_indent() {

		$indent = '';

		for ( $i = $this->indent_count; $i > 0 ; $i-- ) {
			$indent .= '	';
		}

		return $indent;

	}

	// Get the variable name
	private function get_var_name( $name ) {

		// Replace any hyphens with underscores
		$var_name = str_replace('-', '_', $name);

		// Replace any other special chars with underscores
		$var_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $var_name);

		// Replace multiple underscores with single
		$var_name = preg_replace('/_+/', '_', $var_name);

		return $var_name;

	}

	// Get location paramater ( a srting with a variable or a value)
	private function get_location_param() {

		// If location set to options page, add the options parameter
		if ($this->location == 'options_page') {

			return ', \'option\'';

		} elseif ($this->location == 'user_role' || $this->location == 'user_form' ) {

			return ', $user_id_prefixed';

		} elseif ($this->location == 'taxonomy') {

			return ', $term_id_prefixed';

		} elseif ($this->location == 'attachment') {

			return ', $attachment_id';

		} elseif ($this->location == 'widget') {

			return ', $widget_id_prefixed';

		} elseif ($this->location == 'comment') {

			return ', $comment_id_prefixed';

		// else set location to an empty string
		} else {

			return '';

		}

	}
	// Get the path to the partial used for rendering the field
	private function get_render_partial() {

		if ( !empty( $this->type ) ) {

			// Field types only supported in TC Pro
			if ( file_exists( ACFTCP_Core::$plugin_path . 'pro' ) &&
				 in_array( $this->type, ACFTCP_Core::$tc_pro_field_types ) ) {
				$render_partial = ACFTCP_Core::$plugin_path . 'pro/render/' . $this->type . '.php';
			}

			// Basic field types with a shared partial
			elseif ( in_array( $this->type, ACFTCP_Core::$basic_types ) ) {
				$render_partial = ACFTCP_Core::$plugin_path . 'render/basic.php';
			}

			// Field types with their own partial
			else {
				$render_partial = ACFTCP_Core::$plugin_path . 'render/' . $this->type . '.php';
			}

			return $render_partial;

		}

	}

	// Render theme PHP for field
	public function render_field() {

		if ( !empty($this->type) ) {

			// Ignore these fields tyles
			$ignore_field_types = array( 'tab', 'message', 'accordion', 'enhanced_message', 'row' );

			// Bail early for these ignored field types
			if ( in_array( $this->type, $ignore_field_types )) {
				return;
			}

			if ( 0 == $this->nesting_level && !$this->clone ) {

				// Open field meta div
				echo '<div class="acftc-field-meta">';

					// Setup a var for debug mode
					$debug_mode = '';

					// Chcek if debug mode is set, escape and store the value
					if ( isset( $_GET["debug"] ) ) {

						$debug_mode = htmlspecialchars( $_GET["debug"] );

					}

					// If debug mode is true
					if( $debug_mode == 'on' ) {

						// Echo the label as a heading for debugging
						echo htmlspecialchars('<h2>Debug: '. $this->label .'</h2>');

					} else {

						// Echo the code block title as pseudo contnet to avoid selection
						echo '<span class="acftc-field-meta__title" data-type="'. $this->type .'"data-pseudo-content="'. $this->label .'"></span>';

					}

				// Close field meta div
				echo '</div>';

				// Open div for field code wrapper (used for the button etc)
				echo '<div class="acftc-field-code" id="acftc-' . $this->quick_link_id . '">';

				// Copy button
				echo '<a href="#" class="acftc-field__copy acf-js-tooltip" title="Copy to clipboard"></a>';

				// PHP code block for field
				echo '<pre class="line-numbers"><code class="language-php">';

			}

			// Include field type partial
			if ( file_exists( $this->render_partial ) ) {
				include( $this->render_partial );
			}

			// Field not supported at all (yet)
			else {
				echo $this->indent . htmlspecialchars( "<?php // The " . $this->type  . " field type is not supported in this verison of the plugin. ?>" ) . "\n";
				echo $this->indent . htmlspecialchars( "<?php // Contact http://www.hookturn.io to request support for this field type. ?>" ) . "\n";
			}

			if ( 0 == $this->nesting_level && !$this->clone ) {

				// Close PHP code block
				echo '</div></code></pre>';
			}

		}

	}

}
