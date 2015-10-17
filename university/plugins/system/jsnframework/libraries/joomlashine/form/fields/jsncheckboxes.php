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
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Create checkboxes.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldJSNCheckboxes extends JSNFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'JSNCheckboxes';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var	boolean
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$labelClass = ' class="checkbox inline ' . (string) $this->element['class'] . '"';

		// Prepare field value
		if ( ! empty($this->value) AND is_string($this->value))
		{
			$this->value = json_decode($this->value, true);
		}

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
			$class = ' class="checkbox' . ( ! empty($option->class) ? " {$option->class}" : '') . '"';
			$disabled = ! empty($option->disable) ? ' disabled="disabled"' : '';

			// Initialize some JavaScript option attributes.
			$onclick = ! empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

			$html[] = '<label for="' . $this->id . $i . '"' . $labelClass . '> <input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $onclick . $disabled . '/>' . JText::_($option->text) . '</label>';
		}

		return implode($html);
	}
}
