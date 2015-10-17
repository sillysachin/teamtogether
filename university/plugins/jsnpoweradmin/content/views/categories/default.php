<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN PowerAdmin support for com_content
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$items    = $data->items;
$pageclass_sfx =  $data->pageclass_sfx;
$params =  $data->params;
$parent =  $data->parent;
$maxLevelcat = $data->maxLevelcat;

?>
<input type="hidden" name="category_id" id="category_id" value="<?php echo $parent->id;?>" />
<div class="jsn-article-layout">
	<div class="categories-list">
		<?php if ($params->get('page_heading')) : ?>
		<?php $showPageHeading = $params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_page_heading" class="parent-category element-switch contextmenu-approved <?php echo $showPageHeading?>">
			<?php echo $params->get('page_heading'); ?>
		</div>
		<?php endif; ?>

		<?php $showBaseDescription = $params->get('show_base_description') ? 'display-default display-item' : 'hide-item'; ?>

		<?php 	//If there is a description in the menu parameters use that; ?>
			<?php if($params->get('categories_description')) : ?>
				<div parname="show_base_description" id="paren_description_<?php echo $parent->id?>" category="<?php echo $parent->id?>" class="parent-category element-switch contextmenu-approved <?php echo $showBaseDescription?>">
				<?php echo  JHtml::_('content.prepare',  JSNLayoutHelper::fixImageLinks($params->get('categories_description')), '', 'com_content.categories'); ?>
				</div>
			<?php  else: ?>
				<?php  if ($parent->description) : ?>
				<div parname="show_base_description"  id="paren_description_<?php echo $parent->id?>" category="<?php echo $parent->id?>" class="parent-category element-switch contextmenu-approved <?php echo $showBaseDescription?>">
					<?php  echo JHtml::_('content.prepare',JSNLayoutHelper::fixImageLinks( $parent->description), '', 'com_content.categories'); ?>
				</div>
				<?php  endif; ?>
			<?php  endif; ?>
	<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/content/views/categories/categories_items.php';?>
	</div>
</div>
