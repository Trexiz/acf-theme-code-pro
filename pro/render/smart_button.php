<?php
// smart_button field

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Very basic support for clone field (store in variable and allow for a quick dump of values)
echo $this->indent . htmlspecialchars("<?php \$".$this->name. ' = ' . $this->get_field_method . "( '" . $this->name ."' ); ?>")."\n";
echo $this->indent . htmlspecialchars("<?php if ( \$".$this->name." ): ?>")."\n";
echo $this->indent . htmlspecialchars("     <a href=\"<?php echo \$".$this->name."['url']; ?>\" <?php echo \$".$this->name."['target']; ?> > ")."\n";
echo $this->indent . htmlspecialchars("         <?php echo \$".$this->name."['text']; ?>")."\n";
echo $this->indent . htmlspecialchars("     </a>")."\n";
echo $this->indent . htmlspecialchars("<?php endif; ?>")."\n";
