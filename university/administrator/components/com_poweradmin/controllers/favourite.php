<?php
/**
 * @version     $Id$
 * @package     JSN_PowerAdmin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

// Register tables class path
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models/favourite.php';
// Load helper
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers/poweradmin.php';

/**
 * This is a controller class, it have some methods to load, save
 * activities. All methods will be called from client-side by ajax request
 *
 * @author binhpt
 */
class PoweradminControllerFavourite extends JSNBaseController
{
	/**
	 * Retrieve all activities of current user in database
	 * and response to client as JSON format.
	 *
	 * @return void
	 * @author binhpt
	 */
	public function load()
	{
		$model		= new PoweradminModelFavourite();
		$res		= $model->getItems();
		echo json_encode($res);
		jexit();
	}

	/**
	 * Remove a favourite item from database
	 */
	public function remove()
	{
		$model		= new PoweradminModelFavourite();
		$id			= JRequest::getInt('id');
		$user		= JFactory::getUser();
		$userId		= $user->id;
		$model->removeItem($id, $userId);
		jexit();
	}

	/**
	 * Save activity object that received from client
	 *	 * @return void
	 */
	public function save()
	{
		$model		= new PoweradminModelFavourite();
		$title		= JRequest::getVar('title');
		$url		= urldecode(JRequest::getVar('url'));
		if ($title && $url) {
			$model->saveItem($title, $url);
		}else{
			jexit(0);
		}
		// Close proccess to prevent output
		jexit();
	}
}









