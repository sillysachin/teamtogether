<?php
/**
 * @version     $Id: view.html.php 16024 2012-09-13 11:55:37Z hiepnv $
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

include_once (JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/positions/view.php');

class PoweradminViewChangeposition extends JSNPositionsView
{
	public function display($tpl = null)
	{
 		$app = JFactory::getApplication();
 		$document = JFactory::getDocument();
 		// Check if this view is used for module editing page.
 		$moduleEdit = JRequest::getCmd('moduleedit', '');
 		$active_positions = Array();
 		$model = $this->getModel('changeposition');
 		if(!$moduleEdit){
 			$moduleid = $app->getUserState( 'com_poweradmin.changeposition.moduleid' );

 		}else{
 			$moduleid = array(JRequest::getCmd('moduleid', ''));
 		}

 		for( $i = 0; $i < count($moduleid); $i++ ){
 			$active_positions[] = "$('#".$model->getModulePosition(  $moduleid[$i] )."-jsnposition').addClass('active-position').attr('title', 'Active position');";
 		}

 		JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.js');
 		JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.jquery.noconflict.js');
 		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.functions.js');
 		//$document->addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.filter.visualmode.js');


 		//Enable position filter.
 		$this->setFilterable(true);

		$customScript = "
			var baseUrl  = '".JURI::root()."';
			var moduleid = new Array();
			moduleid = [". @implode(",", $moduleid)."];
			(function ($){
				$(document).ready(function (){
					".implode(PHP_EOL, $active_positions)."
				});
			})(JoomlaShine.jQuery);
 		";

 		$this->addCustomScripts($customScript);


 		//Callback after position clicked.
 		if(!$moduleEdit){
 			$onPostionClick = "
 			if ( !$(this).hasClass('active-position') ){
				JoomlaShine.jQuery.setPosition(moduleid, $(this).attr('id').replace('-jsnposition', ''));
 				parent.JoomlaShine.jQuery('.ui-dialog-content').dialog('close');
 			}
 			";
 		}else{
 			$onPostionClick = "
 			if ( !$(this).hasClass('active-position') ){
 				var posName = $(this).attr('id').replace('-jsnposition', '');
 				parent.JoomlaShine.jQuery('#jform_position').val(posName);
 				parent.modal.close();
 			}
 			";
 		}

 		$this->addPositionClickCallBack($onPostionClick);

		parent::display($tpl);
	}
}
