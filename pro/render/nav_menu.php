<?php
// Nav Menu field

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// get return format
$return_format = $this->settings['save_format'];

// Basic support for the nav menu field
if ( $return_format == 'id' ) {
    echo $this->indent . htmlspecialchars("<?php \$".$this->name. ' = ' . $this->get_field_method . "( '" . $this->name ."' );")."\n";
    echo $this->indent . htmlspecialchars("wp_nav_menu( array(")."\n";
    echo $this->indent . htmlspecialchars(" 'id' => \$".$this->name)."\n";
    echo $this->indent . htmlspecialchars(") ); ?>")."\n";
}

if ( $return_format == 'menu' ) {
    echo $this->indent . htmlspecialchars("<?php ". $this->the_field_method . "( '" . $this->name ."' ); ?>")."\n";
}

if ( $return_format == 'object' ) {
    echo $this->indent . htmlspecialchars("<?php \$".$this->name. ' = ' . $this->get_field_method . "( '" . $this->name ."' ); ?>")."\n";
    echo $this->indent . htmlspecialchars("<?php // var_dump( \$".$this->name. " ); ?>")."\n";
}
