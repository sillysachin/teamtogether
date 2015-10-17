<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;
$form 	= $data->form;
$params = $data->params;
?>
<style>
.jsn-rawmode-login {
	background-color: #F4F4F4;
	border: 1px solid #E5E5E5;
	margin: 0 auto;
	padding: 10px;
	width: 350px;
}

.jsn-rawmode-login label {
	width: 115px;
}

</style>
<div class="jsn-article-layout">

<div class="jsn-rawmode-login">
<div >
	<?php if ($params->get('show_page_heading')) : ?>
	<h1>
		<?php echo htmlspecialchars($params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<?php if (($params->get('logindescription_show') == 1 && str_replace(' ', '', $params->get('login_description')) != '') || $params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif ; ?>

		<?php if($params->get('logindescription_show') == 1) : ?>
			<?php echo $params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($params->get('login_image')!='')) :?>
			<img src="<?php echo htmlspecialchars($params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($params->get('logindescription_show') == 1 && str_replace(' ', '', $params->get('login_description')) != '') || $params->get('login_image') != '') : ?>
	</div>
	<?php endif ; ?>

	<form action="javascript:void(0)" method="post">

		<fieldset>
			<?php foreach ($form->getFieldset('credentials') as $field): ?>
				<?php if (!$field->hidden): ?>
					<div class="login-fields"><?php echo $field->label; ?>
					<?php echo $field->input; ?></div>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			<?php endif; ?>
			<button type="button" class="button"><?php echo JText::_('JLOGIN'); ?></button>
		</fieldset>
	</form>
</div>
<div>
	<ul>
		<li>
			<a href="javascript:void(0)">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
		</li>
		<li>
			<a href="javascript:void(0)">
			<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="javascript:void(0)">
				<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div>
</div>
</div>