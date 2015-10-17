<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Submission
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');


jimport('joomla.application.component.view');
jimport('joomla.application.helper');

/**
 * View class for a list of Submission.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewSubmission extends JSNBaseView
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
	public function display($tpl = null)
	{
		$this->_item = $this->get('Item');

		if (JSNUniformHelper::checkStateForm($this->_item->form_id))
		{
			$menu = JFactory::getApplication()->getMenu();
			$this->_input = JFactory::getApplication()->input;
			$menuItem = $menu->getItem((int) $this->_input->get("Itemid"));
			$this->_params = json_decode($menuItem->params);

			$this->_infoForm = $this->get('InfoForm');
			$dataContentForm = $this->get('FormPages');
			$this->nextAndPreviousForm = $this->get('NextAndPreviousForm');
			$this->_formPages = $dataContentForm;
			$this->_dataSubmission = $this->get('DataSubmission');
			$this->_dataFields = $this->get('DataFields');
			// Display the template
			parent::display($tpl);
			$this->addAssets();
		}
	}

	/**
	 * Add the libraries css and javascript
	 *
	 * @return void
	 *
	 * @since        1.6
	 */
	protected function addAssets()
	{
		$uri = JUri::getInstance();
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
		//$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/font-awesome/css/font-awesome.css');
		$document->addStyleSheet(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/tipsy.css');
		$document->addStyleSheet(JSN_UNIFORM_ASSETS_URI . '/css/form.css');
		/** end  */
		/** Load Js */
		$document->addScriptDeclaration('var nextAndPreviousForm =' . json_encode($this->nextAndPreviousForm));
		$getHeadData = JFactory::getDocument()->getHeadData();
		$checkLoadScript = true;
		$scripts = array();
		foreach ($getHeadData['scripts'] as $script => $option)
		{
			$scripts[$script] = $option;
			if ($script == JSN_UNIFORM_ASSETS_URI . '/js/form.js' || $script == JSN_UNIFORM_ASSETS_URI . '/js/submission.js')
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
				$scripts[JSN_UNIFORM_ASSETS_URI . '/js/submission.js'] = $option;
				$checkLoadScript = false;
			}
		}
		if ($checkLoadScript)
		{
			if ($uri->getScheme() == 'https')
			{
				$document->addScript('https://maps.google.com/maps/api/js?sensor=false&libraries=places');
			}
			else
			{
				$document->addScript('http://maps.google.com/maps/api/js?sensor=false&libraries=places');
			}
			$document->addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/jquery-ui-1.10.3.custom.min.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/json-2.3.min.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.services.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/libs/googlemaps/jquery.ui.map.extensions.js');
			$document->addScript(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/jquery.tipsy.js');
			$document->addScript(JSN_UNIFORM_ASSETS_URI . '/js/submission.js');
		}
		else if (!empty($scripts))
		{
			$getHeadData['scripts'] = $scripts;
			JFactory::getDocument()->setHeadData($getHeadData);
		}
	}
}
