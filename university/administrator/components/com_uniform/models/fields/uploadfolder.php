<?php

/**
 * @version     $Id: uploadfolder.php 19014 2012-11-28 04:48:56Z thailv $
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
class JFormFieldUploadFolder extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'uploadFolder';

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
		$class = array('control-label');
		$class[] = $this->required == true ? ' required' : '';
		$class[] = !empty($this->labelClass) ? ' ' . $this->labelClass : '';
		$class = implode('', $class);

		// Create tooltip for description
		if (!empty($this->description))
		{
			$label .= '<label class="' . $class . ' jsn-label-des-tipsy"';
			$label .= ' original-title="' . htmlspecialchars($this->translateDescription ? JText::_($this->description) : $this->description, ENT_COMPAT, 'UTF-8') . '" ';
		}else{
			$label .= '<label class="' . $class . '"';
		}

		// Add the label text and closing tag
		$label .= '>' . $text . ($this->required ? ' <span class="star">&#160;*</span>' : '');

		
		// Finalize label
		$label .= '</label>';

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
		$name = isset($this->name) ? (string) $this->name : '';
		$classInput = isset($this->element['class']) ? $this->element['class'] : '';

		$btnTitle = JText::_('JSN_UNIFORM_BUTTON_APPLY');
		$html = "<input type=\"text\" value=\"{$this->value}\" class=\"{$classInput}\" name=\"{$name}\" id=\"{$this->id}\"> ";
		$html .= "<button class=\"btn\" id=\"apply-folder\" type=\"button\">{$btnTitle}</button>";
		$html .= "<input type=\"hidden\" value=\"{$this->value}\" id=\"folder_upload_old\">";
		$html .= "<div id=\"message-verify\"><i id=\"jsn-apply-folder-loading\" class=\"jsn-icon16 jsn-icon-loading\"></i><span id=\"message-apply\"></span></div>";
		return $html;
	}

}
