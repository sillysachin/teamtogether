<p><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_AUTH_DESC') ?></p>
<div class="alert alert-warning">
	<span class="label label-important"><?php echo JText::_('JSN_TPLFW_IMPORTANT_INFORMATION') ?></span>
	<ul>
		<li><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_AUTH_NOTE_01') ?></li>
		<li><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_AUTH_NOTE_02') ?></li>
	</ul>
</div>

<form id="jsn-confirm-update" class="form-inline">
	<?php if ($authenticate == true): ?>
	<h2><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_AUTH_ENTER_CUSTOMER_INFO') ?></h2>
	<p><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_AUTH_ENTER_CUSTOMER_INFO_DESC') ?></p>

	<label for="username"><?php echo JText::_('JSN_TPLFW_USERNAME') ?>:</label>
	<input type="text" name="username" />

	<label for="password"><?php echo JText::_('JSN_TPLFW_PASSWORD') ?>:</label>
	<input type="password" name="password" />

	<!-- Error message after submit login information -->
	<div id="jsn-update-error" class="alert alert-error hide"></div>

	<hr />

	<div class="jsn-toolbar">
		<button id="btn-confirm-update" class="btn btn-primary" type="button" disabled="disabled"><?php echo JText::_('JSN_TPLFW_UPDATE') ?></button>
		<button id="btn-cancel-update" class="btn" type="button"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
	</div>
	<?php else: ?>
	<div class="jsn-toolbar">
		<button id="btn-confirm-update" class="btn btn-primary" type="button"><?php echo JText::_('JSN_TPLFW_UPDATE') ?></button>
		<button id="btn-cancel-update" class="btn" type="button"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
	</div>
	<?php endif ?>
	<?php echo JHtml::_('form.token') ?>
</form>
