<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: set_layout_content.php 13973 2012-07-13 09:32:56Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="jsn-component-settings" id="jsn-component-settings">
<div class="jsn-bootstrap">
	<form name="adminForm" action="index.php?option=com_poweradmin&view=component" method="post" onsubmit="
		if (jQuery('#filter_field_0').attr('checked')){
			jQuery('#filter_field').remove();
		}
		if (jQuery('#list_show_date_0').attr('checked')){
			jQuery('#list_show_date').remove();
		}
		" class="form-horizontal">
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_FILTER_FIELD_DESC',true);?>" class="control-label"><?php echo JText::_('Filter');?></label>
			<div class="controls">
				<label class="radio inline" for="filter_field_0">
					<input type="radio" name="filter_field" id="filter_field_0" value="hide" onchange="if ( jQuery(this).attr('checked') ){ jQuery('#filter_field_select_options').hide(); } "  <?php if ($this->params->get('filter_field') == 'hide'){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_HIDE');?>
				</label>
				<label class="radio inline" for="filter_field_1">
					<input type="radio" name="filter_field" id="filter_field_1" onchange="if ( jQuery(this).attr('checked') ){ jQuery('#filter_field_select_options').show(); } " <?php if ( in_array($this->params->get('filter_field'), array('title', 'author', 'hits') )){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('filter_field_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('filter_field_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[filter_field]" <?php if ($this->params->get('filter_field_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group" id="filter_field_select_options" <?php if ($this->params->get('filter_field') == 'hide') { echo 'style="display:none;"';} ?>>
			<div class="controls">
				<label class="help-inline" for="filter_field" title="<?php echo JText::_('JGLOBAL_FILTER_FIELD_DESC',true);?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_DATE_BY_TITLE');?></label>
				<select name="filter_field" id="filter_field" class="input-small">
					<option value="title" <?php if ($this->params->get('filter_field') == "title"){ echo 'selected="selected"';}?>><?php echo JText::_('JGLOBAL_TITLE');?></option>
					<option value="author" <?php if ($this->params->get('filter_field') == "author"){ echo 'selected="selected"';}?>><?php echo JText::_('JAUTHOR');?></option>
					<option value="hits" <?php if ($this->params->get('filter_field') == "hits"){ echo 'selected="selected"';}?>><?php echo JText::_('JGLOBAL_HITS');?></option>
			</select>
			</div>
		</div>
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_DISPLAY_SELECT_DESC',true);?>" class="control-label"><?php echo JText::_('JGLOBAL_DISPLAY_SELECT_LABEL');?></label>
			<div class="controls">
				<label class="radio inline" for="show_pagination_limit_0">
					<input type="radio" name="show_pagination_limit" id="show_pagination_limit_0" value="0" <?php if ($this->params->get('show_pagination_limit') == 0){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_HIDE');?>
				</label>
				<label class="radio inline" for="show_pagination_limit_1">
					<input type="radio" name="show_pagination_limit" id="show_pagination_limit_1" value="1" <?php if ($this->params->get('show_pagination_limit') == 1){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('show_pagination_limit_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('show_pagination_limit_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[show_pagination_limit]" <?php if ($this->params->get('show_pagination_limit_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_SHOW_HEADINGS_DESC',true);?>" class="control-label"><?php echo JText::_('JGLOBAL_SHOW_HEADINGS_LABEL');?></label>
			<div class="controls">
				<label class="radio inline" for="show_headings_0">
					<input type="radio" name="show_headings" id="show_headings_0" value="0" <?php if ($this->params->get('show_headings') == 0){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_HIDE');?>
				</label>
				<label class="radio inline" for="show_headings_1">
					<input type="radio" name="show_headings" id="show_headings_1" value="1" <?php if ($this->params->get('show_headings') == 1){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('show_headings_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('show_headings_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[show_headings]" <?php if ($this->params->get('show_headings_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_LIST_AUTHOR_DESC',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_AUTHOR_TITLE');?></label>
			<div class="controls">
				<label class="radio inline" for="list_show_author_0">
					<input type="radio" name="list_show_author" id="list_show_author_0" value="0" <?php if ($this->params->get('list_show_author') == 0){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_HIDE');?>
				</label>
				<label class="radio inline" for="list_show_author_1">
					<input type="radio" name="list_show_author" id="list_show_author_1" value="1" <?php if ($this->params->get('list_show_author') == 1){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('list_show_author_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('list_show_author_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[list_show_author]" <?php if ($this->params->get('list_show_author_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_SHOW_DATE_DESC',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_DATE_TITLE');?></label>
			<div class="controls">
				<label class="radio inline" for="list_show_date_0">
					<input type="radio" name="list_show_date" id="list_show_date_0" value="0" onchange="if ( jQuery(this).attr('checked') ){ jQuery('#list_show_date_select_options').hide(); jQuery('#date_format_txt').hide(); } "  <?php if ($this->params->get('list_show_date') == '0'){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_HIDE');?>
				</label>
				<label class="radio inline" for="list_show_date_1">
					<input type="radio" name="list_show_date" id="list_show_date_1" onchange="if ( jQuery(this).attr('checked') ){ jQuery('#list_show_date_select_options').show(); jQuery('#date_format_txt').show(); } " <?php if ( in_array($this->params->get('list_show_date'), array('created', 'modified', 'published') )){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('list_show_date_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('list_show_date_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[list_show_date]" <?php if ($this->params->get('list_show_date_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group" id="list_show_date_select_options" <?php if ($this->params->get('list_show_date') == '0') { echo 'style="display:none;"';} ?>>
			<div class="controls">
				<label class="help-inline" for="list_show_date" title="<?php echo JText::_('JGLOBAL_SHOW_DATE_DESC',true);?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_DATE_BY_TITLE');?></label>
				<select name="list_show_date" id="list_show_date" class="input-small">
					<option value="created" <?php if ($this->params->get('list_show_date') == "created"){ echo 'selected="selected"';}?>><?php echo JText::_('JGLOBAL_CREATED');?></option>
					<option value="modified" <?php if ($this->params->get('list_show_date') == "modified"){ echo 'selected="selected"';}?>><?php echo JText::_('JGLOBAL_MODIFIED');?></option>
					<option value="published" <?php if ($this->params->get('list_show_date') == "published"){ echo 'selected="selected"';}?>><?php echo JText::_('JPUBLISHED');?></option>
				</select>
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_TEXT');?></span>
			</div>
		</div>
		<div class="control-group" id="date_format_txt" <?php if ($this->params->get('list_show_date') == '0') { echo 'style="display:none;"';} ?>>
			<div class="controls">
				<label class="help-inline" for="list_date_format" title="<?php echo JText::_('JGLOBAL_DATE_FORMAT_DESC',true);?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_DATE_FORMAT');?></label>
				<input type="text" maxlength="50" id="list_date_format" name="date_format" value="<?php echo $this->params->get('date_format');?>" class="input-small" />	
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('date_format_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('date_format_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[date_format]" <?php if ($this->params->get('date_format_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>		
			</div>
		</div>
		<div class="control-group">
			<label title="<?php echo JText::_('JGLOBAL_LIST_HITS_DESC',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_HITS_TITLE');?></label>
			<div class="controls">
				<label class="radio inline" for="list_show_hits_0">
					<input type="radio" name="list_show_hits" id="list_show_hits_0" value="0" <?php if ($this->params->get('list_show_hits') == 0){ echo 'checked="checked"';}?> />
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_HIDE');?>
				</label>
				<label class="radio inline" for="list_show_hits_1">
					<input type="radio" name="list_show_hits" id="list_show_hits_1" value="1" <?php if ($this->params->get('list_show_hits') == 1){ echo 'checked="checked"';}?>/>
					<?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW');?>
				</label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('list_show_hits_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('list_show_hits_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[list_show_hits]" <?php if ($this->params->get('list_show_hits_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<hr />
		<div class="control-group">
			<label for="orderby_pri" title="<?php echo JText::_('JGLOBAL_CATEGORY_ORDER_DESC',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI');?></label>
			<div class="controls">
				<select name="orderby_pri" id="orderby_pri">
					<option value="none" <?php if ($this->params->get('orderby_pri') == "none"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_NONE');?></option>
					<option value="alpha" <?php if ($this->params->get('orderby_pri') == "alpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_ALPHA');?></option>
					<option value="ralpha" <?php if ($this->params->get('orderby_pri') == "ralpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_RALPHA');?></option>
					<option value="order" <?php if ($this->params->get('orderby_pri') == "order"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_ORDER');?></option>
				</select>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('orderby_pri_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('orderby_pri_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[orderby_pri]" <?php if ($this->params->get('orderby_pri_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="help-inline" for="show_subcategory_content" title="<?php echo JText::_('JGLOBAL_SHOW_SUBCATEGORY_CONTENT_DESC',true);?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT');?></label>
				<select name="show_subcategory_content" id="show_subcategory_content" class="input-mini">
					<option value="0" <?php if ($this->params->get('show_subcategory_content') == "0"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT_NONE');?></option>
					<option value="-1" <?php if ($this->params->get('show_subcategory_content') == "-1"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT_ALL');?></option>
					<option value="1" <?php if ($this->params->get('show_subcategory_content') == "1"){ echo 'selected="selected"';}?>><?php echo JText::_('1');?></option>
					<option value="2" <?php if ($this->params->get('show_subcategory_content') == "2"){ echo 'selected="selected"';}?>><?php echo JText::_('2');?></option>
					<option value="3" <?php if ($this->params->get('show_subcategory_content') == "3"){ echo 'selected="selected"';}?>><?php echo JText::_('3');?></option>
					<option value="4" <?php if ($this->params->get('show_subcategory_content') == "4"){ echo 'selected="selected"';}?>><?php echo JText::_('4');?></option>
					<option value="5" <?php if ($this->params->get('show_subcategory_content') == "5"){ echo 'selected="selected"';}?>><?php echo JText::_('5');?></option>
				</select>
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT_TEXT');?></span>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('show_subcategory_content_useglobal', null) != null ) echo 'style="display:none;"'; ?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('show_subcategory_content_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[show_subcategory_content]" <?php if ($this->params->get('show_subcategory_content_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label for="orderby_sec" title="<?php echo JText::_('JGLOBAL_Article_Order_Desc',true);?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC');?></label>
			<div class="controls">
				<select name="orderby_sec" id="orderby_sec" onchange="if (this.value == 'rdate' || this.value == 'date') { jQuery('#order_date_select_options').show(); } else{ jQuery('#order_date_select_options').hide(); }">
					<option value="front" <?php if ($this->params->get('orderby_sec') == "front"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_FRONT');?></option>
					<option value="rdate" <?php if ($this->params->get('orderby_sec') == "rdate"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_RDATE');?></option>
					<option value="date" <?php if ($this->params->get('orderby_sec') == "date"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_DATE');?></option>
					<option value="alpha" <?php if ($this->params->get('orderby_sec') == "alpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_ALPHA');?></option>
					<option value="ralpha" <?php if ($this->params->get('orderby_sec') == "ralpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_RALPHA');?></option>
					<option value="author" <?php if ($this->params->get('orderby_sec') == "author"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_AUTHOR');?></option>
					<option value="rauthor" <?php if ($this->params->get('orderby_sec') == "rauthor"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_RAUTHOR');?></option>
					<option value="hits" <?php if ($this->params->get('orderby_sec') == "hits"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_HITS');?></option>
					<option value="rhits" <?php if ($this->params->get('orderby_sec') == "rhits"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_RHITS');?></option>
					<option value="order" <?php if ($this->params->get('orderby_sec') == "order"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC_ORDER');?></option>
				</select>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('orderby_sec_useglobal', null) != null ) echo 'style="display:none;"'; ?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('orderby_sec_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[orderby_sec]" <?php if ($this->params->get('orderby_sec_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls" id="order_date_select_options" <?php if ($this->params->get('orderby_sec') == "rdate" || $this->params->get('orderby_sec') == "date"){ echo 'style="display:block;"'; }else{ echo 'style="display:none;"'; }?>>
				<label class="help-inline" for="order_date" title="<?php echo JText::_('JGLOBAL_ORDERING_DATE_DESC',true);?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE');?></label>
				<select name="order_date" id="order_date" class="input-small">
					<option value="created" <?php if ($this->params->get('order_date') == "created"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_CREATED');?></option>
					<option value="modified" <?php if ($this->params->get('order_date') == "modified"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_MODIFIED');?></option>
					<option value="published" <?php if ($this->params->get('order_date') == "published"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_PUBLISHED');?></option>
				</select>
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_TEXT');?></span>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE',true);?>" <?php if ($this->params->get('order_date_useglobal', null) != null ) echo 'style="display:none;"'; ?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY',true);?>" <?php if ($this->params->get('order_date_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[order_date]" <?php if ($this->params->get('order_date_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<input type="hidden" name="option" value="com_poweradmin" />
		<input type="hidden" name="view" value="component" />
		<input type="hidden" name="layout" value="set_layout_content" />
		<input type="hidden" name="task" value="" />
	</form>
</div>
</div>