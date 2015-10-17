<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<form action="index.php?option=com_poweradmin&view=assignpages&task=assignpages.save" name="adminForm" method="post">
	<div id="jsn-assign-module" class="jsn-assignment-form jsn-bootstrap">
		<div class="jsn-heading-panel clearafter">
			<!-- Assingment dropdown list -->			
			<div class="asignment-dropdown-list">
				<label for="assignment-dropdown-list"><?php echo JText::_('JSN_ASSIGNPAGE_ASSIGNTO');?></label>
				<select name="assignment" id="assignment-dropdown-list">
					<option value="0" <?php if ($this->assignType === 0) { echo 'selected="selected"'; }?>><?php echo JText::_('JSN_ASSINGMENT_NOPAGES');?></option>
					<option value="1" <?php if ($this->assignType === 1) { echo 'selected="selected"'; }?>><?php echo JText::_('JSN_ASSINGMENT_ALLPAGES');?></option>
					<option value="2" <?php if ($this->assignType === 2 || $this->assignType === -2) { echo 'selected="selected"'; }?>><?php echo JText::_('JSN_ASSIGNMENT_EXCEPT_SELECTED');?></option>
					<option value="3" <?php if ($this->assignType === 3) { echo 'selected="selected"'; }?>><?php echo JText::_('JSN_ASSINGMENT_ONSELECTPAGES');?></option>
				</select>
			</div>
			<!-- Menu items publishing -->
			<span class="jsn-toggle-button">
			   <button class="btn-disabled btn btn-success" type="button" onclick="javascript:void(0)" id="jsn-toggle-publish-module" title="<?php echo JText::_('JSN_RAWMODE_SHOW_PUBLISHED_MENUITEM');?>">
			   	<i class="icon-eye-open icon-white"></i>
			   	<i class="icon-eye-close"></i>
			   </button>
			</span>
		</div>
		<!-- Menu item listing -->
		<div class="jsn-menu-selector-container">
			<div class="jsn-menu-selector-container_inner menus-assignment">
				<!-- Menu type dropdown list -->
				<?php echo $this->menutypes;?>
				<?php echo $this->menuitems;?>
			</div>
		</div>
	</div>
	<input type="hidden" name="option" value="com_poweradmin" />
	<input type="hidden" name="view" value="assignpages" />
	<input type="hidden" name="task" value="assignpages.save" />
</form>