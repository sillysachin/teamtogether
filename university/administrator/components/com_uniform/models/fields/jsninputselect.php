<?php

/**
 * @version     $Id: jsninputselect.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Fields
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('JPATH_BASE') or die;

/**
 * Supports an HTML select list of form
 * 
 * @package     JSNFramework
 * @subpackage  field
 * @since       1.0.0
 */
class JFormFieldJSNInputSelect extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'JSNInputSelect';

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
			$label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' . ($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
		}

		// Add the label text and closing tag
		$label .= '>' . $text . ($this->required ? '<span class="star">&#160;*</span>' : '') . '</label>';

		return $label;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * 
	 * @since	1.6
	 */
	protected function getInput()
	{
		$title = isset($this->value->title) ? (string) $this->value->title : '';
		$value = isset($this->value) ? json_encode($this->value) : '';
		$name = isset($this->name) ? (string) $this->name : '';
		$id = isset($this->id) ? (string) $this->id : '';
		$classInput = isset($this->element['class']) ? $this->element['class'] : '';
		$btnTitle = JText::_('JSN_UNIFORM_SELECTED');
		$html = "<div class=\"input-append row-fluid\">
				    <input type=\"text\" value=\"{$title}\" class=\"{$classInput}\" readonly=\"true\" name=\"{$name}_title\" id=\"{$id}_title\"><button class=\"btn\" id=\"btn_{$id}\" type=\"button\">{$btnTitle}</button><button onclick=\"document.getElementById('{$id}').value='';document.getElementById('{$id}_title').value='';\" class=\"btn btn-icon\" type=\"button\"><i class=\" icon-remove\"></i></button>
				    <input type=\"hidden\" value='{$value}' name=\"{$name}\" id=\"{$id}\">
			    </div>";
		return $html;
	}

}
