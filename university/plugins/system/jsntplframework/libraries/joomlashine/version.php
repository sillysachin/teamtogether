<?php
/**
 * @version    $Id$
 * @package    JSN_TplFramework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Class containing compatibility relation between framework and template.
 *
 * @package  JSN_TplFramework
 * @since    2.0.0
 */
class JSNTplVersion
{
	/**
	 * Following template version is not compatible with this version of framework.
	 *
	 * @var array
	 */
	protected static $incompatible = array(
		'epic' => '5.1.0',
		'dome' => '3.1.0',
		'tendo' => '2.1.0',
		'teki' => '2.1.0',
		'cube' => '2.1.0',
		'gruve' => '2.1.0',
		'pixel' => '2.1.0',
		'decor' => '2.1.0',
		'vintage' => '2.1.0',
		'kido' => '2.1.0',
		'neon' => '2.1.0',
		'boot' => '2.1.0',
		'air' => '1.1.0',
		'mico' => '1.1.0',
		'escape' => '1.1.0',
		'nuru' => '1.1.0',
		'sky' => '1.0.0',
		'metro' => '1.0.0'
	);

	/**
	 * Method for checking compatibility between installed template version and installed framework version.
	 *
	 * @param   string  $template  Template name.
	 * @param   string  $version   Installed version.
	 *
	 * @return  boolean
	 */
	public static function isCompatible($template, $version)
	{
		// Parse template name
		@list($prefix, $name, $suffix) = explode('_', $template);

		// If template is not in incompatible list then it is compatible
		if ( ! array_key_exists($name, self::$incompatible))
		{
			return true;
		}

		return version_compare($version, self::$incompatible[$name], '>');
	}
}
