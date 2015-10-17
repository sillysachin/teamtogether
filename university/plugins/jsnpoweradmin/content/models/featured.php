<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: featured.php 13311 2012-06-14 12:31:48Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JSNFactory::import('components.com_content.models.featured', 'site');
JSNFactory::import('components.com_content.helpers.query', 'site');
JSNFactory::import('components.com_content.helpers.route', 'site');

class PoweradminContentModelFeatured extends ContentModelFeatured
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $context = 'com_content.featured';
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null){}
	/**
	 * Get featured items
	 *
	 */
	public function prepareDisplayedData($pk)
	{
		JSNFactory::localimport('libraries.joomlashine.config');
		$params = JSNConfig::getMenuParams( $pk['Itemid'] );
		JSNConfig::megreGlobalParams( 'com_content', $params );

		$this->setState('params', $params);
		$data = new stdClass();
		$data->params = $params;
		$activeAllParams = new JRegistry();
		if ($params instanceof JRegistry){
			foreach($params->toArray() as $key => $val ){
				if ( strpos($key, 'show_') !== false && (int) $params->get($key) == 0 ){
					$activeAllParams->set($key, 1);
				}else{
					$activeAllParams->set($key, $val);
				}
			}
		}
		$limit = (int) $params->def('num_leading_articles', 1) + (int) $params->get('num_intro_articles', 4) + (int) $params->def('num_links', 4);

		$this->setState('params', $activeAllParams );
		$this->setState('filter.published', 1);
		$this->setState('filter.access', '');
		$this->setState('list.start', 0);
		$this->setState('list.limit', $limit);
		$this->setState('list.direction', '');
		$this->setState('list.filter', '');
		// filter.subcategories indicates whether to include articles from subcategories in the list or blog
		$this->setState('list.links', $activeAllParams->get('num_links'));
		if ($activeAllParams->get('featured_categories') && implode(',', $activeAllParams->get('featured_categories'))  == true) {
			$this->setState('filter.frontpage.categories', $activeAllParams->get('featured_categories'));
		}
		$this->setState('filter.frontpage', 1);
		$items = parent::getItems();

		// PREPARE THE DATA

		// Get the metrics for the structural page layout.
		$numLeading = $params->def('num_leading_articles', 1);
		$numIntro = $params->def('num_intro_articles', 4);
		$numLinks = $params->def('num_links', 4);

		// Compute the article slugs and prepare introtext (runs content plugins).
		foreach ($items as $i => & $item)
		{
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

			// No link for ROOT category
			if ($item->parent_alias == 'root') {
				$item->parent_slug = null;
			}

			// Ignore content plugins on links.
			if ($i < $numLeading + $numIntro)
			{
				$item->introtext = JHtml::_('content.prepare', $item->introtext);
			}
		}

		// Preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interogate the arrays.
		$max = count($items);

		// The first group is the leading articles.
		$limit = $numLeading;
		for ($i = 0; $i < $limit && $i < $max; $i++)
		{
			$data->lead_items[$i] = &$items[$i];
		}

		// The second group is the intro articles.
		$limit = $numLeading + $numIntro;
		// Order articles across, then down (or single column mode)
		for ($i = $numLeading; $i < $limit && $i < $max; $i++)
		{
			$data->intro_items[$i] = &$items[$i];
		}

		$data->columns = max(1, $params->def('num_columns', 1));
		$order = $params->def('multi_column_order', 1);

		if ($order == 0 && $data->columns > 1)
		{
			// call order down helper
			$data->intro_items = ContentHelperQuery::orderDownColumns($data->intro_items, $data->columns);
		}

		// The remainder are the links.
		for ($i = $numLeading + $numIntro; $i < $max; $i++)
		{
			$data->link_items[$i] = &$items[$i];
		}

		$data->pagination = $this->getPagination();

		//Escape strings for HTML output
		$data->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		return $data;
	}
}
