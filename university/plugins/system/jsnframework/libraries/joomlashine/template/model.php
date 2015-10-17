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

require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/base/model.php';

/**
 * Template modal.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNTemplateModel extends JSNBaseModel
{
	/**
	 * Get template assigned
	 *
	 * @return  object
	 */
	public static function getDefaultTemplate()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("t.*");
		$query->from("#__extensions as t");
		$query->join("LEFT", "#__template_styles as s ON t.element = s.template");
		$query->where("s.client_id = 0 AND s.home = 1 AND t.type = 'template'");
		$db->setQuery($query);

		return $db->loadObject();
	}
}
