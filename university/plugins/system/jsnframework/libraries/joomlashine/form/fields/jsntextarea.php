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
class JFormFieldJSNTextarea extends JSNFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'JSNTextarea';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 */
	protected function getInput()
	{
		$class = isset($this->element['class']) ? $this->element['class'] : '';
		$rows  = isset($this->element['rows']) ? "rows=\"{$this->element['rows']}\"" : '';
		$cols  = isset($this->element['cols']) ? "cols=\"{$this->element['cols']}\"" : '';
		$html  = "<textarea class=\"{$class}\" {$rows} {$cols} name=\"{$this->name}\" id=\"$this->id\">{$this->value}</textarea>";

		return $html;
	}
}
