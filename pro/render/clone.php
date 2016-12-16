<?php
// Clone field

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Very basic support for clone field (store in variable and allow for a quick dump of values)
echo $this->indent . htmlspecialchars("<?php \$".$this->name. ' = ' . $this->get_field_method . "( '" . $this->name ."' ); ?>")."\n";
echo $this->indent . htmlspecialchars("<?php // var_dump( \$".$this->name. " ); ?>")."\n";
