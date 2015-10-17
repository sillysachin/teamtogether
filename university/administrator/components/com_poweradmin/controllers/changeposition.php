<?php
/**
 * @version     $Id: changeposition.php 16460 2012-09-26 09:52:25Z hiepnv $
 * @package     JSN_Poweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class PoweradminControllerChangeposition extends JControllerForm
{
	/**
	 * 
	 * Save setting position to the database
	 */
	public function setPosition(){
		error_reporting(0);
		$app = JFactory::getApplication();
		$moduleid = $app->getUserState( 'com_poweradmin.changeposition.moduleid', JRequest::getVar('moduleid', array(), 'get', 'array') );
		$position = JRequest::getVar('position', '');
		$model = $this->getModel('changeposition');
		for($i = 0; $i < count($moduleid); $i++){
			$model->setPosition($moduleid[$i], $position);
		}
		jexit();
	}
	/**
	 * 
	 * Redirect to position listing page
	 */
	public function selectPosition()
	{
		if(!JSNFactory::_cURLCheckFunctions()){
			$msg 	=	 JText::_('JSN_POWERADMIN_ERROR_CURL_NOT_ENABLED');
			$this->setRedirect('index.php?option=com_poweradmin&view=error&tmpl=component', $msg, 'error');
			$this->redirect();
		}
	
		global $templateAuthor, $notSupportedTemplateAuthors;
		if(in_array($templateAuthor,$notSupportedTemplateAuthors)){
			$msg 	=	 JText::_('JSN_POWERADMIN_ERROR_TEMPLATE_NOT_SUPPORTED');
			$this->setRedirect('index.php?option=com_poweradmin&view=error&tmpl=component',$msg);			
		}else{		
			$app = JFactory::getApplication();			
			$moduleid = JRequest::getVar('moduleid', array(), 'get', 'array');
			
			$appendRedirect = 'tmpl=component';
			
			$moduleid = explode(',', $moduleid[0]);
			$app->setUserState( 'com_poweradmin.changeposition.moduleid', $moduleid );
			$this->setRedirect('index.php?option=com_poweradmin&view=changeposition&'.$appendRedirect);
			$this->redirect();			
		}		

	}	
	
}
?>