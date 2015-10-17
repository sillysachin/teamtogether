<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default_articles.php 13922 2012-07-12 04:23:17Z thangbh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Create some shortcuts.
$params		= &$data->item->params;
$n			= count($data->items);
$listOrder	= htmlspecialchars($data->state->get('list.ordering'));
$listDirn	= htmlspecialchars($data->state->get('list.direction'));
?>
	<?php if (empty($data->items)) : ?>
		<?php $showNoArticlesClass = $data->params->get('show_no_articles',1) ? 'display-default display-item' : 'hide-item'; ?>
		<div class="no-articles  element-switch contextmenu-approved <?php echo $showNoArticlesClass;?>" id="show_no_articles"><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></div>
	<?php else : ?>
	<div class="clearbreak"></div>
	<div class="article-tablist contextmenu-approved" id="article-tablist">
		<?php
			if ($data->params->get('filter_field') == 'hide' || $data->params->get('filter_field') == ''){
				$filter_label = JText::_('COM_CONTENT_title_FILTER_LABEL');
				$showFilterClass = 'hide-item';
			}else{
				$filter_label = JText::_('COM_CONTENT_'.$data->params->get('filter_field').'_FILTER_LABEL');
				$showFilterClass = 'display-default display-item';
			}
		?>
		<div class="filter-search element-switch contextmenu-approved <?php echo $showFilterClass;?>" id="filter_field">
			<label class="filter-search-lbl" for="filter-search"><?php echo $filter_label.'&#160;'; ?></label>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo htmlspecialchars($data->state->get('list.filter')); ?>" class="inputbox" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
		</div>
		<?php $showPaginationLimitClass = $data->params->get('show_pagination_limit') ? 'display-default display-item' : 'hide-item'; ?>
		<div id="show_pagination_limit" class="display-limit element-switch contextmenu-approved <?php echo $showPaginationLimitClass;?>">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $data->pagination->getLimitBox(); ?>
		</div>
		<div class="clearbreak"></div>
		<!-- List items -->
		<div class="article-table-listing">
			<?php $showHeadingTablistClass = $data->params->get('show_headings') ? 'display-default display-item' : 'hide-item'; ?>
			<div class="table-header element-switch contextmenu-approved <?php echo $showHeadingTablistClass;?>" id="show_headings">
				<div class="table-header-container">
					<div class="list-title" id="tableOrdering">
						<label class="row-index">#</label> <?php  echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder) ; ?>
					</div>
					<?php $date = $data->params->get('list_show_date'); ?>
					<?php $showListDateClass = $data->params->get('list_show_date') ? 'display-default display-item' : 'hide-item'; ?>
					<div class="list-date element-switch <?php echo $showListDateClass;?>" id="list_show_date">
						<?php if ($date == "created") : ?>
							<?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.created', $listDirn, $listOrder); ?>
						<?php elseif ($date == "modified") : ?>
							<?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.modified', $listDirn, $listOrder); ?>
						<?php else : ?>
							<?php echo JHtml::_('grid.sort', 'COM_CONTENT_published_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
						<?php endif; ?>
					</div>
					<?php $showAuthorListClass = $data->params->get('list_show_author', 1) ? 'display-default display-item' : 'hide-item'; ?>
					<div class="list-author element-switch <?php echo $showAuthorListClass;?>" id="list_show_author">
						<?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
					</div>
					<?php $showHitsClass = $data->params->get('list_show_hits',1) ? 'display-default  display-item' : 'hide-item';?>
					<div class="list-hits element-switch <?php echo $showHitsClass;?>" id="list_show_hits">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
					</div>
					<div class="clearbreak"></div>
				</div>
			</div>
			<?php
			$k = 1;
			foreach ($data->items as $i => $article) : ?>
				<div class="article-item row-<?php echo $k;?>" >
					<div class="list-title">
						<label class="row-index"><?php echo $k++;?></label>
						<?php echo htmlspecialchars($article->title); ?>
					</div>
					<div class="list-date element-switch contextmenu-approved <?php echo $showListDateClass;?>" id="list_show_date_<?php echo $article->id;?>">
						<?php echo JHtml::_('date',$article->displayDate, htmlspecialchars($data->params->get('date_format', JText::_('DATE_FORMAT_LC3')))); ?>
					</div>
					<div class="list-author element-switch contextmenu-approved <?php echo $showAuthorListClass;?>"  id="list_show_author_<?php echo $article->id;?>">
						<?php $author =  $article->author ?>
						<?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>

						<?php if ($data->params->get('link_author') == true):?>
							<a><?php echo $author;?></a>
						<?php else :?>
							<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
						<?php endif; ?>
					</div>
					<div class="list-hits element-switch contextmenu-approved <?php echo $showHitsClass;?>"  id="list_show_hits_<?php echo $article->id;?>">
						<?php echo $article->hits; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="clearbreak"></div>
		</div>
	</div>
	<?php endif;?>
	<div class="jsn-article-separator"></div>
	<!-- Show pagination -->
	<?php $showPaginationClass = (($data->params->def('show_pagination', 1) == 1  || ($data->params->get('show_pagination') == 2)) && ($data->pagination->get('pages.total') > 1))  ? 'display-default display-item' : 'hide-item'; ?>
	<div class="jsn-rawmode-pagination">
		<div id="show_pagination" class="pagination-links <?php echo $showPaginationClass;?> element-switch contextmenu-approved">
			<?php echo $data->pagination ? $data->pagination->getPagesLinks() : ''; ?>
		</div>
		<div class="clearbreak"></div>
		<?php $showPaginationResultsClass = ($data->params->def('show_pagination_results', 1)) ? 'display-default display-item' : 'hide-item'; ?>
		<p id="show_pagination_results" class="counter element-switch contextmenu-approved <?php echo $showPaginationResultsClass;?>">
			<?php echo $data->pagination ? $data->pagination->getPagesCounter() : '' ; ?>
		</p>
	</div>