<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
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
 * This is helper class use to generate well-form format html
 *
 * @package     JSNTPLFramework
 * @subpackage  Form
 * @since       1.0.0
 */
abstract class JSNTPLFormHelper
{
	/**
	 * Outputs a list of checkbox form elements with the proper
	 * markup for twitter bootstrap styles
	 *
	 * @param   string  $name     Name of the control
	 * @param   array   $data     Data that use to generate checkbox
	 * @param   string  $options  Options for checkbox
	 *
	 * @return  string
	 */
	public static function checkbox ($name, $data, $options = array())
	{
		$html = array();

		foreach ($data AS $item)
		{
			$html[] = '
				<label class="checkbox inline ' . $options['class'] . '">
					<input type="checkbox" onclick="jQuery(this).next().val(this.checked ? 1 : 0);"' . ($item['checked']) . ($options['disabled']) . ' />
					<input type="hidden" name="' . $name . '[' . $item['value'] . ']" value="' . ($item['checked'] ? 1 : 0) . '" />
					<span>' . JText::_($item['text']) . '</span>
				</label>';
		}

		return implode('', $html);
	}

	/**
	 * Outputs a list of radio form elements with the proper
	 * markup for twitter bootstrap styles
	 *
	 * @param   string  $name     Name of the control
	 * @param   array   $data     Data that use to generate radio buttons
	 * @param   string  $options  Options for radio buttons
	 *
	 * @return  string
	 */
	public static function radio ($name, $data, $options = array())
	{
		$keyName 	= isset($options['value']) ? $options['value'] : 'value';
		$textName 	= isset($options['text'])  ? $options['text']  : 'text';
		$default    = isset($options['default']) ? $options['default'] : '';
		$disabled   = isset($options['disabled']) ? 'disabled' : '';
		$class      = $disabled;
		$html 		= array();

		foreach ($data as $item)
		{
			$value = (isset($item[$keyName]))  ? $item[$keyName]  : '';
			$text  = (isset($item[$textName])) ? JText::_($item[$textName]) : '';
			$checked = $value == $default ? 'checked' : '';

			$html[] = "
				<label class=\"radio inline {$class}\">
					<input type=\"radio\" name=\"{$name}\" value=\"{$value}\" {$checked} {$disabled} />
					<span>{$text}</span>
				</label>
			";
		}

		return implode('', $html);
	}

	/**
	 * Return HTML markup of input element
	 *
	 * @param   string  $name     Name of the control
	 * @param   array   $data     Value of the control
	 * @param   string  $options  Options for input field
	 *
	 * @return  string
	 */
	public static function input ($name, $data, $options = array())
	{
		if (!is_array($options))
			$options = array();

		if (!isset($options['type']))
			$options['type'] = 'text';

		$attrs = array();
		foreach ($options as $_name => $_value)
			$attrs[] = sprintf('%s="%s"', $_name, htmlentities($_value));

		if (isset($options['disabled']) && $options['disabled'] == true) {
			$attrs['disabled'] = 'disabled';
			$attrs['class'] = 'disabled';
		}

		$attrs[] = 'name="' . $name . '"';
		$attrs[] = 'value="' . $data . '"';

		return sprintf('<input %s/>', implode(' ', $attrs));
	}
}
