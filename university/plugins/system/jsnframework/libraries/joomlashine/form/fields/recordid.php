<?php
/**
 * @version     $Id: recordid.php 19315 2012-12-07 10:21:50Z cuongnm $
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
 * Record ID field renderer
 *
 * @package     JSN_Presentation
 * @subpackage  AdminComponent
 * @since       1.0.0
 */
class JFormFieldRecordId extends JSNFormField
{
	protected $type = 'RecordId';

	/**
	 * The hidden state for the form field.
	 *
	 * @var  boolean
	 */
	protected $hidden = true;

	/**
	 * Method to get the field input markup
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Initialize field visibility
		$hide = (isset($this->element['hide-field']) AND (int) $this->element['hide-field'] > 0);

		// Generate HTML
		$html	= '<input ' . ($hide ? 'type="hidden"' : 'type="number" class="readonly input-mini" readonly="readonly"') . ' name="' . $this->name . '" value="'
				. ($id = ($id = JFactory::getApplication()->input->getInt((string) $this->element['name'])) ? $id : $this->value)
				. '" />';

		return $html;
	}
}
