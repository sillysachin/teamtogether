<?php
/**
 * @version     $Id$
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class JSNModules
{
	public function __construct()
	{
		//TODO:
	}
	
    /**
	* Return global JSNTemplate object
	*  
	*/
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}
		
		if (empty($instances['JSNModules'])) {
			$instance	= new JSNModules();
			$instances['JSNModules'] = &$instance;
		}

		return $instances['JSNModules'];
	}
	
	/**
	* Load all by status(publish/unpublish/all) modules
	*
	* @return: Array
	*/
	public static function getModules( $position, $Itemid, $published = '' )
	{
		$instances = JSNModules::getInstance();
		
		$app  = JFactory::getApplication();
		$lang = JFactory::getLanguage()->getTag();
		
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, module, position, content, showtitle, params, published, checked_out');
		$query->from('#__modules AS m');
		$query->where('m.position = '.$db->Quote( $position ));
		
		if ((int) $published >= 1){
			$query->join('LEFT','#__modules_menu AS mm ON mm.moduleid = m.id');
			$query->where('m.published = '.$db->Quote( $published ));
			//if assigned to all or current itemid
			$query->where('(mm.menuid = '. (int) $Itemid .' OR mm.menuid = 0)');
		}else{
			$query->where('m.published <> -2');
		}
		
		
		$date = JFactory::getDate();		
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($date).')');
		$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($date).')');

		$query->where('m.client_id = 0');

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter()) {
			$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
		}

		$query->order('m.ordering ASC, m.title DESC, m.id DESC');

		// Set the query
		$db->setQuery($query);
		if (!($modules = @$db->loadObjectList())) {
			return;
		}		
		// Apply negative selections and eliminate duplicates
		$clean	= array();
		for ($i = 0, $n = count($modules); $i < $n; $i++)
		{
			$module = &$modules[$i];

			// Only accept modules without explicit exclusions.
			if (!isset($clean[$module->id]))
			{
				//determine if this is a custom module
				$file				= $module->module;
				$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
				$module->user		= $custom;
				// Custom module name is given by the title field, otherwise strip off "com_"
				$module->name		= $custom ? $module->title : substr($file, 4);
				$module->style		= null;
				$module->position	= strtolower($module->position);
				$module->assignment = $instances->getItemAssignmentType($module->id, $Itemid);
				$module->moduletype = $instances->getModuleType($module->id);
				$clean[$module->id]	= $module;
			}
		}
		unset($dupes);
		// Return to simple indexing that matches the query order.
		$clean = array_values($clean);
		
		return $clean;
	}
	/**
	 * 
	 * Get all modules assigned/unassign not default position
	 * 
	 * @param Array $defaultPositions
	 * @param Number $Itemid
	 * @return: Array 
	 */
	public static function getModulesNotDefaultPosition( $Itemid )
	{
		$instances   = JSNModules::getInstance();
		$jsntemplate = JSNFactory::getTemplate();
		$positions   = $jsntemplate->loadXMLPositions();
		
		$defaultPositions = "";
		foreach($positions as $position){
			$defaultPositions .= "'".(string) $position->name."',";
		}
		
		if ( strlen($defaultPositions) ){
			$defaultPositions .= "''";
		}

		$app  = JFactory::getApplication();
		$lang = JFactory::getLanguage()->getTag();
		
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, module, position, content, showtitle, params, published, checked_out');
		$query->from('#__modules AS m');
		$query->where(' ( m.position NOT IN ('.$defaultPositions.') OR m.position IS NULL OR m.position = "" ) ');
		$query->where('m.published <> -2');

		$date = JFactory::getDate();
		
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($date).')');
		$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($date).')');

		$query->where('m.client_id = 0');

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter()) {
			$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
		}

		$query->order('m.ordering ASC, m.title DESC, m.id DESC');
		
		// Set the query
		$db->setQuery($query);
		if (!($modules = @$db->loadObjectList())) {
			return;
		}

		// Apply negative selections and eliminate duplicates
		$clean	= array();
		for ($i = 0, $n = count($modules); $i < $n; $i++)
		{
			$module = &$modules[$i];

			// Only accept modules without explicit exclusions.
			if (!isset($clean[$module->id]))
			{
				//determine if this is a custom module
				$file				= $module->module;
				$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
				$module->user		= $custom;
				// Custom module name is given by the title field, otherwise strip off "com_"
				$module->name		= $custom ? $module->title : substr($file, 4);
				$module->style		= null;
				$module->position	= strtolower($module->position);
				$module->assignment = $instances->getItemAssignmentType($module->id, $Itemid);
				$module->moduletype = $instances->getModuleType($module->id);
				$clean[$module->id]	= $module;
			}
		}
		unset($dupes);
		// Return to simple indexing that matches the query order.
		$clean = array_values($clean);
		
		return $clean;
	}
	/**
	 * 
	 * Get module type title
	 * 
	 * @param (Number) $moduleid
	 */
	public static function getModuleType( $moduleid )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('e.element, e.client_id');
		$query->from('#__modules as m');
		$query->join('left', '#__extensions as e ON m.module = e.element');
		$query->where('m.id = '.$db->quote($moduleid));
		$db->setQuery((string) $query);
		$mObject = $db->loadObject();
		$moduleTyle = JText::_('JSN_RAWMODE_MODULETYPE');
		if ( is_object($mObject) && !empty($mObject->element) ) {
			$client = JApplicationHelper::getClientInfo( $mObject->client_id );
			$lang = JFactory::getLanguage();
			$lang->load($mObject->element.'.sys', $client->path, null, false, false)
				||	$lang->load($mObject->element.'.sys', $client->path.'/modules/'.$mObject->element, null, false, false)
				||	$lang->load($mObject->element.'.sys', $client->path, $lang->getDefault(), false, false)
				||	$lang->load($mObject->element.'.sys', $client->path.'/modules/'.$mObject->element, $lang->getDefault(), false, false);

				$mName = JText::_($mObject->element);
				if ($mObject->element == $mName){
					$path = JPath::clean($client->path.'/modules/'.$mObject->element.'/'.$mObject->element.'.xml');
					if ( file_exists($path) ) {
						$moduleDetails = simplexml_load_file($path);
						$mName = JText::_($moduleDetails->name);
					}
				}
			$moduleTyle .= '&#013;'.htmlspecialchars($mName, ENT_QUOTES);
		}
		return $moduleTyle;
	}
	/**
	 * 
	 * Get assignment type
	 * @param Number $moduleid
	 */
	public static function getItemAssignmentType($moduleid, $itemid)
	{		
		$instances  = JSNModules::getInstance();		
		$assigned   = $instances->getAssigned($moduleid);
		$assignment = $instances->checkAssign($moduleid);
		if ( $assignment === 1 ){
			return 'all';
		}else if ( $assignment === 2 ){
			return in_array("-$itemid", $assigned) ? 'except' : 'this';
		}else if ( $assignment === 3 ){
			return in_array($itemid, $assigned) ? 'selected' : '';
		}
		return '';
	}
    /**
	 * Get array modules_menu assigned
	 */
    public static function getAssigned($moduleid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("menuid");
		$query->from("#__modules_menu");
		$query->where("moduleid=".$db->quote($moduleid));
		$db->setQuery((string)$query);		
		$result = $db->loadObjectList();
		$resultArr = array();
		if(count($result)){
			foreach ($result as $row){
				array_push($resultArr, $row->menuid);
			}
		} 
		return $resultArr;
	}
    /**
	* 
	* Get assigment type
	*/
	public static function checkAssign($moduleid)
	{
		$instances  = JSNModules::getInstance();		
		$assigned   = $instances->getAssigned($moduleid);
		$assignment = '';
		if (empty($moduleid)) {
			$assignment = 0;
		}
		else if (empty($assigned)) {
			// For an existing module it is assigned to none.
			$assignment = 0;
		}
		else {
			if ($assigned[0] == 0){
				$assignment = 1;
			}else if ($assigned[0] < 0) {
				$assignment = 2;
			}else if ($assigned[0] > 0) {
				$assignment = 3;
			}else {
				$assignment = 0;
			}
		}
		return $assignment;
	}
	
    /**
	 * 
	 * Get name of module in the database
	 * @param unknown_type $moduleid
	 */
	public static function getNameOfModule($moduleid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("title");
		$query->from("#__modules");
		$query->where("id=".$db->quote($moduleid));		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
   /**
	* Move an module to new position
	*
	* @param: $moduleid is id of module in the database
	* @param: $position is position of template 
	* @return: None return value just change the param of module
	*/
	public static function moveModule( $moduleid, $position, $order )
	{
		$instances  = JSNModules::getInstance();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('position');
		$query->from('#__modules');
		$query->where("id = ".$db->Quote( $moduleid ));
		$db->setQuery( $query );
		$from_position = $db->loadResult(); 
		// save your drag & drop
		$query->clear();
		$query->update("#__modules");
		$query->set("position = ".$db->Quote( $position ));
		$query->where("id = ".$db->Quote( $moduleid ));
		$db->setQuery( $query );
		if (!$db->query()){
			JText::printf('MSG_AJAX_ERROR', $db->getErrorMsg());
			return false;
		}
		
		//save your sort on position
		foreach($order as $key => $ordering){
			$query->clear();
			$query->update("#__modules");
			$query->set("ordering=".$db->quote($key));
			$query->where("id=".$db->quote($ordering));
			$db->setQuery($query);
			$db->query();
		}		
		return true;
	}
	
    /**
	 * 
	 * Show/Hide title of module
	 * @param Number $moduleid
	 * @param Number $showtitle is 0/1
	 */
	public static function showTitle($moduleid, $showtitle)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update("#__modules");
		$query->set("showtitle=".$db->quote($showtitle));
		$query->where("id=".$db->quote($moduleid));
		$db->setQuery($query);
		$db->query();
	}	
	
    /**
	* Publish function
	*
	* @param: $moduleid is index of module in database
	* @return: Not return value only set to the database
	*/
	public static function publish( $moduleid )
	{		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->update("#__modules");
		$query->set("published = '1'");
		$query->where("id = ".$db->Quote($moduleid));		
		$db->setQuery( $query );
		if (!$db->query()){
			JText::printf('MSG_AJAX_ERROR', $db->getErrorMsg()); 
			return false;
		}
		return true;
	}

	/**
	* unpunlish function
	*
	* @param: $moduleid is index of module in database
	* @return: Not return value only set to the database
	*/
	public static function unpublish( $moduleid )
	{		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->update("#__modules");
		$query->set("published = '0'");
		$query->where("id = ".$db->Quote($moduleid));
		$db->setQuery( $query );
		if (!$db->query()){
			JText::printf('MSG_AJAX_ERROR', $db->getErrorMsg()); 
			return false;
		}
		return true;
	}
	
	/**
	 * Check in module
	 * 
	 * @param:	Int	$moduleid index of module in database
	 * 
	 */
	
	public static function checkin($moduleid)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->update("#__modules");
		$query->set("checked_out = '0'");
		$query->set("checked_out_time = ''");
		$query->where("id = ".$db->Quote($moduleid));
		$db->setQuery( $query );
		if (!$db->query()){
			JText::printf('MSG_AJAX_ERROR', $db->getErrorMsg());
			return false;
		}
		return true;
	}
}
