<div id="jsn-upgrade-login">
	<h2><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_LOGIN_TITLE') ?></h2>
	<p><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_LOGIN_DESC') ?></p>

	<form id="jsn-upgrade-login">
		<div class="form-inline">
			<label for="username"><?php echo JText::_('JSN_TPLFW_USERNAME') ?>:</label>
			<input type="text" name="username" />
			<label for="password"><?php echo JText::_('JSN_TPLFW_PASSWORD') ?>:</label>
			<input type="password" name="password" />

			<p id="jsn-upgrade-login-error" class="hide jsn-error alert alert-error"></p>
		</div>
		<hr />
	</form>

	<div id="jsn-upgrade-edition-select" class="hide">
		<p><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_EDITIONS_DESC') ?></p>
		<div class="form-inline">
			<label for="edition"><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_TO_EDITION') ?>:</label>
			<select name="edition">
				<option value=""><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_SELECT_EDITION') ?></option>
			</select>
		</div>
		<hr />
	</div>
	<div class="jsn-actions">
		<button id="btn-load-editions" type="button" class="btn" disabled="disabled"><?php echo JText::_('JSN_TPLFW_NEXT') ?></button>
		<button id="btn-start-upgrade" type="button" class="btn hide" disabled="disabled"><?php echo JText::_('JSN_TPLFW_UPGRADE') ?></button>
		<button id="btn-cancel-upgrade" class="btn" type="button"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
	</div>
</div>