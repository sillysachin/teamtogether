<?php
/**
 * @version    $Id$
 * @package    JSNPoweradmin
 * @subpackage Item
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

jimport('joomla.application.component.view');

class PoweradminViewTemplates extends JSNBaseView
{

	public function display($tpl = null)
	{
		$JSNMedia = JSNFactory::getMedia();
		$JSNMedia->addStyleSheet(JSN_POWERADMIN_STYLE_URI. 'styles.css');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI."jsn.mousecheck.js");
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI."jsn.submenu.js");
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI."jsn.manage-styles.js");

		$JSNMedia->addStyleDeclaration("
		.template-item {
			background: url(".JSN_POWERADMIN_IMAGES_URI."icons-24/icon-24-loading-circle.gif) no-repeat center center;
		}
		.loading {
			background: url(".JSN_POWERADMIN_IMAGES_URI."icons-16/icon-16-loading-circle.gif) no-repeat center right;
		}
		");

		$JSNMedia->addScriptDeclaration("
            (function ($){
               $(document).ready(function (){
                    $('#client-switch').change(function (e) {
                        var val =$(this).attr('value');
                        if(val == 0){
                            $('.template-list').hide();
                            $('#site').show();
                        }else{
                            $('.template-list').hide();
                            $('#admin').show();
                        }
                    })
	           });
            })(JoomlaShine.jQuery);
        ");
		// Add javascript lang translation
		$jsnLang = new JSNJavascriptLanguages;
		$jsnLang->addLang('JSN_POWERADMIN_TM_MAKE_DEFAULT');
		$jsnLang->addLang('JSN_POWERADMIN_TM_UNINSTALL_TEMPLATE');
		$jsnLang->addLang('JSN_POWERADMIN_TM_MAKE_DEFAULT');
		$jsnLang->addLang('JSN_POWERADMIN_TM_UNINSTALL_TEMPLATE');
		$jsnLang->addLang('JSN_POWERADMIN_TM_CLOSE_BEFORE_DELETE');
		$jsnLang->addLang('JSN_POWERADMIN_TM_CLOSE_BEFORE_UNINSTALL');
		$jsnLang->addLang('JSN_POWERADMIN_TM_ALREADY_DEFAULT');
		$jsnLang->addLang('JSN_POWERADMIN_TM_CANNOT_DELETE_DEFAULT');
		$jsnLang->addLang('JSN_POWERADMIN_TM_CANNOT_UNINSTALL_DEFAULT');

		$jsnLang->addLang('JSN_POWERADMIN_TM_DELETE_STYLE_CONFIRM');
		$jsnLang->addLang('JSN_POWERADMIN_TM_UNINSTALL_TEMPLATE_CONFIRM');
		$JSNMedia->addScriptDeclaration($jsnLang->loadLang());

		$model = $this->getModel('templates');
		$rows  = $model->getTemplates();
		$adminRows  = $model->getTemplates(1);

		// Check permission for removing styles.
		JSNFactory::import('components.com_templates.helpers.templates');
		$canDo 		= version_compare( JVERSION, '3.2.2', 'ge' ) ? JHelperContent::getActions('com_templates') : TemplatesHelper::getActions();
		$canDelete 	= $canDo->get('core.delete');
		$canDelete 	= '<input type="hidden" id="candelete" value="'.$canDelete.'"></input>';
		echo $canDelete;

		// Check permission for uninstalling template.
		JSNFactory::import('components.com_installer.helpers.installer');
		$canDo	= version_compare( JVERSION, '3.2.2', 'ge' ) ? JHelperContent::getActions('com_installer') : TemplatesHelper::getActions();
		$canUninstall = $canDo->get('core.delete');
		$canUninstall = '<input type="hidden" id="canuninstall" value="'.$canUninstall.'"></input>';
		echo $canUninstall;
		//assign to view
		$this->assign('templates', $rows);
		$this->assign('adminTemplates', $adminRows);
		$this->assign('canDelete', $canDelete);
		return parent::display();
	}

}