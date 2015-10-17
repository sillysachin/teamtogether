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

/**
 * Helper class for working with XML files.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsXml
{
	/**
	 * Array containing all instantiated SimpleXMLElement objects.
	 *
	 * @var	array
	 */
	protected static $xml;

	/**
	 * Load an XML file and parse to a SimpleXMLElement object.
	 *
	 * @param   string  $path  Path to XML file.
	 *
	 * @return  object	SimpleXMLElement object.
	 */
	public static function load($path)
	{
		// Only load the file it is not loaded before
		if ( ! isset(self::$xml) OR ! isset(self::$xml[$path]))
		{
			self::$xml[$path] = simplexml_load_file($path);
		}

		return self::$xml[$path];
	}

	/**
	 * Load extension manifest cache from database.
	 *
	 * @param   string  $extension  Extension name.
	 * @param   string  $type       Extension type: component, module, plugin or template.
	 * @param   string  $folder     If extension type is plugin then folder should be set.
	 *
	 * @return  mixed   Object on success, null on failure.
	 */
	public static function loadManifestCache($extension = '', $type = '', $folder = '')
	{
		// Initialize extension name
		! empty($extension) OR $extension = JFactory::getApplication()->input->getCmd('option');

		// Get database and query object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		// Build query to get manifest cache
		$query->select('manifest_cache');
		$query->from('#__extensions');
		$query->where('(element = "' . $extension . '" OR name LIKE "%' . $extension . '%")', 'AND');

		if ( ! empty($type))
		{
			$query->where('type = "' . $type . '"');
		}

		if ($type == 'plugin' AND ! empty($folder))
		{
			$query->where('folder = "' . $folder . '"');
		}

		// Load manifest cache then return
		$db->setQuery($query);

		if ($result = $db->loadResult())
		{
			return json_decode($result);
		}
		else
		{
			return null;
		}
	}
}
