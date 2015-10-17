<?php

/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('JPATH_BASE') or die;

/**
 * Supports an HTML select list of form
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldJSNSelectbox extends JSNFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'JSNSelectBox';

	/**
	 * Get the select box field input markup.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Get radio button options
		$options = $this->getOptions();
		$class   = empty($this->element['class']) ? "inputbox jsn-select-value" : $this->element['class'];
		$html    = JHTML::_('select.genericList', $options, $this->name, 'class="' . $class . '"', 'value', 'text', $this->value);

		return $html;
	}

	/**
	 * Get the field options for screen list.
	 *
	 * @return  array
	 */
	protected function getOptions()
	{
		// Preset options array
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element
			$tmp = JHtml::_('select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text');

			// Add the option object to the options array
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}

}
