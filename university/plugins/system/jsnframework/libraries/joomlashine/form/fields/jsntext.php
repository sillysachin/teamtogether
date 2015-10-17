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
 * Supports an HTML select list of newsfeeds.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldJSNText extends JSNFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'jsnText';

	/**
	 * True to translate the default value string.
	 *
	 * @var	boolean
	 */
	protected $defaultTranslation;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	 The field input markup.
	 */
	protected function getInput()
	{
		$_type = isset($this->element['input-type']) ? $this->element['input-type'] : 'text';
		$_value = $this->element['defaultTranslation'] ? JText::_($this->value) : $this->value;
		$_extText = $this->element['exttextTranlation'] ? JText::_($this->element['exttext']) : $this->element['exttext'];
		$class = isset($this->element['class']) ? $this->element['class'] : "";

		$html[] = "<input type=\"{$_type}\" class=\"{$class}\" value=\"{$_value}\" name=\"{$this->name}\" id=\"$this->id\"> ";
		$html[] = '<span class="help-inline">' . $_extText . "</span>";

		return implode($html);
	}
}
