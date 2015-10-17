<?php
/**
 * @version     $Id$
 * @package     JSNTPLFW
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
 * Font processing widget
 *
 * @package  JSNTPLFW
 * @since    2.0.0
*/
class JSNTplWidgetFont extends JSNTplWidgetBase
{
	/**
	 * Method to handle upload action
	 *
	 * @return  void
	 */
	public function uploadAction()
	{
		if ($this->request->getMethod() != 'POST')
		{
			return;
		}

		if (isset($_FILES['font-upload']) AND $_FILES['font-upload']['error'] == 0)
		{
			// Verify font file
			if ( ! preg_match('/\.(ttf|otf|eot|svg|woff)$/', $_FILES['font-upload']['name']))
			{
				exit(JText::_('JSN_TPLFW_FONT_FILE_NOT_SUPPORTED'));
			}

			// Prepare directory to store uploaded font file
			$path = JPATH_ROOT . "/templates/{$this->template['name']}/uploads/fonts";

			if ( ! is_dir($path) AND ! JFolder::create($path))
			{
				exit(JText::_('JSN_TPLFW_UPLOAD_CREATE_DIR_FAIL'));
			}

			// Check if the directory is writable
			$buffer = '<html><head></head><body></body></html>';

			if ( ! JFile::write("{$path}/index.html", $buffer))
			{
				exit(JText::_('JSN_TPLFW_UPLOAD_CREATE_DIR_FAIL'));
			}

			// Move uploaded file to temporary folder
			$path .= '/' . str_replace(' ', '-', $_FILES['font-upload']['name']);

			if ( ! JFile::move($_FILES['font-upload']['tmp_name'], $path))
			{
				exit(JText::_('JSN_TPLFW_UPLOAD_MOVE_FILE_FAIL'));
			}
		}
		else
		{
			exit(JText::sprintf('JSN_TPLFW_UPLOAD_FAIL', isset($_FILES['font-upload']) ? $_FILES['font-upload']['error'] : 'unknown'));
		}

		exit('OK');
	}
}
