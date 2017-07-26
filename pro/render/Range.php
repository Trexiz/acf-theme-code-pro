<?php // Range

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// get the return format
$return_format = isset( $this->settings['slider_type'] ) ? $this->settings['slider_type'] : '';

// if reutruning a single number
if ( $return_format == 'default' ) {
	echo $this->indent . htmlspecialchars("<?php " . $this->the_field_method . "( '" . $this->name ."'". $this->location . " ); ?>")."\n";
}

// if returning an array (min and max)
if ( $return_format == 'range' ) {
	echo $this->indent . htmlspecialchars("<?php \$".$this->var_name. " = " .  $this->get_field_method . "( '" . $this->name ."'". $this->location . " ); ?>")."\n";
	echo $this->indent . htmlspecialchars("<?php if ( \$".$this->var_name." ) { ?>")."\n";
	echo $this->indent . htmlspecialchars("	<?php echo \$".$this->var_name."['min']; ?>")."\n";
	echo $this->indent . htmlspecialchars("	<?php echo \$".$this->var_name."['max']; ?>")."\n";
	echo $this->indent . htmlspecialchars("<?php } ?>\n");
}

