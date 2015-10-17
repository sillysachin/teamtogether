<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Load base class
require_once JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/form/field.php';
require_once JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/form/helper.php';

/**
 * Custom field to output about section for the framework
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       1.0.0
 */
class JFormFieldJSNFolder extends JSNTPLFormField
{
	public $type = 'JSNFolder';

	/**
	 * Return HTML markup for the field
	 *
	 * @return  string
	 */
	public function getInput ()
	{
		// Prepare field attributes
		$this->disabled = (string) $this->element['disabled'] == 'true';

		$attrs = array(
			'id'	=> $this->id,
			'class'	=> (string) $this->element['class']
		);

		! $this->disabled OR $attrs['disabled'] = 'disabled';

		$label = JText::_(isset($this->element['verifyLabel']) ? $this->element['verifyLabel'] : 'JSN_TPLFW_VERIFY');

		$html[] = '<div class="input-append">';

		$html[] = JSNTplFormHelper::input(
			$this->name,
			$this->value,
			$attrs
		);

		if ($this->disabled)
		{
			$html[] = '	<span class="add-on">' . $label . '</span>';
		}
		else
		{
			$html[] = '	<a href="javascript:void(0)" class="add-on btn btn-verify-folder">' . $label . '</a>';
		}

		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '<p class="pull-left label hide" style="margin-bottom:0"></p>';

		return implode("\n", $html);
	}
}
