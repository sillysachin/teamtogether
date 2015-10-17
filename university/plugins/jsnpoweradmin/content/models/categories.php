<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: article.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JSNFactory::import('components.com_content.models.categories', 'site');
JSNFactory::import('components.com_content.helpers.route', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminContentModelCategories extends ContentModelCategories
{
	private $_items = null;
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		// Load the parameters.
		$params = JComponentHelper::getParams('com_content');
		$this->setState('params', $params);
	}
	/**
	 *
	 * Get item
	 *
	 * @param Array $pk
	 */
	public function getItems($pk, $recursive = false )
	{
		if (!count($this->_items)) {
			// Process params
			$this->params = $params	= $this->getState('params');


			$options = array();
			$options['countItems'] = $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories = JCategories::getInstance('Content', $options);
			$this->_parent = $categories->get($pk['id']);

			$JSNConfig = JSNFactory::getConfig();
			$JSNConfig->megreMenuParams( $pk['Itemid'], $this->params );
			$JSNConfig->megreGlobalParams( 'com_content', $this->params, true );

			if (is_object($this->_parent)) {
				$this->_items = $this->_parent->getChildren(false);
			}
			else {
				$this->_items = false;
			}
		}

		return $this->_items;
	}

	public function getItem($pk)
	{
		return;
	}
	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function &prepareDisplayedData( $pk )
	{
		$data	=	null;
		$state		= $this->getState();
		$items		= $this->getItems($pk);
		$parent		= $this->_parent;

		$params = &$state->params;

		$items = array($parent->id => $items);

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$data->maxLevelcat = $params->get('maxLevelcat', -1);
		$data->params =		$params;
		$data->parent =	$parent;
		$data->items = $items;
		return $data;
	}
}