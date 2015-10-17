<?php
defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.0', 'gt'))
{
	$script = <<< ENDSCRIPT

	jQuery(document).ready(function() {
		document.forms['adminForm'].action = document.forms['adminForm'].action + '&skipsrp=1';
	});

ENDSCRIPT;

}
else
{
	$script = <<< ENDSCRIPT

	window.addEvent('domready', function() {
		document.forms['adminForm'].action = document.forms['adminForm'].action + '&skipsrp=1';
	});

ENDSCRIPT;

}

JFactory::getDocument()->addScriptDeclaration($script);