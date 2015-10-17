<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: blog_children.php 13922 2012-07-12 04:23:17Z thangbh $
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;
?>

<?php if (count($data->children[$data->category->id]) > 0 && $data->maxLevel != 0) : ?>
	<ul>
	<?php foreach($data->children[$data->category->id] as $id => $child) : ?>
		<?php
		$totalItems = $child->getNumItems(true);		
		?>
		<?php if ($totalItems > 0) :?>
			<?php $appredContextMenu = 'contextmenu-approved';?>
			<li>
		<?php else: ?>
			<?php $appredContextMenu = '';?>
			<?php $showEmptyCategoryClass = $data->params->get('show_empty_categories') ? 'display-default display-item' : 'hide-item'; ?>
			<li class="empty-category element-switch contextmenu-approved <?php echo $showEmptyCategoryClass;?>" id="show_empty_categories_<?php echo $child->id;?>" catid="<?php echo $child->id;?>">
		<?php endif;?>
			<span class="item-title">
				<?php echo htmlspecialchars($child->title); ?>
			</span>
			<div class="clearbreak"></div>
			<?php $showSubcatDescClass = $data->params->get('show_subcat_desc')? 'display-default display-item' : 'hide-item'; ?>
			<div class="category-desc element-switch <?php echo $appredContextMenu;?> <?php echo $showSubcatDescClass;?>" id="show_subcat_desc_<?php echo $child->id;?>" catid="<?php echo $child->id;?>">
				<?php
					echo JSNLayoutHelper::fixImageLinks( $child->description ); 
				?>
			</div>
			<div class="clearbreak"></div>
			<?php $showCatNumberArticleClass = $data->params->get('show_cat_num_articles') ? 'display-default display-item' : 'hide-item'; ?>
			<div class="category-num-items element-switch <?php echo $appredContextMenu;?> <?php echo $showCatNumberArticleClass;?>" id="show_cat_num_articles_<?php echo $child->id;?>" catid="<?php echo $child->id;?>">
				<?php echo JText::_('COM_CONTENT_NUM_ITEMS') ; ?>
				<?php echo $totalItems; ?>
			</div>
			<div class="clearbreak"></div>
			<?php 
				if (count($child->getChildren()) > 0):
					$data->children[$child->id] = $child->getChildren();
					$data->category = $child;
					$data->maxLevel--;
					if ($data->maxLevel != 0) :
						include( dirname(__FILE__) . DS . 'blog_children.php' );
					endif;
					$data->category = $child->getParent();
					$data->maxLevel++;
				endif; 
			?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif;
