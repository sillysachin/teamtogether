<?php

/**
 * @version     $Id: uniform.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Helper
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
defined('_JEXEC') or die('Restricted access');

/**
 * JSNUniform form helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 */
class JSNUniformHelper
{
	/**
	 * Load assets.
	 *
	 * @return  void
	 */
	public static function addAssets($checkVersion = true)
	{
		// Load common assets
		$stylesheets = array();
		$stylesheets[] = JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css';

		$stylesheets[] = JURI::root(true) . '/plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css';
		//$stylesheets[] = JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/font-awesome/css/font-awesome.css';
		// Load proprietary assets
		// Load scripts

		if ($checkVersion && JSNVersion::isJoomlaCompatible('3.2'))
		{
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/jui/js/jquery.min.js');
		//	$document = JFactory::getDocument();
			//$document->addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
		//	JSNHtmlAsset::addScriptLibrary('jquery.ui', JSN_UNIFORM_ASSETS_URI . "/js/libs/jquery-ui-1.10.3.custom.min", array('jquery'));
		}
		$stylesheets[] = JURI::base(true) . '/components/com_uniform/assets/css/uniform.css';
		JSNHtmlAsset::addStyle($stylesheets);
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	public static function addSubmenu($vName = 'forms')
	{
		if (JFactory::getApplication()->input->getCmd('tmpl', null) == null)
		{
			// Get 5 most-recent items
			$items = self::getForms(5);

			// Declare 1st-level menu items
			JSNMenuHelper::addEntry('forms', 'JSN_UNIFORM_SUBMENU_FORMS', 'index.php?option=com_uniform', $vName == '' OR $vName == 'forms', 'administrator/components/com_uniform/assets/images/icons-16/icon-forms.png', 'sub-menu');
			JSNMenuHelper::addEntry('submissions', 'JSN_UNIFORM_SUBMENU_SUBMISSION', 'index.php?option=com_uniform&view=submissions', $vName == '' OR $vName == 'submissions', 'administrator/components/com_uniform/assets/images/icons-16/icon-submissions.png', 'sub-menu');

			JSNMenuHelper::addEntry('configuration', 'JSN_UNIFORM_SUBMENU_CONFIGURATION', '', $vName == '' OR $vName == 'configuration', 'administrator/components/com_uniform/assets/images/icons-16/icon-cog.png', 'sub-menu');

			JSNMenuHelper::addEntry('about', 'JSN_UNIFORM_SUBMENU_ABOUT', 'index.php?option=com_uniform&view=about', $vName == 'about', 'administrator/components/com_uniform/assets/images/icons-16/icon-about.png', 'sub-menu');

			// Declare 2nd-level menu items	for 'items' entry
			JSNMenuHelper::addEntry('all-forms', 'All Forms', 'index.php?option=com_uniform&view=forms', false, '', 'sub-menu.forms');

			if ($items)
			{
				JSNMenuHelper::addEntry('recent-forms', 'Recent Forms', '', false, '', 'sub-menu.forms');

				foreach ($items AS $item)
				{
					JSNMenuHelper::addEntry('item-' . $item->form_id, $item->form_title, 'index.php?option=com_uniform&view=form&task=form.edit&layout=edit&form_id=' . $item->form_id, false, '', 'sub-menu.forms.recent-forms');
				}
			}
			JSNMenuHelper::addSeparator('sub-menu.forms');
			JSNMenuHelper::addEntry('item-new', 'Create New Form', 'index.php?option=com_uniform&view=form&layout=edit', false, '', 'sub-menu.forms');

			// Declare 2nd-level menu items	for 'configuration' entry
			JSNMenuHelper::addEntry('global-params', 'Global Parameters', 'index.php?option=com_uniform&view=configuration&s=configuration&g=configs', false, '', 'sub-menu.configuration');

			JSNMenuHelper::addEntry('messages', 'Messages', 'index.php?option=com_uniform&view=configuration&s=configuration&g=msgs', false, '', 'sub-menu.configuration');

			JSNMenuHelper::addEntry('languages', 'Languages', 'index.php?option=com_uniform&view=configuration&s=configuration&g=langs', false, '', 'sub-menu.configuration');

			JSNMenuHelper::addEntry('update', 'Product Update', 'index.php?option=com_uniform&view=configuration&s=configuration&g=update', false, '', 'sub-menu.configuration');

			JSNMenuHelper::addEntry('maintenance', 'Maintenance', '', false, '', 'sub-menu.configuration');

			// Declare 3rd-level menu items	for 'maintenance' entry
			JSNMenuHelper::addEntry('data', 'Data', 'index.php?option=com_uniform&view=configuration&s=maintenance&g=data', false, '', 'sub-menu.configuration.maintenance');

			JSNMenuHelper::addEntry('permissions', 'Permissions', 'index.php?option=com_uniform&view=configuration&s=maintenance&g=permissions', false, '', 'sub-menu.configuration.maintenance');

			// Render the sub-menu
			JSNMenuHelper::render('sub-menu');
		}
	}

	/**
	 * Set toolbar title and do some initialization
	 *
	 * @param   string   $title  Title to set for toolbar.
	 * @param   string   $icon   Custom icon for the title.
	 * @param   boolean  $help   Whether to show help button or not?
	 *
	 * @return  void
	 */
	public static function initToolbar($title, $icon = '', $help = true)
	{
		// Set toolbar title
		JToolBarHelper::title(JText::_($title), $icon);
		// Setup custom menu button
		self::addToolbarMenu();
		// Show help button?
		if ($help)
		{
			$bar = JToolBar::getInstance('toolbar');

			$bar->appendButton('Custom', '<button class="btn btn-small" id="jsn-help" onclick="return false;"><i class="icon-question-sign"></i>' . JText::_('JSN_UNIFORM_HELP') . '</button>');

		}
	}

	/**
	 * Add toolbar button.
	 *
	 * @return        void
	 */
	public static function addToolbarMenu()
	{
		// Get 5 most-recent items
		$items = self::getForms(5);

		// Create a toolbar button that drop-down a sub-menu when clicked
		JSNMenuHelper::addEntry('toolbar-menu', 'Menu', '', false, 'jsn-icon16 jsn-icon-menu', 'toolbar');


		// Declare 1st-level menu items
		JSNMenuHelper::addEntry('forms', 'JSN_UNIFORM_SUBMENU_FORMS', 'index.php?option=com_uniform', false, 'administrator/components/com_uniform/assets/images/icons-16/icon-forms.png', 'toolbar-menu');
		JSNMenuHelper::addEntry('submissions', 'JSN_UNIFORM_SUBMENU_SUBMISSION', 'index.php?option=com_uniform&view=submissions', false, 'administrator/components/com_uniform/assets/images/icons-16/icon-submissions.png', 'toolbar-menu');

		JSNMenuHelper::addEntry('configuration', 'JSN_UNIFORM_SUBMENU_CONFIGURATION', 'index.php?option=com_uniform&view=configuration', false, 'administrator/components/com_uniform/assets/images/icons-16/icon-cog.png', 'toolbar-menu');

		JSNMenuHelper::addEntry('about', 'JSN_UNIFORM_SUBMENU_ABOUT', 'index.php?option=com_uniform&view=about', false, 'administrator/components/com_uniform/assets/images/icons-16/icon-about.png', 'toolbar-menu');

		// Declare 2nd-level menu items	for 'items' entry
		JSNMenuHelper::addEntry('all-forms', 'All Forms', 'index.php?option=com_uniform&view=forms', false, '', 'toolbar-menu.forms');

		if ($items)
		{
			JSNMenuHelper::addEntry('recent-forms', 'Recent forms', '', false, '', 'toolbar-menu.forms');

			foreach ($items AS $item)
			{
				JSNMenuHelper::addEntry('item-' . $item->form_id, $item->form_title, 'index.php?option=com_uniform&view=form&task=form.edit&layout=edit&form_id=' . $item->form_id, false, '', 'toolbar-menu.forms.recent-forms');
			}
		}
		JSNMenuHelper::addSeparator('toolbar-menu.forms');
		JSNMenuHelper::addEntry('item-new', 'Create New Form', 'index.php?option=com_uniform&view=form&layout=edit', false, '', 'toolbar-menu.forms');

	}

	/**
	 * Setup menu add new form button.
	 *
	 * @return  void
	 */
	public static function buttonAddNewForm()
	{
		// Create a toolbar button that drop-down a sub-menu when clicked
		JSNMenuHelper::addEntry('toolbar-new-form', JText::_('JTOOLBAR_NEW'), '', false, 'jsn-icon16 jsn-icon-plus', 'toolbar');

		// Declare 1st-level menu items
		JSNMenuHelper::addEntry('blank-form', 'JSN_UNIFORM_BLANK_FORM', 'index.php?option=com_uniform&view=form&layout=edit', false, '', 'toolbar-new-form');
		JSNMenuHelper::addSeparator('toolbar-new-form');
		JSNMenuHelper::addEntry('contact-form', 'JSN_UNIFORM_CONTACT_US_FORM', 'index.php?option=com_uniform&view=form&layout=edit&form=Contact Us', false, '', 'toolbar-new-form');
		JSNMenuHelper::addEntry('feedback-form', 'JSN_UNIFORM_CUSTOMER_FEEDBACK_FORM', 'index.php?option=com_uniform&view=form&layout=edit&form=Customer Feedback', false, '', 'toolbar-new-form');
		JSNMenuHelper::addEntry('application-form', 'JSN_UNIFORM_JOB_APPLICATION_FORM', 'index.php?option=com_uniform&view=form&layout=edit&form=Job Application', false, '', 'toolbar-new-form');
		JSNMenuHelper::addEntry('register-form', 'JSN_UNIFORM_EVENT_REGISTRATION', 'index.php?option=com_uniform&view=form&layout=edit&form=Event Registration', false, '', 'toolbar-new-form');
		JSNMenuHelper::addEntry('voting-form', 'JSN_UNIFORM_VOTING_FORM', 'index.php?option=com_uniform&view=form&layout=edit&form=Voting Form', false, '', 'toolbar-new-form');
		JSNMenuHelper::addEntry('Survey-form', 'Survey Product/Service Satisfaction', 'index.php?option=com_uniform&view=form&layout=edit&form=Survey Product/Service Satisfaction', false, '', 'toolbar-new-form');

	}

	/**
	 * Genrate published options
	 *
	 * @return array
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = JHtml::_('select.option', '1', 'JSN_UNIFORM_PUBLISHED');
		$options[] = JHtml::_('select.option', '0', 'JSN_UNIFORM_UNPUBLISHED');

		return $options;
	}

	/**
	 * Word limiter
	 *
	 * @param   string   $str       String input
	 *
	 * @param   integer  $limit     Limit number
	 *
	 * @param   string   $end_char  End char
	 *
	 * @return string
	 */
	public static function wordLimiter($str, $limit = 100, $end_char = '&#8230;')
	{
		if (trim($str) == '')
		{
			return $str;
		}

		preg_match('/\s*(?:\S*\s*){' . (int) $limit . '}/', $str, $matches);

		if (strlen($matches[0]) == strlen($str))
		{
			$end_char = '';
		}

		return rtrim($matches[0]) . $end_char;
	}

	/**
	 * Get module info
	 *
	 * @return type
	 */
	public static function getModuleInfo()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('element=\'mod_uniform\' AND type=\'module\'');
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
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
	 * Get options Menus
	 *
	 * @return object list
	 */
	public static function getOptionMenus()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('menutype As value, title As text');
		$query->from('#__menu_types');
		$query->order('title');
		$db->setQuery($query);
		$menus = $db->loadObjectList();
		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		return $menus;
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
	 * Get user name by id
	 *
	 * @return type
	 */
	public static function getUserNameById()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id,username');
		$query->from('#__users');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$forms = array();
		if (count($items))
		{
			foreach ($items as $item)
			{
				$forms[$item->id] = $item->username;
			}
		}
		return $forms;
	}

	/**
	 * Get options forms
	 *
	 * @return object list
	 */
	public static function getOptionForms()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('form_id As value, form_title As text');
		$query->from('#__jsn_uniform_forms');
		$query->where('form_state = 1');
		$query->order('form_id DESC');
		$db->setQuery($query);
		$forms = $db->loadObjectList();
		$listForm = array();
		foreach ($forms as $form)
		{
			$query = $db->getQuery(true);
			$query->select(count("field_id"))->from('#__jsn_uniform_fields')->where('form_id=' . (int) $form->value);
			$db->setQuery($query);
			if ($db->loadResult())
			{
				$listForm[] = $form;
			}
		}
		return $listForm;
	}

	/**
	 * Get postion fields
	 *
	 * @param   int  $idForm  Id Form
	 *
	 * @return object
	 */
	public static function getDataSumbissionByField($fieldId, $formId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(count("data_id"))->from("#__jsn_uniform_submission_data")->where('form_id=' . (int) $formId . ' AND field_id = ' . (int) $fieldId);
		$db->setQuery($query);
		return $db->loadResult();

	}

	/**
	 * Replace type field
	 *
	 * @param   string  $type  Type field
	 *
	 * @return string
	 */
	public static function replaceField($type)
	{
		$type = str_replace(array("password", "email", "single-line-text", "currency", "phone", "website", "dropdown", "choices", "date", "country", "number", "likert"), "varchar(255)", $type);
		$type = str_replace(array("address", "checkboxes", "name", "file-upload", "paragraph-text", "list"), "longtext", $type);
		return $type;
	}

	/**
	 * Retrieve form data for use in page list submission
	 *
	 * @return Object List
	 */
	public static function getFormData()
	{
		$db = JFactory::getDBO();
		$db->setQuery("SHOW COLUMNS FROM #__jsn_uniform_submissions");
		return $db->loadObjectList();
	}

	/**
	 * CREATE TABLE IF NOT EXISTS `#__jsn_uniform_submission_data`
	 */
	public static function createTableIfNotExistsSubmissionData()
	{
		$db = JFactory::getDBO();
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__jsn_uniform_submission_data` (
					  `submission_data_id` int(11) NOT NULL AUTO_INCREMENT,
					  `submission_id` int(11) NOT NULL,
					  `form_id` int(11) NOT NULL,
					  `field_id` int(11) NOT NULL,
					  `field_type` varchar(45) NOT NULL,
					  `submission_data_value` longtext NOT NULL,
					  PRIMARY KEY (`submission_data_id`),
					  KEY `submission_data_id` (`submission_data_id`),
					  KEY `submission_id` (`submission_id`),
					  KEY `form_id` (`form_id`),
					  KEY `field_id` (`field_id`)
					) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
		);
		$db->execute();
	}

	/**
	 * CREATE TABLE IF NOT EXISTS `#__jsn_uniform_submissions`
	 */
	public static function createTableIfNotExistsSubmissions()
	{
		$db = JFactory::getDBO();
		$db->setQuery("CREATE  TABLE IF NOT EXISTS `#__jsn_uniform_submissions` (
					  `submission_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
					  `form_id` INT UNSIGNED NOT NULL ,
					  `user_id` INT UNSIGNED NULL ,
					  `submission_ip` VARCHAR(40) NOT NULL ,
					  `submission_country` VARCHAR(45) NOT NULL ,
					  `submission_country_code` VARCHAR(4) NOT NULL ,
					  `submission_browser` VARCHAR(45) NOT NULL ,
					  `submission_browser_version` VARCHAR(20) NOT NULL ,
					  `submission_browser_agent` VARCHAR(255) NOT NULL ,
					  `submission_os` VARCHAR(45) NOT NULL ,
					  `submission_created_by` INT UNSIGNED NOT NULL COMMENT '0 = Guest' ,
					  `submission_created_at` DATETIME NOT NULL ,
					  `submission_state` TINYINT(1) UNSIGNED NOT NULL COMMENT '-1 = Trashed; 0 = Unpublish; 1 = Published' ,
					  PRIMARY KEY (`submission_id`) ) DEFAULT CHARSET=utf8;"
		);
		$db->execute();
	}

	/**
	 * Convert data Submissions
	 *
	 * @return boolen
	 */
	public static function convertTableSubmissions($action = null)
	{
		$db = JFactory::getDBO();
		$checkConvert = false;

		if (self::checkTableSql('#__jsn_uniform_submissions') == false)
		{
			self::createTableIfNotExistsSubmissions();
			$checkConvert = true;
		}
		else if ($action == "restore")
		{
			$checkConvert = true;
		}
		if ($checkConvert && JSNUniformHelper::checkTableSql('#__jsn_uniform_data'))
		{
			$query = $db->getQuery(true);
			$query->delete('#__jsn_uniform_submissions');
			$db->setQuery($query);
			$db->execute();
			$db->getQuery(true);
			$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_data'));
			$dataForms = $db->loadObjectList();
			if (!empty($dataForms))
			{
				foreach ($dataForms as $data)
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->insert('#__jsn_uniform_submissions');
					$query->columns(array($db->quoteName('submission_id'), $db->quoteName('form_id'),
							$db->quoteName('user_id'), $db->quoteName('submission_ip'), $db->quoteName('submission_country'),
							$db->quoteName('submission_country_code'), $db->quoteName('submission_browser'), $db->quoteName('submission_browser_version'),
							$db->quoteName('submission_browser_agent'), $db->quoteName('submission_os'), $db->quoteName('submission_created_by'),
							$db->quoteName('submission_created_at'), $db->quoteName('submission_state'))
					);
					$query->values(
						$db->quote($data->data_id) . ', ' . $db->quote($data->form_id) . ', ' . $db->quote($data->user_id)
							. ', ' . $db->quote($data->data_ip) . ', ' . $db->quote($data->data_country)
							. ', ' . $db->quote($data->data_country_code) . ', ' . $db->quote($data->data_browser)
							. ', ' . $db->quote($data->data_browser_version) . ', ' . $db->quote($data->data_browser_agent)
							. ', ' . $db->quote($data->data_os) . ', ' . $db->quote($data->data_created_by)
							. ', ' . $db->quote($data->data_created_at) . ', ' . $db->quote($data->data_state)
					);
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	/**
	 * Convert data Submissions
	 *
	 * @return boolen
	 */
	public static function convertTableSubmissionData($action = null)
	{
		$db = JFactory::getDBO();
		$checkConvert = false;
		if (self::checkTableSql('#__jsn_uniform_submission_data') === false)
		{
			self::createTableIfNotExistsSubmissionData();
			$checkConvert = true;
		}
		else if ($action == "restore")
		{
			$checkConvert = true;
		}
		if ($checkConvert)
		{
			$query = $db->getQuery(true);
			$query->delete('#__jsn_uniform_submission_data');
			$db->setQuery($query);
			$db->execute();

			$db->getQuery(true);
			$db->setQuery($db->getQuery(true)->select('form_id')->from('#__jsn_uniform_forms'));
			$dataForms = $db->loadObjectList();
			$db->getQuery(true);
			$db->setQuery($db->getQuery(true)->select('field_id,field_type')->from("#__jsn_uniform_fields"));
			$fieldData = $db->loadObjectList();
			$fields = array();
			foreach ($fieldData as $field)
			{
				$fields[$field->field_id] = $field->field_type;
			}
			foreach ($dataForms as $item)
			{
				$tableName = "#__jsn_uniform_submissions_" . (int) $item->form_id;
				if (self::checkTableSql($tableName))
				{
					$db->getQuery(true);
					$db->setQuery($db->getQuery(true)->select('*')->from($tableName));
					$dataSubmissions = $db->loadObjectList();
					if (!empty($dataSubmissions))
					{
						foreach ($dataSubmissions as $data)
						{
							foreach ($data as $key => $value)
							{
								if ($key != "data_id")
								{
									$fieldId = (int) str_replace("sb_", "", $key);
									$tableSubmissionData = JTable::getInstance('Submissiondata', 'JSNUniformTable');
									$tableSubmissionData->bind(array('submission_id' => (int) $data->data_id, 'form_id' => (int) $item->form_id, 'field_id' => (int) $fieldId, 'field_type' => $fields[$fieldId], 'submission_data_value' => $value));
									$tableSubmissionData->store();
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Check table in database
	 *
	 * @param   string  $table  Name table
	 *
	 * @return boolen
	 */
	public static function checkTableSql($table)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$prefix = $db->getPrefix();
		$table = str_replace("#__", "", $table);
		$tableName = $prefix . $table;
		$app = JFactory::getApplication();
		$dbName = $app->getCfg('db');
		$query->select('TABLE_NAME')->from('INFORMATION_SCHEMA.TABLES')->where("TABLE_NAME LIKE " . $db->Quote($tableName) . " AND TABLE_SCHEMA = " . $db->quote($dbName));
		$db->setQuery($query);
		if (!$db->loadResult())
		{
			return false;
		}
		return true;
	}

	/**
	 * Set position field
	 *
	 * @param   int    $idForm  Id form
	 *
	 * @param   array  $data    Data field
	 *
	 * @return void
	 */
	public static function setPositionFields($idForm, $data)
	{
		$infoData = new stdClass;
		$infoData->identifier = $data['fields']['identifier'];
		$infoData->field_view = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($data['field_view']) : $data['field_view'];

		$db = JFactory::getDBO();
		$query = "REPLACE INTO `#__jsn_uniform_config` (name, value) VALUES ('position_form_" . (int) $idForm . "'," . $db->quote(json_encode($infoData)) . ")";
		$db->setQuery($query);
		if (!$db->execute())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}
	}

	/**
	 * Get postion fields
	 *
	 * @param   int  $idForm  Id Form
	 *
	 * @return object
	 */
	public static function getPositionFields($idForm)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('value')->from('#__jsn_uniform_config')->where('name = ' . '"position_form_' . (int) $idForm . '"');
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * get fields by form id
	 *
	 * @param   int  $idForm  Id Form
	 *
	 * @return mixed
	 */
	public static function getListFieldByForm($formId)
	{
		if (!empty($formId) && is_numeric($formId))
		{
			$db = JFactory::getDBO();
			$db->setQuery($db->getQuery(true)->select('*')->from('#__jsn_uniform_fields')->where('form_id=' . intval($formId))->where('field_type!="static-heading"')->where('field_type!="paragraph-text"')->order('field_id DESC'));
			return $db->loadObjectList();
		}
	}


	/**
	 * Get action form submission
	 *
	 * @param   int     $action      Action value
	 *
	 * @param   string  $actionData  Action data
	 *
	 * @return array
	 */
	public static function actionFrom($action = null, $actionData = null)
	{
		$redirectToUrl = "";
		$menuItem = "";
		$menuItemTitle = "";
		$article = "";
		$articleTitle = "";
		$message = "";
		if (isset($action))
		{
			switch($action)
			{
				case 1:
					$redirectToUrl = $actionData;
					break;
				case 2:
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->select('id,title')->from('#__menu')->where('id = ' . (int) $actionData);
					$db->setQuery($query);
					$dataMenu = $db->loadObject();
					if ($dataMenu)
					{
						$menuItem = $dataMenu->id;
						$menuItemTitle = $dataMenu->title;
					}
					break;
				case 3:
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->select('id,title')->from('#__content')->where('id = ' . (int) $actionData);
					$db->setQuery($query);
					$dataContent = $db->loadObject();
					if ($dataContent)
					{
						$article = $dataContent->id;
						$articleTitle = $dataContent->title;
					}
					break;
				case 4:
					$message = $actionData;
					break;
			}
		}
		else
		{
			$action = 0;
		}
		return array('redirect_to_url' => $redirectToUrl, 'menu_item' => $menuItem, 'menu_item_title' => $menuItemTitle, 'article' => $article, 'article_title' => $articleTitle, 'message' => $message, 'action' => $action);
	}

	/**
	 * get data folder upload in data default config
	 *
	 * @param   String  $name  Name field
	 *
	 * @return Object
	 */
	public static function getDataConfig($name)
	{
		$db = JFactory::getDBO();
		$db->setQuery($db->getQuery(true)->select('*')->from("#__jsn_uniform_config")->where("name=" . $db->Quote($name)));
		return $db->loadObject();
	}

	/**
	 * Get data field
	 *
	 * @param   string   $fieldType   Field type
	 *
	 * @param   object   $submission  Data submission
	 *
	 * @param   string   $key         Key field
	 *
	 * @param   int      $formId      Form id
	 *
	 * @param   boolean  $linkImg     Link Imgages
	 *
	 * @param   boolean  $checkNull   Check validate null
	 *
	 * @param   string   $action      Action data
	 *
	 * @return html code
	 */
	public static function getDataField($fieldType, $submission, $key, $formId, $linkImg = false, $checkNull = true, $action = '')
	{

		switch($fieldType)
		{
			case "datetime":
				$checkdate = @explode(" ", @$submission->$key);
				$checkdate = @explode("-", @$checkdate[0]);
				if (@$checkdate[0] != '0000' && @$checkdate[1] != '00' && @$checkdate[2] != '00')
				{
					$dateTime = new DateTime(@$submission->$key);
					$contentField = $dateTime->format("j F Y");
				}
				break;
			case "name":
				if (!empty($submission->$key))
				{
					$jsonName = json_decode($submission->$key);
					if ($jsonName)
					{
						$nameTitle = isset($jsonName->title) ? $jsonName->title . " " : '';
						$nameFirst = isset($jsonName->first) ? $jsonName->first . " " : '';
						$nameLast = isset($jsonName->last) ? $jsonName->last : '';
						$nameSuffix = isset($jsonName->suffix) ? $jsonName->suffix . " " : '';
						if (!empty($jsonName->first) || !empty($jsonName->last) || !empty($jsonName->suffix))
						{
							$contentField = $nameTitle . $nameFirst . $nameSuffix . $nameLast;
						}
						else
						{
							$contentField = '';
						}
					}
					else
					{
						$contentField = $submission->$key;
					}
				}

				break;
			case "file-upload":
				$jsonFile = json_decode(@$submission->$key);
				if (!empty($jsonFile))
				{
					if (!is_array($jsonFile) && is_object($jsonFile))
					{
						$tmpArray[] = $jsonFile;
						$jsonFile = $tmpArray;
					}
					$contentField = array();
					foreach ($jsonFile as $file)
					{
						$configFolderUpload = self::getDataConfig("folder_upload");
						$url = isset($configFolderUpload->value) ? $configFolderUpload->value : "images/jsnuniform/";
						$url = str_replace("//", "/", $url . '/jsnuniform_uploads/' . $formId . '/' . $file->link);
						$link = JURI::root() . $url;
						$fileName = explode(".", $file->name);
						if ($action == "export")
						{
							$contentField[] = isset($file->name) ? $link : "N/A";
						}
						elseif ($action == "email")
						{
							$contentField[] = isset($file->name) ? "<a href=\"{$link}\">{$file->name}</a>" : "N/A";
						}
						elseif ($action == 'fileAttach')
						{
							$contentField[] = isset($file->name) ? JPath::clean(JPATH_ROOT . "/" . $url) : "";
						}
						else
						{
							if (in_array(strtolower(array_pop($fileName)), array("jpg", "gif", "jpeg", "png")))
							{
								if ($linkImg)
								{
									$contentField[] = isset($file->name) ? "<img src=\"{$link}\" />" : "N/A";
								}
								else
								{
									$contentField[] = isset($file->name) ? "<a href=\"{$link}\" class=\"thumbnail\" target=\"_blank\"><img src=\"{$link}\" /></a>" : "N/A";
								}
							}
							else
							{
								if ($action == "list")
								{
									$contentField[] = isset($file->name) ? $file->name : "N/A";
								}
								else
								{
									$contentField[] = isset($file->name) ? "<a href=\"{$link}\">{$file->name}</a>" : "N/A";
								}
							}
						}
					}
					if (!empty($contentField))
					{
						if ($action != 'fileAttach')
						{
							$contentField = implode("\n", $contentField);
						}

					}
				}
				break;
			case "address":
				if (!empty($submission->$key))
				{
					$jsonAddress = json_decode($submission->$key);
					if ($jsonAddress)
					{
						$nameStreet = !empty($jsonAddress->street) ? $jsonAddress->street . ", " : '';
						$nameLine2 = !empty($jsonAddress->line2) ? $jsonAddress->line2 . ", " : '';
						$nameCity = !empty($jsonAddress->city) ? $jsonAddress->city . ", " : '';
						$nameCode = !empty($jsonAddress->code) ? $jsonAddress->code . " " : '';
						$nameState = !empty($jsonAddress->state) ? $jsonAddress->state . " " : '';
						$nameCountry = !empty($jsonAddress->country) ? $jsonAddress->country . " " : '';
						$contentField = $nameStreet . $nameLine2 . $nameCity . $nameState . $nameCode . $nameCountry;
					}
					else
					{
						$contentField = $submission->$key;
					}
				}
				else
				{
					$contentField = '';
				}

				break;
			case ($fieldType == "likert"):
				if (!empty($submission->$key))
				{
					$jsonName = json_decode($submission->$key);

					if (!empty($jsonName->settings))
					{
						$settings = json_decode($jsonName->settings);
						if (!empty($jsonName))
						{
							$contentField = array();
							foreach ($settings->rows as $set)
							{
								$likertHtml = '';
								$likertHtml .= "<strong>" . $set->text . ":</strong>";
								$value = 'N/A';
								foreach ($jsonName->values as $key => $val)
								{
									if ($key == $set->text)
									{
										$value = $val;
									}
								}
								$likertHtml .= $value;
								$contentField[] = $likertHtml;
							}
							$contentField = implode("<br/>", $contentField);
						}
					}
					else
					{
						$contentField = '';
					}
				}
				else
				{
					$contentField = '';
				}

				break;
			case ($fieldType == "checkboxes" || $fieldType == "list"):
				if (!empty($submission->$key))
				{
					$jsonName = json_decode($submission->$key);
					if ($action == "email")
					{
						if (!empty($jsonName) && is_array($jsonName))
						{
							$listCheckBox = '';
							foreach ($jsonName as $item)
							{
								$listCheckBox .= '<li style="padding:0;">' . $item . '</li>';
							}
							if ($listCheckBox)
							{
								$contentField = "<ul style=\"padding:0 0px 0px 10px;margin:0;\">{$listCheckBox}</ul>";
							}
							else
							{
								$contentField = '';
							}
						}
					}
					else
					{
						if (empty($jsonName))
						{
							$jsonName = explode("\n", $submission->$key);
							$contentField = implode("<br/>", $jsonName);
						}
						elseif (!empty($jsonName) && is_array($jsonName))
						{
							$contentField = implode("<br/>", $jsonName);
						}
						else
						{
							$contentField = $submission->$key;
						}
					}
				}
				else
				{
					$contentField = '';
				}

				break;
			default:
				if ($checkNull)
				{
					$contentField = isset($submission->$key) ? $submission->$key : "<span>N/A</span>";
				}
				else
				{
					$contentField = isset($submission->$key) ? $submission->$key : "";
				}
				break;
		}
		return isset($contentField) ? $contentField : "";
	}

	/**
	 * Get select Form
	 *
	 * @param   string    $name          Name form
	 *
	 * @param   int       $id            Id form
	 *
	 * @param   boolean   $btn           Button
	 *
	 * @param   string    $value         Value form
	 *
	 * @param   boolean   $checkVersion  Check version joomla
	 *
	 * @return  html code
	 */
	public static function getSelectForm($name, $id = null, $view = "", $value = null, $checkVersion = false)
	{
		$enabledCSS = 'hide';
		$menuid = JRequest::getInt('id');
		$formID = $value;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		//build the list of categories
		$query->select('uf.form_title AS text, uf.form_id AS id')->from('#__jsn_uniform_forms AS uf')->where('uf.form_state = 1');
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$results[] = JHTML::_('select.option', '0', '- ' . JText::_('JSN_UNIFORM_SELECT_FORM') . ' -', 'id', 'text');
		$results = array_merge($results, $data);
		JSNUniformHelper::addAssets($checkVersion);

		JSNHtmlAsset::addStyle(JURI::base(true) . '/components/com_uniform/assets/css/uniform.css');
		$arrayTranslated = array('JSN_UNIFORM_UPGRADE_EDITION_TITLE', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0', 'JSN_UNIFORM_UPGRADE_EDITION');
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "free";
		$baseUrl = JURI::base(true);
		JSNHtmlAsset::addScriptPath('uniform', $baseUrl . '/components/com_uniform/assets/js');
		JSNHtmlAsset::addScriptPath('uniform/3rd', $baseUrl . '/components/com_uniform/assets/3rd-party');

		if ($data)
		{
			$enabledCSS = '';
			if (!$menuid && !$formID)
			{
				$value = $data[0]->id;
			}
		}
		if ($view == "content")
		{
			$listForm = JHTML::_('select.genericList', $results, $name, 'title="' . ((!$data) ? JText::_('JSN_UNIFORM_DO_NOT_HAVE_ANY_FORM') : '') . '" class="jform_request_form_id"', 'id', 'text');
			$html = '<div class="jsn-uniform-plg-editor-container jsn-bootstrap">
						<div class="jsn-uniform-plg-editor-wrapper">
							<h3 class="jsn-section-header">' . JText::_('JSN_UNIFORM_MODULE_LIST_FORM_DES') . '</h3>
							<div class="setting">
								<ul>
									<li>
										<label style="float:left;">' . JText::_('JSN_UNIFORM_MODULE_LIST_FORM') . '</label>
										' . $listForm . '
											<a id="form-icon-edit"  action="article" href="javascript: void(0);" target="_blank" title="' . JText::_('JSN_UNIFORM_EDIT_SELECTED_FORM') . '"><span class="jsn-icon16 jsn-icon-pencil"></span></a>
											<a id="form-icon-add" action="article" href="javascript: void(0);" title="' . JText::_('JSN_UNIFORM_CREATE_NEW_FORM') . '"><span class="jsn-icon16 jsn-icon-plus"></span></a>
									</li>
								</ul>
							</div>
							<div class="insert">
								<div class="form-actions">
									<button disabled="disabled" id="select-forms" onclick="" name="button_installation_data" type="button" class="btn">' . JText::_('JSN_UNIFORM_BTN_SELECTED') . '</button>
								</div>
							</div>
						</div>
					</div>';
			$html .= JSNHtmlAsset::loadScript('uniform/menuform', array('edition' => $edition, 'language' => JSNUtilsLanguage::getTranslated($arrayTranslated)), true);
			return $html;
		}
		else if ($view == "menuform")
		{
			$listForm = JHTML::_('select.genericList', $results, $name, 'title="' . ((!$data) ? JText::_('JSN_UNIFORM_DO_NOT_HAVE_ANY_FORM') : '') . '" class="jform_request_form_id"', 'id', 'text', $value, $id);
			$html = "<div id='jsn-form-icon-warning'>";
			$html .= $listForm;
			$html .= "<span id =\"form-icon-warning\" class='{$enabledCSS}' title=\"" . JText::_('JSN_UNIFORM_FIELD_DES_FORM_WARNING') . "\"><span class=\"jsn-icon16 jsn-icon-warning-sign\"></span></span>";
			$html .= "<a id=\"form-icon-edit\" href=\"javascript: void(0);\" target=\"_blank\" title=\"" . JText::_('JSN_UNIFORM_EDIT_SELECTED_FORM') . "\"><span class=\"jsn-icon16 jsn-icon-pencil\"></span></a>";
			$html .= "<a id=\"form-icon-add\" href=\"javascript: void(0);\" title=\"" . JText::_('JSN_UNIFORM_CREATE_NEW_FORM') . "\"><span class=\"jsn-icon16 jsn-icon-plus\"></span></a>";
			$html .= "<span id='select-forms'></span>";
		}
		else if ($view == "menusubmissions")
		{
			$listForm = JHTML::_('select.genericList', $results, $name, 'title="' . ((!$data) ? JText::_('JSN_UNIFORM_DO_NOT_HAVE_ANY_FORM') : '') . '" class="jform_request_form_id"', 'id', 'text', $value);
			$html = "<div id='jsn-form-icon-warning'>";
			$html .= $listForm;
			$html .= "<span id =\"form-icon-warning\" class='{$enabledCSS}' title=\"" . JText::_('JSN_UNIFORM_FIELD_DES_FORM_WARNING') . "\"><span class=\"jsn-icon16 jsn-icon-warning-sign\"></span></span>";
			$html .= "</div>";
			//JSNHtmlAsset::loadScript('uniform/menusubmissions', array('language' => JSNUtilsLanguage::getTranslated($arrayTranslated)));
			return $html;
		}
		$html .= "</div><div id=\"jsn-modal\"></div>" . JSNHtmlAsset::loadScript('uniform/menuform', array('edition' => $edition, 'language' => JSNUtilsLanguage::getTranslated($arrayTranslated)), true);
		return $html;
	}

	/**
	 * Get list page form
	 *
	 * @param   string  $formContent  Form content
	 *
	 * @return  html code
	 */
	public static function getListPage($formContent, $formId = 0)
	{
		$session = JFactory::getSession();

		$listPage = $session->get('form_list_page', '', 'form-design-' . $formId);
		$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "free";
		$option = "";
		$defaultListpage = "";
		$dataValue = "";
		$pageContent = array();

		if (isset($formContent))
		{
			foreach ($formContent as $i => $fContent)
			{
				$session->set('form_page_' . $fContent->page_id, $fContent->page_content, 'form-design-' . $formId);
				if (!empty($fContent->page_container) && count(json_decode($fContent->page_container)))
				{
					$session->set('form_container_page_' . $fContent->page_id, $fContent->page_container, 'form-design-' . $formId);
				}
				else
				{
					$session->set('form_container_page_' . $fContent->page_id, '[[{"columnName":"left","columnClass":"span12"}]]', 'form-design-' . $formId);
				}
				if (count(json_decode($fContent->page_content)))
				{
					$pageContent[] = $fContent->page_id;
				}
				if ($i == 0)
				{
					$defaultListpage .= "<div data-value='{$fContent->page_id}' id=\"form-design-header\" class=\"jsn-section-header\"><div class=\"jsn-iconbar-trigger page-title\"><h1>{$fContent->page_title}</h1><div class=\"jsn-iconbar\"><a href=\"javascript:void(0)\" title=\"Edit page\" class=\"element-edit\"><i class=\"icon-pencil\"></i></a><a href=\"javascript:void(0)\" title=\"Delete page\" class=\"element-delete\"><i class=\"icon-trash\"></i></a></div></div><div class=\"jsn-page-actions jsn-buttonbar\"><div class=\"jsn-page-pagination pull-left btn-group\"><button onclick=\"return false;\" class=\"btn btn-icon prev-page\"><i class=\"icon-arrow-left\"></i></button><button onclick=\"return false;\" class=\"btn btn-icon next-page\"><i class=\"icon-arrow-right\"></i></button></div><button onclick=\"return false;\" class=\"btn btn-success new-page\">" . JText::_("JSN_UNIFORM_FORM_NEW_PAGE") . "</button></div><div class=\"clearbreak\"></div>";
					$dataValue = $fContent->page_id;
				}
				$option .= "<li id=\"{$fContent->page_id}\" data-value='{$fContent->page_id}' class=\"page-items\"><input type=\"hidden\" value=\"{$fContent->page_title}\" data-id=\"{$fContent->page_id}\" name=\"name_page[{$fContent->page_id}]\"/></li>";
			}
			if (count($pageContent))
			{
				$session->set('page_content', json_encode($pageContent), 'form-design-' . $formId);
			}
		}
		elseif ($listPage)
		{
			$listPages = json_decode($listPage);
			foreach ($listPages as $i => $page)
			{
				if ($i == 0)
				{
					$defaultListpage .= "<div data-value='{$page[0]}' id=\"form-design-header\" class=\"jsn-section-header\"><div class=\"jsn-iconbar-trigger page-title\"><h1>{$page[1]}</h1><div class=\"jsn-iconbar\"><a href=\"javascript:void(0)\" title=\"Edit page\" class=\"element-edit\"><i class=\"icon-pencil\" ></i></a><a href=\"javascript:void(0)\" title=\"Delete page\" class=\"element-delete\"><i class=\"icon-trash\" ></i></a></div></div><div class=\"jsn-page-actions jsn-buttonbar\"><div class=\"jsn-page-pagination pull-left btn-group\"><button onclick=\"return false;\" class=\"btn btn-icon prev-page\"><i class=\"icon-arrow-left\"></i></button><button onclick=\"return false;\" class=\"btn btn-icon next-page\"><i class=\"icon-arrow-right\"></i></button></div><button onclick=\"return false;\" class=\"btn btn-success new-page\">" . JText::_("JSN_UNIFORM_FORM_NEW_PAGE") . "</button></div><div class=\"clearbreak\"></div>";
					$dataValue = $page[0];
				}
				$option .= "<li id=\"{$page[0]}\" data-value='{$page[0]}' class=\"page-items\"><input type=\"hidden\" value=\"{$page[1]}\" data-id=\"{$page[0]}\" name=\"name_page[{$page[0]}]\"/></li>";
			}
		}
		else
		{
			$randomID = rand(1000000, 10000000000);
			$defaultListpage = "<div data-value='{$randomID}' id=\"form-design-header\" class=\"jsn-section-header\"><div class=\"jsn-iconbar-trigger page-title\"><h1>Page 1</h1><div class=\"jsn-iconbar\"><a href=\"javascript:void(0)\" title=\"Edit page\" class=\"element-edit\"><i class=\"icon-pencil\"></i></a><a href=\"javascript:void(0)\" title=\"Delete page\" class=\"element-delete\"><i class=\"icon-trash\" ></i></a></div></div><div class=\"jsn-page-actions jsn-buttonbar\"><div class=\"jsn-page-pagination pull-left btn-group\"><button onclick=\"return false;\" class=\"btn btn-icon prev-page\"><i class=\"icon-arrow-left\"></i></button><button onclick=\"return false;\" class=\"btn btn-icon next-page\"><i class=\"icon-arrow-right\"></i></button></div><button onclick=\"return false;\" class=\"btn btn-success new-page\">" . JText::_("JSN_UNIFORM_FORM_NEW_PAGE") . "</button></div><div class=\"clearbreak\"></div>";
			$dataValue = $randomID;
			$option = '<li id="new_' . $randomID . '" data-value="' . $randomID . '" class="page-items"><input type="hidden" value="Page 1" data-id="' . $randomID . '" name="name_page[' . $randomID . ']"/></li>';
			if (!empty($_GET['form']))
			{
				$getSampleForm = JSNUniformHelper::getSampleForm($_GET['form']);
				$session->set('form_page_' . $randomID, $getSampleForm, 'form-design-' . $formId);
				$sessionPage = JFactory::getSession();
				$sessionPage->set('form_list_page', json_encode(array($randomID, 'Page 1')), 'form-design-' . $formId);
				$session->set('form_container_page_' . $randomID, '[[{"columnName":"left","columnClass":"span12"}]]', 'form-design-' . $formId);
			}
		}
		if (strtolower($edition) == "free")
		{
			$defaultListpage = "<div class=\"hide\" id=\"form-design-header\" data-value=\"{$dataValue}\">";
		}
		$select = "{$defaultListpage} <ul class=\"jsn-page-list hide\">{$option}</ul></div>";
		return $select;
	}

	/**
	 * Get Sample Form
	 *
	 * @param   string  $formType  Form Type
	 *
	 * @return  html code
	 */
	public static function getSampleForm($formType)
	{
		$return = "";
		switch($formType)
		{
			case 'Contact Us':
				$return = '[{"type":"name","position":"left","identify":"name","label":"Name","instruction":"","options":{"label":"Name","instruction":"","required":"1","format":"Extended","items":[{"text":"Mrs","checked":false},{"text":"Mr","checked":true},{"text":"Ms","checked":false},{"text":"Baby","checked":false},{"text":"Master","checked":false},{"text":"Prof","checked":false},{"text":"Dr","checked":false},{"text":"Gen","checked":false},{"text":"Rep","checked":false},{"text":"Sen","checked":false},{"text":"St","checked":false}],"vtitle":"1","vfirst":"1","vmiddle":"1","vlast":"1","identify":"name","identify":"jsn_tmp_721799"}},{"type":"paragraph-text","position":"left","identify":"when_is_the_best_time_to_contact_you_","label":"When is the best time to contact you?","instruction":"","options":{"label":"When is the best time to contact you?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_477590"}},{"type":"paragraph-text","position":"left","identify":"what_is_the_best_way_to_contact_you_","label":"What is the best way to contact you?","instruction":"","options":{"label":"What is the best way to contact you?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_448875"}},{"type":"email","position":"left","identify":"email","label":"Email","instruction":"","options":{"label":"Email","instruction":"","required":"1","noDuplicates":0,"size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_181757"}},{"type":"address","position":"left","identify":"address","label":"Address","instruction":"","options":{"label":"Address","instruction":"","vstreetAddress":"1","vstreetAddress2":"1","vcity":"1","vcode":"1","vstate":"1","vcountry":"1","required":"1","country":[{"text":"Afghanistan","checked":true},{"text":"Albania","checked":false},{"text":"Algeria","checked":false},{"text":"Andorra","checked":false},{"text":"Angola","checked":false},{"text":"Antigua and Barbuda","checked":false},{"text":"Argentina","checked":false},{"text":"Armenia","checked":false},{"text":"Australia","checked":false},{"text":"Austria","checked":false},{"text":"Azerbaijan","checked":false},{"text":"Bahamas","checked":false},{"text":"Bahrain","checked":false},{"text":"Bangladesh","checked":false},{"text":"Barbados","checked":false},{"text":"Belarus","checked":false},{"text":"Belgium","checked":false},{"text":"Belize","checked":false},{"text":"Benin","checked":false},{"text":"Bhutan","checked":false},{"text":"Bolivia","checked":false},{"text":"Bosnia and Herzegovina","checked":false},{"text":"Botswana","checked":false},{"text":"Brazil","checked":false},{"text":"Brunei","checked":false},{"text":"Bulgaria","checked":false},{"text":"Burkina Faso","checked":false},{"text":"Burundi","checked":false},{"text":"Cambodia","checked":false},{"text":"Cameroon","checked":false},{"text":"Canada","checked":false},{"text":"Cape Verde","checked":false},{"text":"Central African Republic","checked":false},{"text":"Chad","checked":false},{"text":"Chile","checked":false},{"text":"China","checked":false},{"text":"Colombi","checked":false},{"text":"Comoros","checked":false},{"text":"Congo (Brazzaville)","checked":false},{"text":"Congo","checked":false},{"text":"Costa Rica","checked":false},{"text":"Cote d\'Ivoire","checked":false},{"text":"Croatia","checked":false},{"text":"Cuba","checked":false},{"text":"Cyprus","checked":false},{"text":"Czech Republic","checked":false},{"text":"Denmark","checked":false},{"text":"Djibouti","checked":false},{"text":"Dominica","checked":false},{"text":"Dominican Republic","checked":false},{"text":"East Timor (Timor Timur)","checked":false},{"text":"Ecuador","checked":false},{"text":"Egypt","checked":false},{"text":"El Salvador","checked":false},{"text":"Equatorial Guinea","checked":false},{"text":"Eritrea","checked":false},{"text":"Estonia","checked":false},{"text":"Ethiopia","checked":false},{"text":"Fiji","checked":false},{"text":"Finland","checked":false},{"text":"France","checked":false},{"text":"Gabon","checked":false},{"text":"Gambia, The","checked":false},{"text":"Georgia","checked":false},{"text":"Germany","checked":false},{"text":"Ghana","checked":false},{"text":"Greece","checked":false},{"text":"Grenada","checked":false},{"text":"Guatemala","checked":false},{"text":"Guinea","checked":false},{"text":"Guinea-Bissau","checked":false},{"text":"Guyana","checked":false},{"text":"Haiti","checked":false},{"text":"Honduras","checked":false},{"text":"Hungary","checked":false},{"text":"Iceland","checked":false},{"text":"India","checked":false},{"text":"Indonesia","checked":false},{"text":"Iran","checked":false},{"text":"Iraq","checked":false},{"text":"Ireland","checked":false},{"text":"Israel","checked":false},{"text":"Italy","checked":false},{"text":"Jamaica","checked":false},{"text":"Japan","checked":false},{"text":"Jordan","checked":false},{"text":"Kazakhstan","checked":false},{"text":"Kenya","checked":false},{"text":"Kiribati","checked":false},{"text":"Korea, North","checked":false},{"text":"Korea, South","checked":false},{"text":"Kuwait","checked":false},{"text":"Kyrgyzstan","checked":false},{"text":"Laos","checked":false},{"text":"Latvia","checked":false},{"text":"Lebanon","checked":false},{"text":"Lesotho","checked":false},{"text":"Liberia","checked":false},{"text":"Libya","checked":false},{"text":"Liechtenstein","checked":false},{"text":"Lithuania","checked":false},{"text":"Luxembourg","checked":false},{"text":"Macedonia","checked":false},{"text":"Madagascar","checked":false},{"text":"Malawi","checked":false},{"text":"Malaysia","checked":false},{"text":"Maldives","checked":false},{"text":"Mali","checked":false},{"text":"Malta","checked":false},{"text":"Marshall Islands","checked":false},{"text":"Mauritania","checked":false},{"text":"Mauritius","checked":false},{"text":"Mexico","checked":false},{"text":"Micronesia","checked":false},{"text":"Moldova","checked":false},{"text":"Monaco","checked":false},{"text":"Mongolia","checked":false},{"text":"Morocco","checked":false},{"text":"Mozambique","checked":false},{"text":"Myanmar","checked":false},{"text":"Namibia","checked":false},{"text":"Nauru","checked":false},{"text":"Nepa","checked":false},{"text":"Netherlands","checked":false},{"text":"New Zealand","checked":false},{"text":"Nicaragua","checked":false},{"text":"Niger","checked":false},{"text":"Nigeria","checked":false},{"text":"Norway","checked":false},{"text":"Oman","checked":false},{"text":"Pakistan","checked":false},{"text":"Palau","checked":false},{"text":"Panama","checked":false},{"text":"Papua New Guinea","checked":false},{"text":"Paraguay","checked":false},{"text":"Peru","checked":false},{"text":"Philippines","checked":false},{"text":"Poland","checked":false},{"text":"Portugal","checked":false},{"text":"Qatar","checked":false},{"text":"Romania","checked":false},{"text":"Russia","checked":false},{"text":"Rwanda","checked":false},{"text":"Saint Kitts and Nevis","checked":false},{"text":"Saint Lucia","checked":false},{"text":"Saint Vincent","checked":false},{"text":"Samoa","checked":false},{"text":"San Marino","checked":false},{"text":"Sao Tome and Principe","checked":false},{"text":"Saudi Arabia","checked":false},{"text":"Senegal","checked":false},{"text":"Serbia and Montenegro","checked":false},{"text":"Seychelles","checked":false},{"text":"Sierra Leone","checked":false},{"text":"Singapore","checked":false},{"text":"Slovakia","checked":false},{"text":"Slovenia","checked":false},{"text":"Solomon Islands","checked":false},{"text":"Somalia","checked":false},{"text":"South Africa","checked":false},{"text":"Spain","checked":false},{"text":"Sri Lanka","checked":false},{"text":"Sudan","checked":false},{"text":"Suriname","checked":false},{"text":"Swaziland","checked":false},{"text":"Sweden","checked":false},{"text":"Switzerland","checked":false},{"text":"Syria","checked":false},{"text":"Taiwan","checked":false},{"text":"Tajikistan","checked":false},{"text":"Tanzania","checked":false},{"text":"Thailand","checked":false},{"text":"Togo","checked":false},{"text":"Tonga","checked":false},{"text":"Trinidad and Tobago","checked":false},{"text":"Tunisia","checked":false},{"text":"Turkey","checked":false},{"text":"Turkmenistan","checked":false},{"text":"Tuvalu","checked":false},{"text":"Uganda","checked":false},{"text":"Ukraine","checked":false},{"text":"United Arab Emirates","checked":false},{"text":"United Kingdom","checked":false},{"text":"United States","checked":false},{"text":"Uruguay","checked":false},{"text":"Uzbekistan","checked":false},{"text":"Vanuatu","checked":false},{"text":"Vatican City","checked":false},{"text":"Venezuela","checked":false},{"text":"Vietnam","checked":false},{"text":"Yemen","checked":false},{"text":"Zambia","checked":false},{"text":"Zimbabwe","checked":false}],"identify":"jsn_tmp_746859"}},{"type":"website","position":"left","identify":"website","label":"Website","instruction":"","options":{"label":"Website","instruction":"","required":0,"noDuplicates":0,"size":"jsn-input-medium-fluid","value":"http:\/\/","identify":"jsn_tmp_269016"}},{"type":"single-line-text","position":"left","identify":"company","label":"Company","instruction":"","options":{"label":"Company","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"limitType":"Words","size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_383883"}},{"type":"single-line-text","position":"left","identify":"message_subject","label":"Message Subject","instruction":"","options":{"label":"Message Subject","instruction":"","required":"1","limitation":0,"limitMin":0,"limitMax":0,"limitType":"Words","size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_938869"}},{"type":"paragraph-text","position":"left","identify":"message","label":"Message","instruction":"","options":{"label":"Message","instruction":"","required":"1","limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_15779"}},{"type":"file-upload","position":"left","identify":"attached_file","label":"Attached File","instruction":"","options":{"label":"Attached File","instruction":"","required":0,"allowedExtensions":"png,jpg,gif,zip,rar,txt,doc,pdf","maxSize":0,"maxSizeUnit":"KB","identify":"jsn_tmp_851365"}}]';
				break;
			case 'Customer Feedback':
				$return = '[{"type":"name","position":"left","identify":"name","label":"Name","instruction":"","options":{"label":"Name","instruction":"","required":"1","format":"Normal","items":[{"text":"Mrs","checked":false},{"text":"Mr","checked":true},{"text":"Ms","checked":false},{"text":"Baby","checked":false},{"text":"Master","checked":false},{"text":"Prof","checked":false},{"text":"Dr","checked":false},{"text":"Gen","checked":false},{"text":"Rep","checked":false},{"text":"Sen","checked":false},{"text":"St","checked":false}],"size":"jsn-input-small-fluid","vtitle":"","vfirst":"1","vmiddle":"","vlast":"1","identify":"jsn_tmp_859991"}},{"type":"email","position":"left","identify":"email","label":"Email","instruction":"","options":{"label":"Email","instruction":"","required":"1","noDuplicates":0,"size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_600109"}},{"type":"address","position":"left","identify":"address","label":"Address","instruction":"","options":{"label":"Address","instruction":"","vstreetAddress":"1","vstreetAddress2":"1","vcity":"1","vcode":"1","vstate":"1","vcountry":"1","required":0,"country":[{"text":"Afghanistan","checked":true},{"text":"Albania","checked":false},{"text":"Algeria","checked":false},{"text":"Andorra","checked":false},{"text":"Angola","checked":false},{"text":"Antigua and Barbuda","checked":false},{"text":"Argentina","checked":false},{"text":"Armenia","checked":false},{"text":"Australia","checked":false},{"text":"Austria","checked":false},{"text":"Azerbaijan","checked":false},{"text":"Bahamas","checked":false},{"text":"Bahrain","checked":false},{"text":"Bangladesh","checked":false},{"text":"Barbados","checked":false},{"text":"Belarus","checked":false},{"text":"Belgium","checked":false},{"text":"Belize","checked":false},{"text":"Benin","checked":false},{"text":"Bhutan","checked":false},{"text":"Bolivia","checked":false},{"text":"Bosnia and Herzegovina","checked":false},{"text":"Botswana","checked":false},{"text":"Brazil","checked":false},{"text":"Brunei","checked":false},{"text":"Bulgaria","checked":false},{"text":"Burkina Faso","checked":false},{"text":"Burundi","checked":false},{"text":"Cambodia","checked":false},{"text":"Cameroon","checked":false},{"text":"Canada","checked":false},{"text":"Cape Verde","checked":false},{"text":"Central African Republic","checked":false},{"text":"Chad","checked":false},{"text":"Chile","checked":false},{"text":"China","checked":false},{"text":"Colombi","checked":false},{"text":"Comoros","checked":false},{"text":"Congo (Brazzaville)","checked":false},{"text":"Congo","checked":false},{"text":"Costa Rica","checked":false},{"text":"Cote d\'Ivoire","checked":false},{"text":"Croatia","checked":false},{"text":"Cuba","checked":false},{"text":"Cyprus","checked":false},{"text":"Czech Republic","checked":false},{"text":"Denmark","checked":false},{"text":"Djibouti","checked":false},{"text":"Dominica","checked":false},{"text":"Dominican Republic","checked":false},{"text":"East Timor (Timor Timur)","checked":false},{"text":"Ecuador","checked":false},{"text":"Egypt","checked":false},{"text":"El Salvador","checked":false},{"text":"Equatorial Guinea","checked":false},{"text":"Eritrea","checked":false},{"text":"Estonia","checked":false},{"text":"Ethiopia","checked":false},{"text":"Fiji","checked":false},{"text":"Finland","checked":false},{"text":"France","checked":false},{"text":"Gabon","checked":false},{"text":"Gambia, The","checked":false},{"text":"Georgia","checked":false},{"text":"Germany","checked":false},{"text":"Ghana","checked":false},{"text":"Greece","checked":false},{"text":"Grenada","checked":false},{"text":"Guatemala","checked":false},{"text":"Guinea","checked":false},{"text":"Guinea-Bissau","checked":false},{"text":"Guyana","checked":false},{"text":"Haiti","checked":false},{"text":"Honduras","checked":false},{"text":"Hungary","checked":false},{"text":"Iceland","checked":false},{"text":"India","checked":false},{"text":"Indonesia","checked":false},{"text":"Iran","checked":false},{"text":"Iraq","checked":false},{"text":"Ireland","checked":false},{"text":"Israel","checked":false},{"text":"Italy","checked":false},{"text":"Jamaica","checked":false},{"text":"Japan","checked":false},{"text":"Jordan","checked":false},{"text":"Kazakhstan","checked":false},{"text":"Kenya","checked":false},{"text":"Kiribati","checked":false},{"text":"Korea, North","checked":false},{"text":"Korea, South","checked":false},{"text":"Kuwait","checked":false},{"text":"Kyrgyzstan","checked":false},{"text":"Laos","checked":false},{"text":"Latvia","checked":false},{"text":"Lebanon","checked":false},{"text":"Lesotho","checked":false},{"text":"Liberia","checked":false},{"text":"Libya","checked":false},{"text":"Liechtenstein","checked":false},{"text":"Lithuania","checked":false},{"text":"Luxembourg","checked":false},{"text":"Macedonia","checked":false},{"text":"Madagascar","checked":false},{"text":"Malawi","checked":false},{"text":"Malaysia","checked":false},{"text":"Maldives","checked":false},{"text":"Mali","checked":false},{"text":"Malta","checked":false},{"text":"Marshall Islands","checked":false},{"text":"Mauritania","checked":false},{"text":"Mauritius","checked":false},{"text":"Mexico","checked":false},{"text":"Micronesia","checked":false},{"text":"Moldova","checked":false},{"text":"Monaco","checked":false},{"text":"Mongolia","checked":false},{"text":"Morocco","checked":false},{"text":"Mozambique","checked":false},{"text":"Myanmar","checked":false},{"text":"Namibia","checked":false},{"text":"Nauru","checked":false},{"text":"Nepa","checked":false},{"text":"Netherlands","checked":false},{"text":"New Zealand","checked":false},{"text":"Nicaragua","checked":false},{"text":"Niger","checked":false},{"text":"Nigeria","checked":false},{"text":"Norway","checked":false},{"text":"Oman","checked":false},{"text":"Pakistan","checked":false},{"text":"Palau","checked":false},{"text":"Panama","checked":false},{"text":"Papua New Guinea","checked":false},{"text":"Paraguay","checked":false},{"text":"Peru","checked":false},{"text":"Philippines","checked":false},{"text":"Poland","checked":false},{"text":"Portugal","checked":false},{"text":"Qatar","checked":false},{"text":"Romania","checked":false},{"text":"Russia","checked":false},{"text":"Rwanda","checked":false},{"text":"Saint Kitts and Nevis","checked":false},{"text":"Saint Lucia","checked":false},{"text":"Saint Vincent","checked":false},{"text":"Samoa","checked":false},{"text":"San Marino","checked":false},{"text":"Sao Tome and Principe","checked":false},{"text":"Saudi Arabia","checked":false},{"text":"Senegal","checked":false},{"text":"Serbia and Montenegro","checked":false},{"text":"Seychelles","checked":false},{"text":"Sierra Leone","checked":false},{"text":"Singapore","checked":false},{"text":"Slovakia","checked":false},{"text":"Slovenia","checked":false},{"text":"Solomon Islands","checked":false},{"text":"Somalia","checked":false},{"text":"South Africa","checked":false},{"text":"Spain","checked":false},{"text":"Sri Lanka","checked":false},{"text":"Sudan","checked":false},{"text":"Suriname","checked":false},{"text":"Swaziland","checked":false},{"text":"Sweden","checked":false},{"text":"Switzerland","checked":false},{"text":"Syria","checked":false},{"text":"Taiwan","checked":false},{"text":"Tajikistan","checked":false},{"text":"Tanzania","checked":false},{"text":"Thailand","checked":false},{"text":"Togo","checked":false},{"text":"Tonga","checked":false},{"text":"Trinidad and Tobago","checked":false},{"text":"Tunisia","checked":false},{"text":"Turkey","checked":false},{"text":"Turkmenistan","checked":false},{"text":"Tuvalu","checked":false},{"text":"Uganda","checked":false},{"text":"Ukraine","checked":false},{"text":"United Arab Emirates","checked":false},{"text":"United Kingdom","checked":false},{"text":"United States","checked":false},{"text":"Uruguay","checked":false},{"text":"Uzbekistan","checked":false},{"text":"Vanuatu","checked":false},{"text":"Vatican City","checked":false},{"text":"Venezuela","checked":false},{"text":"Vietnam","checked":false},{"text":"Yemen","checked":false},{"text":"Zambia","checked":false},{"text":"Zimbabwe","checked":false}],"identify":"jsn_tmp_319579"}},{"type":"single-line-text","position":"left","identify":"company","label":"Company","instruction":"","options":{"label":"Company","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"limitType":"Words","size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_437796"}},{"type":"choices","position":"left","identify":"how_would_you_rate_our_customer_support_","label":"How would you rate our customer support?","instruction":"","options":{"label":"How would you rate our customer support?","instruction":"","required":0,"randomize":0,"layout":"jsn-columns-count-one","items":[{"text":"Excellent","checked":false},{"text":"Good","checked":false},{"text":"Fair","checked":false},{"text":"Poor","checked":false},{"text":"Terrible","checked":false}],"value":"","identify":"jsn_tmp_226978"}},{"type":"paragraph-text","position":"left","identify":"feedback","label":"Feedback","instruction":"","options":{"label":"Feedback","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_750906"}}]';
				break;
			case 'Job Application':
				$return = '[{"type":"name","position":"left","identify":"name","label":"Name","instruction":"","options":{"label":"Name","instruction":"","required":"1","format":"Extended","items":[{"text":"Mrs","checked":false},{"text":"Mr","checked":true},{"text":"Ms","checked":false},{"text":"Baby","checked":false},{"text":"Master","checked":false},{"text":"Prof","checked":false},{"text":"Dr","checked":false},{"text":"Gen","checked":false},{"text":"Rep","checked":false},{"text":"Sen","checked":false},{"text":"St","checked":false}],"vtitle":"1","vfirst":"1","vmiddle":"1","vlast":"1","identify":"jsn_tmp_484544"}},{"type":"choices","position":"left","identify":"gender","label":"Gender","instruction":"","options":{"label":"Gender","instruction":"","required":"1","randomize":0,"layout":"jsn-columns-count-no","items":[{"text":"Male","checked":false},{"text":"Female","checked":false}],"value":"","identify":"jsn_tmp_709858"}},{"type":"date","position":"left","identify":"date_of_birth","label":"Date of Birth","instruction":"","options":{"label":"Date of Birth","instruction":"","required":"1","enableRageSelection":0,"size":"jsn-input-small-fluid","timeFormat":0,"dateFormat":"1","identify":"jsn_tmp_480445","dateValue":"","dateValueRange":"","dateOptionFormat":"mm\/dd\/yy","timeOptionFormat":"hh:mm tt"}},{"type":"email","position":"left","identify":"email","label":"Email","instruction":"","options":{"label":"Email","instruction":"","required":"1","noDuplicates":0,"size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_351177"}},{"type":"phone","position":"left","identify":"phone","label":"Phone","instruction":"","options":{"label":"Phone","instruction":"","required":0,"format":"1-field","value":"","identify":"jsn_tmp_324795","oneField":"","twoField":"","threeField":""}},{"type":"address","position":"left","identify":"address","label":"Address","instruction":"","options":{"label":"Address","instruction":"","vstreetAddress":"1","vstreetAddress2":"1","vcity":"1","vcode":"1","vstate":"1","vcountry":"1","required":0,"country":[{"text":"Afghanistan","checked":true},{"text":"Albania","checked":false},{"text":"Algeria","checked":false},{"text":"Andorra","checked":false},{"text":"Angola","checked":false},{"text":"Antigua and Barbuda","checked":false},{"text":"Argentina","checked":false},{"text":"Armenia","checked":false},{"text":"Australia","checked":false},{"text":"Austria","checked":false},{"text":"Azerbaijan","checked":false},{"text":"Bahamas","checked":false},{"text":"Bahrain","checked":false},{"text":"Bangladesh","checked":false},{"text":"Barbados","checked":false},{"text":"Belarus","checked":false},{"text":"Belgium","checked":false},{"text":"Belize","checked":false},{"text":"Benin","checked":false},{"text":"Bhutan","checked":false},{"text":"Bolivia","checked":false},{"text":"Bosnia and Herzegovina","checked":false},{"text":"Botswana","checked":false},{"text":"Brazil","checked":false},{"text":"Brunei","checked":false},{"text":"Bulgaria","checked":false},{"text":"Burkina Faso","checked":false},{"text":"Burundi","checked":false},{"text":"Cambodia","checked":false},{"text":"Cameroon","checked":false},{"text":"Canada","checked":false},{"text":"Cape Verde","checked":false},{"text":"Central African Republic","checked":false},{"text":"Chad","checked":false},{"text":"Chile","checked":false},{"text":"China","checked":false},{"text":"Colombi","checked":false},{"text":"Comoros","checked":false},{"text":"Congo (Brazzaville)","checked":false},{"text":"Congo","checked":false},{"text":"Costa Rica","checked":false},{"text":"Cote d\'Ivoire","checked":false},{"text":"Croatia","checked":false},{"text":"Cuba","checked":false},{"text":"Cyprus","checked":false},{"text":"Czech Republic","checked":false},{"text":"Denmark","checked":false},{"text":"Djibouti","checked":false},{"text":"Dominica","checked":false},{"text":"Dominican Republic","checked":false},{"text":"East Timor (Timor Timur)","checked":false},{"text":"Ecuador","checked":false},{"text":"Egypt","checked":false},{"text":"El Salvador","checked":false},{"text":"Equatorial Guinea","checked":false},{"text":"Eritrea","checked":false},{"text":"Estonia","checked":false},{"text":"Ethiopia","checked":false},{"text":"Fiji","checked":false},{"text":"Finland","checked":false},{"text":"France","checked":false},{"text":"Gabon","checked":false},{"text":"Gambia, The","checked":false},{"text":"Georgia","checked":false},{"text":"Germany","checked":false},{"text":"Ghana","checked":false},{"text":"Greece","checked":false},{"text":"Grenada","checked":false},{"text":"Guatemala","checked":false},{"text":"Guinea","checked":false},{"text":"Guinea-Bissau","checked":false},{"text":"Guyana","checked":false},{"text":"Haiti","checked":false},{"text":"Honduras","checked":false},{"text":"Hungary","checked":false},{"text":"Iceland","checked":false},{"text":"India","checked":false},{"text":"Indonesia","checked":false},{"text":"Iran","checked":false},{"text":"Iraq","checked":false},{"text":"Ireland","checked":false},{"text":"Israel","checked":false},{"text":"Italy","checked":false},{"text":"Jamaica","checked":false},{"text":"Japan","checked":false},{"text":"Jordan","checked":false},{"text":"Kazakhstan","checked":false},{"text":"Kenya","checked":false},{"text":"Kiribati","checked":false},{"text":"Korea, North","checked":false},{"text":"Korea, South","checked":false},{"text":"Kuwait","checked":false},{"text":"Kyrgyzstan","checked":false},{"text":"Laos","checked":false},{"text":"Latvia","checked":false},{"text":"Lebanon","checked":false},{"text":"Lesotho","checked":false},{"text":"Liberia","checked":false},{"text":"Libya","checked":false},{"text":"Liechtenstein","checked":false},{"text":"Lithuania","checked":false},{"text":"Luxembourg","checked":false},{"text":"Macedonia","checked":false},{"text":"Madagascar","checked":false},{"text":"Malawi","checked":false},{"text":"Malaysia","checked":false},{"text":"Maldives","checked":false},{"text":"Mali","checked":false},{"text":"Malta","checked":false},{"text":"Marshall Islands","checked":false},{"text":"Mauritania","checked":false},{"text":"Mauritius","checked":false},{"text":"Mexico","checked":false},{"text":"Micronesia","checked":false},{"text":"Moldova","checked":false},{"text":"Monaco","checked":false},{"text":"Mongolia","checked":false},{"text":"Morocco","checked":false},{"text":"Mozambique","checked":false},{"text":"Myanmar","checked":false},{"text":"Namibia","checked":false},{"text":"Nauru","checked":false},{"text":"Nepa","checked":false},{"text":"Netherlands","checked":false},{"text":"New Zealand","checked":false},{"text":"Nicaragua","checked":false},{"text":"Niger","checked":false},{"text":"Nigeria","checked":false},{"text":"Norway","checked":false},{"text":"Oman","checked":false},{"text":"Pakistan","checked":false},{"text":"Palau","checked":false},{"text":"Panama","checked":false},{"text":"Papua New Guinea","checked":false},{"text":"Paraguay","checked":false},{"text":"Peru","checked":false},{"text":"Philippines","checked":false},{"text":"Poland","checked":false},{"text":"Portugal","checked":false},{"text":"Qatar","checked":false},{"text":"Romania","checked":false},{"text":"Russia","checked":false},{"text":"Rwanda","checked":false},{"text":"Saint Kitts and Nevis","checked":false},{"text":"Saint Lucia","checked":false},{"text":"Saint Vincent","checked":false},{"text":"Samoa","checked":false},{"text":"San Marino","checked":false},{"text":"Sao Tome and Principe","checked":false},{"text":"Saudi Arabia","checked":false},{"text":"Senegal","checked":false},{"text":"Serbia and Montenegro","checked":false},{"text":"Seychelles","checked":false},{"text":"Sierra Leone","checked":false},{"text":"Singapore","checked":false},{"text":"Slovakia","checked":false},{"text":"Slovenia","checked":false},{"text":"Solomon Islands","checked":false},{"text":"Somalia","checked":false},{"text":"South Africa","checked":false},{"text":"Spain","checked":false},{"text":"Sri Lanka","checked":false},{"text":"Sudan","checked":false},{"text":"Suriname","checked":false},{"text":"Swaziland","checked":false},{"text":"Sweden","checked":false},{"text":"Switzerland","checked":false},{"text":"Syria","checked":false},{"text":"Taiwan","checked":false},{"text":"Tajikistan","checked":false},{"text":"Tanzania","checked":false},{"text":"Thailand","checked":false},{"text":"Togo","checked":false},{"text":"Tonga","checked":false},{"text":"Trinidad and Tobago","checked":false},{"text":"Tunisia","checked":false},{"text":"Turkey","checked":false},{"text":"Turkmenistan","checked":false},{"text":"Tuvalu","checked":false},{"text":"Uganda","checked":false},{"text":"Ukraine","checked":false},{"text":"United Arab Emirates","checked":false},{"text":"United Kingdom","checked":false},{"text":"United States","checked":false},{"text":"Uruguay","checked":false},{"text":"Uzbekistan","checked":false},{"text":"Vanuatu","checked":false},{"text":"Vatican City","checked":false},{"text":"Venezuela","checked":false},{"text":"Vietnam","checked":false},{"text":"Yemen","checked":false},{"text":"Zambia","checked":false},{"text":"Zimbabwe","checked":false}],"identify":"jsn_tmp_840267"}},{"type":"paragraph-text","position":"left","identify":"which_position_are_you_applying_for_","label":"Which position are you applying for?","instruction":"","options":{"label":"Which position are you applying for?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"3","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_849757"}},{"type":"paragraph-text","position":"left","identify":"are_you_willing_to_relocate_","label":"Are you willing to relocate?","instruction":"","options":{"label":"Are you willing to relocate?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"3","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_30786"}},{"type":"paragraph-text","position":"left","identify":"when_can_you_start_","label":"When can you start?","instruction":"","options":{"label":"When can you start?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"3","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_221957"}},{"type":"paragraph-text","position":"left","identify":"portfolio_web_site","label":"Portfolio Web Site","instruction":"","options":{"label":"Portfolio Web Site","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"3","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_275768"}},{"type":"paragraph-text","position":"left","identify":"salary_requirement","label":"Salary Requirement","instruction":"","options":{"label":"Salary Requirement","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"3","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_457473"}},{"type":"file-upload","position":"left","identify":"resume_or_cv","label":"Resume or CV","instruction":"","options":{"label":"Resume or CV","instruction":"","required":0,"allowedExtensions":"png,jpg,gif,zip,rar,txt,doc,pdf","maxSize":0,"maxSizeUnit":"KB","identify":"jsn_tmp_727578"}}]';
				break;
			case 'Event Registration':
				$return = '[{"type":"number","position":"left","identify":"number_of_attendance","label":"Number of Attendance","instruction":"","options":{"label":"Number of Attendance","instruction":"","required":"1","limitation":0,"limitMin":0,"limitMax":0,"size":"jsn-input-small-fluid","value":"","identify":"jsn_tmp_841112"}},{"type":"name","position":"left","identify":"name","label":"Name","instruction":"","options":{"label":"Name","instruction":"","required":"1","format":"Extended","items":[{"text":"Mrs","checked":false},{"text":"Mr","checked":true},{"text":"Ms","checked":false},{"text":"Baby","checked":false},{"text":"Master","checked":false},{"text":"Prof","checked":false},{"text":"Dr","checked":false},{"text":"Gen","checked":false},{"text":"Rep","checked":false},{"text":"Sen","checked":false},{"text":"St","checked":false}],"vtitle":"1","vfirst":"1","vmiddle":"1","vlast":"1","identify":"jsn_tmp_676286"}},{"type":"address","position":"left","identify":"address","label":"Address","instruction":"","options":{"label":"Address","instruction":"","vstreetAddress":"1","vstreetAddress2":"1","vcity":"1","vcode":"1","vstate":"1","vcountry":"1","required":"1","country":[{"text":"Afghanistan","checked":true},{"text":"Albania","checked":false},{"text":"Algeria","checked":false},{"text":"Andorra","checked":false},{"text":"Angola","checked":false},{"text":"Antigua and Barbuda","checked":false},{"text":"Argentina","checked":false},{"text":"Armenia","checked":false},{"text":"Australia","checked":false},{"text":"Austria","checked":false},{"text":"Azerbaijan","checked":false},{"text":"Bahamas","checked":false},{"text":"Bahrain","checked":false},{"text":"Bangladesh","checked":false},{"text":"Barbados","checked":false},{"text":"Belarus","checked":false},{"text":"Belgium","checked":false},{"text":"Belize","checked":false},{"text":"Benin","checked":false},{"text":"Bhutan","checked":false},{"text":"Bolivia","checked":false},{"text":"Bosnia and Herzegovina","checked":false},{"text":"Botswana","checked":false},{"text":"Brazil","checked":false},{"text":"Brunei","checked":false},{"text":"Bulgaria","checked":false},{"text":"Burkina Faso","checked":false},{"text":"Burundi","checked":false},{"text":"Cambodia","checked":false},{"text":"Cameroon","checked":false},{"text":"Canada","checked":false},{"text":"Cape Verde","checked":false},{"text":"Central African Republic","checked":false},{"text":"Chad","checked":false},{"text":"Chile","checked":false},{"text":"China","checked":false},{"text":"Colombi","checked":false},{"text":"Comoros","checked":false},{"text":"Congo (Brazzaville)","checked":false},{"text":"Congo","checked":false},{"text":"Costa Rica","checked":false},{"text":"Cote d\'Ivoire","checked":false},{"text":"Croatia","checked":false},{"text":"Cuba","checked":false},{"text":"Cyprus","checked":false},{"text":"Czech Republic","checked":false},{"text":"Denmark","checked":false},{"text":"Djibouti","checked":false},{"text":"Dominica","checked":false},{"text":"Dominican Republic","checked":false},{"text":"East Timor (Timor Timur)","checked":false},{"text":"Ecuador","checked":false},{"text":"Egypt","checked":false},{"text":"El Salvador","checked":false},{"text":"Equatorial Guinea","checked":false},{"text":"Eritrea","checked":false},{"text":"Estonia","checked":false},{"text":"Ethiopia","checked":false},{"text":"Fiji","checked":false},{"text":"Finland","checked":false},{"text":"France","checked":false},{"text":"Gabon","checked":false},{"text":"Gambia, The","checked":false},{"text":"Georgia","checked":false},{"text":"Germany","checked":false},{"text":"Ghana","checked":false},{"text":"Greece","checked":false},{"text":"Grenada","checked":false},{"text":"Guatemala","checked":false},{"text":"Guinea","checked":false},{"text":"Guinea-Bissau","checked":false},{"text":"Guyana","checked":false},{"text":"Haiti","checked":false},{"text":"Honduras","checked":false},{"text":"Hungary","checked":false},{"text":"Iceland","checked":false},{"text":"India","checked":false},{"text":"Indonesia","checked":false},{"text":"Iran","checked":false},{"text":"Iraq","checked":false},{"text":"Ireland","checked":false},{"text":"Israel","checked":false},{"text":"Italy","checked":false},{"text":"Jamaica","checked":false},{"text":"Japan","checked":false},{"text":"Jordan","checked":false},{"text":"Kazakhstan","checked":false},{"text":"Kenya","checked":false},{"text":"Kiribati","checked":false},{"text":"Korea, North","checked":false},{"text":"Korea, South","checked":false},{"text":"Kuwait","checked":false},{"text":"Kyrgyzstan","checked":false},{"text":"Laos","checked":false},{"text":"Latvia","checked":false},{"text":"Lebanon","checked":false},{"text":"Lesotho","checked":false},{"text":"Liberia","checked":false},{"text":"Libya","checked":false},{"text":"Liechtenstein","checked":false},{"text":"Lithuania","checked":false},{"text":"Luxembourg","checked":false},{"text":"Macedonia","checked":false},{"text":"Madagascar","checked":false},{"text":"Malawi","checked":false},{"text":"Malaysia","checked":false},{"text":"Maldives","checked":false},{"text":"Mali","checked":false},{"text":"Malta","checked":false},{"text":"Marshall Islands","checked":false},{"text":"Mauritania","checked":false},{"text":"Mauritius","checked":false},{"text":"Mexico","checked":false},{"text":"Micronesia","checked":false},{"text":"Moldova","checked":false},{"text":"Monaco","checked":false},{"text":"Mongolia","checked":false},{"text":"Morocco","checked":false},{"text":"Mozambique","checked":false},{"text":"Myanmar","checked":false},{"text":"Namibia","checked":false},{"text":"Nauru","checked":false},{"text":"Nepa","checked":false},{"text":"Netherlands","checked":false},{"text":"New Zealand","checked":false},{"text":"Nicaragua","checked":false},{"text":"Niger","checked":false},{"text":"Nigeria","checked":false},{"text":"Norway","checked":false},{"text":"Oman","checked":false},{"text":"Pakistan","checked":false},{"text":"Palau","checked":false},{"text":"Panama","checked":false},{"text":"Papua New Guinea","checked":false},{"text":"Paraguay","checked":false},{"text":"Peru","checked":false},{"text":"Philippines","checked":false},{"text":"Poland","checked":false},{"text":"Portugal","checked":false},{"text":"Qatar","checked":false},{"text":"Romania","checked":false},{"text":"Russia","checked":false},{"text":"Rwanda","checked":false},{"text":"Saint Kitts and Nevis","checked":false},{"text":"Saint Lucia","checked":false},{"text":"Saint Vincent","checked":false},{"text":"Samoa","checked":false},{"text":"San Marino","checked":false},{"text":"Sao Tome and Principe","checked":false},{"text":"Saudi Arabia","checked":false},{"text":"Senegal","checked":false},{"text":"Serbia and Montenegro","checked":false},{"text":"Seychelles","checked":false},{"text":"Sierra Leone","checked":false},{"text":"Singapore","checked":false},{"text":"Slovakia","checked":false},{"text":"Slovenia","checked":false},{"text":"Solomon Islands","checked":false},{"text":"Somalia","checked":false},{"text":"South Africa","checked":false},{"text":"Spain","checked":false},{"text":"Sri Lanka","checked":false},{"text":"Sudan","checked":false},{"text":"Suriname","checked":false},{"text":"Swaziland","checked":false},{"text":"Sweden","checked":false},{"text":"Switzerland","checked":false},{"text":"Syria","checked":false},{"text":"Taiwan","checked":false},{"text":"Tajikistan","checked":false},{"text":"Tanzania","checked":false},{"text":"Thailand","checked":false},{"text":"Togo","checked":false},{"text":"Tonga","checked":false},{"text":"Trinidad and Tobago","checked":false},{"text":"Tunisia","checked":false},{"text":"Turkey","checked":false},{"text":"Turkmenistan","checked":false},{"text":"Tuvalu","checked":false},{"text":"Uganda","checked":false},{"text":"Ukraine","checked":false},{"text":"United Arab Emirates","checked":false},{"text":"United Kingdom","checked":false},{"text":"United States","checked":false},{"text":"Uruguay","checked":false},{"text":"Uzbekistan","checked":false},{"text":"Vanuatu","checked":false},{"text":"Vatican City","checked":false},{"text":"Venezuela","checked":false},{"text":"Vietnam","checked":false},{"text":"Yemen","checked":false},{"text":"Zambia","checked":false},{"text":"Zimbabwe","checked":false}],"identify":"jsn_tmp_885160"}},{"type":"email","position":"left","identify":"email","label":"Email","instruction":"","options":{"label":"Email","instruction":"","required":"1","noDuplicates":0,"size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_471658"}},{"type":"phone","position":"left","identify":"phone","label":"Phone","instruction":"","options":{"label":"Phone","instruction":"","required":0,"format":"1-field","value":"","identify":"jsn_tmp_978072","oneField":"","twoField":"","threeField":""}},{"type":"single-line-text","position":"left","identify":"company","label":"Company","instruction":"","options":{"label":"Company","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"limitType":"Words","size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_963935"}},{"type":"choices","position":"left","identify":"t_shirt_size","label":"T-Shirt Size","instruction":"","options":{"label":"T-Shirt Size","instruction":"","required":0,"randomize":0,"layout":"jsn-columns-count-three","items":[{"text":"S","checked":false},{"text":"M","checked":false},{"text":"L","checked":false},{"text":"XL","checked":false},{"text":"XXL","checked":false},{"text":"3XL","checked":false}],"value":"","identify":"jsn_tmp_41996"}},{"type":"paragraph-text","position":"left","identify":"message","label":"Message","instruction":"","options":{"label":"Message","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"jsn_tmp_496497"}}]';
				break;
			case 'Voting Form':
				$return = '[{"type":"name","position":"left","identify":"name","label":"Name","instruction":"","options":{"label":"Name","instruction":"","required":"1","format":"Extended","items":[{"text":"Mrs","checked":false},{"text":"Mr","checked":true},{"text":"Ms","checked":false},{"text":"Baby","checked":false},{"text":"Master","checked":false},{"text":"Prof","checked":false},{"text":"Dr","checked":false},{"text":"Gen","checked":false},{"text":"Rep","checked":false},{"text":"Sen","checked":false},{"text":"St","checked":false}],"vtitle":"1","vfirst":"1","vmiddle":"1","vlast":"1","identify":"jsn_tmp_83149"}},{"type":"choices","position":"left","identify":"multiple_choice","label":"Multiple Choice","instruction":"","options":{"label":"Multiple Choice","instruction":"","required":"1","randomize":0,"layout":"jsn-columns-count-no","items":[{"text":"Male","checked":false},{"text":"Female","checked":false}],"value":"","identify":"jsn_tmp_580389"}},{"type":"date","position":"left","identify":"date_of_birth","label":"Date of Birth","instruction":"","options":{"label":"Date of Birth","instruction":"","required":0,"enableRageSelection":0,"size":"jsn-input-small-fluid","timeFormat":0,"dateFormat":"1","identify":"jsn_tmp_475674","dateValue":"","dateValueRange":"","dateOptionFormat":"mm\/dd\/yy","timeOptionFormat":"hh:mm tt"}},{"type":"email","position":"left","identify":"email","label":"Email","instruction":"","options":{"label":"Email","instruction":"","required":"1","noDuplicates":0,"size":"jsn-input-medium-fluid","value":"","identify":"jsn_tmp_458467"}},{"type":"address","position":"left","identify":"address","label":"Address","instruction":"","options":{"label":"Address","instruction":"","vstreetAddress":"1","vstreetAddress2":"1","vcity":"1","vcode":"1","vstate":"1","vcountry":"1","required":0,"country":[{"text":"Afghanistan","checked":true},{"text":"Albania","checked":false},{"text":"Algeria","checked":false},{"text":"Andorra","checked":false},{"text":"Angola","checked":false},{"text":"Antigua and Barbuda","checked":false},{"text":"Argentina","checked":false},{"text":"Armenia","checked":false},{"text":"Australia","checked":false},{"text":"Austria","checked":false},{"text":"Azerbaijan","checked":false},{"text":"Bahamas","checked":false},{"text":"Bahrain","checked":false},{"text":"Bangladesh","checked":false},{"text":"Barbados","checked":false},{"text":"Belarus","checked":false},{"text":"Belgium","checked":false},{"text":"Belize","checked":false},{"text":"Benin","checked":false},{"text":"Bhutan","checked":false},{"text":"Bolivia","checked":false},{"text":"Bosnia and Herzegovina","checked":false},{"text":"Botswana","checked":false},{"text":"Brazil","checked":false},{"text":"Brunei","checked":false},{"text":"Bulgaria","checked":false},{"text":"Burkina Faso","checked":false},{"text":"Burundi","checked":false},{"text":"Cambodia","checked":false},{"text":"Cameroon","checked":false},{"text":"Canada","checked":false},{"text":"Cape Verde","checked":false},{"text":"Central African Republic","checked":false},{"text":"Chad","checked":false},{"text":"Chile","checked":false},{"text":"China","checked":false},{"text":"Colombi","checked":false},{"text":"Comoros","checked":false},{"text":"Congo (Brazzaville)","checked":false},{"text":"Congo","checked":false},{"text":"Costa Rica","checked":false},{"text":"Cote d\'Ivoire","checked":false},{"text":"Croatia","checked":false},{"text":"Cuba","checked":false},{"text":"Cyprus","checked":false},{"text":"Czech Republic","checked":false},{"text":"Denmark","checked":false},{"text":"Djibouti","checked":false},{"text":"Dominica","checked":false},{"text":"Dominican Republic","checked":false},{"text":"East Timor (Timor Timur)","checked":false},{"text":"Ecuador","checked":false},{"text":"Egypt","checked":false},{"text":"El Salvador","checked":false},{"text":"Equatorial Guinea","checked":false},{"text":"Eritrea","checked":false},{"text":"Estonia","checked":false},{"text":"Ethiopia","checked":false},{"text":"Fiji","checked":false},{"text":"Finland","checked":false},{"text":"France","checked":false},{"text":"Gabon","checked":false},{"text":"Gambia, The","checked":false},{"text":"Georgia","checked":false},{"text":"Germany","checked":false},{"text":"Ghana","checked":false},{"text":"Greece","checked":false},{"text":"Grenada","checked":false},{"text":"Guatemala","checked":false},{"text":"Guinea","checked":false},{"text":"Guinea-Bissau","checked":false},{"text":"Guyana","checked":false},{"text":"Haiti","checked":false},{"text":"Honduras","checked":false},{"text":"Hungary","checked":false},{"text":"Iceland","checked":false},{"text":"India","checked":false},{"text":"Indonesia","checked":false},{"text":"Iran","checked":false},{"text":"Iraq","checked":false},{"text":"Ireland","checked":false},{"text":"Israel","checked":false},{"text":"Italy","checked":false},{"text":"Jamaica","checked":false},{"text":"Japan","checked":false},{"text":"Jordan","checked":false},{"text":"Kazakhstan","checked":false},{"text":"Kenya","checked":false},{"text":"Kiribati","checked":false},{"text":"Korea, North","checked":false},{"text":"Korea, South","checked":false},{"text":"Kuwait","checked":false},{"text":"Kyrgyzstan","checked":false},{"text":"Laos","checked":false},{"text":"Latvia","checked":false},{"text":"Lebanon","checked":false},{"text":"Lesotho","checked":false},{"text":"Liberia","checked":false},{"text":"Libya","checked":false},{"text":"Liechtenstein","checked":false},{"text":"Lithuania","checked":false},{"text":"Luxembourg","checked":false},{"text":"Macedonia","checked":false},{"text":"Madagascar","checked":false},{"text":"Malawi","checked":false},{"text":"Malaysia","checked":false},{"text":"Maldives","checked":false},{"text":"Mali","checked":false},{"text":"Malta","checked":false},{"text":"Marshall Islands","checked":false},{"text":"Mauritania","checked":false},{"text":"Mauritius","checked":false},{"text":"Mexico","checked":false},{"text":"Micronesia","checked":false},{"text":"Moldova","checked":false},{"text":"Monaco","checked":false},{"text":"Mongolia","checked":false},{"text":"Morocco","checked":false},{"text":"Mozambique","checked":false},{"text":"Myanmar","checked":false},{"text":"Namibia","checked":false},{"text":"Nauru","checked":false},{"text":"Nepa","checked":false},{"text":"Netherlands","checked":false},{"text":"New Zealand","checked":false},{"text":"Nicaragua","checked":false},{"text":"Niger","checked":false},{"text":"Nigeria","checked":false},{"text":"Norway","checked":false},{"text":"Oman","checked":false},{"text":"Pakistan","checked":false},{"text":"Palau","checked":false},{"text":"Panama","checked":false},{"text":"Papua New Guinea","checked":false},{"text":"Paraguay","checked":false},{"text":"Peru","checked":false},{"text":"Philippines","checked":false},{"text":"Poland","checked":false},{"text":"Portugal","checked":false},{"text":"Qatar","checked":false},{"text":"Romania","checked":false},{"text":"Russia","checked":false},{"text":"Rwanda","checked":false},{"text":"Saint Kitts and Nevis","checked":false},{"text":"Saint Lucia","checked":false},{"text":"Saint Vincent","checked":false},{"text":"Samoa","checked":false},{"text":"San Marino","checked":false},{"text":"Sao Tome and Principe","checked":false},{"text":"Saudi Arabia","checked":false},{"text":"Senegal","checked":false},{"text":"Serbia and Montenegro","checked":false},{"text":"Seychelles","checked":false},{"text":"Sierra Leone","checked":false},{"text":"Singapore","checked":false},{"text":"Slovakia","checked":false},{"text":"Slovenia","checked":false},{"text":"Solomon Islands","checked":false},{"text":"Somalia","checked":false},{"text":"South Africa","checked":false},{"text":"Spain","checked":false},{"text":"Sri Lanka","checked":false},{"text":"Sudan","checked":false},{"text":"Suriname","checked":false},{"text":"Swaziland","checked":false},{"text":"Sweden","checked":false},{"text":"Switzerland","checked":false},{"text":"Syria","checked":false},{"text":"Taiwan","checked":false},{"text":"Tajikistan","checked":false},{"text":"Tanzania","checked":false},{"text":"Thailand","checked":false},{"text":"Togo","checked":false},{"text":"Tonga","checked":false},{"text":"Trinidad and Tobago","checked":false},{"text":"Tunisia","checked":false},{"text":"Turkey","checked":false},{"text":"Turkmenistan","checked":false},{"text":"Tuvalu","checked":false},{"text":"Uganda","checked":false},{"text":"Ukraine","checked":false},{"text":"United Arab Emirates","checked":false},{"text":"United Kingdom","checked":false},{"text":"United States","checked":false},{"text":"Uruguay","checked":false},{"text":"Uzbekistan","checked":false},{"text":"Vanuatu","checked":false},{"text":"Vatican City","checked":false},{"text":"Venezuela","checked":false},{"text":"Vietnam","checked":false},{"text":"Yemen","checked":false},{"text":"Zambia","checked":false},{"text":"Zimbabwe","checked":false}],"identify":"jsn_tmp_538561"}},{"type":"phone","position":"left","identify":"phone","label":"Phone","instruction":"","options":{"label":"Phone","instruction":"","required":0,"format":"1-field","value":"","identify":"jsn_tmp_673980","oneField":"","twoField":"","threeField":""}},{"type":"choices","position":"left","identify":"overall_how_satisfied_were_you_with_the_product_service_","label":"Overall, how satisfied were you with the product \/ service? ","instruction":"","options":{"label":"Overall, how satisfied were you with the product \/ service? ","instruction":"","required":"1","randomize":0,"layout":"jsn-columns-count-one","items":[{"text":"Very Satisfied","checked":false},{"text":"Satisfied","checked":false},{"text":"Neutral","checked":false},{"text":"Unsatisfied","checked":false},{"text":"Very Unsatisfied","checked":false},{"text":"N\/A ","checked":false}],"value":"","identify":"jsn_tmp_921222"}}]';
				break;
			case 'Survey Product/Service Satisfaction':
				$return = '[{"id":178,"type":"static-content","position":"left","identify":"static_content","label":"Static Content","instruction":null,"options":{"label":"Static Content","value":"<b>Dear Customer:<br><br>\r\nAs the manager of [COMPANY], I want to thank you for giving us the opportunity to serve you. Please help us serve you better by taking a couple of minutes to tell us about the service that you have received so far. We appreciate your business and want to make sure we meet your expectations. Attached, you will find a coupon good for ...... We hope that you will accept this as a token of our good will.\r\n<br><br>\r\nSincerely,\r\n<br><br>\r\n[MANAGER_NAME]\r\n<br><br>\r\nManager<br><\/b><hr><b><br><\/b>","identify":"static_content","customClass":""}},{"id":179,"type":"choices","position":"left","identify":"in_thinking_about_your_most_recent_experience_with_company_was_the_quality_of_customer_service_you_received_","label":"In thinking about your most recent experience with [COMPANY], was the quality of customer service you received: ","instruction":"","options":{"label":"In thinking about your most recent experience with [COMPANY], was the quality of customer service you received: ","instruction":"","required":0,"randomize":0,"labelOthers":"Others","layout":"jsn-columns-count-one","items":[{"text":"Very Poor","checked":false},{"text":"Somewhat Unsatisfactory","checked":false},{"text":"About Average","checked":false},{"text":"Very Satisfactory","checked":false},{"text":"Superior","checked":false}],"value":"","identify":"in_thinking_about_your_most_recent_experience_with_company_was_the_quality_of_customer_service_you_received_","customClass":"","itemAction":""}},{"id":180,"type":"paragraph-text","position":"left","identify":"if_you_indicated_that_the_customer_service_was_unsatisfactory_would_you_please_describe_what_happened_","label":"If you indicated that the customer service was unsatisfactory, would you please describe what happened? ","instruction":"","options":{"label":"If you indicated that the customer service was unsatisfactory, would you please describe what happened? ","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"if_you_indicated_that_the_customer_service_was_unsatisfactory_would_you_please_describe_what_happened_","customClass":""}},{"id":181,"type":"choices","position":"left","identify":"the_process_for_getting_your_concerns_resolved_was_","label":"The process for getting your concerns resolved was: ","instruction":"","options":{"label":"The process for getting your concerns resolved was: ","instruction":"","required":0,"randomize":0,"labelOthers":"Others","layout":"jsn-columns-count-one","items":[{"text":"Very Poor","checked":false},{"text":"Somewhat Unsatisfactory","checked":false},{"text":"About Average","checked":false},{"text":"Very Satisfactory","checked":false},{"text":"Superior","checked":false}],"value":"","identify":"the_process_for_getting_your_concerns_resolved_was_","customClass":"","itemAction":""}},{"id":182,"type":"paragraph-text","position":"left","identify":"would_you_please_take_a_few_minutes_to_describe_what_happened_","label":"Would you please take a few minutes to describe what happened?","instruction":"","options":{"label":"Would you please take a few minutes to describe what happened?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"would_you_please_take_a_few_minutes_to_describe_what_happened_","customClass":""}},{"id":183,"type":"choices","position":"left","identify":"now_please_think_about_the_features_and_benefits_of_the_product_itself_how_satisfied_are_you_with_the_product_","label":"Now please think about the features and benefits of the [PRODUCT] itself. How satisfied are you with the [PRODUCT]:","instruction":"","options":{"label":"Now please think about the features and benefits of the [PRODUCT] itself. How satisfied are you with the [PRODUCT]:","instruction":"","required":0,"randomize":0,"labelOthers":"Others","layout":"jsn-columns-count-one","items":[{"text":"Very Poor","checked":false},{"text":"Somewhat Unsatisfactory","checked":false},{"text":"About Average","checked":false},{"text":"Very Satisfactory","checked":false},{"text":"Superior","checked":false}],"value":"","identify":"now_please_think_about_the_features_and_benefits_of_the_product_itself_how_satisfied_are_you_with_the_product_","customClass":"","itemAction":""}},{"id":184,"type":"paragraph-text","position":"left","identify":"would_you_please_take_a_few_minutes_to_describe_why_you_are_not_satisfied_with_the_product_","label":"Would you please take a few minutes to describe why you are not satisfied with the product?","instruction":"","options":{"label":"Would you please take a few minutes to describe why you are not satisfied with the product?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"would_you_please_take_a_few_minutes_to_describe_why_you_are_not_satisfied_with_the_product_","customClass":""}},{"id":185,"type":"static-content","position":"left","identify":"static_content_9","label":"Static Content","instruction":null,"options":{"label":"Static Content","value":"<b>Customer Service Representative<br><\/b><hr><b><br><\/b>","identify":"static_content_9","customClass":""}},{"id":186,"type":"likert","position":"left","identify":"the_following_questions_pertain_to_the_customer_service_representative_you_spoke_with_most_recently_please_indicate_whether_you_agree_or_disagree_with_the_following_statements_","label":"The following questions pertain to the customer service representative you spoke with most recently. Please indicate whether you agree or disagree with the following statements ","instruction":"","options":{"label":"The following questions pertain to the customer service representative you spoke with most recently. Please indicate whether you agree or disagree with the following statements ","instruction":"","required":0,"size":"jsn-input-mini-fluid","rows":[{"text":"The customer service representative was very courteous","checked":false},{"text":"The customer service representative handled my call quickly","checked":false},{"text":"The customer service representative was very knowledgeable","checked":false}],"columns":[{"text":"Strongly Agree","checked":false},{"text":"Agree","checked":false},{"text":"Neutral","checked":false},{"text":"Disagree","checked":false},{"text":"Strongly Disagree","checked":false}],"identify":"the_following_questions_pertain_to_the_customer_service_representative_you_spoke_with_most_recently_please_indicate_whether_you_agree_or_disagree_with_the_following_statements_","customClass":""}},{"id":187,"type":"paragraph-text","position":"left","identify":"are_there_any_other_comments_about_the_customer_service_representative_you_would_like_to_add_","label":"Are there any other comments about the customer service representative you would like to add?","instruction":"","options":{"label":"Are there any other comments about the customer service representative you would like to add?","instruction":"","required":0,"limitation":0,"limitMin":0,"limitMax":0,"rows":"8","size":"jsn-input-xlarge-fluid","limitType":"Words","value":"","identify":"are_there_any_other_comments_about_the_customer_service_representative_you_would_like_to_add_","customClass":""}},{"id":188,"type":"static-content","position":"left","identify":"static_content_12","label":"Static Content","instruction":null,"options":{"label":"Static Content","value":"<b>The Process<br><\/b><hr><b><br><\/b>","identify":"static_content_12","customClass":""}},{"id":189,"type":"likert","position":"left","identify":"the_following_questions_pertain_to_the_process_by_which_your_most_recent_service_contract_was_handled_please_indicate_whether_you_agree_or_disagree_with_the_following_statements_","label":"The following questions pertain to the process by which your most recent service contract was handled. Please indicate whether you agree or disagree with the following statements. ","instruction":"","options":{"label":"The following questions pertain to the process by which your most recent service contract was handled. Please indicate whether you agree or disagree with the following statements. ","instruction":"","required":0,"size":"jsn-input-mini-fluid","rows":[{"text":"The waiting time for having my questions addressed was satisfactory","checked":false},{"text":"My phone call was quickly transferred to the person who best could answer my question","checked":false},{"text":"The automated phone system made the customer service experience more satisfying","checked":false}],"columns":[{"text":"Strongly Agree","checked":false},{"text":"Agree","checked":false},{"text":"Neutral","checked":false},{"text":"Disagree","checked":false},{"text":"Strongly Disagree","checked":false}],"identify":"the_following_questions_pertain_to_the_process_by_which_your_most_recent_service_contract_was_handled_please_indicate_whether_you_agree_or_disagree_with_the_following_statements_","customClass":""}},{"id":190,"type":"likert","position":"left","identify":"","label":"","instruction":"","options":{"label":"","instruction":"","required":0,"size":"jsn-input-mini-fluid","rows":[{"text":"Considering the total package offered by including customer service, [PRODUCT] features and benefits, and cost; how satisfied are you with [COMPANY]?","checked":false}],"columns":[{"text":"Very Satisfied","checked":false},{"text":"Somewhat Satisfied","checked":false},{"text":"Neutral","checked":false},{"text":"Somewhat Dissatisfied","checked":false},{"text":"Very Dissatisfied","checked":false}],"identify":"","customClass":""}},{"id":191,"type":"static-content","position":"left","identify":"static_content_15","label":"Static Content","instruction":null,"options":{"label":"Static Content","value":"<hr><b>\r\nThank you for your feedback. We sincerely appreciate your honest opinion and will take your input into consideration while providing products and services in the future.\r\n<br>\r\nIf you have any comments or concerns about this survey please Contact: -\r\n<br><br>\r\n[COMPANY]\r\n<br><br>\r\n[MANAGER_NAME]\r\n<br><br>\r\n[ADDRESS_1]\r\n<br><br>\r\n[ADDRESS_2]\r\n<br><br>\r\n[CITY], [STATE], [ZIP]<\/b>","identify":"static_content_15","customClass":""}}]';
				break;
		}
		return $return;
	}

	/**
	 * Get List Email Notification
	 *
	 * @param   type   $items
	 *
	 * @return string
	 */
	public static function getListEmailNotification($items = array())
	{
		$html = '<div class="control-group jsn-items-list-container">
		    <label  class="control-label jsn-label-des-tipsy"  original-title="' . JText::_('JSN_UNIFORM_SPECIFY_EMAIL_ADDRESS') . '">' . JText::_('JSN_UNIFORM_SEND_EMAIL_TO') . '</label>
		    <div class="controls">
			<button class="btn btn-icon pull-right "  data-placement="top" id="btn_email_list" original-title="' . JText::_('JSN_UNIFORM_EMAIL_CONTENT') . '" title="' . JText::_('JSN_UNIFORM_EMAIL_CONTENT') . '" onclick="return false;"><i class="icon-envelope"></i></button>
			<div class="email-addresses">
			    <ul id="emailAddresses" class="jsn-items-list ui-sortable">';
		$script = array();
		if (!empty($items) && count($items))
		{
			foreach ($items as $email)
			{
				if (!empty($email->email_address))
				{
					$script[] = "'" . $email->email_address . "'";
					$emailName = isset($email->email_name) ? $email->email_name : "";
					$emailUserId = isset($email->user_id) ? $email->user_id : "";
					$emailId = isset($email->email_id) ? $email->email_id : "";
					$html .= '<li class="jsn-item ui-state-default jsn-iconbar-trigger" data-email="' . $email->email_address . '" id="email_' . preg_replace('/[^a-zA-Z0-9-_]/i', "_", $email->email_address) . '">
					    <input type="hidden" value="' . $emailName . '" name="form_email_notification_name[' . $email->email_address . ']">
					    <input type="hidden" value="' . $emailId . '" name="semail_id[' . $email->email_address . ']">
					    <input type="hidden" value="' . $emailUserId . '" name="form_email_notification_user_id[' . $email->email_address . ']">
					    <input type="hidden" value="' . $email->email_address . '" name="form_email_notification[]">
					    <span class="email-address">' . $email->email_address . '</span>
					    <div class="jsn-iconbar">
						<a data-email="' . $email->email_address . '" class="element-edit" title="Edit email" href="javascript:void(0)"><i class="icon-pencil"></i></a>
						<a data-email="' . $email->email_address . '" class="element-delete" title="Delete email" href="javascript:void(0)"><i class="icon-trash"></i></a>
					    </div>
					</li>';
				}
			}
		}
		$html .= '
			    </ul>
			    <script> var listemail=[' . implode(",", $script) . '];</script>
			    <a href="#" onclick="return false;" id="show-div-add-email" class="jsn-add-more">' . JText::_('JSN_UNIFORM_ADD_MORE_EMAIL') . '</a>
			    <div id="addMoreEmail" class="jsn-form-bar">
				<div class="control-group input-append">
				    <input  name="nemail" class="input-medium" id="input_new_email" type="text">
				    <button class="btn" id="email-select" href="#myModal" onclick="return false;">' . JText::_('JSN_UNIFORM_EMAIL_SELECT') . '</button>
				</div>
				<div class="control-group">
				    <button class="btn btn-icon" onclick="return false;" id="add-email" title="' . JText::_('JSN_UNIFORM_BUTTON_SAVE') . '" ><i class="icon-ok"></i></button>
				    <button class="btn btn-icon"  onclick="return false;" id="close-email" title="' . JText::_('JSN_UNIFORM_BUTTON_CANCEL') . '"><i class="icon-remove"></i></button>
				</div>
				<div class="control-group"></div>
			    </div>
			</div>
		    </div>
		</div>';
		return $html;
	}

	/**
	 * Generate Style Pages
	 *
	 * @param   Object  $formStyle                List style opbject
	 * @param   String  $container                class css container
	 * @param   String  $containerActive          class css container active
	 * @param   String  $title                    class css title
	 * @param   String  $messageErrors            class css title
	 * @param   String  $messageBackgroundErrors  class css Background errors
	 * @param   String  $field                    class css field
	 *
	 * @return string
	 */
	public static function generateStylePages($formStyle, $container = "", $containerActive = "", $title = "", $messageErrors = "", $messageBackgroundErrors = "", $field = "")
	{
		if (!empty($container))
		{
			$styleCustom[] = $container . "{";
			if (!empty($formStyle->background_color))
			{
				$styleCustom[] = "background-color:{$formStyle->background_color};";
			}
			if (!empty($formStyle->border_thickness))
			{
				$styleCustom[] = "border:{$formStyle->border_thickness}px solid;";
			}
			if (!empty($formStyle->border_color))
			{
				$styleCustom[] = "border-color:{$formStyle->border_color};";
			}
			if (!empty($formStyle->rounded_corner_radius))
			{
				$styleCustom[] = "border-radius:{$formStyle->rounded_corner_radius}px;";
			}
			if (!empty($formStyle->rounded_corner_radius))
			{
				$styleCustom[] = "-moz-border-radius:{$formStyle->rounded_corner_radius};";
			}
			if (!empty($formStyle->rounded_corner_radius))
			{
				$styleCustom[] = "-webkit-border-radius:{$formStyle->rounded_corner_radius};";
			}
			if (!empty($formStyle->padding_space))
			{
				$styleCustom[] = "padding:{$formStyle->padding_space}px;";
			}
			if (!empty($formStyle->margin_space))
			{
				$styleCustom[] = "margin:{$formStyle->margin_space}px 0px;";
			}
			$styleCustom[] = "}";
		}
		if (!empty($containerActive))
		{
			$styleCustom[] = $containerActive . "{";
			if (!empty($formStyle->background_active_color))
			{
				$styleCustom[] = "background-color:{$formStyle->background_active_color};";
			}
			if (!empty($formStyle->border_active_color))
			{
				$styleCustom[] = "border-color:{$formStyle->border_active_color};";
			}
			$styleCustom[] = "}";
		}
		if (!empty($title))
		{
			$styleCustom[] = $title . " {";
			if (!empty($formStyle->text_color))
			{
				$styleCustom[] = "color:{$formStyle->text_color};";
			}
			if (!empty($formStyle->font_type))
			{
				$styleCustom[] = "font-family:{$formStyle->font_type};";
			}
			if (!empty($formStyle->font_size))
			{
				$styleCustom[] = "font-size:{$formStyle->font_size}px;";
			}
			$styleCustom[] = "}";
		}
		if (!empty($messageErrors))
		{
			$styleCustom[] = $messageErrors . " {";
			if (!empty($formStyle->message_error_text_color))
			{
				$styleCustom[] = "color:{$formStyle->message_error_text_color};";
			}
			$styleCustom[] = "}";
		}

		if (!empty($messageBackgroundErrors))
		{
			$styleCustom[] = $messageBackgroundErrors . " {";
			if (!empty($formStyle->message_error_background_color))
			{
				$styleCustom[] = "background-color:{$formStyle->message_error_background_color};";
			}
			$styleCustom[] = "}";
		}
		if ($messageErrors)
		{
			$styleCustom[] = $messageErrors . " {";
			if (!empty($formStyle->message_error_text_color))
			{
				$styleCustom[] = "color:{$formStyle->message_error_text_color};";
			}
			$styleCustom[] = "}";
		}
		if (!empty($field))
		{
			$styleCustom[] = $field . " {";
			if (!empty($formStyle->field_background_color))
			{
				$styleCustom[] = "background:" . $formStyle->field_background_color . ";";
			}
			if (!empty($formStyle->field_shadow_color))
			{
				$styleCustom[] = "box-shadow:0 1px 0 rgba(255, 255, 255, 0.1), 0 1px 7px 0 rgba(" . JSNUniformHelper::hex2rgb($formStyle->field_shadow_color) . ",0.8) inset;";
			}
			if (!empty($formStyle->field_border_color))
			{
				$styleCustom[] = "border-color:{$formStyle->field_border_color};";
			}
			if (!empty($formStyle->field_text_color))
			{
				$styleCustom[] = "color:{$formStyle->field_text_color};";
			}
			$styleCustom[] = "}";
		}
		return implode("\n", $styleCustom);
	}

	/**
	 * Convert color hex to rgb
	 *
	 * @param   $colour  Color hex
	 *
	 * @return array|bool
	 */
	public static function hex2rgb($colour = "")
	{
		if ($colour[0] == '#')
		{
			$colour = substr($colour, 1);
		}
		if (strlen($colour) == 6)
		{
			list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
		}
		elseif (strlen($colour) == 3)
		{
			list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		}
		else
		{
			return false;
		}
		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return $r . "," . $g . "," . $b;
		//return array('red' => $r, 'green' => $g, 'blue' => $b);
	}

	/**
	 * Check state Form
	 *
	 * @param   int     $formId    Form Id
	 *
	 * @return bool
	 */
	public static function checkStateForm($formId)
	{
		$db = JFactory::getDBO();
		$db->setQuery($db->getQuery(true)->from('#__jsn_uniform_forms')->select('form_id')->where('form_state = 1 AND form_id=' . (int) $formId));
		if ($db->loadResult())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * generate HTML Pages
	 *
	 * @param   Object  $formId          Form Id
	 * @param   String  $formName        Form Name
	 * @param   String  $formType        Form Type
	 * @param   String  $topContent      Module Top content
	 * @param   String  $bottomContent   Module Bottom Content
	 * @param   String  $showTitle       State Show Title Form
	 * @param   String  $showDes         State Show Description Form
	 *
	 * @return string
	 */
	public static function generateHTMLPages($formId, $formName, $formType = "", $topContent = "", $bottomContent = "", $showTitle = false, $showDes = false)
	{
		$uri = JUri::getInstance();
		$baseUrl = JURI::base(true);
		$html = "";
		// Load language
		$lang = JFactory::getLanguage();
		$lang->load('com_uniform');
		$document = JFactory::getDocument();
		/** load Css  */
		$loadBootstrap = self::getDataConfig('load_bootstrap_css');
		$loadBootstrap = isset($loadBootstrap->value) ? $loadBootstrap->value : "0";
		$stylesheets = array();
		if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
		{
			$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery.ui.1.9.0.ie.css');
		}
		if ($loadBootstrap == 1)
		{
			$document->addStyleSheet(JSN_UNIFORM_ASSETS_URI . '/3rd-party/bootstrap/css/bootstrap.min.css');
		}
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css');
		//$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/font-awesome/css/font-awesome.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/tipsy.css');
		$document->addStyleSheet(JSN_UNIFORM_ASSETS_URI . '/css/form.css');

		/** end  */
		/* Get data form */
		$db = JFactory::getDBO();
		$db->setQuery($db->getQuery(true)->from('#__jsn_uniform_forms')->select('*')->where('form_state = 1 AND form_id=' . (int) $formId));
		$items = $db->loadObject();
		/* Get data page form */
		$db->setQuery($db->getQuery(true)->from('#__jsn_uniform_form_pages')->select('*')->where('form_id=' . (int) $formId)->order("page_id ASC"));
		$formPages = $db->loadObjectList();
		/* define language */
		$arrayTranslated = array('JSN_UNIFORM_CHARACTERS','JSN_UNIFORM_WORDS','JSN_UNIFORM_CONFIRM_FIELD_PASSWORD_MIN_MAX_CHARACTER', 'JSN_UNIFORM_CONFIRM_FIELD_EMAIL_CONFIRM', 'JSN_UNIFORM_CONFIRM_FIELD_MIN_NUMBER', 'JSN_UNIFORM_CONFIRM_FIELD_MAX_NUMBER', 'JSN_UNIFORM_DATE_HOUR_TEXT', 'JSN_UNIFORM_DATE_MINUTE_TEXT', 'JSN_UNIFORM_DATE_CLOSE_TEXT', 'JSN_UNIFORM_DATE_PREV_TEXT', 'JSN_UNIFORM_DATE_NEXT_TEXT', 'JSN_UNIFORM_DATE_CURRENT_TEXT', 'JSN_UNIFORM_DATE_MONTH_JANUARY', 'JSN_UNIFORM_DATE_MONTH_FEBRUARY', 'JSN_UNIFORM_DATE_MONTH_MARCH', 'JSN_UNIFORM_DATE_MONTH_APRIL', 'JSN_UNIFORM_DATE_MONTH_MAY', 'JSN_UNIFORM_DATE_MONTH_JUNE', 'JSN_UNIFORM_DATE_MONTH_JULY', 'JSN_UNIFORM_DATE_MONTH_AUGUST', 'JSN_UNIFORM_DATE_MONTH_SEPTEMBER', 'JSN_UNIFORM_DATE_MONTH_OCTOBER', 'JSN_UNIFORM_DATE_MONTH_NOVEMBER', 'JSN_UNIFORM_DATE_MONTH_DECEMBER', 'JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT', 'JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT', 'JSN_UNIFORM_DATE_MONTH_MARCH_SHORT', 'JSN_UNIFORM_DATE_MONTH_APRIL_SHORT', 'JSN_UNIFORM_DATE_MONTH_MAY_SHORT', 'JSN_UNIFORM_DATE_MONTH_JUNE_SHORT', 'JSN_UNIFORM_DATE_MONTH_JULY_SHORT', 'JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT', 'JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT', 'JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT', 'JSN_UNIFORM_DATE_DAY_SUNDAY', 'JSN_UNIFORM_DATE_DAY_MONDAY', 'JSN_UNIFORM_DATE_DAY_TUESDAY', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY', 'JSN_UNIFORM_DATE_DAY_THURSDAY', 'JSN_UNIFORM_DATE_DAY_FRIDAY', 'JSN_UNIFORM_DATE_DAY_SATURDAY', 'JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_MONDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT', 'JSN_UNIFORM_DATE_DAY_SUNDAY_MIN', 'JSN_UNIFORM_DATE_DAY_MONDAY_MIN', 'JSN_UNIFORM_DATE_DAY_TUESDAY_MIN', 'JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN', 'JSN_UNIFORM_DATE_DAY_THURSDAY_MIN', 'JSN_UNIFORM_DATE_DAY_FRIDAY_MIN', 'JSN_UNIFORM_DATE_DAY_SATURDAY_MIN', 'JSN_UNIFORM_DATE_DAY_WEEK_HEADER', 'JSN_UNIFORM_CONFIRM_FIELD_MAX_LENGTH', 'JSN_UNIFORM_CONFIRM_FIELD_MIN_LENGTH', 'JSN_UNIFORM_CAPTCHA_PUBLICKEY', 'JSN_UNIFORM_BUTTON_BACK', 'JSN_UNIFORM_BUTTON_NEXT', 'JSN_UNIFORM_BUTTON_RESET', 'JSN_UNIFORM_BUTTON_SUBMIT', 'JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY', 'JSN_UNIFORM_CONFIRM_FIELD_INVALID');
		/* Check load JS */
		$checkLoadJS = array();
		$checkLoadJSTipsy = false;

		if ($items)
		{
			$formStyleCustom = new stdClass;
			if (!empty($items->form_style))
			{
				$formStyleCustom = json_decode($items->form_style);
			}
			$dataSumbission = '';
			$classForm = !empty($formStyleCustom->layout) ? $formStyleCustom->layout : '';
			$formTheme = !empty($formStyleCustom->theme) ? $formStyleCustom->theme : '';
			$uri = JURI::getInstance();
			$url = $uri->toString( array( 'scheme', 'host', 'port' ) ) . JURI::root(true);
			if (!$formType)
			{
				if ($showTitle && !empty($items->form_title))
				{
					$html .= "<h2 class='contentheading'>{$items->form_title}</h2>";
				}
				if ($showDes && !empty($items->form_description))
				{
					$des = str_replace("\n", "<br/>", $items->form_description);
					$html .= "<p>{$des}</p>";
				}
				$document->addStyleSheet(JRoute::_('index.php?option=com_uniform&view=form&task=generateStylePages&form_id=' . $items->form_id));
				$html .= "<div class=\"jsn-uniform jsn-master\" data-form-name='" . $formName . "' id='jsn_form_" . $items->form_id . "'><div class=\"jsn-bootstrap\">";
				$html .= $topContent;
				$html .= "<form name='form_{$formName}' id='form_{$formName}' action=\"" . $url . '/index.php?option=com_uniform&view=form&task=form.save&form_id=' . $items->form_id . "\" method=\"post\" class=\"form-validate {$classForm} \" enctype=\"multipart/form-data\" >";
				$html .= "<span class=\"hide jsn-language\" style=\"display:none;\" data-value='" . json_encode(JSNUniformHelper::getTranslated($arrayTranslated)) . "'></span>";
				$html .= "<span class=\"hide jsn-base-url\" style=\"display:none;\" data-value=\"" . $url . "\"></span>";
				$html .= "<div id=\"page-loading\" class=\"jsn-bgloading\"><i class=\"jsn-icon32 jsn-icon-loading\"></i></div>";
				$html .= "<div class=\"jsn-row-container {$formTheme}\">";
			}
			$html .= "<div class=\"message-uniform\"> </div>";
			foreach ($formPages as $i => $contentForm)
			{
				$pageContainer = !empty($contentForm->page_container) && json_decode($contentForm->page_container) ? $contentForm->page_container : '[[{"columnName":"left","columnClass":"span12"}]]';
				$formContent = isset($contentForm->page_content) ? json_decode($contentForm->page_content) : "";
				$htmlForm = "";
				if (!empty($formContent))
				{
					foreach ($formContent as $content)
					{
						if (!empty($content->instruction))
						{
							$checkLoadJSTipsy = true;
						}
						if (!empty($content->type))
						{
							$checkLoadJS[$content->type] = $content->type;
						}
					}
					$htmlForm .= JSNFormGenerateHelper::generate($formContent, $dataSumbission, $pageContainer);
				}
				$html .= "<div data-value=\"{$contentForm->page_id}\" class=\"jsn-form-content hide\">{$htmlForm}";
				if ($i + 1 == count($formPages))
				{
					if (!empty($items->form_captcha) && $items->form_captcha == 1)
					{

						if ($uri->getScheme() == 'https')
						{
							$html .= "<script type=\"text/javascript\" src=\"https://www.google.com/recaptcha/api/js/recaptcha_ajax.js\"></script>";
						}
						else
						{
							$html .= "<script type=\"text/javascript\" src=\"http://www.google.com/recaptcha/api/js/recaptcha_ajax.js\"></script>";
						}
						$html .= "<div id=\"" . md5(date("Y-m-d H:i:s") . $i . $formName) . "\"  publickey=\"" . JSN_UNIFORM_CAPTCHA_PUBLICKEY . "\" class=\"form-captcha control-group\"></div>";
					}
					else if (!empty($items->form_captcha) && $items->form_captcha == 2)
					{
						require_once JPATH_ROOT . '/components/com_uniform/libraries/3rd-party/securimage/securimage.php';
						$img = new Securimage();
						$img->case_sensitive = true; // true to use case sensitve codes - not recommended
						$img->image_bg_color = new Securimage_Color("#ffffff"); // image background color
						$img->text_color = new Securimage_Color("#000000"); // captcha text color
						$img->num_lines = 0; // how many lines to draw over the image
						$img->line_color = new Securimage_Color("#0000CC"); // color of lines over the image
						$img->namespace = $formName;
						$img->signature_color = new Securimage_Color(rand(0, 64), rand(64, 128), rand(128, 255)); // random signature color
						ob_start();
						$img->show(JPATH_ROOT . '/components/com_uniform/libraries/3rd-party/securimage/backgrounds/bg4.png');
						$dataCaptcha = base64_encode(ob_get_clean());
						$html .= '<div class="control-group">
									<div class="controls">
									<div class="row-fluid"><img src="data:image/png;base64,' . $dataCaptcha . '" alt="CAPTCHA" /></div>
									<input type="text" id="jsn-captcha" name="captcha" autocomplete="off" placeholder="' . JText::_("JSN_UNIFORM_CAPTCHA") . '">
									</div>
									</div>';
					}
				}
				$html .= "</div>";
			}
			$formSettings = !empty($items->form_settings) ? json_decode($items->form_settings) : "";
			$btnNext = !empty($formSettings->form_btn_next_text) ? $formSettings->form_btn_next_text : "Next";
			$btnPrev = !empty($formSettings->form_btn_prev_text) ? $formSettings->form_btn_prev_text : "Prev";
			$btnSubmit = !empty($formSettings->form_btn_submit_text) ? $formSettings->form_btn_submit_text : "Submit";
			$btnReset = !empty($formSettings->form_btn_reset_text) ? $formSettings->form_btn_reset_text : "Reset";
			$btnNextStyle = !empty($formStyleCustom->button_next_color) ? $formStyleCustom->button_next_color : "btn  btn-primary";
			$btnPrevStyle = !empty($formStyleCustom->button_prev_color) ? $formStyleCustom->button_prev_color : "btn";
			$btnSubmitStyle = !empty($formStyleCustom->button_submit_color) ? $formStyleCustom->button_submit_color : "btn  btn-primary";
			$btnResetStyle = !empty($formStyleCustom->button_reset_color) ? $formStyleCustom->button_reset_color : "btn";
			$btnPosition = !empty($formStyleCustom->button_position) ? $formStyleCustom->button_position : "btn-toolbar";
			$htmlBtnReset = "";
			if (!empty($formSettings->form_state_btn_reset_text) && $formSettings->form_state_btn_reset_text == "Yes")
			{
				$htmlBtnReset = '<button class="' . $btnResetStyle . ' reset hide" onclick="return false;">' . JText::_($btnReset) . '</button>';
			}
			$html .= '<div class="form-actions">
									<div class="' . $btnPosition . '">
									    <button class="' . $btnPrevStyle . ' prev hide" onclick="return false;">' . JText::_($btnPrev) . '</button>
									    <button class="' . $btnNextStyle . ' next hide" onclick="return false;">' . JText::_($btnNext) . '</button>
									    ' . $htmlBtnReset . '
									    <button type="submit" class="' . $btnSubmitStyle . ' jsn-form-submit hide" >' . JText::_($btnSubmit) . '</button>
									</div>
								     </div>';
			$formId = isset($items->form_id) ? $items->form_id : "";
			$postAction = isset($items->form_post_action) ? $items->form_post_action : "";
			$postActionData = isset($items->form_post_action_data) ? $items->form_post_action_data : "";
			$edition = defined('JSN_UNIFORM_EDITION') ? JSN_UNIFORM_EDITION : "free";
			if (strtolower($edition) == "free")
			{
				$html .= "<div class=\"jsn-text-center\"><a href=\"http://www.joomlashine.com/joomla-extensions/jsn-uniform.html\" target=\"_blank\">" . JText::_('JSN_UNIFORM_POWERED_BY') . "</a> by <a href=\"http://www.joomlashine.com\" target=\"_blank\">JoomlaShine</a></div>";
			}
			$html .= "<input type=\"hidden\" name=\"form_name\" value=\"{$formName}\" />";
			if (!$formType)
			{
				$html .= "</div>";
				$html .= "<input type=\"hidden\" name=\"task\" value=\"form.save\" />";
				$html .= "<input type=\"hidden\" name=\"option\" value=\"com_uniform\" />";
				$html .= "<input type=\"hidden\" name=\"form_id\" value=\"{$formId}\" />";
				$html .= "<input type=\"hidden\" id=\"form_post_action\" name=\"form_post_action\" value=\"{$postAction}\" />";
				$html .= "<input type=\"hidden\" name=\"form_post_action_data\" value='" . htmlentities($postActionData, ENT_QUOTES, "UTF-8") . "' />";
				$html .= JHtml::_('form.token');
				$html .= "</form>";
				$html .= $bottomContent;
				$html .= "</div></div>";
			}

			/* Load JS */
			if (!empty($checkLoadJS['date']))
			{
				$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
			}
			$getHeadData = JFactory::getDocument()->getHeadData();
			$checkLoadScript = true;
			$scripts = array();
			foreach ($getHeadData['scripts'] as $script => $option)
			{
				if ($script == JSN_UNIFORM_ASSETS_URI . '/js/form.js')
				{
					if (!empty($checkLoadJS['google-maps']) && empty($getHeadData['scripts']['https://maps.google.com/maps/api/js?sensor=false&libraries=places']))
					{
						if ($uri->getScheme() == 'https')
						{
							$scripts['https://maps.google.com/maps/api/js?sensor=false&libraries=places'] = $option;
						}
						else
						{
							$scripts['http://maps.google.com/maps/api/js?sensor=false&libraries=places'] = $option;
						}
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.js'] = $option;
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.services.js'] = $option;
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.extensions.js'] = $option;
					}
					if (!empty($checkLoadJS['date']) && empty($getHeadData['scripts'][JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-timepicker-addon.js']))
					{
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-1.10.3.custom.min.js'] = $option;
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-timepicker-addon.js'] = $option;
					}
					if ($checkLoadJSTipsy && empty($getHeadData['scripts'][JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/jquery.tipsy.js']))
					{
						$scripts[JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/jquery.tipsy.js'] = $option;
					}
					$scripts[$script] = $option;
					$checkLoadScript = false;
				}
				else
				{
					$scripts[$script] = $option;
				}
				if ($script == JSN_UNIFORM_ASSETS_URI . '/js/submissions.js' || $script == JSN_UNIFORM_ASSETS_URI . '/js/submission.js')
				{
					$scripts[JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-scrollto/jquery.scrollTo.js'] = $option;
					$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery.placeholder.js'] = $option;
					$scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js'] = $option;
					if (!empty($checkLoadJS['google-maps']))
					{
						if ($uri->getScheme() == 'https')
						{
							$scripts['https://maps.google.com/maps/api/js?sensor=false&libraries=places'] = $option;
						}
						else
						{
							$scripts['http://maps.google.com/maps/api/js?sensor=false&libraries=places'] = $option;
						}
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.js'] = $option;
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.services.js'] = $option;
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.extensions.js'] = $option;
					}
					if (!empty($checkLoadJS['date']))
					{
						$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-timepicker-addon.js'] = $option;
					}
					$scripts[JSN_UNIFORM_ASSETS_URI . '/js/form.js'] = $option;
					$checkLoadScript = false;
				}
			}
			if ($checkLoadScript)
			{
				$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/jsn_uf_jquery_safe.js');
				$document->addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
				$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery.placeholder.js');

				$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/json-2.3.min.js');
				if ($checkLoadJSTipsy)
				{
					$document->addScript(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/jquery.tipsy.js');
				}
				$document->addScript(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-scrollto/jquery.scrollTo.js');
				$document->addScript(JURI::root(true) . '/media/jui/js/bootstrap.min.js');
				if (!empty($checkLoadJS['date']))
				{
					$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-1.10.3.custom.min.js');
					$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-timepicker-addon.js');
				}
				if (!empty($checkLoadJS['google-maps']))
				{
					if ($uri->getScheme() == 'https')
					{
						$document->addScript('https://maps.google.com/maps/api/js?sensor=false&libraries=places');
					}
					else
					{
						$document->addScript('http://maps.google.com/maps/api/js?sensor=false&libraries=places');
					}
					$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.js');
					$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.services.js');
					$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.extensions.js');
				}
				$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/form.js');
				$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/jsn_uf_conflict.js');
			}
			else if (!empty($scripts))
			{
				$getHeadData['scripts'] = $scripts;
				JFactory::getDocument()->setHeadData($getHeadData);
			}
		}
		return $html;
	}

	/**
	 * Method to get text translation.
	 *
	 * @param   array  $strings  String to translate.
	 *
	 * @return  array
	 */
	public static function getTranslated($strings)
	{
		$translated = array();

		foreach ($strings AS $string)
		{
			$translated[strtoupper($string)] = str_replace("'", "&apos;", JText::_($string));
		}

		return $translated;
	}

	/**
	 * checkEditSubmission
	 *
	 * @param $userId
	 *
	 * @param $groupCheck
	 *
	 * @return bool
	 */
	public static function checkEditSubmission($userId, $groupCheck)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__user_usergroup_map');
		$query->where('user_id=' . $db->Quote($userId), "AND");
		$query->where('group_id=' . $db->Quote($groupCheck), "AND");
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if (count($items))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Render Options Button Style
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function renderOptionsButtonStyle($defaultValue)
	{
		$list = array();
		$list['btn'] = "Default";
		$list['btn btn-primary'] = "Primary";
		$list['btn btn-info'] = "Info";
		$list['btn btn-success'] = "Success";
		$list['btn btn-warning'] = "Warning";
		$list['btn btn-danger'] = "Danger";
		$list['btn btn-inverse'] = "Inverse";
		$list['btn btn-link'] = "Link";
		$options = '';

		foreach ($list as $key => $value)
		{
			$selected = "";
			if ($key == $defaultValue)
			{
				$selected = "selected='selected'";
			}
			$options .= "<option {$selected} value='{$key}'>{$value}</option>";
		}
		return $options;
	}

	/**
	 * Render Options Button Position
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function renderOptionsButtonPosition($defaultValue)
	{
		$list = array();
		$list['btn-toolbar'] = "Center";
		$list['btn-toolbar pull-left'] = "Left";
		$list['btn-toolbar pull-right'] = "Right";
		$options = '';
		foreach ($list as $key => $value)
		{
			$selected = "";
			if ($key == $defaultValue)
			{
				$selected = "selected='selected'";
			}
			$options .= "<option {$selected} value='{$key}'>{$value}</option>";
		}
		return $options;
	}
}
