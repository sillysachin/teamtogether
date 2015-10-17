<?php

defined('_JEXEC') or die;

//$cparams = JComponentHelper::getParams ('com_media');
$item 	= $data->item;
$params	= $data->params;
$contact 	= $item;
$contacts	= $data->contacts;
?>
<input type="hidden" name="contact_id" id="contact_id" value="<?php echo $contact->id?>"  />
<div class="jsn-rawmode-component-category-list" id="jsn-rawmode-component-category-list">
<div class="contact">
<?php $showPageHeading = $params->get('show_page_heading') ? 'display-default display-item' : 'hide-item'; ?>
<?php if ($params->get('page_heading')) {?>
<div parname="show_page_heading" id="show_page_heading" class="element-switch contextmenu-approved <?php echo $showPageHeading;?>" >
<h1>
	<?php echo $params->get('page_heading'); ?>
</h1>
</div>
<?php }?>

	<?php if ($contact->name) : ?>
	<?php $showName = $params->get('show_name') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_name" id="show_name" class="element-switch contextmenu-approved <?php echo $showName;?>" >
		<h2>
			<?php echo $contact->name; ?>
		</h2>
		</div>
	<?php endif;  ?>
	<?php if ($params->get('show_contact_category') == 'show_no_link') : ?>
		<h3>
			<?php echo $contact->category_title; ?>
		</h3>
	<?php endif; ?>
	<?php if ($params->get('show_contact_category') == 'show_with_link') : ?>
		<?php $contactLink = ContactHelperRoute::getCategoryRoute($contact->catid);?>
		<h3>
			<a href="javascript:void(0)"><?php echo $contact->category_title; ?></a>
		</h3>
	<?php endif; ?>
	<?php if (count($contacts) > 1) : ?>
	<?php $showContactList = $params->get('show_contact_list') ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_contact_list" id="show_contact_list" class="element-switch contextmenu-approved <?php echo $showContactList;?>" >
			<?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?>
			<?php echo JHtml::_('select.genericlist',  $contacts, 'id', 'class="inputbox" ', 'link', 'name', $contact->link);?>
	</div>
	<?php endif; ?>



	<?php  echo '<h3>'. JText::_('COM_CONTACT_DETAILS').'</h3>';  ?>

	<?php if ($contact->image) : ?>
		<?php $showImage = $params->get('show_image') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_image" id="show_image" class="element-switch contextmenu-approved <?php echo $showImage;?>" >
			<img style="width: 150px;" src="<?php echo JUri::root() . '/' . JSNLayoutHelper::fixImageLinks($contact->image); ?>"/>
		</div>
	<?php endif; ?>

	<?php if ($contact->con_position) : ?>
	<?php $showPosition = $params->get('show_position') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_position" id="show_position" class="element-switch contextmenu-approved <?php echo $showPosition;?>" >
			<p class="contact-position"><?php echo $contact->con_position; ?></p>
		</div>
	<?php endif; ?>

	<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/contact/default_address.php';?>

	<?php $allowVcard = $params->get('allow_vcard') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="allow_vcard" id="allow_vcard" class="element-switch contextmenu-approved <?php echo $allowVcard;?>" >
		<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
			<a href="javscript:void(0)">
			<?php echo JText::_('COM_CONTACT_VCARD');?></a>
		</div>

	<p></p>
	<?php if ($contact->email_to || $contact->user_id) : ?>
	<?php $allowEmailForm = $params->get('show_email_form') ? 'display-default display-item' : 'hide-item'; ?>
	<div parname="show_email_form" id="show_email_form" class="element-switch contextmenu-approved <?php echo $allowEmailForm;?>" >
	<?php  echo '<h3>'. JText::_('COM_CONTACT_EMAIL_FORM').'</h3>';  ?>

	<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/contact/default_form.php';?>
	</div>
	<?php endif; ?>


	<?php $showLinks = $params->get('show_links') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_links" id="show_links" class="element-switch contextmenu-approved <?php echo $showLinks;?>" >
		<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/contact/default_links.php';?>
		</div>

	<?php if ($contact->user_id && $contact->articles) : ?>
	<?php $showArticles = $params->get('show_articles') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_articles" id="show_articles" class="element-switch contextmenu-approved <?php echo $showArticles;?>" >
			<?php echo '<h3>'. JText::_('JGLOBAL_ARTICLES').'</h3>'; ?>
			<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/contact/default_articles.php';?>
		</div>
	<?php endif; ?>

	<?php if ($contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
	<?php $showProfile = $params->get('show_profile') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_profile" id="show_profile" class="element-switch contextmenu-approved <?php echo $showProfile;?>" >
			<?php echo '<h3>'. JText::_('COM_CONTACT_PROFILE').'</h3>'; ?>
			<?php include JPATH_ROOT . '/plugins/jsnpoweradmin/contact/views/contact/default_frofile.php';?>
		</div>
	<?php endif; ?>

	<?php if ($contact->misc) : ?>
	<?php $showMisc = $params->get('show_misc') ? 'display-default display-item' : 'hide-item'; ?>
		<div parname="show_misc" id="show_misc" class="element-switch contextmenu-approved <?php echo $showMisc;?>" >
			<?php echo '<h3>'. JText::_('COM_CONTACT_OTHER_INFORMATION').'</h3>'; ?>
				<div class="contact-miscinfo">
					<div class="<?php echo $params->get('marker_class'); ?>">
						<?php echo $params->get('marker_misc'); ?>
					</div>
					<div class="contact-misc">
						<?php echo $contact->misc; ?>
					</div>
				</div>
		</div>
	<?php endif; ?>
</div>
</div>
