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
$parent = $data->parent;
$items	= $data->items;

?>
<input type="hidden" name="category_id" id="category_id" value="<?php echo $parent->id;?>" />
<div class="jsn-article-layout">

	<?php $showPageHeading = $params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>

	<?php if ($params->get('page_heading')) {?>
	<div parname="show_page_heading" id="show_page_heading" class="show-category element-switch contextmenu-approved <?php echo $showPageHeading;?>" >
		<?php echo $data->params->get('page_heading'); ?>
	</div>
	<?php }?>


	<?php $showBaseDescription = $params->get('show_base_description') ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_base_description" id="show_base_description" class="element-switch contextmenu-approved <?php echo $showBaseDescription;?>" >
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($params->get('categories_description')) : ?>
		<div class="category-desc base-desc">
			<b><?php echo  $params->get('categories_description'); ?></b>
			</div>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($parent->description) : ?>
				<div class="category-desc base-desc">
					<b><?php  echo $parent->description; ?></b>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
		</div>
<?php
include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/categories/default_items.php';
?>
</div>
