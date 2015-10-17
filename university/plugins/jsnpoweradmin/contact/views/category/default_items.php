<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.framework');

$listOrder	= $data->state->get('list.ordering');
$listDirn	= $data->state->get('list.direction');
$items 		= $data->items;
?>
<?php if (empty($items)) : ?>
	<p> <?php echo JText::_('COM_CONTACT_NO_ARTICLES'); ?>	 </p>
<?php else : ?>

	<form action="javascript:void(0)" method="post" name="adminForm" id="adminForm">

	<?php $filter =  ($params->get('filter_field') != 'hide' || $params->get('show_pagination_limit')) ? 'display-default display-item' : 'hide-item'; ?>
	<div id="filter"   class=" element-switch  <?php echo $filter?>">

			<?php $filterField =  $params->get('filter_field') != 'hide' ? 'display-default display-item' : 'hide-item'; ?>
			<div id="filter_field" parname="filter_field"  class="show-category element-switch contextmenu-approved <?php echo $filterField?> pull-left">
					<label class="filter-search-lbl element-invisible" for="filter-search">
						<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span><?php echo JText::_('COM_CONTACT_FILTER_LABEL').'&#160;'; ?>
						</label>
					<input type="text" name="filter-search" id="filter-search" value="" class="inputbox" title="<?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC'); ?>"
						placeholder="<?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC'); ?>" />
			</div>


			<?php $showPaginationLimit =  $params->get('show_pagination_limit') ? 'display-default display-item' : 'hide-item'; ?>
			<div id="show_pagination_limit" parname="show_pagination_limit" style="width: 120px;"  class="show-category element-switch contextmenu-approved <?php echo $showPaginationLimit?> pull-right">
					<label for="limit" class="element-invisible">
						<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
					</label>
					<?php echo $pagination->getLimitBox(); ?>
			</div>
		<div class="clearbreak"></div>
	</div>

		<ul class="category list-striped">
			<?php foreach ($items as $i => $item) : ?>


					<?php if ($items[$i]->state == 0) : ?>
						<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
					<?php else: ?>
						<li class="cat-list-row<?php echo $i % 2; ?>" >
					<?php endif; ?>

						<span class="pull-right">
							<?php if (!empty($item->telephone)) : ?>
								<?php $showTelephoneHeading =  $params->get('show_telephone_headings') ? 'display-default display-item' : 'hide-item'; ?>
								<div id="show_telephone_headings" parname="show_telephone_headings"  class="show-category element-switch contextmenu-approved <?php echo $showTelephoneHeading?>">
								<?php echo JTEXT::sprintf('COM_CONTACT_TELEPHONE_NUMBER', $item->telephone); ?>
								</div>
							<?php endif; ?>

							<?php if (!empty ($item->mobile)) : ?>
								<?php $showMobileHeading =  $params->get('show_mobile_headings') ? 'display-default display-item' : 'hide-item'; ?>
								<div id="show_mobile_headings" parname="show_mobile_headings"  class="show-category element-switch contextmenu-approved <?php echo $showMobileHeading?>">
									<?php echo JTEXT::sprintf('COM_CONTACT_MOBILE_NUMBER', $item->mobile); ?>
								</div>
							<?php endif; ?>

							<?php if (!empty($item->fax) ) : ?>
								<?php $showFaxHeading =  $params->get('show_fax_headings') ? 'display-default display-item' : 'hide-item'; ?>
								<div id="show_fax_headings" parname="show_fax_headings"  class="show-category element-switch contextmenu-approved <?php echo $showFaxHeading?>">
								<?php echo JTEXT::sprintf('COM_CONTACT_FAX_NUMBER', $item->fax); ?><br/>
								</div>
							<?php endif; ?>
					</span>

					<p>
						<strong class="list-title">
							<a href="javascript:void(0)">
								<?php echo $item->name; ?></a>
							<?php if ($items[$i]->published == 0) : ?>
								<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
							<?php endif; ?>

						</strong>

						<?php $showPositionHeading =  $params->get('show_position_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div id="show_position_headings" parname="show_position_headings"  class="show-category element-switch contextmenu-approved <?php echo $showPositionHeading?>">
								<?php echo $item->con_position; ?>
						</div>

						<?php $showEmailHeading =  $params->get('show_email_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div id="show_email_headings" parname="show_email_headings"  class="show-category element-switch contextmenu-approved <?php echo $showEmailHeading?>">
								<?php echo $item->email_to; ?>
						</div>

						<?php if (!empty($item->suburb)) : ?>
						<?php $showSuburbHeading =  $params->get('show_suburb_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div id="show_suburb_headings" parname="show_suburb_headings"  class="show-category element-switch contextmenu-approved <?php echo $showSuburbHeading?>">
							<?php echo $item->suburb . ', '; ?>
						</div>
						<?php endif; ?>

						<?php if (!empty($item->state)) : ?>
						<?php $showStateHeading =  $params->get('show_state_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div id="show_state_headings" parname="show_state_headings"  class="show-category element-switch contextmenu-approved <?php echo $showStateHeading?>">
							<?php echo $item->state . ', '; ?>
						</div>
						<?php endif; ?>

						<?php if (!empty($item->country)) : ?>
						<?php $showCountryHeading =  $params->get('show_country_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div id="show_country_headings" parname="show_country_headings"  class="show-category element-switch contextmenu-approved <?php echo $showCountryHeading?>">
							<?php echo $item->country; ?>
						</div>
						<?php endif; ?>
					</p>
					</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($params->get('show_pagination')) : ?>
		<div class="pagination">
			<?php if ($params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $pagination->getPagesCounter(); ?>
			</p>
			<?php endif; ?>
			<?php echo $pagination->getPagesLinks(); ?>
		</div>
		<?php endif; ?>
</form>
<?php endif; ?>