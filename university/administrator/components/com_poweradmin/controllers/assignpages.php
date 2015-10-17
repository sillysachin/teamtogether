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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
error_reporting(0);
class PoweradminControllerAssignpages extends JControllerForm
{
	/**
	 * 
	 * Redirect to custom page assignment
	 */
	public function customPage()
	{
		$app = JFactory::getApplication();
		$moduleid = JRequest::getVar('moduleid', 0, 'int');
		$moduleid = explode(",", $moduleid);
		$app->setUserState('com_poweradmin.assignpages.custompage.moduleid', $moduleid);
		$this->setRedirect('index.php?option=com_poweradmin&view=assignpages&tmpl=component');
		$this->redirect();
	}
	
	/**
	 * 
	 * Save custom assign to database
	 * 
	 */
	public function save($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$assignment = JRequest::getVar('assignment', 1, 'int');
		$moduleid = $app->getUserState('com_poweradmin.assignpages.custompage.moduleid', JRequest::getVar('moduleid', array(), 'get', 'array'));

		$redirectPage = 'index.php?option=com_poweradmin&view=assignpages&tmpl=component';		
		$this->setRedirect($redirectPage);
		
		$count = count($moduleid);
		//Redirect if moduleid empty  
		if ($count == 0){
			JError::raiseNotice(E_NOTICE, 'Module ID is empty.');
			$this->redirect();
		}
		
		$model = $this->getModel('assignpages');
		
		switch($assignment)
		{
			case 0:
				for($i = 0; $i < $count; $i++){
					$model->removeAll($moduleid[$i]);
				}
				break;
			case 1:
				for($i = 0; $i < $count; $i++){
					$model->assignToAllPages($moduleid[$i]);
				}
				break;
			case 2:
				$pages = JRequest::getVar('assignpages', array(), 'post', 'array');
				for($i = 0; $i < $count; $i++){
					$model->exceptPages($moduleid[$i], $pages, true);
				}
				break;
			case 3:
				$pages = JRequest::getVar('assignpages', array(), 'post', 'array');
				for($i = 0; $i < $count; $i++){
					$model->removeAll($moduleid[$i]);
					$model->assignPages($moduleid[$i], $pages);
				}
				break;

			default:
				JError::raiseNotice(E_NOTICE, 'Assignment empty.');
				break;

		}
		$this->redirect();
	}
	
    /**
	* This function to set an module to publish
	* 
	* @return: Change value in table of database
	*/
	public function assign()
	{
		JSNFactory::localimport('libraries.joomlashine.modules');

		$moduleid    = JRequest::getVar('moduleid', array(), 'post', 'array');
		$pages       = JRequest::getVar('assignpages', array(), 'post', 'array');
		$publish_area= JRequest::getVar('publish_area', '');
		
		$count = count($moduleid);
		if ($count == 0){
			JText::printf('MSG_AJAX_ERROR', JText::_('MSG_AJAX_MOVE_ERROR'));
			jexit();
		}

		$model = $this->getModel('assignpages');

		switch( $publish_area )
		{
			case 'all':
				for($i = 0; $i < $count; $i++){
					$model->assignToAllPages( $moduleid[$i] );
				}
				if ($count == 1){
					JText::printf('MSG_AJAX_ASSIGNMENT_MODULE', '"'.JSNModules::getNameOfModule($moduleid[0]).'"', 'assigned', 'to', 'All Pages');
				}else{
					JText::printf('MSG_AJAX_MULTIPLE', $count, ' assigned to all pages. ');
				}
				break;
			default:
			case 'one':
				for($i = 0; $i < $count; $i++){
					$model->assignPages( $moduleid[$i], $pages,null,true );
				}
				if ($count == 1){
					JText::printf('MSG_AJAX_ASSIGNMENT_MODULE', '"'.JSNModules::getNameOfModule($moduleid[0]).'"', 'assigned', 'to', $model->getPageName($pages[0]));
				}else{
					JText::printf('MSG_AJAX_MULTIPLE', $count, ' assigned to this page. ');
				}
				break;
		}
		jexit();
	}
	
	/**
	* This function to set an module to unpublish
	* 
	* @return: Change value in table of database
	*/
	function unassign()
	{
		JSNFactory::localimport('libraries.joomlashine.modules');

		$moduleid        = JRequest::getVar('moduleid', array(), 'post', 'array');
		$pages           = JRequest::getVar('assignpages', array(), 'post', 'array');
		$unpublish_area  = JRequest::getVar('unpublish_area', '');

		$count = count($moduleid);
		if ($count == 0){
			JText::printf('MSG_AJAX_ERROR', JText::_('MSG_AJAX_MOVE_ERROR'));
			jexit();
		}

		$model = $this->getModel('assignpages');

		switch($unpublish_area)
		{
			case 'all':
				for($i = 0; $i < $count; $i++){
					$model->removeAll( $moduleid[$i] );
				}
				if ($count == 1){
					JText::printf('MSG_AJAX_ASSIGNMENT_MODULE', '"'.JSNModules::getNameOfModule($moduleid).'"', 'unassigned', 'from', 'All Pages');
				}else{
					JText::printf('MSG_AJAX_MULTIPLE', $count, ' unassigned to all pages. ');
				}
				break;
			default:
			case 'one':
				for($i = 0; $i < $count; $i++){
					$model->unassignPages( $moduleid[$i], $pages );
				}
				if ($count == 1){
					JText::printf('MSG_AJAX_ASSIGNMENT_MODULE', '"'.JSNModules::getNameOfModule($moduleid[0]).'"', 'unassigned', 'from', $model->getPageName($pages[0]) );
				}else{
					JText::printf('MSG_AJAX_MULTIPLE', $count, ' unassigned to this page. ');
				}
				break;
		}

		jexit();
	}	
}
?>