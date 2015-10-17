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

?>
<?php if (count($items[$parent->id]) > 0 && $maxLevelcat != 0) :?>
<ul>
<?php foreach($items[$parent->id] as $id => $item) : ?>
	<?php
	if ($params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :
	if (!isset($items[$parent->id][$id + 1]))
	{
		$class = ' class="last"';
	}
	?>
	<li<?php echo $class; ?>>
	<?php $class = ''; ?>
		<div class="item-title contextmenu-approved" id="title_<?php echo $item->id?>" category="<?php echo $item->id?>" >
			<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));?>">
			<?php echo  htmlspecialchars($item->title); ?></a>
		</div>


		<?php if ($item->description) : ?>
		<div id="description_<?php echo $item->id?>" parname="show_subcat_desc_cat" >
				<?php echo JHtml::_('content.prepare', JSNLayoutHelper::fixImageLinks($item->description), '', 'com_content.categories'); ?>
		</div>
		<?php endif; ?>

		<?php $showCatNumArticlesCat =  $params->get('show_cat_num_articles_cat') == 1 ? 'display-default display-item' : 'hide-item'; ?>
		<div id="show_aritcle_num_<?php echo $item->id?>" parname="show_cat_num_articles_cat"  class="parent-category element-switch contextmenu-approved <?php echo $showCatNumArticlesCat?>">
				<?php echo JText::_('COM_CONTENT_NUM_ITEMS'); ?> <?php echo $item->numitems; ?>
		</div>

		<?php if (count($item->getChildren()) > 0) :
			$items[$item->id] = $item->getChildren();
			$parent = $item;
			$maxLevelcat--;
			include JPATH_ROOT . '/plugins/jsnpoweradmin/content/views/categories/categories_items.php';
			$parent = $item->getParent();
			$maxLevelcat++;
		endif; ?>

	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>