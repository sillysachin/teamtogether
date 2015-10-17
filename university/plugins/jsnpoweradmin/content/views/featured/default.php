<?php 
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 14475 2012-07-27 09:40:40Z thangbh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$JSNConfig = JSNFactory::getConfig();

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>
<div class="jsn-category-blog">
	<!-- Heading -->
	<?php $showPageHeadingClass = $data->params->get('show_page_heading', 1) == 1 ? 'display-default display-item' : 'hide-item'; ?>
	<div class="category heading">
		<div class="<?php echo $showPageHeadingClass;?> element-switch contextmenu-approved" id="show_page_heading">
			<?php echo htmlspecialchars($data->params->get('page_heading')); ?>
		</div>
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
						include( dirname(__FILE__) . DS . 'default_item.php' );
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
				$row = $counter / $data->columns;
		
				if ( $rowcount == 1 ) : ?>
			<div class="items-row <?php echo 'row-'.$row ; ?>">
			<?php endif; ?>
			<div class="article_layout item column-<?php echo $rowcount;?>" style="width:<?php echo floor(100/$data->columns).'%';?>">
				<div class="jsn-padding-small">
				<?php
					$item = &$item;
					include( dirname(__FILE__) . DS . 'default_item.php' );
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
				include( dirname(__FILE__) . DS . 'default_links.php' );
			?>
		</div>
		<?php endif; ?>
	</div>

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
</div>