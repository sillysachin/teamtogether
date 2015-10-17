<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: rawmode.php 16006 2012-09-13 03:29:17Z hiepnv $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');
error_reporting(0);
class PoweradminControllerRawmode extends JControllerForm
{
	/**
	 *
	 * Ajax render to store in session
	 */
	public function ajaxGetRender()
	{
		/** load libraries for the system rener **/
		JSNFactory::localimport('libraries.joomlashine.mode.rawmode');
		JSNFactory::localimport('libraries.joomlashine.menu.menuitems');
		/** get url **/
		$render_url = JRequest::getVar('render_url', '');
		$urlRender  = base64_decode($render_url);
		$session    = JSession::getInstance('files', array('name'=>'jsnpoweradmin'));
		if ( $render_url == '' ){
			$urlRender = JSNDatabase::getDefaultPage()->link;
		}
		$currUri = new JURI($urlRender);
		if ( !$currUri->hasVar('Itemid') ){
			$currUri->setVar('Itemid', JSNDatabase::getDefaultPage()->id);
		}
		$urlString = $currUri->toString();
		$session->set('rawmode_render_url', base64_encode($urlString));
		$parts = JString::parse_url( $urlString );
		if ( !empty($parts['query']) ){
			parse_str($parts['query'], $params);
		}else{
			$params = array();
		}
		$jsntemplate = JSNFactory::getTemplate();
		$jsnrawmode = JSNRawmode::getInstance( $params );
		$jsnrawmode->setParam('positions', $jsntemplate->loadXMLPositions());
		$jsnrawmode->renderAll();

		$session = JSession::getInstance('files', array('name'=>'jsnajaxgetrender'));
		$session->set('component', $jsnrawmode->getHTML('component'));
		$session->set('jsondata',  $jsnrawmode->getScript('positions', 'JSON'));
		jexit('success');
	}
    /**
    *
    * Ajax load data render
    */
	public function getSessionData()
	{
		$name = JString::strtolower(JRequest::getVar('session_name', ''));
		$session = JSession::getInstance('files', array('name'=>'jsnajaxgetrender'));
		switch ($name)
		{
			case 'component':
				echo $session->get('component');
				$session->set('component', '');
				break;
			case 'jsondata':
				echo $session->get('jsondata');
				$session->set('jsondata', '');
				break;
		}
		jexit();
	}


}
?>