<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.tooltip');
?>
<div id="jsn-module-type-container">
	<?php foreach ($this->items as &$item) : ?>
		<div class="jsn-item-type" id="<?php echo $item->extension_id;?>"  style="display: none;">
			<?php
				$name	= $this->escape($item->name);
				$desc	= $this->escape($item->desc);
			?>
			<div class="editlinktip" title="<?php echo $name.':&#013;'.$desc; ?>"><?php echo $name;?></div>
		</div>
	<?php endforeach; ?>
</div>
<input type="hidden" id="position" value="<?php echo $this->position?>" />
