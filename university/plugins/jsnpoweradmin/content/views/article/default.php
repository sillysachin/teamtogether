<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 13922 2012-07-12 04:23:17Z thangbh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
// Create shortcuts to some parameters.
$item    = &$data;
$params  = $item->params;
$canEdit = $item->params->get('access-edit');
$user	 = JFactory::getUser();
if(!is_object($params)){
	return;
}
?>
<div class="jsn-article-layout">
	<!-- Show/hide category -->
	<?php $showCategoryClass = $item->parent_slug != '1:root' ? 'display-default display-item' : 'hide-item'; ?>
	<div class="show-category <?php echo $showCategoryClass;?>" >
		<!-- Show/hide parent category -->
		<?php
			$parentCategoryClass = $params->get('show_parent_category')  ? 'display-default display-item' : 'hide-item';
			@list($parent_category_id, $notuse) = explode(':', $item->parent_slug);
		?>
		<div class="parent-category element-switch contextmenu-approved <?php echo $parentCategoryClass;?>" id="show_parent_category" link="<?php echo $params->get('link_parent_category');?>" catid="<?php echo $parent_category_id;?>">
			<?php
				$title = htmlspecialchars($item->parent_title);
				$url = '<a>'.$title.'</a>';

				if ($params->get('link_parent_category') AND $item->parent_slug) :
					echo JText::sprintf('COM_CONTENT_PARENT', $url);
				else :
					echo JText::sprintf('COM_CONTENT_PARENT', $title);
				endif;
			?>
		</div>
		<!-- Show/hide category name -->
		<?php
			$show_category = $params->get('show_category') ?  'display-default display-item' : 'hide-item';
			@list($category_id, $notuse) = explode(':', $item->catslug);
		?>
		<div class="category-name element-switch contextmenu-approved <?php echo $show_category;?>" id="show_category"  link="<?php echo $params->get('link_category');?>" catid="<?php echo $category_id;?>">
			<?php
				 $title = htmlspecialchars($item->category_title);
				 $url = '<a>'.$title.'</a>';
				 if ($params->get('link_category')) :
					echo JText::sprintf('COM_CONTENT_CATEGORY', $url);
				 else :
					 echo JText::sprintf('COM_CONTENT_CATEGORY', $title);
				 endif;
			 ?>
		</div>
	</div>
	<div class="clearbreak"></div>
	<!-- Show/hide title -->
	<?php $titleClass = $params->get('show_title') ? 'display-default display-item' : 'hide-item'; ?>
	<div class="article-header-title element-switch contextmenu-approved <?php echo $titleClass;?>" id="show_title" link="<?php echo $params->get('link_titles');?>">
		<?php if ($params->get('link_titles')): ?>
			<a><?php echo htmlspecialchars($item->title); ?></a>
		<?php else : ?>
			<?php echo htmlspecialchars($item->title); ?>
		<?php endif; ?>
	</div>
	<div class="clearbreak"></div>
	<!-- Show/hide print/email/edit icon -->
	<ul class="actions " id="show_icons">
		<?php $emailIconClass = $params->get('show_email_icon') ? 'display-default display-item' : 'hide-item'; ?>
		<li class="email-icon element-switch contextmenu-approved <?php echo $emailIconClass;?>" id="show_email_icon"  icon="<?php echo $params->get('show_icons');?>">
			<?php echo JHtml::_('icon.email', $item, $params); ?>
		</li>
		<?php $printIconClass = $params->get('show_print_icon') ? 'display-default display-item' : 'hide-item';?>
		<li class="print-icon element-switch contextmenu-approved <?php echo $printIconClass;?>" id="show_print_icon"  icon="<?php echo $params->get('show_icons');?>">
			<?php echo JHtml::_('icon.print_popup', $item, $params); ?>
		</li>
	</ul>
	<div class="clearbreak"></div>
	<!-- Show/hide useDefList -->
	<dl class="article-info">
		<dt class="article-info-term"><?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?></dt>
		<!-- Created by. Show/hide author -->
		<?php $createdByClass = $params->get('show_author') ? 'display-default display-item' : 'hide-item'; ?>
		<dd class="createdby element-switch contextmenu-approved <?php echo $createdByClass;?>" id="show_author" link="<?php echo $params->get('link_author');?>" contactid="<?php echo $item->contactid;?>" userid="<?php echo $item->contactid;?>">
			<?php
				$author = $item->created_by_alias ? $item->created_by_alias : $item->author;
			 	if ($params->get('link_author') && $item->contactid):
					$url = '<a href="'.JURI::root().'administrator/index.php?option=com_contact&task=contact.edit&id=' . $item->contactid.'" target="_blank">'.$author.'</a>';
					echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $url);
				else:
					echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author);
			 	endif;
			 ?>
		</dd>
		<!-- Show create date. Show/hide create date -->
		<?php $createClass = $params->get('show_create_date') ? 'display-default display-item' : 'hide-item'; ?>
		<dd class="create element-switch contextmenu-approved <?php echo $createClass;?>" id="show_create_date" >
			<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<!-- Show/hide published date -->
		<?php $publishedClass = $params->get('show_publish_date') ? 'display-default display-item' : 'hide-item'; ?>
		<dd class="published element-switch contextmenu-approved <?php echo $publishedClass; ?>" id="show_publish_date">
			<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHtml::_('date',$item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<!-- Show/hide hits -->
		<?php $hitsClass = $params->get('show_hits') ? 'display-default display-item' : 'hide-item'; ?>
		<dd class="hits element-switch contextmenu-approved <?php echo $hitsClass;?>" id="show_hits">
			<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $item->hits); ?>
		</dd>
	</dl>
	<div class="clearbreak"></div>
	<!-- Show Rating -->
	<?php $voteClass = $params->get('show_vote') ? 'display-default display-item' : 'hide-item';?>
	<div class="show_vote element-switch contextmenu-approved <?php echo $voteClass;?>" id="show_vote">
		<?php echo JSNLayoutHelper::showArticleRating( $item->rating, $item->rating_count ); ?>
	</div>
	<div class="clearbreak"></div>
	<!-- Show/hide toc -->
	<?php $tocClass  = isset ($item->toc) ? 'display-default display-item' : 'hide-item'; ?>
	<?php $item->toc = isset ($item->toc) ? $item->toc : '';?>
	<div class="toc element-switch contextmenu-approved <?php echo $tocClass;?>">
		<?php echo $item->toc; ?>
	</div>
	<div class="clearbreak"></div>
	<!-- Introtext -->
	<?php if ($item->fulltext != null and $item->introtext != ''):?>
		<?php $showIntrotextClass = $params->get('show_intro') ? 'display-default display-item' : 'hide-item';?>
		<div class="introtext element-switch contextmenu-approved <?php echo $showIntrotextClass;?>" id="show_intro">
			<?php echo JSNLayoutHelper::fixImageLinks( $item->introtext ); ?>
		</div>
		<div class="clearbreak"></div>
		<!-- Main text -->
		<?php if ($item->fulltext):?>
			<div class="article_text element-switch contextmenu-approved display-default" id="main_text">
				<?php echo JSNLayoutHelper::fixImageLinks( $item->fulltext ); ?>
			</div>
		<?php endif;?>
	<?php else: ?>
		<div class="article_text element-switch contextmenu-approved display-default" id="show_intro">
			<?php echo JSNLayoutHelper::fixImageLinks( $item->introtext ); ?>
		</div>
	<?php endif;?>
  	<div class="clearbreak"></div>
  	<!-- Show pagenavigation -->
  	<?php $classNavigation = $params->get('show_item_navigation') ? 'display-default display-item' : 'hide-item'; ?>
  	<div class="show_item_navigation element-switch contextmenu-approved <?php echo $classNavigation;?>" id="show_item_navigation">
  	 	<?php echo JSNLayoutHelper::showNavigation( $item, $params );?>
  	</div>
  	<div class="clearbreak"></div>
  	<!-- Show/hide modify date -->
  	<?php $modifiedClass = $params->get('show_modify_date') ? 'display-default display-item' : 'hide-item'; ?>
  	<div class="modified element-switch contextmenu-approved <?php echo $modifiedClass;?>" id="show_modify_date">
		<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date',$item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
  	</div>
  	<input type="hidden" name="articleId" id="articleId" value="<?php echo $item->id;?>" />
</div>