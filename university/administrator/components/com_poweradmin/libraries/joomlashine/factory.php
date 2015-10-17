<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: factory.php 16006 2012-09-13 03:29:17Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class JSNFactory
{
	private static $config;
	private static $media;
	private static $rawmode;
	private static $template;
	private static $import;

	private static $_importedPackages = null;

	/**
	 *
	 * Get config instanced
	 */
	public static function getConfig()
	{
		JSNFactory::localimport('libraries.joomlashine.config');
		if (!isset(self::$config) ){
			self::$config = JSNConfig::getInstance();
		}

		return self::$config;
	}
	/**
	 *
	 * Get media instance
	 */
	public static function getMedia()
	{
		JSNFactory::localimport('libraries.joomlashine.document.media');
		if (!isset(self::$media) ){
			self::$media = JSNMedia::getInstance();
		}

		return self::$media;
	}
	/**
	 *
	 * Get template instance
	 */
	public static function getTemplate()
	{
		require_once JPATH_ROOT. '/plugins/system/jsnframework/libraries/joomlashine/template/helper.php';
		if (!isset(self::$template) ){
			self::$template = JSNTemplateHelper::getInstance();
		}

		return self::$template;
	}
	/**
	 *
	 * Get ramode instanced
	 *
	 * @param Array $params
	 */
	public static function getRawmode( $params = Array() )
	{
		JSNFactory::localimport('libraries.joomlashine.mode.rawmode');
		if (!isset(self::$rawmode) ){
			self::$rawmode = JSNRawmode::getInstance($params);
		}

		return self::$rawmode;
	}
	/**
	 *
	 * Import local file component
	 *
	 * @param String $args
	 * @param String $client
	 */
	public static function localimport($package, $client = 'admin')
	{
		$package = strtolower($package);

		$segments = explode('.', $package);
		$path = ($client == 'site') ? JPATH_ROOT.'/components/com_poweradmin/' : JPATH_ROOT.'/administrator/components/com_poweradmin/';
		$path.= implode('/', $segments);

		if (is_file($path . '.php')) {
			require_once $path . '.php';
		}

		if (is_dir($path)) {
			$lastSegment = end($segments);
			$filePath = $path . '/' . $lastSegment . '.php';

			if (is_file($filePath)) {
				require_once $filePath;
			}
		}
	}
	/**
	 *
	 * Import file
	 *
	 * @param String $args
	 * @param String $client
	 */
	public static function import($args, $client = 'admin')
	{
		$args = JString::strtolower($args);
		$filePath  = implode('/', explode('.', $args)).'.php';
		switch ($client)
		{
			case 'site':
				if (!isset(self::$import[$filePath.$client]) && file_exists(JPATH_ROOT.'/'.$filePath)){
					self::$import[$filePath.$client] = $args;
					require_once(JPATH_ROOT.'/'.$filePath);
				}
				break;

			case 'admin':
			default:
				if (!isset(self::$import[$filePath.$client]) && file_exists(JPATH_ADMINISTRATOR.'/'.$filePath)){
					self::$import[$filePath.$client] = $args;
					require_once(JPATH_ADMINISTRATOR.'/'.$filePath);
				}
				break;
		}
	}

	public static function _cURLCheckFunctions()
	{
		if (!function_exists("curl_init") ||
				!function_exists("curl_setopt") ||
				!function_exists("curl_exec") ||
				!function_exists("curl_close")) {
			return false;
		};

		return true;
	}
}