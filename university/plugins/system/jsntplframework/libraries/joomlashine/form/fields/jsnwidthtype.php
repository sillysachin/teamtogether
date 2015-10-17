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

/**
 * JSNWidthType field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */
class JFormFieldJSNWidthType extends JSNTplFormField
{
	public $type = 'JFormFieldJSNWidthType';

	/**
	 * Width type options.
	 *
	 * @var  array
	 */
	protected $options = array();

	/**
	 * Disable label by default.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput()
	{
		// Make sure we have options declared
		if ( ! isset($this->element->option))
		{
			return JText::_('JSN_TPLFW_LAYOUT_MISSING_WIDTH_TYPE_DECLARATION');
		}

		// Get template data
		$data = JSNTplHelper::getEditingTemplate();

		// Initialize field value
		if (isset($data->params[(string) $this->element['name']]))
		{
			$this->value = $data->params[(string) $this->element['name']];
		}
		else
		{
			! empty($this->value) OR $this->value = (string) $this->element['default'];

			if (is_string($this->value))
			{
				$this->value = (substr($this->value, 0, 1) == '{' AND substr($this->value, -1) == '}')
					? json_decode($this->value, true)
					: array('type' => $this->value);
			}
		}

		// Parse default template width type options
		foreach ($this->element->option AS $option)
		{
			// Store option
			$this->options[(string) $option['name']] = array(
				'label' => (string) $option['label'],
				'suffix' => (string) $option['suffix'],
				'type' => count($option->children()) ? ((int) $option['multiple'] ? 'checkbox' : 'radio') : 'number',
				'options' => $option->children(),
				'class' => (string) $option['class'],
				'pro' => (string) $option['pro'] == 'true' ? true : false
			);

			// Preset missing field value with default value
			if ( ! isset($this->value[(string) $option['name']]))
			{
				if (count($option->children()))
				{
					foreach ($option->children() AS $child)
					{
						if ((string) $child['default'] == 'checked')
						{
							if ((int) $option['multiple'])
							{
								$this->value[(string) $option['name']][] = (string) $child['value'];
							}
							else
							{
								$this->value[(string) $option['name']] = (string) $child['value'];
							}
						}
					}
				}
				else
				{
					$this->value[(string) $option['name']] = (string) $option['default'];
				}
			}
		}

		return parent::getInput();
	}
}
