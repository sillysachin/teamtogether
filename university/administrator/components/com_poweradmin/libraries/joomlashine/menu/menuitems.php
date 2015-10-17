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


jimport('joomla.application.menu');
JSNFactory::localimport('libraries.joomlashine.html');
JSNFactory::localimport('libraries.joomlashine.database');

/**
 * JSNMenuitems class
 *
 * @package		Joomla.Framework
 * @subpackage	com_poweradmin ( Power By JoomlaShine - joomlashine.com )
 * @since		1.6 - 1.7
 */
class JSNMenuitems extends JSNDatabase{
	
	/** 
	 * 
	 * Array menu module
	 * @param Array
	 */	
	protected $_menus = array();
	
	/**
	 * Contructure for this class
	 * 
	 * @params: Array $options
	 */
	public function __construct( $options = array() )
	{
		parent::__construct($options);
		$this->_menus = parent::getMenus();
	}
	
	/**
	 * Return global JSNMenuitems object
	 * 
	 * @param: String of url	 
	 *  
	 */
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}
		
		if (empty($instances['JSNMenuitems'])) {
			$instance	= new JSNMenuitems();
			$instances['JSNMenuitems'] = &$instance;
		}

		return $instances['JSNMenuitems'];
	}

	public function getMenuTypes ()
	{
		return $this->getMenus();
	}
	
	/**
	 * 
	 * Get dropdown list menu type listing
	 * 
	 * @return: HTML elements
	 */
	public function menuTypeDropDownList()
	{
		if (!count($this->_menus)){ 
			$this->_menus = $this->getMenus();
		}
		$HTML          = array();
		$dropDownList  = array();
		$titleLists    = array();
		$dropDownList[] = JSNHtmlHelper::openTag('div', array('id' => 'jsn-menutypes'));
		$dropDownList[] = JSNHtmlHelper::openTag('div', array('class' => 'jsn-scrollable'));
		$dropDownList[] = JSNHtmlHelper::openTag('div', array('class' => 'viewport'));
		$dropDownList[] = JSNHtmlHelper::openTag('div', array('class' => 'overview'));
		$dropDownList[] = JSNHtmlHelper::openTag('ul', array('class'=>'jsn-menu-dropdown-lists', 'id'=>'jsn-menu-dropdown-list', 'next-id'=>$this->getNextMenuTypeId()));
		for( $i = 0; $i < count($this->_menus); $i++){
			$titleLists[$i] = JSNHtmlHelper::openTag('div', array('class'=>'jsn-menutype-title',
																	'id'=>'jsn-menutype-title-'.$this->_menus[$i]->id,
																	'menutype'=> $this->_menus[$i]->menutype,
																	'menuid'=>$this->_menus[$i]->id,
																	'menutitle'=>$this->_menus[$i]->title));

			$titleLists[$i] .= '<div class="btn-group">';
			$titleLists[$i] .= JSNHtmlHelper::openTag('btn', array('class'=>'btn menutype-title',
																'menutype'=> $this->_menus[$i]->menutype,
																'menuid'=>$this->_menus[$i]->id,
																'menutitle'=>$this->_menus[$i]->title));
			$titleLists[$i] .= $this->_menus[$i]->title;
			$titleLists[$i] .= JSNHtmlHelper::closeTag('btn');
			$titleLists[$i] .= '<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
								<span class="caret"></span>
								</button>';
			$titleLists[$i] .= '</div>';

			$titleLists[$i] .= JSNHtmlHelper::closeTag('div');
		    $dropDownList[] = JSNHtmlHelper::openTag('li', array('id'=>'dropdownmenutype-'.$this->_menus[$i]->id))
		                 .JSNHtmlHelper::openTag('a', array('class'=>'text', 'href'=>'javascript:void(0)'))
		                    .$this->_menus[$i]->title
		                 .JSNHtmlHelper::closeTag('a')
		              .JSNHtmlHelper::closeTag('li');
		}
		$dropDownList[] = JSNHtmlHelper::closeTag('ul');
		$dropDownList[] = JSNHtmlHelper::closeTag('div');
		$dropDownList[] = JSNHtmlHelper::closeTag('div');
		$dropDownList[] = '<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>';
		$dropDownList[] = '<div class="clearfix"></div>';
		$dropDownList[] = JSNHtmlHelper::closeTag('div');

		$dropDownList[] = JSNHtmlHelper::openTag('div', array('id'=>'add-new-menu'))
		             .JSNHtmlHelper::openTag('a', array('class'=>'text', 'href'=>'javascript:void(0)'))
		                .JText::_('JSN_POWERADMIN_ADD_NEW_MENU',true)
		             .JSNHtmlHelper::closeTag('a')
		           .JSNHtmlHelper::closeTag('div');
		$dropDownList[] = JSNHtmlHelper::closeTag('div');
		$titleLists[]   = JSNHtmlHelper::openTag('span', array('class' => 'dropdown-arrow') ).JSNHtmlHelper::closeTag('span');
		// $dropDownList[] = JSNHtmlHelper::closeTag('ul');
		$HTML  = JSNHtmlHelper::openTag('div', array('class'=>'jsn-menu-selector jsn-menu-selector-disabled', 'id' => 'jsn-rawmode-menu-selector'));
		$HTML .= implode(PHP_EOL, $titleLists).implode(PHP_EOL, $dropDownList);
		$HTML .= JSNHtmlHelper::closeTag('div');
		return $HTML; 
	}
	/**
	 * Return LEFT panel, modules and items in module
	 * 
	 * @return: HTML
	 */
	public  function render()
	{
		$elements = array();
		for( $i = 0; $i < count($this->_menus); $i++){
			$items = $this->getItems( $this->_menus[$i]->menutype, 1);
			$elements[$i]  = JSNHtmlHelper::openTag('div', array('class'=>'jsn-menu-details', 'id'=>'jsn-menu-details-'.$this->_menus[$i]->id));			
			$elements[$i] .= JSNHtmlHelper::openTag('div', array('class'=>'jsn-menuitem-assignment', 'id'=>'jsn-menu-elements-'.$this->_menus[$i]->id));										  		  
			$elements[$i] .= JSNHtmlHelper::openTag('ul', array('id'=>'parentid-1', 'class'=>'jsn-jstree'));
			$elements[$i] .= $this->renderItems( $items, $this->_menus[$i]->id );
			$elements[$i] .= JSNHtmlHelper::closeTag('ul');
			$elements[$i] .= JSNHtmlHelper::closeTag('div');
			$elements[$i] .= JSNHtmlHelper::closeTag('div');
		}
		
		return implode(PHP_EOL, $elements);
	}
	/**
	 * 
	 * Render menu
	 * 
	 * @param (Number) $id
	 * @param (String) $menutype
	 * @param (String) $menutitle
	 * @return: HTML
	 */
	public function renderMenu($id, $menutype, $menutitle)
	{
		$elements = array();
		$items = $this->getItems( $menutype, 1);
		$elements[] = JSNHtmlHelper::openTag('div', array('class'=>'jsn-menu-details', 'id'=>'jsn-menu-details-'.$id));
		$elements[] = JSNHtmlHelper::openTag('div', array('class'=>'jsn-menuitem-assignment', 'id'=>'jsn-menu-elements-'.$id));					  		  
		$elements[] = JSNHtmlHelper::openTag('ul', array('id'=>'parentid-1', 'class'=>'jsn-jstree'));
		$elements[] = $this->renderItems( $items, $id );
		$elements[] = JSNHtmlHelper::closeTag('ul');
		$elements[] = JSNHtmlHelper::closeTag('div');
		$elements[] = JSNHtmlHelper::closeTag('div');
		return implode(PHP_EOL, $elements);
	}
	
	/**
	 * 
	 * Render an menu
	 * @param String $menutype
	 * @param int $id
	 */
	public function renderMenuItem( $id, $menutype, $menutitle )
	{
		$items  = $this->getItems( $menutype, 1);
		$menu   = JSNHtmlHelper::openTag('div', array('class'=>'jsn-menuitem-assignment', 'id'=>'jsn-menu-elements-'.$id));
		$menu  .= JSNHtmlHelper::openTag('ul',  array('id'=>'parentid-1', 'class'=>'jsn-jstree')).$this->renderItems( $items, $id ).JSNHtmlHelper::closeTag('ul');
		$menu  .= JSNHtmlHelper::closeTag('div');
		return $menu;
	}
	
	/**
	 * Return HTML, subitems in menu
	 * 
	 * @param: Array items
	 * @param: int $menuid
	 */
	protected function renderItems( $mItems, $menuid )
	{
		$items = '';
		if ( count($mItems) ){
			for( $i = 0; $i < count($mItems); $i++){
				$publish = ($mItems[$i]->published == 1)?'Unpublish':'Publish';
				$class_unpublish = ($mItems[$i]->published == 0)?' unpublish':'';
				$default = ($mItems[$i]->home == 1)?' default':'';
				$defaultIndicator = ($mItems[$i]->home == 1)?' <ins class="menu-default-indicator"></ins>':'';

				$uri = new JURI($mItems[$i]->link);
				$link = $uri->toString();
				if (!JURI::isInternal($link)){
					if (JString::strtolower($link) != JString::strtolower($_SERVER['HTTP_HOST'])){
						$link = '#';
					}
				}else if($mItems[$i]->link == '#'){
					$link = '#';
				}else{
					$link = JURI::root().$link;
				}
				if ($link != '#'){
					if (strpos($link, '?') === false){
						$link .= '?Itemid='.$mItems[$i]->id;
					}else{
						$link .= '&Itemid='.$mItems[$i]->id;
					}
				}
				
 				if ( $mItems[$i]->type == 'alias' ){
					jimport('joomla.html.parameter');
 					$aliasparams =  new JRegistry(); 					
 					$aliasparams->loadString( $mItems[$i]->params );
 					$address_itemid = $aliasparams->get('aliasoptions');
					if ( (int) $address_itemid > 0 ){						
						$address_item = $this->getMenuItem($address_itemid);
						if ( is_object($address_item)){
							$link = $address_item->link;
						}else if ( is_array($address_item)) {
							$link = $address_item['link'];
						}
						
						if ( strpos($link, '?') === false ){
							$link .= '?aliasoptions='.$address_itemid.'&Itemid='.$mItems[$i]->id;
						}else{
							$link .= '&aliasoptions='.$address_itemid.'&Itemid='.$mItems[$i]->id;
						}
						$mItems[$i]->link = $link;
					}
 				}

				if ( $this->hasChild( $mItems[$i]->id )){
					$subItems = $this->getItems($mItems[$i]->menutype, $mItems[$i]->id);
					$items   .= JSNHtmlHelper::openTag('li', array('id'=>$publish.'-menutypeid'.$menuid.'-'.$mItems[$i]->id, 'class'=>'moveable'))
					              .JSNHtmlHelper::openTag('a', array('onClick'=>'javascript:void(0);', 'class'=>$default.$class_unpublish, 'link'=>$link, 'title' => $this->getMenuItemType($mItems[$i]->link)))
					                .$mItems[$i]->title
					                .$defaultIndicator
					              .JSNHtmlHelper::closeTag('a');
					$items   .= JSNHtmlHelper::openTag('ul', array('class'=>'jsn-menu-items', 'style'=>'display:none;', 'id'=>'parentid-'.$mItems[$i]->id)).$this->renderItems( $subItems, $menuid ).JSNHtmlHelper::closeTag('ul');
					$items   .= JSNHtmlHelper::closeTag('li');
				}else{
					$items   .= JSNHtmlHelper::openTag('li', array('id'=>$publish.'-menutypeid'.$menuid.'-'.$mItems[$i]->id, 'class'=>'moveable'))
					             .JSNHtmlHelper::openTag('a', array('onClick'=>'javascript:void(0);', 'class'=>$default.$class_unpublish, 'link'=>$link, 'title' => $this->getMenuItemType($mItems[$i]->link) )).$mItems[$i]->title
					             .$defaultIndicator
					             .JSNHtmlHelper::closeTag('a')					             
					            .JSNHtmlHelper::closeTag('li');
				}
			}
		}
		
		return $items;
	}
	/**
	 * 
	 * Get menu item type
	 * 
	 * @param String $link
	 */
	protected function getMenuItemType( $link )
	{
		$uri = new JURI($link);
		$componentName = $uri->getVar('option');
		$view = $uri->getVar('view');
	
		// load language
		$lang = JFactory::getLanguage();
		$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR.'/components/'.$componentName, null, false, false)
		||	$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR.'/components/'.$componentName, $lang->getDefault(), false, false);

		$value = '';
		if ( !empty($componentName) ) {
			$value	= JText::_( $componentName ,true);
			$vars	= null;

			parse_str($link, $vars);
			if (isset($vars['view'])) {
				// Attempt to load the view xml file.
				$file = JPATH_SITE.'/components/'.$componentName.'/views/'.$vars['view'].'/metadata.xml';
				if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
					// Look for the first view node off of the root node.
					if ($view = $xml->xpath('view[1]')) {
						if (!empty($view[0]['title'])) {
							$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

							// Attempt to load the layout xml file.
							// If Alternative Menu Item, get template folder for layout file
							if (strpos($vars['layout'], ':') > 0)
							{
								// Use template folder for layout file
								$temp = explode(':', $vars['layout']);
								$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$componentName.'/'.$vars['view'].'/'.$temp[1].'.xml';
								// Load template language file
								$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, null, false, false)
								||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], null, false, false)
								||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, $lang->getDefault(), false, false)
								||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], $lang->getDefault(), false, false);
							}
							else
							{
								// Get XML file from component folder for standard layouts
								$file = JPATH_SITE.'/components/'.$componentName.'/views/'.$vars['view'].'/tmpl/'.$vars['layout'].'.xml';
							}
							if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
								// Look for the first view node off of the root node.
								if ($layout = $xml->xpath('layout[1]')) {
									if (!empty($layout[0]['title'])) {
										$value .= ' » ' . JText::_(trim((string) $layout[0]['title']),true);
									}
								}
							}
						}
					}
					unset($xml);
				}
				else {
					// Special case for absent views
					$value .= ' » ' . JText::_($componentName.'_'.$vars['view'].'_VIEW_DEFAULT_TITLE',true);
				}
			}
		}
		return JText::_('JSN_MENU_ITEM_TYPE',true).$value;
	}
}