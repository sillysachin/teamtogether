<?php
/**
 * @version    $Id$
 * @package    JSN_Sample
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
 * Class for finalizing JSN extension installation.
 *
 * @package  JSN_Sample
 * @since    1.0.0
 */
abstract class JSNInstallerScript
{
	/**
	 * XML manifest.
	 *
	 * @var  SimpleXMLElement
	 */
	protected $manifest;

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
		// Initialize variables
		$this->parentInstaller = $parent->getParent();

		$this->app = JFactory::getApplication();
		$this->manifest = $this->parentInstaller->getManifest();

		// Get installed extension directory and name
		$this->path = $this->parentInstaller->getPath('extension_administrator');
		$this->name = substr(basename($this->path), 4);

		// Check if installed Joomla version is compatible
		$joomlaVersion = new JVersion;
		$canInstall		= substr($joomlaVersion->RELEASE, 0, 1) == substr((string) $this->manifest['version'], 0, 1) ? true : false;

		if (!$canInstall)
		{
			$this->app->enqueueMessage(sprintf('Cannot install "%s" because the installation package is not compatible with your installed Joomla version', (string) $this->manifest->name), 'error');
		}

		// Parse dependency
		$this->parseDependency($this->parentInstaller);

		// Skip checking folder permission if FTP layer is enabled
		$config = JFactory::getConfig();

		if ($config->get('ftp_enable'))
		{
			// Try to backup user edited language file
			return $this->backupLanguage();
		}

		// Check environment
		$canInstallExtension = true;
		$canInstallSiteLanguage = is_writable(JPATH_SITE . '/language');
		$canInstallAdminLanguage = is_writable(JPATH_ADMINISTRATOR . '/language');

		if (!$canInstallSiteLanguage)
		{
			$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', JPATH_SITE . '/language'), 'error');
		}
		else
		{
			foreach (glob(JPATH_SITE . '/language/*', GLOB_ONLYDIR) AS $dir)
			{
				if (!is_writable($dir))
				{
					$canInstallSiteLanguage = false;
					$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', $dir), 'error');
				}
			}
		}

		if (!$canInstallAdminLanguage)
		{
			$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', JPATH_ADMINISTRATOR . '/language'), 'error');
		}
		else
		{
			foreach (glob(JPATH_ADMINISTRATOR . '/language/*', GLOB_ONLYDIR) AS $dir)
			{
				if (!is_writable($dir))
				{
					$canInstallAdminLanguage = false;
					$this->app->enqueueMessage(sprintf('Cannot install language file at "%s"', $dir), 'error');
				}
			}
		}
		$componentInfo = $this->getComponentInfo();
		if (!empty($componentInfo))
		{
			$this->jsnUniformRreFlight($componentInfo);
		}
		// Checking directory permissions for dependency installation
		foreach ($this->dependencies AS & $extension)
		{
			// Simply continue if extension is set to be removed
			if (isset($extension->remove) AND (int) $extension->remove > 0)
			{
				continue;
			}

			// Check if dependency can be installed
			switch($extension->type = strtolower($extension->type))
			{
				case 'component':
					$sitePath = JPATH_SITE . '/components';
					$adminPath = JPATH_ADMINISTRATOR . '/components';

					if (!is_dir($sitePath) OR !is_writable($sitePath))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $sitePath), 'error');
					}

					if (!is_dir($adminPath) OR !is_writable($adminPath))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $adminPath), 'error');
					}
					break;

				case 'plugin':
					$path = JPATH_ROOT . '/plugins/' . $extension->folder;

					if ((is_dir($path) AND !is_writable($path)) OR (!is_dir($path) AND !is_writable(dirname($path))))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $path), 'error');
					}
					break;

				case 'module':
				case 'template':
					$path = ($extension->client == 'site' ? JPATH_SITE : JPATH_ADMINISTRATOR) . "/{$extension->type}s";

					if (!is_dir($path) OR !is_writable($path))
					{
						$canInstallExtension = false;
						$this->app->enqueueMessage(sprintf('Cannot install "%s" %s because "%s" is not writable', $extension->name, $extension->type, $path), 'error');
					}
					break;
			}

			if ($canInstall AND $canInstallExtension AND $canInstallSiteLanguage AND $canInstallAdminLanguage AND isset($extension->source))
			{
				// Backup dependency parameters
				$db = JFactory::getDbo();
				$q = $db->getQuery(true);

				$q->select('params');
				$q->from('#__extensions');
				$q->where("element = '{$extension->name}'");
				$q->where("type = '{$extension->type}'");
				$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

				$db->setQuery($q);

				$extension->params = $db->loadResult();
			}
		}

		if ($canInstall AND $canInstallExtension AND $canInstallSiteLanguage AND $canInstallAdminLanguage)
		{
			// Try to backup user edited language file
			return $this->backupLanguage();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Implement postflight hook.
	 *
	 * @param   string  $type    Extension type.
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		// Get original installer
		isset($this->parentInstaller) OR $this->parentInstaller = $parent->getParent();

		// Update language file
		$this->updateLanguage();

		// Process dependency installation
		foreach ($this->dependencies AS $extension)
		{
			// Remove installed extension if requested
			if (isset($extension->remove) AND (int) $extension->remove > 0)
			{
				$this->removeExtension($extension);
				continue;
			}

			// Install only dependency that has local installation package
			if (isset($extension->source))
			{
				// Install and update dependency status
				$this->installExtension($extension);
			}
			elseif (!isset($this->missingDependency))
			{
				$this->missingDependency = true;
			}
		}

		// Create dependency declaration constant
		if (!defined('JSN_' . strtoupper($this->name) . '_DEPENDENCY'))
		{
			// Get Joomla config
			$config = JFactory::getConfig();

			// Unset some unnecessary properties
			foreach ($this->dependencies AS & $dependency)
			{
				unset($dependency->source);
				unset($dependency->upToDate);
			}

			$this->dependencies = json_encode($this->dependencies);

			// Store dependency declaration
			file_exists($defines = $this->path . '/defines.php')
				OR file_exists($defines = $this->path . "/{$this->name}.defines.php")
				OR $defines = $this->path . "/{$this->name}.php";

			if ($config->get('ftp_enable') OR is_writable($defines))
			{
				$buffer = preg_replace(
					'/(defined\s*\(\s*._JEXEC.\s*\)[^\n]+\n)/',
					'\1' . "\ndefine('JSN_" . strtoupper($this->name) . "_DEPENDENCY', '" . $this->dependencies . "');\n",
					JFile::read($defines)
				);

				JFile::write($defines, $buffer);
			}
		}

		// Clean latest product version cache file
		$cache = JFactory::getConfig()->get('tmp_path') . '/JoomlaShineUpdates.json';

		if (file_exists($cache))
		{
			jimport('joomla.filesystem.file');
			JFile::delete($cache);
		}

		// Register check update link for Joomla 3.1
		$JVersion = new JVersion;

		if (version_compare($JVersion->RELEASE, '3.1', '>='))
		{
			// Get id for the extension just installed
			$ext = JTable::getInstance('Extension');

			$ext->load(array(
					'name' => $this->name,
					'element' => basename($this->path),
					'type' => 'component'
				)
			);

			if ($ext->extension_id)
			{
				// Get current check update data
				$db = JFactory::getDbo();
				$q = $db->getQuery(true);

				$q->select('update_site_id');
				$q->from('#__update_sites');
				$q->where('location LIKE ' . $q->quote('%index.php?option=' . $ext->element . '&task=checkUpdate%'));

				$db->setQuery($q);

				if ($uid = $db->loadResult())
				{
					// Clean-up current check update data
					$q = $db->getQuery(true);

					$q->delete('#__update_sites');
					$q->where('update_site_id = ' . (int) $uid);

					$db->setQuery($q);
					$db->execute();

					$q = $db->getQuery(true);

					$q->delete('#__update_sites_extensions');
					$q->where('update_site_id = ' . (int) $uid);

					$db->setQuery($q);
					$db->execute();
				}

				// Register check update data
				$ln = JUri::root() . 'administrator/index.php?option=' . $ext->element . '&task=checkUpdate&file=update.xml';
				$q = $db->getQuery(true);

				$q->insert('#__update_sites');
				$q->columns('`name`, `type`, `location`, `enabled`');
				$q->values($q->quote($this->name) . ', ' . $q->quote('collection') . ', ' . $q->quote($ln) . ', 1');

				$db->setQuery($q);
				$db->execute();

				if ($uid = $db->insertid())
				{
					$q = $db->getQuery(true);

					$q->insert('#__update_sites_extensions');
					$q->columns('`update_site_id`, `extension_id`');
					$q->values((int) $uid . ', ' . (int) $ext->extension_id);

					$db->setQuery($q);
					$db->execute();
				}
			}
		}

		// Check if redirect should be disabled
		if ($this->app->input->getBool('tool_redirect', true))
		{
			// Do we have any missing dependency
			if ($this->missingDependency)
			{
				if (strpos($_SERVER['HTTP_REFERER'], '/administrator/index.php?option=com_installer') !== false)
				{
					// Set redirect to finalize dependency installation
					$this->parentInstaller->setRedirectURL('index.php?option=com_' . $this->name . '&view=installer');
				}
				else
				{
					// Let Ajax client redirect
					echo '
<script type="text/javascript">
	if (window.parent)
		window.parent.location.href ="' . JUri::root() . 'administrator/index.php?option=com_' . $this->name . '&view=installer";
	else
		location.href ="' . JUri::root() . 'administrator/index.php?option=com_' . $this->name . '&view=installer";
</script>';
					exit;
				}
			}
		}
	}

	/**
	 * Implement uninstall hook.
	 *
	 * @param   object  $parent  JInstaller object.
	 *
	 * @return  void
	 */
	public function uninstall($parent)
	{
		// Initialize variables
		isset($this->parentInstaller) OR $this->parentInstaller = $parent->getParent();

		$this->app = JFactory::getApplication();
		$this->manifest = $this->parentInstaller->getManifest();

		// Get installed extension directory and name
		$this->path = $this->parentInstaller->getPath('extension_administrator');
		$this->name = substr(basename($this->path), 4);

		// Parse dependency
		$this->parseDependency($this->parentInstaller);

		// Remove all dependency
		foreach ($this->dependencies AS $extension)
		{
			$this->removeExtension($extension);
		}
	}

	/**
	 * Retrieve dependency from manifest file.
	 *
	 * @param   object  $installer  JInstaller object.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function parseDependency($installer)
	{
		// Continue only if dependency not checked before
		if (!isset($this->dependencies) OR !is_array($this->dependencies))
		{
			// Preset dependency list
			$this->dependencies = array();

			if (isset($this->manifest->subinstall) AND $this->manifest->subinstall instanceOf SimpleXMLElement)
			{
				// Loop on each node to retrieve dependency information
				foreach ($this->manifest->subinstall->children() AS $node)
				{
					// Verify tag name
					if (strcasecmp($node->getName(), 'extension') != 0)
					{
						continue;
					}

					// Re-create serializable dependency object
					$extension = (array) $node;
					$extension = (object) $extension['@attributes'];

					$extension->title = trim((string) $node != '' ? (string) $node : ($node['title'] ? (string) $node['title'] : (string) $node['name']));

					// Validate dependency
					if (!isset($extension->name) OR !isset($extension->type) OR !in_array($extension->type, array('template', 'plugin', 'module', 'component')) OR ($extension->type == 'plugin' AND !isset($extension->folder)))
					{
						continue;
					}

					// Check if dependency has local installation package
					if (isset($extension->dir) AND is_dir($source = $installer->getPath('source') . '/' . $extension->dir))
					{
						$extension->source = $source;
					}

					$this->dependencies[] = $extension;
				}
			}
		}

		return $this;
	}

	/**
	 * Install a dependency.
	 *
	 * @param   object  $extension  Object containing extension details.
	 *
	 * @return  void
	 */
	public function installExtension($extension)
	{
		// Get application object
		isset($this->app) OR $this->app = JFactory::getApplication();

		// Get database object
		$db = JFactory::getDbo();
		$q = $db->getQuery(true);

		// Build query to get dependency installation status
		$q->select('manifest_cache, custom_data');
		$q->from('#__extensions');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);

		if ($status = $db->loadObject())
		{
			// Initialize variables
			$JVersion = new JVersion;
			$manifest = json_decode($status->manifest_cache);

			// Get information about the dependency to be installed
			$xml = JPATH::clean($extension->source . '/' . $extension->name . '.xml');

			if (is_file($xml) AND ($xml = simplexml_load_file($xml)))
			{
				if ($JVersion->RELEASE == (string) $xml['version'] AND version_compare($manifest->version, (string) $xml->version, '<'))
				{
					// The dependency to be installed is newer than the existing one, mark for update
					$doInstall = true;
				}

				if ($JVersion->RELEASE != (string) $xml['version'] AND version_compare($manifest->version, (string) $xml->version, '<='))
				{
					// The dependency to be installed might not newer than the existing one but Joomla version is difference, mark for update
					$doInstall = true;
				}
			}
		}
		elseif (isset($extension->source))
		{
			// The dependency to be installed not exist, mark for install
			$doInstall = true;
		}

		if (isset($doInstall) AND $doInstall)
		{
			// Install dependency
			$installer = new JInstaller;

			if (!$installer->install($extension->source))
			{
				$this->app->enqueueMessage(sprintf('Error installing "%s" %s', $extension->name, $extension->type), 'error');
			}
			else
			{
				$this->app->enqueueMessage(sprintf('Install "%s" %s was successfull', $extension->name, $extension->type));

				// Update dependency status
				$this->updateExtension($extension);

				// Build query to get dependency installation status
				$q = $db->getQuery(true);

				$q->select('manifest_cache, custom_data');
				$q->from('#__extensions');
				$q->where("element = '{$extension->name}'");
				$q->where("type = '{$extension->type}'");
				$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

				$db->setQuery($q);

				// Load dependency installation status
				$status = $db->loadObject();
			}
		}

		// Update dependency tracking
		if (isset($status))
		{
			$ext = isset($this->name) ? $this->name : substr($this->app->input->getCmd('option'), 4);
			$dep = !empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

			// Backward compatible: move all dependency data from params to custom_data column
			if (is_array($params = (isset($extension->params) AND $extension->params != '{}') ? (array) json_decode($extension->params) : null))
			{
				foreach (array('imageshow', 'poweradmin', 'sample') AS $com)
				{
					if ($com != $ext AND isset($params[$com]))
					{
						$dep[] = $com;
					}
				}
			}

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
			$params = is_array($params)
				? array_merge($params, array_combine($status->custom_data, $status->custom_data))
				: array_combine($status->custom_data, $status->custom_data);

			$q->set("params = '" . json_encode($params) . "'");

			$q->where("element = '{$extension->name}'");
			$q->where("type = '{$extension->type}'");
			$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

			$db->setQuery($q);
			$db->execute();
		}
	}

	/**
	 * Update dependency status.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function updateExtension($extension)
	{
		// Get object to working with extensions table
		$table = JTable::getInstance('Extension');

		// Load extension record
		$condition = array(
			'type' => $extension->type,
			'element' => $extension->name
		);

		if ($extension->type == 'plugin')
		{
			$condition['folder'] = $extension->folder;
		}

		$table->load($condition);

		// Update extension record
		$table->enabled = (isset($extension->publish)    AND (int) $extension->publish > 0) ? 1 : 0;
		$table->protected = (isset($extension->lock)        AND (int) $extension->lock > 0) ? 1 : 0;
		$table->client_id = (isset($extension->client)    AND $extension->client == 'site') ? 0 : 1;

		// Store updated extension record
		$table->store();

		// Update module instance
		if ($extension->type == 'module' AND $extension->name != 'mod_uniform')
		{
			// Get object to working with modules table
			$module = JTable::getInstance('module');

			// Load module instance
			$module->load(array('module' => $extension->name));

			// Update module instance
			$module->title = $extension->title;
			$module->ordering = isset($extension->ordering) ? $extension->ordering : 0;
			$module->published = (isset($extension->publish) AND (int) $extension->publish > 0) ? 1 : 0;

			if ($hasPosition = (isset($extension->position) AND (string) $extension->position != ''))
			{
				$module->position = (string) $extension->position;
			}

			// Store updated module instance
			$module->store();

			// Set module instance to show in all page
			if ($hasPosition AND (int) $module->id > 0)
			{
				// Get database object
				$db = JFactory::getDbo();
				$q = $db->getQuery(true);

				try
				{
					// Remove all menu assignment records associated with this module instance
					$q->delete('#__modules_menu');
					$q->where("moduleid = {$module->id}");

					// Execute query
					$db->setQuery($q);
					$db->execute();

					// Build query to show this module instance in all page
					$q->insert('#__modules_menu');
					$q->columns('moduleid, menuid');
					$q->values("{$module->id}, 0");

					// Execute query
					$db->setQuery($q);
					$db->execute();
				} catch (Exception $e)
				{
					throw $e;
				}
			}
		}

		return $this;
	}

	/**
	 * Disable a dependency.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function disableExtension($extension)
	{
		// Get database object
		$db = JFactory::getDbo();
		$q = $db->getQuery(true);

		// Build query
		$q->update('#__extensions');
		$q->set('enabled = 0');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);
		$db->execute();

		return $this;
	}

	/**
	 * Unlock a dependency.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function unlockExtension($extension)
	{
		// Get database object
		$db = JFactory::getDbo();
		$q = $db->getQuery(true);

		// Build query
		$q->update('#__extensions');
		$q->set('protected = 0');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);
		$db->execute();

		return $this;
	}

	/**
	 * Remove a dependency.
	 *
	 * @param   object  $extension  Extension to update.
	 *
	 * @return  object  Return itself for method chaining.
	 */
	protected function removeExtension($extension)
	{
		// Get application object
		isset($this->app) OR $this->app = JFactory::getApplication();

		// Preset dependency status
		$extension->removable = true;

		// Get database object
		$db = JFactory::getDbo();
		$q = $db->getQuery(true);

		// Build query to get dependency installation status
		$q->select('extension_id, manifest_cache, custom_data, params');
		$q->from('#__extensions');
		$q->where("element = '{$extension->name}'");
		$q->where("type = '{$extension->type}'");
		$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

		// Execute query
		$db->setQuery($q);

		if ($status = $db->loadObject())
		{
			// Initialize variables
			$id = $status->extension_id;
			$ext = isset($this->name) ? $this->name : substr($this->app->input->getCmd('option'), 4);
			$deps = !empty($status->custom_data) ? (array) json_decode($status->custom_data) : array();

			// Update dependency tracking
			$status->custom_data = array();

			foreach ($deps AS $dep)
			{
				// Backward compatible: ensure that product is not removed
				// if ($dep != $ext)
				if ($dep != $ext AND is_dir(JPATH_BASE . '/components/com_' . $dep))
				{
					$status->custom_data[] = $dep;
				}
			}

			if (count($status->custom_data))
			{
				// Build query to update dependency data
				$q = $db->getQuery(true);

				$q->update('#__extensions');
				$q->set("custom_data = '" . json_encode($status->custom_data) . "'");

				// Backward compatible: keep data in this column for older product to recognize
				$manifestCache = json_decode($status->manifest_cache);
				$manifestCache->dependency = $status->custom_data;

				$q->set("manifest_cache = '" . json_encode($manifestCache) . "'");

				// Backward compatible: keep data in this column also for another old product to recognize
				$params = is_array($params = (isset($status->params) AND $status->params != '{}') ? (array) json_decode($status->params) : null)
					? array_merge($params, array_combine($status->custom_data, $status->custom_data))
					: array_combine($status->custom_data, $status->custom_data);

				$q->set("params = '" . json_encode($params) . "'");

				$q->where("element = '{$extension->name}'");
				$q->where("type = '{$extension->type}'");
				$extension->type != 'plugin' OR $q->where("folder = '{$extension->folder}'");

				$db->setQuery($q);
				$db->execute();

				// Indicate that extension is not removable
				$extension->removable = false;
			}
		}
		else
		{
			// Extension was already removed
			$extension->removable = false;
		}

		if ($extension->removable)
		{
			// Disable and unlock dependency
			$this->disableExtension($extension);
			$this->unlockExtension($extension);

			// Remove dependency
			$installer = new JInstaller;

			if ($installer->uninstall($extension->type, $id))
			{
				$this->app->enqueueMessage(sprintf('"%s" %s has been uninstalled', $extension->name, $extension->type));
			}
			else
			{
				$this->app->enqueueMessage(sprintf('Cannot uninstall "%s" %s', $extension->name, $extension->type));
			}
		}

		return $this;
	}

	/**
	 * Attemp to backup user edited language file before re-install/update.
	 *
	 * @return  boolean  FALSE if backup fail for any reason, TRUE otherwise.
	 */
	protected function backupLanguage()
	{
		// Load language utility class
		$langUtil = (class_exists('JSNUtilsLanguage') AND method_exists('JSNUtilsLanguage', 'edited'))
			? 'JSNUtilsLanguage' : 'JSNUtilsLanguageTmp';

		if ($langUtil != 'JSNUtilsLanguage')
		{
			file_exists($this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php')
				? require_once $this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php'
				: ($langUtil = null);
		}

		if ($langUtil)
		{
			// Get list of component's supported language
			$admin = is_dir($this->path . '/language/admin') ? JFolder::folders($this->path . '/language/admin') : null;
			$site = is_dir($this->path . '/language/site') ? JFolder::folders($this->path . '/language/site') : null;

			if ($admin AND $site)
			{
				$langs = array_merge($admin, $site);
			}
			elseif ($admin OR $site)
			{
				$langs = $admin ? $admin : $site;
			}
			$langs = isset($langs) ? array_unique($langs) : array();

			// Loop thru supported language list and get all language files installed in Joomla's language folder
			foreach ($langs AS $lang)
			{
				// Check if language is installed in Joomla's language folder and manually edited by user
				$isEdited = array(
					'admin' => call_user_func(array($langUtil, 'edited'), $lang, false, "com_{$this->name}"),
					'site' => call_user_func(array($langUtil, 'edited'), $lang, true, "com_{$this->name}")
				);

				foreach ($isEdited AS $client => $edited)
				{
					if ($edited)
					{
						// Get list of language file
						$files = glob($this->path . "/language/{$client}/{$lang}/{$lang}.*.ini");

						// Backup all language file installed in Joomla's language folder
						foreach ($files AS $file)
						{
							// Generate path to user edited language file in Joomla's language folder
							$f = ($client == 'admin' ? JPATH_ADMINISTRATOR : JPATH_SITE) . "/language/{$lang}/" . basename($file);

							// Backup user edited language file to temporary directory
							if (is_readable($f))
							{
								if (!JFile::copy($f, "{$f}.jsn-installer-backup"))
								{
									$backupFails[] = str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $f);
								}
							}
						}
					}
				}
			}

			if (isset($backupFails))
			{
				$this->app->enqueueMessage(
					'Cannot backup following user edited language file(s): <ul><li>' . implode('</li><li>', $backupFails) . '</li></ul>',
					'warning'
				);

				return false;
			}
		}

		return true;
	}

	/**
	 * Update all language file installed in Joomla's language folder.
	 *
	 * @return  boolean  FALSE if update fail for any reason, TRUE otherwise.
	 */
	protected function updateLanguage()
	{
		// Load language utility class
		$langUtil = (class_exists('JSNUtilsLanguage') AND method_exists('JSNUtilsLanguage', 'edited'))
			? 'JSNUtilsLanguage' : 'JSNUtilsLanguageTmp';

		if ($langUtil != 'JSNUtilsLanguage')
		{
			file_exists($this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php')
				? require_once $this->parentInstaller->getPath('source') . '/admin/libraries/joomlashine/utils/language.php'
				: ($langUtil = null);
		}

		if ($langUtil)
		{
			// Get list of component's supported language
			$admin = is_dir($this->path . '/language/admin') ? JFolder::folders($this->path . '/language/admin') : null;
			$site = is_dir($this->path . '/language/site') ? JFolder::folders($this->path . '/language/site') : null;

			if ($admin AND $site)
			{
				$langs = array_merge($admin, $site);
			}
			elseif ($admin OR $site)
			{
				$langs = $admin ? $admin : $site;
			}
			$langs = isset($langs) ? array_unique($langs) : array();

			// Loop thru supported language list and get all language files installed in Joomla's language folder
			foreach ($langs AS $lang)
			{
				// Check if language is installed in Joomla's language folder
				$isInstalled = array(
					'admin' => call_user_func(array($langUtil, 'installed'), $lang, false, "com_{$this->name}"),
					'site' => call_user_func(array($langUtil, 'installed'), $lang, true, "com_{$this->name}")
				);

				foreach ($isInstalled AS $client => $installed)
				{
					if ($installed)
					{
						// Install new language file
						call_user_func(array($langUtil, 'install'), (array) $lang, $client != 'admin' ? true : false, true, "com_{$this->name}");

						// Get list of language file
						$files = glob($this->path . "/language/{$client}/{$lang}/{$lang}.*.ini");

						// Check if any installed language file has backup
						foreach ($files AS $file)
						{
							// Clean all possible new-line character left by 'glob' function
							$file = preg_replace('/(\r|\n)/', '', $file);

							// Generate path to installed language file in Joomla's language folder
							$f = ($client == 'admin' ? JPATH_ADMINISTRATOR : JPATH_SITE) . "/language/{$lang}/" . basename($file);

							// If language file has backup, merge all user's translation into new language file
							if (is_readable("{$f}.jsn-installer-backup"))
							{
								// Read content of new language file to array
								$new = file($file);

								// Read content of backup file to associative array
								foreach (file("{$f}.jsn-installer-backup") AS $line)
								{
									if (!empty($line) AND !preg_match('/^\s*;/', $line) AND preg_match('/^\s*([^=]+)="([^\r\n]+)"\s*$/', $line, $match))
									{
										$bak[$match[1]] = $match[2];
									}
								}

								// Merge user's translation into new language file
								foreach ($new AS & $line)
								{
									if (!empty($line) AND !preg_match('/^\s*;/', $line) AND preg_match('/^\s*([^=]+)="([^\r\n]+)"\s*$/', $line, $match))
									{
										if (isset($bak[$match[1]]))
										{
											$line = str_replace($match[2], $bak[$match[1]], $line);

											// Mark as merged
											isset($merged) OR $merged = true;
										}
									}
								}

								// Update installed language file with merged content
								if (isset($merged) AND $merged)
								{
									$new = implode($new);

									if (!JFile::write($f, $new))
									{
										$mergeFails[] = str_replace(JPATH_ROOT, 'JOOMLA_ROOT', $f);
									}
								}

								// Remove backup file
								JFile::delete("{$f}.jsn-installer-backup");
							}
						}
					}
				}
			}

			if (isset($mergeFails))
			{
				$this->app->enqueueMessage(
					'Cannot merge user edited translation back to following language file(s): <ul><li>' . implode('</li><li>', $mergeFails) . '</li></ul>',
					'warning'
				);

				return false;
			}
		}

		return true;
	}

	/**
	 * get component info
	 *
	 * @return type
	 */
	public static function getComponentInfo()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('element=\'com_uniform\' AND type=\'component\'');
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}

	/**
	 * get data folder upload in data default config
	 *
	 * @return Object
	 */
	public static function getFolderUploadConfig()
	{
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_config')->where('name="folder_upload"'));
		return $db->loadObject();
	}

	/**
	 * Get data forms
	 *
	 * @return object list
	 */
	public static function getForms($limit = "")
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__jsn_uniform_forms');
		$query->order("form_id DESC");
		if (empty($limit))
		{
			$db->setQuery($query);
		}
		else
		{
			$db->setQuery($query, 0, $limit);
		}
		$forms = $db->loadObjectList();
		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		return $forms;
	}

	/**
	 *  JSN Uniform Update Compontent RreFlight
	 *
	 * @param $componentInfo
	 */
	public static function jsnUniformRreFlight($componentInfo)
	{
		$session = JFactory::getSession();
		$sessionQueue = $session->get('registry');
		$sessionQueue->set('com_jsnuniform', null);

		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$prefix = $db->getPrefix();
		$dbName = $app->getCfg('db');
		$componentInfo = json_decode($componentInfo->manifest_cache);

		if (!empty($componentInfo->version) && version_compare($componentInfo->version, '1.3.0', '<'))
		{
			$db = JFactory::getDbo();
			$prefix = $db->getPrefix();
			$db->setQuery("SHOW COLUMNS FROM #__jsn_uniform_forms");
			$columnForm = $db->loadObjectList();
			$checkBtn = true;
			foreach ($columnForm as $itemForm)
			{
				if (isset($itemForm->Field) && $itemForm->Field == "form_edit_submission")
				{
					$checkBtn = false;
				}
			}
			if ($checkBtn)
			{
				$query = "ALTER TABLE " . $db->quoteName($prefix . "jsn_uniform_forms") . "  ADD `form_edit_submission` INT NOT NULL";
				$db->setQuery($query);
				$db->query();
			}

			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_fields')->where("field_type='address'"));
			$dataField = $db->loadObjectList();
			foreach ($dataField as $itemField)
			{
				$fieldSettings = json_decode($itemField->field_settings);
				if ($fieldSettings)
				{
					$fieldSettings->options->vstreetAddress = "1";
					$fieldSettings->options->vstreetAddress2 = "1";
					$fieldSettings->options->vcity = "1";
					$fieldSettings->options->vcode = "1";
					$fieldSettings->options->vstate = "1";
					$fieldSettings->options->vcountry = "1";
					$itemField->field_settings = json_encode($fieldSettings);
				}

				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__jsn_uniform_fields'));
				$query->set("field_settings = " . $db->Quote($itemField->field_settings));
				$query->where($db->quoteName('field_id') . ' = ' . (int) $itemField->field_id);
				$db->setQuery($query);
				$db->execute();
			}
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_fields')->where("field_type='name'"));
			$dataField = $db->loadObjectList();
			foreach ($dataField as $itemField)
			{
				$fieldSettings = json_decode($itemField->field_settings);

				if (!empty($fieldSettings->options->format) && $fieldSettings->options->format == "Normal")
				{
					$fieldSettings->options->vfirst = "1";
					$fieldSettings->options->vlast = "1";
					$fieldSettings->options->size = "jsn-input-small-fluid";
					$itemField->field_settings = json_encode($fieldSettings);
				}
				else
				{
					$fieldSettings->options->vfirst = "1";
					$fieldSettings->options->vlast = "1";
					$fieldSettings->options->vmiddle = "1";
					$fieldSettings->options->vtitle = "1";
					$fieldSettings->options->size = "jsn-input-mini-fluid";
					$itemField->field_settings = json_encode($fieldSettings);
				}

				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__jsn_uniform_fields'));
				$query->set("field_settings = " . $db->Quote($itemField->field_settings));
				$query->where($db->quoteName('field_id') . ' = ' . (int) $itemField->field_id);
				$db->setQuery($query);
				$db->execute();
			}
			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_form_pages'));
			$dataFormPage = $db->loadObjectList();
			if ($dataFormPage)
			{
				foreach ($dataFormPage as $page)
				{
					$pageContent = json_decode($page->page_content);

					if ($pageContent)
					{
						foreach ($pageContent as $i => $itemContent)
						{
							if (isset($itemContent->type) && $itemContent->type == "address")
							{
								$itemContent->options->vstreetAddress = "1";
								$itemContent->options->vstreetAddress2 = "1";
								$itemContent->options->vcity = "1";
								$itemContent->options->vcode = "1";
								$itemContent->options->vstate = "1";
								$itemContent->options->vcountry = "1";
								$pageContent[$i] = $itemContent;
							}
							if (isset($itemContent->type) && $itemContent->type == "name")
							{
								if (!empty($itemContent->options->format) && $itemContent->options->format == "Normal")
								{
									$itemContent->options->vfirst = "1";
									$itemContent->options->vlast = "1";
									$itemContent->options->size = "jsn-input-small-fluid";
									$pageContent[$i] = $itemContent;

								}
								else
								{
									$itemContent->options->vfirst = "1";
									$itemContent->options->vlast = "1";
									$itemContent->options->vmiddle = "1";
									$itemContent->options->vtitle = "1";
									$itemContent->options->size = "jsn-input-mini-fluid";
									$pageContent[$i] = $itemContent;
								}
							}
						}
					}
					$page->page_content = json_encode($pageContent);
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__jsn_uniform_form_pages'));
					$query->set("page_content = " . $db->Quote($page->page_content));
					$query->where($db->quoteName('page_id') . ' = ' . (int) $page->page_id);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		if (!empty($componentInfo->version) && version_compare($componentInfo->version, '1.6.0', '<='))
		{
			$db = JFactory::getDbo();
			$prefix = $db->getPrefix();
			$db->setQuery("SHOW COLUMNS FROM #__jsn_uniform_form_pages");
			$columnForm = $db->loadObjectList();
			$checkBtn = true;
			foreach ($columnForm as $itemForm)
			{
				if (isset($itemForm->Field) && $itemForm->Field == "page_container")
				{
					$checkBtn = false;
				}
			}
			if ($checkBtn)
			{
				$query = "ALTER TABLE " . $db->quoteName($prefix . "jsn_uniform_form_pages") . "  ADD `page_container` LONGTEXT NOT NULL AFTER `page_template`";
				$db->setQuery($query);
				$db->query();
			}
		}

		if (!empty($componentInfo->version) && version_compare($componentInfo->version, '3.0.0', '<='))
		{

			$db = JFactory::getDbo();
			$db->setQuery($db->getQuery(true)->select('form_id')->from('#__jsn_uniform_forms'));
			$forms = $db->loadObjectList();
			foreach($forms as $f){
				$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_config')->where("name='position_form_".$f->form_id."'"));
				$positionForm = $db->loadObject();
				$value = str_replace("sb_","sd_",$positionForm->value);
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__jsn_uniform_config'));
				$query->set("value = " . $db->Quote($value));
				$query->where($db->quoteName('name') . ' = ' . $db->Quote($positionForm->name));
				$db->setQuery($query);
				$db->execute();
			}

			$db = JFactory::getDbo();
			$prefix = $db->getPrefix();
			$db->setQuery("SHOW COLUMNS FROM #__jsn_uniform_forms");
			$columnForm = $db->loadObjectList();
			$checkBtn = true;
			foreach ($columnForm as $itemForm)
			{
				if (isset($itemForm->Field) && $itemForm->Field == "form_settings")
				{
					$checkBtn = false;
				}
			}
			if ($checkBtn)
			{
				$query = "ALTER TABLE " . $db->quoteName($prefix . "jsn_uniform_forms") . "  ADD `form_settings` LONGTEXT NOT NULL AFTER `form_type`";
				$db->setQuery($query);
				$db->query();

				$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_forms'));
				$dataForms = $db->loadObjectList();
				foreach ($dataForms as $data)
				{
					$listButton = new stdClass;
					$listButton->form_btn_next_text = $data->form_btn_next_text;
					$listButton->form_btn_prev_text = $data->form_btn_prev_text;
					$listButton->form_btn_submit_text = $data->form_btn_submit_text;

					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->update($db->quoteName('#__jsn_uniform_forms'));
					$query->set("form_settings = " . $db->Quote(json_encode($listButton)));
					$query->where($db->quoteName('form_id') . ' = ' . (int) $data->form_id);
					$db->setQuery($query);
					$db->execute();
				}
			}


			$db = JFactory::getDbo();
			$prefix = $db->getPrefix();
			$db->setQuery("SHOW COLUMNS FROM #__jsn_uniform_templates");
			$columnForm = $db->loadObjectList();
			$checkField = true;
			foreach ($columnForm as $itemForm)
			{
				if (isset($itemForm->Field) && $itemForm->Field == "template_attach")
				{
					$checkField = false;
				}
			}
			if ($checkField)
			{
				$query = "ALTER TABLE " . $db->quoteName($prefix . "jsn_uniform_templates") . "  ADD `template_attach` TEXT  NOT NULL AFTER `template_message`";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}
