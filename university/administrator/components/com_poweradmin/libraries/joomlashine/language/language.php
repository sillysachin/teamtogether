<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: language.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

// Load libraries
jimport('joomla.language.language');

/**
 * 
 */
class JSNLanguage extends JLanguage
{
	protected static $languages = array();
	
	/**
	 * Constructor of JSNLanguage object
	 * 
	 * @param string $lang
	 * @param boolean $debug
	 */
	public function __construct ($lang, $debug)
	{
		parent::__construct($lang, $debug);
	}
	
	/**
	 * Override getInstance method of JLanguage class
	 * 
	 * @param   string   $lang   The language to use.
	 * @param   boolean  $debug  The debug mode.
	 * @return JSNLanguage
	 */
	public static function getInstance ($lang = null, $debug = false)
	{
		$config = JFactory::getConfig();
		if ($lang == null)
			$lang = $config->get('locale');
		
		if (!isset(self::$languages[$lang.$debug])) {
			self::$languages[$lang.$debug] = new JSNLanguage($lang, $debug);
		}
		
		return self::$languages[$lang.$debug];
	}
	
	/**
	 * Return all strings and keys that use to translate
	 * @return array
	 */
	public function getStrings ()
	{
		return $this->strings;
	}
}




