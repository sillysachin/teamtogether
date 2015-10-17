<?php
/**
 * @package                Joomla.Site
 * @copyright        Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @param        array
 * @return        array
 */
function UniformBuildRoute(&$query)
{
	$segments = array();

	// get a menu item based on Itemid or currently active
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$params = JComponentHelper::getParams('com_uniform');
	$advanced = $params->get('sef_advanced_link', 0);
	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
	}

	$mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mId = (empty($menuItem->query['submission_id'])) ? null : $menuItem->query['submission_id'];
	//var_dump($query);
	if (isset($query['view']))
	{

		$view = $query['view'];
		if (empty($query['Itemid']))
		{
			$segments[] = $query['view'];
		}
		unset($query['view']);

	}

	// are we dealing with a contact that is attached to a menu item?
	if (isset($view) && ($mView == $view) and (isset($query['id'])) and ($mId == intval($query['submission_id'])))
	{
		unset($query['view']);
		unset($query['submission_id']);
		return $segments;
	}

	if (isset($view) and ($view == 'submission'))
	{

		if ($mId != intval($query['submission_id']) || $mView != $view)
		{
			if ($advanced)
			{
				list($tmp, $id) = explode(':', $query['submission_id'], 2);
			}
			else
			{
				$id = $query['submission_id'];
			}
			$segments[] = 'submission';
			$segments[] = $id;
		}
		unset($query['submission_id']);
	}
	/*
			if (isset($view) and ($view == 'form'))
			{
					if ($mId != intval($query['form_id']) || $mView != $view)
					{
							if ($advanced)
							{
									list($tmp, $id) = explode(':', $query['form_id'], 2);
							}
							else
							{
									$id = $query['form_id'];
							}

							$segments[] = 'form';
							$segments[] = $id;

							if (!empty($query['task']) && $query['task'] == "generateStylePages")
							{
									$segments[] = "form.css";
							}
							else
							{
									$segments[] = $query['task'];
							}
					}
					unset($query['form_id']);
					unset($query['task']);
					var_dump($query);

			}
	*/
	return $segments;
}

/**
 * @param        array
 * @return        array
 */
function UniformParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();
	$params = JComponentHelper::getParams('com_uniform');
	$advanced = $params->get('sef_advanced_link', 0);
	$count = count($segments);

	// Standard routing for uniform.

	if (isset($segments[0]) && $segments[0] == "submission")
	{
		$vars['submission_id'] = isset($segments[$count - 1]) ? $segments[$count - 1] : 0;
		$vars['view'] = 'submission';
	}

	return $vars;
}
