<?php

/**
 * @version     $Id: jsnlistform.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Elements
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . '/administrator/components/com_uniform/uniform.defines.php';
$lang = JFactory::getLanguage();
$lang->load('com_uniform');

/**
 * Abstract Form Field class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldJsnfield extends JFormField
{

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$columnTableFormData = JSNUniformHelper::getFormData();
		$arrayTranslated = array();
		$html = "<div class=\"jsn-master\"><div id=\"page-loading\" class=\"jsn-bgloading\"><i class=\"jsn-icon32 jsn-icon-loading\"></i></div><div class=\"jsn-bootstrap menu-items\"><input type='hidden' id='uniform_field' name='".$this->name."' value='".$this->value."' /><ul class=\"jsn-items-list ui-sortable hide\" id=\"form_field\">";
		$html .= "</ul></div></div>";
		JSNHtmlAsset::loadScript('uniform/menusubmissions', array('value'=>$this->value,'name'=>'uniform_listField','columnTableFormData'=>$columnTableFormData,'language' => JSNUtilsLanguage::getTranslated($arrayTranslated)));
		return $html;
	}

}
