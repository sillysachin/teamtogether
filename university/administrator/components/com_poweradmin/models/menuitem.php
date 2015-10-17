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

jimport('joomla.application.component.modellist');
/**
 *
 * @package		Joomla.Admin
 * @subpackage	com_poweradmin
 * @since		1.6
 */
class PoweradminModelMenuitem extends JModelList
{
	/**
	 *
	 * Delete an menu module
	 * @param interger $modid
	 */
	public function deleteMenu( $mid )
	{
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);

		//get menu type
		$query->select("menutype");
		$query->from("#__menu_types");
		$query->where("id=".$db->quote($mid));
		$db->setQuery($query);
		$menutype = $db->loadResult();

		//delete all items
		$query->clear();
		$query->delete();
		$query->from("#__menu");
		$query->where("menutype = ".$db->quote($menutype));
		$db->setQuery($query);
		$db->query();

		//delete menu type
		$query->clear();
		$query->delete();
		$query->from("#__menu_types");
		$query->where("id=".$db->quote($mid));
		$db->setQuery($query);
		return (bool) $db->query();
	}
	/**
	 *
	 * Get title of menu_type
	 * @param Number $mid
	 */
	public function getMenuTitle($mid){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select("title");
		$query->from("#__menu_types");
		$query->where("id=".$db->quote($mid));
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 *
	 * Get menu type
	 * @param Number $mid
	 */
	public function getMenuType($mid){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select("menutype");
		$query->from("#__menu_types");
		$query->where("id=".$db->quote($mid));
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 *
	 * Get menu type
	 * @param String $menutype
	 */
	public function getMenuId($menutype)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select("id");
		$query->from("#__menu_types");
		$query->where("id=".$db->quote($menutype));
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Method rebuild the entire nested set tree.
	 *
	 * @return	boolean	False on failure or error, true otherwise.
	 * @since	1.6
	 */
	public function rebuild($mid)
	{
		// Initialiase variables.
		$db = $this->getDbo();
		$menu = JTable::getInstance('menu');

		if (!$menu->rebuild()) {
			$this->setError($menu->getError());
			return false;
		}

		$menutype = $this->getMenuType($mid);

		// Convert the parameters not in JSON format.
		$db->setQuery(
			'SELECT id, params' .
			' FROM #__menu' .
			' WHERE params NOT LIKE '.$db->quote('{%') .
			'  AND params <> '.$db->quote('') .
		    '  AND menutype = '.$db->quote($menutype)
		);

		$items = $db->loadObjectList();
		if ($error = $db->getErrorMsg()) {
			$this->setError($error);
			return false;
		}

		foreach ($items as &$item)
		{
			$registry = new JRegistry;
			$registry->loadJSON($item->params);
			$params = (string)$registry;

			$db->setQuery(
				'UPDATE #__menu' .
				' SET params = '.$db->quote($params).
				' WHERE id = '.(int) $item->id
			);
			if (!$db->query()) {
				$this->setError($error);
				return false;
			}

			unset($registry);
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 *
	 * Rebuild itemid
	 * @param int $itemid
	 */
	public function rebuilditem($itemid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$menu = JTable::getInstance('menu');
		if (!$menu->rebuild()) {
			$this->setError($menu->getError());
			return false;
		}

		$query->select("params");
		$query->from("#__menu");
		$query->where("id=".$db->quote($itemid));
		$db->setQuery($query);
		$params = $db->loadResult();
		$registry = new JRegistry;
		$registry->loadJSON($params);
		$params = (string)$registry;
		$query->clear();
		$query->update("#__menu");
		$query->set("params = ".$db->quote($params));
		$query->where("id=".$db->quote($itemid));
		$db->setQuery($query);
		unset($registry);
		$this->cleanCache();
		return (bool) $db->query();
	}

	/**
	 *
	 * Publishing menu item
	 * @param interger $id
	 * @param string $publish
	 */
	public function publishing($id, $publish)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update("#__menu");
		$query->set("published = ".$db->quote($publish));
		$query->where("id=".$db->quote($id));
		$db->setQuery($query);
		return (bool) $db->query();
	}

	/**
	 *
	 * Check-in menu items
	 * @param interger $ids
	 */
	public function checkin( $itemid )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__menu');
		$query->set('checked_out = 0, checked_out_time = '.$this->_db->quote($this->_db->getNullDate()));
		$query->where('id = '.$db->quote($itemid));
		$db->setQuery($query);
		return $db->query();
	}

	/**
	 *
	 * Trash menu item
	 * @param int $itemid
	 */
	public function trash($itemid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update("#__menu");
		$query->set("published = -2");
		$query->where("id=".$db->quote($itemid)." AND home = 0");
		$db->setQuery($query);
		return (bool) $db->query();
	}

	/**
	 *
	 * Delete menu item
	 * @param interger $itemid
	 */
	public function delete($itemid)
	{
		$menu = JTable::getInstance('menu');
		return $menu->delete($itemid);
	}

	/**
	 *
	 * set menu default for joomla
	 * @param int $itemid
	 */
	public function setHome($itemid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('count(id)');
		$query->from('#__menu');
		$query->where('published = 1 AND id = '.$db->quote($itemid));
		$db->setQuery($query);
		if ((int) $db->loadResult() >= 1){
			$query->clear();
			$query->update("#__menu");
			$query->set("home = 0");
			$db->setQuery($query);
			if ($db->query()){
				$query->clear();
				$query->update("#__menu");
				$query->set("home=1");
				$query->where("id=".$db->quote($itemid));
				$db->setQuery($query);
				return (bool) $db->query();
			}
		}
		return false;
	}

	/**
	 * Move item to new position
	 *
	 * @param: int $itemid
	 * @param: int $parentid
	 * @param: int $ordering
	 *
	 * @return: Save to database new position for menu item
	 */
	public function moveItem($itemId, $parentid, $orders)
	{
		JSNFactory::import('components.com_menus.models.menu');
		$oldParentId = $this->getParentId($itemId);
		$menuInstance = new MenusModelMenu();
		$table = $menuInstance->getTable('Menu');
		if($oldParentId != $parentid){
			// Update parent id then rebuild menu table.
			$table->load($itemId);
			$table->setLocation($parentid, 'last-child');
			$table->store();
			$table->rebuildPath($table->id);
		}

		// Save items orders.
		$pks = $orders;
		$_order = array();
		for ($i=1; $i <= count($orders); $i++){
			$_order[] = $i;
		}

		$table->saveorder($pks, $_order);
		return true;
	}

	/**
	 * Get item parent id
	 *
	 * @param: int $itemid
	 * @param: int $parentid
	 * @param: int $ordering
	 *
	 * @return: Get order position in the database for a menu item
	 */
	public static function getParentId ($itemId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("parent_id");
		$query->from("#__menu");
		$query->where('id=' . $db->Quote($itemId));
		$db->setQuery( $query );
		return $db->loadResult();
	}

	/**
	 *
	 * Get all menu items article layout
	 *
	 * @return: Array
	 */
	public function getAllItems( $queries )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("id");
		$query->from("#__menu");
		//$query->where("link LIKE '%option=com_content%'");
		foreach($queries as $key => $value){
			if (!empty($value)){
			 	$query->where("link LIKE '%".$key."=".$value."%'");
			}
		}
		$db->setQuery((string) $query);
		$rows = $db->loadObjectList();
		return $rows;
	}

	/**
	 * Method to get assets of a menu item
	 */
	public static function loadMenuCustomAssets($menuId, $type = 'css')
	{
		$menuAssets	= new stdClass();
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('assets, legacy');
		$query->from('#__jsn_poweradmin_menu_assets');
		$query->where("type='" . $type . "' AND menuId='" . (int)$menuId . "'");
		$dbo->setQuery($query);
		$assets = $dbo->loadObject();
		if (count($assets)) {
			$menuAssets->legacy	= $assets->legacy;
			$menuAssets->assets = json_decode($assets->assets);
		}
		return $menuAssets;
	}


	/**
	 * Method to save assets of a menu item
	 */
	public static function saveMenuAssets($menuId, $assets = "", $type = "css", $legacy = 0)
	{
		$dbo = JFactory::getDbo();

		if($assets){
			$menuAssets = self::loadMenuCustomAssets($menuId, $type);
			if(isset($menuAssets->assets) && $menuAssets->assets){
				$query 	= "UPDATE #__jsn_poweradmin_menu_assets SET assets='" . $assets . "', legacy = " . (int)$legacy . " WHERE type = '" . $type . "' AND menuId=" . (int)$menuId;
			}else{
				$query 	= "INSERT INTO #__jsn_poweradmin_menu_assets(menuId, assets, type, legacy) VALUES ('" . (int)$menuId . "', '" . $assets . "', '" . $type . "', '" . (int)$legacy . "') ";
			}
		}else{
			$query = "DELETE FROM #__jsn_poweradmin_menu_assets WHERE menuId=" . (int)$menuId . " AND type='" . $type . "'";
		}
		$dbo->setQuery($query);
		return $dbo->query();
	}

	/**
	 * Method to  Get inherited asset files from all parents
	 * @param array $assetFiles
	 * @param int $itemId
	 * @param string $type
	 */
	public static function getInheritedAssetsFromParents(&$assetFiles, $itemId, $type = 'css')
	{
		$assetFiles = (array)$assetFiles;
		$parentId = self::getParentId($itemId);
		if ($parentId) {
			$parentAssets = self::loadMenuCustomAssets($parentId, $type);

			if (isset($parentAssets->legacy)) {
				if ($parentAssets->legacy) {
					$parentAccessFiles = (array)$parentAssets->assets;
						if (count($parentAccessFiles) && count($assetFiles)) {
							foreach ($assetFiles as $key=>$value) {
								if (isset($parentAccessFiles[$key]) && $parentAccessFiles[$key]->loaded == 'true') {
									unset($parentAccessFiles[$key]);
								}
							}
						}
						$assetFiles = array_merge($parentAccessFiles, $assetFiles );
				}
			}

			self::getInheritedAssetsFromParents($assetFiles, $parentId, $type);
		}else{
			return $assetFiles;
		}


	}

	/**
	 * Method to get all same menu parent id
	 * @param int $parentId
	 */
	public function getSameLevelMenuItems($parentId, $menuType = '')
	{

		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select("id");
		$query->from("#__menu");
		$query->where('parent_id=' . $parentId);
		if($parentId <= 1){
			$query->where("menutype='" . $menuType. "'");
		}
		$dbo->setQuery($query);
		return $dbo->loadObjectList();
	}

	/**
	 * Method to remove custom assets of menu
	 */
	public function removeCustomAssets($menuId, $type = 'css')
	{
		$menuId = (array)$menuId;
		$dbo 	= JFactory::getDbo();
		$query  = "DELETE FORM #__menu WHERE type='" . $type . "' AND menuId IN ('" . implode(",", $menuId) . "')";
		$dbo->setQuery($query);
		return $dbo->query();
	}

	/**
	 * Method to get menu item info
	 */
	public function getMenuItem($menuId)
	{
		$dbo 	= JFactory::getDbo();
		$query  = $dbo->getQuery(true);
		$query->select('parent_id, menutype');
		$query->from('#__menu');
		$query->where('id=' . (int)$menuId);
		$dbo->setQuery($query);
		return $dbo->loadObject();
	}
}