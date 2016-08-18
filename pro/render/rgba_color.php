<?php
// rgba_color (3rd party field)

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Set return value
// NOTE: Value is a string
$return_format = isset( $this->settings['return_value'] ) ? $this->settings['return_value'] : '';

// Return format 'css rgba'
if ( $return_format == '0' ) {
    echo $this->indent . htmlspecialchars("<?php \$".$this->name. ' = ' . $this->get_field_method . "( '" . $this->name ."' ); ?>")."\n";
    echo $this->indent . htmlspecialchars("<?php echo \$".$this->name."; ?>")."\n";
}

// Return format 'css rgba'
if ( $return_format == '1' ) {
    echo $this->indent . htmlspecialchars("<?php \$".$this->name. ' = ' . $this->get_field_method . "( '" . $this->name ."' ); ?>")."\n";
    echo $this->indent . htmlspecialchars("<?php echo \$".$this->name."['hex']; ?>")."\n";
    echo $this->indent . htmlspecialchars("<?php echo \$".$this->name."['opacity']; ?>")."\n";
}
