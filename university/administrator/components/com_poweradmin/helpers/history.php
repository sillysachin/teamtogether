<?php
/**
 * @version    $Id$
 * @package    JSNPoweradmin
 * @subpackage helpers
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file.
defined('_JEXEC') or die;

final class PowerAdminHistoryHelper
{
	/**
	 * Helper method to handle event onAfterInitialise
	 *
	 * @return void
	 */
	public static function onAfterInitialise()
	{
		$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
		if ($isAjax)
			return;

		// Handle global form post
		if (JRequest::getMethod() == 'POST')
			self::handlePostRequest();
		else
			self::handleGetRequest();
	}

	/**
	 * Save history for edit task that submitted by edit button
	 * in list page
	 *
	 * @return void
	 */
	private static function handlePostRequest()
	{
		$post = JRequest::get('post');

		if (!isset($post['task']))
			return;

		// TODO: handleing remove task to delete associated history
		if (preg_match('/^([a-zA-Z0-9]+)\.?(delete|remove|trash|publish)$/i', $post['task']) && (isset($post['cid']) || isset($post['id']))) {
			self::updateHistoryState($post);
			return;
		}

		// TODO: Handling save task to update item title after saved to database
		if (preg_match('/\.?(apply|save)$/i', $post['task']) && isset($post['jsn_history_id']) && isset($post['jsn_history_title'])) {
			self::updateHistoryTitle($post['jsn_history_id'], $post['jsn_history_title']);
			return;
		}

		// TODO: Save editing item to history
		if (!preg_match('/\.?edit/i', $post['task']) ||
			!isset($post['boxchecked']) || intval($post['boxchecked']) == 0 ||
			!isset($post['cid']) || empty($post['cid']))
			return;

		$cid = $post['cid'];
		if (is_array($cid))
			$cid = array_shift($cid);

		if (!is_numeric($cid))
			return;

		if (!isset($post['option']))
			$post['option'] = JRequest::getVar('option');

		$sessionKey = md5('post.' . time() . mt_rand(1, 1000));

		if (isset($post['view']))
			$formData['view'] = $post['view'];

		if (isset($post['layout']))
			$formData['layout'] = $post['layout'];

		if (isset($post['extension']))
			$formData['extension'] = $post['extension'];

		$session = JFactory::getSession();
		$session->set($sessionKey, json_encode($post));

		// Send session key to client by using cookie
		setcookie('jsn-poweradmin-post-session', $sessionKey);
	}

	/**
	 * Update title for history item
	 *
	 * @param int $id ID of history item
	 * @param string $title New title of history
	 */
	private static function updateHistoryTitle($id, $title)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_poweradmin/tables');

		$history = JTable::getInstance('History', 'PowerAdminTable');
		$history->load($id);
		$history->title = $title;
		$history->store();
	}

	/**
	 * Delete history that associated with deleting item
	 * @param $post
	 * @return void
	 */
	private static function updateHistoryState($post)
	{
		if (!isset($_COOKIE['jsn-poweradmin-list-page']))
			return;

		$listPage = json_decode($_COOKIE['jsn-poweradmin-list-page']);
		if ($listPage == NULL)
			$listPage = json_decode(stripslashes($_COOKIE['jsn-poweradmin-list-page']));

		$listPage->params = (isset($listPage->params)) ? str_replace('&amp;', '&', $listPage->params) : '';
		$id = array();

		if (isset($post['id']) && is_numeric($post['id']))
			$id[] = $post['id'];
		else if (isset($post['id']) && is_array($post['id']))
			$id = array_merge($id, $post['id']);

		if (isset($post['cid']) && is_numeric($post['cid']))
			$id[] = $post['cid'];
		else if (isset($post['cid']) && is_array($post['cid']))
			$id = array_merge($id, $post['cid']);

		$isDelete = (int)preg_match('/\.?(delete|remove|trash)$/i', $post['task']);
		
		if (count ($id) && (is_numeric($id) || is_array($id))) {
			// Bypass if any of id list is not a number
			if (is_array($id)) {
				foreach ($id as $i) {
					if (!is_numeric($i)) {
						return;
					}
				}
			}

			$dbo = JFactory::getDBO();
			$dbo->setQuery("UPDATE #__jsn_poweradmin_history SET is_deleted={$isDelete} WHERE list_page_params LIKE '{$listPage->params}' AND object_id IN (".implode(',', $id).")");
			@$dbo->query();
		}
	}

	/**
	 * Save history for edit task that user clicked directly to edit link
	 * in list page
	 *
	 * @return void
	 */
	private static function handleGetRequest()
	{
		$task = JRequest::getVar('task');
		$cid = JRequest::getVar('cid');
		$id = JRequest::getInt('id');

		if ($id == null && !empty($cid))
			$id = (is_array($cid)) ? array_shift($cid) : $cid;

		if (empty($task))
			return;

		$params = array(
			'queryString' => $_SERVER['QUERY_STRING'],
			'object_id'   => $id
		);

		$sessionKey = md5('get.' . time() . mt_rand(1, 1000));

		$session = JFactory::getSession();
		$session->set($sessionKey, json_encode($params));

		if (isset($_COOKIE['jsn-poweradmin-get-session']) && $session->has($_COOKIE['jsn-poweradmin-get-session']))
			$session->clear($_COOKIE['jsn-poweradmin-get-session']);

		setcookie('jsn-poweradmin-get-session', $sessionKey);
	}

	/**
	 * Handle onAfterRender to determine default view of current component
	 *
	 * @return void
	 */
	public static function onAfterRender()
	{
		$option = JRequest::getVar('option');
		$view = JRequest::getVar('view');
		$task = JRequest::getVar('task');
		$layout = JRequest::getVar('layout');

		if (!isset($_SERVER['HTTP_REFERER']))
			$_SERVER['HTTP_REFERER'] = '';

		// Find actual view of current request
		if (!empty($option)) {
			$includedFiles = get_included_files();
			$isMatchedView = false;
			$isMatchedLayout = false;

			foreach ($includedFiles as $file) {
				$file = str_replace('\\', '/', $file);
				if (!$isMatchedView && preg_match("/\/{$option}\/views\/([^\/]+)\/view\.html\.php$/i", $file, $matches)) {
					$view = $matches[1];
					$isMatchedView = true;
				}

				if ($isMatchedLayout && preg_match("/\/{$option}\/views\/([^\/]+)\/tmpl\/(.*?)\.php$/i", $file, $matches)) {
					$layout = $matches[2];
					$isMatchedLayout = true;
				}

				if ($isMatchedLayout && $isMatchedView)
					break;
			}
		}

		$params = array();
		$params['option'] = $option;

		if (!empty($view))
			$params['view'] = $view;
		if (!empty($layout))
			$params['layout'] = $layout;
		if (!empty($task))
			$params['task'] = $task;

		setcookie('jsn-poweradmin-page-key', http_build_query($params));
		setcookie('jsn-poweradmin-default-view', $view);
		setcookie('jsn-poweradmin-default-layout', $layout);
		setcookie('jsn-poweradmin-referer-page', $_SERVER['HTTP_REFERER']);
	}
}
