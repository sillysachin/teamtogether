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
$state		= $data->state;
$listOrder	= htmlentities(htmlspecialchars($state->get('list.ordering')));
$listDirn	= htmlentities(htmlspecialchars($state->get('list.direction')));

?>

<?php if (empty($items)) : ?>
	<p> <?php echo JText::_('COM_CONTACT_NO_CONTACTS'); ?>	 </p>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	<?php if ($params->get('show_pagination_limit')) : ?>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $pagination->getLimitBox(); ?>
		</div>
	<?php endif; ?>
	</fieldset>

	<div class="article-table-listing">
		<table style="width: 100%" class="table-listing-records">
			<thead>
				<?php $showHeadings = $params->get('show_headings') ? 'display-default display-item' : 'hide-item'; ?>
				<tr>
					<th style="width: 1%">
						<?php echo JText::_('JGLOBAL_NUM'); ?>
					</th>
					<th style="width: 20%">
						<?php echo JText::_('COM_CONTACT_CONTACT_EMAIL_NAME_LABEL'); ?>
					</th>
					<th style="width:9%">
					<?php $showPosistionHeadings = $params->get('show_position_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_position_headings" id="show_position_headings" class="item-position element-switch contextmenu-approved <?php echo $showPosistionHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_POSITION'); ?>
					</div>
					</th>
					<th style="width:9%">
					<?php $showEmailHeadings = $params->get('show_email_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_email_headings" id="show_email_headings" class="item-email element-switch contextmenu-approved <?php echo $showEmailHeadings;?>"  >
						<?php echo JText::_('JGLOBAL_EMAIL'); ?>
					</div>
					</th>
					<th style="width:9%">

					<?php $showTelephoneHeadings = $params->get('show_telephone_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_telephone_headings" id="show_telephone_headings" class="item-telephone element-switch contextmenu-approved <?php echo $showTelephoneHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_TELEPHONE'); ?>
					</div>
					</th>
					<th style="width:9%">

					<?php $showMobileHeadings = $params->get('show_mobile_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_mobile_headings" id="show_mobile_headings" class="item-mobile element-switch contextmenu-approved <?php echo $showMobileHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_MOBILE'); ?>
					</div>
					</th>
					<th style="width:9%">
					<?php $showFaxHeadings = $params->get('show_fax_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_fax_headings" id="show_fax_headings" class="item-fax element-switch contextmenu-approved <?php echo $showFaxHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_FAX'); ?>
					</div>
					</th>
					<th style="width:9%">

					<?php $showSuburbHeadings = $params->get('show_suburb_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_suburb_headings" id="show_suburb_headings" class="item-suburb element-switch contextmenu-approved <?php echo $showSuburbHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_SUBURB'); ?>
					</div>

					</th>
					<th style="width:9%">
					<?php $showStateHeadings = $params->get('show_state_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_state_headings" id="show_state_headings" class="item-state element-switch contextmenu-approved <?php echo $showStateHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_STATE'); ?>
					</div>
					</th>
					<th style="width:9%">

					<?php $showCountryHeadings = $params->get('show_country_headings') ? 'display-default display-item' : 'hide-item'; ?>
					<div parname="show_country_headings" id="show_country_headings" class="item-country element-switch contextmenu-approved <?php echo $showCountryHeadings;?>"  >
						<?php echo JText::_('COM_CONTACT_COUNTRY'); ?>
					</div>
					</th>
			</thead>
			<tbody>
				<?php foreach($items as $i => $item) : ?>
				<tr>
						<td class="item-num">
							<?php echo $i; ?>
						</td>
						</td>
						<td style="width: 20%;padding: 5px;">
						<div class="item-name record">
							<a href="javscript:void(0)" >
								<?php echo $item->name; ?></a>
						</div>
						</td>
						<td style="width: 9%">
						<?php $showPosistionHeadings = $params->get('show_position_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_position_headings" id="show_position_headings" class="record item-position element-switch contextmenu-notapproved <?php echo $showPosistionHeadings;?>" >
								<?php echo $item->con_position; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showEmailHeadings = $params->get('show_email_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_email_headings" id="show_email_headings" class="record item-email element-switch contextmenu-notapproved <?php echo $showEmailHeadings;?>" >
								<?php echo $item->email_to; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showTelephoneHeadings = $params->get('show_telephone_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_telephone_headings" id="show_telephone_headings" class="record item-telephone element-switch contextmenu-notapproved <?php echo $showTelephoneHeadings;?>" >
								<?php echo $item->telephone; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showMobileHeadings = $params->get('show_mobile_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_mobile_headings" id="show_mobile_headings" class="record item-mobile element-switch contextmenu-notapproved <?php echo $showMobileHeadings;?>" >
								<?php echo $item->mobile; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showFaxHeadings = $params->get('show_fax_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_fax_headings" id="show_fax_headings" class="record item-fax element-switch contextmenu-notapproved <?php echo $showFaxHeadings;?>" >
							<?php echo $item->fax; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showSuburbHeadings = $params->get('show_suburb_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_suburb_headings" id="show_suburb_headings" class="record item-suburb element-switch contextmenu-notapproved <?php echo $showSuburbHeadings;?>" >
							<?php echo $item->suburb; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showStateHeadings = $params->get('show_state_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_state_headings" id="show_state_headings" class="record item-state element-switch contextmenu-notapproved <?php echo $showStateHeadings;?>" >
							<?php echo $item->state; ?>
						</div>
						</td>
						<td  style="width: 9%">
						<?php $showCountryHeadings = $params->get('show_country_headings') ? 'display-default display-item' : 'hide-item'; ?>
						<div parname="show_country_headings" id="show_country_headings" class="record item-country element-switch contextmenu-notapproved <?php echo $showCountryHeadings;?>" >
							<?php echo $item->country; ?>
						</div>
						</td>
					</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</form>
<?php endif; ?>
