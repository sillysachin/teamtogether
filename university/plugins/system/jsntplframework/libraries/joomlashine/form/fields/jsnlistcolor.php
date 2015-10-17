<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JSNColorList field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       1.0.0
 */
class JFormFieldJSNListColor extends JSNTplFormField
{
	public $type = 'JSNListColor';

	/**
	 * Disable label by default.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Generate HTML code for input field.
	 *
	 * @return  string
	 */
	public function getInput ()
	{
		// Get template data
		$data = JSNTplHelper::getEditingTemplate();

		// Preset values
		$data->_JSNListColor = new stdClass;

		$defaultValues = array(
			'list'   => array(),
			'colors' => array(),
		);

		foreach ($this->element->option AS $option)
		{
			$value = (string) $option['value'];

			$data->_JSNListColor->default[$value] = array(
				'label' => (string) $option,
				'value' => $value,
			);

			$defaultValues['list'][]   = $value;
			$defaultValues['colors'][] = $value;
		}

		$data->_JSNListColor->option['list']    = array_keys($data->_JSNListColor->default);
		$data->_JSNListColor->option['checked'] = $data->_JSNListColor->option['list'];

		// Generate param name
		$paramName = (string) $this->element['name'];

		if (isset($data->params[$paramName]))
		{
			$decodedValue = json_decode($data->params[$paramName]);

			if ($decodedValue AND is_array($decodedValue->list))
			{
				$optionList = array();

				foreach ($decodedValue->list AS $item)
				{
					if (isset($data->_JSNListColor->default[$item]))
					{
						$optionList[] = $item;
					}
				}

				$arrayDiff                           = array_diff($data->_JSNListColor->option['list'], $optionList);
				$data->_JSNListColor->option['list'] = array_merge($optionList, $arrayDiff);
			}

			if (@is_array($decodedValue->colors))
			{
				if (count($arrayDiff))
				{
					$data->_JSNListColor->option['checked'] = array_merge($optionList, array_diff($data->_JSNListColor->option['checked'], $decodedValue->colors));
				}
				else
				{
					$data->_JSNListColor->option['checked'] = $decodedValue->colors;
				}
			}
		}
		else
		{
			$data->params[$paramName] = array(
				'list'   => $data->_JSNListColor->option['list'],
				'colors' => $data->_JSNListColor->option['checked']
			);
		}

		// Prepare other field attributes
		$this->disabled = ( 'true' == (string) $this->element['disabled'] );

		return parent::getInput();
	}
}
