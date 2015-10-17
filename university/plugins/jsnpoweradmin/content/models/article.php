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

JSNFactory::import('components.com_content.models.article', 'site');
JSNFactory::import('components.com_content.helpers.route', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminContentModelArticle extends ContentModelArticle
{
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
	public function &getItem( $pk = Array() )
	{
		$item = parent::getItem( $pk['id'] );

		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;

		// Merge article params. If this is single-article view, menu params override article params
		// Otherwise, article params override menu item params
		$this->params	= $this->state->get('params');
		$temp	= clone ($this->params);

		// Merge so that article params take priority
		$temp->merge($item->params);
		$item->params = $temp;

		//Megre params
		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreMenuParams( $pk['Itemid'], $item->params );
		$JSNConfig->megreGlobalParams( 'com_content', $item->params, true );
		$offset = $this->state->get('list.offset');

		$item->pageclass_sfx = htmlspecialchars($item->params->get('pageclass_sfx'));

		return $item;
	}
	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function &prepareDisplayedData( $pk )
	{
		return $this->getItem($pk);
	}
}