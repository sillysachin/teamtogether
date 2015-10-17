<p><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_INSTALLATION_DESC') ?></p>

<ul id="jsn-update-processes">
	<li id="jsn-download-package" class="jsn-loading">
		<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_DOWNLOAD_PACKAGE') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
		<span class="jsn-status"></span>
	</li>
	<li id="jsn-install-update" class="jsn-loading hide">
		<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_INSTALL') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
		<span class="jsn-status"></span>
	</li>
</ul>

<div id="jsn-success-message" class="hide">
	<h3><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_INSTALL_SUCCESS') ?></h3>
	<p><?php echo JText::_('JSN_TPLFW_AUTO_UPDATE_FRAMEWORK_INSTALL_SUCCESS_DESC') ?></p>
</div>

<hr />
<div class="jsn-toolbar">
	<button id="btn-finish-install" class="btn btn-primary hide"><?php echo JText::_('JSN_TPLFW_FINISH') ?></button>
</div>