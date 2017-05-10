<?php

// to do : make this a child class of field group class ?

// Class for a layout in a flexible content field.
class ACFTCP_Flexible_Content_Layout {

	public $name; // Used in flexible content layout render partial
	public $sub_fields; // Used in flexible content layout render partial

	/**
	 * $nesting_level
	 *
	 * 0 = not nested inside another field
	 * 1 = nested one level deep inside another field eg. repeater
	 * 2 = nested two levels deep inside other fields etc
	 */
	public $nesting_level;
	public $indent_count;
	public $field_location;

	/**
	* Constructor
	*
	* @param string	$name
	* @param int 	$nesting_level
	* @param int 	$indent_count
	* @param string $field_location
	* @param strong	$layout_key			Used to get sub fields (ACF PRO)
	* @param int	$parent_field_id	Used to get sub fields (ACF PRO)
	* @param array 	$sub_fields			Used to get sub fields (Flexi add on)
	*/
	function __construct( $name, $nesting_level = 0, $indent_count = 0, $field_location = '', $layout_key = null, $parent_field_id = null, $sub_fields = null ) {

		$this->name = $name;
		$this->nesting_level = $nesting_level;
		$this->indent_count = $indent_count;
		$this->field_location = $field_location;

		// If flexi add on is used
		if ( 'postmeta' == ACFTCP_Core::$db_table ) {

			$this->sub_fields = $sub_fields;

		}
		// Else ACF PRO is used
		elseif ( 'posts' == ACFTCP_Core::$db_table ) {

			$this->sub_fields = $this->get_sub_fields_from_posts_table( $layout_key, $parent_field_id );

		}

	}

	/**
	* Get all sub fields in layout
	*
	* @param $layout_key
	* @param $parent_field_id
	*/
	private function get_sub_fields_from_posts_table( $layout_key, $parent_field_id ) {

		// get all sub fields of parent field
		$query_args = array(
			'post_type' =>  array( 'acf-field' , 'acf' ), // TODO should this be a conditional?
			'post_parent' => $parent_field_id,
			'posts_per_page' => '-1',
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$fields_query = new WP_Query( $query_args );
		$all_sub_fields = $fields_query->posts;

		// get only fields that belong to layout
		$layout_sub_fields = array();

		foreach ( $all_sub_fields as $sub_field ) {

			$sub_field_content = unserialize( $sub_field->post_content );
			$sub_field_layout_key = $sub_field_content['parent_layout'];

			// if sub field belongs to layout, add it to the array of fields
			if ( $layout_key == $sub_field_layout_key ) {
				array_push( $layout_sub_fields, $sub_field );
			}

		}

		return $layout_sub_fields;

	}

	// Renders theme PHP for layout sub fields
	public function render_sub_fields() {

		// loop through sub fields
		foreach ( $this->sub_fields as $sub_field ) {

			$field_location = ''; // TODO: Is this needed?

			$acftc_field = new ACFTCP_Field( $this->nesting_level, $this->indent_count, $field_location, $sub_field );

			$acftc_field->render_field();

		}

	}

}
