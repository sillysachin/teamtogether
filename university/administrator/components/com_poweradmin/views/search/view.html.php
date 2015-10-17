<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.html.php 16460 2012-09-26 09:52:25Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

JSNFactory::localimport('libraries.joomlashine.document.media');

class PoweradminViewSearch extends JViewLegacy
{
	/**
	 * @var JApplication
	 */
	protected $app;

	/**
	 * @var JLanguage
	 */
	protected $language;

	/**
	 * @var JUser
	 */
	protected $user;

	public function display($tpl = null)
	{
		$this->app = JFactory::getApplication();
		$this->user = JFactory::getUser();
		$this->document = JFactory::getDocument();

		$this->language = JFactory::getLanguage();
		$this->language->load('plg_system_jsnpoweradmin');

		$this->keyword = $this->app->getUserStateFromRequest('search.keyword', 'keyword', '');
		$this->coverage = $this->app->getUserStateFromRequest('search.coverage', 'coverages', '');

		// Create coverages select box
		$this->coverages = JHTML::_('select.genericlist', $this->getCoverages(), 'coverages', null, 'value', 'text', $this->coverage);
		$this->state = $this->get('state');

		$this->document->addStyleSheet('components/com_poweradmin/assets/css/styles.css');

		$this->addToolbar();
		$this->populateSearch();

		parent::display($tpl);
	}

	/**
	 * Add toolbar to page
	 * @return void
	 */
	private function addToolbar () {
		JToolBarHelper::title(JText::_('JSN_SITE_SEARCH_TITLE'), 'poweradmin-search');
		//JToolBarHelper::help('JSNPOWERADMIN_HELP_SITESEARCH');
	}

	/**
	 * Return a list of coverages that use to generate select box
	 * @return Array
	 */
	private function getCoverages () {
		$coverages = array();
		foreach (PoweradminHelper::getSearchCoverages() as $coverage) {
			if ($coverage == 'adminmenus')
				continue;

			$coverages[] = array(
				'value' => $coverage,
				'text'	=> JText::_('PLG_JSNADMINBAR_SEARCH_COVERAGE_' . strtoupper (str_replace(JSN_3RD_EXTENSION_STRING .'-', '', $coverage)),true)
			);
		}

		return $coverages;
	}

	/**
	 * Retrieve search coverage configuration
	 * @param String $coverage
	 * @return Array
	 */
	private function getConfiguration ($coverage) {
		$configurations = array(
			'articles' => array(
				'language' 		=> 'com_content',
				'modelfile'			=> 'components/com_content/models/articles.php',
				'viewfile'			=> 'articles',
				'modelname'			=> 'ContentModelArticles',
				'order'			=> 'a.title'
			),

			'components'		=> array(
				'tabs'	=> array(
					'com_banners' => array(
						'title'			=> 'Banners',
						'language' 		=> 'com_banners',
						'modelfile'			=> 'components/com_banners/models/banners.php',
						'viewfile'			=> 'banners_items',
						'modelname'			=> 'BannersModelBanners',
						'order'			=> 'name'
					),

					'com_banners_categories' => array(
						'title'			=> 'Banners Categories',
						'language' 		=> 'com_categories',
						'modelfile'			=> 'components/com_categories/models/categories.php',
						'viewfile'			=> 'categories',
						'modelname'			=> 'CategoriesModelCategories',
						'order'			=> 'a.lft',
						'filters'		=> array(
							'filter.extension' => 'com_banners'
						)
					),

					'com_banners_clients' => array(
						'title'			=> 'Banners Clients',
						'language' 		=> 'com_banners',
						'modelfile'			=> 'components/com_banners/models/clients.php',
						'viewfile'			=> 'banners_clients',
						'modelname'			=> 'BannersModelClients',
						'order'			=> 'a.name'
					),

					'com_contact' => array(
						'title'			=> 'Contacts',
						'language' 		=> 'com_contact',
						'modelfile'			=> 'components/com_contact/models/contacts.php',
						'viewfile'			=> 'contacts_items',
						'modelname'			=> 'ContactModelContacts',
						'order'			=> 'name'
					),

					'com_contact_categories' => array(
						'title'			=> 'Contacts Categories',
						'language' 		=> 'com_categories',
						'modelfile'			=> 'components/com_categories/models/categories.php',
						'viewfile'			=> 'categories',
						'modelname'			=> 'CategoriesModelCategories',
						'order'			=> 'a.lft',
						'filters'		=> array(
							'filter.extension' => 'com_contact'
						)
					),

					'com_messages' => array(
						'title'			=> 'Messages',
						'language' 		=> 'com_messages',
						'modelfile'			=> 'components/com_messages/models/messages.php',
						'viewfile'			=> 'messages',
						'modelname'			=> 'MessagesModelMessages',
						'order'			=> 'a.date_time'
					),

					'com_newsfeeds' => array(
						'title'			=> 'Feeds',
						'language' 		=> 'com_newsfeeds',
						'modelfile'			=> 'components/com_newsfeeds/models/newsfeeds.php',
						'viewfile'			=> 'feeds',
						'modelname'			=> 'NewsfeedsModelNewsfeeds',
						'order'			=> 'a.name'
					),

					'com_newsfeeds_categories' => array(
						'title'			=> 'Feeds Categories',
						'language' 		=> 'com_categories',
						'modelfile'			=> 'components/com_categories/models/categories.php',
						'viewfile'			=> 'categories',
						'modelname'			=> 'CategoriesModelCategories',
						'order'			=> 'a.lft',
						'filters'		=> array(
							'filter.extension' => 'com_newsfeeds'
						)
					),

					'com_weblinks' => array(
						'title'			=> 'Web Links',
						'language' 		=> 'com_weblinks',
						'modelfile'			=> 'components/com_weblinks/models/weblinks.php',
						'viewfile'			=> 'weblinks',
						'modelname'			=> 'WeblinksModelWeblinks',
						'order'			=> 'a.title',
					),

					'com_weblinks_categories' => array(
						'title'			=> 'Web Links Categories',
						'language' 		=> 'com_categories',
						'modelfile'			=> 'components/com_categories/models/categories.php',
						'viewfile'			=> 'categories',
						'modelname'			=> 'CategoriesModelCategories',
						'order'			=> 'a.lft',
						'filters'		=> array(
							'filter.extension' => 'com_weblinks'
						)
					)
				)
			),

			'categories' => array(
				'language' 		=> 'com_categories',
				'modelfile'			=> 'components/com_categories/models/categories.php',
				'viewfile'			=> 'categories',
				'modelname'			=> 'CategoriesModelCategories',
				'order'			=> 'a.lft'
			),

			'modules' => array(
				'language' 		=> 'com_modules',
				'modelfile'			=> 'components/com_modules/models/modules.php',
				'viewfile'			=> 'modules',
				'modelname'			=> 'ModulesModelModules',
				'order'			=> 'a.title'
			),

			'plugins' => array(
				'language' 		=> 'com_plugins',
				'modelfile'			=> 'components/com_plugins/models/plugins.php',
				'viewfile'			=> 'plugins',
				'modelname'			=> 'PluginsModelPlugins',
				'order'			=> 'a.title'
			),

			'menus' => array(
				'language' 		=> 'com_menus',
				'modelfile'			=> 'components/com_poweradmin/models/menusearch.php',
				'viewfile'			=> 'menus',
				'modelname'			=> 'PowerAdminModelMenuSearch',
				'order'			=> 'a.lft'
			),

			'templates' => array(
				'language' 		=> 'com_templates',
				'modelfile'			=> 'components/com_templates/models/styles.php',
				'viewfile'			=> 'templates',
				'modelname'			=> 'TemplatesModelStyles',
				'order'			=> 'a.title'
			),

			'users' => array(
				'language' 		=> 'com_users',
				'modelfile'			=> 'components/com_users/models/users.php',
				'viewfile'			=> 'users',
				'modelname'			=> 'UsersModelUsers',
				'order'			=> 'a.name'
			)
		);

		$supportedExtConfigs = JSNPaExtensionsHelper::getExtConfigurations(str_ireplace(JSN_3RD_EXTENSION_STRING . '-', '', $coverage));

		if (count($supportedExtConfigs))
		{
			foreach ($supportedExtConfigs as $key=>$config)
			{
				$configurations[JSN_3RD_EXTENSION_STRING . '-' .strtolower($key)]	= $config;
			}
		}

		if (!isset($configurations[$coverage]))
			return null;

		$config = $configurations[$coverage];
		if (!isset($config['tabs']))
			return $config;

		$this->tabs = array();
		foreach ($config['tabs'] as $key => $tab)
		{
			if ($coverage == 'components')
				continue;

			$model = $this->getItemModel($tab, $this->state, $key);
			$total = $model->getTotal();

			if ($total > 0) {
				$this->tabs[$key] = array(
					'title' => "{$tab['title']} ({$total})",
					'selected' => false
				);
			}
		}

		$selectedTab = $this->app->getUserStateFromRequest("components.selected", 'tab', null);
		if ($selectedTab == null || !isset($this->tabs[$selectedTab])) {
			$tabKeys = array_keys($this->tabs);
			$selectedTab = array_shift($tabKeys);

			$this->app->setUserState('components.selected', $selectedTab);
		}

		if (empty($this->tabs))
			return null;

		$this->tabs[$selectedTab]['selected'] = true;
		return $config['tabs'][$selectedTab];
	}

	/**
	 * Load model instance that determined by coverage.
	 * Retrieve results and assign it to view
	 * @return void
	 */
	private function populateSearch () {
		$this->config = $this->getConfiguration($this->coverage);
		if ($this->config == null)
			return;
		// Create model instance
		$model = $this->getItemModel($this->config, $this->state, $this->coverage);

		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
	}

	/**
	 * Return an instance of a model that loaded base on configuration
	 * @param mixed $config
	 * @param object $state
	 * @param string $coverage
	 * @return JModelList
	 */
	private function getItemModel ($config, $state, $coverage) {
		$path	=	'';
		// Load model file
		if (!$config['path'])
		{
			$path	= JPATH_ADMINISTRATOR.'/';
		}
		else
		{
			$path	= $config['path'] . '/';
		}

		require_once $path . $config['modelfile'];
		// Load component language
		$this->language->load($config['language'], JPATH_ADMINISTRATOR);

		// Create model instance
		$model = new $config['modelname'](array('state' => $state));
		$model->getState('filter.search');

		$currentCoverage = $this->app->getUserStateFromRequest('components.selected', 'tab');
		if ($currentCoverage == $coverage) {
			$order = $this->app->getUserStateFromRequest("{$currentCoverage}.{$config['modelname']}.order", 'filter_order', $config['order']);
			$orderDir = $this->app->getUserStateFromRequest("{$currentCoverage}.{$config['modelname']}.orderDir", 'filter_order_Dir', 'asc');

			$state->set('list.ordering', $order);
			$state->set('list.direction', $orderDir);
		}

		$state->set('filter.search', $this->keyword);
		$state->set('filter.published', 'all');
		$state->set('filter.state', 'all');

		if (isset($config['filters']) && is_array($config['filters'])) {
			foreach ($config['filters'] as $key => $value) {
				$state->set($key, $value);
			}
		}
		return $model;
	}

	public function includeViewFile($config)
	{
		if ($config !== null)
		{
			if (!$config['path'])
			{
				include dirname(__FILE__).'/tmpl/'."{$config['viewfile']}.php";
			}
			else
			{
				include $config['path'].'/'."{$config['viewfile']}.php";
			}
		}
	}
}