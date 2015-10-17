<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: modules.php 12645 2012-05-14 07:45:58Z binhpt $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
JSNFactory::import('components.com_modules.controllers.modules');
JSNFactory::localimport('libraries.joomlashine.modules');
error_reporting(0);
class PoweradminControllerModules extends ModulesControllerModules
{
	/**
	 * 
	 * Rawmode load data json
	 */
	public function loadModulesJsonData()
	{
		JSNFactory::localimport('libraries.joomlashine.mode.rawmode');		
		$position = JRequest::getVar('position', '');
		$Itemid   = JRequest::getVar('currItemid', 0);
		
		$jsnrawmode = JSNRawmode::getInstance();
		$jsnrawmode->setParam('Itemid', $Itemid);
		$jsnrawmode->renderPosition($position);
        echo $jsnrawmode->getScript('position', 'JSON', $position);
	    jexit();
	}
}