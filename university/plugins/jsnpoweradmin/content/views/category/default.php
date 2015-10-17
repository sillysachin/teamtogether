<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<input type="hidden" name="category_id" id="category_id" value="<?php echo $data->category->id;?>" />
<div class="jsn-rawmode-component-category-list" id="jsn-rawmode-component-category-list">
	<!-- Heading -->
	<?php $showPageHeadingClass = $data->params->get('show_page_heading', 1) == 1 ? 'display-default display-item' : 'hide-item'; ?>
	<div  class="category heading">
		<div class="<?php echo $showPageHeadingClass;?> element-switch contextmenu-approved" id="show_page_heading" catid="<?php echo $data->category->id;?>">
			<?php echo htmlspecialchars($data->params->get('page_heading')); ?>
		</div>
	</div>
	<!-- Subheading or title -->
	<?php $showSubheadingOrTitle = ( $data->params->get('show_category_title', 1) or $data->params->get('page_subheading') ) ? 'display-default display-item' : 'hide-item'; ?>
	<div class="category subheading">
		<div class="<?php echo $showSubheadingOrTitle;?> element-switch contextmenu-approved" id="show_category_title" catid="<?php echo $data->category->id;?>">
			<?php echo htmlspecialchars($data->params->get('page_subheading')); ?>
			<?php $showCategoryTitleClass = $data->params->get('show_category_title') ? 'display-default display-item' : 'hide-item'; ?>
			<div class="subheading-category"><?php echo $data->category->title;?></div>
		</div>
	</div>
	<!-- Description -->
	<div class="category category-desc">
		<?php $showDesImageClass = ($data->params->get('show_description_image') && $data->category->getParams()->get('image')) ? 'display-default display-item' : 'hide-item'; ?>
		<?php $showDesTextClass =  ($data->params->get('show_description') && $data->category->description) ? 'display-default display-item' : 'hide-item'; ?>
 		<?php if ( $data->category && $data->category->getParams()->get('image') ):?>
			<div class="description-img element-switch contextmenu-approved <?php echo $showDesImageClass;?>" id="show_description_image" catid="<?php echo $data->category->id;?>">
				<?php echo JSNLayoutHelper::showImage($data->category->getParams()->get('image'));?>
			</div>
		<?php endif;?>
		<?php if ( $data->category->description ):?>
			<div class="element-switch contextmenu-approved <?php echo $showDesTextClass;?>" id="show_description" catid="<?php echo $data->category->id;?>">
				<?php echo JHtml::_('content.prepare', JSNLayoutHelper::fixImageLinks( $data->category->description )); ?>
			</div>
		<?php endif;?>
	</div>
	<div class="cat-items">
		<?php include(dirname(__FILE__).'/default_articles.php'); ?>
	</div>
	<div class="jsn-article-separator"></div>
	<?php if (!empty($data->children[$data->category->id])&& $data->maxLevel != 0) : ?>
	<div class="cat-children element-switch contextmenu-approved display-default" id="sub-categories">
		<h3>
			<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
		</h3>
		<?php include( dirname(__FILE__).'/default_children.php' ); ?>
	</div>
	<?php endif; ?>
	<div class="jsn-article-separator"></div>
</div>
