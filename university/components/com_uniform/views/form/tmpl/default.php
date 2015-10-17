<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Form
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
$showTitle = false;
$showDes = false;
$app = JFactory::getApplication();
$params = $app->getParams();
$getShowTitle = $this->_input->get('show_form_title');
$getShowDes = $this->_input->get('show_form_description');
if (!empty($getShowTitle) && $getShowTitle == 1)
{
	$showTitle = true;
}
if (!empty($getShowDes) && $getShowDes == 1)
{
	$showDes = true;
}
if (JSNUniformHelper::checkStateForm($this->_formId))
{
	echo JSNUniformHelper::generateHTMLPages($this->_formId, $this->_formName,'','','',$showTitle,$showDes);
}
