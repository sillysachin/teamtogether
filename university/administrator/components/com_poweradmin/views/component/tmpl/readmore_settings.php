<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: readmore_settings.php 13973 2012-07-13 09:32:56Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="jsn-component-settings" id="jsn-component-settings">
<div class="jsn-bootstrap">
	<form name="adminForm" action="index.php?option=com_poweradmin&view=component" method="post" class="form-horizontal">
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_SHOW_READMORE_DESC');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_READMORE_TITLE');?></label>
			<div class="controls">
				<label class="radio inline" for="show_readmore_0">
					<input type="radio" name="show_readmore" id="show_readmore_0" value="0" <?php if ($this->params->get('show_readmore') == 0){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JNo'); ?>
				</label>
				<label class="radio inline" for="show_readmore_1">
					<input type="radio" name="show_readmore" id="show_readmore_1" value="1" <?php if ($this->params->get('show_readmore') == 1){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JYes'); ?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('show_readmore_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('show_readmore_useglobal', null) == null ) echo 'style="display:none;"'; ?>>&#8734;</span>
					<input type="hidden" name="saveTypes[show_readmore]" <?php if ($this->params->get('show_readmore_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_SHOW_READMORE_TITLE_DESC');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_READMORE_SHOW_ARTICLE_TITLE');?></label>
			<div class="controls">
				<label class="radio inline" for="show_readmore_title_0">
					<input type="radio" name="show_readmore_title" id="show_readmore_title_0" value="0" <?php if ($this->params->get('show_readmore_title') == 0){ echo 'checked="checked"'; }?> />
					<?php echo JText::_('JNo'); ?>
				</label>
				<label class="radio inline" for="show_readmore_title_1">
					<input type="radio" name="show_readmore_title" id="show_readmore_title_1" value="1" <?php if ($this->params->get('show_readmore_title') == 1){ echo 'checked="checked"'; }?> />
					<?php echo JText::_('JYes');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('show_readmore_title_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('show_readmore_title_useglobal', null) == null ) echo 'style="display:none;"'; ?>>&#8734;</span>
					<input type="hidden" name="saveTypes[show_readmore_title]" <?php if ($this->params->get('show_readmore_title_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="help-inline" for="readmore_limit" title="<?php echo JText::_('JGLOBAL_SHOW_READMORE_LIMIT_DESC');?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_CHARACTER_LIMIT')?></label>
				<input type="number" class="input-mini" id="readmore_limit" name="readmore_limit" value="<?php echo $this->params->get('readmore_limit');?>" />
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_CHARACTER_LIMIT_TEXT');?></span>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('readmore_limit_useglobal', null) != null ) echo 'style="display:none;"'?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('readmore_limit_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[readmore_limit]" <?php if ($this->params->get('readmore_limit_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<input type="hidden" name="option" value="com_poweradmin" />
		<input type="hidden" name="view" value="component" />
		<input type="hidden" name="layout" value="readmore_settings" />
		<input type="hidden" name="task" value="" />
	</form>
</div>
</div>
