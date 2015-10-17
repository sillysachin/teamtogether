<p><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_INSTALL_DESC') ?></p>
<ul id="jsn-sample-data-processes">
	<li id="jsn-download-package" class="jsn-loading">
		<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_DOWNLOAD_PACKAGE') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
		<span class="jsn-status"></span>
	</li>
	<li id="jsn-list-extensions" class="hide">
		<span><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_EXTENSION_LIST_DESC') ?></span>
		<span class="hide"><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_DOWNLOAD_EXTENSION') ?></span>
		<ul id="jsn-root-extensions"></ul>
	</li>
	<li id="jsn-install-extensions" class="hide">
		<span><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_EXTENSION_LIST_DESC') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
		<span class="hide"><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_INSTALL_SELECTED_EXTENSIONS') ?></span>
	</li>
	<li id="jsn-install-data" class="hide">
		<span class="jsn-title"><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_INSTALL') ?> <i class="jsn-icon16 jsn-icon-status"></i></span>
		<span class="jsn-status"></span>
	</li>
</ul>

<div id="jsn-manual-install" class="hide">
	<form method="post" enctype="multipart/form-data" target="jsn-sampledata-upload">
		<ol>
			<li>
				<?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_DOWNLOAD_PACKAGE') ?>
				<a href="<?php echo $fileUrl ?>" class="btn"><?php echo JText::_('JSN_TPLFW_DOWNLOAD_FILE') ?></a>
			</li>
			<li>
				<?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_SELECT_DOWNLOADED_PACKAGE') ?>
				<input type="file" name="package" class="jsn-sample-package" />
			</li>
		</ol>
	</form>
	<iframe src="about:blank" class="hide" id="jsn-sampledata-upload" name="jsn-sampledata-upload"></iframe>
</div>

<div id="jsn-success-message" class="hide">
	<h3><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_INSTALL_SUCCESS') ?></h3>
	<p><?php echo JText::sprintf('JSN_TPLFW_SAMPLE_DATA_STEP_INSTALL_SUCCESS_DESC', $template['realName']) ?></p>

	<div id="jsn-attention" class="alert alert-error hide">
		<h4><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_ATTENTION') ?></h4>
		<p><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_ATTENTION_DESC') ?></p>

		<ul>
			<li id="jsn-attension-dummy" class="hide">
				<strong></strong> -
				<?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_STEP_ATTENTION_EXTENSION') ?>
				<a href="" target="_blank" class="btn btn-mini"><?php echo JText::_('JSN_TPLFW_GET_IT_NOW') ?></a>
			</li>
		</ul>
	</div>
</div>

<div class="jsn-toolbar">
	<hr />

	<button id="btn-finish-install" class="btn btn-primary hide"><?php echo JText::_('JSN_TPLFW_SAMPLE_DATA_BUTTON_FINISH') ?></button>
	<button id="btn-manual-install" class="btn btn-primary hide" disabled="disabled"><?php echo JText::_('JSN_TPLFW_CONTINUE') ?></button>
	<button id="btn-confirm-install" class="btn btn-primary" disabled="disabled"><?php echo JText::_('JSN_TPLFW_CONTINUE') ?></button>
	<button id="btn-cancel-install" class="btn"><?php echo JText::_('JSN_TPLFW_CANCEL') ?></button>
</div>
