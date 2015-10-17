<p><?php echo JText::sprintf('JSN_TPLFW_SAMPLE_DATA_CONFIRM_DESC', $template['realName']) ?></p>
<?php if ($update['template']['hasUpdate']) : ?>
<div id="cancel-sample-data-installation" class="alert alert-error"><?php echo JText::sprintf('JSN_TPLFW_ERROR_SAMPLE_DATA_OUT_OF_DATED', $update['template']['currentVersion']); ?></div>

<hr />

<div class="jsn-toolbar">
	<button id="btn-update-template" class="btn btn-primary"><?php echo JText::_('JSN_TPLFW_UPDATE_TEMPLATE') ?></button>
	<button id="btn-cancel-install" class="btn"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
</div>
<?php else : ?>
<div class="alert alert-warning">
	<span class="label label-important"><?php echo JText::_('JSN_TPLFW_IMPORTANT_INFORMATION') ?></span>
	<ul>
		<li><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_CONFIRM_NOTE_01') ?></li>
		<li><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_CONFIRM_NOTE_02') ?></li>
	</ul>
</div>

<!-- Error message after submit login information -->
<div id="jsn-confirm-error" class="alert alert-error hide"></div>

<form id="jsn-confirm-agreement">
	<div class="row-fluid">
		<label class="checkbox">
			<input type="checkbox" name="agree" value="1" id="confirm-agreement" />
			<strong><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_CONFIRM_AGREEMENT') ?></strong>
		</label>
	</div>

	<div class="jsn-toolbar">
		<button id="btn-install" class="btn btn-primary" type="button" disabled="disabled"><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_BUTTON_INSTALL') ?></button>
		<button id="btn-cancel-install" class="btn"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
	</div>

	<?php echo JHtml::_('form.token') ?>
</form>
<?php endif; ?>