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

JSNFactory::import('components.com_content.helpers.query', 'site');
JSNFactory::import('components.com_content.models.archive', 'site');
JSNFactory::import('components.com_content.helpers.route', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminContentModelArchive extends ContentModelArchive
{
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		$params = JComponentHelper::getParams('com_content');
		$this->setState('params', $params);

		// Filter on archived articles
		$this->setState('filter.published', 2);

		// Filter on month, year
		$this->setState('filter.month', JRequest::getInt('month'));
		$this->setState('filter.year', JRequest::getInt('year'));

		// Optional filter text
		$this->setState('list.filter', JRequest::getString('filter-search'));
		$app = JFactory::getApplication();
		$itemid = JRequest::getInt('Itemid', 0);
		$limit = $app->getUserStateFromRequest('com_content.archive.list' . $itemid . '.limit', 'limit', $params->get('display_num'), 'uint');
		$this->setState('list.limit', $limit);

	}
	/**
	 *
	 * Get item
	 *
	 * @param Array $pk
	 */
	public function &getItem( $pk = Array() )
	{

	}
	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function &prepareDisplayedData( $pk )
	{
		$data	=	null;

		$JSNConfig = JSNFactory::getConfig();
		$params = $JSNConfig->getMenuParams( $pk['Itemid'] );
		$JSNConfig->megreGlobalParams( 'com_content', $params );

		$items 		= $this->getItems();
		$pagination	= $this->getPagination();

		foreach ($items as $item)
		{
			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
		}

		$form = new stdClass();
		// Month Field
		$months = array(
				'' => JText::_('COM_CONTENT_MONTH'),
				'01' => JText::_('JANUARY_SHORT'),
				'02' => JText::_('FEBRUARY_SHORT'),
				'03' => JText::_('MARCH_SHORT'),
				'04' => JText::_('APRIL_SHORT'),
				'05' => JText::_('MAY_SHORT'),
				'06' => JText::_('JUNE_SHORT'),
				'07' => JText::_('JULY_SHORT'),
				'08' => JText::_('AUGUST_SHORT'),
				'09' => JText::_('SEPTEMBER_SHORT'),
				'10' => JText::_('OCTOBER_SHORT'),
				'11' => JText::_('NOVEMBER_SHORT'),
				'12' => JText::_('DECEMBER_SHORT')
		);
		$form->monthField = JHtml::_(
				'select.genericlist',
				$months,
				'month',
				array(
						'list.attr' => 'size="1" class="inputbox"',
						'list.select' => $this->getState('filter.month'),
						'option.key' => null
				)
		);
		// Year Field
		$years = array();
		$years[] = JHtml::_('select.option', null, JText::_('JYEAR'));
		for ($i = 2000; $i <= 2020; $i++) {
			$years[] = JHtml::_('select.option', $i, $i);
		}
		$form->yearField = JHtml::_(
				'select.genericlist',
				$years,
				'year',
				array('list.attr' => 'size="1" class="inputbox"', 'list.select' =>  $this->getState('filter.year'))
		);
		$form->limitField = $pagination->getLimitBox();

		//Escape strings for HTML output
		$data->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$data->filter =  $this->getState('list.filter');

		$data->form	= $form;
		$data->items	=	$items;
		$data->params = $params;
		$data->pagination = $pagination;
		return $data;
	}
}