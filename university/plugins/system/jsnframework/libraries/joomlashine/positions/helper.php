<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class of JSN Config library.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNPositionsHelper
{
	/**
	 * Method to render the position selector.
	 *
	 * @param   object  $jsnrender  Data for rendering.
	 *
	 * @return  void
	 */
	public static function render($jsnrender)
	{
		require dirname(__FILE__) . '/tmpl/default.php';
	}

	/**
	 * Method to get module info.
	 *
	 * @param   int  $moduleId .
	 *
	 * @return  object Module info
	 */
	public static function getModule($moduleId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('client_id');
		$query->from("#__modules");
		$query->where('id=' . (int)$moduleId);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to dispatch special template framework
	 * @param string $templateAuthor
	 */
	public static function dispatchTemplateFramework($templateAuthor)
	{
		$cacheFolder	= JPATH_ROOT . '/cache';
		if (is_file($cacheFolder) && JFolder::delete($cacheFolder))
		{

		}
		else
		{
			//JFolder::create($cacheFolder);
		}
	}
}
