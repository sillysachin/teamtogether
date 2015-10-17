<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: set_sub_categories.php 13935 2012-07-12 09:26:45Z thangbh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="jsn-component-settings" id="jsn-component-settings">
<div class="jsn-bootstrap">
	<form name="adminForm" action="index.php?option=com_poweradmin&view=component" method="post" class="form-horizontal">
		<div class="control-group">
			<label for="orderby_pri" title="<?php echo JText::_('JGLOBAL_CATEGORY_ORDER_DESC',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI',true);?></label>
			<div class="controls">
				<select name="orderby_pri" id="orderby_pri">
					<option value="none" <?php if ($this->params->get('orderby_pri') == "none"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_NONE',true);?></option>
					<option value="alpha" <?php if ($this->params->get('orderby_pri') == "alpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_ALPHA',true);?></option>
					<option value="ralpha" <?php if ($this->params->get('orderby_pri') == "ralpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_RALPHA',true);?></option>
					<option value="order" <?php if ($this->params->get('orderby_pri') == "order"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_ORDER',true);?></option>
				</select>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('orderby_pri_useglobal', null) != null ) echo 'style="display:none;"'; ?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('orderby_pri_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[orderby_pri]" <?php if ($this->params->get('orderby_pri_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label for="maxLevel" title="<?php echo JText::_('JGLOBAL_MAXIMUM_CATEGORY_LEVELS_DESC',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT_SUB');?></label>
			<div class="controls">
				<select name="maxLevel" id="maxLevel" class="span1">
					<option value="0" <?php if ($this->params->get('maxLevel') == "0"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT_NONE');?></option>
					<option value="-1" <?php if ($this->params->get('maxLevel') == "-1"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT_ALL');?></option>
					<option value="1" <?php if ($this->params->get('maxLevel') == "1"){ echo 'selected="selected"';}?>><?php echo JText::_('1');?></option>
					<option value="2" <?php if ($this->params->get('maxLevel') == "2"){ echo 'selected="selected"';}?>><?php echo JText::_('2');?></option>
					<option value="3" <?php if ($this->params->get('maxLevel') == "3"){ echo 'selected="selected"';}?>><?php echo JText::_('3');?></option>
					<option value="4" <?php if ($this->params->get('maxLevel') == "4"){ echo 'selected="selected"';}?>><?php echo JText::_('4');?></option>
					<option value="5" <?php if ($this->params->get('maxLevel') == "5"){ echo 'selected="selected"';}?>><?php echo JText::_('5');?></option>
				</select>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('maxLevel_useglobal', null) != null ) echo 'style="display:none;"'; ?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('maxLevel_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[maxLevel]" <?php if ($this->params->get('maxLevel_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>	
		<input type="hidden" name="option" value="com_poweradmin" />
		<input type="hidden" name="view" value="component" />
		<input type="hidden" name="layout" value="set_sub_categories" />
		<input type="hidden" name="task" value="" />
	</form>
</div>
</div>