<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: rawmode.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin
 * @since		1.7
 */
JSNFactory::localimport('libraries.joomlashine.modules');
JSNFactory::localimport('libraries.joomlashine.template');
JSNFactory::localimport('libraries.joomlashine.html');

class JSNRawmode{
	var $_params;
	var $_htmlpositions;	
	var $_jspositions;
	var $_component;
	/**
	 * 
	 * Constructor
	 * 
	 * @param Array $params
	 */
	public function __construct( $params = Array() )
	{
		$this->_params    = $params;
		$this->_htmlpositions = Array();
		$this->_jspositions = Array();
	}
	/**
	 * 
	 * Get instance 
	 *
	 * @param Array $params
	 */
	public static function getInstance( $params = Array() )
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}
		if (empty($instances['JSNRawmode'])) {
			$instance	= new JSNRawmode( $params );
			$instances['JSNRawmode'] = &$instance;
		}else{
			$instances['JSNRawmode']->megreParams( $params );
		}

		return $instances['JSNRawmode'];
	}
	/**
	 * 
	 * Megre new params
	 * 
	 * @param Array $params
	 */
	public function megreParams( $params = Array() )
	{
		foreach ($params as $key => $value )
		{
			if ( $this->getParam( $key ) != $value ){
				$this->setParam( $key, $value );
			}
		}
	}
	/**
	 * 
	 * Set param
	 * 
	 * @param String $name
	 * @param (None) $value
	 */
	public function setParam( $name, $value )
	{
		$this->_params[$name] = $value;
	}
	/**
	 * 
	 * Get param
	 * 
	 * @param String $name
	 */
	public function getParam( $name )
	{
		if (isset($this->_params[$name])){
			return $this->_params[$name];
		}
		return '';
	}
	/**
	 * 
	 * Get HTML
	 * 
	 * @param String $type
	 * @param String $pos
	 */
	public function getHTML($type = 'positions', $pos = '')
	{
		switch ($type)
		{
			case 'position':
				return $this->_htmlpositions[$pos];
			case 'positions':
				return implode(PHP_EOL, $this->_htmlpositions);
			case 'component':
				return $this->_component;
		}
		
	}
	/**
	 * 
	 * Get Javascript position
	 * 
	 * @param String $type
	 * @param String $data
	 * @param String $pos
	 */
	public function getScript($type = 'positions', $data = 'Array', $pos = '')
	{
		if ($type == 'position'){
			switch ($data)
			{
				case 'String':
					return $this->_jspositions[$pos];
				case 'Array':
					return Array($pos=>$this->_jspositions[$pos]);
				case 'JSON':
					return json_encode($this->_jspositions[$pos]);
			}
		}else if( $type == 'positions' ){
			switch ($data)
			{
				case 'String':
					return implode(PHP_EOL, $this->_jspositions);
				case 'Array':
					return $this->_jspositions;
				case 'JSON':
					return json_encode($this->_jspositions);
			}
		}
	}
	/**
	 * 
	 * Convert php to js position
	 * 
	 * @param String $position
	 * @param Array $modules
	 */
	public function toJSPosition( $position, $modules = Array() )
	{
		$count = count($modules);
		//From AJAX requested
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$position_to_json = new stdClass();
			$position_to_json->element_id  =  '#'.$position.'-jsnposition';
			if ($position == 'notdefault'){
				$position_to_json->inactive_position = true;
			}else{
				$position_to_json->inactive_position = false;
			}
			if ($count == 0){
				$position_to_json->container_class = 'jsn-element-container_inner jsn-inactive-element';
			}else{
				$active = false;
				for($i = 0; $i < $count; $i++){
					$module = $modules[$i];
					if ($module->published == 1 && ($module->assignment == 'selected' || $module->assignment == 'all' || $module->assignment == 'this')){
						$active = true;
					}
				}
				if(!$active){
					$position_to_json->container_class = 'jsn-element-container_inner jsn-inactive-element';
				}else{
					$position_to_json->container_class = 'jsn-element-container_inner';
				}
			}

			$position_to_json->modules = array();
			
			for($i = 0; $i < $count; $i++){
				$module = $modules[$i];
				$published     = ($module->published == 0) ? 'unpublished':'published';
				$class_publish = ($module->published == 0 ) ? ' jsn-module-unpublish':'';
				if ($module->published == 0){
					$unpublish = true;
				}else{
					$unpublish = false;
				}
				
				if ($module->assignment == '' || $module->assignment == 'except'){
					$class_unassignment = ' jsn-module-unassignment';
				}else{
					$class_unassignment = '';
				}

				$item = new stdClass();
				$item->title      = htmlspecialchars($module->title);
				$item->module_id  = $module->id;
				$item->element_id = '#'.$module->id.'-jsnposition-'.$published;
				$item->classset   = 'poweradmin-module-item'.$class_unassignment.$class_publish;
				$item->unpublish  = $unpublish;
				$item->assignment = $module->assignment;
				$item->moduletype = $module->moduletype;
				
				if ( !$unpublish && $class_unassignment == '' ){
					$position_to_json->inactive_position = false;
				}
				$position_to_json->modules[] = $item;
			}
			
			return $position_to_json;
		}
		//From http requested
		else{
			$moduleObj = array();			
			if ($count){
				$inactive_position = 'true';
			}else{
				$inactive_position = 'false';
			}
			
			for($i = 0; $i < $count; $i++){
				$module = $modules[$i];
				$module_status = new stdClass();
				
				$published  = ($module->published == 0) ? 'unpublished':'published';
				if ($module->published == 0){
					$unpublish = true;
				}else{
					$unpublish = false;
				}
				if ( $module->assignment == '' || $module->assignment == 'except' ){
					$class_unassignment = ' jsn-module-unassignment';
				}else{
					$class_unassignment = '';
				}
				
				if ( !$unpublish && $class_unassignment == ''){
					$inactive_position = 'false';
				}
				
				$module_element_id = $module->id.'-jsnposition-'.$published;
				$moduleObj[]       = "'".$module->id."':{
													unpublish:".($unpublish?'true':'false').", 
													element_id:'#".$module_element_id."', 
													assignment:'".$module->assignment."', 
													moduletype:'".$module->moduletype."'
			                                      }";
			}
	
			return    "positions['".$position."'] = {
			                      element_id:'#".$position.'-jsnposition'."',
			                      inactive_position:".$inactive_position.",
			                      modules:{".PHP_EOL.implode(','.PHP_EOL, $moduleObj)."}
		              };";
		}
		
	}
	
	/**
	* Render modules
	* 
	* @param: (string) $position is position of default template
	* @return: (Array) $modules is array modules in position
	* @return: HTML 
	*/
	public function renderModulesPosition($position, $modules)
	{
		$count = count($modules);
		$inactive_class = '';
		if ($count == 0){
			$inactive_class = ' jsn-inactive-element';
		}else{
			$active = false;
			for($i = 0; $i < $count; $i++){
				$module = $modules[$i];
				if ($module->published == 1 && ($module->assignment == 'selected' || $module->assignment == 'all' || $module->assignment == 'this')){
					$active = true;
				}
			}
			if(!$active){
				$inactive_class = ' jsn-inactive-element';
			}
		}
		
		$position_html  = JSNHtmlHelper::openTag('div', array('class'=>"jsn-element-container"));
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"jsn-element-container_inner".$inactive_class));
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"jsn-position-name"))
		                   .JSNHtmlHelper::openTag('h2')
		                      .$position
		                   .JSNHtmlHelper::closeTag('h2')
		               .JSNHtmlHelper::closeTag('div');

		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');		
		
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>'jsn-poweradmin-position', 'id'=>$position.'-jsnposition'));
		
		for($i = 0; $i < $count; $i++){			 
			$module = $modules[$i];
			$checked_out = $module->checked_out ? ' <i class="checked-out icon-lock"></i>' : '' ;			
			$module->published = (int) $module->published;
			$published     = ($module->published == 0) ? 'unpublished':'published';
			$class_publish = ($module->published == 0) ? ' jsn-module-unpublish':'';
			if ($module->assignment == '' || $module->assignment == 'except'){
				$class_unassignment = ' jsn-module-unassignment';
			}else{
				$class_unassignment = '';
			}
			
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>'jsn-bootstrap poweradmin-module-item '.$class_unassignment . $class_publish, 'id'=>$module->id.'-jsnposition-'.$published, 'title'=>$module->moduletype));
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item-drag-handle")).JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item-inner"));
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item-inner-text"));
			$position_html .= $module->title;
			$position_html .= JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::closeTag('div');			
			$position_html .= $checked_out;
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::closeTag('div');
		}
		$position_html .= JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::closeTag('div');		
		return $position_html;
	}
	/**
	 * 
	 * Render all modules not assign to default position
	 * 
	 * @param Array $modules
	 * @return: HTML
	 */
	public function renderModulesNotDefaultPosition( $modules )
	{
		$position_html  = JSNHtmlHelper::openTag('div', array('class'=>"jsn-element-container"));
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"jsn-element-container_inner jsn-notdefault-element"));
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');		
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>'jsn-poweradmin-position', 'id'=>'notdefault-jsnposition'));
		
		$count = count($modules);
		for($i = 0; $i < $count; $i++){
			$module = $modules[$i];
			$checked_out = $module->checked_out ? ' <i class="checked-out icon-lock"></i>' : '' ;	
			$module->published = (int) $module->published;
			$published     = ($module->published == 0) ? 'unpublished':'published';
			$class_publish = ($module->published == 0) ? ' jsn-module-unpublish':'';
			if ($module->assignment == '' || $module->assignment == 'except'){
				$class_unassignment = ' jsn-module-unassignment';
			}else{
				$class_unassignment = '';
			}
			
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>'jsn-bootstrap poweradmin-module-item ' . $class_unassignment . $class_publish , 'id'=>$module->id.'-jsnposition-'.$published, 'title'=>$module->moduletype));
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item-drag-handle")).JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item-inner"));
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item-inner-text"));
			$position_html .= $module->title;
			$position_html .= JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::closeTag('div');		
			$position_html .= $checked_out;
			$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
			$position_html .= JSNHtmlHelper::closeTag('div');
		}
		$position_html .= JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::openTag('div', array('class'=>"clearbreak")).JSNHtmlHelper::closeTag('div');
		$position_html .= JSNHtmlHelper::closeTag('div');		
		return $position_html;
	}
	/**
	 * 
	 * Render component
	 * 
	 * @return: HTML
	 */
	public function renderComponent()
	{
		JSNFactory::localimport('helpers.html.jsnrenders');
		$params = Array();
		foreach ($this->_params as $key => $value){
			$params[$key] = $value;
		}
		
		//Set default option is Empty
		if ( !key_exists('option', $params) ){
			$params['option'] = 'com_empty';
		}
		
		//Set default view is Empty
		if ( !key_exists('view', $params) ){
			$params['view'] = 'Empty';
		}
		
		$this->_component = JSNRenderHelper::dispatch( $params );
	}
	/**
	 * 
	 * Render position
	 * 
	 * @param String $position
	 */
	public function renderPosition( $position )
	{
		if ($position == 'notdefault'){			
			$modules = JSNModules::getModulesNotDefaultPosition( $this->getParam('Itemid') );
		}else{			
			$modules = JSNModules::getModules( $position, $this->getParam('Itemid') );
		}		
		
		
		$this->_htmlpositions[$position] = $this->renderModulesPosition( $position, $modules );
		$this->_jspositions[$position]   = $this->toJSPosition( $position, $modules );
	}
	/**
	 * 
	 * Render all rawmode
	 */
	public function renderAll()
	{
		foreach($this->getParam('positions') as $position)
		{
			$position = (string) $position->name;
			$this->renderPosition( $position );
		}
		$modulesNotDefaultPosition = JSNModules::getModulesNotDefaultPosition( $this->getParam('Itemid') );
		$this->_htmlpositions['notdefault'] = $this->renderModulesNotDefaultPosition( $modulesNotDefaultPosition );
		$this->_jspositions['notdefault']   = $this->toJSPosition( 'notdefault', $modulesNotDefaultPosition );
		$this->renderComponent();
	}
}