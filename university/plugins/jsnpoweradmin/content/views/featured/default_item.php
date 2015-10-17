<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default_item.php 13922 2012-07-12 04:23:17Z thangbh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
// Create a shortcut for params.
$params = &$item->params;
$itemAttribs = json_decode($item->attribs);
$JSNConfig->megreMenuParams(JRequest::getVar('jsnCurrentItemid', 0), $params, $itemAttribs);

if(!is_object($params)){
	return;
}
?>
<!-- Show/hide title -->
<?php $showTitleClass = ($params->get('show_title')) ? 'display-default display-item' : 'hide-item'; ?>
<div class="intro-item">
	<div class="intro-item-title element-switch contextmenu-approved <?php echo $showTitleClass;?>" id="show_title_<?php echo $item->id;?>" link="<?php echo $params->get('link_titles');?>">
		<?php if ($params->get('link_titles')) : ?>
			<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
			<?php echo htmlspecialchars($item->title); ?></a>
		<?php else : ?>
			<?php echo htmlspecialchars($item->title); ?>
		<?php endif; ?>
	</div>
	<!-- Actions -->
	<ul class="actions">
		<?php $showEmailIconClass = ($params->get('show_email_icon')) ? 'display-default display-item' : 'hide-item'; ?>
		<li class="email-icon element-switch contextmenu-approved <?php echo $showEmailIconClass;?>" id="show_email_icon_<?php echo $item->id;?>" icon="<?php echo $params->get('show_icons');?>">
			<?php echo JHtml::_('icon.email', $item, $params); ?>
		</li>
		<?php $showPrintIconClass = ($params->get('show_print_icon')) ? 'display-default display-item' : 'hide-item'; ?>
		<li class="print-icon element-switch contextmenu-approved <?php echo $showPrintIconClass;?>" id="show_print_icon_<?php echo $item->id;?>" icon="<?php echo $params->get('show_icons');?>">
			<?php echo JHtml::_('icon.print_popup', $item, $params); ?>
		</li>
	</ul>
	<div class="clearbreak"></div>
</div>
<!--  Show/hide category  -->
<?php $showCategoryClass = $item->parent_slug != '1:root' ? 'display-default display-item' : 'hide-item'; ?>
<div class="show-category <?php echo $showCategoryClass;?>" >
	<!-- Show/hide parent category -->
	<?php
		$parentCategoryClass = $params->get('show_parent_category')  ? 'display-default display-item' : 'hide-item';
		@list($parent_category_id, $notuse) = explode(':', $item->parent_slug);
	?>
	<div class="item-category parent-category element-switch contextmenu-approved <?php echo $parentCategoryClass;?>" id="show_parent_category_<?php echo $item->id;?>" link="<?php echo $params->get('link_parent_category');?>" catid="<?php echo $parent_category_id;?>">
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
	<div class="item-category category-name element-switch contextmenu-approved <?php echo $show_category;?>" id="show_category_<?php echo $item->id;?>"  link="<?php echo $params->get('link_category');?>" catid="<?php echo $category_id;?>">
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
<!-- to do not that elegant would be nice to group the params -->

 <dl class="article-info">
	<dt class="article-info-term"><?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?></dt>
	<!-- Created by. Show/hide author -->
	<?php $createdByClass = ($params->get('show_author') && !empty($item->author )) ? 'display-default display-item' : 'hide-item'; ?>
	<dd class="createdby element-switch contextmenu-approved <?php echo $createdByClass;?>" id="show_author_<?php echo $item->id;?>" link="<?php echo $params->get('link_author');?>" contactid="<?php echo $item->contactid;?>" userid="<?php echo $item->contactid;?>">
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
	<div class="clearbreak"></div>
	<!-- Show create date. Show/hide create date -->
	<?php $createClass = $params->get('show_create_date') ? 'display-default display-item' : 'hide-item'; ?>
	<dd class="create element-switch contextmenu-approved <?php echo $createClass;?>" id="show_create_date_<?php echo $item->id;?>" >
		<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC2'))); ?>
	</dd>
	<div class="clearbreak"></div>
	<!-- Show/hide published date -->
	<?php $publishedClass = $params->get('show_publish_date') ? 'display-default display-item' : 'hide-item'; ?>
	<dd class="published element-switch contextmenu-approved <?php echo $publishedClass; ?>" id="show_publish_date_<?php echo $item->id;?>">
		<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHtml::_('date',$item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
	</dd>
	<div class="clearbreak"></div>
	<!-- Show/hide hits -->
	<?php $hitsClass = $params->get('show_hits') ? 'display-default display-item' : 'hide-item'; ?>
	<dd class="hits element-switch contextmenu-approved <?php echo $hitsClass;?>" id="show_hits_<?php echo $item->id;?>">
		<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $item->hits); ?>
	</dd>
</dl>
<div class="clearbreak"></div>
<!-- Show Rating -->
<?php $voteClass = $params->get('show_vote') ? 'display-default display-item' : 'hide-item';?>
<div class="show_vote element-switch contextmenu-approved <?php echo $voteClass;?>" id="show_vote_<?php echo $item->id;?>">
	<?php echo JSNLayoutHelper::showArticleRating( $item->rating, $item->rating_count ); ?>
</div>
<div class="clearbreak"></div>
<!-- Introtext -->
<div class="article_text element-switch contextmenu-approved display-default" id="show_intro_<?php echo $item->id;?>">
	<?php echo JSNLayoutHelper::fixImageLinks( $item->introtext ); ?>
</div>
<div class="clearbreak"></div>
<?php if ($item->readmore) : ?>
	<?php $showReadmore = $params->get('show_readmore') ? 'display-default display-item' : 'hide-item';
		$showReadmoreTitle  = $params->get('show_readmore_title', 0) == 0 ? 'hide-item' : 'display-default display-item';
	?>
	<div class="readmore element-switch contextmenu-approved <?php echo $showReadmore;?>" id="show_readmore_<?php echo $item->id;?>">
		<a><?php echo JText::_('COM_CONTENT_READ_MORE');?><span style="display: inline-block;" class="element-switch <?php echo $showReadmoreTitle?>" ><?php echo $item->title?></span></a>
	</div>
<?php endif;?>
<!-- Show modified date -->
<?php $modifiedClass = $params->get('show_modify_date') ? 'display-default display-item' : 'hide-item'; ?>
  <div class="modified element-switch contextmenu-approved <?php echo $modifiedClass;?>" id="show_modify_date_<?php echo $item->id;?>">
	<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date',$item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
  </div>
<div class="item-separator"></div>