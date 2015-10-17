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
jimport('joomla.filesystem.folder');

// Import image manipulation library
jsnimport('3rd-party.ace-media-image.image');

/**
 * Image manipulation class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsImage
{
	/**
	 * Resize an image and save to given destination.
	 *
	 * @param   string  $source  Path to source image.
	 * @param   string  $dest    Path to the location where resized image will be stored.
	 * @param   array   $size    Either array('width' => new_width, 'height' => new_height) or array(0 => new_width, 1 => new_height).
	 * @param   string  $name    Custom name for resized image file.
	 *
	 * @return  string  Path to resized image file on success, original source image path on failure.
	 */
	public static function resize($source, $dest, $size, $name = '')
	{
		// Initialize variables
		if (is_string($size) AND preg_match('/\d+x\d+/i', $size))
		{
			// Parse user defined image dimension
			$size = explode('x', strtolower($size), 2);
		}

		$width	= intval(isset($size['width'])	? $size['width']	: (isset($size[0]) ? $size[0] : 0));
		$height	= intval(isset($size['height'])	? $size['height']	: (isset($size[1]) ? $size[1] : 0));
		$resize	= $width > 0 OR $height > 0;

		// Do resize if possible
		if ($resize)
		{
			// Check current image size
			$imgSize = getimagesize($source);

			if ($imgSize[0] != $width AND $imgSize[1] != $height)
			{
				$image = new Ace_Media_Image($source);
				$image->resize($width, $height, $height > 0 AND $height > 0 ? false : ($width > 0 ? 'W' : 'H'));

				if (JFolder::create($dest) AND $image->save($dest = "{$dest}/" . (empty($name) ? basename($source) : $name), 90))
				{
					return $dest;
				}
			}
		}

		return $source;
	}
}
