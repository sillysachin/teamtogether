<?php

/**
 * @version     $Id: form.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Helpers
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 *  JSNUniform form upload helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 */
class JSNUniformUpload
{

	/**
	 * Upload Form
	 *
	 * @param   string  $file      POST File
	 * 
	 * @param   string  &$err      Message Error
	 * 
	 * @param   string  $settings  $Setting
	 * 
	 * @return boolean 
	 */
	public static function canUpload($file, &$err, $settings)
	{
		if (empty($file['name']))
		{
			$err = JText::_('JSN_UNIFORM_ERROR_UPLOAD_INPUT');
			return false;
		}

		$params = JComponentHelper::getParams('com_media');


		if (empty($settings->options->limitFileExtensions) || $settings->options->limitFileExtensions != 1)
		{
			$settings->options->allowedExtensions = $params->get('upload_extensions');
		}
		if (empty($settings->options->limitFileSize) || $settings->options->limitFileSize != 1)
		{
			$settings->options->maxSize = $params->get('upload_maxsize');
			$settings->options->maxSizeUnit = 'MB';
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$err = JText::_('JSN_UNIFORM_ERROR_WARNFILENAME');
			return false;
		}
		$format = strtolower(JFile::getExt($file['name']));
		$allowedExtensions = str_replace(" ", "", $settings->options->allowedExtensions);
		$allowable = explode(',', $allowedExtensions);
		switch ($settings->options->maxSizeUnit)
		{
			case 'KB':
				$uploadMaxSize = $settings->options->maxSize * 1024;
				break;
			case 'MB':
				$uploadMaxSize = $settings->options->maxSize * 1024 * 1024;
				break;
			case 'GB':
				$uploadMaxSize = $settings->options->maxSize * 1024 * 1024 * 1024;
				break;
		}

		if ($uploadMaxSize > (int) (ini_get('upload_max_filesize')) * 1024 * 1024)
		{

			if ((int) $file['size'] == 0 && (int) $file['error'] == 1 && empty($file['tmp_name']))
			{
				$err = JText::sprintf('JSN_UNIFORM_POST_UPLOAD_SIZE', (int) (ini_get('upload_max_filesize')) . " MB");
				return false;
			}
		}

		if (!in_array($format, $allowable) || in_array($format, array('php', 'phps', 'php3', 'php4', 'phtml', 'pl', 'py', 'jsp', 'asp', 'htm', 'shtml', 'sh', 'cgi', 'htaccess', 'exe', 'dll')))
		{
			$err = JText::sprintf('JSN_UNIFORM_ERROR_WARNFILETYPE', "." . $format);
			return false;
		}
		if ((int) $file['size'] > $uploadMaxSize)
		{
			$err = JText::sprintf('JSN_UNIFORM_POST_UPLOAD_SIZE', $settings->options->maxSize . " " . $settings->options->maxSizeUnit);
			return false;
		}
		else if ((int) $file['size'] == 0 && (int) $file['error'] == 1 && empty($file['tmp_name']))
		{
			$err = JText::sprintf('JSN_UNIFORM_POST_UPLOAD_SIZE', $settings->options->maxSize . " " . $settings->options->maxSizeUnit);
			return false;
		}
		return true;
	}

}
