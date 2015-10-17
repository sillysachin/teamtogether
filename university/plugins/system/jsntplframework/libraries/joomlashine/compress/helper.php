<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * CSS Compression engine
 *
 * @package     TPLFramework
 * @subpackage  Plugin
 * @since       1.0.0
 */
abstract class JSNTplCompressHelper
{
	/**
	 * Retrieve path to file in hard disk based from file URL
	 *
	 * @param   string  $file  URL to the file
	 *
	 * @return  string
	 */
	public static function getFilePath ($file)
	{
		// Located file from root
		if (strpos($file, '/') === 0)
		{
			if (file_exists($tmp = realpath(str_replace(JUri::root(true), JPATH_ROOT, $file))))
			{
				return $tmp;
			}
			elseif (file_exists($tmp = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $file)))
			{
				return $tmp;
			}
			elseif (file_exists($tmp = realpath(JPATH_ROOT . '/' . $file)))
			{
				return $tmp;
			}
		}

		if (strpos($file, '://') !== false && JURI::isInternal($file))
		{
			$path = parse_url($file, PHP_URL_PATH);

			if (file_exists($tmp = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $path)))
			{
				return $tmp;
			}
			elseif (file_exists($tmp = realpath(JPATH_ROOT . '/' . $path)))
			{
				return $tmp;
			}
		}

		$rootURL = JUri::root();
		$currentURL = JUri::current();

		$currentPath = JPATH_ROOT . '/' . substr($currentURL, strlen($rootURL));
		$currentPath = str_replace(DIRECTORY_SEPARATOR, '/', $currentPath);
		$currentPath = dirname($currentPath);

		return JPath::clean($currentPath . '/' . $file);
	}

	/**
	 * Retrieve absolute path from the current path
	 *
	 * @param   string  $currentPath  Current path
	 * @param   string  $filePath     File path
	 *
	 * @return  string
	 */
	public static function getRelativeFilePath ($currentPath, $filePath)
	{
		// Prepare file path
		if (strpos($filePath, '?') !== false)
		{
			list($filePath, $queryString) = explode('?', $filePath, 2);
		}

		$currentPath = str_replace('\\', '/', $currentPath);
		$realPath = realpath($currentPath . '/' . $filePath);
		$rootPath = realpath(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']));

		return substr($realPath, strlen($rootPath)) . (isset($queryString) ? "?{$queryString}" : '');
	}

	/**
	 * Parse attributes from the html tag
	 *
	 * @param   string  $markup  HTML Markup of the tag
	 *
	 * @return  array
	 */
	public static function parseAttributes ($markup)
	{
		$attributes = array();

		// Parse attributes by using regular expression
		if (preg_match_all('/\s*([a-z]+)\s*=(["|\']([^"|\']+)["|\'])/i', $markup, $matches))
		{
			$attributes = array_combine(
				array_map('strtolower',
					array_map('trim',
						$matches[1]
					)
				),
				$matches[3]
			);
		}

		// Return the parsed attibutes
		return $attributes;
	}

	/**
	 * Method to prepend content to the beginning of a file
	 *
	 * @param   string    $string    Content will be prepended
	 * @param   resource  $filename  File to prepend
	 *
	 * @return  boolean
	 */
	public static function prependIntoFile($string, $filename)
	{
		$combine = $string . JFile::read($filename);

		return JFile::write($filename, $combine);
	}
}
