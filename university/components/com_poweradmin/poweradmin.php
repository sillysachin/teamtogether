<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: poweradmin.php 16460 2012-09-26 09:52:25Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include dependancies
jimport('joomla.application.component.controller');

//include defines
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_poweradmin' . DS . 'defines.poweradmin.php' ;
echo "test";
//load front-end helper 
JSNFactory::localimport('helpers.poweradmin', 'site');

// Execute the task.
$controller	= JController::getInstance('poweradmin');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
