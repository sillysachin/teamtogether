<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: templates.php 15329 2012-08-21 09:40:36Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.modeladmin');

class PoweradminModelTemplates extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true){}

	/**
	 *
	 * Get all templates was installed
	 */
	public function getTemplates($clientId = 0)
	{
		$db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("a.*, e.extension_id AS tid");
        $query->from($db->quoteName('#__template_styles').' AS a');

        // Join on menus.
        $query->select('COUNT(m.template_style_id) AS assigned');
        $query->leftjoin('#__menu AS m ON m.template_style_id = a.id');
        $query->group('a.id, a.template, a.title, a.home, a.client_id, l.title, l.image, e.extension_id');

        // Join over the language
        $query->join('LEFT', '#__languages AS l ON l.lang_code = a.home');

        // Filter by extension enabled
        $query->select('extension_id AS e_id');
        $query->join('LEFT', '#__extensions AS e ON e.element = a.template');
        $query->where('e.enabled = 1');
        $query->where($db->quoteName('e.type') . '=' . $db->quote('template'));

        $query->where('a.client_id = '.(int) $clientId);

        $query->order("a.template ASC");
        $db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 *
	 * Get template by id
	 * @param Number $id
	 */
	public function setDefaultTemplate($id, $clientId = 0)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update("#__template_styles");
		$query->set("home = 0");
		$query->where("client_id=" . $clientId);
		$db->setQuery($query);
		if ($db->query()){
			$query->clear();
			$query->update("#__template_styles");
			$query->set("home = 1");
			$query->where("id=".$db->quote($id));
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 *
	 * Get latest Style
	 */
	public function getLatestStyle()
	{
		$db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("s.id, s.template, s.title, s.home, e.extension_id AS tid");
        $query->from("#__template_styles AS s");
        $query->join('LEFT', "#__extensions AS e ON e.element = s.template ");
        $query->where("s.client_id=0");
        $query->order("s.id DESC");
		$query->limit("1");
        $db->setQuery($query);

		return $db->loadObject();
	}
}
?>