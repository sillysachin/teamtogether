<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
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
 * Template update widget
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplWidgetUpdate extends JSNTplWidgetBase
{
	private $_templateVersionData;

	/**
	 * Checking template version for auto update
	 *
	 * @return  void
	 */
	public function checkUpdateAction ()
	{
		$response = array();

		try
		{
			$templateVersion	= JSNTplHelper::getTemplateVersion($this->template['name']);
			$templateInfo		= JSNTplApiLightcart::getProductDetails('cat_template', $this->template['id']);

			$response['template'] = array(
				'currentVersion'	=> $templateVersion,
				'newVersion'		=> $templateInfo->version,
				'hasUpdate'			=> version_compare($templateInfo->version, $templateVersion, '>')
			);
		}
		catch (Exception $ex)
		{
			$response['template'] = array(
				'currentVersion'	=> $templateVersion,
				'newVersion'		=> $templateVersion,
				'hasUpdate'			=> false
			);
		}

		try
		{
			$frameworkInfo = JSNTplApiLightcart::getProductDetails('cat_template', JSN_TPLFRAMEWORK_ID);

			$response['framework'] = array(
				'currentVersion'	=> JSN_TPLFRAMEWORK_VERSION,
				'newVersion'		=> $frameworkInfo->version,
				'hasUpdate'			=> version_compare($frameworkInfo->version, JSN_TPLFRAMEWORK_VERSION, '>')
			);
		}
		catch (Exception $ex)
		{
			$response['framework'] = array(
				'currentVersion'	=> JSN_TPLFRAMEWORK_VERSION,
				'newVersion'		=> JSN_TPLFRAMEWORK_VERSION,
				'hasUpdate'			=> false
			);
		}

		$this->setResponse($response);
	}

	/**
	 * Authentication action before install sample data
	 *
	 * @return  void
	 */
	public function confirmAction ()
	{
		$target = $this->request->getString('target');

		if ($target == 'framework')
		{
			// Check if template has update also
			$this->checkUpdateAction();
			$result = $this->getResponse();

			$this->render('framework_confirm', array(
				'manifest'			=> JSNTplHelper::getManifestCache('jsntplframework'),
				'template'			=> $this->template['name'],
				'templateHasUpdate'	=> $result['template']['hasUpdate']
			));

			return;
		}

		// Process posted back data that sent from client
		if ($this->request->getMethod() == 'POST')
		{
			// Checking customer information
			$input = JFactory::getApplication()->input;
			$username = $input->getString('username', '');
			$password = $input->getString('password', '');

			// Try retrieve ordered editions to check customer information
			try
			{
				JSNTplApiLightcart::getOrderedEditions($this->template['id'], $username, $password);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return;
		}

		// Retrieve version data
		try
		{
			$versionData = JSNTplHelper::getVersionData();
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Find template information by identify name
		foreach ($versionData['items'] AS $item)
		{
			if ($item['identified_name'] == $this->template['id'])
			{
				if ((isset($item['edition']) AND ! empty($item['edition'])) OR (@is_array($item['editions']) AND count($item['editions']) == 1))
				{
					$template = @is_array($item['editions']) ? $item['editions'][0] : $item;

					// Render login view
					$this->render('confirm', array(
						'template' => $this->template,
						'authenticate' => $template['authentication']
					));
				}
				else
				{
					foreach ($item['editions'] AS $template)
					{
						$edition = trim($template['edition']);

						if (str_replace('PRO ', '', $this->template['edition']) == str_replace('PRO ', '', $edition))
						{
							// Render login view
							$this->render('confirm', array(
								'template' => $this->template,
								'authenticate' => $template['authentication']
							));

							break;
						}
					}
				}

				break;
			}
		}
	}

	/**
	 * Render UI for install update screen
	 *
	 * @return  void
	 */
	public function installAction ()
	{
		$target = $this->request->getString('target');
		if ($target == 'framework') {
			$this->render('framework_install', array(
				'manifest' => JSNTplHelper::getManifestCache('jsntplframework')
			));

			return;
		}

		$this->render('install', array('template' => $this->template));
	}

	/**
	 * Download update package for template
	 *
	 * @return  void
	 */
	public function downloadAction ()
	{
		// Process posted back data that sent from client
		if ($this->request->getMethod() == 'POST')
		{
			// Checking customer information
			$input = JFactory::getApplication()->input;
			$username = $input->getString('username', '');
			$password = $input->getString('password', '');

			// Load template xml file
			$edition = strtoupper(trim($this->template['edition']));

			if ($edition != 'FREE' AND strpos($edition, 'PRO ') === false)
			{
				$edition = 'PRO ' . $edition;
			}

			JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

			// Download package file
			try
			{
				JSNTplApiLightcart::downloadPackage($this->template['id'], $edition, $username, $password);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}

	/**
	 * Check files modification state based on checksum.
	 * Files that are not being updated will be ignored.
	 *
	 * @return  void
	 */
	public function checkBeforeUpdateAction()
	{
		$packageFile = JFactory::getConfig()->get('tmp_path') . '/jsn-' . $this->template['id'] . '.zip';

		// Check if downloaded template package existen
		if ( ! is_readable($packageFile)) {
			throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_PACKAGE_FILE_NOT_FOUND'));
		}

		// Get list of modified files that are being updated
		$modifiedFiles		= JSNTplHelper::getModifiedFilesBeingUpdated($this->template['name'], $packageFile);
		$hasModification	= count($modifiedFiles);

		// Backup modified files that are being updated
		if ($hasModification)
		{
			$integrity = new JSNTplWidgetIntegrity;
			$integrity->backupAction();
		}

		$this->setResponse(array(
			'hasModification'	=> (boolean) $hasModification
		));
	}

	/**
	 * Start process to install template update
	 *
	 * @return  void
	 */
	public function installPackageAction ()
	{
		// Initialize variables
		$joomlaConfig	= JFactory::getConfig();
		$packageFile	= $joomlaConfig->get('tmp_path') . '/jsn-' . $this->template['id'] . '.zip';
		$packagePath	= substr($packageFile, 0, -4);
		$templatePath	= JPATH_ROOT . '/templates/' . $this->template['name'];

		// Checking downloaded template package
		if ( ! is_file($packageFile))
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_PACKAGE_FILE_NOT_FOUND'));
		}

		// Check if template is copied to another name
		if ($xml = simplexml_load_file($packagePath . '/template/templateDetails.xml'))
		{
			if (strcasecmp($this->template['name'], trim((string) $xml->name)) != 0)
			{
				// Update templateDetails.xml with new name
				$content = str_replace((string) $xml->name, $this->template['name'], JFile::read($packagePath . '/template/templateDetails.xml'));

				JFile::write($packagePath . '/template/templateDetails.xml', $content);
			}
		}

		// Get list of files to be updated
		try
		{
			$update = JSNTplHelper::getFilesBeingUpdated($this->template['name'], $packageFile);

			if ( ! $update)
			{
				throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_PACKAGE_FILE_NOT_FOUND'));
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Include template checksum and manifest files
		in_array('template.checksum', $update['edit']) OR $update['edit'][] = 'template.checksum';
		in_array('templateDetails.xml', $update['edit']) OR $update['edit'][] = 'templateDetails.xml';

		// Import necessary libraries
		jimport('joomla.filesystem.file');
		jimport('joomla.installer.helper');

		// Update the template
		foreach ($update AS $action => $files)
		{
			foreach ($files AS $file)
			{
				if ($action != 'add')
				{
					JFile::delete($templatePath . '/' . $file);
				}

				if ($action != 'delete' AND JFolder::create(dirname($templatePath . '/' . $file)))
				{
					JFile::copy($packagePath . '/template/' . $file, $templatePath . '/' . $file);
				}
			}
		}

		// Move backup file to template directory
		$source = $joomlaConfig->get('tmp_path') . '/' . $this->template['name'] . '_modified_files.zip';
		$target = $templatePath . '/backups/' . date('y-m-d_H-i-s') . '_modified_files.zip';

		if (is_readable($source))
		{
			JFile::copy($source, $target);

			// Remove backup file in temporary directory
			filesize($source) != filesize($target) OR JFile::delete($source);
		}

		// Clean up temporary data
		JInstallerHelper::cleanupInstall($packageFile, $packagePath);

		// Check if update success
		$messages = JFactory::getApplication()->getMessageQueue();

		if (class_exists('JError'))
		{
			$messages = array_merge(JError::getErrors(), $messages);
		}

		foreach ($messages AS $message)
		{
			if (
				(is_array($message) AND @$message['type'] == 'error')
				OR
				(is_object($message) AND ( ! method_exists($message, 'get') OR $message->get('level') == E_ERROR))
			)
			{
				$msg = str_replace(JPATH_ROOT, '', is_array($message) ? $message['message'] : $message->getMessage());
				$errors[$msg] = 1;
			}
		}

		if (@count($errors))
		{
			throw new Exception('<ul><li>' . implode('</li><li>', array_keys($errors)) . '</li></ul>');
		}

		// Update template version in manifest cache
		$manifest = JSNTplHelper::getManifest($this->template['name'], true);
		$template = JTable::getInstance('extension');

		$template->load(array(
			'type'		=> 'template',
			'element'	=> $this->template['name']
		));

		if ($template->extension_id)
		{
			// Decode manifest cache
			$template->manifest_cache = json_decode($template->manifest_cache);

			// Set new template version
			$template->manifest_cache->version = (string) $manifest->version;

			// Re-encode manifest cache
			$template->manifest_cache = json_encode($template->manifest_cache);

			// Store new data
			$template->store();
		}

		// Update template version in template definition file
		$content = preg_replace(
			'/\$JoomlaShine_Template_Version = \'[^\']+\';/i',
			'$JoomlaShine_Template_Version = \'' . (string) $manifest->version . '\';',
			JFile::read($templatePath . '/template.defines.php')
		);

		JFile::write($templatePath . '/template.defines.php', $content);

		// Clear backup state
		JFactory::getApplication()->setUserState('jsn-tplfw-backup-done', 0);

		// Clean up compressed files
		$this->_cleanCache();
	}

	public function downloadFrameworkAction()
	{
		if (!JSNTplHelper::isDisabledFunction('set_time_limit')) {
			set_time_limit(0);
		}

		// Download package file
		try
		{
			JSNTplApiLightcart::downloadPackage('tpl_framework');
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	public function installFrameworkAction ()
	{
		$packageFile = JFactory::getConfig()->get('tmp_path') . '/jsn-tpl_framework.zip';

		// Checking downloaded template package
		if (!is_file($packageFile)) {
			throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_PACKAGE_FILE_NOT_FOUND'));
		}

		// Load install library
		jimport('joomla.installer.helper');

		// Turn off debug mode to catch install error
		$conf = JFactory::getConfig();
		$conf->set('debug', 0);

		$unpackedInfo	= JInstallerHelper::unpack($packageFile);
		$installer		= new JInstaller();
		$installer->setUpgrade(true);
		$installResult	= $installer->install($unpackedInfo['dir']);

		// Clean up temporary data
		JInstallerHelper::cleanupInstall($packageFile, $unpackedInfo['dir']);

		// Clean up compressed files
		$this->_cleanCache();

		// Send error if install is failure
		if (class_exists('JError')) {
			$error = JError::getError();
			if (!empty($error))
				throw $error;
		}
	}

	/**
	 * Keep this method for backward compatible with template framework pre-1.1.0 version.
	 *
	 * @return  void
	 */
	public function backupAction()
	{
		// Backup modified files
		$integrity = new JSNTplWidgetIntegrity;
		$integrity->backupAction();

		// Set response
		$this->setResponse('index.php?widget=integrity&action=download&type=update&template=' . $this->template['name']);
	}

	private function _cleanCache()
	{
		// Import necessary library
		jimport('joomla.filesystem.folder');

		// Get all template style parameter
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('params');
		$q->from('#__template_styles');
		$q->where('template = ' . $q->quote($this->template['name']));

		$db->setQuery($q);

		if ($styles = $db->loadColumn())
		{
			foreach ($styles AS $style)
			{
				$cache = 'cache/';

				if ($params = json_decode($style) AND isset($params->cacheDirectory))
				{
					$cache = $params->cacheDirectory;
				}

				if ( ! preg_match('#^(/|\\|[a-z]:)#i', $cache))
				{
					$cache = JPATH_ROOT . '/' . rtrim($cache, '\\/');
				}
				else
				{
					$cache = rtrim($cache, '\\/');
				}

				$cache = $cache . '/' . $this->template['name'];

				// Clean up compressed files
				! is_dir($cache) OR JFolder::delete($cache);
			}
		}
	}
}
