<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  View
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');


// import Joomla view library
jimport('joomla.application.component.view');

/**
 * View class for a list of Form.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewSubmissions extends JSNBaseView
{

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
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$this->_formId = $params->get('form_id');

		if ($this->_formId && JSNUniformHelper::checkStateForm($this->_formId))
		{
			$this->_state = $this->get('State');
			$this->_items = $this->get('Items');
			$this->_pagination = $this->get('Pagination');
			$this->_fieldsForm = $this->get('FieldsForm');
			$this->_fieldView = $params->get('form_field');
			if(!empty($this->_fieldView)){
				$this->_fieldView = json_decode($this->_fieldView);
			}
			$this->_viewField = $this->getViewField();
			//var_dump($this->_viewField);
			// Display the view
			parent::display($tpl);
			$this->addAssets();
		}
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 */
	protected function addAssets()
	{
		$document = JFactory::getDocument();
		/** load Css  */
		$loadBootstrap = JSNUniformHelper::getDataConfig('load_bootstrap_css');
		$loadBootstrap = isset($loadBootstrap->value) ? $loadBootstrap->value : "0";
		$stylesheets = array();
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
		if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
		{
			$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery.ui.1.9.0.ie.css');
		}
		if ($loadBootstrap == 1)
		{
			$document->addStyleSheet(JSN_UNIFORM_ASSETS_URI . '/3rd-party/bootstrap/css/bootstrap.min.css');
		}
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css');
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_uniform/assets/js/libs/daterangepicker/daterangepicker-bs2.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/tipsy.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-daterangepicker/css/ui.daterangepicker.css');
		$document->addStyleSheet(JSN_UNIFORM_ASSETS_URI . '/css/form.css');
		/** end  */
		/** Load Js */
		$getHeadData = JFactory::getDocument()->getHeadData();
		$checkLoadScript = true;
		$scripts = array();
		foreach ($getHeadData['scripts'] as $script => $option)
		{
			$scripts[$script] = $option;
			if ($script == JSN_UNIFORM_ASSETS_URI . '/js/form.js' || $script == JSN_UNIFORM_ASSETS_URI . '/js/submission.js')
			{
				$scripts[JSN_UNIFORM_ASSETS_URI . '/js/libs/daterangepicker.jQuery.compressed.js'] = $option;
				$scripts[JSN_UNIFORM_ASSETS_URI . '/js/submissions.js'] = $option;
				$checkLoadScript = false;
			}
		}
		if ($checkLoadScript)
		{
			$document->addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-1.10.3.custom.min.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/json-2.3.min.js');
			$document->addScript(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/jquery.tipsy.js');
			$document->addScript(JURI::root(true) . '/administrator/components/com_uniform/assets/js/libs/daterangepicker/daterangepicker.js');
			$document->addScript(JURI::root(true) . '/administrator/components/com_uniform/assets/js/libs/daterangepicker/moment.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/submissions.js');
		}
		else if (!empty($scripts))
		{
			$getHeadData['scripts'] = $scripts;
			JFactory::getDocument()->setHeadData($getHeadData);
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
		$listViewField = $this->escape($this->_state->get('filter.list_view_field' . $this->_formId));
		$listViewField = (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($listViewField) : $listViewField;
		$positionField = $this->escape($this->_state->get('filter.position_field' . $this->_formId));
		$configGetPosition = (object) $this->_fieldView;

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

			if (!in_array($fieldsData->Field, array('submission_data_id', 'form_id', 'user_id', 'submission_state', 'submission_country_code', 'submission_browser_version', 'submission_browser_agent')))
			{
				$resultFields['identifier'][] = $fieldsData->Field;
				$resultFields['title'][] = 'JSN_UNIFORM_' . strtoupper($fieldsData->Field);
				$resultFields['sort'][] = 'sb.' . $fieldsData->Field;
				$resultFields['type'][$fieldsData->Field] = $fieldsData->Type;
				$resultFields['styleclass'][] = "field";
			}
		}
		if ($positionField)
		{
			$positionField = explode(",", $positionField);
		}
		elseif (!empty($configGetPosition->field_identifier) && $configGetPosition && $configGetPosition->field_identifier)
		{
			$positionField = array_merge($configGetPosition->field_identifier, $resultFields['identifier']);
			$positionField = array_unique($positionField);
		}
		if (!$listViewField && $configGetPosition)
		{
			$listViewField = !empty($configGetPosition->field_view) ? implode(",", $configGetPosition->field_view) : "";
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
		//JSNUniformHelper::setPositionFields($this->_state->get('filter.filter_form_id'), $result);
		return $result;
	}
}
