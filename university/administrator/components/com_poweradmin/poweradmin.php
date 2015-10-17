<?php
/**
 * @version     $Id: poweradmin.php 16024 2012-09-13 11:55:37Z hiepnv $
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

define('JSN_POWERADMIN_DEPENDENCY', '[{"type":"plugin","folder":"system","name":"jsnframework","identified_name":"ext_framework","client":"site","publish":"1","lock":"1","title":"JSN Extension Framework System Plugin"},{"type":"plugin","folder":"system","dir":"plugins\/system\/jsnpoweradmin","name":"jsnpoweradmin","client":"site","publish":"1","lock":"1","title":"jsnpoweradmin"},{"type":"plugin","folder":"jsnpoweradmin","dir":"plugins\/jsnpoweradmin\/content","name":"content","client":"site","publish":"1","lock":"0","title":"content"},{"type":"plugin","folder":"jsnpoweradmin","dir":"plugins\/jsnpoweradmin\/contact","name":"contact","client":"site","publish":"1","lock":"0","title":"contact"},{"type":"plugin","folder":"jsnpoweradmin","dir":"plugins\/jsnpoweradmin\/users","name":"users","client":"site","publish":"1","lock":"0","title":"users"},{"type":"plugin","folder":"jsnpoweradmin","dir":"plugins\/jsnpoweradmin\/weblinks","name":"weblinks","client":"site","publish":"1","lock":"0","title":"weblinks"},{"type":"plugin","folder":"jsnpoweradmin","dir":"plugins\/jsnpoweradmin\/k2","name":"k2","remove":"1","title":"k2","removable":false},{"type":"module","folder":"system","dir":"modules\/admin\/mod_poweradmin","name":"mod_poweradmin","client":"admin","publish":"1","position":"icon","title":"JSN PowerAdmin Quick Icons","ordering":"0","lock":"0","params":null}]');

// Access check
if ( ! JFactory::getUser()->authorise('core.manage', JRequest::getCmd('option', 'com_poweradmin')))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_ROOT . '/administrator/components/com_poweradmin/defines.poweradmin.php' ;
// Import JoomlaShine base MVC classes
require_once dirname(__FILE__) . '/libraries/joomlashine/base/model.php';
require_once dirname(__FILE__) . '/libraries/joomlashine/base/view.php';
require_once dirname(__FILE__) . '/libraries/joomlashine/base/controller.php';

// Import joomla controller library
jimport('joomla.application.component.controller');

// Get application object
$app = JFactory::getApplication();
$input = $app->input;
$tmpl  = $input->getCmd('tmpl');
$task  = $input->getCmd('task');
$isControllerExecutable = true;

if(!($tmpl === 'component' || $task)){
	// Check if all dependency is installed
	require_once JPATH_COMPONENT_ADMINISTRATOR . '/dependency.php';


	// Require helper file
	JLoader::register('PowerAdminHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/poweradmin.php');

	! (class_exists('JSNUtilsLanguage') && method_exists(new JSNUtilsLanguage, 'loadDefault'));

	if (strpos('installer + update + upgrade', $input->getCmd('view')) !== false OR JSNVersion::isJoomlaCompatible('3.'))
	{
		$isControllerExecutable = true;
	}else{
		$isControllerExecutable = false;
	}
}


if($isControllerExecutable){
	// Get the appropriate controller
	$controller = JSNBaseController::getInstance('Poweradmin');
	$controller = new $controller;

	// Perform the request task
	$controller->execute($input->getCmd('task'));

	// Redirect if set by the controller
	$controller->redirect();
}
