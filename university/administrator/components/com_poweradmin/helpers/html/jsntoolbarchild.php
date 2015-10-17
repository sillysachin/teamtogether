<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: jsntoolbarchild.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class JSNToolbarChild extends JObject
{
	private $_items;
	private $_menuClass = 'jsn-toolbar-parent-item';
	
	public function __construct()
	{
		$this->_items = Array();
	}
	/**
	 * 
	 * Get instance 
	 *
	 * @param Array $params
	 */
	public static function getInstance( $name = '' )
	{
		static $instances;

		if ($name == ''){
			$name = 'JSNToolbarChild';
		}
		if (!isset($instances)) {
			$instances = array();
		}
		if ( empty($instances[$name]) ) {
			$instance	= new JSNToolbarChild();
			$instances[$name] = &$instance;
		}
		
		return $instances[$name];
	}
	/**
	 * 
	 * Set class for menu
	 * 
	 * @param String $className
	 */
	public function setMenuClass( $className )
	{
		$this->_menuClass = $className;
	}
	/**
	 * 
	 * Add an child item
	 * 
	 * Format of an child item:
	 * ->text
	 * ->title
	 * ->href
	 * ->action: (popup/newpage/)
	 * ->iconClass
	 * 
	 * @param Object $item
	 */
	public function addItem( &$item )
	{
		if ( !isset($item->text) ){
			$item->text = 'Child Item';
		}
		
		if ( !isset($item->title) ){
			$item->title = 'Child Item';
		}
		
		if ( !isset($item->href) ){
			$item->href = JURI::root();
		}
		
		if ( !isset($item->action) ){
			$item->action = 'popup';
		}
		
		if ( !isset($item->iconClass) ){
			$item->iconClass = 'icon-32-featured';
		}
		
		if ( !isset($item->itemClass) ){
			$item->itemClass = 'jsn-toolbar-child-item';
		}
		
		$this->_items[] = $item;
	}
	/**
	 * 
	 * Render child item
	 */
	public function render()
	{
		if ( count($this->_items) ){
			$childItems = Array();
			$childItems[] = '<ul class="'.$this->_menuClass.'">';
			foreach($this->_items as $item ){
				if ( $item->action == 'popup' ){
					$onClick = "jsnHelps.openChildPage('".$item->href."', {title:'".$item->title."'});";
					$href = '#';
				}else if( $item->action == 'newpage' ){
					$onClick = "jsnHelps.openNewPage('".$item->href."', {title:'".$title."'});";
					$href = '#';
				}else{
					$href = $item->href;
					$onClick = 'javascript:void(0);';
				}
		
				$itemHTML  = '<li class="'.$item->itemClass.'">';
				$itemHTML .= '<a href="'.$href.'" onclick="'.$onClick.'" rel="'.$item->title.'" title="'.$item->title.'">';
				$itemHTML .= '<span class="'.$item->iconClass.'"></span>';
				$itemHTML .= $item->text;
				$itemHTML .= '</a>';
				$itemHTML .= '</li>';
				$childItems[] = $itemHTML;
			}
			$childItems[] = '</ul>';
			
			return implode(PHP_EOL, $childItems);
		}
		
		return '';
	}
}