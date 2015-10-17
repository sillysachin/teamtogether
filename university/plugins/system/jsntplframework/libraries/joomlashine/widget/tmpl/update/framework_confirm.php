<p><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_AUTH_DESC') ?></p>
<div class="alert alert-warning">
	<span class="label label-important"><?php echo JText::_('JSN_TPLFW_IMPORTANT_INFORMATION') ?></span>
	<ul>
		<li><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_AUTH_NOTE_01') ?></li>
	</ul>
</div>
<?php if ($templateHasUpdate) : ?>
<p><?php echo JText::sprintf('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_INVITATION', JText::_($template)) ?></p>
<?php endif; ?>
<form id="jsn-confirm-update" class="form-inline">
	<div class="jsn-toolbar">
		<?php if ($templateHasUpdate) : ?>
		<button id="btn-confirm-update-both" class="btn" type="button"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_AND_TEMPLATE') ?></button>
		<button id="btn-confirm-update" class="btn" type="button"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_ONLY') ?></button>
		<?php else: ?>
		<button id="btn-confirm-update" class="btn btn-primary" type="button"><?php echo JText::_('JSN_TPLFW_UPDATE') ?></button>
		<?php endif; ?>
		<button id="btn-cancel-update" class="btn" type="button"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
	</div>
	<?php echo JHtml::_('form.token') ?>
</form>
