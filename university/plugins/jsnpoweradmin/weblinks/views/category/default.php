<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$params = $data->params;
$state	= $data->state;
$items 	= $data->items;
$category	= $data->category;
$children 	= $data->children;
$parent		= $data->parent;
$pagination	= $data->pagination;

?>
<div class="jsn-article-layout">
<input type="hidden" id="category_id" name="category_id" value="<?php echo $category->id?>" />
<?php if ($params->get('page_heading')) {?>
<?php $showPageHeading = $params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_page_heading" id="show_page_heading" class="element-switch contextmenu-approved <?php echo $showPageHeading;?>" >
<h1>
	<?php echo htmlspecialchars($params->get('page_heading')); ?>
</h1>
</div>
<?php }?>

<?php if ($category->title) {?>
<?php $showCategoryTitle = $params->get('show_category_title', 1) ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_category_title" id="show_category_title" class="element-switch contextmenu-approved <?php echo $showCategoryTitle;?>" >
<h2>
	<?php echo JHtml::_('content.prepare', $category->title, '', 'com_weblinks.category'); ?>
</h2>
</div>
<?php }?>


<div class="category-desc">
	<div class="clearbreak"></div>
	<?php if ($category->getParams()->get('image')) : ?>
	<?php $showDescriptionImage = $params->get('show_description_image', 1) ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_description_image" style="float: left;" id="show_description_image" class="parent-category element-switch contextmenu-approved <?php echo $showDescriptionImage;?>" >
		<img src="<?php echo JSNLayoutHelper::fixImageLinks ($category->getParams()->get('image')); ?>"/>
	</div>
	<div class="clearbreak"></div>
	<?php endif; ?>

	<?php if ($category->description) : ?>
	<?php $showDescription = $params->get('show_description', 1) ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_description"  id="show_description" class="element-switch contextmenu-approved <?php echo $showDescription;?>" >
		<?php echo JHtml::_('content.prepare', $category->description, '', 'com_weblinks.category'); ?>
	</div>
	<?php endif; ?>
	<div class="clearbreak"></div>
</div>

<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/weblinks/views/category/default_items.php'; ?>

<?php if (!empty($children[$category->id])&& $data->maxLevel != 0) : ?>
	<div class="cat-children">
	<h3><?php echo JText::_('JGLOBAL_SUBCATEGORIES') ; ?></h3>
	<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/weblinks/views/category/default_children.php'; ?>
	</div>
<?php endif; ?>
</div>
