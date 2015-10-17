<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.raw.php 12795 2012-05-21 02:35:16Z binhpt $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class PoweradminViewJsnrender extends JViewLegacy
{
	public function display($tpl = null)
	{
		//load libraries for the system rener
		JSNFactory::localimport('libraries.joomlashine.mode.render');

		$url = base64_decode(JRequest::getVar('render_url', ''));
		if ( $url == ''){
			$url = JURI::root().'index.php';
		}
		JRequest::setVar('layout', 'default');

		$jsnpwrender = JSNRender::getInstance( $url, 'visualmode' );
		$this->assign('jsnpwrender', $jsnpwrender);

		$this->addScripts();

		parent::display();
	}

	/**
	* Add jquery files
	* @return: Array
	*/
	protected function addScripts()
	{
		$JSNMedia = JSNFactory::getMedia();
		$template = JSNFactory::getTemplate();
		$template_js_positions = $template->loadArrayJavascriptTemplatePositions( true );

		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jquery-baseencode64.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.mousecheck.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.autodragdrop.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.submenu.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.visualmode.draganddrop.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.visualmode.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.showblock.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI. 'jstorage.js');

		$currentUrlInos = $this->jsnpwrender->getCurrentUrlInfos();
		$showTemplatePosition = $currentUrlInos->showTemplatePosition?'true':'false';

		$customscripts = "
			".$template_js_positions."
			var jsnpoweradmin = true;
			var baseUrl       = '".JURI::root()."';
			var lang          = '".$JSNMedia->getLang()."';
			var currItemid    = '".$this->jsnpwrender->getCurrentItemid()."';

			(function($){
				$(document).ready(function(){
					if (".$showTemplatePosition."){
						$('.poweradmin-module-item').each(function(){
							var el = $(this);
							if ( el.attr('id').split('-')[0] == '0'){
								if (el.parent().find('.poweradmin-module-item').length == 1){
									el.parent().addClass('inactive-position');
									el.parent().html('<label class=\"jsn-position-name\">'+el.parent().attr('id').replace('-jsnposition', '')+'</label><a class=\"add-new-module\" title=\"Add new module to this position.\" href=\"javascript:;\"></a>');
								}
								el.remove();
							}else{
								if (el.find('.mod-preview-wrapper').length > 0){
									var moduleContent = $('.mod-preview-wrapper', el).html();
									el.children().html( moduleContent );
								}
							}
						});
					}
					if (typeof window.parent !== undefined){
						if (typeof window.parent.jQuery._visualmode.changeToolbar == 'function' ){
							window.parent.jQuery._visualmode.changeToolbar('".$currentUrlInos->urlString."');
						}

						if (typeof window.parent.jQuery._visualmode.jsnRenderReady == 'function' ){
							window.parent.jQuery._visualmode.jsnRenderReady();
						}
					}
					$.jsnmouse.init();
					//no show browser context menu
	    			$('body')[0].oncontextmenu = function() {
						return false;
					}
				});

			 })(JoomlaShine.jQuery);";

		$JSNMedia->addScriptDeclaration( $customscripts );

		$this->assign('JSNMedia', $JSNMedia);
	}
}