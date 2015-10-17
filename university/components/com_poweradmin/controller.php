<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: controller.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Poweradmin Controller
 *
 * @package		Joomla
 * @subpackage	Poweradmin
 * @since		1.6
 */
class PoweradminController extends JController
{
	/**
	* Joomla display
	*/
	public function display( $tpl = '' )
	{
		parent::display($tpl);
	}
	
	/**
	 * 
	 * Ajax sef URL
	 */
	public function getRouterLink()
	{
		error_reporting(0);
		$config = JFactory::getConfig();
		if ($config->get('sef') == 1){
			$url = base64_decode(JRequest::getVar('link', ''));
			if ($url){
				$uri = new JURI($url);
				$query = $uri->getQuery();
				if ($query){
					$routeLink = JRoute::_('index.php?'.$query);
					echo base64_encode($routeLink);
				}else{
					echo base64_encode(JURI::root());
				}
			}
		}else{
			echo 'error';
		}
		jexit();
	}
}