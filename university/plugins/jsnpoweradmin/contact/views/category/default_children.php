<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (count($children[$category->id]) > 0 && $data->maxLevel != 0) :
?>
<ul class="list-striped list-condensed">
<?php foreach ($children[$category->id] as $id => $child) : ?>
	<?php
	if ($params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
		if (!isset($children[$category->id][$id + 1]))
		{
			$class = ' class="last"';
		}
	?>
	<li<?php echo $class; ?>>
		<?php $class = ''; ?>
			<h4 class="item-title">
				<a href="javascript:void(0)">
				<?php echo htmlspecialchars($child->title); ?>
				</a>


				<?php $showCatItems =  $params->get('show_cat_items') ==1 ? 'display-default display-item' : 'hide-item'; ?>
				<div id="show_cat_items" parname="show_cat_items"  class="pull-right show-category element-switch contextmenu-approved <?php echo $showCatItems?>">
						<span class="badge badge-info " title="<?php echo JText::_('COM_CONTACT_CAT_NUM'); ?>"><?php echo $child->numitems; ?></span>
				</div>
			</h4>


				<?php $showSubcatDesc =  $params->get('show_subcat_desc') ==1 ? 'display-default display-item' : 'hide-item'; ?>
				<div id="show_subcat_desc" parname="show_subcat_desc"  class="element-switch contextmenu-approved <?php echo $showSubcatDesc?>">
				<?php if ($child->description) : ?>
					<small class="category-desc">
						<?php echo JHtml::_('content.prepare', $child->description, '', 'com_contact.category'); ?>
					</small>
				</div>
				<?php endif; ?>

			<?php if (count($child->getChildren()) > 0 ) :
				$children[$child->id] = $child->getChildren();
				$category = $child;
				$data->maxLevel--;
				include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/category/default_children.php';
				$category = $child->getParent();
				$data->maxLevel++;
			endif; ?>
	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>
