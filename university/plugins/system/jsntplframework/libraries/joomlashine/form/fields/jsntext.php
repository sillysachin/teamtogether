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

/**
 * Custom field to output input field that accept only number
 * as a value
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       1.0.0
 */
class JFormFieldJSNText extends JSNTPLFormField
{
	public $type = 'JSNText';

	protected $inputClass = array();
	protected $inputAttrs = '';
	protected $textSuffix = '';
	protected $textPrefix = '';
	protected $dataType   = 'text';

	public function getInput ()
	{
		if (isset($this->element['suffix']) && !empty($this->element['suffix'])) {
			$this->inputClass[] = 'input-append';
			$this->textSuffix = (string) $this->element['suffix'];
		}

		if (isset($this->element['dataType'])) {
			$this->dataType = (string) $this->element['dataType'];
		}

		if (isset($this->element['prefix']) && !empty($this->element['prefix'])) {
			$this->inputClass[] = 'input-prepend';
			$this->textPrefix = (string) $this->element['prefix'];
		}

		if (isset($this->element['disabled']) && $this->element['disabled'] == 'true') {
			$this->inputClass[] = 'disabled';
			$this->inputAttrs .= ' disabled="disabled"';
		}

		if (isset($this->element['validate']))
		{
			$this->element['class'] .= ' validate-' . str_replace(' ', '-', (string) $this->element['validate']);
		}

		return $this->renderLayout();
	}
}
