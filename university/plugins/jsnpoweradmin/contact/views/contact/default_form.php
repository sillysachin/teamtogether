<?php

 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$form = $data->form;
?>
<div class="contact-form">
		<fieldset>
			<legend><?php echo JText::_('COM_CONTACT_FORM_LABEL'); ?></legend>
			<dl>
				<dt><?php echo $form->getLabel('contact_name'); ?></dt>
				<dd><?php echo $form->getInput('contact_name'); ?></dd>
				<dt><?php echo $form->getLabel('contact_email'); ?></dt>
				<dd><?php echo $form->getInput('contact_email'); ?></dd>
				<dt><?php echo $form->getLabel('contact_subject'); ?></dt>
				<dd><?php echo $form->getInput('contact_subject'); ?></dd>
				<dt><?php echo $form->getLabel('contact_message'); ?></dt>
				<dd><?php echo $form->getInput('contact_message'); ?></dd>

				<?php $showEmailCopy = $params->get('show_email_copy') ? 'display-default display-item' : 'hide-item'; ?>
				<dt parname="show_email_copy" id="show_email_copy" class="contact-articles element-switch contextmenu-approved <?php echo $showEmailCopy;?>"><?php echo $form->getLabel('contact_email_copy'); ?></dt>
				<dd><?php echo $form->getInput('contact_email_copy'); ?></dd>


			<?php //Dynamically load any additional fields from plugins. ?>
			     <?php foreach ($form->getFieldsets() as $fieldset): ?>
			          <?php if ($fieldset->name != 'contact'):?>
			               <?php $fields = $form->getFieldset($fieldset->name);?>
			               <?php foreach($fields as $field): ?>
			                    <?php if ($field->hidden): ?>
			                         <?php echo $field->input;?>
			                    <?php else:?>
			                         <dt>
			                            <?php echo $field->label; ?>
			                            <?php if (!$field->required && $field->type != "Spacer"): ?>
			                               <?php echo JText::_('COM_CONTACT_OPTIONAL');?>
			                            <?php endif; ?>
			                         </dt>
			                         <dd><?php echo $field->input;?></dd>
			                    <?php endif;?>
			               <?php endforeach;?>
			          <?php endif ?>
			     <?php endforeach;?>
				<dt></dt>
				<dd><input type="button" onclick="javascript:void(0)" value="<?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?>" />
				</dd>
			</dl>
		</fieldset>
</div>
