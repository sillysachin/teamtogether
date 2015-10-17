<?php
/**
 * @version		2.0 beta
 * @package		zKunenaLatest
 * @author		Aaron Gilbert {@link http://www.nzambi.braineater.ca}
 * @author		Created on Feb-14 2012
 * @copyright	Copyright (C) 2009 - 2012 Aaron Gilbert. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
//-- No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
// CHECK if Kunena NOT installed or Kunena version is not supported 
if (! (class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') ) ) {
	echo JText::_ ( 'MOD_ZKUENALATEST_NOKUNENA' );
	return;
} 

if (!KunenaForum::enabled()) {// Kunena online check
	if($params->get('showOffline', 1 )) {
		echo JText::_ ( 'MOD_ZKUENALATEST_OFFLINE' );
	}
	return;
}

	
require_once(dirname(__FILE__).DS.'helper.php');
require_once KPATH_SITE . '/lib/kunena.link.class.php';

//Initialize  the Kunena Framework
KunenaForum::setup();

KunenaFactory::loadLanguage();
KunenaFactory::loadLanguage('com_kunena.templates');

//$KunenaConfig 	= KunenaFactory::getConfig ();
$document 		=& JFactory::getDocument();
$layout 		= 'default';

if (!$items = modZKunenaLatestHelper::getItems($params) ) {
	return NULL;
}


if($params->get('MooFX', 1 )) {
	$document->addScriptDeclaration( modZKunenaLatestHelper::getScript($params, $module)  );
}
$document->addStyleDeclaration( modZKunenaLatestHelper::getExtraCss($params, $module->id ) );

require(JModuleHelper::getLayoutPath('mod_zkunenalatest', $layout));
?>
