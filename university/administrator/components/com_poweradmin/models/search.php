<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: search.php 16477 2012-09-27 03:23:59Z hiepnv $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

class PoweradminModelSearch extends JModelList
{
	private function getDescriptionMap()
	{
		$_descriptionMap = array(
				'menus' 					=> "Menu: \n{desc}",
				'users' 					=> "Login name – Email: \n{desc}",
				'categories' 				=> "Category description: \n{desc}",
				'articles' 					=> "Article intro text: \n{desc}",
				'modules' 					=> "Position: \n {desc}\n\nLocation: {location}",
				'plugins' 					=> "Plug-in type: \n{desc}",
				'templates' 				=> "Description: \n{desc}\n\nLocation: {location}",
				// Component specific

				'com_banners' 				=> "Banners: \n{desc}",
				'com_banners_categories' 	=> "Categories: \r{desc}",
				'com_banners_clients' 		=> "Clients: \r{desc}",
				'com_contacts' 				=> "Contacts: \r{desc}",
				'com_contacts_categories' 	=> "Categories: \r{desc}",
				'com_messages' 				=> "Messages: \r{desc}",
				'com_newsfeeds' 			=> "News Feed: \r{desc}",
				'com_newsfeeds_categories' 	=> "Categories: \r{desc}",
				'com_weblinks' 				=> "Web Links: \r{desc}",
				'com_weblinks_categories' 	=> "Categories: \r{desc}"

		// 		'com_zoo' 					=> "Item intro text: \r",
		// 		'com_zoo_categories'		=> "Category description: \r",
		// 		'com_easyblog' 				=> "Item intro text: \r",
		// 		'com_easyblog_categories'		=> "Category description: \r",
		// 		'com_virtuemart' 				=> "Item intro text: \r",
		// 		'com_virtuemart_categories'		=> "Category description: \r",

		);

		JPluginHelper::importPlugin('jsnpoweradmin');
		$dispatcher	= JDispatcher::getInstance();
		$extDescMap = $dispatcher->trigger('getSpotLightDescriptionMap');
		if (count($extDescMap))
		{
			foreach ($extDescMap as $k=>$v)
			{
				$_descriptionMap = array_merge($v, $_descriptionMap);
			}
		}

		return $_descriptionMap;
	}

	/**
	 * Retrieve the number of results that matched with condition
	 * @return int
	 */
	public function getTotal ()
	{
		$this->context = $this->getState('search.coverage');

		$query = $this->getListQuery(true);
		$mapping = $this->_getTableMapping($this->context);
		if ($mapping == null)
			return 0;

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	public function getItems ()
	{
		$params = JSNConfigHelper::get('com_poweradmin');
		$resultLimit = (int)$params->get('search_result_num', 10);

		$this->context = $this->getState('search.coverage');
		$mapping = $this->_getTableMapping($this->context);

		if ($mapping == null)
			return array();

		$results = $this->_getList($this->getListQuery(), 0, $resultLimit);
		if (!is_array($results) || count($results) == 0)
			return array();

		$items = array();
		foreach ($results as $record) {
			$item = new stdClass;
			$item->type = 'item';
			$item->icon = $mapping['icon'];
			//$item->data = $record;

			if (isset($record->manifest_cache)) {
				$lang = JFactory::getLanguage();
				$item->title = (isset($record->name)) ? $record->name : $record->title;

				switch ($this->context) {
					case 'plugins':
						$lang->load("{$record->name}.sys", JPATH_ADMINISTRATOR);
						$item->title = $this->_title(JText::_(strtoupper($record->name),true));
						$item->description = $this->_description(ucfirst($record->folder));
					break;

					case 'modules':
						$lang->load("{$record->element}.sys", JPATH_SITE);
						$_location = $record->client_id ? JText::_('JSITE') : JText::_('JADMINISTRATOR');
						$_location = $_location;
						$item->description = $this->_description($record->position, $_location);
					break;

					case 'templates':
						$lang->load("tpl_{$record->element}.sys", JPATH_ADMINISTRATOR);
						$lang->load("tpl_{$record->element}.sys", JPATH_SITE);
                        $_location = $record->client_id ? JText::_('JSITE') : JText::_('JADMINISTRATOR');
                        $_location = $_location;

						$manifest = json_decode($record->manifest_cache);
						$item->description = $this->_description(JText::_(strtoupper($manifest->description),true), $_location);
					break;
				}
			}
			elseif (!isset($mapping['fields'])) {
				$item->title = $this->_title($record->title);
				$item->description = $this->_description($record->description);
			}
			else {
				$_title = $mapping['fields']['title'];
				$_parts = array();
				if(preg_match_all('/{([^\}]+)}/i', $_title, $_parts)){
					$found 		= $_parts[0];
					$replaced 	= $_parts[1];
					foreach ($found as $k=>$value){
						$_title = str_replace($value, $record->$replaced[$k], $_title);
					}
				}
				$item->title =  $this->_title($_title);

				$_description = $mapping['fields']['description'];
				$_parts = array();
				if(preg_match_all('/{([^\}]+)}/i', $_description, $_parts)){
					$found 		= $_parts[0];
					$replaced 	= $_parts[1];
					foreach ($found as $k=>$value){
						$_description = str_replace($value, $record->$replaced[$k], $_description);
					}
				}
				$item->description =  $this->_description($_description);
			}

			$_link = $mapping['link'];
			$_parts = array();
			if(preg_match_all('/{([^\}]+)}/i', $_link, $_parts)){
				$found 		= $_parts[0];
				$replaced 	= $_parts[1];
				foreach ($found as $k=>$value){
					$_link = str_replace($value, $record->$replaced[$k], $_link);
				}
			}
			$item->link =  $_link;

			$item->checkedOut = $record->checked_out;
			$items[] = $item;
		}
		return $items;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery ($countable = false)
	{
		$query = $this->_db->getQuery(true);
		$coverage = $this->getState('search.coverage');
		$keyword  = $this->getState('search.keyword');

		if ($coverage == 'menus') {
			if ($countable == true) {
				$query->select('COUNT(*)');
			}
			else {
				$query->select('m.*, t.title as type_name');
			}

			$query->from('#__menu AS m');
			$query->innerJoin('#__menu_types AS t ON m.menutype=t.menutype');
			$query->where("m.title LIKE '%{$keyword}%' AND m.published!=-2");

			return $query;
		}
		elseif ($coverage == 'modules') {
			if ($countable == true) {
				$query->select('COUNT(*)');
			}
			else {
				$query->select('m.*, e.element, e.manifest_cache');
			}

			$query->from('#__modules AS m');
			$query->innerJoin('#__extensions AS e ON (m.module = e.element AND e.type=\'module\')');
			$query->where("m.title LIKE '%{$keyword}%' AND e.client_id=0 AND m.published!=-2");

			return $query;
		}
		elseif ($coverage == 'templates') {
			if ($countable == true) {
				$query->select('COUNT(*)');
			}
			else {
				$query->select('t.*, e.element, e.manifest_cache');
			}

			$query->from('#__template_styles AS t');
			$query->innerJoin('#__extensions AS e ON t.template=e.element');
			$query->where("t.title LIKE '%{$keyword}%'");

			return $query;
		}

		if ($countable == true) {
			$query->select('COUNT(*)');
		}
		else {
			$query->select('*');
		}

		$query->from($this->_getDataTable());
		$query->where($this->_getDataConditions());

		return $query;
	}

	private function _getTableMapping ($name) {
		// Support Virtuemart.
// 		$vmLang = '';
// 		if(file_exists(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php')){
// 			require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php';
// 			$langs =  (array) VmConfig::get('active_languages');

// 			jimport('joomla.language.helper');
// 			$languages = JLanguageHelper::getLanguages('lang_code');
// 			$vmLang = JFactory::getLanguage()->getTag();

// 			if ( ! $vmLang ) {
// 				// use user default
// 				$lang =JFactory::getLanguage();
// 				$vmLang = $lang->getTag();
// 			}

// 			if(!in_array($vmLang, $langs)) {
// 				$params = JComponentHelper::getParams('com_languages');
// 				$vmLang = $params->get('site', 'en-GB');//use default joomla
// 			}
// 		}
// 		$vmLang = $vmLang ? '_' .  strtolower(strtr($vmLang,'-','_')) : '';

		// ------------------------

		$mappings = array(
			'menus' => array(
				'fields' => array('title' => '{title}', 'description' => '{type_name}'),
				//'icon'	=> 'cls:icon-16-menu',
				'link'	=> 'index.php?option=com_menus&task=item.edit&id={id}'
			),

			'users' => array(
				'name' => '#__users',
				'lookup' => array('name', 'username'),
				'fields' => array('title' => '{name}', 'description' => '{username} - {email}'),
				//'icon'	=> 'cls:icon-16-user',
				'link'	=> 'index.php?option=com_users&task=user.edit&id={id}',
			),

			'categories' => array(
				'name'	=> '#__categories',
				'lookup' => array('title'),
				//'icon'	=> 'cls:icon-16-category',
				'link'	=> 'index.php?option=com_categories&task=category.edit&id={id}&extension={extension}',
				'conditions' => 'extension=\'com_content\''
			),

			'articles' => array(
				'name' => '#__content',
				'lookup' => array('title'),
				'fields' => array('title' => '{title}', 'description' => "{introtext}"),
				//'icon'	=> 'cls:icon-16-article',
				'link'	=> 'index.php?option=com_content&task=article.edit&id={id}',
			),

			'modules' => array(
				'name' => '#__modules',
				'lookup' => array('title'),
				'fields' => array('title' => '{title}', 'description' => ''),
				//'icon'	=> 'cls:icon-16-module',
				'link'	=> 'index.php?option=com_modules&task=module.edit&id={id}'
			),

			'plugins' => array(
				'name' => '#__extensions',
				'lookup' => array('name'),
				'conditions' => 'type=\'plugin\'',
				'fields' => array('title' => '{name}', 'description' => ''),
				//'icon'	=> 'cls:icon-16-plugin',
				'link'	=> 'index.php?option=com_plugins&task=plugin.edit&extension_id={extension_id}',
			),

			'templates' => array(
				'name' => '#__template_styles',
				'lookup' => array('title'),
				'fields' => array('title' => '{title}','description' => ''),
				//'icon'	=> 'cls:icon-16-themes',
				'link'	=> 'index.php?option=com_templates&task=style.edit&id={id}'
			),

			'com_banners' => array(
				'name' => '#__banners',
				'lookup' => array('name'),
				'fields' => array('title' => '{name}', 'description' => '{description}'),
				//'icon'	=> 'cls:icon-16-banners',
				'link'	=> 'index.php?option=com_banners&task=banner.edit&id={id}',
			),

			'com_banners_categories' => array(
				'name'	=> '#__categories',
				'lookup' => array('title'),
				//'icon'	=> 'cls:icon-16-banners-cat',
				'link'	=> 'index.php?option=com_categories&task=category.edit&id={id}&extension=com_banners',
				'conditions' => 'extension=\'com_banners\'',
			),

			'com_banners_clients' => array(
				'name'	=> '#__banner_clients',
				'lookup' => array('name'),
				//'icon'	=> 'cls:icon-16-banners-clients',
				'fields' => array('title' => '{name}', 'description' => '{contact} - {email}'),
				'link'	=> 'index.php?option=com_banners&task=client.edit&id={id}',
			),

			'com_contacts' => array(
				'name'	=> '#__contact_details',
				'lookup' => array('name'),
				//'icon'	=> 'cls:icon-16-contact',
				'fields' => array('title' => '{name}', 'description' => '{misc}'),
				'link'	=> 'index.php?option=com_contacts&task=contact.edit&id={id}',
			),

			'com_contacts_categories' => array(
				'name'	=> '#__categories',
				'lookup' => array('title'),
				//'icon'	=> 'cls:icon-16-contact-cat',
				'link'	=> 'index.php?option=com_categories&task=category.edit&id={id}&extension=com_contacts',
				'conditions' => 'extension=\'com_contacts\'',
			),

			'com_messages' => array(
				'name'	=> '#__messages',
				'lookup' => array('subject'),
				//'icon'	=> 'cls:icon-16-messages-read',
				'fields' => array('title' => '{subject}', 'description' => '{message}'),
				'link'	=> 'index.php?option=com_messages&view=message&message_id={message_id}'
			),

			'com_newsfeeds' => array(
				'name'	=> '#__newsfeeds',
				'lookup' => array('name'),
				//'icon'	=> 'cls:icon-16-messages-read',
				'fields' => array('title' => '{name}', 'description' => ''),
				'link'	=> 'index.php?option=com_newsfeeds&task=newsfeeds.edit&id={id}',
			),

			'com_newsfeeds_categories' => array(
				'name'	=> '#__categories',
				'lookup' => array('title'),
				//'icon'	=> 'cls:icon-16-newsfeeds-cat',
				'link'	=> 'index.php?option=com_categories&task=category.edit&id={id}&extension=com_newsfeeds',
				'conditions' => 'extension=\'com_newsfeeds\'',
			),

			'com_weblinks' => array(
				'name'	=> '#__weblinks',
				'lookup' => array('title'),
				//'icon'	=> 'cls:icon-16-weblinks',
				'link'	=> 'index.php?option=com_weblinks&task=weblink.edit&id={id}',
			),

			'com_weblinks_categories' => array(
				'name'	=> '#__categories',
				'lookup' => array('title'),
				//'icon'	=> 'cls:icon-16-weblinks-cat',
				'link'	=> 'index.php?option=com_categories&task=category.edit&id={id}&extension=com_weblinks',
				'conditions' => 'extension=\'com_weblinks\'',
			)
		);

		$supportedExtList	= JPluginHelper::getPlugin('jsnpoweradmin');
		if (count($supportedExtList))
		{
			foreach ($supportedExtList as $supportedExt)
			{
				$_mappings = JSNPaExtensionsHelper::executeExtMethod($supportedExt->name, 'getTableMapping');
				if (count($_mappings))
				{

					foreach ($_mappings as $k=>$m)
					{
						$mappings[$k]	= $m;
					}
				}
			}
		}

		$params = JSNConfigHelper::get('com_poweradmin');
		if (intval($params->get('search_trashed', 0)) == 0)
		{
			$mappings['categories']['conditions'] 				.= ' AND published!=-2';
			$mappings['articles']['conditions'] 				= 'state!=-2';
			$mappings['com_banners']['conditions'] 				= 'state!=-2';
			$mappings['com_banners_categories']['conditions'] 	.= ' AND published!=-2';
			$mappings['com_banners_clients']['conditions'] 		= 'state!=-2';
			$mappings['com_contacts']['conditions'] 			= 'published!=-2';
			$mappings['com_contacts_categories']['conditions'] 	.= ' AND published!=-2';
			$mappings['com_newsfeeds']['conditions'] 			= 'published!=-2';
			$mappings['com_newsfeeds_categories']['conditions'] .= ' AND published!=-2';
			$mappings['com_weblinks']['conditions'] 			= 'state!=-2';
			$mappings['com_weblinks_categories']['conditions'] 	.= ' AND published!=-2';
		}

		return !isset($mappings[$name]) ? null : $mappings[$name];
	}

	private function _getDataTable ()
	{
		$coverage = $this->getState('search.coverage');
		$mapping  = $this->_getTableMapping($coverage);

		return $mapping['name'];
	}

	private function _getDataConditions ()
	{
		$coverage = $this->getState('search.coverage');
		$keyword  = $this->getState('search.keyword');

		$mapping  = $this->_getTableMapping($coverage);
		$conditions = array();

		foreach ($mapping['lookup'] as $field) {
			$conditions[] = "`{$field}` LIKE '%{$keyword}%'";
		}

		$where = implode(' OR ', $conditions);
		if (isset($mapping['conditions'])) {
			$where = "({$where}) AND ({$mapping['conditions']})";
		}

		return $where;
	}

	private function _description ($description, $location = null) {
		$description = trim(strip_tags($description));

		// Remove unnecessarily while space
		while (strpos($description, '  ')) {
			$description = str_replace('  ', ' ', $description);
		}

		$wordsLimit = 30;
		if (str_word_count($description) > $wordsLimit) {
			$words = explode(' ', $description);
			$usableWords = array_slice($words, 0, $wordsLimit);
			$description = trim(implode(' ', $usableWords), '\'".');
			$description.= '...';
		}

		$coverage = $this->getState('search.coverage');
		$_descriptionMap	= $this->getDescriptionMap();
		if (isset($_descriptionMap[$coverage]) && !empty($description))
		{
			$description = str_replace('{desc}', $description, $_descriptionMap[$coverage]);
			if(isset($location))
			{
                $description = str_replace('{location}', $location, $description);
			}
		}

		return $description;
	}



	function _title ($title) {
		$keyword = strtolower($this->getState('search.keyword'));
		$lowerTitle = strtolower($title);
		$maxLength = 30;

		if (strpos($lowerTitle, $keyword) >= $maxLength) {
			$totalLength = strlen($title);
			$keywordLength = strlen($keyword);
			$keywordPosition = strpos($lowerTitle, $keyword);
			$leftLength = ($maxLength - $keywordLength)/2;

			if ($totalLength <= $keywordPosition + $keywordLength) {
				$startPos = $totalLength - $maxLength;
			}
			else {
				$startPos = $keywordPosition - $leftLength;
			}

			$title = '... ' . substr($title, (int)$startPos);
		}

		return (strlen($title) > 30) ? substr($title, 0, 30).' ...' : $title;
	}
}
?>