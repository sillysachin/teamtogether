<?php
/**
 * @version     $Id$
 * @package     JSNTPLFW
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get template data
$data = JSNTplHelper::getEditingTemplate();

// Generate param name
$paramName =  (string) $this->element['name'];
?>
<div class="jsn-color-list jsn-color-list-v1 <?php echo $this->disabledClass ?>">
	<ul class="jsn-items-list ui-sortable" data-target="#<?php echo $this->id ?>">
		<?php foreach ($data->_JSNColorList->option['list'] as $item): ?>
		<?php $option = $data->_JSNColorList->default[$item] ?>
		<?php $checked = in_array($item, $data->_JSNListColor->option['checked']) ? 'checked' : '' ?>

		<li class="jsn-item ui-state-default">
			<label class="checkbox <?php echo $this->disabledClass ?>">
				<input type="checkbox" name="<?php echo $this->group ?>[<?php echo $this->element['name'] ?>Items][]" value="<?php echo htmlentities($option['value']) ?>" <?php echo $checked ?> <?php echo $this->disabledClass ?> />
				<?php echo JText::_($option['label']) ?>
			</label>
		</li>
		<?php endforeach ?>
	</ul>
	<?php $value = is_array($this->value) ? json_encode($this->value) : $this->value; ?>
	<input type="hidden" name="<?php echo $this->name ?>" value="<?php echo htmlentities($value) ?>" id="<?php echo $this->id ?>" />
</div>
