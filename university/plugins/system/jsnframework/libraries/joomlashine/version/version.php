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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Prevent this file from being included twice
if (class_exists('JSNVersion'))
{
	return;
}

/**
 * Class containing compatibility relation between framework and extension.
 *
 * @package  JSN_Framework
 * @since    1.1.0
 */
class JSNVersion
{
	/**
	 * List of JSN products by directory name.
	 *
	 * @var array
	 */
	public static $products = array(
		'com_imageshow',
		'com_poweradmin',
		'com_sample',
		'com_uniform'
	);

	/**
	 * Compatibility list.
	 *
	 * @var array
	 */
	public static $version = array(
		'ext_imageshow'		=> '4.4.1',
		'ext_poweradmin'	=> '1.1.3',
		'ext_sample'		=> '1.0.0',
		'ext_uniform'		=> '1.0.0'
	);

	/**
	 * Method for checking compatibility between framework and extension.
	 *
	 * @param   string  $identified_name    Identified name of extension.
	 * @param   string  $extension_version  Current extension version.
	 *
	 * @return  boolean
	 */
	public static function checkCompatibility($identified_name, $extension_version)
	{
		if ( ! isset(self::$version[$identified_name]))
		{
			return true;
		}

		return version_compare($extension_version, self::$version[$identified_name], '>=');
	}

	/**
	 * Method for checking if extension is compatible with installed Joomla version.
	 *
	 * @param   string  $requiredJoomlaVersion  Joomla version required by extension, e.g. 2.5, 3.0, etc.
	 *
	 * @return  boolean
	 */
	public static function isJoomlaCompatible($requiredJoomlaVersion)
	{
		static $results;
		isset($results) OR $results = array();

		// Fix Joomla version for backward compatible
		$requiredJoomlaVersion != '3.' OR $requiredJoomlaVersion = '3.0';

		// Check if we already have results
		if ( ! isset($results[$requiredJoomlaVersion]))
		{
			// Get installed Joomla version
			$JVersion = new JVersion;
			$JVersion = $JVersion->getShortVersion();

			// Check if installed Joomla version is compatible
			if ($requiredJoomlaVersion == '2.5')
			{
				$results['2.5'] = (version_compare($JVersion, '2.5', '>=') AND version_compare($JVersion, '3.0', '<'));
			}
			else
			{
				$results[$requiredJoomlaVersion] = version_compare($JVersion, $requiredJoomlaVersion, '>=');
			}
		}

		return $results[$requiredJoomlaVersion];
	}
}
