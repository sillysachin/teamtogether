<?php
/**
 * @version    $Id: view.php 18023 2012-11-06 07:57:54Z cuongnm $
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import necessary Joomla libraries
require_once JPATH_ROOT . '/administrator/components/com_categories/views/categories/view.html.php';
require_once JPATH_ROOT . '/administrator/components/com_categories/models/categories.php';
require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/categories/categories.php';

// Load language
$lang = JFactory::getLanguage();
$lang->load('com_categories');

/**
 * Menu selection view class.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNCategoriesView extends CategoriesViewCategories
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 */
	public function __construct($config = array())
	{
		// Display only the component output
		JFactory::getApplication()->input->def('tmpl', 'component');

		parent::__construct($config);

		// Load category model
		$model = JSNBaseModel::getInstance('Categories', 'CategoriesModel');
		$this->setModel($model, true);

		// Include the component HTML helpers
		$this->addTemplatePath(dirname(__FILE__) . '/tmpl');
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// Load assets
		JSNBaseHelper::loadAssets();
		echo JSNHtmlAsset::loadScript('jsn/selectorFilter',array(),true);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
	}
}
