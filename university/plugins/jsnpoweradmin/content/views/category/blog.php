<?php 
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: blog.php 14475 2012-07-27 09:40:40Z thangbh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html'); 
$JSNConfig = JSNFactory::getConfig();
if (!is_object($data)) return;
?>
<input type="hidden" name="category_id" id="category_id" value="<?php echo $data->category->id;?>" />
<div class="jsn-category-blog">
	<!-- Heading -->
	<?php $showPageHeadingClass = $data->params->get('show_page_heading', 1) == 1 ? 'display-default display-item' : 'hide-item'; ?>
	<div class="category heading">
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
		<?php if ( $data->category->getParams()->get('image') ):?>
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
	<div class="article-layout-grid contextmenu-approved" id="article-layout-grid">
		<!-- Leading items -->
		<?php $leadingcount = 0 ; ?>
		<?php if (!empty($data->lead_items)) : ?>
		<div class="article_layout items-leading">
			<?php foreach ($data->lead_items as &$item) : ?>
				<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
					<?php
						$item = &$item;
						include( dirname(__FILE__) . DS . 'blog_item.php' );
					?>
				</div>
				<?php
					$leadingcount++;
				?>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	
		<!-- Intro items -->	
		<?php if (!empty($data->intro_items)) : ?>
			<?php
				$introcount = count($data->intro_items);
				$counter = 0;
			?>
			<?php foreach ($data->intro_items as $key => &$item) : ?>
			<?php
				$key = ( $key - $leadingcount ) + 1;
				$rowcount = ( ((int) $key - 1 ) %	(int) $data->columns) +1;
				$row = $counter / $data->columns ;
		
				if ( $rowcount == 1 ) : ?>
			<div class="items-row <?php echo 'row-'.$row ; ?>">
			<?php endif; ?>
			<div class="article_layout item column-<?php echo $rowcount;?>" style="width:<?php echo floor(100/$data->columns).'%';?>">
				<div class="jsn-padding-small">
				<?php
					$item = &$item;
					include( dirname(__FILE__) . DS . 'blog_item.php' );
				?>
				<div class="clearbreak"></div>
				</div>
			</div>
			<?php $counter++; ?>
			<?php if (($rowcount == $data->columns) or ($counter == $introcount)): ?>
					<span class="row-separator"></span>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<div class="clearbreak"></div>
		<!-- Link items -->
		<?php if (!empty($data->link_items)) : ?>
		<div class="link-items">
			<?php 
				include( dirname(__FILE__) . DS . 'blog_links.php' );
			?>
		</div>
		<?php endif; ?>
	</div>
	<?php if (!empty($data->children[$data->category->id])&& $data->maxLevel != 0) : ?>
	<div class="cat-children element-switch contextmenu-approved display-default" id="sub-categories">
		<h3><?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
		<?php
			include( dirname(__FILE__) . DS . 'blog_children.php' );
		?>
	</div>
	<?php endif; ?>

	<!-- Show pagination -->
	<?php $showPaginationClass = (($data->params->def('show_pagination', 1) == 1  || ($data->params->get('show_pagination') == 2)) && ($data->pagination->get('pages.total') > 1))  ? 'display-default display-item' : 'hide-item'; ?>
	<div class="jsn-rawmode-pagination">
		<div id="show_pagination" class="pagination-links <?php echo $showPaginationClass;?> element-switch contextmenu-approved">
			<?php echo $data->pagination->getPagesLinks(); ?>
		</div>
		<div class="clearbreak"></div>
		<?php $showPaginationResultsClass = ($data->params->def('show_pagination_results', 1)) ? 'display-default display-item' : 'hide-item'; ?>
		<p id="show_pagination_results" class="counter element-switch contextmenu-approved <?php echo $showPaginationResultsClass;?>">
			<?php echo $data->pagination->getPagesCounter(); ?>
		</p>
	</div>
	<div class="jsn-article-separator"></div>		
</div>