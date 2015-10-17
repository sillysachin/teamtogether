<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: assignpages.php 13334 2012-06-15 13:05:16Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

class PoweradminModelAssignpages extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true){}
	
	/**
	 * 
	 * Remove all assigned 
	 * @param Number $moduleid
	 */
	public function removeAll($moduleid, $pages = array(), $except = false)
	{
		$db = JFactory::getDbo();
		$query = "DELETE FROM #__modules_menu WHERE moduleid=".$db->quote($moduleid);
		if($pages && is_array($pages)){
			if($except){
				$_pages = array();
				foreach ($pages as $k=>$v){
					$_pages[$k] = '-'.$v;
				}
				$query .= " AND menuid IN (".implode(",", $_pages).")";
			}else{
				$query .= " AND menuid IN (".implode(",", $pages).")";
			}
						
		}
		$db->setQuery($query);
		if (!$db->query()){
			JError::raiseWarning(500, $db->getErrorMsg());
		}
	}
	
	/**
	 * 
	 * Assign page to all page exists
	 * @param Number $moduleid
	 */
	public function assignToAllPages($moduleid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$this->removeAll($moduleid);
		$query->insert("#__modules_menu");
		$query->values($moduleid.', 0');
		$db->setQuery($query);
		$db->query();
	}
	/**
	 * 
	 * Save except pages
	 * 
	 * @param Number $moduleid
	 * @param Array $pages
	 */
	public function exceptPages($moduleid, $pages)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$this->removeAll($moduleid);
		for($i = 0; $i < count($pages); $i++){
			if($pages[$i]){
				$query->clear();
				$query->insert("#__modules_menu");
				$query->values($moduleid.',-'.$pages[$i]);
				$db->setQuery($query);
				$db->query();
			}
		}
	}
	
	/**
	 * 
	 * Assign to pages choose
	 * @param Number $moduleid
	 * @param Array $pages
	 */
	public function assignPages($moduleid, $pages, $except = false, $fromRawmode = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if($fromRawmode){
			$assignedMenuIds = $this->getMenuIds($moduleid);
			if($assignedMenuIds){
				if( $assignedMenuIds[0]->menuid > 0){
					//add more
					$this->removeAll($moduleid, $pages);
				}else{
					//change except
					$this->removeAll($moduleid, $pages, true);
					return;
				}
			}
		}else{
			$this->removeAll($moduleid);
		}
				
		for($i = 0; $i < count($pages); $i++){
			if($pages[$i]){
				$query->clear();
				$query->insert("#__modules_menu");
				if($except){
					$query->values($moduleid.',-'.$pages[$i]);	
				}else{
					$query->values($moduleid.','.$pages[$i]);
				}				
				$db->setQuery($query);
				$db->query();
			}
		}		
	}
	/**
	 * 
	 * Unassign all pages selected
	 * @param Number $moduleid
	 * @param Array $pages
	 */
	public function unassignPages( $moduleid, $pages )
	{
		JSNFactory::localimport('libraries.joomlashine.modules');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$assignmentType = JSNModules::checkAssign( $moduleid );
		if ( $assignmentType == 0 ){
			for($i = 0; $i < count($pages); $i++){
				$query->clear();
				$query->insert("#__modules_menu");
				$query->values($moduleid.',-'.$pages[$i]);
				$db->setQuery($query);
				$db->query();
			}
		}else if ($assignmentType === 1){
			$this->removeAll($moduleid);
			$allItems = $this->getAllPages();
			for($i = 0; $i < count($allItems); $i++){
				if ( !in_array($allItems[$i]->id, $pages) ){ 
					$query->clear();
					$query->insert("#__modules_menu");
					$query->values($moduleid.','.$allItems[$i]->id);
					$db->setQuery($query);
					$db->query();
				}
			}
		}else if ($assignmentType === 2){
			for($i = 0; $i < count($pages); $i++){
				$query->clear();
				$query->insert("#__modules_menu");
				$query->values($moduleid.',-'.$pages[$i]);
				$db->setQuery($query);
				$db->query();
			}
		}else if ($assignmentType === 3){
			for($i = 0; $i < count($pages); $i++){
				$query->clear();
				$query->delete();
				$query->from("#__modules_menu");
				$query->where("menuid=".$db->quote($pages[$i]).' AND moduleid = '.$db->quote($moduleid));
				$db->setQuery($query);
				$db->query();
			}
		}
	}
	
	/**
	 * 
	 * Get all pages exists in database
	 */
	public function getAllPages()
	{
		$db = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select("id");
	    $query->from("#__menu");
	    $query->where("id<>0");
	    $db->setQuery($query);
	    return $db->loadObjectList();
	}
	/**
	 * 
	 * Get name of page
	 * 
	 * @param Number $pageId
	 */
	public function getPageName($pageId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("title");
		$query->from("#__menu");
		$query->where("id=".$db->quote($pageId));
		$db->setQuery( (string) $query );
		return $db->loadResult();
	}
	/**
	 * Method to get the client object
	 *
	 * @return	void
	 * @since	1.6
	 */
	function &getClient()
	{
		return $this->_client;
	}
	
	/**
	 * Custom clean cache method for different clients
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache('com_poweradmin', $this->getClient());
	}
	
	public function getMenuIds($moduleId){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("menuid");
		$query->from("#__modules_menu");
		$query->where("moduleid=".$db->quote($moduleId));
		$db->setQuery( (string) $query );
		return $db->loadObjectList();
	}
}
?>