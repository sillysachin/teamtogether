<?php
/**
 * @version     $Id: uniform.php 19094 2012-11-30 02:27:22Z thailv $
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


// Get application object
$app = JFactory::getApplication();

// Get input object
$input = $app->input;

// Access check
if (!JFactory::getUser()->authorise('core.manage', $input->getCmd('option')))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Initialize common assets
require_once JPATH_COMPONENT_ADMINISTRATOR . '/bootstrap.php';

// Check if all dependency is installed
require_once JPATH_COMPONENT_ADMINISTRATOR . '/dependency.php';

// Register include path for class that working with database table
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

if (strpos('installer + update + upgrade', $input->getCmd('view')) !== false OR JSNVersion::isJoomlaCompatible('3.'))
{
	// Get the appropriate controller
	$controller = JSNBaseController::getInstance('JSNUniform');

	// Perform the request task
	$controller->execute($input->getCmd('task'));

	// Redirect if set by the controller
	$controller->redirect();
}
