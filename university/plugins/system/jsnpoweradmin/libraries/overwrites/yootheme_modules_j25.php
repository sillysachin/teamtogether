<?php

/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: yootheme_modules_j25.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * JSN Poweradmin created this file and overwrite modules.php of yootheme framework
*/
// load modules
$modules = $this['modules']->load($position);
$count   = count($modules);
$output  = array();

$poweradmin         = JRequest::getCmd('poweradmin', 0);
$vsm_changeposition = JRequest::getCmd('vsm_changeposition', 0);

foreach ($modules as $index => $module) {

	// set module params
	$params           = array();
	$params['count']  = $count;
	$params['order']  = $index + 1;
	$params['first']  = $params['order'] == 1;
	$params['last']   = $params['order'] == $count;
	$params['suffix'] = $module->parameter->get('moduleclass_sfx', '');

	// pass through menu params
	if (isset($menu)) {
		$params['menu'] = $menu;
	}

	// get class suffix params
	$parts = preg_split('/[\s]+/', $params['suffix']);

	foreach ($parts as $part) {
		if (strpos($part, '-') !== false) {
			list($name, $value) = explode('-', $part, 2);
			$params[$name] = $value;
		}
	}

	// remove used parameters from suffix so we dont end up having the wrong css-classes
	$params['suffix'] = preg_replace("/style-[a-z0-9]+ /", "", $params['suffix']);
	$params['suffix'] = preg_replace("/color-[a-z0-9]+ /", "", $params['suffix']);
	$params['suffix'] = preg_replace("/badge-[a-z0-9]+ /", "", $params['suffix']);
	$params['suffix'] = preg_replace("/icon-[a-z0-9]+ /", "", $params['suffix']);
	$params['suffix'] = preg_replace("/header-[a-z0-9]+ /", "", $params['suffix']);

	// render module
	if ( $poweradmin == 1 ){
		$module_html = '<div class="poweradmin-module-item" id="'.$module->id.'-jsnposition" title="'.$module->title.'" showtitle="'.$module->showtitle.'"><div id="moduleid-'.$module->id.'-content">'.$this->render('module', compact('module', 'params')).'</div></div>';
		$output[] = $module_html;
	}else{
		$output[] = $this->render('module', compact('module', 'params'));
	}	
}

if ( $poweradmin == 1 ){
	if ($count > 0){
		$block_start = '<div class="jsn-element-container_inner"><div class="jsn-poweradmin-position clearafter" id="'.$position.'-jsnposition">';
		$block_end   = '</div></div>';
	}else{
		$block_start = $block_end = '';
	}
	
	// render module layout
	if ( $vsm_changeposition == 1 ){
		$block_layout = '<p>'.$position.'</p>';
	}else{
		$block_layout = (isset($layout) && $layout) ? $this->render("modules/layouts/{$layout}", array('modules' => $output)): implode("\n", $output);		
	}
	
	echo $block_start.$block_layout.$block_end;
}else{
	// render module layout
	echo (isset($layout) && $layout) ? $this->render("modules/layouts/{$layout}", array('modules' => $output)): implode("\n", $output);
}