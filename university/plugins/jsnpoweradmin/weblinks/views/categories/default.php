<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$params	= $data->params;
$parent	= $data->parent;
$items	= $data->items;
?>
<div class="jsn-article-layout">
<input type="hidden" id="category_id" name="category_id" value="<?php echo $data->parent->id?>" />
<?php if ($params->get('page_heading')) {?>
	<?php $showPageHeading = $params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_page_heading" id="show_page_heading" class="element-switch contextmenu-approved <?php echo $showPageHeading;?>" >
		<h1>
			<?php echo htmlspecialchars($params->get('page_heading')); ?>
		</h1>
	</div>
<?php }?>
<?php if  ($params->get('categories_description') || $parent->description) {?>

<?php $showBaseDescription = $params->get('show_base_description') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_base_description" id="show_base_description" class="element-switch contextmenu-approved <?php echo $showBaseDescription;?>" >
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($params->get('categories_description')) : ?>
			<div class="category-desc base-desc">
			<?php echo JHtml::_('content.prepare', JSNLayoutHelper::fixImageLinks($params->get('categories_description')), '', 'com_weblinks.categories'); ?>
			</div>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($parent->description) : ?>
				<div class="category-desc base-desc">
					<?php echo JHtml::_('content.prepare', JSNLayoutHelper::fixImageLinks($parent->description), '', 'com_weblinks.categories'); ?>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
</div>
<?php }?>
<?php
include JPATH_ROOT . '/plugins/jsnpoweradmin/weblinks/views/categories/default_items.php';
?>
</div>
