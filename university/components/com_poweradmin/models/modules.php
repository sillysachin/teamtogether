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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');
JSNFactory::localimport('libraries.joomlashine.modules');
/**
 * Banners model for the Joomla Banners component.
 *
 * @package		Joomla.Site
 * @subpackage	com_poweradmin
 * @since		1.6
 */
class PoweradminModelModules extends JModelList
{
	/**
	* get all modules in position 
	* @position: String - position of template
	* @attributes: String - Attributes will be set for modules in position
	* @return: String of modules in position after rendered
	*/
	public function renderModules( $position, $Itemid, $attributes = array(), $showmode, $published = '' )
	{
		$modules = JSNModules::getModules( $position, $Itemid, $published);

		$modulesHTML = array();
        $count = count($modules);
        
		if ($count){
			foreach($modules as $module){
				$modulesHTML[] = PoweradminFrontHelper::renderModule($module, $attributes, $showmode);
			}
		}

		return implode(PHP_EOL, $modulesHTML);		
	}
}