<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: database.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class JSNDatabase
{
	public function __construct()
	{
		//to do
	}	
    /**
	 * Return menu items in menutype
	 * 
	 * @param: String menutype
	 * @param: String parent_id
	 */
	public function getItems( $menutype, $parent_id )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from("#__menu");
		$query->where(" published <> -2 AND menutype = ".$db->Quote( $menutype )." AND parent_id = ".$db->quote($parent_id));
		$query->order("rgt");
		$db->setQuery( $query );
		
		return $db->loadObjectList();
	}
	
    /**
	 * Return true/false to check exists child menu
	 * 
	 * @param: Menu id
	 */
	protected function hasChild( $menuId )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(' count(id) ');
		$query->from('#__menu');
		$query->where('parent_id = '.$db->Quote($menuId)." AND published <> -2");
		$db->setQuery( $query );
		return ($db->loadResult() > 0)?true:false;
	}
	
    /**
	 * 
	 * get Next insert id of menu_types table
	 * 
	 * @return: int id
	 */
	public function getNextMenuTypeId()
	{
		$config = JFactory::getConfig();
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$query->select("AUTO_INCREMENT AS id");
		$query->from("information_schema.tables");
		$query->where("table_schema = ".$db->quote($config->get('db'))." AND table_name = ".$db->quote($config->get("dbprefix")."menu_types"));
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows[0]->id;	
	}
	
    /**
	 * Return menu modules
	 * 
	 */
	protected function getMenus($select = '*')
	{
		$select = (trim($select)==''?'*':$select);		
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($select);
		$query->from("#__menu_types");
		$query->order("id");
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	/**
	 * 
	 * Get menu id of default page
	 * 
	 * @return: menu id
	 */
	public static function getDefaultPage()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("id, link, title");
		$query->from("#__menu");
		$query->where("home=1");
		$db->setQuery( $query );
		
		$default = new stdClass();
		$default->id    = 0;
		$default->link  = '';
		$default->title = '';
		
		$row = $db->loadObjectList();
		if ( count($row) ){
			$default->id    = $row[0]->id;
			$default->link  = $row[0]->link;
			$default->title = $row[0]->title;
		}
		
		return $default;
	}
   
	/**
	 * 
	 * Get menuitem details
	 * 
	 * @param Number $id
	 */
	public function getMenuItem($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__menu');
		$query->where('id='.$id);
		$db->setQuery($query);
		return $db->loadObject();
	}
}