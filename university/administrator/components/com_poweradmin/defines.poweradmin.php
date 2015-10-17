<?php
/**
 * 2.1.4    $Id$
 * @package    JSNPoweradmin
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//Define constants for about & update page
define('JSN_POWERADMIN_EDITION', '');
define('JSN_POWERADMIN_DOC_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-poweradmin-docs.zip');
define('JSN_POWERADMIN_INFO_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-poweradmin.html');
define('JSN_POWERADMIN_UPDATE_LINK', 'index.php?option=com_poweradmin&amp;view=update');
define('JSN_POWERADMIN_REVIEW_LINK', 'http://www.joomlashine.com/joomla-extensions/jsn-poweradmin-on-jed.html');


define('JSN_POWERADMIN_DEFINED', true);
define('JSN_POWERADMIN_PATH', JPATH_ROOT . '/administrator/components/com_poweradmin');
define('JSN_POWERADMIN_STYLE_URI', JURI::root(true) . '/administrator/components/com_poweradmin/assets/css/');
define('JSN_POWERADMIN_SITE_PATH', JPATH_ROOT . '/components/com_poweradmin');
define('JSN_POWERADMIN_STYLE_URI_SITE', JURI::root().'components/com_poweradmin/assets/css/');
define('JSN_POWERADMIN_LIB_JSNJS_URI_SITE', 		JURI::root().'components/com_poweradmin/assets/javascripts/joomlashine/');
define('JSN_POWERADMIN_LIB_PATH', JPATH_ROOT . '/administrator/components/com_poweradmin/libraries/jJoomlashine');
define('JSN_POWERADMIN_LIB_JSNJS_URI', JURI::root() . 'administrator/components/com_poweradmin/assets/js/joomlashine/');
define('JSN_POWERADMIN_LIB_JS_URI', JURI::root() . 'administrator/components/com_poweradmin/assets/js/');
define('JSN_POWERADMIN_IMAGES_URI', JURI::root() . 'administrator/components/com_poweradmin/assets/images/');
define('JSN_POWERADMIN_PLUGIN_ADMINBAR_JS_URI', JURI::root() . 'plugins/system/jsnpoweradmin/assets/js/');

define('JSN_POWERADMIN_TEMPLATE_PATH', JPATH_ROOT . '/templates');
define('JSN_PATH_RENDER_COMPONENT_LAYOUT', JPATH_ADMINISTRATOR . '/components/com_poweradmin/helpers/html/layouts/');
define('JSN_POWERADMIN_IDENTIFIED_NAME', 'ext_poweradmin');
define('JSN_POWERADMIN_VERSION',			'2.1.4');

define('JSN_FRAMEWORK_ASSETS', JURI::root(true) . '/plugins/system/jsnframework/assets');

define('JSN_3RD_EXTENSION_STRING', 'JSN3rdExtension');
define('JSN_3RD_EXTENSION_NOT_INSTALLED_STRING', 'JSNNotInstalled3rdExtension');
define('JSN_3RD_EXTENSION_NOT_ENABLED_STRING', 'JSNNotEnabled3rdExtension');
define('JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX', 'ext_jsnpoweradmin_');

$supportedExtensions = array();
define('JSNPA_SUPPORTED_EXT_LIST', json_encode($supportedExtensions));

