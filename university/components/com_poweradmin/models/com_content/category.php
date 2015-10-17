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
JSNFactory::import('components.com_content.models.category', 'site');
JSNFactory::import('components.com_content.helpers.query', 'site');
JSNFactory::import('components.com_content.helpers.route', 'site');
JSNFactory::import('components.com_content.models.articles', 'site');
class PoweradmincontentModelCategory extends ContentModelCategory
{
	protected $_data;

	protected $_context = 'com_poweradmin.category';

	/**
	 *
	 * Get params of current view
	 */
	protected function populateState($ordering = null, $direction = null){
		$params = JComponentHelper::getParams('com_content');
		$this->setState('params', $params);
	}
	/**
	 *
	 * Get model
	 *
	 * @param $type
	 * @param $prefix
	 */
	protected function getModel( $type, $prefix = '', $config )
	{
		$type		= preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$modelClass	= $prefix.ucfirst($type);

		if ( !class_exists($modelClass) ) {
			$extension = 'com_'.str_replace('model', '', JString::strtolower($prefix));
			$path = JPATH_ROOT.DS.'components'.DS.$extension.DS.'models'.DS.JString::strtolower($type).'.php';
			if ($path) {
				require_once $path;

				if ( !class_exists($modelClass) ) {
					JError::raiseWarning(0, JText::sprintf('JLIB_APPLICATION_ERROR_MODELCLASS_NOT_FOUND', $modelClass));
					return false;
				}
			}else{
				return false;
			}
		}

		return new $modelClass($config);
	}
	/**
	 * Get the articles in the category
	 *
	 * @return	mixed	An array of articles or false if an error occurs.
	 * @since	1.5
	 */
	function getSpecificItems( $Itemid )
	{
		$app = JFactory::getApplication('site');
		$JSNConfig = JSNFactory::getConfig();
		$this->_data->params = $JSNConfig->getMenuParams( $Itemid );
		$JSNConfig->megreGlobalParams( 'com_content', $this->_data->params );

		$this->setState('params', $this->_data->params);

		if ($this->_data->params->get('layout_type') == 'blog') {
			$limit = (int) $this->_data->params->def('num_leading_articles', 1) + (int) $this->_data->params->get('num_intro_articles', 4) + (int) $this->_data->params->def('num_links', 4);
		}else{
			$this->setState('filter.published', 1);
			$limit = $app->getUserStateFromRequest('com_content.category.list.' . $Itemid . '.limit', 'limit', $this->_data->params->get('display_num'));
		}

		if ($category = $this->getCategory()) {
			$model = $this->getModel('Articles', 'ContentModel', array('ignore_request' => true));
			$activeAllParams = new JRegistry();
			foreach($this->_data->params->toArray() as $key => $val ){
				if ( strpos($key, 'show_') !== false && (int) $this->_data->params->get($key) == 0 && $this->_data->params->get('layout_type') == 'blog'){
					$activeAllParams->set($key, 1);
				}else if($this->_data->params->get('layout_type') == '' && strpos($key, 'list_') !== false){
					if ($this->_data->params->get($key) == ''){
						if ( $key == 'filter_field'){
							$activeAllParams->set($key, 'title');
						}else if( $key == 'list_show_date'){
							$activeAllParams->set($key, 'published');
						}else if ($key == 'date_format'){
							$activeAllParams->set($key, 'DD-MM-YY');
						}else{
							$activeAllParams->set($key, 1);
						}
					}
				}else{
					$activeAllParams->set($key, $val);
				}
			}

			$activeAllParams->set('layout_type', '');
			$model->setState('params', $activeAllParams );
			$model->setState('filter.category_id', $category->id);
			$model->setState('filter.published', $this->getState('filter.published'));
			$model->setState('filter.access', 1);
			$model->setState('list.ordering', $this->_buildContentOrderBy());
			$model->setState('list.start', 0);
			$model->setState('list.limit', $limit);
			$model->setState('list.direction', '');
			$model->setState('list.filter', '');
			// filter.subcategories indicates whether to include articles from subcategories in the list or blog
			$model->setState('filter.subcategories', $this->_data->params->get('show_subcat_desc'));
			$model->setState('filter.max_category_levels', $this->_data->params->get('show_subcategory_content'));
			$model->setState('list.links', $this->_data->params->get('num_links'));

			if ( $limit >= 0 ) {
				$this->_data->items = $model->getSpecificItems();
				if ( $this->_data->items === false) {
					$this->setError( $model->getError() );
				}
			}else{
				$this->_data->items = array();
			}
			$this->_data->pagination = $model->getPagination();
		}
	}
	/**
	 *
	 * Get data
	 *
	 * @param Array $pk
	 */
	public function getData( $pk )
	{
		$this->_data = new stdClass();
		$this->_data->items = array();
		$this->_data->children = null;
		$this->_data->category = null;

		jimport('joomla.application.categories');

		$this->setState('category.id', $pk['id']);

		$this->_data->items = $this->getItems();

		// Get some data from the models

		if( isset( $this->state->params ) ) {
			$this->_data->params = $this->state->params;
			$options = array();
			$options['countItems'] = $this->_data->params->get('show_cat_num_articles', 1) || !$this->_data->params->get('show_empty_categories_cat', 0);
		}else{
			$options['countItems'] = 0;
		}

		$categories = JCategories::getInstance('Content', $options);
		$this->_data->category = $categories->get( $this->getState('category.id', 'root') );

		// Compute selected asset permissions.
		if ( is_object( $this->_data->category ) ) {
			// TODO: Why aren't we lazy loading the children and siblings?
			$this->_data->children = $this->_data->category->getChildren();
			$this->_data->parent = false;

			if ($this->_data->category->getParent()) {
				$this->_data->parent = $this->_data->category->getParent();
			}

			// Setup the category parameters.
			$cparams = $this->_data->category->getParams();
			$this->_data->category->params = clone($this->_data->params);
			$this->_data->category->params->merge($cparams);
		}
		else {
			$this->_data->children = false;
			$this->_data->parent = false;
		}

		// PREPARE THE DATA
		// Get the metrics for the structural page layout.
		$numLeading	= $this->_data->params->def('num_leading_articles', 1);
		$numIntro	= $this->_data->params->def('num_intro_articles', 4);
		$numLinks	= $this->_data->params->def('num_links', 4);

		// Compute the article slugs and prepare introtext (runs content plugins).
		for ($i = 0, $n = count($this->_data->items); $i < $n; $i++)
		{
			$item = &$this->_data->items[$i];
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// No link for ROOT category
			if ( $item->parent_alias == 'root' ) {
				$item->parent_slug = null;
			}

			// Ignore content plugins on links.
			if ( $i < $numLeading + $numIntro ) {
				$item->introtext = JHtml::_('content.prepare', $item->introtext);
			}
		}

		// For blog layouts, preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interrogate the arrays.
		if ( ( $this->_data->params->get('layout_type') == 'blog' ) || ( @$pk['layout'] == 'blog') ) {
			$max = count($this->_data->items);

			// The first group is the leading articles.
			$limit = $numLeading;
			for ($i = 0; $i < $limit && $i < $max; $i++) {
				$this->_data->lead_items[$i] = &$this->_data->items[$i];
				// Add router helpers.
				$item = &$this->_data->lead_items[$i];
				$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
				$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
				$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;
			}

			// The second group is the intro articles.
			$limit = $numLeading + $numIntro;
			// Order articles across, then down (or single column mode)
			for ($i = $numLeading; $i < $limit && $i < $max; $i++) {
				$this->_data->intro_items[$i] = &$this->_data->items[$i];
				// Add router helpers.
				$item = &$this->_data->intro_items[$i];
				$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
				$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
				$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;
			}

			$this->_data->columns = max(1, $this->_data->params->def('num_columns', 1));
			$order = $this->_data->params->def('multi_column_order', 1);

			if ( $order == 0 && $this->_data->columns > 1 ) {
				// call order down helper
				$this->_data->intro_items = ContentHelperQuery::orderDownColumns($this->_data->intro_items, $this->_data->columns);
			}

			$limit = $numLeading + $numIntro + $numLinks;

			// The remainder are the links.
			for ($i = $numLeading + $numIntro; $i < $limit && $i < $max; $i++)
			{
				$this->_data->link_items[$i] = &$this->_data->items[$i];
			}
		}

		// Order subcategories
		if (sizeof($this->_data->children)) {
			if ($this->_data->params->get('orderby_pri') == 'alpha' || $this->_data->params->get('orderby_pri') == 'ralpha') {
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_data->children, 'title', ($this->_data->params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		if ( isset( $this->_data->category->id ) ){
			$this->_data->children = array( $this->_data->category->id => $this->_data->children );
		}

		//Escape strings for HTML output
		$this->_data->pageclass_sfx = htmlspecialchars($this->_data->params->get('pageclass_sfx'));

		$this->_data->maxLevel = $this->_data->params->get('maxLevel', -1);

		$this->_data->state = $this->state;

		return $this->_data;
	}

	function getItems()
	{
		$params = $this->getState()->get('params');
		$limit = $this->getState('list.limit');

		if ($this->_articles === null && $category = $this->getCategory()) {
			$model = JSNBaseModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
			$model->setState('params', $this->state->params);
			$model->setState('filter.category_id', $category->id);
			$model->setState('filter.published', $this->getState('filter.published'));
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('filter.language', $this->getState('filter.language'));
			$model->setState('list.ordering', $this->_buildContentOrderBy());
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.limit', $limit);
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.filter', $this->getState('list.filter'));
			// filter.subcategories indicates whether to include articles from subcategories in the list or blog
			$model->setState('filter.subcategories', $this->getState('filter.subcategories'));
			$model->setState('filter.max_category_levels', $this->setState('filter.max_category_levels'));
			$model->setState('list.links', $this->getState('list.links'));

			if ($limit >= 0) {
				$this->_articles = $model->getItems();

				if ($this->_articles === false) {
					$this->setError($model->getError());
				}
			}
			else {
				$this->_articles=array();
			}

			$this->_pagination = $model->getPagination();
			$this->_data->pagination = $this->_pagination;
		}

		return $this->_articles;
	}
}