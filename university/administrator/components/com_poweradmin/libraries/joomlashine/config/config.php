<?php
/**
 * @version     $Id$
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.registry.registry');

class JSNConfig{
	/**
	 *
	 * Return global JSNConfig object
	 *
	 * @return: Object
	 */
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances['JSNConfig'])) {
			$instance	= new JSNConfig();
			$instances['JSNConfig'] = &$instance;
		}

		return $instances['JSNConfig'];
	}
	/**
	 *
	 * This function to helper save configs for the component
	 *
	 * @param: (Number) ($componentId) is id of component in extension table
	 * @param: (string) ($componentName) is name of component
	 * @param: (Array) ($config) is array store key and value to save
	 * @return: Save to the database table
	 */
	public static function extension( $extension_name, $configs = Array() )
	{
		// Import files used for Joomla 3.2
		JSNFactory::import('plugins.system.jsnframework.libraries.joomlashine.version.version', 'site');
		// Import files for Joomla 3.2
		if (JSNVersion::isJoomlaCompatible('3.2'))
		{
			JSNFactory::import('components.com_config.model.cms', 'site');
			JSNFactory::import('components.com_config.model.form', 'site');
		}
		
		$extension_name = JString::strtolower( JString::trim( $extension_name ) );
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("params");
		$query->from("#__extensions");
		$query->where("name = " . $db->quote( $extension_name ));
		$db->setQuery($query);
		$paramsString = $db->loadResult();
		if ( !empty($paramsString) ){
			$jParams =  new JRegistry();
			//$jParams = new JParameter();
			$jParams->loadObject(json_decode($paramsString));
			$params = $jParams->toArray();
			foreach( $configs as $k => $val ){
				$params[$k] = (string) $val;
			}
			$query->clear();
			$query->select("extension_id");
			$query->from("#__extensions");
			$query->where("name=".$db->quote(JString::strtolower($extension_name)));
			$db->setQuery($query);
			$ext_id = $db->loadResult();
			JSNFactory::import('components.com_config.models.component');
			$config = new ConfigModelComponent();
			$config->save(Array(
				'params' => $params,
				'id'     => $ext_id,
				'option' => $extension_name
			));
			return true;
		}
		return false;
	}
	/**
	 *
	 * This function to helper save config for article
	 *
	 * @param: (Number) ($articleId) is id of article in content table
	 * @param: (Array) ($options) is array setting need to change the config
	 * @return: Save to table of database
	 */
	public static function article( $aId, $options )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("attribs");
		$query->from("#__content");
		$query->where("id = " . $db->quote( $aId ));
		$db->setQuery($query);
		$paramsString = $db->loadResult();
		if ( !empty($paramsString) ){
			if ( !class_exists('JRegistry') ){
				JSNFactory::import('libraries.joomla.registry.registry', 'site');
			}
			$jParams = new JRegistry();
			$jParams->loadObject(json_decode($paramsString));
			$params = $jParams->toArray();
			foreach( $options as $k => $val ){
				$params[$k] = (string) $val;
			}
			$jParams->loadArray( $params );
			$query->clear();
			$query->update("#__content");
			$query->set("attribs = " . $db->quote( $jParams->toString('JSON'), false ) );
			$query->where("id = " . $db->quote( $aId ));
			$db->setQuery($query);
			$db->query();
			return true;
		}
		return false;
	}
	/**
	 * Version J1.5
	 *
	 * This function to helper save config for menu item
	 *
	 * @param: (Number) ($mId) is id of menuitem in menu table
	 * @param: (Array) ($options) is array setting need to change the config
	 * @return: Save to table of database
	 */
	public static function menuitem( $mId, $options )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("params");
		$query->from("#__menu");
		$query->where("id = " . $db->quote( $mId ));
		$db->setQuery($query);
		$paramsString = $db->loadResult();
		if ( !empty($paramsString) ){
			$jParams =  new JRegistry();
			//$jParams = new JParameter();
			$jParams->loadObject(json_decode($paramsString));
			$params = $jParams->toArray();
			foreach( $options as $k => $val ){
				$params[$k] = (string) $val;
			}
			$jParams->loadArray( $params );
			$query->clear();
			$query->update("#__menu");
			$query->set("params = " . $db->quote( $jParams->toString('JSON'), false ) );
			$query->where("id = " . $db->quote( $mId ));
			$db->setQuery($query);
			$db->query();
			return true;
		}
		return false;
	}
	/**
	 *
	 * Get params of menu item
	 *
	 * @param Number $id
	 */
	public static function getMenuParams($id)
	{
		$menuSite = JMenu::getInstance('site');
		$Item = $menuSite->getItem($id);
		if ( isset($Item->params)){
			if ( $Item->params instanceof JRegistry ){
				return $Item->params;
			}
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("params");
		$query->from("#__menu");
		$query->where("id = " . $db->quote( $id ));
		$db->setQuery($query);
		$paramsString = $db->loadResult();
		$jParams = new JRegistry();
		$jParams->loadObject(json_decode($paramsString));
		return $jParams;
	}
	/**
	 *
	 * Megre menu params
	 *
	 * @param String $menuid
	 * @param JRegistry $params
	 */
	public static function &megreMenuParams($menuid, &$params, $itemAttribs = null, $component = "com_content")
	{
		if ( $params instanceof JRegistry){
			$menuParams 	= self::getMenuParams($menuid);
			if ( $menuParams instanceof JRegistry && $params instanceof JRegistry ){
				$arrs = $menuParams->toArray();
				$globalParams 	= JComponentHelper::getParams( $component );
				foreach($arrs as $key => $val){
					if ($val != ''){
						if( $val == 'use_article'){
							if($itemAttribs && isset($itemAttribs->{$key})){
								if( $itemAttribs->{$key} == null || $itemAttribs->{$key} == '' ){
									$val = $globalParams->get($key);
								}else{
									$val = $itemAttribs->{$key};
								}
							}
						}
						$params->set($key, $val);
					}
				}
			}
		}
		return $params;
	}
	/**
	 *
	 * Megre global params
	 *
	 * @param String $componentName
	 * @param JRegistry $params
	 */
	public static function megreGlobalParams( $componentName, &$params, $forcheck = false )
	{
		if ( $params instanceof JRegistry){
			$comParams = JComponentHelper::getParams( $componentName );
			if ( $comParams instanceof JRegistry && $params instanceof JRegistry ){
				$arrs = $comParams->toArray();
				foreach($arrs as $key => $val){
					if ( $params->get($key) == ''){
						$params->set($key, $val);
						if ($forcheck){
							$params->set($key.'_useglobal', true);
						}
					}
				}
			}
		}
	}
}