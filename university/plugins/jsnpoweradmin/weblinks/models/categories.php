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
JSNFactory::import('components.com_weblinks.models.categories', 'site');
//JSNFactory::import('components.com_content.helpers.route', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminWeblinksModelCategories extends WeblinksModelCategories
{
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		// Load the parameters.
		$params = JComponentHelper::getParams('com_weblinks');
		$this->setState('params', $params);
	}


	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function &prepareDisplayedData( $pk )
	{
		$parentId = JRequest::getInt('id');
		$this->setState('filter.parentId', $pk['id']);

		$data	=	null;
		$state		= $this->getState();
		$items		= $this->getItems($pk);
		$parent		= $this->getParent();

		if ($items === false) {
			echo JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
			return;

		}

		if ($parent == false) {
			echo JText::_('JGLOBAL_CATEGORY_NOT_FOUND');
			return;
		}

		$params = &$state->params;

		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreMenuParams( $pk['Itemid'], $params );
		$JSNConfig->megreGlobalParams( 'com_weblinks', $params, true );

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