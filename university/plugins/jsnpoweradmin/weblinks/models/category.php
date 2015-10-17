<?php
/**
 * @version     $Id$
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
//error_reporting(0);
jimport('joomla.application.categories');
JSNFactory::import('components.com_weblinks.models.category', 'site');

class PoweradminWeblinksModelCategory extends WeblinksModelCategory
{
	protected $_item = null;

	protected $_articles = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;


	/**
	 *
	 * Get params of current view
	 */
	protected function populateState($ordering = null, $direction = null){
		$params = JComponentHelper::getParams('com_weblinks');

		$this->setState('params', $params);
	}


	/**
	 *
	 * Get data
	 *
	 * @param Array $pk
	 */
	public function prepareDisplayedData( $pk )
	{
		$data		= null;
		$this->setState('category.id', $pk['id']);
		// Get some data from the models
		$state		= $this->getState();
		$items		= $this->getItems();
		$category	= $this->getCategory();
		$children	= $this->getChildren();
		$parent 	= $this->getParent();
		$pagination	= $this->getPagination();

		$params		= $this->getState('params');
		// Check for errors.


		if ($category == false) {
			echo  JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
			return;

		}

		if ($parent == false) {
			echo JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
			return;
		}
		// Prepare the data.
		// Compute the weblink slug & link url.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;

			if ($item->params->get('count_clicks', $params->get('count_clicks')) == 1) {
				$item->link = JRoute::_('index.php?option=com_weblinks&task=weblink.go&&id='. $item->id);
			}
			else {
				$item->link = $item->url;
			}

			$temp		= new JRegistry();
			$temp->loadString($item->params);
			$item->params = clone($params);
			$item->params->merge($temp);
		}

		// Setup the category parameters.
		$cparams = $category->getParams();
		$category->params = clone($params);
		$category->params->merge($cparams);

		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreMenuParams( $pk['Itemid'], $params );
		$JSNConfig->megreGlobalParams( 'com_weblinks', $params, true );

		$children = array($category->id => $children);
		$maxLevel =  $params->get('maxLevel', -1);

		$data->maxLevel	= $maxLevel;
		$data->state	= $state;
		$data->items	= $items;
		$data->category	= $category;
		$data->children	= $children;
		$data->params	= $params;
		$data->parent	= $parent;
		$data->pagination	= $pagination;

		return $data;
	}
}