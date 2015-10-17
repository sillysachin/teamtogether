<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldInformation extends JFormField {
	protected $type = 'Information';

	protected function getInput() {
		return '<div id="nZambiInfo">' . JText::_('MOD_ZKUNENALATEST_ADMIN_INFORMATION') . '</div>';
	}
}
