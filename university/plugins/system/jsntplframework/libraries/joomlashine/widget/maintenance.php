<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
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
 * Maintenance widget.
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       2.0.0
 */
class JSNTplWidgetMaintenance extends JSNTplWidgetBase
{
	/**
	 * Backup template parameters
	 *
	 * @return  void
	 */
	public function backupAction()
	{
		// Get template and style ID
		$app = JFactory::getApplication();
		$tpl = $app->input->getCmd('template');
		$sid = $app->input->getInt('styleId');

		// Get database and query object
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		// Build query to get template style parameters
		$q->select('params');
		$q->from('#__template_styles');
		$q->where('template = ' . $q->quote($tpl), 'AND');
		$q->where('id = ' . (int) $sid);

		try
		{
			$db->setQuery($q);

			if ( ! ($params = $db->loadResult()))
			{
				throw new Exception($db->getErrorMsg());
			}
		}
		catch (Exception $e)
		{
			$params = $e->getMessage();
		}

		// Force user to download backup
		header('Content-Type: application/json');
		header('Content-Length: ' . strlen($params));
		header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', JText::sprintf('JSN_TPLFW_MAINTENANCE_FILE_NAME', JText::_($tpl))) . '.json');
		header('Cache-Control: no-cache, must-revalidate, max-age=60');
		header('Expires: Sat, 01 Jan 2000 12:00:00 GMT');

		echo $params;

		// Exit immediately
		exit;
	}

	/**
	 * Restore template parameters
	 *
	 * @return  void
	 */
	public function restoreAction()
	{
		// Check if we have backup file uploaded
		if ( ! isset($_FILES['backup-upload']))
		{
			throw new Exception(JText::sprintf('JSN_TPLFW_UPLOAD_FAIL', ''));
		}

		// Check if file is uploaded successful
		if ($_FILES['backup-upload']['error'] != 0)
		{
			throw new Exception(JText::sprintf('JSN_TPLFW_UPLOAD_FAIL', $_FILES['backup-upload']['error']));
		}

		// Read template parameters in uploaded file
		$params = JFile::read($_FILES['backup-upload']['tmp_name']);

		if ( ! $params)
		{
			throw new Exception(JText::_('JSN_TPLFW_MAINTENANCE_RESTORE_PARAMS_READ_FILE_FAIL'));
		}

		if (substr($params, 0, 1) != '{' OR substr($params, -1) != '}')
		{
			throw new Exception(JText::_('JSN_TPLFW_MAINTENANCE_RESTORE_PARAMS_INVALID_BACKUP'));
		}

		// Get template and style ID
		$app = JFactory::getApplication();
		$tpl = $app->input->getCmd('template');
		$sid = $app->input->getInt('styleId');

		// Get database and query object
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		// Build query to get template style parameters
		$q->update('#__template_styles');
		$q->set("params = '" . str_replace('\\', '\\\\', $params) . "'");
		$q->where('template = ' . $q->quote($tpl), 'AND');
		$q->where('id = ' . (int) $sid);

		try
		{
			$db->setQuery($q);

			if ( ! call_user_func(array($db, method_exists($db, 'execute') ? 'execute' : 'query')))
			{
				throw new Exception($db->getErrorMsg());
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}

		$this->setResponse(JText::_('JSN_TPLFW_MAINTENANCE_RESTORE_PARAMS_SUCCESS'));
	}
}
