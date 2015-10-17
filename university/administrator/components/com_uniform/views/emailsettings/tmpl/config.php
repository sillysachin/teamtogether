<?php
/**
 * @version     $Id: config.php 19014 2012-11-28 04:48:56Z thailv $
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
?>
<div class="jsn-bootstrap emailsettings">
    <div id="form-loading" class="jsn-bgloading"><i class="jsn-icon32 jsn-icon-loading"></i></div>
    <form action="<?php echo JRoute::_('index.php?option=com_uniform&view=emailsettings&layout=config&tmpl=component'); ?>" class="form-horizontal hide" method="post" name="adminForm" id="uni-form">
		<fieldset style="border: none;margin: 0;padding: 0;">
			<?php echo '<p class="alert alert-info">' . JText::_('JSN_UNIFORM_EMAIL_USUALLY_SENT_TO_WEBSITE') . '</p>'; ?>
			<div class="control-group">
				<label class="control-label  jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_SUBJECT_1'); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_SUBJECT'); ?></label>
				<div class="controls">
					<?php echo $this->_form->getInput('template_subject') ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label  jsn-label-des-tipsy" original-title="<?php echo JText::_('JSN_UNIFORM_EMAIL_SPECIFY_THE_CONTENT_1'); ?>"><?php echo JText::_('JSN_UNIFORM_EMAIL_SETTINGS_MESSAGE'); ?></label>
				<div class="controls" >
					<?php echo $this->_form->getInput('template_message') ?>
				</div>
			</div>
		</fieldset>
		<?php echo $this->_form->getInput('template_id') ?>
		<input type="hidden" name="jform[template_notify_to]" value="1" />
		<input type="hidden" name="task" value="emailsettings.config" />
		<?php echo JHtml::_('form.token'); ?>
    </form>
</div>