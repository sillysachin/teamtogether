<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: javascriptlanguages.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
class JSNJavascriptLanguages
{
	//Array store all lang
	private $_langs;

	//Loaded lang to document
	private $_loaded;
	/**
	 *
	 * Get instance
	 *
	 * @param Array $params
	 */
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if ( empty($instances['JSNJavascriptLanguages']) ) {
			$instance	= new JSNJavascriptLanguages();
			$instance->_langs = Array();
			$instance->_loaded= false;
			$instances['JSNJavascriptLanguages'] = &$instance;

			//Load lang file
			$lang = JFactory::getLanguage();
			$lang->load('com_poweradmin');
		}

		return $instances['JSNJavascriptLanguages'];
	}
	/**
	 *
	 * Add to array
	 *
	 * @param String $key
	 */
	public function addLang( $key )
	{
		if (!is_array($this->_langs)){
			$this->_langs = Array();
		}

		if ( !key_exists($key, $this->_langs) ){
			$this->_langs[$key] = "JSNLang.add('".$key."', '".JText::_($key,true)."');";
		}
	}
	/**
	 *
	 * Load all JS lang to array
	 *
	 * @return: String js command lines add lang
	 */
	public function loadLang($loadDefault = true)
	{
		if ($loadDefault){
			$this->addLang('MSG_AUTO_DRAGDROP_ELEMENT_NOT_VALID');
			$this->addLang('MSG_AUTO_DRAGDROP');
			$this->addLang('MSG_AUTO_DRAGDROP_REDO');
			$this->addLang('TITLE_EDIT_MODULE');
			$this->addLang('TITLE_SUBMENU_EDIT');
			$this->addLang('TITLE_CHANGE_POSITION');
			$this->addLang('TITLE_PAGE_CHANGE_POSITION');
			$this->addLang('DEFAULT_TEXT_SEARCH', 'Search...');
			$this->addLang('DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE');
			$this->addLang('DEFAULT_TEXT_SEARCH_CHANGE_POSITION');
			$this->addLang('TITLE_SUBMENU_UNPUBLISH');
			$this->addLang('TITLE_SUBMENU_CHECKIN');
			$this->addLang('TITLE_SUBMENU_PUBLISH');
			$this->addLang('TITLE_SUBMENU_ASSIGN');
			$this->addLang('TITLE_SUBMENU_ASSIGN_TO_ALL_PAGES');
			$this->addLang('TITLE_SUBMENU_ASSIGN_ONLY_TO_THIS_PAGE');
			$this->addLang('TITLE_SUBMENU_ASSIGN_TO_ONLY_TO_CUSTOM_PAGE');
			$this->addLang('TITLE_CUSTOM_PAGE_ASSIGN');
			$this->addLang('TITLE_SUBMENU_UNASSIGN');
			$this->addLang('TITLE_SUBMENU_UNASSIGN_FROM_ALL_PAGES');
			$this->addLang('TITLE_SUBMENU_UNASSIGN_FROM_THIS_PAGE_ONLY');
			$this->addLang('TITLE_SUBMENU_UNASSIGN_CUSTOM_PAGES');
			$this->addLang('TITLE_UNASSIGN_CUSTOM_PAGES');
			$this->addLang('TITLE_SUBMENU_MORE');
			$this->addLang('TITLE_SUBMENU_DUPLICATE');
			$this->addLang('TITLE_SUBMENU_TRASH');
			$this->addLang('TITLE_SUBMENU_OPTIONS');
			$this->addLang('TITLE_MODULE_PAGE_OPTIONS');
			$this->addLang('CONFIRM_PAGE_EMPTY_COMPONENT');
			$this->addLang('TITLE_SELECT_MODULE_TYPE_PAGE');
			$this->addLang('TITLE_NEW_MODULE_PAGE');
			$this->addLang('TITLE_SELECT_TEMPLATE');
			$this->addLang('TITLE_SHOW_UNPUBLISHED_MENUITEMS');
			$this->addLang('TITLE_HIDE_UNPUBLISHED_MENUITEMS');
			$this->addLang('TITLE_SHOW_DISABLED_COMPONENT_ELEMENTS');
			$this->addLang('TITLE_HIDE_DISABLED_COMPONENT_ELEMENTS');
			$this->addLang('TITLE_SHOW_UNPUBLISHED_MODULES_POSITIONS');
			$this->addLang('TITLE_HIDE_UNPUBLISHED_MODULES_POSITIONS');
			$this->addLang('TITLE_EDIT_MENU_ITEM_PAGE');
			$this->addLang('CONFIRM_DELETE_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_SELECTITEM_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_EDIT_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_UNPUBLISH_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_PUBLISH_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_SUBPANEL_MORE');
			$this->addLang('TITLE_SUBMENU_CHECKIN_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_MAKEHOME_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_TRASH_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_REBUILD_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_SUBPANEL_EXPAND_ALL');
			$this->addLang('TITLE_SUBMENU_SUBPANEL_COLLAPSE_ALL');
			$this->addLang('TITLE_SUBMENU_ADD_MENU_ITEM');
			$this->addLang('TITLE_SUBMENUTITLE_EDIT');
			$this->addLang('TITLE_SUBPANEL_MORE');
			$this->addLang('TITLE_PAGE_MENU_SETTINGS');
			$this->addLang('TITLE_SUBMENUTITLE_DELETE');
			$this->addLang('CONFIRM_DELETE_MENU');
			$this->addLang('TITLE_SUBMENUTITLE_OPTIONS');
			$this->addLang('TITLE_SUBMENUTITLE_PAGE_OPTIONS');
			$this->addLang('TITLE_SUBMENU_SWITCH_EXPAND_ALL');
			$this->addLang('TITLE_SUBMENU_SWITCH_COLLAPSE_ALL');
			$this->addLang('TITLE_SUBMENU_SWITCH_ADD_MENU_ITEM');
			$this->addLang('TITLE_PAGE_MENUITEM_ADD_MENU');
			$this->addLang('TITLE_PAGE_MENUITEM_SELECT_MENU_TYPE');
			$this->addLang('TITLE_PAGE_MENUITEM_SELECT_MENU_TYPE_TEXT_SEARCH');
			$this->addLang('TITLE_PAGE_MENUITEM_ADD_MENU_ITEM');
			$this->addLang('CONFIRM_SELECT_EMPTY_MENU_ITEM');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_EXPAND_ALL');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_COLLAPSE_ALL');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_SELECT_ITEM');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_SELECT_ALL');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ALL');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ITEM');
			$this->addLang('TITLE_SUBMENU_ASSIGNPAGE_INVERT_SELECTION');
			$this->addLang('JSN_CONFIRM_LINK_RENDER');
			$this->addLang('JSN_MESSAGE_TITLE');
			$this->addLang('JSN_CONFIRM_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_EDIT_ARTICLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_EDIT_ARTICLE_PAGE_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY');
			$this->addLang('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY_PAGE_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR');
			$this->addLang('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR_PAGE_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_ONLY_THIS_PAGE');
			$this->addLang('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_GLOBAL_FOR_ALL_PAGES');
			$this->addLang('JSN_RAWMODE_COMPONENT_ENABLE_LINK');
			$this->addLang('JSN_RAWMODE_COMPONENT_DISABLE_LINK');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_GLOBAL_FOR_ALL_PAGES');
			$this->addLang('JSN_RAWMODE_COMPONENT_ICON_SHOW_ICON');
			$this->addLang('JSN_RAWMODE_COMPONENT_ICON_SHOW_TEXT');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_SET_ARTICLE_LAYOUT');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_SET_ARTICLE_LAYOUT_PAGE_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_EDITSETTINGS_READMORE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_EDITSETTINGS_READMORE_PAGE_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES_PAGE_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_SET_CONTENT_LAYOUT');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_SET_CONTENT_LAYOUT_TITLE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_FILTER');
			$this->addLang('JSN_RAWMODE_COMPONENT_HIDE');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_FILTER_VALUE_1');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_FILTER_VALUE_2');
			$this->addLang('JSN_RAWMODE_COMPONENT_ACTION_FILTER_VALUE_3');
			$this->addLang('JSN_CHANGE_POSITION_MODE_TEXTMODE');
			$this->addLang('JSN_CHANGE_POSITION_MODE_VISUALMODE');
			$this->addLang("JSN_RAWMODE_EDIT_NOTALLOWED_SHOW_HINT");
			$this->addLang("JSN_RAWMODE_EDIT_CHECKEDOUT_SHOW_HINT");
			$this->addLang('JSN_RAWMODE_POSITION_CONTEXTMENU_VIEWPOSITIONS');
			$this->addLang('JSN_RAWMODE_POSITION_CONTEXTMENU_ADDMODULE');
			$this->addLang('JSN_RAWMODE_VIEWPOSITIONS_PAGE_TITLE');
			//Add gui tips contents text
			$this->addLang("JSN_TIP_MENU_AREA");
			$this->addLang("JSN_TIP_MENU_ITEM");
			$this->addLang("JSN_TIP_MENU_MODE");
			$this->addLang("JSN_TIP_MODULE_AREA");
			$this->addLang("JSN_TIP_MODULE_ITEM");
			$this->addLang("JSN_TIP_MODULE_MODE");
			$this->addLang("JSN_TIP_COMPONENT_AREA");
			$this->addLang("JSN_TIP_COMPONENT_SUPPORTED");
			$this->addLang("JSN_TIP_COMPONENT_MODE");
			$this->addLang("JSN_TIP_MODULE_POSITION");
			$this->addLang('CONFIRM_DELETE_MODULE');
			$this->addLang('CONFIRM_DELETE_MODULE_MULTIPLE');
			$this->addLang('JSN_POWERADMIN_MENUASSETS_LOAD_CUSTOM_ASSETS');
		}
		if (!$this->_loaded){
			$this->_loaded = true;
			return PHP_EOL.implode(PHP_EOL, $this->_langs).PHP_EOL;
		}

		return;
	}
}
