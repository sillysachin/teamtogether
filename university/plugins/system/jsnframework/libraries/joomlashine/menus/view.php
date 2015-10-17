<?php
/**
 * @version    $Id$
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

// Load language
$lang = JFactory::getLanguage();
$lang->load('com_menus');

if ((defined('JSN_UNIFORM_VERSION') && version_compare(JSN_UNIFORM_VERSION, '1.4.0', '>')) || !defined('JSN_UNIFORM_VERSION'))
{
		// Import necessary Joomla libraries
		require_once JPATH_ROOT . '/administrator/components/com_menus/views/menus/view.html.php';
		require_once JPATH_ROOT . '/administrator/components/com_menus/models/menus.php';
		require_once JPATH_ROOT . '/administrator/components/com_menus/models/menu.php';
		require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/menus/menus.php';
		/**
		 * Menu selection view class.
		 *
		 * @package  JSN_Framework
		 * @since    1.0.3
		 */
		class JSNMenusView extends MenusViewMenus
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

						// Load menu model
						$model = JSNBaseModel::getInstance('Menus', 'MenusModel');
						$this->setModel($model, true);
				}

				/**
				 * Display the view
				 *
				 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
				 *
				 * @return  void
				 */
				public function display($tpl = null)
				{
						// Include the component HTML helpers.
						$this->_path['template'] = array(JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/menus/tmpl');
						JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

						// Load assets
						JSNBaseHelper::loadAssets();

						parent::display($tpl);
						echo JSNHtmlAsset::loadScript('jsn/selectorFilter', array(), true);
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
}
else
{
		// Import necessary Joomla libraries
		require_once JPATH_ROOT . '/administrator/components/com_menus/views/items/view.html.php';
		require_once JPATH_ROOT . '/administrator/components/com_menus/models/items.php';
		require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/menuitems/menus.php';
		/**
		 * Menu selection view class.
		 *
		 * @package  JSN_Framework
		 * @since    1.0.3
		 */
		class JSNMenusView extends MenusViewItems
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

						// Load menu model
						$model = JSNBaseModel::getInstance('Items', 'MenusModel');
						$this->setModel($model, true);
				}

				/**
				 * Display the view
				 *
				 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
				 *
				 * @return  void
				 */
				public function display($tpl = null)
				{
						// Include the component HTML helpers.
						$this->_path['template'] = array(JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/menuitems/tmpl');
						JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

						// Load assets
						JSNBaseHelper::loadAssets();

						parent::display($tpl);
						echo JSNHtmlAsset::loadScript('jsn/selectorFilter', array(), true);
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
}