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
	 * @var	string
	 */
	protected $type = 'JSNRadio';

	/**
	 * Get the radio button field input markup.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Preset output
		$html = array ();

		// Get radio button options
		$options = $this->getOptions();

		// Build the radio buttons
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes
			$checked  = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$disabled = ! empty($option->disable) ? ' disabled="disabled"' : '';
			$class	= ! empty($option->class) ? ' class="' . $option->class . '"' : '';
			$onclick  = ! empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

			// Generate HTML code
			$html[] = '<label id="' . $this->id . $i . '-lbl" class="radio inline" for="' . $this->id . $i . '">'
				. '<input id="' . $this->id . $i . '" type="radio" value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '" name="' . $this->name . '"' . $class . $onclick . $checked . $disabled . ' />'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
		}

		return implode($html);
	}
}
