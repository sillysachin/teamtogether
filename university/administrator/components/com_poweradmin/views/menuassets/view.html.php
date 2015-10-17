<?php
/**
 * @version     $Id$
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

/**
 * MenuAssets View class
 *
 * @package		Joomla.Site
 * @subpackage	com_poweradmin
 * @since 		1.6
 */
class PoweradminViewMenuassets extends JSNBaseView
{
	function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.js');
		JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI . 'jsn.jquery.noconflict.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI . 'menuassets/itemlist.js');
		$doc->addStyleSheet(JSN_POWERADMIN_STYLE_URI . 'menuassets.css');
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css');
		JSNHtmlAsset::addScript(JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/jquery.tipsy.js');
		$menuId = JRequest::getInt('id');
		require_once JPATH_ROOT . '/administrator/components/com_poweradmin/models/menuitem.php';

		$menuCss 	= PoweradminModelMenuitem::loadMenuCustomAssets($menuId, 'css');
		$cssFiles 	= count($menuCss) ? $menuCss->assets : array();

		$menuJs  	= PoweradminModelMenuitem::loadMenuCustomAssets($menuId, 'js');
		$jsFiles 	= count($menuJs) ? $menuJs->assets : array();

		$this->assign('cssFiles', $cssFiles);
		$this->assign('applyCssToChildren', $menuCss->legacy);
		$this->assign('jsFiles', $jsFiles);
		$this->assign('applyJsToChildren', $menuJs->legacy);
		$customScript = '
			(function ($){
				$(document).ready(function (){
					$(".control-label-withtip").tipsy({
						gravity: "w",
						fade: true
					});
					options = {
								inputName: "cssItems[]",
								handlerButton: $("#css-editor"),
								btnEditLabel: "' . JText::_('JSN_POWERADMIN_MENUASSETS_EDIT') . '",
								btnDoneLabel: "' . JText::_('JSN_POWERADMIN_MENUASSETS_DONE') . '",
								fileNotExistedTitle: "' . JText::_('JSN_POWERADMIN_MENUASSETS_NOT_EXISTED_TITLE') . '",
								baseUrl: "' . JURI::root() .'"
							}
					var cssList = new $.JSNItemList($("#css-item-list"), options);

					options = {
							inputName: "jsItems[]",
							handlerButton: $("#js-editor"),
							btnEditLabel: "' . JText::_('JSN_POWERADMIN_MENUASSETS_EDIT') . '",
							btnDoneLabel: "' . JText::_('JSN_POWERADMIN_MENUASSETS_DONE') . '",
							fileNotExistedTitle: "' . JText::_('JSN_POWERADMIN_MENUASSETS_NOT_EXISTED_TITLE') . '",
							baseUrl: "' . JURI::root() .'"
						}
					var jsList = new $.JSNItemList($("#js-item-list"), options);
				});
			})(JoomlaShine.jQuery);
		';
		$doc->addScriptDeclaration($customScript);
		parent::display($tpl);
	}
}
