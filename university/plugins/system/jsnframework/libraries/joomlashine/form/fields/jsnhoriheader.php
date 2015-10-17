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
 * Create a horizon line with header.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldJSNHoriheader extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'JSNHoriheader';

	/**
	 * Get the field label markup.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		// Preset label
		$label = '';

		// Get the label text from the XML element, defaulting to the element name
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label
		$class = array ();
		$class[] = 'jsn-horizon-header';
		$class[] = ! empty($this->labelClass) ? ' ' . $this->labelClass : '';
		$class   = implode(' ', $class);

		// Add the opening label tag and class attribute
		$label .= '<label class="' . $class . '"';

		// Add the label text and closing tag
		$label .= '>' . $text . '</label>';

		return $label;
	}

	/**
	 * Always return null to disable input markup generation.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		return '';
	}
}
