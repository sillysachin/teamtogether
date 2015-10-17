<?php
/**
 * @version     $Id: poweradmin.php 16454 2012-09-26 09:13:12Z hiepnv $
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Poweradmin component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_poweradmin
 * @since       1.6
 */
class JSNPaExtensionsHelper
{
    public static function getSupportedExtList()
    {
		$supportedList = json_decode(JSNPA_SUPPORTED_EXT_LIST);
		return $supportedList;
    }

    public static function getExtConfigurations($extName = '')
    {
    	$installedComponents = PoweradminHelper::getInstalledComponents();
    	$configurations	= array();

    	if (!$extName){
    		$supportedList	= JPluginHelper::getPlugin('jsnpoweradmin');
    		if (count($supportedList))
    		{
    			foreach ($supportedList as $key=>$ext)
    			{
    				if (in_array('com_' . $ext->name, $installedComponents))
    				{
	    				$config			= self::executeExtMethod($ext->name, 'addConfiguration');
		    			if (count($config))
		    			{
		    				$configurations[$ext->name]	= $config;
		    			}
    				}
    			}
    		}
    	}
    	else
    	{
    		if (in_array('com_' . $extName, $installedComponents))
    		{
    			$config			= self::executeExtMethod($extName, 'addConfiguration');
    			if (count($config))
    			{
    				$configurations[$extName]	= $config;
    			}
    		}
    	}

    	return $configurations;
    }

    public static function executeExtMethod($extName, $method, $params = null)
    {
    	JPluginHelper::importPlugin('jsnpoweradmin', $extName);
    	$plgClassName	= 'plgJsnpoweradmin' . ucfirst($extName);
    	$result	= call_user_func(array($plgClassName, $method), $params);
    	return $result;
    }

	public static function checkInstalledPlugin($name, $type = 'jsnpoweradmin')
    {
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('enabled');
		$query->from('#__extensions');
		$query->where("type = 'plugin' AND folder='" . $type . "' AND element='" . $name . "'");
		$db->setQuery($query);
		$rs	= $db->loadResult();
		$_isInstalled 	= false;
		$_isEnabled		= false;

		if (!isset($rs))
		{
			return array('isInstalled'=>false, 'isEnabled'=>false);
		}
		else
		{
			$result['isInstalled']	= true;
			$result['isEnabled']	= $rs ? true : false;
			return $result;
		}
    }

    public static function enableExt($name, $type = 'jsnpoweradmin', $isEnabled = true)
    {
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->update('#__extensions');
		$query->set('enabled=' . (int)$isEnabled);
		$query->where("type = 'plugin' AND folder=" . $db->quote($type) . " AND element=" . $db->quote($name));
		$db->setQuery($query);
		return $db->execute();
    }

    public static function getPaExtensions()
    {
    	$db	= JFactory::getDbo();
    	$query	= $db->getQuery(true);
    	$query->select('*');
    	$query->from('#__extensions');
    	$query->where("type = 'plugin' AND folder='jsnpoweradmin'");
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }

    public static function getDependentExtensions()
    {
    	$indentifiedNames	= array();
    	$indentifiedNames[JSNUtilsText::getConstant('IDENTIFIED_NAME', 'framework')] = JSNUtilsText::getConstant('VERSION', 'framework');
    	$indentifiedNames[JSN_POWERADMIN_IDENTIFIED_NAME] = JSN_POWERADMIN_VERSION;

    	$exts	= self::getPaExtensions();
    	if (count($exts))
    	{
    		foreach ($exts as $ext)
    		{
				$manifest	= json_decode($ext->manifest_cache);
				$indentifiedNames[JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX . $ext->element]	= $manifest->version;
    		}
    	}
		return $indentifiedNames;
    }
}
