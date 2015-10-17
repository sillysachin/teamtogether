<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;
$params = $data->params;
$form	= $data->form;
?>
<div class="jsn-article-layout">
	<?php if ($params->get('show_page_heading')) : ?>
	<h1>
		<?php echo htmlspecialchars($params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form id="user-registration" action="javascript:void(0)" method="post" class="form-validate">

		<?php foreach ($form->getFieldsets() as $fieldset): ?>
		<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
			<dl>
			<?php foreach ($form->getFieldset($fieldset->name) as $name => $field): ?>
				<dt><?php echo $field->label; ?></dt>
				<dd><?php echo $field->input; ?></dd>
			<?php endforeach; ?>
			</dl>
		</fieldset>
		<?php endforeach; ?>

		<div>
			<button type="button" class="validate"><?php echo JText::_('JSUBMIT'); ?></button>

		</div>
	</form>
</div>
