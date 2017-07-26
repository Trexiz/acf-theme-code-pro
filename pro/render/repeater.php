<?php
// Repeater field

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// ACFTCP_Group arguments
if ( "posts" == ACFTCP_Core::$db_table ) { // ACF PRO repeater
	$field_group_id = $this->id;
	$fields = NULL;
}
elseif ( "postmeta" == ACFTCP_Core::$db_table ) { // Repeater Add On
	$field_group_id = NULL;
	$fields = $this->settings['sub_fields']; // In this case $this->settings
	// is actually just an array of all available field data
}
$nesting_arg = 0;
$sub_field_indent_count = $this->indent_count + ACFTCP_Core::$indent_repeater;
$field_location = '';

$repeater_field_group = new ACFTCP_Group( $field_group_id, $fields, $nesting_arg + 1, $sub_field_indent_count, $field_location );

// If repeater has sub fields
if ( !empty( $repeater_field_group->fields ) ) {

	echo $this->indent . htmlspecialchars("<?php if ( have_rows( '" . $this->name ."'". $this->location . " ) ) : ?>")."\n";
	echo $this->indent . htmlspecialchars("	<?php while ( have_rows( '" . $this->name ."'". $this->location . " ) ) : the_row(); ?>")."\n";

	$repeater_field_group->render_field_group();

	echo $this->indent . htmlspecialchars("	<?php endwhile; ?>")."\n";
	echo $this->indent . htmlspecialchars("<?php else : ?>")."\n";
	echo $this->indent . htmlspecialchars("	<?php // no rows found ?>")."\n";
	echo $this->indent . htmlspecialchars("<?php endif; ?>")."\n";

}
// Repeater has no sub fields
else {

	echo $this->indent . htmlspecialchars("<?php // warning: repeater '" . $this->name . "' has no sub fields ?>")."\n";

}
