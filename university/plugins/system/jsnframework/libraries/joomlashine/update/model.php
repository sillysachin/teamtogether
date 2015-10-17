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

// Import Joomla libraries
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.installer.installer');

/**
 * Model class of JSN Update library.
 *
 * To implement <b>JSNUpdateModel</b> class, create a model file in
 * <b>administrator/components/com_YourComponentName/models</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixModelUpdate extends JSNUpdateModel
 * {
 * }</code>
 *
 * The <b>JSNUpdateModel</b> class pre-defines <b>download</b> and
 * <b>install</b> method to handle product update task. So, you <b>DO NOT
 * NEED</b> to re-define those methods in your model class.
 *
 * <b>JSNUpdateModel</b> class has following protected methods that you can
 * overwrite in your model class to customize product update task:
 *
 * <ul>
 *     <li>beforeDownload()</li>
 *     <li>afterDownload($path)</li>
 *     <li>beforeInstall($path)</li>
 *     <li>afterInstall($path)</li>
 * </ul>
 *
 * If you overwrite any of 4 methods above, remember to call parent method
 * either before or after your customization in order to make JSN Update library
 * working properly. See example below:
 *
 * <code>class YourComponentPrefixModelUpdate extends JSNUpdateModel
 * {
 *     protected function beforeDownload()
 *     {
 *         parent::beforeDownload();
 *
 *         // Do some additional preparation...
 *     }
 *
 *     protected function afterInstall($path)
 *     {
 *         // Do some additional finalization...
 *
 *         parent::afterInstall($path);
 *     }
 * }</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUpdateModel extends JSNBaseModel
{
	/**
	 * Download update package.
	 *
	 * @return  void
	 */
	public function download()
	{
		// Do any preparation needed before downloading update package
		try
		{
			$this->beforeDownload();
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Download update package
		try
		{
			$path = $this->downloadPackage();
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Do any extra work needed after downloading update package
		try
		{
			$this->afterDownload($path);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Complete AJAX based download task
		jexit('DONE: ' . basename($path));
	}

	/**
	 * Do any preparation needed before downloading update package.
	 *
	 * @return  void
	 */
	protected function beforeDownload()
	{
	}

	/**
	 * Download update package for current product.
	 *
	 * @return  void
	 */
	protected function downloadPackage()
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Initialize variable
		$input	= JFactory::getApplication()->input;
		$JVersion	= new JVersion;

		// Get the product info
		$info		= JSNUtilsXml::loadManifestCache();
		$edition	= $input->getVar('edition', JSNUtilsText::getConstant('EDITION'));
		$identified	= ($identified	= JSNUtilsText::getConstant('IDENTIFIED_NAME')) ? $identified : strtolower($info->name);

		// Build query string
		$query[] = 'joomla_version=' . $JVersion->RELEASE;
		$query[] = 'username=' . urlencode($input->getUsername('customer_username'));
		$query[] = 'password=' . urlencode($input->getString('customer_password'));
		$query[] = 'identified_name=' . ($input->getCmd('id') ? $input->getCmd('id') : $identified);
		$query[] = 'edition=' . strtolower(urlencode($edition));

		// Build final URL for downloading update
		$url = JSN_EXT_DOWNLOAD_UPDATE_URL . '&' . implode('&', $query);

		// Generate file name for update package
		$name[] = 'jsn';
		$name[] = $input->getCmd('id') ? $input->getCmd('id') : $identified;

		if ($edition)
		{
			$name[]	= $input->getCmd('view') == 'upgrade'
					? 'pro_' . (strtolower($edition) == 'free' ? 'standard' : 'unlimited')
					: strtolower(str_replace(' ', '_', $input->getVar('edition') ? $input->getVar('edition') : $edition));
		}

		$name[] = 'j' . $JVersion->RELEASE;
		$name[] = 'install.zip';
		$name   = implode('_', $name);

		// Set maximum execution time
		ini_set('max_execution_time', 300);

		// Try to download the update package
		try
		{
			$path = $config->get('tmp_path') . '/' . $name;

			if ( ! JSNUtilsHttp::get($url, $path, true))
			{
				throw new Exception(JText::_('JSN_EXTFW_UPDATE_DOWNLOAD_PACKAGE_FAIL'));
			}
		}
		catch (Exception $e)
		{
			throw new Exception(JText::_('JSN_EXTFW_UPDATE_DOWNLOAD_PACKAGE_FAIL'));
		}

		// Validate downloaded update package
		if (filesize($path) < 10)
		{
			// Get LightCart error code
			$errorCode = JFile::read($path);

			if ($edition)
			{
				$edition = $input->getCmd('view') == 'upgrade'
					? 'pro ' . (strtolower($edition) == 'free' ? 'standard' : 'unlimited')
					: $input->getVar('edition') ? $input->getVar('edition') : $edition;
			}

			throw new Exception(JText::sprintf('JSN_EXTFW_LIGHTCART_ERROR_' . $errorCode, JText::_($info->name) . ' ' . strtoupper($edition)));
		}

		return $path;
	}

	/**
	 * Do any extra work needed after downloading update package.
	 *
	 * @param   string  $path  Path to downloaded update package.
	 *
	 * @return  void
	 */
	protected function afterDownload($path)
	{
	}

	/**
	 * Install downloaded update package.
	 *
	 * @param   string  $path  Path to downloaded update package.
	 *
	 * @return  void
	 */
	public function install($path)
	{
		$config = JFactory::getConfig();

		// Initialize update package path
		$path = $config->get('tmp_path') . '/' . $path;

		if ( ! is_file($path))
		{
			throw new Exception(JText::_('JSN_EXTFW_PACKAGE_FILE_NOT_FOUND') . ': ' . $path);
		}

		// Extract update package
		if ( ! JArchive::extract($path, substr($path, 0, -4)))
		{
			throw new Exception(JText::_('JSN_EXTFW_UPDATE_EXTRACT_PACKAGE_FAIL'));
		}
		$path = substr($path, 0, -4);

		// Do any preparation needed before installing update package
		try
		{
			$this->beforeInstall($path);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Get Joomla version object
		$JVersion = new JVersion;

		// Switch off debug mode to catch JInstaller error message manually
		$config	= JFactory::getConfig();
		$debug	= $config->get('debug');

		$config->set('debug', version_compare($JVersion->RELEASE, '3.0', '<') ? false : true);

		// Install update package
		$installer = JInstaller::getInstance();

		try
		{
			$installer->update($path);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Restore debug settings
		$config->set('debug', $debug);

		// Check if installation success
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
				$errors[is_array($message) ? $message['message'] : $message->getMessage()] = 1;
			}
		}

		if (@count($errors))
		{
			throw new JException('<ul><li>' . implode('</li><li>', array_keys($errors)) . '</li></ul>');
		}

		// Do any extra work needed after installing update package
		try
		{
			$this->afterInstall($path);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Complete AJAX based update package installation task
		jexit('DONE');
	}

	/**
	 * Do any preparation needed before installing update package.
	 *
	 * @param   string  $path  Path to downloaded update package.
	 *
	 * @return  void
	 */
	protected function beforeInstall($path)
	{
		// Get product config
		$xml = JSNUtilsXml::load(JPATH_COMPONENT_ADMINISTRATOR . '/config.xml');

		// Build backup options
		$backupOptions = array('no-download' => true);

		if (is_array($xml->xpath('//field[@type="databackup"]/option')))
		{
			foreach ($xml->xpath('//field[@type="databackup"]/option') AS $option)
			{
				// Parse option parameters
				$value = array();

				if ( (string) $option['type'] == 'tables')
				{
					// Generate option value
					foreach ($option->table AS $param)
					{
						$value[] = (string) $param;
					}
				}
				elseif ( (string) $option['type'] == 'files')
				{
					// Generate option value
					foreach ($option->folder AS $param)
					{
						$value[(string) $param] = (string) $param['filter'];
					}
				}
				else
				{
					continue;
				}

				$backupOptions[(string) $option['type']][] = json_encode($value);
			}
		}

		// Backup the product data
		try
		{
			$this->backup = new JSNDataModel;
			$this->backup = $this->backup->backup($backupOptions);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Do any extra work needed after installing update package.
	 *
	 * @param   string  $path  Path to downloaded update package.
	 *
	 * @return  void
	 */
	protected function afterInstall($path)
	{
		// Clean-up temporary folder and file
		JFolder::delete($path);
		JFile::delete("{$path}.zip");

		// Restore the backed up product data
		try
		{
			$data = new JSNDataModel;
			$data->restore($this->backup);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
}
