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
 * Integrity checking, backup and download widget.
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplWidgetIntegrity extends JSNTplWidgetBase
{
	/**
	 * Check files modification state based on checksum.
	 * Send list of modified files to client
	 *
	 * @return  void
	 */
	public function checkAction ()
	{
		$modifiedFiles		= JSNTplHelper::getModifiedFiles($this->template['name']);
		$hasModification	= count($modifiedFiles['add']) + count($modifiedFiles['delete']) + count($modifiedFiles['edit']);

		$this->setResponse(array(
			'hasModification'	=> (boolean) $hasModification,
			'files'				=> $modifiedFiles
		));
	}

	/**
	 * Backup all modified files
	 *
	 * @return  void
	 */
	public function backupAction ()
	{
		// Initialize variables
		$app			= JFactory::getApplication();
		$joomlaConfig	= JFactory::getConfig();
		$packageFile	= $joomlaConfig->get('tmp_path') . '/jsn-' . $this->template['id'] . '.zip';
		$templatePath	= JPATH_ROOT . '/templates/' . $this->template['name'];
		$backupPath		= $joomlaConfig->get('tmp_path') . '/' . $this->template['name'] . '_modified_files.zip';

		if (is_readable($packageFile))
		{
			$modifiedFiles	= JSNTplHelper::getModifiedFilesBeingUpdated($this->template['name'], $packageFile);
		}
		else
		{
			$modifiedFiles	= JSNTplHelper::getModifiedFiles($this->template['name']);
			$modifiedFiles	= array_merge($modifiedFiles['add'], $modifiedFiles['edit']);
		}

		// Check if backup was done before
		if ( ! $app->getUserState('jsn-tplfw-backup-done') OR ! is_file($backupPath))
		{
			// Read all modified files
			foreach ($modifiedFiles AS $file)
			{
				// Create array of file name and content for making archive later
				$files[] = array(
					'name' => $file,
					'data' => JFile::read("{$templatePath}/{$file}")
				);
			}

			// Create backup archive
			$archiver = new JSNTplArchiveZip;
			$archiver->create($backupPath, $files);

			// State that backup is created
			$app->setUserState('jsn-tplfw-backup-done', 1);
		}

		$this->setResponse($backupPath);
	}

	/**
	 * Create a backup of modified files then force user to download it.
	 *
	 * @return  void
	 */
	public function downloadAction ()
	{
		// Import necessary library
		jimport('joomla.filesystem.file');

		if ($isUpdate = (JFactory::getApplication()->input->getCmd('type') == 'update'))
		{
			if (is_readable(JFactory::getConfig()->get('tmp_path') . '/jsn-' . $this->template['id'] . '.zip'))
			{
				$this->backupAction();
			}
			else
			{
				$this->setResponse(JSNTplHelper::findLatestBackup($this->template['name']));
			}
		}
		else
		{
			$this->backupAction();
		}

		// Get path to backup file
		$path = $this->getResponse();

		// Force user to download backup file
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . filesize($path));
		header('Content-Disposition: attachment; filename=' . basename($path));
		header('Cache-Control: no-cache, must-revalidate, max-age=60');
		header('Expires: Sat, 01 Jan 2000 12:00:00 GMT');

		echo JFile::read($path);

		// Exit immediately
		exit;
	}
}
