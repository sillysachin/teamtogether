<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: jsnjoomlaxtc.php 16006 2012-09-13 03:29:17Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JSNFactory::localimport('libraries.joomlshine.libraries.template');

class JSNJoomlaXTCHelper{
	
	private $_template;
	private $_document;
	private $_columns;
	/**
	 * 
	 * Constructure 
	*/
	public function __construct( $template, $document = null )
	{
		$this->_template = $template;
		$this->_document = $document;
		$this->makeArrayColumns();
	}
	
	public static function getInstance( $template, $document  = null  )
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}
		if (empty($instances['JSNJoomlaXTCHelper'])) {
			$instance	= new JSNJoomlaXTCHelper( $template, $document );
			$instances['JSNJoomlaXTCHelper'] = &$instance;
		}
		return $instances['JSNJoomlaXTCHelper'];
	}
	/**
	 * 
	 * Set document HTML page
	 * 
	 * @param HTML $document is HTML of page
	 */
	public function setDocument( $document )
	{
		$this->_document = $document;
	}
	/**
	 * 
	 * Make array store all position in template
	 */
	protected function makeArrayColumns(){
		//All position not defined in XML
		$this->_columns = Array('user14', 'user15', 'user16', 'user17', 'user18', 'user19', 'user20', 
	                             'user21', 'user22', 'user23', 'user24', 'user25', 'user26', 'user27',
	                             'bottom1', 'bottom2', 'bottom3', 'bottom4', 'bottom5');
		$template  = JSNFactory::getTemplate(); 
		$positions = $template->loadXMLPositions();
		foreach( $positions as $position ){
			if ( !in_array( $position->name[0], $this->_columns ) ){
				$this->_columns[] = (string) $position->name[0];
			}
		}
	}
	/**
	 * 
	 *  Get <head> 
	*/
	protected function getHeader()
	{
		return preg_replace("/.*<head]*>|<\/head>.*/si", "", $this->_document);
	}
	/**
	 * 
	 * Get <body>
	 */
	protected function getBody()
	{
		$body = new stdClass();
		$body->attr = '';
		if(preg_match_all('#<body\ (.*)>#iU', $this->_document, $matches)) {
			$body->attr = $matches[1][0];
		}
		preg_match('/<body(.*)>(.*)<\/body>/is', $this->_document, $matches);
		$body->html = $matches[0];
		return $body;
	} 
	/**
	 * 
	 * Render page
	 * 
	 * @param (Number) $vms_mode is mode of request
	 */
	protected function renderModules( $vms_mode = 0)
	{
		$domDocument = new DOMDocument();
		$domDocument->loadHTML( $this->_document );
		if ( $vms_mode ){
			foreach( $this->_columns as $column ){
				$contentPosition = $domDocument->getElementById( $column );
				if ( is_object($contentPosition) ){
					$class = $contentPosition->getAttribute('class');
					$contentPosition->setAttribute('class', 'jsn-element-container_inner');
					$contentPosition->removeAttribute('id');
					foreach( $contentPosition->childNodes as $child ){
						$contentPosition->removeChild($child);
					}
					$positionContainer = $domDocument->createElement("div");
					$positionContainer->setAttribute('class', 'poweradmin-position');
					$positionContainer->setAttribute('id', $column.'-jsnposition');
					$positionTitle = $domDocument->createElement("p", $column);
					$positionContainer->appendChild( $positionTitle );
					$contentPosition->appendChild( $positionContainer );
				}
			}
			$this->_document = $domDocument->saveHTML();
		}else{
			$modulesHTML = Array();
			foreach( $this->_columns as $column ){
				$contentPosition = $domDocument->getElementById( $column );
				if ( is_object($contentPosition) ){
					$class = $contentPosition->getAttribute('class');
					$contentPosition->setAttribute('class', 'jsn-element-container_inner');
					$contentPosition->removeAttribute('id');
					foreach( $contentPosition->childNodes as $child ){
						$contentPosition->removeChild($child);
					}					
					if ((string) $column != ''){
						$modules = JModuleHelper::getModules( $column );
					}else{
						$modules = Array();
					}					
					$HTML = '';
					if ( count($modules) ){
						foreach ( $modules as $mod ) {
							if ($mod->position != $column) continue;
							     $HTML .= JSNHtmlHelper::openTag('div', array('class'=>"poweradmin-module-item", 'id'=>$mod->id.'-jsnposition-published', 'title'=>$mod->title, 'showtitle'=>$mod->showtitle))
							              .JSNHtmlHelper::openTag('div', array('id'=>"moduleid-'.$mod->id.'-content"))
							                 .JModuleHelper::renderModule($mod, array('style'=>'none'))
							              .JSNHtmlHelper::closeTag('div')
							           .JSNHtmlHelper::closeTag('div');
						}
					}
					$modulesHTML[$column] = $HTML;
					$positionContainer = $domDocument->createElement("div");
					$positionContainer->setAttribute('class', 'poweradmin-position');
					$positionContainer->setAttribute('id', $column.'-jsnposition');
					$positionContainer->appendChild( new DOMText('HTML'.$column) );
					$contentPosition->appendChild( $positionContainer );
				}
			}
			
			$this->_document = $domDocument->saveHTML();
			foreach ($modulesHTML as $key => $innerHTML ){
				$this->_document = str_replace( 'HTML'.$key, $innerHTML, $this->_document );
			}
		}		
	}
	/**
	 * 
	 * Main function to render page
	 */
	public function render()
	{
		$poweradmin = JRequest::getCmd('poweradmin', 0);
		$vms_mode   = JRequest::getCmd('vsm_changeposition', 0);
		
		if ( $poweradmin ){
			$header = $this->getHeader();
			$this->renderModules( $vms_mode );
			$body   = $this->getBody();
			$HTML = Array();
			$HTML[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			$HTML[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$this->_template->language.'" lang="'.$this->_template->language.'" >';			
			$HTML[] = '<head>'.$header.'</head>';			
			$HTML[] = '<body '.$body->attr.'>'.$body->html.'</body>';
			$HTML[] = '</html>';
			echo implode(PHP_EOL, $HTML);
		}else{
			echo $this->_document;
		}
	}
}
?>