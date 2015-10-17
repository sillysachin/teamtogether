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
 * JSNCheckbox field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */
class JFormFieldJSNCheckbox extends JSNTPLFormField
{
	public $type = 'JSNCheckbox';

	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput ()
	{
		// Make sure we have options declared
		if ( ! isset($this->element->option))
		{
			return JText::_('JSN_TPLFW_ERROR_MISSING_OPTIONS');
		}

		// Get template data
		$data = JSNTplHelper::getEditingTemplate();

		// Initialize field value
		if (isset($data->params[(string) $this->element['name']]))
		{
			$this->value = $data->params[(string) $this->element['name']];
		}
		elseif (empty($this->value))
		{
			$this->value = (string) $this->element['default'];
		}

		if (is_string($this->value))
		{
			$this->value = (substr($this->value, 0, 1) == '{' AND substr($this->value, -1) == '}')
				? json_decode($this->value, true)
				: array($this->value => 1);
		}

		// Parse field attributes
		$options['class'] = isset($this->element['class']) ? (string) $this->element['class'] : '';
		$options['disabled'] = '';

		if (isset($this->element['disabled']) AND $this->element['disabled'] == 'true')
		{
			$options['class'] .= ' disabled';
			$options['disabled'] = ' disabled="disabled"';
		}

		// Get all checkbox options from xml
		$data = array();

		foreach ($this->element->children() AS $option)
		{
			// Check if option is checked
			if (is_array($this->value))
			{
				$checked = (array_key_exists((string) $option['value'], $this->value) AND $this->value[(string) $option['value']]);
			}
			else
			{
				$checked = (isset($option['default']) AND (string) $option['default'] == 'checked');
			}

			$data[] = array(
				'value' => (string) $option['value'],
				'text' => (string) $option,
				'checked' => $checked ? ' checked="checked"' : ''
			);
		}

		return JSNTplFormHelper::checkbox($this->name, $data, $options);
	}
}
