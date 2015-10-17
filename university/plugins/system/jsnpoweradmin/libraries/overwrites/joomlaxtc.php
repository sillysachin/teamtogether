<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: joomlaxtc.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// Call XTC framework
require JPATH_THEMES.'/'.$this->template.'/XTC/XTC.php';

// Load template parameters
$templateParameters = xtcLoadParams();

// Get the selected layout
$layout = $templateParameters->templateLayout;

// Call layout from layouts folder to create HTML
if ( !class_exists('JSNJoomlaXTCHelper') ){
	require JPATH_THEMES.'/'.$this->template.'/layouts/'.$layout.'/layout.php';
}else{
	/**
	 * Get content to variable and make new format
	 */
	ob_start();
	require JPATH_THEMES.'/'.$this->template.'/layouts/'.$layout.'/layout.php';
	$document = ob_get_contents();
	ob_end_clean();
	
	$JSNJoomlaXTCHelper = JSNJoomlaXTCHelper::getInstance( $this, $document );
	$JSNJoomlaXTCHelper->render();
}