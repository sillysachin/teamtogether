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

if ( ! class_exists('JSN_Loader'))
{
	/**
	 * Class autoloader.
	 *
	 * @package  JSN_Framework
	 * @since    1.3.8
	 */
	class JSN_Loader
	{
		/**
		 * Path to search for class declaration.
		 *
		 * @var  array
		 */
		protected static $paths = array();

		/**
		 * Register base path to search for class declaration files.
		 *
		 * @param   string  $path    Base path.
		 * @param   string  $prefix  Class prefix.
		 *
		 * @return  void
		 */
		public static function register($path, $prefix = 'JSN')
		{
			self::$paths[$path] = $prefix;
		}

		/**
		 * Auload InnoThemes's classes.
		 *
		 * @param   string  $className  Name of class.
		 *
		 * @return  void
		 */
		public static function load($className)
		{
			// Only autoload class name prefixed with JSN
			if (substr($className, 0, 3) == 'JSN')
			{
				foreach (array_reverse(self::$paths) AS $base => $prefix)
				{
					if (strpos($className, $prefix) === 0)
					{
						// Split the class name into parts separated by camelCase
						$path = preg_split('/(?<=[a-z0-9])(?=[A-Z])/x', str_replace($prefix, '', $className));

						// Convert class name to file path
						$path = implode('/', array_map('strtolower', $path));

						// Check if class declaration file exists
						$file = $base . '/' . $path . '.php';

						while ( ! ($exists = @is_file($file)) AND strpos($file, '/') !== false)
						{
							$file = preg_replace('#/([^/]+)$#', '-\\1', $file);
						}

						if ( ! $exists AND strpos($path, '/') === false)
						{
							// If class name has single word, e.g. JSN_Version, duplicate it for alternative file path, e.g. version/version.php
							$exists = @is_file($file = $base . '/' . $path . '/' . $path . '.php');
						}

						if ($exists)
						{
							return include_once $file;
						}
					}
				}

				return false;
			}
		}

		/**
		 * Search a file in registered paths.
		 *
		 * @param   string  $file  Relative file path to search for.
		 *
		 * @return  string
		 */
		public static function get_path($file)
		{
			foreach (array_reverse(self::$paths) AS $base => $prefix)
			{
				if (@is_file($base . '/' . $file))
				{
					return $base . '/' . $file;
				}
			}

			return null;
		}
	}

	// Register class autoloader with PHP
	spl_autoload_register(array('JSN_Loader', 'load'));
}

// Register path to JSN Extension Framework's libraries
JSN_Loader::register(dirname(__FILE__) . '/joomlashine');

/**
 * Manually import class file of JSN Framework.
 *
 * Besides auto-loader, JSN Framework provides <b>jsnimport</b> function
 * for manually load class declaration file using dot syntax as following:
 *
 * <dl>
 * <dt>jsnimport('joomlashine.config.helper');</dt>
 * <dd>will load the following file: <q>libraries/joomlashine/config/helper.php</q></dd>
 * <dt>jsnimport('somevendor.somelib.someclass');</dt>
 * <dd>will load the following file:<q>libraries/somevendor/somelib/someclass.php</q></dd>
 * </dl>
 *
 * This function also supports loading class file from component directory
 * instead of framework directory. For example, in the administration section
 * of your component, create following directory structure:
 *
 * <pre>- JoomlaRoot/administrator/components/com_YourComponent
 *     - libraries
 *         - joomlashine
 *             + test
 * </pre>
 *
 * Then create a file named <b>helper.php</b> under the <b>test</b> directory.
 * Now, in your component, simply use following function call to load that file:
 *
 * <code>jsnimport('joomlashine.test.helper');</code>
 *
 * If you follow the class naming rule of JSN Framework, e.g. the class declared
 * in the above file is named <b>JSNTestHelper</b>, then your class will
 * autoload-able anywhere it is used without the need of executing the above
 * code first.
 *
 * @param   string  $path       A dot syntax path.
 * @param   string  $className  Class name.
 *
 * @return  boolean
 */
function jsnimport($path, $className = '')
{
	static $imported;

	// Only import the library if not already attempted
	if ( ! isset($imported[$path]))
	{
		// Check if class already declared
		if ( ! empty($className) AND class_exists($className, false))
		{
			return ($imported[$path] = true);
		}

		// Initialize variables
		$appl = is_object($appl = JFactory::getApplication()->input) ? $appl->getCmd('option') : '';
		$file = str_replace('.', '/', $path) . '.php';;
		$path = JPATH_ROOT . '/administrator/components/' . $appl . '/libraries';

		// Prefer to look for class file from extension directory first
		if ($appl)
		{
			$filePath = @is_file("{$path}/{ $file}") ? "{$path}/{ $file}" : null;
		}

		// Then look for class file from JSN Framework directory
		if ( ! isset($filePath))
		{
			$filePath = @is_file(JSN_PATH_LIBRARIES . "/{$file}") ? JSN_PATH_LIBRARIES . "/{$file}" : null;
		}

		// If the file exists attempt to include it
		if (isset($filePath))
		{
			$success = (bool) require_once $filePath;
		}

		// Add the import key to the memory cache container.
		$imported[$path] = isset($success) ? $success : false;
	}

	return $imported[$path];
}
