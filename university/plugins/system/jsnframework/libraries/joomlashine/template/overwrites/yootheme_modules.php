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

defined('_JEXEC') or die('Restricted access');

/**
 * JSN Poweradmin created this file and overwrite modules.php of yootheme framework
 **/

// Load modules
$modules	= $this['modules']->load($position);
$count		= count($modules);
$output		= array();

// Get input object
$input = JFactory::getApplication()->input;

$poweradmin			= $input->getCmd('poweradmin', 0);
$vsm_changeposition	= $input->getCmd('vsm_changeposition', 0);

foreach ($modules AS $index => $module)
{
	// Set module params
	$params				= array();
	$params['count']	= $count;
	$params['order']	= $index + 1;
	$params['first']	= $params['order'] == 1;
	$params['last']		= $params['order'] == $count;
	$params['suffix']	= $module->parameter->get('moduleclass_sfx', '');

	// Pass through menu params
	if (isset($menu))
	{
		$params['menu'] = $menu;
	}

	// Get class suffix params
	$parts = preg_split('/[\s]+/', $params['suffix']);

	foreach ($parts AS $part)
	{
		if (strpos($part, '-') !== false)
		{
			list($name, $value) = explode('-', $part, 2);
			$params[$name] = $value;
		}
	}

	// Render module
	if ($poweradmin == 1)
	{
		$module_html = '<div class="poweradmin-module-item" id="' . $module->id . '-jsnposition" title="' . $module->title . '" showtitle="' . $module->showtitle . '"><div id="moduleid-' . $module->id . '-content">' . $this->render('module', compact('module', 'params')) . '</div></div>';
		$output[] = $module_html;
	}
	else
	{
		$output[] = $this->render('module', compact('module', 'params'));
	}
}

if ($poweradmin == 1)
{
	if ($count > 0)
	{
		$block_start	= '<div class="jsn-element-container_inner"><div class="jsn-poweradmin-position clearafter" id="' . $position . '-jsnposition">';
		$block_end		= '</div></div>';
	}
	else
	{
		$block_start = $block_end = '';
	}

	// Render module layout
	if ($vsm_changeposition == 1)
	{
		$block_layout = '<p>' . $position . '</p>';
	}
	else
	{
		$block_layout = (isset($layout) && $layout) ? $this->render("modules/layouts/{$layout}", array('modules' => $output)): implode("\n", $output);
	}

	echo $block_start . $block_layout . $block_end;
}
else
{
	// Render module layout
	echo (isset($layout) && $layout) ? $this->render("modules/layouts/{$layout}", array('modules' => $output)): implode("\n", $output);
}
