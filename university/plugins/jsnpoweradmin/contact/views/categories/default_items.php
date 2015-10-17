<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (count($items[$parent->id]) > 0 && $data->maxLevelcat != 0) :
?>
<ul>
<?php foreach($items[$parent->id] as $id => $item) : ?>
	<?php
	if($params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :

	?>
	<li>
		<span class="item-title"><a href="javascript:void(0)">
			<?php echo  htmlspecialchars($item->title); ?></a>
		</span>

		<?php $showSubcatDescCat =  $params->get('show_subcat_desc_cat') == 1 ? 'display-default display-item' : 'hide-item'; ?>
		<?php if ($item->description) : ?>
			<div id="description_<?php echo $item->id?>" parname="show_subcat_desc_cat" category="<?php echo $item->id?>" class="parent-category element-switch contextmenu-approved <?php echo $showSubcatDescCat?>">
				<?php echo JHtml::_('content.prepare', JSNLayoutHelper::fixImageLinks($item->description), '', 'com_contact.categories'); ?>
			</div>
		<?php endif; ?>



		<?php $showCatItemsCat =  $params->get('show_cat_items_cat') == 1 ? 'display-default display-item' : 'hide-item'; ?>
		<div id="show_item_num_<?php echo $item->id?>" parname="show_cat_items_cat"  class="parent-category element-switch contextmenu-approved <?php echo $showCatItemsCat?>">
				<?php echo JText::_('COM_CONTACT_COUNT'); ?> <?php echo $item->numitems; ?>
		</div>


		<?php if(count($item->getChildren()) > 0) :
			$items[$item->id] = $item->getChildren();
			$parent = $item;
			$data->maxLevelcat--;
			include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/categories/default_items.php';
			$parent = $item->getParent();
			$data->maxLevelcat++;
		endif; ?>

	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
