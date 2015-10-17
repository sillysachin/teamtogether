<?php
/**
 * @version     $Id$
 * @package     JSN_Framework
 * @subpackage  Config
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
 * Create checkbox.
 *
 *
 * @package  JSN_Framework
 * @since    1.0.0
 *
 */
class JFormFieldJSNInlinefield extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'JSNInlinefield';

    /**
     * Flag to tell the field to always be in multiple values mode.
     *
     * @var    boolean
     * @since  11.1
     */

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

        // Get the label text from the XML element, defaulting to the element name
        $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
        $text = $this->translateLabel ? JText::_($text) : $text;

        // Build the class for the label
        $class = array ('control-label');
        //$class[] = ! empty($this->description) ? ' hasTip' : '';
        $class[] = $this->required == true ? ' required' : '';
        $class[] = ! empty($this->labelClass) ? ' ' . $this->labelClass : '';
        
		$class   = implode('', $class);

        // Add the opening label tag and class attribute
        $label .= '<label class="' . $class . '"';

        // If a description is specified, use it to build a tooltip
        if ( ! empty($this->description))
        {
            $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' . ($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
        }

        // Add the label text and closing tag
        $label .= '>' . $text . ($this->required ? '<span class="star">&#160;*</span>' : '') . '</label>';

        return $label;
    }

    /**
     * Method to get the field input markup for check boxes.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */


    protected function getInput()
    {
        // Initialize variables.
        $_value = $this->element['defaultTranslation'] ? JText::_($this->value) : $this->value;
        $_extText = $this->element['exttextTranlation'] ? JText::_($this->element['exttext']) : $this->element['exttext'];
		$class = isset($this->element['class']) ? $this->element['class'] : "";
		
		$html[] = "<input type=\"text\" class=\"{$class}\" value=\"{$_value}\" name=\"{$this->name}\" id=\"$this->id\"> ";
		$html[] = '<span class="jsn-configuration-ext-text">' . $_extText . "</span>";
		if ($this->element['cbname'])
		{
			// Add checkbox.
			$html[] = '<label class="help-inline checkbox" style="margin-left:25px;">';
			$html[] = '<input type="checkbox" name="' . $this->element['cbname'] . '" id="' . $this->element['cbname'] . '" value="1">'. JText::_($this->element['cblabel']);
			$html[] = '</label>';
		}

		return implode($html);
    }
}
