<?php
/**
 * @version    $Id$
 * @package    JSNPoweradmin
 * @subpackage Item
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

jimport('joomla.application.component.controllerform');
error_reporting(0);
/**
 * @package		Joomla.Site
 * @subpackage	com_poweradmin
 */
class PoweradminControllerMenuitem extends JControllerForm
{
	/**
	 * 
	 * Delete menu
	 */
	public function deleteMenu()
	{
		$menuid = (int)JRequest::getVar('menuid');
		$res    =  $this->getModel('menuitem')->deleteMenu($menuid);
		$msg    = ($res)?$menuid.'||success':'error';
		jexit($msg);
	}
	
	/**
	 * 
	 * Rebuild menu 
	 */
	public function rebuild()
	{
		$mid = JRequest::getVar('mid', '');
		$res   =  $this->getModel('menuitem')->rebuild($mid);
		$msg   = ($res)?'success':'error';
		jexit($msg);
	}
	
	/**
	 * 
	 * Get menus and items
	 */
	public function getMenus()
	{
		//load libraries for the system rener menu
		JSNFactory::localimport('libraries.joomlashine.menu.menuitems');
		$jsnmenuitems = JSNMenuitems::getInstance();
		echo $jsnmenuitems->render();
		jexit();
	}
	
	/**
	 * 
	 * Get menu
	 * 
	 * @return: menu
	 */
	public function getMenu()
	{
		$mid = trim(JRequest::getVar('mid', ''));
		//load libraries for the system rener modules mene
		JSNFactory::localimport('libraries.joomlashine.menu.menuitems');
		$menutype = $this->getModel('menuitem')->getMenuType($mid);
		if ($menutype){
			$menutitle = $this->getModel('menuitem')->getMenuTitle($mid);
			$jsnmenuitems = JSNMenuitems::getInstance();
			echo $jsnmenuitems->renderMenu( $mid, $menutype, $menutitle );
		}else{
			echo 'error';
		}
		jexit();
	}
	
	/**
	 * Render menu
	 */
	public function getMenuType()
	{
		$mid = trim(JRequest::getVar('mid', ''));
		//load libraries for the system rener modules mene
		JSNFactory::localimport('libraries.joomlashine.menu.menuitems');
		$menutype = $this->getModel('menuitem')->getMenuType($mid);
		if ($menutype){
			$jsnmenuitems = JSNMenuitems::getInstance();
			$menutitle    = $this->getModel('menuitem')->getMenuTitle($mid);
			echo $jsnmenuitems->renderMenuItem( $mid, $menutype, $menutitle );
		}else{
			echo 'error';
		}
		jexit();
	}
	
	/**
	 * 
	 * Publish/Unpublish menu item
	 */
	public function menuitempublishing()
	{
		$itemid  = (int)JRequest::getVar('itemid', 0);
		$publish = trim(JRequest::getVar('publish', 'Unpublish'));
		$publish = ($publish == 'Publish')?1:0;
		$res     =  $this->getModel('menuitem')->publishing($itemid, $publish);
		$msg     = ($res)?'success':'error';
		jexit($msg);
	}
	
	/**
	 * 
	 * Check in menu item
	 */
	public function checkinmenuitem()
	{
		$itemid = (int)JRequest::getVar('itemid', 0);
		$res    =  $this->getModel('menuitem')->checkin( $itemid );
		$msg    = ($res)?'success':'error';
		jexit($msg);
	}
	
	/**
	 * 
	 * Delete menu item
	 */
	public function deletemenuitem()
	{
		$itemid = (int)JRequest::getVar('itemid', 0);
		$res    =  $this->getModel('menuitem')->delete($itemid);
		$msg    = ($res)?'success':'error';
		jexit($msg);
	}
	
   /**
	 * 
	 * Trash menu item
	 */
	public function trashmenuitem()
	{
		$itemid = (int)JRequest::getVar('itemid', 0);
		$res    =  $this->getModel('menuitem')->trash($itemid);
		$msg    = ($res)?'success':'error';
		jexit($msg);
	}
	
	/**
	 * 
	 * Set default menu item
	 */
	public function setdefault()
	{
		$itemid = (int)JRequest::getVar('itemid', 0);
		$res    =  $this->getModel('menuitem')->setHome($itemid);
		$msg    = ($res)?'success':'error';
		jexit($msg);
	}
	
	/**
	 * 
	 * Rebuild menu item
	 */
	public function rebuilditem()
	{
		$itemid = (int)JRequest::getVar('itemid', 0);
		$res    = $this->getModel('menuitem')->rebuilditem($itemid);
		$msg    = ($res)?'success':'error';
		jexit($msg);
	}
	
	/**
	 * Move menu item
	 */
	public function moveItem()
	{
		$itemid = (int)JRequest::getVar('itemid', 0);
		$menu = JTable::getInstance('menu');
		$menu->load($itemid);
		$menutype = $menu->menutype;
		$parentid = (int)JRequest::getVar('parentid', 1);
		$orders   = JRequest::getVar('orders', array(), '', 'array');
		
		$res = $this->getModel('menuitem')->moveItem($itemid, $parentid, $orders);		
		
		//print message
		$msg = ($res)?$menutype.'|success':'error';
		jexit($msg);
	}
	
	/**
	 * Save custom assets for each menu item
	 */
	public function saveAssets()
	{
		$menuId = JRequest::getInt('id');		
		$cssPlainFiles 	= JRequest::getString('cssPlainFiles');
		$cssPlainFiles 	= explode("\n", $cssPlainFiles);
		$cssChosenFiles = JRequest::getVar('cssItems');
		$applyCssToChildren = JRequest::getVar('cssSameLevelApply');
		
		$jsPlainFiles = JRequest::getString('jsPlainFiles');		
		$jsPlainFiles = explode("\n", $jsPlainFiles);
		$jsChosenFiles = JRequest::getVar('jsItems');
		$applyJsToChildren = JRequest::getVar('jsSameLevelApply');
		
		$assets = array();
		$css = array();
		$js	 = array();
		
		for($i = 0; $i < count($cssPlainFiles); $i++){
			$cssFile = str_replace(array("'","\""), "", trim($cssPlainFiles[$i]));	
			if($cssFile){
				if(in_array($cssFile, $cssChosenFiles)){
					$css[$cssFile]['loaded'] = "true";
				}else{
					$css[$cssFile]['loaded'] = "false";
				}	
			}			
		}
		
		for($i = 0; $i < count($jsPlainFiles); $i++){
			$jsFile = str_replace(array("'","\""), "", trim($jsPlainFiles[$i]));
			if($jsFile){
				if(in_array($jsFile, $jsChosenFiles)){
					$js[$jsFile]['loaded'] = "true";
				}else{
					$js[$jsFile]['loaded'] = "false";
				}	
			}			
		}
		
		$css = count($css) ? json_encode($css) : '';
		$js  = count($js) ? json_encode($js) : '';
				
		$model = $this->getModel('menuitem');

		if(isset($applyCssToChildren) || isset($applyJsToChildren)){
			$_menu 		= $model->getMenuItem($menuId);
			$parentId 	= $_menu->parent_id; 
			$menuType	= $_menu->menutype;
			$_sameLevelItems = $model->getSameLevelMenuItems($parentId, $menuType);
			$sameLevelItemsArr = array();
			for($i = 0; $i < count($_sameLevelItems); $i++){
				array_push($sameLevelItemsArr, $_sameLevelItems[$i]->id);
			}
		}
		
		$model->saveMenuAssets($menuId, $css, 'css', isset($applyCssToChildren));
		
		$model->saveMenuAssets($menuId, $js, 'js', isset($applyJsToChildren));
				
		jexit('success');				
	}
	
	/**
	 * Method to check if asset file existed
	 *  
	 */
	public function checkAssetFile()
	{
		error_reporting(0);
		$url = JRequest::getString('url');
		if(strpos($url, 'http') === false){
			$url = JURI::root() . $url;
		}
		$headers = get_headers($url);
		if(!count($headers)){
			jexit('false');
		}else{
			$_httpHeader = $headers[0];
			if(strpos($_httpHeader, '200') != false &&  strpos($_httpHeader, 'OK') != false){
				jexit('true');
			}
		}
		
		jexit('false');
	}
}
