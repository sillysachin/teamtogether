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

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Sample data installation
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplWidgetSample extends JSNTplWidgetBase
{
	/**
	 * Template detailed information
	 * @var array
	 */
	protected $template = array();

	/**
	 * Display agreement screen to ensure start sample
	 * data installation process
	 *
	 * @return  void
	 */
	public function confirmAction ()
	{
		// Make sure current template is not out-of-date
		$update = new JSNTplWidgetUpdate;

		$update->checkUpdateAction();

		$update = $update->getResponse();

		// Render confirm view
		$this->render('confirm', array(
			'template'	=> $this->template,
			'update'	=> $update
		));
	}

	/**
	 * Render installation screen to display all steps
	 * we will walking through for install sample data
	 *
	 * @return  void
	 */
	public function installAction ()
	{
		$sampleVersion	= substr($sampleVersion = JSNTplHelper::getJoomlaVersion(2, false), 0, 1) == '3' ? '30' : $sampleVersion;
		$fileUrl		= 'http://www.joomlashine.com/joomla-templates/'
						. str_replace('_', '-', $this->template['name']) . '-sample-data-j' . $sampleVersion . '.zip';

		// Render confirm view
		$this->render('install', array(
			'template'	=> $this->template,
			'fileUrl'	=> $fileUrl
		));
	}

	/**
	 * This method will be install sample data from
	 * uploaded package
	 *
	 * @return  void
	 */
	public function uploadInstallAction ()
	{
		try
		{
			// Move uploaded file to temporary folder
			if (isset($_FILES['package']))
			{
				$package = $_FILES['package'];
				$config  = JFactory::getConfig();
				$tmpPath = $config->get('tmp_path');
				$destination = $tmpPath . '/' . $this->template['name'] . '_sampledata.zip';

				if ( ! preg_match('/.zip$/i', $package['name']))
				{
					throw new Exception(JText::_('JSN_TPLFW_ERROR_UPLOAD_SAMPLE_DATA_PACKAGE_TYPE'));
				}

				if (move_uploaded_file($package['tmp_name'], $destination))
				{
					// Import library
					jimport('joomla.filesystem.archive');

					$path = pathinfo($destination, PATHINFO_DIRNAME) . '/' . pathinfo($destination, PATHINFO_FILENAME);

					JPath::clean($path);
					JArchive::extract($destination, $path);

					$this->installDataAction();
				}
			}

			$response = json_encode(array(
				'type' => 'success',
				'data' => $this->getResponse()
			));

			echo "<script type=\"text/javascript\">window.parent.uploadSampleDataCallback({$response})</script>";
			jexit();
		}
		catch (Exception $e)
		{
			$response = json_encode(array(
				'type' => 'error',
				'data' => $e->getMessage()
			));

			echo "<script type=\"text/javascript\">window.parent.uploadSampleDataCallback({$response})</script>";
			jexit();
		}
	}

	/**
	 * Sample data package will be downloaded to temporary
	 * folder in this action
	 *
	 * @return  void
	 */
	public function downloadPackageAction ()
	{
		JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

		$config = JFactory::getConfig();
		$tmpPath = $config->get('tmp_path');
		$template = JSNTplTemplateRecognization::detect($this->template['name']);
		$fileUrl = 'http://www.joomlashine.com/joomla-templates/jsn-'
				 . strtolower($template->name . '-' . preg_replace('/\s(STANDARD|UNLIMITED)$/', '', $template->edition))
				 . '-sample-data-j' . (substr($version = JSNTplHelper::getJoomlaVersion(2, false), 0, 1) == '3' ? '30' : $version) . '.zip';

		// Download file to temporary folder
		try
		{
			$response = JSNTplHttpRequest::get($fileUrl, $tmpPath . "/{$this->template['name']}_sampledata.zip");
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Check download response headers
		if ($response['header']['content-type'] != 'application/zip')
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_CANNOT_LOCATED_FILE'));
		}

		$listExtensions = $this->_extractExtensions($tmpPath . "/{$this->template['name']}_sampledata.zip");
		$this->setResponse($listExtensions);
	}

	/**
	 * Action to execute queries from sample data file
	 *
	 * @return  void
	 */
	public function installDataAction ()
	{
		try
		{
			// Create a backup of Joomla database
			$this->_backupDatabase();

			// Initialize variables
			$config		= JFactory::getConfig();
			$tmpPath	= $config->get('tmp_path');
			$xmlFiles	= glob("{$tmpPath}/{$this->template['name']}_sampledata/*.xml");

			if (empty($xmlFiles))
			{
				throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));
			}

			// Load XML document
			$xml		= simplexml_load_file(current($xmlFiles));
			$version	= (string) $xml['version'];
			$joomla_ver	= (string) $xml['joomla-version'];

			// Compare versions
			$templateVersion = JSNTplHelper::getTemplateVersion($this->template['name']);
			$joomlaVersion = new JVersion;

			if (version_compare($templateVersion, $version, '<'))
			{
				throw new Exception(JText::sprintf('JSN_TPLFW_ERROR_SAMPLE_DATA_OUT_OF_DATED', $templateVersion), 99);
			}

			if ( ! empty($joomla_ver) AND version_compare($joomlaVersion->getShortVersion(), $joomla_ver, '<'))
			{
				throw new Exception(JText::sprintf('JSN_TPLFW_ERROR_JOOMLA_OUT_OF_DATE', $joomlaVersion->getShortVersion()), 99);
			}

			// Temporary backup data
			$this->_backupThirdPartyModules();
			$this->_backupThirdPartyAdminModules();
			$this->_backupThirdPartyMenus();

			// Start transaction before manipulate database
			$this->dbo->transactionStart();

			// Delete admin modules
			$this->_deleteThirdPartyAdminModules();

			// Disable execution timeout
			if ( ! JSNTplHelper::isDisabledFunction('set_time_limit'))
			{
				set_time_limit(0);
			}

			$attentions = array();

			// Loop each extension to execute queries
			foreach ($xml->xpath('//extension') AS $extension)
			{
				if (isset($extension['author']) && $extension['author'] == 'joomlashine')
				{
					$extensionType 	= (string) $extension['type'];
					$namePrefix		= array('component' => 'com_', 'module' => 'mod_');
					$extensionName	= isset($namePrefix[(string) $extension['type']])
									? $namePrefix[$extensionType] . $extension['name']
									: (string) $extension['name'];

					// Check if JoomlaShine extension is installed
					$canInstall = JSNTplHelper::isInstalledExtension($extensionName);

					if ($canInstall == false AND $extensionType == 'component')
					{
						// Add to attention list when extension is not installed
						$attentions[] = array(
							'id'	=> (string) $extension['name'],
							'name'	=> (string) $extension['description'],
							'url'	=> (string) $extension['producturl']
						);
					}
				}
				else
				{
					$canInstall = true;
					$extensionName = 'com_' . ((string) $extension['name']);
				}

				if ($canInstall === true)
				{
					// Get sample data queries
					if ($queries = $extension->xpath("task[@name=\"dbinstall\"]/parameters/parameter") AND @count($queries))
					{
						// Execute sample data queries
						foreach ($queries AS $query)
						{
							// Find remote assets then download to local system
							if (preg_match_all('#(http://demo.joomlashine.com/[^\s\t\r\n]+/media/joomlashine/)[^\s\t\r\n]+\.(js|css|bmp|gif|ico|jpg|png|svg|ttf|otf|eot|woff)#', $query, $matches, PREG_SET_ORDER))
							{
								foreach ($matches AS $match)
								{
									$keepAsIs = false;

									if ( ! isset($this->mediaFolder))
									{
										// Detect a writable folder to store demo assets
										foreach (array('media', 'cache', 'tmp') AS $folder)
										{
											$folder = JPATH_ROOT . "/{$folder}";

											if (is_dir($folder) AND is_writable($folder) AND JFolder::create("{$folder}/joomlashine"))
											{
												$this->mediaFolder = "{$folder}/joomlashine";

												break;
											}
										}
									}

									if (isset($this->mediaFolder))
									{
										// Generate path to store demo asset
										$mediaFile = str_replace($match[1], "{$this->mediaFolder}/", $match[0]);

										// Download demo asset only once
										if ( ! is_file($mediaFile))
										{
											try
											{
												JSNTplHttpRequest::get($match[0], $mediaFile);
											}
											catch (Exception $e)
											{
												$keepAsIs = true;
											}
										}

										// Alter sample data query
										if ( ! $keepAsIs)
										{
											$query = str_replace($match[0], str_replace(JPATH_ROOT . '/', '', $mediaFile), $query);
										}
									}
								}
							}

							// Execute query
							$this->dbo->setQuery((string) $query);

							if ( ! $this->dbo->{$this->queryMethod}())
							{
								throw new Exception($this->dbo->getErrorMsg());
							}
						}
					}
				}
			}

			// Restore backed up data
			$this->_restoreThirdPartyData();
			$this->_rebuildMenus();

			// Disable default template
			$q = $this->dbo->getQuery(true);

			$q->update('#__template_styles');
			$q->set('home = 0');
			$q->where('client_id = 0');
			$q->where('home = 1');

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			// Set installed template the default one
			$q = $this->dbo->getQuery(true);

			$q->update('#__template_styles');
			$q->set('home = 1');
			$q->where('id = ' . (int) $this->request->getInt('styleId'));

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			// Commit database change
			$this->dbo->transactionCommit();

			// Clean up temporary data
			JInstallerHelper::cleanupInstall("{$tmpPath}/{$this->template['name']}_sampledata.zip", "{$tmpPath}/{$this->template['name']}_sampledata");

			// Clean up assets table for extension that is not installed
			if (count($attentions))
			{
				foreach ($attentions AS $attention)
				{
					$this->_cleanExtensionAssets('com_' . $attention['id']);
				}
			}

			// Set final response
			$this->setResponse(array('attention' => $attentions));
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Action to handle install extension request.
	 *
	 * @param   string  $id  Identified name of the extension to be installed.
	 *
	 * @return  void
	 */
	public function installExtensionAction($id = null)
	{
		JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

		// Get necessary variables
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		$tmpPath = $config->get('tmp_path');

		if (empty($id))
		{
			$id = $this->request->getString('id');
		}

		// Disable debug system
		$config->set('debug', 0);

		// Path to sample data file
		$xmlFiles = glob("{$tmpPath}/{$this->template['name']}_sampledata/*.xml");

		if (empty($xmlFiles))
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));
		}

		// Load XML document
		$xml = simplexml_load_file(current($xmlFiles));
		$extensions = $xml->xpath("//extension[@identifiedname=\"{$id}\"]");

		if ( ! empty($extensions))
		{
			$extension = current($extensions);
			$name = (string) $extension['name'];
			$type = (string) $extension['type'];

			switch ($type)
			{
				case 'component':
					$name = 'com_' . $name;
				break;

				case 'module':
					$name = 'mod_' . $name;
				break;
			}

			$this->_cleanExtensionAssets($name);

			// Install JSN Extension Framework first if not already installed
			if ($type == 'component')
			{
				// Get details about JSN Extension Framework
				$extfw = $xml->xpath('//extension[@identifiedname="ext_framework"]');

				if ( ! empty($extfw))
				{
					$extfw = current($extfw);

					if ($this->_getExtensionState((string) $extfw['name'], (string) $extfw['version']) != 'installed')
					{
						// Install JSN Extension Framework
						try
						{
							$this->installExtensionAction('ext_framework');
						}
						catch (Exception $e)
						{
							throw $e;
						}
					}
				}
			}
		}

		// Download package from lightcart
		try
		{
			$packageFile = JSNTplApiLightcart::downloadPackage($id, 'FREE', null, null, "{$tmpPath}/{$this->template['name']}_sampledata/");
		}
		catch (Exception $e)
		{
			throw $e;
		}

		if ( ! is_file($packageFile))
		{
			throw new Exception("Package file not found: {$packageFile}");
		}

		// Load extension installation library
		jimport('joomla.installer.helper');

		// Rebuild menu structure
		$this->_rebuildMenus();

		// Extract downloaded package
		$unpackedInfo = JInstallerHelper::unpack($packageFile);
		$installer = JInstaller::getInstance();

		if (empty($unpackedInfo) OR ! isset($unpackedInfo['dir']))
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_EXTENSION_PACKAGE_FILE'));
		}

		// Install extracted package
		$installResult = $installer->install($unpackedInfo['dir']);

		if ($installResult === false)
		{
			foreach (JError::getErrors() AS $error)
			{
				throw $error;
			}
		}

		// Clean up temporary data
		JInstallerHelper::cleanupInstall($packageFile, $unpackedInfo['dir']);

		$this->_activeExtension(
			array(
				'type' => $type,
				'name' => $name
			)
		);

		// Rebuild menu structure
		$this->_rebuildMenus();
	}

	/**
	 * Action to clean files & database for install failure extension
	 *
	 * @return  void
	 */
	public function cleanUpAction ()
	{
		$id = $this->request->getString('id');

		// Retrieve temporary path
		$config		= JFactory::getConfig();
		$tmpPath	= $config->get('tmp_path');

		// Path to sample data file
		$xmlFiles	= glob("{$tmpPath}/{$this->template['name']}_sampledata/*.xml");

		if (empty($xmlFiles))
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));

		// Load XML document
		$xml = simplexml_load_file(current($xmlFiles));

		// Retrieve extension information
		$extensions = $xml->xpath("//extension[@identifiedname=\"{$id}\"]");

		if (empty($extensions)) {
			return;
		}

		$extension = current($extensions);
	}

	/**
	 * Auto enable extension after installed
	 *
	 * @param   array  $extension  Extension information that will enabled
	 *
	 * @return  void
	 */
	private function _activeExtension ($extension)
	{
		$namePrefix		= array('component' => 'com_', 'module' => 'mod_', 'plugin' => '');
		$extensionName	= $extension['name'];

		if (isset($namePrefix[$extension['type']]))
		{
			$extensionName = $namePrefix[$extension['type']] . $extension['name'];
		}

		$extensionFolder = '';

		if (preg_match('/^plugin-([a-z0-9]+)$/i', $extension['type'], $matched))
		{
			$extensionFolder = $matched[1];
		}

		$q = $this->dbo->getQuery(true);

		$q->update('#__extensions');
		$q->set('enabled = 1');
		$q->where('element = ' . $q->quote($extensionName));
		$q->where('folder = ' . $q->quote($extensionFolder));

		$this->dbo->setQuery($q);

		if ( ! $this->dbo->{$this->queryMethod}())
		{
			throw new Exception($this->dbo->getErrorMsg());
		}
	}

	/**
	 * Parse extension list can installation from sample data
	 * package
	 *
	 * @param   string  $packageFile  Sample data package
	 * @return  array
	 */
	private function _extractExtensions ($packageFile)
	{
		// Import library
		jimport('joomla.filesystem.archive');

		$path = pathinfo($packageFile, PATHINFO_DIRNAME) . '/' . pathinfo($packageFile, PATHINFO_FILENAME);

		JPath::clean($path);
		JArchive::extract($packageFile, $path);

		// Find extracted files
		$sampleDataFiles = glob("{$path}/*.xml");

		if ( ! is_array($sampleDataFiles) OR count($sampleDataFiles) == 0)
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));
		}

		// Load XML file
		$sampleData	= simplexml_load_file(current($sampleDataFiles));
		$components = array();

		// Looping to each extension type=component to get information and dependencies
		foreach ($sampleData->xpath('//extension[@author="joomlashine"][@type="component"]') AS $component)
		{
			$attrs				= (array) $component->attributes();
			$attrs				= $attrs['@attributes'];
			$attrs['name']		= sprintf('com_%s', $attrs['name']);
			$attrs['state']		= $this->_getExtensionState($attrs['name'], $attrs['version']);
			$attrs['depends']	= array();

			foreach ($component->dependency->parameter AS $name)
			{
				$dependency = $sampleData->xpath("//extension[@name=\"{$name}\"]");

				if ($name == 'jsnframework' OR empty($dependency))
				{
					continue;
				}

				$dependency					= current($dependency);
				$dependencyAttrs			= (array) $dependency->attributes();
				$dependencyAttrs			= $dependencyAttrs['@attributes'];
				$dependencyAttrs['state']	= $this->_getExtensionState($dependencyAttrs['name'], $dependencyAttrs['version']);

				if ($dependencyAttrs['type'] == 'module')
				{
					$dependencyAttrs['name'] = sprintf('mod_%s', $dependencyAttrs['name']);
				}

				$attrs['depends'][] = $dependencyAttrs;
			}

			$components[] = $attrs;
		}

		return $components;
	}

	/**
	 * This method will be used to find an extension that determined
	 * by name.
	 *
	 * Return "install"    when extension does not installed
	 * Return "update"     when extension is installed and is out of date
	 * Return "installed"  when extension is installed and is up to date
	 *
	 * @param   string  $name     The name of extension
	 * @param   string  $version  Version number that used to determine state
	 *
	 * @return  string
	 */
	private function _getExtensionState ($name, $version)
	{
		$installedExtensions = JSNTplHelper::findInstalledExtensions();

		if (!isset($installedExtensions[$name]))
			return 'install';

		if (version_compare($installedExtensions[$name]->version, $version, '<'))
			return 'update';

		return 'installed';
	}

	/**
	 * Backup data for third party extensions
	 * before install sample data
	 *
	 * @return void
	 */
	private function _backupThirdPartyModules ()
	{
		$builtInModules = array(
			'mod_login', 'mod_stats', 'mod_users_latest',
			'mod_footer', 'mod_stats', 'mod_menu', 'mod_articles_latest', 'mod_languages', 'mod_articles_category',
			'mod_whosonline', 'mod_articles_popular', 'mod_articles_archive', 'mod_articles_categories',
			'mod_articles_news', 'mod_related_items', 'mod_search', 'mod_random_image', 'mod_banners',
			'mod_wrapper', 'mod_feed', 'mod_breadcrumbs', 'mod_syndicate', 'mod_custom', 'mod_weblinks'
		);

		$query = $this->dbo->getQuery(true);
		$query->select('*')
			->from('#__modules')
			->where(sprintf('module NOT IN (\'%s\')', implode('\', \'', $builtInModules)))
			->where('id NOT IN (2, 3, 4, 6, 7, 8, 9, 10, 12, 13, 14, 15, 70)')
			->order('client_id ASC');
		$this->dbo->setQuery($query);
		$this->temporaryModules = $this->dbo->loadAssocList();
	}

	/**
	 * Backup menu assignment for 3rd-party admin modules before install sample data.
	 *
	 * @return void
	 */
	private function _backupThirdPartyAdminModules ()
	{
		$builtInModules = array(
			'mod_login', 'mod_stats', 'mod_users_latest',
			'mod_footer', 'mod_stats', 'mod_menu', 'mod_articles_latest', 'mod_languages', 'mod_articles_category',
			'mod_whosonline', 'mod_articles_popular', 'mod_articles_archive', 'mod_articles_categories',
			'mod_articles_news', 'mod_related_items', 'mod_search', 'mod_random_image', 'mod_banners',
			'mod_wrapper', 'mod_feed', 'mod_breadcrumbs', 'mod_syndicate', 'mod_custom', 'mod_weblinks'
		);

		$query = $this->dbo->getQuery(true);

		$query->select('id');
		$query->from('#__modules');
		$query->where('module NOT IN ("' . implode('", "', $builtInModules) . '")');
		$query->where('client_id = 1');

		$this->dbo->setQuery($query);

		if ($results = $this->dbo->loadColumn())
		{
			$query = $this->dbo->getQuery(true);

			$query->select('*');
			$query->from('#__modules_menu');
			$query->where('moduleid IN ("' . implode('", "', $results) . '")');

			$this->dbo->setQuery($query);

			$this->temporaryAdminModules = $this->dbo->loadAssocList();
		}
	}

	/**
	 * Backup menus data for third party extensions
	 *
	 * @return  void
	 */
	private function _backupThirdPartyMenus ()
	{
		$query = $this->dbo->getQuery(true);
		$query->select('*')
			->from('#__menu')
			->where('client_id=1')
			->where('parent_id=1')
			->order('id ASC');

		$this->dbo->setQuery($query);
		$this->temporaryMenus = array();

		foreach ($this->dbo->loadAssocList() as $row)
		{
			// Fetch children menus
			$query = $this->dbo->getQuery(true);
			$query->select('*')
				->from('#__menu')
				->where('client_id=1')
				->where('parent_id=' . $row['id'])
				->order('lft'); // Add order to correctly insert back those menu items with order later on

			$this->dbo->setQuery($query);
			$childrenMenus = $this->dbo->loadAssocList();

			// Save temporary menus data
			$this->temporaryMenus[] = array(
				'data' => $row,
				'children' => $childrenMenus
			);
		}
	}

	/**
	 * Remove all third party modules in administrator
	 *
	 * @return void
	 */
	private function _deleteThirdPartyAdminModules ()
	{
		$q = $this->dbo->getQuery(true);

		$q->delete('#__modules');
		$q->where('id NOT IN (2, 3, 4, 8, 9, 10, 12, 13, 14, 15)');
		$q->where('client_id = 1');

		$this->dbo->setQuery($q);

		if ( ! $this->dbo->{$this->queryMethod}())
		{
			throw new Exception($this->dbo->getErrorMsg());
		}
	}

	/**
	 * Restore data for third party extensions
	 * after install sample data
	 *
	 * @return void
	 */
	private function _restoreThirdPartyData()
	{
		// Preset an array to hold module id mapping
		$moduleIdMapping = array();

		// Restore 3rd-party modules
		foreach ($this->temporaryModules AS $module)
		{
			// Store old module id
			$oldModuleId = $module['id'];

			// Unset old module id to create new record
			unset($module['id']);

			$tblModule = JTable::getInstance('module');
			$tblModule->bind($module);

			// Disable all restored front-end modules
			$tblModule->client_id == 1 OR $tblModule->published = 0;

			if ( ! $tblModule->store())
			{
				throw new Exception($tblModule->getDbo()->getErrorMsg());
			}

			// Map new id to old module id
			$moduleIdMapping[$oldModuleId] = isset($tblModule->id) ? $tblModule->id : $this->dbo->insertid();
		}

		// Restore menu assignment for 3rd-party admin modules
		foreach ($this->temporaryAdminModules AS $module)
		{
			if (isset($moduleIdMapping[$module['moduleid']]))
			{
				$q = $this->dbo->getQuery(true);

				$q->insert('#__modules_menu');
				$q->columns('moduleid, menuid');
				$q->values($moduleIdMapping[$module['moduleid']] . ', ' . $module['menuid']);

				$this->dbo->setQuery($q);

				try
				{
					$this->dbo->{$this->queryMethod}();
				}
				catch (Exception $e)
				{
					// Do nothing
				}
			}
		}

		// Restore administrator menu
		foreach ($this->temporaryMenus as $menu)
		{
			unset($menu['data']['id']);

			$mainmenu = JTable::getInstance('menu');
			$mainmenu->setLocation(1, 'last-child');
			$mainmenu->bind($menu['data']);

			if ( ! $mainmenu->store())
			{
				throw new Exception($mainmenu->getDbo()->getErrorMsg());
			}

			if ( ! empty($menu['children']))
			{
				foreach ($menu['children'] AS $children)
				{
					$children['id'] = null;
					$children['parent_id'] = $mainmenu->id;

					$submenu = JTable::getInstance('menu');
					$submenu->setLocation($mainmenu->id, 'last-child');
					$submenu->bind($children);

					if ( ! $submenu->store())
					{
						throw new Exception($submenu->getDbo()->getErrorMsg());
					}
				}
			}
		}
	}

	/**
	 * Rebuild menu structure
	 *
	 * @return boolean
	 */
	private function _rebuildMenus ()
	{
		$table 	= JTable::getInstance('Menu', 'JTable');

		if (!$table->rebuild())
			throw new Exception($table->getDbo()->getErrorMsg());

		$query = $this->dbo->getQuery(true);
		$query->select('id, params')
			->from('#__menu')
			->where('params NOT LIKE ' . $this->dbo->quote('{%'))
			->where('params <> ' . $this->dbo->quote(''));

		$this->dbo->setQuery($query);
		$items = $this->dbo->loadObjectList();

		if ($error = $this->dbo->getErrorMsg())
			throw new Exception($error);

		foreach ($items as &$item)
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);

			$q = $this->dbo->getQuery(true);

			$q->update('#__menu');
			$q->set('params = ' . $q->quote((string) $registry));
			$q->where('id = ' . (int) $item->id);

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			unset($registry);
		}

		// Clean the cache
		$this->_cleanCache('com_modules');
		$this->_cleanCache('mod_menu');

		return true;
	}

	/**
	 * Remove component's related records in assets table
	 *
	 * @param   string  $name  The component name
	 *
	 * @return  void
	 */
	private function _cleanExtensionAssets ($name)
	{
		$q = $this->dbo->getQuery(true);

		$q->delete('#__assets');
		$q->where('name = ' . $q->quote($name));

		$this->dbo->setQuery($q);

		if ( ! $this->dbo->{$this->queryMethod}())
		{
			throw new Exception($this->dbo->getErrorMsg());
		}
	}

	/**
	 * Clean cache data for an extension
	 *
	 * @param   string  $extension  Name of extension to clean cache
	 * @return  void
	 */
	private function _cleanCache ($extension)
	{
		$conf = JFactory::getConfig();
		$options = array(
			'defaultgroup' 	=> $extension,
			'cachebase'		=> $conf->get('cache_path', JPATH_SITE . '/cache')
		);

		jimport('joomla.cache.cache');

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();
	}

	/**
	 * Method to backup current Joomla database
	 *
	 * @return  void
	 */
	private function _backupDatabase()
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Preset backup buffer
		$buffer = '';

		// Generate file path to write SQL backup
		$file = $config->get('tmp_path') . '/' . $this->template['name'] . '_original_site_data.sql';
		$numb = 1;

		// Get object for working with Joomla database
		$db = JFactory::getDbo();

		// Get all tables in Joomla database
		$tables = $db->getTableList();

		// Loop thru all tables to backup table structure and data
		foreach ($tables AS $table)
		{
			// Create drop table statement
			$buffer .= (empty($buffer) ? '' : "\n\n") . "DROP TABLE IF EXISTS `{$table}`;";

			// Re-create create table statement
			$create = $db->getTableCreate($table);

			$buffer .= "\n" . array_shift($create) . ';';

			// Get all table columns
			$columns = '`' . implode('`, `', array_keys($db->getTableColumns($table, false))) . '`';

			// Get the number of data row in this table
			$q = $db->getQuery(true);

			$q->select('COUNT(*)');
			$q->from($table);
			$q->where('1');

			$db->setQuery($q);

			if ($max = (int) $db->loadResult())
			{
				for ($offset = 0, $limit = 50; $max - $offset > 0; $offset += $limit)
				{
					// Query for all table data
					$q = $db->getQuery(true);

					$q->select('*');
					$q->from($table);
					$q->where('1');

					$db->setQuery($q, $offset, $limit);

					if ($rows = $db->loadRowList())
					{
						$data = array();

						foreach ($rows AS $row)
						{
							$tmp = array();

							// Prepare data for creating insert statement for each row
							foreach ($row AS $value)
							{
								$tmp[] = $db->quote($value);
							}

							$data[] = implode(', ', $tmp);
						}

						// Create insert statement for fetched rows
						$q2 = $db->getQuery(true);

						$q2->insert($table);
						$q2->columns($columns);
						$q2->values($data);

						// Store insert statement
						$insert = "\n" . str_replace('),(', "),\n(", (string) $q2) . ';';

						// Write generated SQL statements to file if reached 2MB limit
						if (strlen($buffer) + strlen($insert) > 2097152)
						{
							if ( ! JFile::write($file, $buffer))
							{
								throw new Exception(JText::_('JSN_TPLFW_CANNOT_CREATE_BACKUP_FILE'));
							}

							// Rename current backup file if neccessary
							if ($numb == 1)
							{
								JFile::move($file, substr($file, 0, -4) . '.01.sql');
							}

							// Increase number of backup file
							$numb++;

							// Generate new backup file name
							$file = $config->get('tmp_path') . '/' . $this->template['name'] . '_original_site_data.' . ($numb < 10 ? '0' : '') . $numb . '.sql';

							// Reset backup buffer
							$buffer = trim($insert);
						}
						else
						{
							$buffer .= $insert;
						}
					}
					else
					{
						break;
					}
				}
			}
		}

		if ( ! JFile::write($file, $buffer))
		{
			throw new Exception(JText::_('JSN_TPLFW_CANNOT_CREATE_BACKUP_FILE'));
		}

		// Get list of backup file
		$files = glob($config->get('tmp_path') . '/' . $this->template['name'] . '_original_site_data.*');

		foreach ($files AS $k => $file)
		{
			// Create array of file name and content for making archive later
			$files[$k] = array(
				'name' => basename($file),
				'data' => JFile::read($file)
			);
		}

		// Create backup archive
		$archiver = new JSNTplArchiveZip;
		$zip_path = JPATH_ROOT . '/templates/' . $this->template['name'] . '/backups/' . date('y-m-d_H-i-s') . '_original_site_data.zip';

		if ($archiver->create($zip_path, $files))
		{
			// Remove all SQL backup file created previously in temporary directory
			foreach ($files AS $file)
			{
				JFile::delete($config->get('tmp_path') . '/' . $file['name']);
			}
		}
	}
}
