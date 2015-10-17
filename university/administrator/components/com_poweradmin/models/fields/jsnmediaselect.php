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
defined('JPATH_BASE') or die;

/**
 * Supports an HTML media selector
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldJSNMediaselect extends JFormField
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'jsnText';

    /**
     * True to translate the default value string.
     *
     * @var    boolean
     * @since  11.1
     */
    protected $defaultTranslation;

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
     * Method to get the media imput field.
     *
     * @return  string   The field input markup.
     *
     * @since   1.6
     */
    protected function getInput()
    {
        $html[] = '<div class="input-append">';
        $html[] = '<input type="text" readonly="readonly" value="' . $this->value .'" id="' . $this->element['name'] . '" name="' . $this->name . '" class="' . $this->element['class'] . '"
        				><button type="button" class="btn" id="' . $this->element['selectbtnid'] . '">' . $this->element['selectbtnlabel'] . '</button>';

        $html[] = '<button type="button" class="btn inline" onclick=" document.id(\'' . $this->element['name'] .'\').value=\'\'; document.id(\'' . $this->element['name'] .'\').fireEvent(\'change\'); return false; ">Clear</button></div>';

        return implode('', $html);
    }
}
