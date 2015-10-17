<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.html.php 13493 2012-06-23 12:21:10Z thangbh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

class PoweradminViewComponent extends JViewLegacy
{

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$JSNMedia = JSNFactory::getMedia();
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI.'jsn.datas.validation.js');
		$JSNMedia->addScriptDeclaration("
			var baseUrl = '".JURI::root()."';
			(function($){
				$(window).ready(function(){
					$('#jsn-component-settings').textboxDataNumberic({maxValue:500});
					$('.apply-setting-area').click(function(){
						if ($(this).children('input').val() == 'globally'){
							$(this).children('input').val('only');
							$(this).children('span.symbol-only').show();
							$(this).children('span.symbol-globally').hide();
						}else{
							$(this).children('span.symbol-only').hide();
							$(this).children('span.symbol-globally').show();
							$(this).children('input').val('globally');
						}
					});
				});
			})(JoomlaShine.jQuery);
		");

		$JSNConfig = JSNFactory::getConfig();
		$params = $JSNConfig->getMenuParams( $app->getUserState('com_poweradmin.component.menuid', 0) );
		$JSNConfig->megreGlobalParams('com_content', $params, true);

		$this->assign('params', $params);
		return parent::display();
	}
}