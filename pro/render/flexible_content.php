<?php
// Flexible content field

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// field location
$field_location = '';

// set sub field nesting level and indent
$sub_field_indent_count = $this->indent_count + ACFTCP_Core::$indent_flexible_content;

// don't need to check for no layouts, acf ui insists on at least one
echo $this->indent . htmlspecialchars("<?php if ( have_rows( '" . $this->name ."'". $this->location . " ) ): ?>")."\n";
echo $this->indent . htmlspecialchars("	<?php while ( have_rows( '" . $this->name ."'". $this->location . " ) ) : the_row(); ?>")."\n";

$layout_count = 0;

// loop through layouts
foreach ( $this->settings['layouts'] as $layout ) {

	// If Flexi add on is used
	if ( "postmeta" == ACFTCP_Core::$db_table ) {

		$layout_key = NULL;
		$parent_field_id = NULL;
		$sub_fields = $layout['sub_fields'];

	}
	// Else ACF PRO is used
	elseif ( "posts" == ACFTCP_Core::$db_table ) {

		$layout_key = $layout['key'];
		$parent_field_id = $this->id;
		$sub_fields = NULL;

	}

	// create layout object that contains layout sub fields
	$acftc_layout = new ACFTCP_Flexible_Content_Layout( $layout['name'], $this->nesting_level + 1, $sub_field_indent_count, $field_location, $layout_key, $this->id, $sub_fields );

	// TODO Check for layout without a name

	// if first non empty layout
	if ( 0 == $layout_count ) {
		// render 'if'
		echo $this->indent . htmlspecialchars("		<?php if ( get_row_layout() == '" . $acftc_layout->name . "' ) : ?>")."\n";
	} else {
		// render 'elseif'
		echo $this->indent . htmlspecialchars("		<?php elseif ( get_row_layout() == '" . $acftc_layout->name . "' ) : ?>")."\n";
	}

	// if layout has sub fields
	if ( !empty( $acftc_layout->sub_fields ) ) {
		$acftc_layout->render_sub_fields();
	}
	else {
		// layout has no sub fields
		echo $this->indent . htmlspecialchars("			<?php // warning: layout '" . $acftc_layout->name . "' has no sub fields ?>")."\n"; // TODO use Label instead of Name?
	}

	$layout_count++;
}

echo $this->indent . htmlspecialchars("		<?php endif; ?>")."\n";
echo $this->indent . htmlspecialchars("	<?php endwhile; ?>")."\n";
echo $this->indent . htmlspecialchars("<?php else: ?>")."\n";
echo $this->indent . htmlspecialchars("	<?php // no layouts found ?>")."\n";
echo $this->indent . htmlspecialchars("<?php endif; ?>")."\n";
