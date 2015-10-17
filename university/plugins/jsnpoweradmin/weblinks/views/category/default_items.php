<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for weblinks
// Create a shortcut for params.
$n = count($items);
$listOrder	= htmlspecialchars($state->get('list.ordering'));
$listDirn	= htmlspecialchars($state->get('list.direction'));
?>

<?php if (empty($items)) : ?>
	<p> <?php echo JText::_('COM_WEBLINKS_NO_WEBLINKS'); ?></p>
<?php else : ?>

<form action="javascript:void(0)" method="post" name="adminForm" id="adminForm">

	<?php $showPaginationLimit = $params->get('show_pagination_limit') ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_pagination_limit" id="show_pagination_limit" class="element-switch contextmenu-approved <?php echo $showPaginationLimit;?>" >
		<fieldset class="filters">
		<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $pagination->getLimitBox(); ?>
		</div>
		</fieldset>
	</div>


	<div style="padding: 2px; border: #cccccc solid 1px;">
		<?php $showHeadings = ($params->get('show_headings') == 1) ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_headings" id="show_headings" class="element-switch contextmenu-approved <?php echo $showHeadings;?>"  style="background: #cccccc">
				<div style="float: left;width: 80%">
						<?php echo JText::_('COM_WEBLINKS_GRID_TITLE'); ?>
				</div>
				<?php $showLinkHits = $params->get('show_link_hits') ? 'display-default display-item' : 'hide-item'; ?>
				<div parname="show_link_hits" id="show_link_hits" style="float: left;width: 15%" class="parent-category element-switch contextmenu-approved <?php echo $showLinkHits;?>" >
						<?php echo JText::_('JGLOBAL_HITS'); ?>
				</div>
				<div class="clearbreak"></div>
		</div>

		<div>
		<?php foreach ($items as $i => $item) : ?>
				<div style="float: left;width: 80%">
					<p>
						<?php if ($params->get('icons') == 0) : ?>
							 <?php echo JText::_('COM_WEBLINKS_LINK'); ?>
						<?php elseif ($params->get('icons') == 1) : ?>
							<?php if (!$params->get('link_icons')) : ?>
								<?php echo JHtml::_('image', JSNLayoutHelper::fixImageLinks( 'system/'.$params->get('link_icons', 'weblink.png') ), JText::_('COM_WEBLINKS_LINK'), NULL, true); ?>
							<?php else: ?>
								<?php echo '<img src="'.JSNLayoutHelper::fixImageLinks($params->get('link_icons')).'" alt="'.JText::_('COM_WEBLINKS_LINK').'" />'; ?>
							<?php endif; ?>
						<?php endif; ?>
						 <a href="javscript:void(0)"><?php echo htmlspecialchars($item->title)?></a>
					</p>

					<?php if ($item->description !=''): ?>
					<?php $showLinkDescription = $params->get('show_link_description') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_link_description" id="show_link_description" class="element-switch contextmenu-approved <?php echo $showLinkDescription;?>" >
						<?php echo $item->description; ?>
					</div>
					<?php endif; ?>
				</div>
				<div style="float: left;width: 15%">
					<?php $showLinkHits = $params->get('show_link_hits') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_link_hits" id="show_link_hits" class="parent-category element-switch contextmenu-approved <?php echo $showLinkHits;?>" >
						<?php echo $item->hits; ?>
					</div>
				</div>
			<div class="clearbreak"></div>
		<?php endforeach; ?>
		</div>
	</div>

	<?php if ($pagination->getPagesCounter() || $pagination->getPagesLinks()){?>
	 <?php $showPagination = $params->get('show_pagination') ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_pagination" id="show_pagination" class="element-switch contextmenu-approved <?php echo $showPagination;?>" >

		<?php $showPaginationResults = $params->def('show_pagination_results', 1) ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_pagination_results" id="show_pagination_results" style="float: left;" class="parent-category element-switch contextmenu-approved <?php echo $showPaginationResults;?>" >
			<p class="counter">
				<?php echo $pagination->getPagesCounter(); ?>
			</p>
		</div>
		<?php echo $pagination->getPagesLinks(); ?>
		<div class="clearbreak"></div>
	</div>
	<?php }?>
</form>
<?php endif; ?>
