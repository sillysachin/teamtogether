<div id="jsn-upgrade-process">
	<h2><?php echo JText::sprintf('JSN_TPLFW_AUTO_UPGRADE_PROCESS_TITLE', $template['edition'], $edition) ?></h2>
	<p><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_PROCESS_DESC') ?></p>

	<ul id="jsn-upgrade-tasks">
		<li id="jsn-upgrade-download" class="hide jsn-loading">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_PROCESS_DOWNLOAD_PACKAGE') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
		<li id="jsn-upgrade-install" class="hide jsn-loading">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_PROCESS_INSTALL_TEMPLATE') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
		<li id="jsn-upgrade-replace" class="hide jsn-loading">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_PROCESS_UPGRADE_FILES') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
		<li id="jsn-upgrade-migrate" class="hide jsn-loading">
			<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_PROCESS_MIGRATE_SETTINGS') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
			<span class="jsn-status"></span>
		</li>
	</ul>
	<hr />
	
	<div id="jsn-upgrade-success" class="hide">
		<h3><?php echo JText::_('JSN_TPLFW_AUTO_UPGRADE_SUCCESS') ?></h3>
		<p><?php echo JText::sprintf('JSN_TPLFW_AUTO_UPGRADE_SUCCESS_DESC', $edition) ?></p>

		<hr />
	</div>

	<div class="jsn-actions">
		<button id="jsn-upgrade-finish" class="btn" disabled="disabled"><?php echo JText::_('JSN_TPLFW_FINISH') ?></button>
	</div>
</div>