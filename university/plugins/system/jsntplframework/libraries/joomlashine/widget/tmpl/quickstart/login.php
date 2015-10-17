<form id="jsn-quickstart-login">
	<h2><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_LOGIN_TITLE') ?></h2>
	<p><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_LOGIN_DESC') ?></p>

	<div class="form-inline">
		<label for="username"><?php echo JText::_('JSN_TPLFW_USERNAME') ?>:</label>
		<input type="text" name="username" />

		<label for="password"><?php echo JText::_('JSN_TPLFW_PASSWORD') ?>:</label>
		<input type="password" name="password" />
	</div>
	<!-- Error message after submit login information -->
	<p id="jsn-login-error" class="alert alert-error hide"></p>
	<hr />

	<div class="jsn-toolbar">
		<button id="btn-login" class="btn btn-primary" type="button" disabled="disabled"><?php echo JText::_('JSN_TPLFW_DOWNLOAD_FILE') ?></button>
	</div>

	<?php echo JHtml::_('form.token') ?>
</form>