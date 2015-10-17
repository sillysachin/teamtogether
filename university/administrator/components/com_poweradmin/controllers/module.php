<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: module.php 15868 2012-09-06 09:35:41Z hiepnv $
-------------------------------------------------------------------------*/

//// no direct access
defined('_JEXEC') or die;

JSNFactory::import('components.com_modules.controllers.module');
JSNFactory::localimport('libraries.joomlashine.modules');
error_reporting(0);
class PoweradminControllerModule extends ModulesControllerModule
{
	/**
	 * 
	 * Redirect to edit module
	 */
	public function edit()
	{
		$editId = JRequest::getVar('id', 0, 'int');
		$this->setRedirect('index.php?option=com_poweradmin&view=module&layout=edit&tmpl=component&id='.$editId);
		$this->redirect();
	}
	/**
	 * 
	 * Duplicate module
	 * @throws Exception
	 */
	public function duplicate()
	{
		// Initialise variables.
		$pks = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($pks);

		try {
			if (empty($pks)) {
				throw new Exception(JText::_('COM_MODULES_ERROR_NO_MODULES_SELECTED'));
			}else{
				$model = $this->getModel();
				if ($model->duplicate($pks)){
					 JText::printf('MSG_AJAX_DUPLICATE', '"'.JSNModules::getNameOfModule($pks[0]).'"', $model->getState('module.id')); 
				}else{
					JText::printf('MSG_AJAX_ERROR', $this->getError());
				}
			}
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		jexit();
	}
	
	/**
	 * 
	 * Trash module
	 * @throws Exception
	 */
	public function trash()
	{
		// Initialise variables.
		$pks = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($pks);

		try {
			if (empty($pks)) {
				throw new Exception(JText::_('COM_MODULES_ERROR_NO_MODULES_SELECTED'));
			}
			$model = $this->getModel();
			$moduleName = JSNModules::getNameOfModule($pks[0]);
			if ($model->delete($pks)){
				 JText::printf('MSG_AJAX_TRASH', '"'.$moduleName.'"'); 
			}else{
				 JText::printf('MSG_AJAX_ERROR', $this->getError());
			}
		} catch (Exception $e) {
			JError::raiseWarning(500, $e->getMessage());
		}

		jexit();
	}

	/**
	 * (non-PHPdoc)
	 * @see libraries/joomla/application/component/JControllerForm::save()
	 */
	public function save()
	{
		$assignment = JRequest::getVar('assignment', 1, 'int');
		$moduleid   = JRequest::getVar('id', 0, 'int'); 

		$data      = JRequest::getVar('jform', array(), 'post', 'array');
		$moduleid = $this->getModel('module')->save($data);
		
		$assignModel = $this->getModel('assignpages');
		//print_r($_POST);
		
		switch($assignment)
		{
			case 0:
				$assignModel->removeAll($moduleid);
				break;
			case 1:
				$assignModel->assignToAllPages($moduleid);
				break;
			case 2:
				//assign to all page except selected pages
				$pages = JRequest::getVar('assignpages', array(), 'post', 'array');
				$assignModel->assignPages($moduleid, $pages, true);
				break;
			case 3:
				//assign to selected pages
				$pages = JRequest::getVar('assignpages', array(), 'post', 'array');
				$assignModel->assignPages($moduleid, $pages);
				break;
			default:
				JError::raiseNotice(E_NOTICE, 'Assignment empty.');
				break;
		}
		$redirectPage = 'index.php?option=com_poweradmin&view=module&layout=edit&tmpl=component&id='.$moduleid;
	    $this->setRedirect($redirectPage);
	}
	
   /**
	* This function to change position of module in database
	*
	* @return: Change to database
	*/
	function moveModule()
	{
		$moduleid = JRequest::getVar('moduleid', '');
		$oldPosition = JRequest::getVar('oldposition', '');
		$newPosition = JRequest::getVar('newposition', '');
		$order	= JRequest::getVar('order', array(), '', 'array');

		if ( (int) $moduleid <= 0 || $newPosition == ''){
			JText::printf('MSG_AJAX_ERROR', JText::_('MSG_AJAX_MOVE_ERROR'));
		}else{
			if (JSNModules::moveModule( $moduleid, $newPosition, $order)){
				JText::printf( 'MSG_AJAX_MOVE_MODULE_SUCCESS', '"'.JSNModules::getNameOfModule($moduleid).'"', '"'.$newPosition.'"');
			}
		}
		jexit();
	}
	
    /**
	 * 
	 * Show/Hide module title
	 */
	public function showTitle()
	{
		$showtitle = JRequest::getVar('showtitle', '');
		$moduleid  = JRequest::getVar('moduleid', 0);
		if ($showtitle != '' && ((int)$showtitle >= 0 && (int) $moduleid > 0)){
			JSNModules::showTitle($moduleid, $showtitle);
		}
		jexit(JText::_('MSG_AJAX_SAVE_SETTING'));
	}
	
	/**
	 * 
	 * Publish module
	 */
	public function publish()
	{
		$moduleid = JRequest::getVar('moduleid', array(), 'post', 'array');
		$count = count($moduleid);
		for($i = 0; $i < $count; $i++){
			JSNModules::publish( $moduleid[$i] );
		}
		if ($count == 1){
			JText::printf('MSG_AJAX_PUBLISHING_MODULE', '"'.JSNModules::getNameOfModule($moduleid[0]).'"', 'published');
		}else{
			JText::printf('MSG_AJAX_MULTIPLE', $count, 'published');
		}
		jexit();
	}
	
	/**
	 * 
	 * Unpublish module
	 */
	public function unpublish()
	{
		$moduleid = JRequest::getVar('moduleid', array(), 'post', 'array');
		$count = count($moduleid);
		for($i = 0; $i < $count; $i++){
			JSNModules::unpublish( $moduleid[$i] );
		}
		if ($count == 1){
			JText::printf('MSG_AJAX_PUBLISHING_MODULE', '"'.JSNModules::getNameOfModule($moduleid[0]).'"', 'published');
		}else{
			JText::printf('MSG_AJAX_MULTIPLE_PUBLISHING', $count, 'published');
		}
		jexit();
	}
	
	/**
	 * Check in
	 */
	
	public function checkin()
	{
		$moduleid = JRequest::getVar('moduleid', array(), 'post', 'array');		
		$count = count($moduleid);
		for($i = 0; $i < $count; $i++){
			JSNModules::checkin( $moduleid[$i] );
		}
		jexit();
	}
}