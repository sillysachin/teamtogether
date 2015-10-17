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
 * Image size field renderer
 *
 * @package     JSN_Presentation
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JFormFieldImageSize extends JSNFormField
{
	protected $type = 'ImageSize';

	/**
	 * Method to get the field input markup
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Initialize image size
		$size = explode('x', $this->value);

		// Generate HTML
		$html	= '<input type="number" value="' . (int) $size[0] . '" class="jsn-input-mini-fluid validate-positive-number" onchange="var field = this.form.querySelector(\'#' . $this->id . '\'); field.value = field.value.replace(/^[\d\s]*x/, this.value + \'x\');" />'
				. ' x '
				. '<input type="number" value="' . (isset($size[1]) ? (int) $size[1] : '') . '" class="jsn-input-mini-fluid validate-positive-number" onchange="var field = this.form.querySelector(\'#' . $this->id . '\'); field.value = field.value.replace(/x[\d\s]*$/, \'x\' + this.value);" />'
				. '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . $this->value . '" />';

		return $html;
	}
}
