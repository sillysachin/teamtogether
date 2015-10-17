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
 * Create radio buttons.
 *
 * Below is a sample field declaration for generating radio input field:
 *
 * <code>&lt;field
 *     name="disable_all_messages" type="jsnradio" default="0" filter="int"
 *     label="JSN_SAMPLE_DISABLE_ALL_MESSAGES_LABEL" description="JSN_SAMPLE_DISABLE_ALL_MESSAGES_DESC"
 * &gt;
 *     &lt;option value="0"&gt;JNO&lt;/option&gt;
 *     &lt;option value="1"&gt;JYES&lt;/option&gt;
 * &lt;/field&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 *
 */
class JFormFieldJSNRadio extends JSNFormField
{
		/**
		 * The form field type.
		 *
		 * @var    string
		 */
		protected $type = 'JSNRadio';

		/**
		 * Get the field label markup.
		 *
		 * @return  string
		 */
		protected function getLabel()
		{
				// Preset label
				$label = '';

				if ($this->hidden)
				{
						return $label;
				}
				if (empty($this->element['label']))
				{
						return;
				}
				// Get the label text from the XML element, defaulting to the element name
				$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
				$text = $this->translateLabel ? JText::_($text) : $text;

				// Build the class for the label
				$class = array('control-label');
				$class[] = !empty($this->description) ? ' hasTip' : '';
				$class[] = $this->required == true ? ' required' : '';
				$class[] = !empty($this->labelClass) ? ' ' . $this->labelClass : '';
				$class = implode('', $class);

				// Add the opening label tag and class attribute
				$label .= '<label class="' . $class . '"';

				// If a description is specified, use it to build a tooltip
				if (!empty($this->description))
				{
						$label .= ' title="' . htmlspecialchars(($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
				}

				// Add the label text and closing tag
				$label .= '>' . $text . ($this->required ? '<span class="star">&#160;*</span>' : '') . '</label>';

				return $label;
		}

		/**
		 * Get the radio button field input markup.
		 *
		 * @return  string
		 */
		protected function getInput()
		{
				// Preset output
				$html = array();

				// Get radio button options
				$options = $this->getOptions();

				// Build the radio buttons
				foreach ($options as $i => $option)
				{
						// Initialize some option attributes
						$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
						$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
						$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
						$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

						// Generate HTML code
						$html[] = '<label id="' . $this->id . $i . '-lbl" class="radio inline" for="' . $this->id . $i . '">'
							. '<input id="' . $this->id . $i . '" type="radio" value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '" name="' . $this->name . '"' . $class . $onclick . $checked . $disabled . ' />'
							. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
				}

				return implode($html);
		}
}
