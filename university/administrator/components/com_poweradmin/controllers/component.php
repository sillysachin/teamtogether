<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: component.php 12645 2012-05-14 07:45:58Z binhpt $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');
jimport('joomla.registry.registry');
error_reporting(0);
/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin
 * @since		1.7
 */
class PoweradminControllerComponent extends JControllerForm
{
	/**
	 *
	 * Ajax request set/get data
	 */
	public function request()
	{

		$data = new JRegistry();
		$dataFromRequest = JRequest::getVar('data', '');

		$data->loadObject(json_decode($dataFromRequest));
		if ( $data->get('requestTask', '') == 'brankNewData' ){
			JSNFactory::localimport('libraries.joomlashine.mode.rawmode');
			$jsnrawmode  = JSNRawmode::getInstance( $data->toArray() );
			$jsnrawmode->renderComponent();
			echo $jsnrawmode->getHTML('component');
			jexit();
		}

		$params = $data->get('params', Array());
		if ( is_object($params) ){
			$params = (array) $params;
		}
		if ( $data->get('prefix_params', false) ){
			$prefixId = 0;
			$_params  = Array();
			foreach($params as $key => $val){
				$suffixs = explode('_', $key);
				$number = (int) $suffixs[count($suffixs)-1];
				if (!$prefixId){
					$prefixId = $number;
				}
				$_params[str_replace('_'.$number, '', $key)] = $val;
			}
			$params = $_params;
		}

		$jsnConfig = JSNFactory::getConfig();

		// Execute saveParams event if option is supported ext
		JSNPaExtensionsHelper::executeExtMethod(str_ireplace('com_', '', $data->get('option')), 'saveParams', array(
																												'data' => $data,
																												'jsnConfig' => $jsnConfig,
																												'params' => $params
																												)
											);

		switch ( $data->get('requestType', 'only') )
		{
			case 'only':
				$jsnConfig->menuitem( $data->get('Itemid', ''), $params );
				break;
			case 'globally':
				//Set global config
				$jsnConfig->extension( $data->get('option', ''), $params );

				foreach( $params as $k => $param ){
					$params[$k] = '';
				}
				//Set for menu article layout
				$allMenuitems = $this->getModel('menuitem')->getAllItems(
						array(
								'option' => $data->get('option', $data->get('option')),
								'view'   => $data->get('view', 'article'),
								'layout' => $data->get('layout', '')
						)
				);

				foreach( $allMenuitems as $item ){

					$jsnConfig->menuitem( $item->id, $params );
				}
				break;
		}
		jexit('success');
	}
	/**
	 *
	 * Custom page save setting
	 */
	public function custompageSave()
	{
		$app       = JFactory::getApplication();
		$saveTypes  = JRequest::getVar('saveTypes', array(), 'post', 'array');
		$layout    = JRequest::getVar('layout', 'readmore_settings');
		$JSNConfig = JSNFactory::getConfig();

		foreach ($_POST as $key => $value){
			$saveType = @$saveTypes[$key];

			switch ( $saveType )
			{
				case 'only':
					$JSNConfig->menuitem( $app->getUserState('com_poweradmin.component.menuid', 0), array($key => $value) );
					break;

				case 'globally':
				default:
					//Set global config
					$JSNConfig->extension( $app->getUserState('com_poweradmin.component.request_from_extension', ''), array($key => $value) );

					//Set for menu article layout
					$allMenuitems = $this->getModel('menuitem')->getAllItems(
						array(
							'option' => $app->getUserState('com_poweradmin.component.request_from_extension', ''),
							'view'   => $app->getUserState('com_poweradmin.component.request_from_view', ''),
							'layout' => $app->getUserState('com_poweradmin.component.request_from_layout', '')
						)
					);
					foreach( $allMenuitems as $item ){
						$JSNConfig->menuitem( $item->id, array($key => '') );
					}
					break;
			}

		}
		//redirect to current layout
		$this->setRedirect('index.php?option=com_poweradmin&view=component&layout='.$layout.'&tmpl=component');
		$this->redirect();
	}
	/**
	 *
	 * Redirect function to content layout setting
	 */
	public function redirect_setting()
	{
		$app    = JFactory::getApplication();
		$option = JRequest::getVar('request_from_extension', '');
		$view   = JRequest::getVar('request_from_view', '');
		$layout = JRequest::getVar('request_from_layout', '');
		$menuid = JRequest::getVar('menuid', 0, 'int');
		$app->setUserState('com_poweradmin.component.request_from_extension', $option);
		$app->setUserState('com_poweradmin.component.request_from_view', $view);
		$app->setUserState('com_poweradmin.component.request_from_layout', $layout);
		$app->setUserState('com_poweradmin.component.menuid', $menuid);

		$layout_setting = JRequest::getVar('layout_setting', 'set_sub_categories');
		$this->setRedirect('index.php?option=com_poweradmin&view=component&layout='.$layout_setting.'&tmpl=component');
		$this->redirect();
	}
	/**
	 *
	 * Check an file exists on server
	 */
	public function checkScript()
	{
		error_reporting(0);
		$scriptFolder = str_replace("com_", '', JString::strtolower(JRequest::getCmd('scriptFolder', ''))) ;
		$scriptName   = JString::strtolower(JRequest::getCmd('scriptName', ''));
		$scriptFile   = JPATH_ROOT . '/plugins/jsnpoweradmin/' . $scriptFolder . '/assets/js/' .  $scriptName .'.js';
		if ( JFile::exists($scriptFile) ){
			$found = JURI::root() . '/plugins/jsnpoweradmin/' . $scriptFolder . '/assets/js/' .  $scriptName .'.js';
		}else{
			$found = 'Not Found';
		}
		jexit($found);
	}
}