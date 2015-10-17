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
JSNFactory::import('components.com_contact.models.category', 'site');
JSNFactory::import('components.com_contact.helpers.query', 'site');
JSNFactory::import('components.com_contact.helpers.route', 'site');
class PoweradminContactModelCategory extends ContactModelCategory
{
	protected $_data;

	protected $_context = 'com_poweradmin.category';

	/**
	 *
	 * Get params of current view
	 */
	protected function populateState($ordering = null, $direction = null){
		$params = JComponentHelper::getParams('com_contact');

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
		$data = null;
		jimport('joomla.application.categories');

		$this->setState('category.id', $pk['id']);
		$params = $this->getState('params');

		// Get some data from the models
		$state		= $this->getState();
		$items		= $this->getItems();
		$category	= $this->getCategory();
		$children	= $this->getChildren();
		$parent 	= $this->getParent();
		$pagination	= $this->getPagination();

		// Check for errors.

		if ($category == false) {
			echo JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
		}

		if ($parent == false) {
			echo JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
		}


		// Prepare the data.
		// Compute the contact slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$temp		= new JRegistry();
			$temp->loadString($item->params);
			$item->params = clone($params);
			$item->params->merge($temp);

			if ($item->params->get('show_email', 0) == 1) {
				$item->email_to = trim($item->email_to);

				if (!empty($item->email_to) && JMailHelper::isEmailAddress($item->email_to)) {
					$item->email_to = JHtml::_('email.cloak', $item->email_to);
				}
				else {
					$item->email_to = '';
				}
			}
		}

		// Setup the category parameters.
		$cparams = $category->getParams();
		$category->params = clone($params);
		$category->params->merge($cparams);

		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreMenuParams( $pk['Itemid'], $params, 'com_contact' );
		$JSNConfig->megreGlobalParams( 'com_contact', $params, true );

		$children = array($category->id => $children);

		$maxLevel = $params->get('maxLevel', -1);
		$data->maxLevel 	= $maxLevel;
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