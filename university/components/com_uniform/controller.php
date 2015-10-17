<?php

/**
 * @version     $Id: controller.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Uniform master display controller.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 *
 */
class JSNUniformController extends JSNBaseController
{

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Set default view
		$input->set('view', $input->get('view', 'form'));

		parent::display();

		return $this;
	}

	/**
	 * Generate Style Pages
	 *
	 * @return string
	 */
	public function generateStylePages()
	{
		$input = JFactory::getApplication()->input;
		$formId = $input->getInt('form_id');
		if (!empty($formId))
		{
			$db = JFactory::getDBO();
			$db->setQuery($db->getQuery(true)->from('#__jsn_uniform_forms')->select('*')->where('form_id=' . (int) $formId));
			$items = $db->loadObject();
			$customCss = "";
			$globalFormStyle = JSNUniformHelper::getDataConfig("form_style");
			$formStyleCustom = new stdClass;
			if (!empty($items->form_style))
			{
				$formStyleCustom = json_decode($items->form_style);
				$customCss = !empty($formStyleCustom->custom_css) ? $formStyleCustom->custom_css : "";
				if (!empty($globalFormStyle))
				{
					$globalFormStyle = json_decode($globalFormStyle->value);

					if (!empty($globalFormStyle->themes_style))
					{
						foreach ($globalFormStyle->themes_style as $key => $value)
						{
							$formStyleCustom->themes_style->{$key} = $value;
						}
					}
					if (!empty($globalFormStyle->themes))
					{
						foreach ($globalFormStyle->themes as $key => $value)
						{
							$formStyleCustom->themes[] = $value;
						}
					}
				}
			}
			if (!empty($formStyleCustom->theme) && !empty($formStyleCustom->themes_style) && $formStyleCustom->theme != "jsn-style-light" && $formStyleCustom->theme != "jsn-style-dark")
			{
				$theme = str_replace("jsn-style-", "", $formStyleCustom->theme);
				if (!empty($formStyleCustom->themes_style->{$theme}))
				{
					$formStyleCustom = json_decode($formStyleCustom->themes_style->{$theme});
				}
			}
			header("Content-Type: text/css;X-Content-Type-Options: nosniff;");
			echo JSNUniformHelper::generateStylePages($formStyleCustom,
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group",
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group.ui-state-highlight",
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group .control-label",
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group.error .help-block,\n" .
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group.error .help-inline,\n" .
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group.error .help-block span.label",
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group .label-important,\n" .
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group .label-important .badge-important",
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group .controls input,\n" .
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group .controls select,\n" .
				"#jsn_form_{$formId}.jsn-master .jsn-bootstrap  .jsn-form-content .control-group .controls textarea"
			);
			echo "\n{$customCss}\n";
		}
		exit();
	}
}
