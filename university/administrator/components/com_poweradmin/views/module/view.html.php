<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.html.php 15318 2012-08-21 08:44:11Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class PoweradminViewModule extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$JSNMedia = JSNFactory::getMedia();
		$JSNMedia->addStyleSheet(JSN_POWERADMIN_STYLE_URI. 'styles.css');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-hotkeys/jquery.hotkeys.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-jstorage/jquery.jstorage.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery.topzindex.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.submenu.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.mousecheck.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.functions.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery.tinyscrollbar.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.assignpages.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-jstree/jquery.jstree.js');
		$JSNMedia->addScriptDeclaration("var baseUrl = '".JURI::root()."';");

		//require helper
		JSNFactory::localimport('libraries.joomlashine.page.assignpages');
		$viewHelper = JSNAssignpages::getInstance();

		$menuTypes = $viewHelper->menuTypeDropDownList(false);
        $this->assign('menutypes', $menuTypes);

        $moduleid = JRequest::getVar('id', 0);
        $menuitems = $viewHelper->renderMenu($moduleid);
        $this->assign('menuitems', $menuitems);

        JSNFactory::localimport('libraries.joomlashine.modules');
        $assignType = JSNModules::checkAssign($moduleid);
        $this->assign('assignType', $assignType);

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		$language = JFactory::getLanguage();
		$language->load('com_modules');

		parent::display($tpl);
	}
}