<p><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_INSTALLATION_DESC') ?></p>

<form id="jsn-update-install" action="index.php?widget=update&action=load-package">
	<ul id="jsn-update-processes">
		<li id="jsn-download-package" class="jsn-loading hide">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_DOWNLOAD_PACKAGE') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
		<li id="jsn-backup-modified-files" class="jsn-loading hide">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_CREATE_LIST_UPDATED') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
			<p id="jsn-download-backup-of-modified-files" class="hide">
				<?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FOUND_MODIFIED_FILE_BEING_UPDATED'); ?>
				<a href="<?php echo JRoute::_('index.php?widget=integrity&action=download&type=update&template=' . $template['name']) ?>" class="btn btn-mini"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_DOWNLOAD_MODIFIED_FILES'); ?></a>
			</p>
		</li>
		<li id="jsn-download-framework" class="jsn-loading hide">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_DOWNLOAD_PACKAGE') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
		<li id="jsn-install-framework" class="jsn-loading hide">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_INSTALL') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
		<li id="jsn-install-update" class="jsn-loading hide">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_INSTALL') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
	</ul>

	<div id="jsn-success-message" class="hide">
		<h3><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_INSTALL_SUCCESS') ?></h3>
		<p><?php echo JText::sprintf('JSN_TPLFW_AUTO_UPDATE_INSTALL_SUCCESS_DESC', $template['realName']) ?></p>

		<div id="jsn-backup-information" class="alert alert-warning hide">
			<span class="label label-important"><?php echo JText::_('JSN_TPLFW_IMPORTANT_INFORMATION') ?></span>
			<p>
				<?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_INSTALL_DOWNLOAD_BACKUP') ?>
				<a href="<?php echo JRoute::_('index.php?widget=integrity&action=download&type=update&template=' . $template['name']) ?>" class="btn btn-mini"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_DOWNLOAD_MODIFIED_FILES'); ?></a>
			</p>
		</div>
	</div>

	<div class="jsn-toolbar hide">
		<hr />

		<div id="jsn-put-update-on-hold" class="hide">
			<button id="btn-continue-install" class="btn btn-primary"><?php echo JText::_('JSN_TPLFW_CONTINUE') ?></button>
			&nbsp;
			<button id="btn-cancel-install" class="btn"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
		</div>
		<button id="btn-finish-install" class="btn btn-primary hide"><?php echo JText::_('JSN_TPLFW_FINISH') ?></button>
	</div>
</form>
