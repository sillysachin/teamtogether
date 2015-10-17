<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Define base constants for the framework
define('JSN_PATH_TPLFRAMEWORK', dirname(__FILE__));
define('JSN_PATH_TPLFRAMEWORK_LIBRARIES', JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine');

define('JSN_TPLFRAMEWORK_ID', 'tpl_framework');
define('JSN_TPLFRAMEWORK_VERSION', '2.2.1');

define('JSN_TPLFRAMEWORK_CHECK_UPDATE_PERIOD', 86400);

// Define remote URL for communicating with JoomlaShine server
define('JSN_TPLFRAMEWORK_LIGHTCART_URL', 'http://www.joomlashine.com/index.php?option=com_lightcart');
define('JSN_TPLFRAMEWORK_VERSIONING_URL', 'http://www.joomlashine.com/versioning/product_version.php');
define('JSN_TPLFRAMEWORK_UPGRADE_DETAILS', 'http://www.joomlashine.com/versioning/product_upgrade.php');
