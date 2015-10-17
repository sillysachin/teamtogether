<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.raw.php 12506 2012-05-09 03:55:24Z hiennh $
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
		@list($position, $attrs) = explode('||', base64_decode(JRequest::getVar('params', '')) );
		$attrs = explode(',', $attrs);
		$attributes = array();
		if (count($attrs)){
			foreach($attrs as $attr){
				@list($key, $val) = explode('=', $attr);
				$attributes[$key] = $val;
			}
		}else{
			$attributes['style'] = 'none';
		}
		
		/** get Itemid **/
		$currItemid = strtolower(trim(JRequest::getVar('currItemid', '')));
		$showmode   = strtolower(trim(JRequest::getVar('showmode', 'visualmode')));
		
		/** get showtype **/
		$showtype = strtolower(trim(JRequest::getVar('showtype', 'publish')));
		
		$model = $this->getModel();
		switch( $showtype )
		{
			case 'all':
				$modules = $model->renderModules( $position, $currItemid, $attributes, $showmode );
				break;
			case 'unpublish':
				$modules = $model->renderModules( $position, $currItemid, $attributes, $showmode, 0 );
				break;
			case 'publish':
			default:
				$modules = $model->renderModules( $position, $currItemid, $attributes, $showmode, 1 );
				break;
		}
		
		/** assignment HTML of modules for view **/
		$this->assign('modules', $modules);		
		
		/** display of this view and show in template **/
		parent::display($tpl);
	}
}
