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
class JFormFieldJSNColorList extends JSNTplFormField
{
	public $type = 'JSNColorList';

	protected $defaultOptions = array();
	protected $optionKeys = array();
	protected $optionColors = array();

	public function getInput ()
	{
		// Get template data
		$data = JSNTplHelper::getEditingTemplate();

		// Preset values
		$data->_JSNListColor = new stdClass;

		$defaultValues = array(
			'list' => array(),
			'colors' => array()
		);

		foreach ($this->element->option AS $option)
		{
			$value = (string) $option['value'];

			$data->_JSNColorList->default[$value] = array(
				'label' => (string) $option,
				'value' => $value
			);

			$defaultValues['list'][]	= $value;
			$defaultValues['colors'][]	= $value;
		}

		$data->_JSNColorList->option['list'] = array_keys($data->_JSNColorList->default);
		$data->_JSNListColor->option['checked'] = $data->_JSNColorList->option['list'];

		if ( ! empty($this->value))
		{
			$decodedValue = json_decode($this->value);

			if (is_array($decodedValue->list))
			{
				$optionList = array();

				foreach ($decodedValue->list AS $item)
				{
					if (isset($data->_JSNColorList->default[$item]))
					{
						$optionList[] = $item;
					}
					elseif (@isset($decodedValue->colors[$item]))
					{
						unset($decodedValue->colors[$item]);
					}
				}

				$arrayDiff = array_diff($data->_JSNColorList->option['list'], $optionList);
				$data->_JSNColorList->option['list'] = array_merge($optionList, $arrayDiff);
			}

			if (is_array($decodedValue->colors) AND ! empty($decodedValue->colors))
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
			$this->value = array(
				'list' => $data->_JSNColorList->option['list'],
				'colors' => $data->_JSNListColor->option['checked']
			);
		}

		$this->disabled = isset($this->element['disabled']) && $this->element['disabled'] == 'true';
		$this->disabledClass = $this->disabled ? 'disabled' : '';

		return parent::getInput();
	}
}
