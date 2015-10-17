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

class PoweradminViewVisualmode extends JViewLegacy
{

	public function display($tpl = null)
	{
		/** add toolbar buttons **/
		$this->addToolBar();

		/** add scripts and css **/
		$this->addMedia();

		$render_url = JRequest::getVar('render_url', '');
		$this->assign('render_url', $render_url);

		//load libraries for the system rener modules mene
		JSNFactory::localimport('libraries.joomlashine.menu.menuitems');
		$jsnmenuitems = JSNMenuitems::getInstance();
		$this->assign('jsnmenuitems', $jsnmenuitems);

		return parent::display();
	}

	/**
	* Add toolbar for this view
	*/
	protected function addToolBar()
	{
		JToolBarHelper::title( JText::_('JSN_VISUAL_LAYOUT_MANAGER_TITLE') );
		JToolBarHelper::custom('', 'selecttemplate.png', 'selecttemplate.png', 'TOOLBAR_JSN_POWERADMIN_VISUAL_SELECTTEMPLATE', false);
		JToolBarHelper::divider();
		JToolBarHelper::custom('', 'icons-32/rawmode.png', 'icons-32/rawmode.png', 'TOOLBAR_JSN_POWERADMIN_VISUAL_RAWMODE', false);
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_JSN_POWERADMIN_VISUAL_MODE');
	}

	/**
	 *
	 * Add Scripts and StyleSheets for this view
	 */
	protected function addMedia()
	{
		$JSNMedia = JSNFactory::getMedia();

		/* require jsnpwTemplate class */
		$template = JSNFactory::getTemplate();
		$template_js_positions = $template->loadArrayJavascriptTemplatePositions( true );

		$JSNMedia->addStyleDeclaration(JSN_POWERADMIN_LIB_JSUILAYOUT_URI, 'layout-default-latest.css');
		$JSNMedia->addStyleDeclaration(JSN_POWERADMIN_STYLE_URI, 'styles.css');

		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSUILAYOUT_URI. 'jquery.layout-latest.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-hotkeys/jquery.hotkeys.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-jstree/jquery.jstree.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery.topzindex.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery-baseencode64.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-jstorage/jquery.jstorage.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.menuitems.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.visualmode.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.submenu.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.mousecheck.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.functions.js');

		//check sef on/off
		$sef = JFactory::getConfig()->get('sef');

		$customScript = "
		".$template_js_positions."
		var lang    = '".$JSNMedia->getLang()."';
		var baseUrl = '".JURI::root()."';
		var sef     = ".$sef.";

		(function($){
				$(document).ready(function(){
					$._visualmode.showLoading(true);
					$( '.jsn-toolbar-button' ).buttonset();
					$._visualmode.initLayout();
					$.jsnmouse.init();
					$._menuitems.init();
					$._menuitems.layoutResize();
					$._visualmode.init();
                    $(window).resize(function(){
                    	$._visualmode.initLayout();
                    });
                    $._visualmode.calculatorRate();
					//init status value and set it in to browser cookie
					var index = $.jStorage.index();
					if (!$.inArray('module_highlight', index)){
						$.jStorage.set('module_hightlight', false);
						$.jStorage.set('inactive_position', false);
						$.jStorage.set('unpublish_module', false);
						$.jStorage.set('add_new_module', false);
						$.jStorage.set('render_url', '');
					}
					/**
					 * Support call to iframe page
					 */
					$.autoDragDrop = function(_ops){
						window.frames.jsnrender.jQuery.autoDragDrop(_ops);
					};
				});
		})(JoomlaShine.jQuery);
		";

		$JSNMedia->addScriptDeclaration( $customScript );
	}
}