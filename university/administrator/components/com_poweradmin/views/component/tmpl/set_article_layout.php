<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: set_article_layout.php 13918 2012-07-12 03:53:14Z thangbh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="jsn-component-settings" id="jsn-component-settings">
<div class="jsn-bootstrap">
	<form name="adminForm" action="index.php?option=com_poweradmin&view=component" method="post" class="form-horizontal">
		<div class="control-group">
			<label for="num_leading_articles" title="<?php echo JText::_('JGLOBAL_NUM_LEADING_ARTICLES_DESC');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_NUM_LEADING_ARTICLES');?></label>
			<div class="controls">
				<input type="number" class="input-mini" name="num_leading_articles" id="num_leading_articles" value="<?php echo $this->params->get('num_leading_articles');?>" />
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ARTICLE');?></span>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('num_leading_articles_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('num_leading_articles_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[num_leading_articles]" <?php if ($this->params->get('num_leading_articles_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label for="num_intro_articles" title="<?php echo JText::_('JGLOBAL_NUM_INTRO_ARTICLES_DESC');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_NUM_INTRO_ARTICLES');?></label>
			<div class="controls">
				<input type="number" class="input-mini" name="num_intro_articles" id="num_intro_articles" value="<?php echo $this->params->get('num_intro_articles');?>" />
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ARTICLE');?></span>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('num_intro_articles_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('num_intro_articles_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[num_intro_articles]" <?php if ($this->params->get('num_intro_articles_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="help-inline" for="num_columns" title="<?php echo JText::_('JGLOBAL_NUM_COLUMNS_DESC');?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_NUM_COLUMNS');?></label>
				<input type="number" class="input-mini" name="num_columns" id="num_columns" value="<?php echo $this->params->get('num_columns');?>" />
				<span class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_COLUMNS');?></span>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('num_columns_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('num_columns_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[num_columns]" <?php if ($this->params->get('num_columns_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="help-inline" for="multi_column_order" title="<?php echo JText::_('JGLOBAL_MULTI_COLUMN_ORDER_DESC');?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_MULTI_COLUMN_ORDER');?></label>
				<select name="multi_column_order" id="multi_column_order" class="input-medium">
					<option value="0" <?php if ($this->params->get('multi_column_order') == "0"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_MULTI_COLUMN_ORDER_DOWN');?></option>
					<option value="1" <?php if ($this->params->get('multi_column_order') == "1"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_MULTI_COLUMN_ORDER_ACROSS');?></option>
				</select>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('multi_column_order_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('multi_column_order_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[multi_column_order]" <?php if ($this->params->get('multi_column_order_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label for="num_links" title="<?php echo JText::_('JGLOBAL_NUM_LINKS_DESC');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_NUM_LINKS')?></label>
			<div class="controls">
				<input type="number" class="input-mini" name="num_links" id="num_links" value="<?php echo $this->params->get('num_links');?>" />
				<label class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_LINKS');?></label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('num_links_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('num_links_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[num_links]" <?php if ($this->params->get('num_links_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<hr />
		<div class="control-group">
			<label for="orderby_pri" title="<?php echo JText::_('JGLOBAL_CATEGORY_ORDER_DESC');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI');?></label>
			<div class="controls">
				<select name="orderby_pri" id="orderby_pri">
					<option value="none" <?php if ($this->params->get('orderby_pri') == "none"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_NONE');?></option>
					<option value="alpha" <?php if ($this->params->get('orderby_pri') == "alpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_ALPHA');?></option>
					<option value="ralpha" <?php if ($this->params->get('orderby_pri') == "ralpha"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_RALPHA');?></option>
					<option value="order" <?php if ($this->params->get('orderby_pri') == "order"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_PRI_ORDER');?></option>
				</select>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('orderby_pri_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('orderby_pri_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[orderby_pri]" <?php if ($this->params->get('orderby_pri_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="help-inline" for="show_subcategory_content" title="<?php echo JText::_('JGLOBAL_SHOW_SUBCATEGORY_CONTENT_DESC');?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_SHOW_SUBCATEGORY_CONTENT');?></label>
				<select name="show_subcategory_content" id="show_subcategory_content" class="input-small">
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
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('show_subcategory_content_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('show_subcategory_content_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[show_subcategory_content]" <?php if ($this->params->get('show_subcategory_content_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group">
			<label for="orderby_sec" title="<?php echo JText::_('JGLOBAL_Article_Order_Desc');?>" class="control-label"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDERBY_SEC');?></label>
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
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('orderby_sec_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('orderby_sec_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[orderby_sec]" <?php if ($this->params->get('orderby_sec_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
		<div class="control-group" id="order_date_select_options" <?php if ($this->params->get('orderby_sec') == "rdate" || $this->params->get('orderby_sec') == "date"){ echo 'style="display:block;"'; }else{ echo 'style="display:none;"'; }?>>
			<div class="controls">
				<label class="help-inline" for="order_date" title="<?php echo JText::_('JGLOBAL_ORDERING_DATE_DESC');?>"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE');?></label>
				<select name="order_date" id="order_date" class="input-medium">
					<option value="created" <?php if ($this->params->get('order_date') == "created"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_CREATED');?></option>
					<option value="modified" <?php if ($this->params->get('order_date') == "modified"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_MODIFIED');?></option>
					<option value="published" <?php if ($this->params->get('order_date') == "published"){ echo 'selected="selected"';}?>><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_PUBLISHED');?></option>
				</select>
				<label class="help-inline"><?php echo JText::_('JSN_RAWMODE_COMPONENT_ORDER_DATE_TEXT');?></label>
				<a class="btn btn-mini apply-setting-area">
					<span class="symbol-only" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_ONLY_THIS_PAGE');?>" <?php if ($this->params->get('order_date_useglobal', null) != null ) echo 'style="display:none;"';?>>1</span>
					<span class="symbol-globally" title="<?php echo JText::_('JSN_RAWMODE_COMPONENT_APPLY_SETTING_GLOBALLY');?>" <?php if ($this->params->get('order_date_useglobal', null) == null ) echo 'style="display:none;"';?>>&#8734;</span>
					<input type="hidden" name="saveTypes[order_date]" <?php if ($this->params->get('order_date_useglobal', null) != null ) echo 'value="globally"'; else{ echo 'value="only"';} ?>>
				</a>
			</div>
		</div>
	<input type="hidden" name="option" value="com_poweradmin" />
	<input type="hidden" name="view" value="component" />
	<input type="hidden" name="layout" value="set_article_layout" />
	<input type="hidden" name="task" value="" />
	</form>
</div>
</div>