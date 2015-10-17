<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: changeposition.php 12779 2012-05-18 02:55:18Z binhpt $
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

class PoweradminModelChangeposition extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true){}
    
	/**
	 * 
	 * Get position of module
	 * 
	 * @param: Number $moduleid
	 */
	public function getModulePosition($moduleid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("position");
		$query->from("#__modules");
		$query->where("id=".$db->quote($moduleid));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * 
	 * Save setting position to the databse
	 * @param Number $moduleid
	 * @param String $position
	 */
	public function setPosition($moduleid, $position)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update("#__modules");
		$query->set("position=".$db->quote($position).", ordering = ".$db->quote($this->getNextOrdering($position)));
		$query->where("id=".$db->quote($moduleid));
		$db->setQuery($query);
		if (!$db->query()){
			echo $db->getErrors();
		}
	}
	
	/**
	 * 
	 * Get next ordering in position
	 * 
	 * @param String $position
	 */
	public function getNextOrdering($position)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("max(ordering)");
		$query->from("#__modules");
		$query->where("position=".$db->quote($position));
		$db->setQuery($query);
		return (int)$db->loadResult()+1;
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
}
?>