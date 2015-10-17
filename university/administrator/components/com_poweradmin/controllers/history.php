<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: history.php 16339 2012-09-24 10:22:59Z hiepnv $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

// Register tables class path
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');

// Load helper
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/poweradmin.php';
error_reporting(0);
/**
 * This is a controller class, it have some methods to load, save
 * activities. All methods will be called from client-side by ajax request
 * 
 * @author binhpt
 */
class PoweradminControllerHistory extends JControllerLegacy
{
	var $_descriptionMaps = array(
		'com_menus.item' 			=> "Menu: \n{desc}",
		'com_categories.category'	=> "Category description: \n{desc}",
		'com_content.article'		=> "Article intro text: \n{desc}",
		'com_modules.module' 		=> "Module description: \n{desc}",
		'com_plugins.plugin' 		=> "Plug-in description: \n{desc}",
		'com_templates.style' 		=> "Template description: \n{desc}",
		
		'com_banners.banner' 		=> "Banners: \n{desc}",
		'com_contacts.contact'		=> "Contacts: \r{desc}",
		'com_weblinks.weblink'		=> "Web Links: \r{desc}"
	);
	
	/**
	 * Retrieve all activities of current user in database
	 * and response to client as JSON format.
	 * 
	 * @return void
	 * @author binhpt
	 */
	public function load()
	{
		// Get current user
		$user = JFactory::getUser();
		
		// Get Database object instance
		$dbo = JFactory::getDBO();
		
		$params = JSNConfigHelper::get('com_poweradmin');

		// Query histories
		$limit = $params->get('history_count', 30);
		$dbo->setQuery("SELECT * FROM #__jsn_poweradmin_history WHERE user_id={$user->id} AND is_deleted = 0 ORDER BY visited DESC LIMIT {$limit}");
		$histories = $dbo->loadObjectList();
		
		$_histories = array();
		foreach ($histories as $history) {
			$_history = new stdClass();
			$_history->title = (strlen($history->title) > 40) ? substr($history->title, 0, 40).' ...' : $history->title;
			$_history->link = "index.php?option=com_poweradmin&task=history.open&id={$history->id}";
			$_history->css = '';
			$_history->deleted = $history->is_deleted;
			$_history->fulltitle = $history->description;
			
			if (!empty($_history->fulltitle)) {
				$params = array();
				parse_str($history->object_key, $params);
				
				// Remove unnecessarily while space
				while (strpos($_history->fulltitle, '  ')) {
					$_history->fulltitle = str_replace('  ', ' ', $_history->fulltitle);
				}
				
				$wordsLimit = 30;
				if (str_word_count($_history->fulltitle) > $wordsLimit) {
					$words = explode(' ', $_history->fulltitle);
					$usableWords = array_slice($words, 0, $wordsLimit);
					$_history->fulltitle = trim(implode(' ', $usableWords), '\'".');
					$_history->fulltitle.= '...';
				}
				
				if (isset($params['view']) && isset($this->_descriptionMaps["{$params['option']}.{$params['view']}"]))
					$_history->fulltitle = str_replace('{desc}', $_history->fulltitle, $this->_descriptionMaps["{$params['option']}.{$params['view']}"]);
			}
			else {
				$_history->fulltitle = "{$history->list_page} \"{$history->title}\"";
			}
			
			$_histories[] = $_history;
		}
		
		echo json_encode($_histories);
		jexit();
	}
	
	/**
	 * This task will receive id of history and redirect browser
	 * to edit form that allow user to edit item information
	 * @return void
	 */
	public function open()
	{
		$id = JRequest::getInt('id', 0);
		if ($id == 0) {
			header("location: {$_SERVER['HTTP_RERFERER']}");
			jexit();
		}
		
		$history = JTable::getInstance('History', 'PowerAdminTable');
		$history->load($id);
		
		if ($history->is_deleted == 1) {
			header("location: {$_SERVER['HTTP_RERFERER']}");
			jexit();
		}

		if (empty($history->form)) {
			$params = array();
			parse_str($history->params, $params);
			
			if (preg_match('/admin|config|checkin|cache|login|users|menus|content|categories|media|banners|contact|messages|newsfeeds|redirect|search|weblinks|installer|modules|plugins|templates|languages/i', $params['option']) && isset($params['view']) && isset($params['layout'])) {
				$params['task'] = "{$params['view']}.{$params['layout']}";
				unset($params['view']);
				unset($params['layout']);
				
				$history->params = str_replace('&amp;', '&', http_build_query($params));
			}

			header("location: index.php?{$history->params}");
		}
		else {
			$form = json_decode($history->form, true);
			$fields = '';
			
			foreach ($form as $name => $value) {
				if (!is_array($value))
					$fields.= "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\" />";
				else {
					foreach ($value as $key => $val) {
						$fields.= "<input type=\"hidden\" name=\"{$name}[{$key}]\" value=\"{$val}\" />";
					}
				}
			}
			
			echo "<form id=\"edit-form\" action=\"\" method=\"post\">{$fields}</form>";
			echo "<script type=\"text/javascript\">document.getElementById('edit-form').submit()</script>";
		}
		
		jexit();
	}
	
	/**
	 * Save activity object that received from client
	 * 
	 * @return void
	 */
	public function save()
	{
		if (JRequest::getMethod() == 'GET')
			jexit();
			
		$post = JRequest::get('post');
		$session = JFactory::getSession();

		if (!isset($post['pageKey']) || !isset($post['title']) || empty($post['title']))
			jexit();
			
		if (isset($post['postSessionKey'])) {
			$historyId = $this->saveByPost($session, $post, $post['pageKey'], $post['title']);
			$session->clear($post['postSessionKey']);
		}
		elseif (isset($post['lastClickedLink'])) {
			$historyId = $this->saveByGet($session, $post, $post['pageKey'], $post['title']);
		}
		
		$params = JSNConfigHelper::get('com_poweradmin');
		$limit = $params->get('history_count', 10);

		$dbo = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->select('id')
			->from('#__jsn_poweradmin_history')
			->orderBy('visited DESC');

		$dbo->setQuery($query, 0, $limit);
		$ids = $dbo->loadColumn();
		
		$dbo->setQuery("DELETE FROM #__jsn_poweradmin_history WHERE id NOT IN(".implode(', ', $ids).")");
		$dbo->query();
		
		echo $historyId;
		
		// Close proccess to prevent output
		jexit();
	}
	
	/**
	 * Save history that associated with query string
	 * 
	 * @param JSession $session
	 * @param mixed $post
	 * @param string $pageKey
	 * @param string $title
	 * 
	 * @return int ID of saved history
	 */
	private function saveByGet($session, $post, $pageKey, $title)
	{
		$link = $post['lastClickedLink'];
		if ($post['lastClickedLink'] != $post['currentLink'] && preg_match('/(cid|id)=([0-9]+)/i', $post['currentLink']) && !preg_match('/(cid|id)=([0-9]+)/i', $post['lastClickedLink'])) {
			$link = $post['currentLink'];
		}
		
		if (preg_match('/(cid|id)=[0-9]+/i', $link) && preg_match('/(cid|id)=([0-9]+)/i', $post['currentLink'], $matches)) {
			$link = preg_replace('/(cid|id)=([0-9]+)/i', '\\1='.$matches[2], $link);
		}
		
		$params = array();
		parse_str($link, $params);
		
		$object_id = 0;
		if (isset($params['id']))
			$object_id = $params['id'];
		else if (isset($params['cid'])) {
			$object_id = (is_array($params['cid'])) ? array_shift($params['cid']) : $params['cid'];
		}
		else {
			foreach ($params as $key => $value) {
				if (preg_match('/[\-_\.]?id$/i', $key) && is_numeric($value)) {
					$object_id = $value;
					break;
				}
			}
		}
		
		// Skip save history if object id is not found
		if ($object_id == 0)
			return;
		
		$userId = JFactory::getUser()->id;
		$history = JTable::getInstance('History', 'PowerAdminTable');
		$history->load(array('user_id' => $userId, 'object_key' => $pageKey, 'object_id' => $object_id));
		
		if ($history->id == null) {
			$history->load(array('user_id' => $userId, 'object_id' => $object_id, 'params' => $link));
			if ($history->id == null) {
				$history->bind(array(
					'object_key'=> $pageKey,
					'user_id' => $userId,
					'object_id' => $object_id
				));
			}
		}

		$history->title = $title;
		$history->params = $link;
		$history->visited = time();
		$history->component = (empty($history->component) && !empty($post['parent'])) ? $post['parent'] : $history->component;
		$history->list_page = (empty($history->list_page) && !empty($post['name'])) ? $post['name'] : $history->list_page;
		$history->list_page_params = (empty($history->list_page_params) && !empty($post['params'])) ? $post['params'] : $history->list_page_params;
		$history->icon = (empty($history->icon) && !empty($post['iconPath'])) ? $post['iconPath'] : $history->icon;
		$history->css = (empty($history->css) && !empty($post['iconCss'])) ? $post['iconCss'] : $history->css;
		$history->description = $post['description'];

		$get = array();
		parse_str($history->params, $get);
		
		if ($get['option'] == 'com_templates') {
			if (!isset($get['task'])) {
				$history->params = "option=com_templates&task={$get['view']}.{$get['layout']}&id={$object_id}";
				unset($get['view']);
				unset($get['layout']);
			}
			
			$history->icon = 'templates/bluestork/images/menu/icon-16-themes.png';
			$history->css  = 'icon-16-themes';
			$history->component = 'Template Manager';
			$history->list_page = 'Template Manager';
		}
		
		$history->store();
		
		return $history->id;
	}
	
	/**
	 * Save history that associated with form data
	 * 
	 * @param JSession $session
	 * @param mixed $post
	 * @param string $pageKey
	 * @param string $title
	 * 
	 * @return int ID of saved history
	 */
	private function saveByPost($session, $post, $pageKey, $title)
	{
		if (!$session->has($post['postSessionKey']))
			return;
			
		$formData = $session->get($post['postSessionKey']);
		$formHash = md5($formData);
		$form = json_decode($formData);
		$id = $form->cid;
		
		if (is_array($id))
			$id = array_shift($id);
		
		$userId = JFactory::getUser()->id;
		
		$history = JTable::getInstance('History', 'PowerAdminTable');
		$history->load(array('user_id' => $userId, 'object_key' => $pageKey, 'object_id' => $id));
		
		if ($history->id == null) {
			$history->bind(array(
				'user_id' 	=> $userId,
				'object_key'=> $pageKey,
				'object_id' => $id
			));
		}
		
		$history->bind(array(
			'title' 	=> $title,
			'visited' 	=> time(),
			'form' 		=> $formData,
			'form_hash' => $formHash,
			'component' => (empty($history->component) && !empty($post['parent'])) ? $post['parent'] : $history->component,
			'list_page' => (empty($history->list_page) && !empty($post['name'])) ? $post['name'] : $history->list_page,
			'list_page_params' => (empty($history->list_page_params) && !empty($post['params'])) ? $post['params'] : $history->list_page_params,
			'icon' 		=> (empty($history->icon) && !empty($post['iconPath'])) ? $post['iconPath'] : $history->icon,
			'css' 		=> (empty($history->css) && !empty($post['iconCss'])) ? $post['iconCss'] : $history->css
		));
		
		$history->description = $post['description'];
		$history->store();
		
		return $history->id;
	}
}









