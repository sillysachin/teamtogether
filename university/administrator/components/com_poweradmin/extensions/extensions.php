<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Image Source Picasa
 * @version $Id: sourcepicasa.php 11402 2012-02-27 10:14:44Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.plugin.plugin' );

abstract class plgJsnpoweradminExtensions extends JPlugin
{
	var $jsnLang;
	/**
	 * This event fired right after PowerAdmin's extension loaded
	 */
	public static function loadJavascriptLang()
	{
		return;
	}

	/**
	 * Method to check current extension version supported or not
	 * @return string message if current extensio
	 * 			version not supported
	 */
	public static function checkSupportedVersion()
	{
		return '';
	}

	/**
	 * Method get possible languages of current plugin
	 * @return array $languages = array('admin'=> array('file', 'path'))
	 */
	public static function getSupportedLanguages()
	{
		return array();
	}

	public static function saveParams($data)
	{
		return true;
	}

	/**
	 * Method to parse language difinition to javascript string
	 * @param string $key Language key
	 */
	public static function addLang($key)
	{
		$jsnLang = JSNJavascriptLanguages::getInstance();
		$jsnLang->addLang($key);
	}

	public static function addSpotlightCoverage()
	{
		return array();
	}

	public static function addSearchRange()
	{
		return array('');
	}


	public static function getTableMapping()
	{
		return;
	}

	public static function getSpotLightDescriptionMap()
	{
		return array();
	}

}