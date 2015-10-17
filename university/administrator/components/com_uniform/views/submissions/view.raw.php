<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Submissions
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of Submissions.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewSubmissions extends JSNBaseView
{

	protected $_items;
	protected $_pagination;
	protected $_state;
	protected $_document;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	function display($tpl = null)
	{
		$this->_document = JFactory::getDocument();
		$this->_state = $this->get('State');
		$this->_formId = 0;
		if ($this->_state->get('filter.filter_form_id') != 0)
		{
			$this->_dataExport = $this->get('Export');
			$this->_infoForm = $this->get('InfoForm');
			$this->_pagination = $this->get('Pagination');
			$this->_viewField = $this->getViewField();
			$this->_formId = $this->_state->get('filter.filter_form_id');
			$edition = defined('JSN_UNIFORM_EDITION') ? strtolower(JSN_UNIFORM_EDITION) : "free";
			if ($edition == "free")
			{
				$this->_countSubmission = $this->get('CountSubmission');
				$countSubmission = 300 - $this->_countSubmission > 0 ? 300 - $this->_countSubmission : 0;
				$msg = JText::sprintf('JSN_UNIFORM_YOU_CAN_ONLY_ACCEPT_UP_TO_300_SUBMISSION', (int) $countSubmission) . ' <a class="jsn-link-action" href="index.php?option=com_uniform&view=upgrade">' . JText::_("JSN_UNIFORM_UPGRADE_EDITION") . '</a>';
				if ($this->_countSubmission <= 300)
				{
					JFactory::getApplication()->enqueueMessage($msg);
				}
				else
				{
					JError::raiseNotice(100, $msg);
				}
			}
		}

		$config = JSNConfigHelper::get();

		// Get messages
		$msgs = '';
		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('SUBMISSIONS');
			$msgs = count($msgs) ? JSNUtilsMessage::showMessages($msgs) : '';
		}

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);


		// Initialize toolbar
		$this->initToolbar();

		// Load assets
		JSNUniformHelper::addAssets();
		//$this->addAssets();

		// Display the template
		parent::display($tpl);
	}


	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 */
	protected function addAssets()
	{
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-daterangepicker/css/ui.daterangepicker.css');
		echo JSNHtmlAsset::loadScript('uniform/submissions', array(), true);
		JSNHtmlAsset::addStyle('http://fonts.googleapis.com/css?family=Chau+Philomene+One');
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since        1.6
	 */
	protected function initToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');

		if (!empty($this->_formId))
		{
			$bar->appendButton('Custom', '<ul class="jsn-menu clearafter">
					 <li class="menu-name"><a href="#" onclick="return false;"><span class="icon-32-save icon-menu"></span>' . JText::_('JSN_UNIFORM_EXPORT') . '</a>
					    <ul class="jsn-submenu">
							<li class="parent primary first"><a target="_blank" href="' . JRoute::_('index.php?option=com_uniform&view=submissions&task=submissions.export_excel&form_id=' . $this->_formId) . '"><span class="jsn-icon24 jsn-icon-joomla jsn-icon-component"></span>' . JText::_('JSN_UNIFORM_EXPORT_TO_EXCEL') . '</a></li>
							<li class="parent primary"><a target="_blank" href="' . JRoute::_('index.php?option=com_uniform&view=submissions&task=submissions.export_cvs&form_id=' . $this->_formId) . '"><span class="jsn-icon24 jsn-icon-joomla jsn-icon-module"></span>' . JText::_('JSN_UNIFORM_EXPORT_TO_CVS') . '</a></li>
						</ul>
					  </li>
					</ul>'
			);
			JToolBarHelper::deleteList('JSN_UNIFROM_CONFIRM_DELETE', 'submissions.delete', 'JTOOLBAR_DELETE');
			JSNUniformHelper::initToolbar('JSN_UNIFORM_SUBMISSIONS_MANAGER', 'uniform-submission');
		}
	}

	/**
	 * get field select view
	 *
	 * @return array
	 */
	public function getViewField()
	{
		$resultFields = array();
		$positionField = "";
		$listViewField = $this->escape($this->_state->get('filter.list_view_field'));
		$listViewField = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($listViewField) : $listViewField;
		$positionField = $this->escape($this->_state->get('filter.position_field'));
		$configGetPosition = JSNUniformHelper::getPositionFields($this->_state->get('filter.filter_form_id'));
		//$fieldsForms       = $this->get('FieldsForm');
		$fieldsDatas = JSNUniformHelper::getFormData();
		$fieldsForms = array();
		$dataPages = $this->get('DataPages');
		foreach ($dataPages as $index => $page)
		{
			$pageContent = isset($page->page_content) ? json_decode($page->page_content) : "";
			foreach ($pageContent as $itemPage)
			{

				if (!empty($itemPage->id))
				{
					$fieldsForms[] = $itemPage;
				}
			}
		}
		foreach ($fieldsForms as $fieldsForm)
		{

			if (isset($fieldsForm->type) && $fieldsForm->type != 'static-content' && $fieldsForm->type != 'google-maps')
			{
				$resultFields['identifier'][] = 'sd_' . $fieldsForm->id;
				$resultFields['title'][] = $fieldsForm->label;
				$resultFields['type']['sd_' . $fieldsForm->id] = $fieldsForm->type;
				$resultFields['sort'][] = 'sd.sd_' . $fieldsForm->id;
				$resultFields['styleclass'][] = "field";
			}
		}
		foreach ($fieldsDatas as $fieldsData)
		{

			if (!in_array($fieldsData->Field, array('submission_data_id', 'submission_id', 'form_id', 'user_id', 'submission_state', 'submission_country_code', 'submission_browser_version', 'submission_browser_agent')))
			{
				$resultFields['identifier'][] = $fieldsData->Field;
				$resultFields['title'][] = 'JSN_UNIFORM_' . strtoupper($fieldsData->Field);
				$resultFields['sort'][] = 'sb.' . $fieldsData->Field;
				$resultFields['type'][$fieldsData->Field] = $fieldsData->Type;
				$resultFields['styleclass'][] = "field";
			}
		}
		if ($configGetPosition)
		{
			$configGetPosition = json_decode($configGetPosition->value);
		}
		if ($positionField)
		{
			$positionField = explode(",", $positionField);
		}
		elseif ($configGetPosition && $configGetPosition->identifier)
		{
			$positionField = array_merge($configGetPosition->identifier, $resultFields['identifier']);
			$positionField = array_unique($positionField);
		}
		if (!$listViewField && $configGetPosition)
		{
			$listViewField = $configGetPosition->field_view;
		}
		if (!$listViewField)
		{
			$check = true;
			$i = 0;
			while ($check)
			{
				$j = 0;
				foreach ($resultFields['type'] as $rField)
				{
					if (isset($rField) && $rField != 'static-content' && $rField != 'google-maps')
					{
						if (strpos($resultFields['identifier'][$j], "sd_") !== false)
						{
							$listViewField[] = '&quot;' . $resultFields['identifier'][$j] . '&quot;';
						}
						if ($j == 2)
						{
							$listViewField[] = '&quot;submission_country&quot;';
							$listViewField[] = '&quot;submission_created_by&quot;';
							$listViewField[] = '&quot;submission_created_at&quot;';
							$listViewField = implode(",", $listViewField);
							$check = false;
							break;
						}
					}
					$j++;
				}
				if ($i == 20)
				{
					$check = false;
				}
				$i++;
			}
		}
		if (!empty($positionField))
		{

			$resultPositionFields = array();
			foreach ($positionField as $pField)
			{
				for ($i = 0; $i < count($resultFields['identifier']); $i++)
				{

					if ($pField == $resultFields['identifier'][$i] && $resultFields['type'][$resultFields['identifier'][$i]] != 'static-content' && $resultFields['type'][$resultFields['identifier'][$i]] != 'google-maps')
					{
						$resultPositionFields['identifier'][] = $resultFields['identifier'][$i];
						$resultPositionFields['title'][] = $resultFields['title'][$i];
						$resultPositionFields['sort'][] = $resultFields['sort'][$i];
						$resultPositionFields['styleclass'][] = $resultFields['styleclass'][$i];
						$resultPositionFields['type'][$resultFields['identifier'][$i]] = $resultFields['type'][$resultFields['identifier'][$i]];
					}
				}
			}
			$result = array('fields' => $resultPositionFields, 'field_view' => $listViewField);
		}
		else
		{
			$result = array('fields' => $resultFields, 'field_view' => $listViewField);
		}
		JSNUniformHelper::setPositionFields($this->_state->get('filter.filter_form_id'), $result);
		return $result;
	}
}
