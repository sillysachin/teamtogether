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

// Import necessary Joomla library
jimport('joomla.filesystem.file');

/**
 * Helper class for working with file.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsFile
{
	/**
	 * Force client to download a file.
	 *
	 * @param   string   $name     File name to show to client.
	 * @param   string   $content  File content or path to existing file.
	 * @param   string   $mime     MIME type to return to client.
	 * @param   boolean  $remove   Remove file after forcing download or leave it as is.
	 *
	 * @return  void
	 */
	public static function forceDownload($name, $content, $mime = 'application/octet-stream', $remove = false)
	{
		// Read content if it is a file path
		if (is_readable($content))
		{
			$file    = $content;
			$content = JFile::read($file);

			// Remove file after downloading?
			if ($remove)
			{
				JFile::delete($file);
			}
		}

		// Clear all output buffering
		while (@ob_end_clean());

		// Force client to download file
		header('Content-Type: ' . $mime);
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Content-Length: ' . strlen($content));
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: no-cache, must-revalidate, max-age=60');
		header('Expires: Sat, 01 Jan 2000 12:00:00 GMT');
		echo $content;
		exit();
	}
}
