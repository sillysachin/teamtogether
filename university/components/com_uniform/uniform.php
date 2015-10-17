<?php

/**
 * @version     $Id: uniform.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Uniform
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
error_reporting(0);
// import joomla controller library
jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/uniform.defines.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'uniform.php';

JLoader::register('JSNUniformUpload', dirname(__FILE__) . DS . 'helpers' . DS . 'form.php');
JLoader::register('Browser', dirname(__FILE__) . DS . 'helpers' . DS . 'browser.php');

$controller = JControllerLegacy::getInstance('JSNUniform');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
