<?php
/**
 * @version     $Id$
 * @package     JSN_Presentation
 * @subpackage  AdminComponent
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
 * Text truncation field renderer
 *
 * @package     JSN_Presentation
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JFormFieldTextLimit extends JSNFormField
{
	protected $type = 'TextLimit';

	/**
	 * Method to get the field input markup
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Initialize text truncation value
		$value	= (int) $this->value;
		$unit	= in_array($unit = substr($this->value, -1), array('c', 'w')) ? $unit : 'w';

		// Generate HTML
		$html	= '<input type="number" value="' . (int) $value . '" class="jsn-input-mini-fluid validate-positive-number" onchange="var field = this.form.querySelector(\'#' . $this->id . '\'); field.value = field.value.replace(/^[\d\s]*/, this.value);" />&nbsp;'
				. '<select onchange="var field = this.form.querySelector(\'#' . $this->id . '\'); field.value = field.value.replace(/[cw]$/, this.options[this.selectedIndex].value);" style="width:auto">'
				. '<option value="c"' . ($unit == 'c' ? ' selected="selected"' : '') . '>' . JText::_('characters') . '</option>'
				. '<option value="w"' . ($unit == 'w' ? ' selected="selected"' : '') . '>' . JText::_('words') . '</option>'
				. '</select>'
				. '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . $value . $unit . '" />';

		return $html;
	}
}
