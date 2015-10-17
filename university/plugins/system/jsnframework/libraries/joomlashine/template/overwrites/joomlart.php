<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

if (class_exists('JSNT3Template'))
{
	$tmpl = new JSNT3Template($this);
	$tmpl->disableInfoMode();
	$tmpl->render();
}
elseif (class_exists('T3Template'))
{
	if (JVERSION < '1.7')
	{
		$tmpl = T3Template::getInstance();
		$tmpl->setTemplate($this);
		$tmpl->render();

		return;
	}
	else
	{
		$tmpl = T3Template::getInstance($this);
		$tmpl->render();

		return;
	}
}
else
{
	// Need to install or enable JAT3 Plugin
	echo JText::_('MISSING_JAT3_FRAMEWORK_PLUGIN');
}
