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

/**
 * Autoload class file of JSN Template Framework.
 *
 * @param   string  $className  Name of class needs to be loaded.
 *
 * @return  boolean
 */
function jsn_template_framework_class_loader($className)
{
	if (strpos($className, 'JSNTpl') === 0)
	{
		$path  = strtolower(preg_replace('/([A-Z])/', '/\\1', substr($className, 6)));
		$fullPath = JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/' . $path;

		// Load alternative class for backward compatible with old template version
		$app = JFactory::getApplication();
		$tpl = $app->getTemplate();

		if ($app->isSite() AND substr($tpl, 0, 4) == 'jsn_' AND ! JSNTplVersion::isCompatible($tpl, JSNTplHelper::getTemplateVersion($tpl)))
		{
			if (is_file("{$fullPath}_v1.php") AND is_readable("{$fullPath}_v1.php"))
			{
				$fullPath .= '_v1';
			}
		}

		if (is_file("{$fullPath}.php") AND is_readable("{$fullPath}.php"))
		{
			return include_once "{$fullPath}.php";
		}

		return false;
	}
}

// Register jsn_template_framework_class_loader for autoloading
spl_autoload_register('jsn_template_framework_class_loader');

// Preload some required classes
require_once JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/helper.php';
require_once JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/version.php';
