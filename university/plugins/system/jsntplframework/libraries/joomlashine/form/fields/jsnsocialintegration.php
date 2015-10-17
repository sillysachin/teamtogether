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
 * JSNSocialIntegration field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */
class JFormFieldJSNSocialIntegration extends JSNTplFormField
{
	public $type = 'JFormFieldJSNSocialIntegration';

	/**
	 * Options.
	 *
	 * @var  array
	 */
	protected $options = array();

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
			return JText::_('JSN_TPLFW_SOCIAL_NETWORK_INTEGRATION_MISSING_OPTIONS');
		}

		// Initialize field value
		is_array($this->value) OR $this->value = array();

		// Pass value to option array
		$this->options = $this->value;

		// Preset social channel status array
		@is_array($this->options['status']) OR $this->options['status'] = array();

		// Parse field options
		foreach ($this->element->option AS $channel)
		{
			// Store social channel data
			$this->options[(string) $channel['name']] = array(
				'title' => (string) $channel,
				'link' => isset($this->options[(string) $channel['name']]['link']) ? $this->options[(string) $channel['name']]['link'] : (string) $channel['value'],
				'placeholder' => (string) $channel['placeholder']
			);

			// If channel is configured, add it to status array if missing
			if ($this->options[(string) $channel['name']]['link'] != '' AND ! in_array((string) $channel['name'], $this->options['status']))
			{
				$this->options['status'][] = (string) $channel['name'];
			}
		}

		// Prepare other field attributes
		$this->disabled = ( 'true' == (string) $this->element['disabled'] );

		return parent::getInput();
	}
}
