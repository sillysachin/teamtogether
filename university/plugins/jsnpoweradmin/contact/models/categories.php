<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN PowerAdmin support for com_content
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.categories');
JSNFactory::import('components.com_contact.models.categories', 'site');
//JSNFactory::import('components.com_content.helpers.route', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminContactModelCategories extends ContactModelCategories
{
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		// Load the parameters.
		$this->setState('filter.extension', 'com_contact');

		// Get the parent id if defined.
		$parentId = JRequest::getInt('id');
		$this->setState('filter.parentId', $parentId);

		$params = JComponentHelper::getParams('com_contact');
		$this->setState('params', $params);

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
	}

	public function getItems($pk)
	{
		if(!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$this->params = $params	= $this->getState('params');

			if($active)
			{
				$params->loadString($active->params);
			}
			$options = array();
			$options['countItems'] = $params->get('show_cat_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories = JCategories::getInstance('Contact', $options);
			$this->_parent = $categories->get($pk['id']);
			$JSNConfig = JSNFactory::getConfig();
			$JSNConfig->megreMenuParams( $pk['Itemid'], $params, 'com_contact' );
			$JSNConfig->megreGlobalParams( 'com_contact', $this->params, true );
			if(is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			} else {
				$this->_items = false;
			}
		}

		return $this->_items;
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

		if ($items === false) {
			return JText::_('JGLOBAL_CATEGORY_NOT_FOUND');

		}

		if ($parent == false) {
			return JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
		}

		$params = &$state->params;

		$items = array($parent->id => $items);


// 		//Escape strings for HTML output
 		$data->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

 		$data->maxLevelcat = $params->get('maxLevelcat', -1);
		$data->params =		$params;
		$data->parent =	$parent;
		$data->items = $items;
		return $data;
	}
}