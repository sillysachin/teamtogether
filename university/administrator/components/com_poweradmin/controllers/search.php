<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: search.php 16454 2012-09-26 09:13:12Z hiepnv $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
error_reporting(0);
class PoweradminControllerSearch extends JControllerForm
{
	function query () {
		$app = JFactory::getApplication();
		$app->setUserState('search.keyword', '');
		$app->setUserState('search.coverage', '');

		$this->setRedirect('index.php?option=com_poweradmin&view=search');
	}

	/**
	 * Populate search and response results to client as json format
	 * @return void
	 */
	function json ()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		$keyword = JRequest::getString('keyword');
		$coverages = array_map('trim', explode(',', JRequest::getString('coverages')));
		$dispatcher	= &JDispatcher::getInstance();
		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers/poweradmin.php';
		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers/plugin.php';

		$params = JSNConfigHelper::get('com_poweradmin');
		$resultLimit = (int)$params->get('search_result_num', 10);

		//JRequest::setVar('limit', $resultLimit);

		// Load language
		JFactory::getLanguage()->load('plg_system_jsnpoweradmin');

		// Load model
		$model = JSNBaseModel::getInstance('Search', 'PowerAdminModel');
		$model->setState('search.keyword', $keyword);
		$model->setState('search.pagination', false);

		$installedComponents = PoweradminHelper::getInstalledComponents();

		$results = array();
		foreach ($coverages as $coverage) {
			if ($coverage == 'adminmenus') {
				continue;
			}

			$components = array();
			if ($coverage == 'components') {
				if (in_array('com_banners', $installedComponents)) $components['com_banners'] = array('com_banners', 'com_banners_categories', 'com_banners_clients');
				if (in_array('com_contacts', $installedComponents)) $components['com_contacts'] = array('com_contacts', 'com_contacts_categories');
				if (in_array('com_messages', $installedComponents)) $components['com_messages'] = array('com_messages');
				if (in_array('com_newsfeeds', $installedComponents)) $components['com_newsfeeds'] = array('com_newsfeeds', 'com_newsfeeds_categories');
				if (in_array('com_weblinks', $installedComponents)) $components['com_weblinks'] = array('com_weblinks', 'com_weblinks_categories');
			}

			if (strpos($coverage, JSN_3RD_EXTENSION_STRING) !== false)
			{

				$_extName	= explode(JSN_3RD_EXTENSION_STRING . "-", $coverage);
				$_extName	= $_extName[1];
				if ($_extName && in_array('com_' . $_extName, $installedComponents))
				{
					JPluginHelper::importPlugin('jsnpoweradmin', $_extName);
					$_range	=	$dispatcher->trigger('addSearchRange');

					if (count($_range[0]))
					{
						$components['com_' . strtolower($_extName)] = array();
						foreach ($_range[0] as $key=>$value)
						{
							array_push($components['com_' . strtolower($_extName)], $key);
						}
					}
				}else{
					continue;
				}
			}

			if (strpos($coverage, JSN_3RD_EXTENSION_NOT_INSTALLED_STRING) !== false)
			{
				$results[] = array('type'=>'notice', 'target'=> 'index.php?option=com_poweradmin&view=configuration&s=maintainence&g=extensions', 'title' => JText::_('JSN_POWERADMIN_EXT_SPOTLIGHT_INSTALL_EXTENSION'). ' ' . str_ireplace(JSN_3RD_EXTENSION_NOT_INSTALLED_STRING . '-', 'com_', $coverage));
				continue;
			}

			if (strpos($coverage, JSN_3RD_EXTENSION_NOT_ENABLED_STRING) !== false)
			{
				$results[] = array('type'=>'notice', 'link'=> 'index.php?option=com_poweradmin&view=configuration', 'title' => JText::_('JSN_POWERADMIN_EXT_SPOTLIGHT_ENABLE_EXTENSION') . ' ' . str_ireplace(JSN_3RD_EXTENSION_NOT_ENABLED_STRING . '-', 'com_', $coverage));
				continue;
			}


			if (!empty($components)) {
				foreach ($components as $key => $component) {
					$componentResults = array();
					$type = $component;

					if (is_array($component)) {
						$type = $key;

						foreach ($component as $subsection) {
							$model->setState('search.coverage', $subsection);
							$componentResults = array_merge($componentResults, $model->getItems());
						}
					}
					else {
						$model->setState('search.coverage', $component);
						$componentResults = array_merge($componentResults, $model->getItems());
					}

					if (count($componentResults) > 0) {
						$results[] = array('title' => JText::_('PLG_JSNADMINBAR_SEARCH_COVERAGE_'.strtoupper($type),true), 'description' => '', 'type' => $type, 'coverage' => $coverage);
						$results = array_merge($results, $componentResults);
					}
				}

				continue;
			}

			$model->setState('search.coverage', $coverage);


			$total = $model->getTotal();
			$items = $model->getItems();

			if (count($items) > 0) {
				$results[] = array(
					'title' => JText::_('PLG_JSNADMINBAR_SEARCH_COVERAGE_'.strtoupper($coverage)),
					'description' => '',
					'type' => $coverage,
					'coverage' => $coverage,
					'hasMore' => $total - $resultLimit
				);

				$results = array_merge($results, $items);
			}
		}

		echo json_encode($results);
		jexit();
	}
}
?>