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

JSNFactory::import('components.com_contact.models.featured', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminContactModelFeatured extends ContactModelFeatured
{
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		// Load the parameters.
		$params = JComponentHelper::getParams('com_contact');
		$this->setState('params', $params);

	}

	public function getItem( $pk = Array() )
	{
		$item = parent::getItem( $pk['id'] );
		return $item;
	}


	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function prepareDisplayedData( $pk )
	{
		$data = null;
		$params		= $this->getState('params');
		// Get some data from the models
		$state		= $this->getState();
		$items		= $this->getItems();

		$pagination	= $this->getPagination();

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
					$item->email_to = $item->email_to;
				} else {
					$item->email_to = '';
				}
			}
		}

		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreMenuParams( $pk['Itemid'], $params, 'com_contact' );
		$JSNConfig->megreGlobalParams( 'com_contact', $params, true );

		$maxLevel = $params->get('maxLevel', -1);
		$data->maxLevel = $maxLevel;
		$data->state 	= 	$state;
		$data->items 	= 	$items;
		$data->params 	= 	$params;
		$data->pagination = $pagination;

		return $data;
	}
}