<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$params = $data->params;
$children = $data->children;
$category = $data->category;
$pagination = $data->pagination;
?>
<input type="hidden" name="category_id" id="category_id" value="<?php echo $category->id;?>" />
<div class="jsn-rawmode-component-category-list" id="jsn-rawmode-component-category-list">
	<?php $showPageHeading = $params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>

	<?php if ($params->get('page_heading') && $params->get('page_heading')) {?>
	<div parname="show_page_heading" id="show_page_heading" class="category subheading element-switch contextmenu-approved <?php echo $showPageHeading;?>" >
		<?php echo $params->get('page_heading'); ?>
	</div>
	<?php }?>


	<?php $showCategoryTitle = $params->get('show_category_title', 1) ? 'display-default display-item' : 'hide-item'; ?>
	<div class="category subheading">
		<div parname="show_category_title" id="show_category_title" class="category_subheading element-switch contextmenu-approved <?php echo $showCategoryTitle;?>" >
			<?php echo htmlspecialchars ($category->title); ?>
		</div>
	</div>


		<?php if ($category->getParams()->get('image')) :?>
		<?php $showDescriptionImage = $params->get('show_description_image') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_description_image" id="show_description_image" class="show-category element-switch contextmenu-approved <?php echo $showDescriptionImage;?>" >
			<img style="width: 150px;" src="<?php echo JUri::root() . '/' . JSNLayoutHelper::fixImageLinks($category->getParams()->get('image')); ?>"/>
		</div>
		<?php endif; ?>

		<?php if ($category->description) : ?>

		<?php $showDescriptionImage = $params->get('show_description') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_description" id="show_description" catid="<?php echo $category->id; ?>" class="show-category element-switch contextmenu-approved <?php echo $showDescriptionImage;?>" >
			<?php echo htmlspecialchars($category->description); ?>
		</div>
		<?php endif; ?>
		<div class="clr"></div>

	<?php
	include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/category/default_items.php';
	?>

	<?php if (!empty($children[$category->id])&& $data->maxLevel != 0) : ?>
	<div class="cat-children">
		<h3><?php echo JText::_('JGLOBAL_SUBCATEGORIES') ; ?></h3>
		<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/category/default_children.php'; ?>
	</div>
	<?php endif; ?>
</div>
