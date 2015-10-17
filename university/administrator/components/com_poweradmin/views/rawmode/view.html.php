<?php
/**
 * @version     $Id: view.html.php 15828 2012-09-05 09:12:27Z hiepnv $
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class PoweradminViewRawmode extends JViewLegacy
{
	/**
	* Add toolbar for this view
	*/
	protected function addToolBar()
	{
		JToolbarHelper::title( JText::_('JSN_RAW_LAYOUT_MANAGER_TITLE'), 'poweradmin-rawmode' );

	}
	/**
	 *
	 * Add Scripts and StyleSheets for this view
	 * @param String $currentUrl
	 */
	protected function addMedia( $currentItemid, $render_url, $php_to_js )
	{
		$currentItemid = (int)$currentItemid;
		/** load libraries for the system rener **/
		$JSNTemplate = JSNFactory::getTemplate();
		$JSNMedia    = JSNFactory::getMedia();
		$template    = JFactory::getDocument()->template;

		$currUri = new JURI($render_url);

		JSNHtmlAsset::addStyle(JSN_POWERADMIN_STYLE_URI. 'uilayout/layout-default-latest.css');
		JSNHtmlAsset::addStyle(JSN_POWERADMIN_STYLE_URI. 'styles.css');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery.tinyscrollbar.js');

		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-hotkeys/jquery.hotkeys.js');

		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery-baseencode64.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-jstorage/jquery.jstorage.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.mousecheck.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.autodragdrop.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.rawmode.draganddrop.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.rawmode.component.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.rawmode.grid.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.functions.js');
		JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS. '/3rd-party/jquery-layout/js/jquery.layout-latest.js');

		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jstree/jstree.override.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.jstreecontext.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.submenu.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.menuitems.js');

		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.jquery.override.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.filter.js');
		//check sef on/off
		$sef = JFactory::getConfig()->get('sef');

		/** Add Custom Scripts **/
		$customScript = "
			var jsnpoweradmin = true;
			var baseUrl       = '".JURI::root()."';
			var sef           = ".$sef.";
			var currentUrl    = '".$render_url."';
			var lang          = '".$JSNMedia->getLang()."';
			var positions     = new Array();
			var JSNGrid, JSNComponent;

			(function($){
			    ".implode(PHP_EOL, $php_to_js)."
			    $.uiBackCompat = false;
				$(document).ready(function(){
					$('#page-loading').hide();
					$('#jsn-rawmode-layout').css('visibility', 'visible');
					if ($('#jsn-adminbar').size() == 0) {
						$('body').addClass('no-adminbar');
					}

					function setFullScreen () {
						$('body').toggleClass('jsn-fullscreen');
						if ($('body').hasClass('jsn-fullscreen')) {
							$('.header').hide();
							$('.subhead-collapse').hide();
						}
						else {
							$('.header').show();
							$('.subhead-collapse').show();
						}
						$(window).trigger('resize');
					}

					$('a#jsn-fullscreen').click(function () {
						setFullScreen();
						$.cookie('jsn-fullscreen', $('body').hasClass('jsn-fullscreen'));
						return false;
					});

					var isFullScreen = $.cookie('jsn-fullscreen');
					if (isFullScreen !== undefined && (isFullScreen == 'true' || isFullScreen == '1')) {
						setFullScreen();
					}

					$.jStorage.set('selected_node', ".$currentItemid.");
					$.ajaxSetup({
					   timeout: 10000
					});
					JSNGrid      = new $.JSNGrid();
					JSNComponent = new $.JSNComponent('".$currUri->getVar('option')."', '".$currUri->getVar('view')."', '".$currUri->getVar('layout')."', '".$currentItemid."');
 					$._menuitems.mode  = 'rawmode';
 					$._menuitems.init();
 					$.jsnmouse.init();
					JSNFilter	= new $.JSNSpotligthModuleFilter($('#module_spotlight_filter'), {defaultText: '" . JText::_('JSN_RAWMODE_MODULE_FILTER_DEFAULT_TEXT',true) ."'});
				});

			})(JoomlaShine.jQuery);
		";
		$JSNMedia->addScriptDeclaration( $customScript );
	}
	/**
	 * Display function
	 */
	public function display($tpl = null)
	{
		if (function_exists('error_reporting'))
		{
			error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);
		}
		/** load libraries for the system rener **/
		JSNFactory::localimport('libraries.joomlashine.mode.rawmode');
		JSNFactory::localimport('libraries.joomlashine.menu.menuitems');

		/** Assignment variables **/

		$jsntemplate  = JSNFactory::getTemplate();
		$jsnmenuitems = JSNMenuitems::getInstance();

		/** get url **/
		$render_url = JRequest::getVar('render_url', '');
		$session = JSession::getInstance('files', array('name'=>'jsnpoweradmin'));

		if ($render_url == '' && $session->get('rawmode_render_url')){
			$render_url = $session->get('rawmode_render_url');
		}
		$urlRender = base64_decode($render_url);
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
		$jsnrawmode = JSNRawmode::getInstance( $params );
 		$jsnrawmode->setParam('positions', $jsntemplate->loadXMLPositions());
 		$jsnrawmode->renderAll();

		$this->assign('component', $jsnrawmode->getHTML('component'));
		$this->assign('modules', $jsnrawmode->getHTML('positions'));
		$this->assign('jsnmenuitems', $jsnmenuitems);
		$this->assign('urlRender', $urlRender);
		/** add toolbar buttons **/
		$this->addToolBar();
		/** add scripts and css **/
		//$this->addMedia( $currUri->getVar('Itemid'), $urlString, array());//$jsnrawmode->getScript('positions', 'Array'));
		$this->addMedia( $currUri->getVar('Itemid'), $urlString, $jsnrawmode->getScript('positions', 'Array'));
		return parent::display();
	}
}