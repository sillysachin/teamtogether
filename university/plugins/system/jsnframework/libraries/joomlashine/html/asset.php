<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class to management asset files
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
abstract class JSNHtmlAsset
{
	/**
	 * Contains all namespace that associated with folder path
	 * @var array
	 */
	private static $_paths = array();

	/**
	 * Contains all dependencies of 3rd-party libraries
	 * @var array
	 */
	private static $_dependencies = array();

	/**
	 * Javascript modules that call when page load
	 * @var array
	 */
	private static $_scriptModules = array();

	/**
	 * List of css file and css inline
	 * @var array
	 */
	private static $_stylesheets = array();

	/**
	 * @var array
	 */
	private static $_loadedVersions = array();

	/**
	 * @var  boolean
	 */
	private static $_hasModules = false;

	/**
	 * Add URL of the CSS file to list that will append to header of webpage
	 *
	 * @param   string  $url         URL of the CSS file
	 * @param   string  $type        Mime type of the file
	 * @param   string  $media       Media type to use
	 * @param   array   $attributes  Attributes use for style tag
	 *
	 * @return  void
	 */
	public static function addStyle ($url, $type = 'text/css', $media = 'screen', $attributes = array())
	{
		// Add list of file use single call
		if (is_array($url))
		{
			foreach ($url as $_url)
			{
				self::addStyle($_url, $type, $media, $attributes);
			}

			return;
		}

		if (preg_match('/components\/([^\/]+)\//i', $url, $matches) || preg_match('/modules\/([^\/]+)\//i', $url, $matches))
		{
			$version = self::_getExtensionVersion($matches[1]);
		}

		$url .= (strpos($url, '?') === false) ? '?v=' : '&v=';
		$url .= isset($version) ? $version : '1.0.0';
		$url  = str_replace('?&', '?', $url);

		$document = JFactory::getDocument();
		$document->addStyleSheet($url, $type, $media, $attributes);
	}

	/**
	 * Add inline css to list that will append to header of webpage
	 *
	 * @param   string  $content  CSS content
	 * @param   string  $type     Mime type of the file
	 *
	 * @return  void
	 */
	public static function addInlineStyle ($content, $type = 'text/css')
	{
		$document = JFactory::getDocument();
		$document->addStyleDeclaration(trim($content), $type);
	}

	/**
	 * Add URL of the Script file to list that will append to header of webpage
	 *
	 * @param   string   $url    URL to the linked script
	 * @param   string   $type   Type of script. Defaults to 'text/javascript'
	 * @param   boolean  $defer  Adds the defer attribute.
	 * @param   boolean  $async  Adds the async attribute.
	 *
	 * @return  void
	 */
	public static function addScript ($url, $type = "text/javascript", $defer = false, $async = false)
	{
		// Add list of file use single call
		if (is_array($url))
		{
			foreach ($url as $_url)
			{
				self::addScript($_url, $type, $defer, $async);
			}

			return;
		}

		// Prepare jQuery compatibility
		if (JSNVersion::isJoomlaCompatible('3.2') AND preg_match('#/media/jui/js/jquery(\.min)?\.js$#', $url) AND strpos($url, '?origin=1') === false)
		{
			$url = JSN_URL_ASSETS . '/3rd-party/jquery/jquery.min.js';
		}

		if (preg_match('/components\/([^\/]+)\//i', $url, $matches) || preg_match('/modules\/([^\/]+)\//i', $url, $matches))
		{
			$version = self::_getExtensionVersion($matches[1]);
		}

		if (isset($version))
		{
			$url .= (strpos($url, '?') === false) ? '?v=' : '&v=';
			$url .= $version;
		}

		$url  = str_replace('?&', '?', $url);

		$document = JFactory::getDocument();
		$document->addScript($url, $type, $defer, $async);

		return;
	}

	/**
	 * Add inline script to list that will append to header of webpage
	 *
	 * @param   string  $content  JS content
	 * @param   string  $type     Mime type of the file
	 *
	 * @return  void
	 */
	public static function addInlineScript ($content, $type = 'text/javascript')
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($content, $type);

		return;
	}

	/**
	 * Method use to register a javascript library for allow to use
	 * by other script
	 *
	 * @param   string  $name     The identity of library that registered to system
	 * @param   string  $path     Path of the library that located to
	 * @param   array   $depends  List of dependencies required for this library
	 *
	 * @return  void
	 */
	public static function addScriptLibrary ($name, $path, $depends = array())
	{
		self::addScriptPath($name, $path);
		self::registerDepends($name, $depends);

		return;
	}

	/**
	 * Register path for requirejs
	 *
	 * @param   string  $alias  Alias name of the path
	 * @param   string  $path   Folder path that associated with namespace
	 *
	 * @return  void
	 */
	public static function addScriptPath ($alias, $path)
	{
		if (is_array($alias) && !self::_isIndexedArray($alias))
		{
			// Loop to each element of array to add it to variable $_paths
			foreach ($alias as $key => $value)
			{
				self::addScriptPath($key, $value);
			}

			return;
		}

		// Add namespace to associate with a path
		self::$_paths[$alias] = $path;
	}

	/**
	 * Preparing configuration for the extension
	 *
	 * @param   string   $extension  Name of the extension
	 * @param   boolean  $isAdmin    If this parameter is true, alias path will be point to
	 *                               administrator folder of the extension
	 * @param   string   $folder     Name of the folder that contains extension
	 *
	 * @return  void
	 */
	public static function prepare ($extension, $isAdmin = true, $folder = '')
	{
		// Igrnore prepare for jsnframework
		if ($extension == 'jsnframework')
		{
			return;
		}

		$alias = $extension;
		$path  = 'components';

		if (strpos($alias, 'com_') !== false)
		{
			$alias = substr($extension, 4);
		}
		elseif (strpos($alias, 'mod_') !== false)
		{
			$path = 'modules';
		}
		else
		{
			$path  = 'plugins';
			$path .= '/' . $folder;
			$isAdmin = false;
		}

		// Remove double slashes
		while (strpos($path, '//') !== false)
		{
			$path = trim(str_replace('//', '/', $path), '/');
		}

		$url = JUri::root(true);

		if ($isAdmin)
		{
			$url .= '/administrator';
		}

		$url .= '/' . $path . '/' . $extension;

		self::addScriptPath($alias, $url . '/assets/js');
		self::addScriptPath($alias . '/3rd', $url . '/assets/3rd-party');
	}

	/**
	 * Register dependencies for 3rd-party libraries that located
	 * outside of the framework
	 *
	 * @param   string  $name     Name of the library
	 * @param   array   $depends  Required libraries from framework to use library
	 *
	 * @return  void
	 */
	public static function registerDepends ($name, $depends)
	{
		// Accept only number indexes array for dependencies list
		if (!self::_isIndexedArray($depends))
		{
			// Convert to number indexes array
			$depends = (is_array($depends)) ? array_values($depends) : array($depends);
		}

		// Merge with existing dependencies list
		if (isset(self::$_dependencies[$name]) && is_array(self::$_dependencies[$name]))
		{
			$depends = array_merge(self::$_dependencies[$name], $depends);
		}

		// Add list to storage variable
		self::$_dependencies[$name] = $depends;
	}

	/**
	 * Register a module with params and dependencies to call list.
	 * Registered modules will call automatic after page loaded
	 *
	 * @param   string   $module  Name of the module that will call
	 * @param   mixed    $params  Parameters when execute the module
	 * @param   boolean  $inline  If value is true, inline javascript code to call a module will be returned
	 *
	 * @return  void
	 */
	public static function loadScript ($module, $params = array(), $inline = false)
	{
		self::$_hasModules = true;

		if ($inline === true)
		{
			return "
				<script type=\"text/javascript\">
					(JoomlaShine = window.JoomlaShine || {});
					(!window.jQuery || (JoomlaShine.jQueryBackup = jQuery));
					(!JoomlaShine.jQuery || (jQuery = JoomlaShine.jQuery));

					require(['{$module}'], function (ModuleObject) {
						JSNLoadedModules['{$module}'] = new ModuleObject(" . json_encode($params) . ");
					});

					(!window.jQuery || (JoomlaShine.jQuery = jQuery));
					(!JoomlaShine.jQueryBackup || (jQuery = JoomlaShine.jQueryBackup));
				</script>
			";
		}

		$module = array(
			'module' => $module,
			'params' => $params
		);

		$moduleKey = md5(json_encode($module));

		// Add module name to call list
		self::$_scriptModules[$moduleKey] = $module;
	}

	/**
	 * Add declaration code of assets to header of webpage.
	 *
	 * @return  void
	 */
	public static function buildHeader ()
	{
		if (!self::$_hasModules && !preg_match('/<script([^>]*)>\s*require\(([^<]+)<\/script>/i', JResponse::getBody()))
		{
			return '';
		}

		$application	= JFactory::getApplication();
		$document		= JFactory::getDocument();

		$component		= preg_replace('/^com_/i', '', is_object($application->input) ? $application->input->getCmd('option') : '');
		$version		= self::_getExtensionVersion(is_object($application->input) ? $application->input->getCmd('option') : '');

		$baseUrl		= JURI::base();
		$rootUrl		= JURI::root();

		// Auto define path for component's modules
		self::addScriptPath($component, 			$baseUrl . '/components/com_' . $component . '/assets/js');
		self::addScriptPath($component . '/3rd', 	$baseUrl . '/components/com_' . $component . '/assets/3rd-party');

		// Common configuration
		$env = array(
			'rootUrl'	=> $rootUrl,
			'baseUrl'	=> $baseUrl,
			'version'	=> $version
		);

		if (JSNVersion::isJoomlaCompatible('2.5') AND ! JSNVersion::isJoomlaCompatible('3.0'))
		{
			$jquery = "JSNFrameworkUrl + '/3rd-party/jquery/jquery-1.7.1.min.js'";
		}
		elseif (JSNVersion::isJoomlaCompatible('3.2'))
		{
			$jquery = "JSNEnv.rootUrl + '/plugins/system/jsnframework/3rd-party/jquery/jquery.min.js'";
		}
		else
		{
			$jquery = "JSNEnv.rootUrl + '/media/jui/js/jquery.min.js'";
		}

		$paths		= json_encode(self::$_paths);
		$depends	= json_encode(self::$_dependencies);
		$modules	= json_encode(self::$_scriptModules);
		$language	= json_encode(JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_GENERAL_LOADING', 'JSN_EXTFW_GENERAL_CLOSE')));

		$html = "
			<script type=\"text/javascript\" src=\"" . JSN_URL_ASSETS . "/3rd-party/requirejs/require.js\"></script>
			<script type=\"text/javascript\">
				// JoomlaShine script configuration
				var JSNEnv = " . json_encode($env) . ";
				var JSNFrameworkUrl = JSNEnv.rootUrl + '/plugins/system/jsnframework/assets';
				var JSNModules = {$modules};
				var JSNLoadedModules = {};
				var JSNCoreLanguage  = {$language};

				require.config({
					urlArgs: 'v=' + JSNEnv.version,
					baseUrl: JSNFrameworkUrl,
					paths: {$paths},
					shim: {$depends},
					waitSeconds: 500
				});

				define('jquery', ((!window.JoomlaShine || (window.JoomlaShine && !window.JoomlaShine.jQuery)) && !window.jQuery) ? [{$jquery}] : [], function () {
					(JoomlaShine = window.JoomlaShine || {});
					(!JoomlaShine.jQuery || (jQuery = JoomlaShine.jQuery));

					return jQuery.noConflict();
				});

				if (typeof(JSNModules) != 'undefined') {
					(JoomlaShine = window.JoomlaShine || {});
					(!window.jQuery || (JoomlaShine.jQueryBackup = jQuery));
					(!JoomlaShine.jQuery || (jQuery = JoomlaShine.jQuery));

					for (key in JSNModules) {
						!function (module, params) {
							require([module], function (Object) {
								if (typeof(Object) == 'function')
									JSNLoadedModules[module] = new Object(params);
							});
						}
						(JSNModules[key].module, JSNModules[key].params);
					}

					(!window.jQuery || (JoomlaShine.jQuery = jQuery));
					(!JoomlaShine.jQueryBackup || (jQuery = JoomlaShine.jQueryBackup));
				}
			</script>";

		return $html;
	}

	/**
	 * This method return true if an array use number system to index values.
	 *
	 * @param   array  $array  Array parameter to check.
	 *
	 * @return  boolean
	 */
	private static function _isIndexedArray (array $array)
	{
		return array_values($array) === $array;
	}

	/**
	 * Retrieve the version number of an extension.
	 *
	 * @param   string  $extension  Name of the extension.
	 *
	 * @return  void
	 */
	private static function _getExtensionVersion($extension)
	{
		if ( ! isset(self::$_loadedVersions[$extension]))
		{
			// Store extension version
			self::$_loadedVersions[$extension] = ($version = JSNUtilsText::getConstant('VERSION')) ? $version : '1.0.0';
		}

		return self::$_loadedVersions[$extension];
	}

	/**
	 * Load jquery lib just use for JSN_IMAGESHOW temporarilly.
	 *
	 * @param   string  $fileName  Name of the extension.
	 *
	 * @return  void
	 */

	public static function jquery($fileName = '')
	{
		$jqueryLocation = JSN_URL_ASSETS . '/3rd-party/jquery/';

		if ( ! file_exists(JSN_PATH_FRAMEWORK . '/assets/3rd-party/jquery/' . $fileName) OR ! $fileName)
		{
			$fileName = 'jquery-1.8.2.js';
		}

		$document = JFactory::getDocument();
		$document->addScript($jqueryLocation . $fileName);

		return;
	}
}
