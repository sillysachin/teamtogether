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

// Import necessary Joomla libraries
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.installer.helper');

if (JSNVersion::isJoomlaCompatible('3.0'))
{
	jimport('joomla.archive.archive');
}
else
{
	jimport('joomla.filesystem.archive');
}

/**
 * Model class of JSN Data library.
 *
 * To implement <b>JSNDataModel</b> class, create a model file in
 * <b>administrator/components/com_YourComponentName/models</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixModelData extends JSNDataModel
 * {
 * }</code>
 *
 * The <b>JSNDataModel</b> class pre-defines <b>backup</b>, <b>restore</b>
 * and <b>installSample</b> method to handle data backup/restore and sample data
 * installation task. So, you <b>DO NOT NEED</b> to re-define those methods in
 * your model class.
 *
 * <b>JSNDataModel</b> class has following protected methods that you can
 * overwrite in your model class to customize data backup/restore task:
 *
 * <ul>
 *     <li>beforeBackup(&amp;$options, &amp;$name)</li>
 *     <li>afterBackup(&amp;$options, &amp;$name)</li>
 *     <li>beforeRestore(&amp;$backup)</li>
 *     <li>afterRestore(&amp;$backup)</li>
 * </ul>
 *
 * If you overwrite any of 4 methods above, remember to call parent method
 * either before or after your customization in order to make JSN Data library
 * working properly. See example below:
 *
 * <code>class YourComponentPrefixModelData extends JSNDataModel
 * {
 *     protected function beforeBackup(&amp;$options, &amp;$name)
 *     {
 *         parent::beforeBackup(&amp;$options, &amp;$name);
 *
 *         // Do some additional preparation...
 *     }
 *
 *     protected function afterRestore(&amp;$backup)
 *     {
 *         // Do some additional finalization...
 *
 *         parent::afterRestore(&amp;$backup);
 *     }
 * }</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNDataModel extends JSNBaseModel
{
	/**
	 * Variable for holding backed up data.
	 *
	 * @var	SimpleXMLElement
	 */
	protected $data;

	/**
	 * Variable for holding backed up files.
	 *
	 * @var	array
	 */
	protected $files;

	/**
	 * Backup selected database tables and/or files.
	 *
	 * @param   array  $options  Backup options.
	 *
	 * @return  void
	 */
	public function backup($options = array())
	{
		// Get Joomla config and version object
		$config	= JFactory::getConfig();
		$jVer	= new JVersion;

		// Initialize variables
		$tag	= isset($options['xmlRoot']) ? $options['xmlRoot'] : 'backup';
		$com	= preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option'));
		$info	= JSNUtilsXml::loadManifestCache();

		$name	= (isset($options['name']) AND ! empty($options['name']))
				? ($options['name'] . (@$options['timestamp'] ? '_' . date('YmdHis') : ''))
				: date('YmdHis');

		$name	= array(
			'zip' => "{$name}.zip",
			'xml' => "jsn_{$com}_backup_db.xml"
		);

		// Preset XML object for holding backed up data
		$this->data = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><' . $tag . '></' . $tag . '>');

		$this->data->addAttribute('extension-name',    'JSN ' . preg_replace('/JSN\s*/i', '', JText::_($info->name)));

		if ($const = JSNUtilsText::getConstant('EDITION'))
		{
			$this->data->addAttribute('extension-edition', $const);
		}

		$this->data->addAttribute('extension-version', $info->version);
		$this->data->addAttribute('joomla-version',    $jVer->getShortVersion());

		// Do any preparation needed before doing real data backup
		try
		{
			$this->beforeBackup($options, $name);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Backup data from selected tables
		$this->backupTables($options['tables']);

		// Backup files from selected folders
		$this->backupFiles($options['files']);

		// Do any extra work needed after doing real data backup
		try
		{
			$this->afterBackup($options, $name);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Force client to download backup file
		if (isset($this->zippedBackup))
		{
			if ( ! isset($options['no-download']) OR ! $options['no-download'])
			{
				JSNUtilsFile::forceDownload($name['zip'], $this->zippedBackup, 'application/zip', true);
			}
			else
			{
				// Store zipped backup to file system
				JFile::write($config->get('tmp_path') . '/' . $name['zip'], $this->zippedBackup);

				return $config->get('tmp_path') . '/' . $name['zip'];
			}
		}

		throw new Exception(JText::_('JSN_EXTFW_DATA_BACKUP_FAIL'));
	}

	/**
	 * Do any preparation needed before doing real data backup.
	 *
	 * @param   array  &$options  Backup options.
	 * @param   array  &$name     array('zip' => 'zip_backup_file_name', 'xml' => 'xml_backup_file_name')
	 *
	 * @return  void
	 */
	protected function beforeBackup(&$options, &$name)
	{
		// Preset variables
		isset($options['tables']) OR $options['tables'] = array();
		isset($options['files'])  OR $options['files']  = array();

		// Decode variables
		$tables = array();

		foreach ($options['tables'] AS $value)
		{
			$tables = array_merge($tables, json_decode(html_entity_decode($value, ENT_QUOTES, 'UTF-8')));
		}
		$options['tables'] = array_unique($tables);

		$files = array();

		foreach ($options['files'] AS $value)
		{
			$files = array_merge($files, (array) json_decode(html_entity_decode($value, ENT_QUOTES, 'UTF-8')));
		}
		$options['files'] = array_unique($files, SORT_REGULAR);
	}

	/**
	 * Backup data from selected database tables.
	 *
	 * @param   array  $tables  Array of table to dump data from.
	 *
	 * @return  void
	 */
	protected function backupTables($tables)
	{
		// Create parent node for storing dumped data
		$this->data->addChild('tables');

		// Get database object
		$db = JFactory::getDbo();

		foreach ($tables AS $table)
		{
			$query = $db->getQuery(true);

			$query->select('*');
			$query->from($table);

			$db->setQuery($query);

			try
			{
				if ($rows = $db->loadAssocList())
				{
					$this->storeTableData($table, $rows);
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}

	/**
	 * Store backed up table data to XML object.
	 *
	 * @param   array  $table  Name of data table.
	 * @param   array  $rows   Dumped data from the table.
	 *
	 * @return  void
	 */
	protected function storeTableData($table, $rows)
	{
		// Create new node for storing backed up table data
		$node = $this->data->tables->addChild('table');
		$node->addAttribute('name', $table);

		// Store backed up table data to table node
		$node = $node->addChild('rows');

		foreach ($rows AS $row)
		{
			// Create new node for storing current row of data
			$rowNode = $node->addChild('row');

			foreach ($row AS $name => $value)
			{
				$rowNode->addChild($name, htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false));
			}
		}
	}

	/**
	 * Backup files from selected folders.
	 *
	 * @param   array  $folders  Array of folder to backup file.
	 *
	 * @return  void
	 */
	protected function backupFiles($folders)
	{
		// Preset files array
		$this->files = array();

		foreach ($folders AS $folder => $filter)
		{
			// Get list of file need to backup
			if ($files = JFolder::files(JPATH_ROOT . '/' . $folder, $filter, true, true))
			{
				foreach ($files AS $file)
				{
					// Append to files array
					$this->files[] = $file;
				}
			}
		}
	}

	/**
	 * Do any extra work needed after doing real data backup.
	 *
	 * @param   array  &$options  Backup options.
	 * @param   array  &$name     array('zip' => 'zip_backup_file_name', 'xml' => 'xml_backup_file_name')
	 *
	 * @return  void
	 */
	protected function afterBackup(&$options, &$name)
	{
		// Create zip file containing all backed up data and/or files
		try
		{
			$this->finalizeBackup($name);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Create downloadable zip-archived backup file.
	 *
	 * @param   array  $name  array('zip' => 'zip_backup_file_name', 'xml' => 'xml_backup_file_name')
	 *
	 * @return  void
	 */
	protected function finalizeBackup($name)
	{
		// Get XML data from XML object
		if (($data = $this->data->asXML()) === false)
		{
			throw new Exception(JText::_('JSN_EXTFW_DATA_XML_GENERATION_FAIL'));
		}

		// Prepend XML file to the array of backed up files
		array_unshift($this->files, array($name['xml'] => $data));

		// Create zip file containing all backed up data and/or files
		if ( ! ($this->zippedBackup = JSNUtilsArchive::createZip($this->files)))
		{
			throw new Exception(JText::_('JSN_EXTFW_DATA_ARCHIVE_CREATION_FAIL'));
		}
	}

	/**
	 * Restore database table data and/or files from backup.
	 *
	 * @param   mixed    $backup        Either path to an existing file or a variable of $_FILES.
	 * @param   boolean  $checkEdition  Check for matching edition before restore?
	 *
	 * @return  void
	 */
	public function restore($backup, $checkEdition = true)
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Initialize backup file
		if (is_array($backup))
		{
			if ( ! JFile::upload($backup['tmp_name'], $config->get('tmp_path') . '/' . $backup['name']))
			{
				throw new Exception(JText::_('JSN_EXTFW_GENERAL_MOVE_UPLOAD_FILE_FAIL'));
			}
			$backup = $config->get('tmp_path') . '/' . $backup['name'];
		}
		elseif ( ! preg_match('/^(\/|[a-z]:)/i', $backup))
		{
			$backup = JPATH_ROOT . '/' . $backup;
		}

		// Do any preparation needed before doing real data restore
		try
		{
			$this->beforeRestore($backup, $checkEdition);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Backup data from selected tables
		$this->restoreTables();

		// Backup files from selected folders
		$this->restoreFiles($backup);

		// Do any extra work needed after doing real data restore
		try
		{
			$this->afterRestore($backup);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Do any preparation needed before doing real data restore.
	 *
	 * @param   string   &$backup       Path to folder containing extracted backup files.
	 * @param   boolean  $checkEdition  Check for matching edition before restore?
	 *
	 * @return  void
	 */
	protected function beforeRestore(&$backup, $checkEdition = true)
	{
		// Initialize variables
		$com	= preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option'));
		$info	= JSNUtilsXml::loadManifestCache();
		$jVer	= new JVersion;

		// Extract backup file
		if ( ! JArchive::extract($backup, substr($backup, 0, -4)))
		{
			throw new Exception(JText::_('JSN_EXTFW_DATA_EXTRACT_UPLOAD_FILE_FAIL'));
		}
		$backup = substr($backup, 0, -4);

		// Auto-detect backup XML file
		$files = glob("{$backup}/*.xml");

		foreach ($files AS $file)
		{
			$this->data = JSNUtilsXml::load($file);

			// Check if this XML file contain backup data for our product
			if (strcasecmp($this->data->getName(), 'backup') == 0 AND isset($this->data['extension-name']) AND isset($this->data['extension-version']) AND isset($this->data['joomla-version']))
			{
				// Store backup XML file name
				$this->xml = basename($file);

				// Simply break the loop if we found backup file
				break;
			}

			unset($this->data);
		}

		if (isset($this->data))
		{
			// Check if Joomla series match
			if ( ! $jVer->isCompatible((string) $this->data['joomla-version']))
			{
				throw new Exception(JText::_('JSN_EXTFW_DATA_JOOMLA_VERSION_NOT_MATCH'));
			}

			// Check if extension match
			if ( (string) $this->data['extension-name'] != 'JSN ' . preg_replace('/JSN\s*/i', '', JText::_($info->name)))
			{
				throw new Exception(JText::_('JSN_EXTFW_DATA_INVALID_PRODUCT'));
			}
			elseif (isset($this->data['extension-edition']) AND $checkEdition
				AND ( ! ($const = JSNUtilsText::getConstant('EDITION')) OR (string) $this->data['extension-edition'] != $const))
			{
				throw new Exception(JText::_('JSN_EXTFW_DATA_INVALID_PRODUCT_EDITION'));
			}
			elseif ( ! version_compare($info->version, (string) $this->data['extension-version'], 'ge'))
			{
				// Get update link for out-of-date product
				$ulink = $info->authorUrl;

				if (isset($this->data['update-url']))
				{
					$ulink = (string) $this->data['update-url'];
				}
				elseif ($const = JSNUtilsText::getConstant('UPDATE_LINK'))
				{
					$ulink = $const;
				}

				throw new Exception(
					JText::_('JSN_EXTFW_DATA_PRODUCT_VERSION_OUTDATE')
					. '&nbsp;<a href="' . $ulink . '" class="jsn-link-action">' . JText::_('JSN_EXTFW_GENERAL_UPDATE_NOW') . '</a>'
				);
			}
		}
		else
		{
			throw new Exception(JText::_('JSN_EXTFW_DATA_BACKUP_XML_NOT_FOUND'));
		}
	}

	/**
	 * Restore database table data from backup.
	 *
	 * @return  void
	 */
	protected function restoreTables()
	{
		// Get database object
		$db = JFactory::getDbo();

		foreach ($this->data->tables->table AS $table)
		{
			// Truncate current table data
			$query = $db->getQuery(true);

			$query->delete((string) $table['name']);
			$query->where('1');

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				throw $e;
			}

			// Get table columns
			$columns = array();

			foreach ($table->rows->row[0]->children() AS $column)
			{
				$columns[] = $column->getName();
			}

			// Restore database table data from backup
			$query = $db->getQuery(true);

			$query->insert((string) $table['name']);
			$query->columns(implode(', ', $columns));

			foreach ($table->rows->row AS $row)
			{
				$columns = array();

				foreach ($row->children() AS $column)
				{
					// Initialize column value
					$column = html_entity_decode((string) $column, ENT_QUOTES, 'UTF-8');
					$column = ! is_numeric($column) ? $db->quote($column) : $column;

					$columns[] = $column;
				}

				$query->values(implode(', ', $columns));
			}

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}

	/**
	 * Restore files from backup.
	 *
	 * @param   string  &$backup  Path to folder containing extracted backup files.
	 *
	 * @return  void
	 */
	protected function restoreFiles(&$backup)
	{
		// Initialize variables
		$config	= JFactory::getConfig();
		$tmp	= str_replace('\\', '/', $config->get('tmp_path'));
		$root	= str_replace('\\', '/', JPATH_ROOT);
		$backup	= str_replace('\\', '/', $backup);

		// Get list of backed up files
		if ($this->files = JFolder::files($backup, '.', true, true))
		{
			foreach ($this->files as $file)
			{
				// Initialize file path
				$file = str_replace('\\', '/', $file);

				if (basename($file) != $this->xml)
				{
					// Generate destination path
					if (strpos($file, $tmp) !== false)
					{
						$path = $root . '/' . trim(str_replace($backup, '', dirname($file)), '/');
					}
					elseif (strpos($file, $root) !== false)
					{
						$path = str_replace($backup, $root, dirname($file));
					}

					// Create folder if necessary
					is_dir($path) OR JFolder::create($path);

					// Copy file now
					JFile::copy($file, $path . '/' . basename($file));
				}
			}
		}
	}

	/**
	 * Do any extra work needed after doing real data restore.
	 *
	 * @param   array  &$backup  Uploaded backup file.
	 *
	 * @return  void
	 */
	protected function afterRestore(&$backup)
	{
		// Clean temporary files
		JFolder::delete($backup);
		JFile::delete("{$backup}.zip");
	}

	/**
	 * Install sample data.
	 *
	 * @param   integer  $step  Installation step.
	 *
	 * @return  void
	 */
	public function installSample($step)
	{
		// Check if method exists for current installation step
		$method = "installSampleStep{$step}";

		if (method_exists($this, $method))
		{
			try
			{
				$this->$method();
			}
			catch (Exception $e)
			{
				$msg = $e->getMessage();

				if ( ! preg_match('/[A-Z]+:\s*/', $msg))
				{
					$msg = 'FAIL: ' . $msg;
				}

				jexit($msg);
			}
		}
	}

	/**
	 * Install sample data (step 1).
	 *
	 * @return  boolean
	 */
	protected function installSampleStep1()
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Get URL to download sample data package
		$url = JFactory::getApplication()->input->getVar('sampleDownloadUrl');

		// Set maximum execution time
		ini_set('max_execution_time', 300);

		// Try to download the sample data package
		try
		{
			$path = $config->get('tmp_path') . '/' . basename($url);

			if ( ! JSNUtilsHttp::get($url, $path, true))
			{
				jexit('DOWNLOAD FAIL');
			}
		}
		catch (Exception $e)
		{
			jexit('DOWNLOAD FAIL');
		}

		// Complete AJAX based download task
		jexit('DONE: ' . trim(str_replace(JPATH_ROOT, '', $path), '/\\'));
	}

	/**
	 * Install sample data (step 2).
	 *
	 * @return  boolean
	 */
	protected function installSampleStep2()
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Get path to downloaded sample data package
		if (isset($_FILES['sampleDataPackage']))
		{
			if ( ! JFile::upload($_FILES['sampleDataPackage']['tmp_name'], $config->get('tmp_path') . '/' . $_FILES['sampleDataPackage']['name']))
			{
				throw new Exception(JText::_('JSN_EXTFW_GENERAL_MOVE_UPLOAD_FILE_FAIL'));
			}
			$path = $config->get('tmp_path') . '/' . $_FILES['sampleDataPackage']['name'];
		}
		else
		{
			$path = JFactory::getApplication()->input->getVar('sampleDownloadUrl');
		}

		// Try to install the sample data package
		try
		{
			$this->restore($path, false);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Complete AJAX based sample data installation task
		jexit('DONE');
	}
}
