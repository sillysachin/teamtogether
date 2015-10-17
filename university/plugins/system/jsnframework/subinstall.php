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

// Disable notice and warning by default for our products.
// The reason for doing this is if any notice or warning appeared then handling JSON string will fail in our code.
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

/**
 * Subinstall script for finalizing JSN Framework installation.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class PlgSystemJSNFrameworkInstallerScript
{
	/**
	 * Implement preflight hook.
	 *
	 * This step will be verify permission for install/update process.
	 *
	 * @param   string  $mode    Install or update?
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  boolean
	 */
	public function preflight($mode, $parent)
	{
		$app = JFactory::getApplication();

		// Only allow install if version >= 3.0
		$JVersion = new JVersion;

		if (version_compare($JVersion->RELEASE, '3.0', '<'))
		{
			$app->enqueueMessage('Plugin is not compatible with current Joomla! version, installation fail.', 'error');

			return false;
		}
	}

	/**
	 * Enable JSN Framework system plugin.
	 *
	 * @param   string  $route  Route type: install, update or uninstall.
	 * @param   object  $_this  The installer object.
	 *
	 * @return  boolean
	 */
	public function postflight($route, $_this)
	{
		// Get a database connector object
		$db = JFactory::getDbo();

		try
		{
			// Enable plugin by default
			$q = $db->getQuery(true);

			$q->update('#__extensions');
			$q->set(array('enabled = 1', 'protected = 1', 'ordering = -9999'));
			$q->where("element = 'jsnframework'");
			$q->where("type = 'plugin'", 'AND');
			$q->where("folder = 'system'", 'AND');

			$db->setQuery($q);
			$db->execute();

			// Check if user install this package manually
			if (strpos($_SERVER['HTTP_REFERER'], '/administrator/index.php?option=com_installer') !== false)
			{
				// Loop thru our products to find which one is installed
				class_exists('JSNVersion') OR require_once dirname(__FILE__) . '/libraries/joomlashine/version/version.php';

				foreach (JSNVersion::$products AS $product)
				{
					if (is_dir(JPATH_ROOT . '/administrator/components/' . $product))
					{
						// Build query to get dependency installation status
						$q	= $db->getQuery(true);

						$q->select('manifest_cache, custom_data');
						$q->from('#__extensions');
						$q->where("element = 'jsnframework'");
						$q->where("type = 'plugin'", 'AND');
						$q->where("folder = 'system'", 'AND');

						$db->setQuery($q);

						// Load dependency installation status
						$status = $db->loadObject();

						$ext = substr($product, 4);
						$dep = ! empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

						// Update dependency list
						in_array($ext, $dep) OR $dep[] = $ext;
						$status->custom_data = array_unique($dep);

						// Build query to update dependency data
						$q = $db->getQuery(true);

						$q->update('#__extensions');
						$q->set("custom_data = '" . json_encode($status->custom_data) . "'");

						// Backward compatible: keep data in this column for older product to recognize
						$manifestCache = json_decode($status->manifest_cache);
						$manifestCache->dependency = $status->custom_data;

						$q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

						// Backward compatible: keep data in this column also for another old product to recognize
						$q->set("params = '" . json_encode((object) array_combine($status->custom_data, $status->custom_data)) . "'");

						$q->where("element = 'jsnframework'");
						$q->where("type = 'plugin'", 'AND');
						$q->where("folder = 'system'", 'AND');

						$db->setQuery($q);
						$db->execute();
					}
				}
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Do some extra work when uninstall.
	 *
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  void
	 */
	public function uninstall($parent)
	{
		// Get Joomla config
		$config	= JFactory::getConfig();

		// Generate cache file path
		$cache = $config->get('tmp_path') . '/JoomlaShineUpdates.json';

		// Clean latest product version cache file
		if (file_exists($cache))
		{
			jimport('joomla.filesystem.file');
			JFile::delete($cache);
		}
	}
}
