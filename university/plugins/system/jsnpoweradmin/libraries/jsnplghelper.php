<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: jsnplghelper.php 16006 2012-09-13 03:29:17Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.module.helper');

class JSNPLGHelper{
	/** private variable **/
	private $_template = '';

	/** private variable **/
	private $_document  = '';

	public function __construct()
	{
	    /* require jsnhtml class */
		JSNFactory::localimport('libraries.joomlashine.html');		
		$this->_document = JFactory::getDocument();
	}
	
	/**
	 * load current template
	 *
	 * @return: Add script to validate before submit form
	 */
	public function getTemplate()
	{
		$this->_template = JSNFactory::getTemplate();
	}
	/**
	 * Return global JSNTemplate object
	 *
	 */
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances['JSNPLGHelper'])) {
			$instance	= new JSNPLGHelper();
			$instances['JSNPLGHelper'] = &$instance;
		}

		return $instances['JSNPLGHelper'];
	}	
}