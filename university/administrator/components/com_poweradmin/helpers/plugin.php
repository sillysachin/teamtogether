<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: plugin.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class JSNPluginHelper
{
	private static $plugins = array();
	
	/**
	 * Retrieve all params of a plugin that determined by name
	 * @return mixed
	 */
	public static function getParams ($name)
	{
		if (!isset(self::$plugins[$name])) {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT params FROM #__extensions WHERE element='{$name}' LIMIT 1");
			$params = $db->loadResult();
			
			self::$plugins[$name] = json_decode($params);
		}
		
		return self::$plugins[$name];
	}
}
