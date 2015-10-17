<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$params = $data->params;
?>
<div class="jsn-article-layout">

	<?php $showPageHeading = $data->params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>
	<?php if ($data->params->get('page_heading')) {?>
	<div parname="show_page_heading" id="show_page_heading" class="show-category element-switch contextmenu-approved <?php echo $showPageHeading;?>" >
		<?php echo $data->params->get('page_heading'); ?>
	</div>
	<?php }?>

	<fieldset class="filters">
	<div class="filter-search">
		<?php $showFilterField = $data->params->get('filter_field') == 'hide' ? 'hide-item' : 'display-default display-item'; ?>
		<?php $_filterLabel  = $data->params->get('filter_field') == 'hide' ? '' : JText::_('COM_CONTENT_'. strtoupper ($data->params->get('filter_field')).'_FILTER_LABEL').'&#160;' ?>
		<div id="filter_field" style="margin-right: 8px;" parname="filter_field" class="show-category contextmenu-approved element-switch <?php echo $showFilterField?>">
			<span><?php echo $_filterLabel; ?></span>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo  htmlspecialchars(htmlentities($data->filter)); ?>" class="inputbox"/>
		</div>

		<div class="show-category" >
		<?php echo $data->form->monthField; ?>
		<?php echo $data->form->yearField; ?>
		<?php echo $data->form->limitField; ?>
		<button type="button" class="btn" style="margin-bottom: 8px;"><?php echo JText::_('JGLOBAL_FILTER_BUTTON'); ?></button>
		</div>
	</div>
	</fieldset>

	<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/content/views/archive/default_items.php';?>

</div>
