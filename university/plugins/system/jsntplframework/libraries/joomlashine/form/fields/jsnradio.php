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
 * Custom field to output input field that accept only number
 * as a value
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       1.0.0
 */
class JFormFieldJSNRadio extends JSNTPLFormField
{
	public $type = 'JSNRadio';

	protected $showInline = false;
	protected $inputClass = array('radio');

	public function getInput ()
	{
		$data = array();
		$options = array('default' => ($this->value == '' ? (int) $this->element['default'] : $this->value));

		if (isset($this->element['disabled']) && $this->element['disabled'] == 'true') {
			$options['class'] = 'disabled';
			$options['disabled'] = 'disabled';
		}

		// Get all radio options from xml
		foreach ($this->element->children() as $option) {
			$data[] = array(
				'value' => $option['value'],
				'text'  => (string) $option
			);
		}

		return JSNTplFormHelper::radio($this->name, $data, $options);
	}
}
