<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$class = ' class="first"';
if (count($items[$parent->id]) > 0 && $data->maxLevelcat != 0) :
?>
<ul>
<?php foreach($items[$parent->id] as $id => $item) : ?>
	<?php
	if($params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :

	?>
	<li>
		<span class="item-title"><a href="javascript:void(0)">
			<?php echo htmlspecialchars($item->title); ?></a>
		</span>

		<?php if ($item->description) : ?>
		<?php $showSubcatDesCat = ($params->get('show_subcat_desc_cat') ==1) ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_subcat_desc_cat" id="show_subcat_desc_cat" class="element-switch contextmenu-approved <?php echo $showSubcatDesCat;?>" >
			<div class="category-desc">
				<?php echo JHtml::_('content.prepare', $item->description, '', 'com_weblinks.categories'); ?>
			</div>
		</div>
		<?php endif; ?>
<div class="clearbreak"></div>
		<?php $showCatNumLinksCat = ($params->get('show_cat_num_links_cat') ==1) ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_cat_num_links_cat" style="float: left;" id="show_cat_num_links_cat" class="parent-category element-switch contextmenu-approved <?php echo $showCatNumLinksCat;?>" >
				<?php echo JText::_('COM_WEBLINKS_NUM'); ?>
				<?php echo $item->numitems; ?>

		</div>
<div class="clearbreak"></div>
		<?php if(count($item->getChildren()) > 0) :
			$items[$item->id] = $item->getChildren();
			$parent = $item;
			$data->maxLevelcat--;
			include JPATH_ROOT . '/plugins/jsnpoweradmin/weblinks/views/categories/default_items.php';
			$parent = $item->getParent();
			$data->maxLevelcat++;
		endif; ?>

	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
