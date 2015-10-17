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

// Import necessary Joomla libraries
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

// Import library to create archive file
require_once JSN_PATH_LIBRARIES . '/3rd-party/zipfile/zip.class.php';

/**
 * Helper class for creating archive file.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsArchive
{
	/**
	 * Create ZIP archive.
	 *
	 * @param   array    $files  An array of files to put in archive.
	 * @param   boolean  $save   Whether to save zip data to file or not?
	 * @param   string   $name   Name with path to store archive file.
	 *
	 * @return  mixed  Zip data if $save is FALSE, or boolean value if $save is TRUE
	 */
	public static function createZip($files, $save = false, $name = '')
	{
		if (is_array($files) AND count($files))
		{
			// Initialize variables
			$zip	= new zipfile;
			$root	= str_replace('\\', '/', JPATH_ROOT);

			foreach ($files AS $file)
			{
				// Add file to zip archive
				if (is_array($file))
				{
					foreach ($file AS $k => $v)
					{
						$zip->create_file($v, $k);
					}
				}
				elseif (is_string($file) AND is_readable($file))
				{
					// Initialize file path
					$file = str_replace('\\', '/', $file);
					$path = str_replace($root, '', $file);

					$zip->create_file(JFile::read($file), $path);
				}
			}

			// Save zip archive to file system
			if ($save)
			{
				if ( ! JFolder::create($dest = dirname($name)))
				{
					throw new Exception(JText::sprintf('JSN_EXTFW_GENERAL_FOLDER_NOT_EXISTS', $dest));
				}

				if ( ! JFile::write($name, $zip->zipped_file()))
				{
					throw new Exception(JText::sprintf('JSN_EXTFW_GENERAL_CANNOT_WRITE_FILE', $name));
				}

				return true;
			}
			else
			{
				return $zip->zipped_file();
			}
		}

		return false;
	}
}
