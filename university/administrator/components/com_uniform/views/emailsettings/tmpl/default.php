<?php
/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Email settings
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

$action = JFactory::getApplication()->input->getVar('action', 1);
?>
<div class="jsn-bootstrap emailsettings">
	<div id="form-loading" class="jsn-bgloading"><i class="jsn-icon32 jsn-icon-loading"></i></div>
	<form action="<?php echo JRoute::_('index.php?option=com_uniform&view=emailsettings&tmpl=component'); ?>" class="form-horizontal hide" method="post" name="adminForm" id="uni-form">
		<fieldset style="border: none;margin: 0;padding: 0;">
			<?php
			if ($action == 0)
			{

				echo '<p class="alert alert-info">' . JText::_('JSN_UNIFORM_EMAIL_USUALLY_SENT_TO_THE_PERSON') . '</p>';
			}
			else
			{
				echo '<p class="alert alert-info">' . JText::_('JSN_UNIFORM_EMAIL_USUALLY_SENT_TO_WEBSITE') . '</p>';
			}
			?>
			<div class="control-group">
				<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_NAME_' . $action); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_FROM'); ?></label>
				<div id="from" class="controls">
					<?php echo $this->_form->getInput('template_from') ?>
					<?php
					if ($action == 1)
					{
						?>
						<button class="btn" id="btn-select-field-from" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_INSERT_FIELD'); ?>"><?php echo JText::_('JSN_UNIFORM_SELECTED'); ?></button>
						<?php
					}
					?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_EMAIL_' . $action); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_REPLY_TO'); ?> </label>
				<div id="reply-to" class="controls">
					<?php echo $this->_form->getInput('template_reply_to') ?>
					<?php
					if ($action == 1)
					{
						?>
						<button class="btn" id="btn-select-field-to" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_INSERT_FIELD'); ?>"><?php echo JText::_('JSN_UNIFORM_SELECTED'); ?></button>
						<?php
					}
					?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_SUBJECT_' . $action); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_SUBJECT'); ?> </label>
				<div id="subject" class="controls">
					<?php echo $this->_form->getInput('template_subject') ?>
					<button class="btn" id="btn-select-field-subject" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_INSERT_FIELD'); ?>"><?php echo JText::_('JSN_UNIFORM_SELECTED'); ?></button>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_CONTENT_' . $action); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_MESSAGE'); ?> </label>
				<div id="template-msg" class="controls">
					<div class="template-msg-content">
						<?php echo $this->_form->getInput('template_message') ?>
					</div>
					<button class="btn " id="btn-select-field-message" onclick="return false;" title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_INSERT_FIELD'); ?>"><?php echo JText::_('JSN_UNIFORM_SELECTED'); ?></button>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_ATTACH_' . $action); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_ATTACH'); ?> </label>
				<div id="attach-file" class="controls">
					<ul class="jsn-items-list ui-sortable" data-value='<?php echo !empty($this->_item->template_attach)?$this->_item->template_attach:"";?>'>
						<li class="ui-state-default ui-state-disabled" title="You must add some file-type field in your form in order to select it here">No file field found</li>
					</ul>
				</div>
			</div>
		</fieldset>
		<?php echo $this->_form->getInput('template_id') ?>
		<input type="hidden" name="jform[form_id]" value="<?php echo isset($_GET['form_id']) ? $_GET['form_id'] : ''; ?>" />
		<input type="hidden" id="template_notify_to" name="jform[template_notify_to]" value="<?php echo isset($_GET['action']) ? $_GET['action'] : ''; ?>" />
		<input type="hidden" name="task" value="emailsettings.form" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>