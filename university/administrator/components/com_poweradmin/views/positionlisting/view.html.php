<?php
/**
 * @version     $Id: view.html.php 16019 2012-09-13 09:36:57Z hiepnv $
 * @package     JSN_Poweradmin
 * @subpackage  Config
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class PoweradminViewPositionlisting extends JSNPositionsView
{
	public function display($tpl = null)
	{
 		$app = JFactory::getApplication();
 		$document = JFactory::getDocument();
 		$positionName	= JRequest::getVar('positionname', '');

 		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.js');
 		JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.jquery.noconflict.js');
 		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.functions.js');
 		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.filter.visualmode.js');

 		//Enable position filter.
 		$this->setFilterable(true);

		$customScript = "
			var baseUrl  = '".JURI::root()."';
			(function ($){
				$(document).ready(function (){
					$('#".$positionName."-jsnposition').addClass('active-position').attr('title', 'Active position');
				});
			})(JoomlaShine.jQuery);
 		";

 		$this->addCustomScripts($customScript);

		parent::display($tpl);
	}
}
