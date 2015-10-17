<?php
/**
 * @version   1.1
 * @author    emkt.mx Fernando Martínez
 * @copyright Copyright (C) 2009 - 2014 emkt.mx
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted index access');

class plgSystemWebsitePreloader extends JPlugin {
    public function onAfterInitialise() {
    	$app =& JFactory::getApplication();
    	$document =& JFactory::getDocument();
    	$scriptUrl = 'plugins/system/websitePreloader/assets/js/preloader.min.js';
		$themeUrl  = 'plugins/system/websitePreloader/assets/themes/'.$this->params->get('theme','').'.css';
		
		if ($app->isAdmin()) {
		}
		else{
			$document->addStyleSheet($themeUrl);
			$document->addScript($scriptUrl);
		}		
    }
}