<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (count($children[$category->id]) > 0 && $data->maxLevel != 0) :
?>
<ul>
<?php foreach($children[$category->id] as $id => $child) : ?>
	<?php
	if($params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
	?>
	<li>
			<span class="item-title"><a href="javascript:void(0)">
				<?php echo htmlspecialchars($child->title); ?></a>
			</span>

			<?php if ($child->description) : ?>
			<?php $showSubcatDesc = ($params->get('show_subcat_desc') == 1) ? 'display-default display-item' : 'hide-item'; ?>
			<div parname="show_subcat_desc" id="show_subcat_desc" class="element-switch contextmenu-approved <?php echo $showSubcatDesc;?>" >
				<?php echo JHtml::_('content.prepare', JSNLayoutHelper::fixImageLinks($child->description), '', 'com_weblinks.category'); ?>
			</div>
			<?php endif; ?>


			<?php $showCatNumLinks = ($params->get('show_cat_num_links') == 1) ? 'display-default display-item' : 'hide-item'; ?>
			<div parname="show_cat_num_links" id="show_cat_num_links" class="element-switch contextmenu-approved <?php echo $showCatNumLinks;?>" >
				<?php echo JText::_('COM_WEBLINKS_NUM'); ?>
				<?php echo $child->numitems; ?>
			</div>


			<?php if(count($child->getChildren()) > 0 ) :
				$children[$child->id] = $child->getChildren();
				$category = $child;
				$data->maxLevel--;
				include JPATH_ROOT . '/plugins/jsnpoweradmin/weblinks/views/category/default_children.php';
				$category = $child->getParent();
				$data->maxLevel++;
			endif; ?>
		</li>
	<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif;
