<?php

/**
 * @version     $Id: data.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Models
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
 * Data model of JSN Framework Sample component
 *
 * @package     Models
 * @subpackage  Data
 * @since       1.6
 */
class JSNUniformModelData extends JSNDataModel
{

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

		$options['tables'] = array(
			'#__jsn_uniform_config',
			'#__jsn_uniform_data',
			'#__jsn_uniform_emails',
			'#__jsn_uniform_fields',
			'#__jsn_uniform_forms',
			'#__jsn_uniform_form_pages',
			'#__jsn_uniform_messages',
			'#__jsn_uniform_submissions',
			'#__jsn_uniform_submission_data',
			'#__jsn_uniform_templates'
		);
		$dataForm = $this->getForm();
		if (!empty($dataForm) && count($dataForm) && !JSNUniformHelper::checkTableSql("#__jsn_uniform_submission_data"))
		{
			foreach ($dataForm as $formId)
			{
				if (JSNUniformHelper::checkTableSql('#__jsn_uniform_submissions_' . (int) $formId->form_id))
				{
					$options['tables'][] = '#__jsn_uniform_submissions_' . (int) $formId->form_id;
				}
			}
		}
		if (!empty($options['files']))
		{
			$folderUpload = $this->getFolderUploadConfig();
			$folderUpload = !empty($folderUpload->value) ? $folderUpload->value : '/images/jsnuniform/';
			$folderUrl = $folderUpload . '/jsnuniform_uploads/';
			if (JFolder::exists(JPath::clean(JPATH_ROOT . "/" . $folderUrl)))
			{
				$options['files'] = array($folderUrl => '.');
			}
			else
			{
				$options['files'] = null;
			}
		}
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

			if (JSNUniformHelper::checkTableSql((string) $table))
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
				} catch (Exception $e)
				{
					// Do nothing
				}
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
				$value = str_replace("&nbsp;", " ", $value);
				$rowNode->addChild($name, htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false));
			}
		}
	}

	/**
	 * get data folder upload in data default config
	 *
	 * @return Object
	 */
	public function getFolderUploadConfig()
	{
		$this->_db->setQuery(
			$this->_db->getQuery(true)
				->select('*')
				->from('#__jsn_uniform_config')
				->where('name="folder_upload"')
		);
		return $this->_db->loadObject();
	}

	/**
	 * Get all id form
	 *
	 * @return Object
	 */
	public function getForm()
	{
		$this->_db->setQuery(
			$this->_db->getQuery(true)
				->select('form_id')
				->from('#__jsn_uniform_forms')
		);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all fields table in form
	 *
	 * @param   type  $formId  Form id
	 *
	 * @return type
	 */
	public function getFields($formId)
	{
		$this->_db->setQuery(
			$this->_db->getQuery(true)
				->select('field_id,field_type')
				->from('#__jsn_uniform_fields')
				->where('form_id = ' . (int) $formId)
		);
		return $this->_db->loadObjectList();
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
		$session = JFactory::getSession();
		$sessionQueue = $session->get('registry');
		$sessionQueue->set('com_jsnuniform', null);
		$dataForm = $this->getForm();
		$checkTableSubmissions = true;
		foreach ($this->data->tables->table as $table)
		{
			if ((string) $table['name'] == "#__jsn_uniform_submissions")
			{
				$checkTableSubmissions = false;
			}
		}
		$checkTableSubmissionData = true;
		foreach ($this->data->tables->table as $table)
		{
			if ((string) $table['name'] == "#__jsn_uniform_submission_data")
			{
				$checkTableSubmissionData = false;
			}
		}

		if (!empty($dataForm) && count($dataForm) && $checkTableSubmissionData && $checkTableSubmissionData)
		{
			foreach ($dataForm as $formId)
			{
				if (!empty($formId->form_id) && (int) $formId->form_id)
				{
					$fieldSubmission = array();
					$fieldSubmission[] = "`data_id` int(11)";
					$getFields = $this->getFields($formId->form_id);
					if (!empty($getFields) && is_array($getFields) && count($getFields))
					{
						foreach ($getFields as $field)
						{
							if (!empty($field->field_id) && !empty($field->field_type))
							{
								if ($field->field_type != 'google-maps' && $field->field_type != 'static-heading' && $field->field_type != 'static-paragraph' && $field->field_type != 'horizontal-ruler' && $field->field_type != 'static-content')
								{
									$fieldSubmission[] = '`sb_' . $field->field_id . '` ' . JSNUniformHelper::replaceField($field->field_type);
								}
							}
						}
						$fieldSubmission = implode(",", $fieldSubmission);
						$this->_db->setQuery("DROP TABLE IF EXISTS #__jsn_uniform_submissions_{$formId->form_id}");
						$this->_db->execute();
						$this->_db->setQuery("CREATE TABLE IF NOT EXISTS #__jsn_uniform_submissions_{$formId->form_id} ({$fieldSubmission})");
						$this->_db->execute();
					}
				}
			}
		}

		$folderUpload = $this->getFolderUploadConfig();

		if (!empty($folderUpload))
		{
			$folderUrl = $folderUpload->value . '/jsnuniform_uploads/';

			if (JFolder::exists(JPath::clean(JPATH_ROOT . $folderUrl)))
			{
				/*
				if (!JFile::exists(JPATH_ROOT . $folderUrl . '/.htaccess'))
				{
					$file = JPath::clean(JPATH_ROOT . $folderUrl . '/.htaccess');
					$buffer = "RemoveHandler .php .phtml .php3 \nRemoveType .php .phtml .php3 \nphp_flag engine off \n ";
					JFile::write($file, $buffer, true);
				}
				*/
			}
		}
		$this->restoreTables();
		$query = $this->_db->getQuery(true);
		$query->update($this->_db->quoteName("#__jsn_uniform_fields"));
		$query->set("field_type = " . $this->_db->Quote("static-content"));
		$query->where("field_type='static-heading'", "OR");
		$query->where("field_type='static-paragraph'", "OR");
		$query->where("field_type='horizontal-ruler'", "OR");
		$this->_db->setQuery($query);
		$this->_db->execute();

		$this->_db->setQuery(
			$this->_db->getQuery(true)
				->select('*')
				->from("#__jsn_uniform_form_pages")
		);
		if ($data = $this->_db->loadObjectList())
		{
			foreach ($data as $item)
			{
				$newContent = array();
				$newTemplate = new stdClass();
				$templateItem = array();
				if (isset($item->page_content))
				{
					$pageContent = json_decode($item->page_content);
					if ($pageContent && (is_array($pageContent) || is_object($pageContent)))
					{
						foreach ($pageContent as $content)
						{
							if ($content->type == 'static-heading')
							{
								$content->type = 'static-content';
								$typeHeading = isset($content->options->type) ? $content->options->type : '';
								$labelHeading = isset($content->options->label) ? $content->options->label : '';
								$content->options->value = "<{$typeHeading}>{$labelHeading}</{$typeHeading}>";
							}
							else if ($content->type == 'static-paragraph')
							{
								$content->type = 'static-content';
							}
							else if ($content->type == 'horizontal-ruler')
							{
								$content->type = 'static-content';
								$sizeHr = isset($content->options->size) ? $content->options->size : '';
								$content->options->value = "<hr class=\"{$sizeHr}\"/>";
							}
							$newContent[] = $content;
						}
					}
				}
				$query = $this->_db->getQuery(true);
				$query->update($this->_db->quoteName("#__jsn_uniform_form_pages"));
				$query->set("page_content = " . $this->_db->Quote(json_encode($newContent)));
				$query->where("page_id = " . intval($item->page_id));
				$this->_db->setQuery($query);
				$this->_db->execute();
			}
		}

		if ($checkTableSubmissions)
		{
			JSNUniformHelper::convertTableSubmissions('restore');
		}

		if ($checkTableSubmissionData)
		{
			JSNUniformHelper::convertTableSubmissionData('restore');
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
			if ((string) $table['name'] == "#__jsn_uniform_data")
			{
				$this->_db->setQuery("DROP TABLE IF EXISTS #__jsn_uniform_data");
				$this->_db->execute();
				$this->_db->setQuery("CREATE TABLE IF NOT EXISTS `#__jsn_uniform_data` (
								  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								  `form_id` int(10) unsigned NOT NULL,
								  `user_id` int(10) unsigned DEFAULT NULL,
								  `data_ip` varchar(40) NOT NULL,
								  `data_country` varchar(45) NOT NULL,
								  `data_country_code` varchar(4) NOT NULL,
								  `data_browser` varchar(45) NOT NULL,
								  `data_browser_version` varchar(20) NOT NULL,
								  `data_browser_agent` varchar(255) NOT NULL,
								  `data_os` varchar(45) NOT NULL,
								  `data_created_by` int(10) unsigned NOT NULL COMMENT '0 = Guest',
								  `data_created_at` datetime NOT NULL,
								  `data_state` tinyint(1) unsigned NOT NULL COMMENT '-1 = Trashed; 0 = Unpublish; 1 = Published',
								  PRIMARY KEY (`data_id`)
								)"
				);
				$this->_db->execute();
			}
			if (JSNUniformHelper::checkTableSql((string) $table['name']))
			{

				$query = $db->getQuery(true);
				$query->delete((string) $table['name']);
				$query->where('1');

				$db->setQuery($query);

				try
				{
					$db->execute();
				} catch (Exception $e)
				{
					throw $e;
				}
				// Get table columns
				$columns = array();

				foreach ($table->rows->row[0]->children() AS $column)
				{
					if (!in_array($column->getName(), array('form_btn_next_text', 'form_btn_prev_text', 'form_btn_submit_text')))
					{
						$columns[] = $column->getName();
					}

				}
				// Restore database table data from backup
				$query = $db->getQuery(true);

				$query->insert((string) $table['name']);
				$query->columns(implode(', ', $columns));

				foreach ($table->rows->row AS $row)
				{

					$columns = array();

					foreach ($row->children() AS $key => $column)
					{
						if (!in_array($key, array('form_btn_next_text', 'form_btn_prev_text', 'form_btn_submit_text')))
						{
							// Initialize column value
							$column = html_entity_decode((string) $column, ENT_QUOTES, 'UTF-8');
							$column = !is_numeric($column) ? $db->quote($column) : $column;
							$columns[] = $column;
						}
					}

					$query->values(implode(', ', $columns));
				}

				$db->setQuery($query);

				try
				{

					$db->execute();
				} catch (Exception $e)
				{

					throw $e;
				}
			}
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
		$com = preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option'));
		$info = JSNUtilsXml::loadManifestCache();
		$jVer = new JVersion;

		// Extract backup file
		if (!JArchive::extract($backup, substr($backup, 0, -4)))
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
			if (!$jVer->isCompatible((string) $this->data['joomla-version']))
			{
				throw new Exception(JText::_('JSN_EXTFW_DATA_JOOMLA_VERSION_NOT_MATCH'));
			}

			// Check if extension match
			if ((string) $this->data['extension-name'] != 'JSN ' . preg_replace('/JSN\s*/i', '', JText::_($info->name)))
			{
				throw new Exception(JText::_('JSN_EXTFW_DATA_INVALID_PRODUCT'));
			}
			elseif (isset($this->data['extension-edition']) AND $checkEdition
				AND (!($const = JSNUtilsText::getConstant('EDITION')) OR (string) $this->data['extension-edition'] != $const)
			)
			{
				throw new Exception(JText::_('JSN_EXTFW_DATA_INVALID_PRODUCT_EDITION'));
			}
			elseif (!version_compare($info->version, (string) $this->data['extension-version'], '=') AND !$checkEdition)
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
			elseif (!version_compare($info->version, (string) $this->data['extension-version'], 'ge'))
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
		$dataForm = $this->getForm();
		if (!empty($dataForm) && count($dataForm))
		{
			foreach ($dataForm as $formId)
			{
				if (!empty($formId->form_id) && (int) $formId->form_id)
				{
					$this->_db->setQuery("DROP TABLE IF EXISTS #__jsn_uniform_submissions_{$formId->form_id}");
					$this->_db->execute();
				}
			}
		}

		if (JSNUniformHelper::checkTableSql('#__jsn_uniform_submissions') === false)
		{
			JSNUniformHelper::createTableIfNotExistsSubmissions();
		}
		if (JSNUniformHelper::checkTableSql('#__jsn_uniform_submission_data') === false)
		{
			JSNUniformHelper::createTableIfNotExistsSubmissionData();
		}
	}

}
