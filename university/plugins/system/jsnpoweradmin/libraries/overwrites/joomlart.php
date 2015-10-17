<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: joomlart.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if (class_exists('JSNT3Template')){
	$tmpl = new JSNT3Template( $this );
	$tmpl->disableInfoMode();
	$tmpl->render();
}else if(class_exists('T3Template')){
	if (JVERSION < '1.7'){
		$tmpl = T3Template::getInstance();
		$tmpl->setTemplate($this);
		$tmpl->render();
		return;
	}else{
		$tmpl = T3Template::getInstance($this);
		$tmpl->render();
		return;
	}	
}else{
	//Need to install or enable JAT3 Plugin
	echo JText::_('MISSING_JAT3_FRAMEWORK_PLUGIN');
}