<?php
/**
 * @version    $Id$
 * @package    JSNPoweradmin
 * @subpackage Item
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

jimport('joomla.application.component.model');
/**
 *
 * @package		Joomla.Admin
 * @subpackage	com_poweradmin
 * @since		2.0
 */
class PoweradminModelFavourite extends JSNBaseModel
{
	/**
	 * Method to get favourite items
	 */
	public function getItems()
	{
		$user 		= JFactory::getUser();
		$userId 	= $user->id;
		$dbo 	= JFactory::getDbo();
		$query  = $dbo->getQuery(true);
		$query->select('*');
		$query->from('#__jsn_poweradmin_favourite');
		$query->where("user_id =" . $userId);
		$query->order('created_date DESC');
		$dbo->setQuery($query);
		return $dbo->loadObjectList();
	}

	/**
	 * Method to get favourite item
	 */
	public function getItemByUrl($userId, $url = '')
	{
		$dbo 	= JFactory::getDbo();
		$query  = $dbo->getQuery(true);
		$query->select('id');
		$query->from('#__jsn_poweradmin_favourite');
		$query->where("user_id =" . $userId . " AND url='$url'" );
		$dbo->setQuery($query);
		return $dbo->loadResult();
	}

	/**
	 * Method to save favourite item
	 * @param string $title Item title
	 * @param string $url Item url
	 * @return int
	 */
	public function saveItem($title, $url)
	{
		$dbo		= JFactory::getDbo();
		$user 		= JFactory::getUser();
		$userId 	= $user->id;
		$title		= addslashes($title);
		$url		= addslashes($url);
		if ($itemId = $this->getItemByUrl($userId, $url)) {
			$query	= "UPDATE #__jsn_poweradmin_favourite SET title='$title' WHERE id=" . (int)$itemId;
		}else{
			$query		= "INSERT INTO #__jsn_poweradmin_favourite(user_id, title, url) VALUES ($userId, '$title', '$url') ";
		}
		
		$dbo->setQuery($query);
		return $dbo->query();
	}

	/**
	 * Method to remove an item
	 * @param int $id
	 */
	public function removeItem($id, $userId)
	{
		$dbo	= JFactory::getDbo();
		$query	= "DELETE FROM #__jsn_poweradmin_favourite WHERE id=" . (int)$id. " AND user_id=" .  $userId;
		$dbo->setQuery($query);
		return $dbo->query();
	}
}