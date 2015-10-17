<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.html.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
/**
 * HTML Module View class for the Poweradmin component
 *
 * @package		Joomla.Site
 * @subpackage	com_poweradmin
 * @since 		1.6
 */
class PoweradminViewModules extends JView
{
	function display($tpl = null)
	{
		
		/** get params was set **/
		$params = JRequest::getVar('params', '');
		
		/** get showtype **/
		$showtype = strtolower(trim(JRequest::getVar('showtype', 'publish')));
		
		$app = JFactory::getApplication();
		$app->redirect( JRoute::_('index.php?option=com_poweradmin&view=modules&showtype='.$showtype.'&params='.$params, false) );		
		
		/** display of this view and show in template **/
		parent::display($tpl);
	}
}
