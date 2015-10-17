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
 * Base class for field renderer.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNFormField extends JFormField
{
	protected $type = '';

	/**
	 * Get the field label markup.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		// Preset label
		$label = '';

		if ($this->hidden OR (int) $this->element['hide-label'])
		{
			return $label;
		}

		// Get the label text from the XML element, defaulting to the element name
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label
		$class = array ('control-label');
		$class[] = $this->required == true ? ' required' : '';
		$class[] = ! empty($this->labelClass) ? ' ' . $this->labelClass : '';
		$class = implode('', $class);

		// Add the opening label tag and class attribute
		$label .= '<label class="' . $class . '"';

		// Create tooltip for description
		if ( ! empty($this->description))
		{
			$label .= ' original-title="' . htmlspecialchars($this->translateDescription ? JText::_($this->description) : $this->description, ENT_COMPAT, 'UTF-8') . '"';
		}

		// Add the label text and closing tag
		$label .= '>' . $text . ($this->required ? ' <span class="star">&#160;*</span>' : '');

		// Finalize label
		$label .= '</label>';

		return $label;
	}

	/**
	 * Get the field input markup.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text', ((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
