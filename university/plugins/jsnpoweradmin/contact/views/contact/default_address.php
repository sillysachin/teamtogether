<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<?php if (($params->get('address_check') > 0) &&  ($contact->address || $contact->suburb  || $contact->state || $contact->country || $contact->postcode)) : ?>
	<div class="contact-address">
	<?php if ($params->get('address_check') > 0) : ?>
		<b class="<?php echo $params->get('marker_class'); ?>" >
			<?php echo $params->get('marker_address'); ?>
		</b>
		<address>
	<?php endif; ?>
	<?php if ($contact->address) : ?>
	<?php $showStreetAdress = $params->get('show_street_address') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_street_address" id="show_street_address" class="element-switch contextmenu-approved <?php echo $showStreetAdress;?>" >
			<?php echo nl2br($contact->address); ?>
		</div>
	<?php endif; ?>
	<?php if ($contact->suburb) : ?>
	<?php $showSuburb = $params->get('show_suburb') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_suburb" id="show_suburb" class="element-switch contextmenu-approved <?php echo $showSuburb;?>" >
			<?php echo $contact->suburb; ?>
		</div>
	<?php endif; ?>
	<?php if ($contact->state) : ?>
	<?php $showState = $params->get('show_state') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_state" id="show_state" class="element-switch contextmenu-approved <?php echo $showState;?>" >
			<?php echo $contact->state; ?>
		</div>
	<?php endif; ?>
	<?php if ($contact->postcode) : ?>
	<?php $showPostcode = $params->get('show_postcode') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_postcode" id="show_postcode" class="element-switch contextmenu-approved <?php echo $showPostcode;?>" >
			<?php echo $contact->postcode; ?>
		</div>
	<?php endif; ?>
	<?php if ($contact->country) : ?>
	<?php $showCountry = $params->get('show_country') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_country" id="show_country" class="element-switch contextmenu-approved <?php echo $showCountry;?>" >
			<?php echo $contact->country; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($params->get('address_check') > 0) : ?>
	</address>
	</div>
<?php endif; ?>


<div>
<?php if ($contact->email_to) : ?>
<?php $showEmail = $params->get('show_email') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_email" id="show_email" class="element-switch contextmenu-approved <?php echo $showEmail;?>" >
	<p>
		<?php echo $params->get('marker_email'); ?>
		<?php echo $contact->email_to; ?>
	</p>
</div>
<?php endif; ?>

<?php if ($contact->telephone) : ?>
<?php $showTelephone = $params->get('show_telephone') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_telephone" id="show_telephone" class="element-switch contextmenu-approved <?php echo $showTelephone;?>" >
	<p>
		<?php echo $params->get('marker_telephone'); ?>
		<?php echo nl2br($contact->telephone); ?>
	</p>
</div>
<?php endif; ?>
<?php if ($contact->fax) : ?>
<?php $showFax = $params->get('show_fax') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_fax" id="show_fax" class="element-switch contextmenu-approved <?php echo $showFax;?>" >
	<p>
		<?php echo $params->get('marker_fax'); ?>
		<?php echo nl2br($contact->fax); ?>
	</p>
</div>
<?php endif; ?>
<?php if ($contact->mobile) :?>
<?php $showMobile = $params->get('show_mobile') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_mobile" id="show_mobile" class="element-switch contextmenu-approved <?php echo $showMobile;?>" >
	<p>
		<?php echo $params->get('marker_mobile'); ?>
		<?php echo nl2br($contact->mobile); ?>
	</p>
</div>
<?php endif; ?>
<?php if ($contact->webpage) : ?>
<?php $showWebpage = $params->get('show_webpage') ? 'display-default display-item' : 'hide-item'; ?>
<div parname="show_webpage" id=show_webpage class="element-switch contextmenu-approved <?php echo $showWebpage;?>" >
	<p>
			<a href="<?php echo $contact->webpage; ?>" target="_blank">
			<?php echo $contact->webpage; ?></a>
	</p>
</div>
<?php endif; ?>

</div>

