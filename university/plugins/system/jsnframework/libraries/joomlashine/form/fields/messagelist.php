<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Create messages list.
 *
 * Below is a sample field declaration for generating message list for
 * enable/disable application messages:
 *
 * <code>&lt;field name="messagelist" type="messagelist"&gt;
 *     &lt;option value="CONFIGURATION"&gt;JSN_SAMPLE_CONFIGURATION&lt;/option&gt;
 *     &lt;option value="ABOUT"&gt;JSN_SAMPLE_ABOUT&lt;/option&gt;
 * &lt;/field&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldMessageList extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'MessageList';

	/**
	 * Always return null to disable label markup generation.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Get the messages list markup.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Preset output
		$html[] = '
<div class="jsn-page-list">
';

		// Add assets
		$input = JFactory::getApplication()->input;

		if ($input->getInt('ajax') == 1)
		{
			$html[] = JSNHtmlAsset::loadScript('jsn/message', array('option' => $input->getCmd('option')), true);
		}
		else
		{
			JSNHtmlAsset::loadScript('jsn/message', array('option' => $input->getCmd('option')));
		}

		// Get screen filter
		$screen = JFactory::getApplication()->input->getCmd('msg_screen');

		// Create screen filter
		$screens = $this->getOptions();
		array_unshift($screens, JHtml::_('select.option', '', JText::_('JSN_EXTFW_MESSAGE_FILTER_LIST')));
		$screens = JHtml::_('select.genericlist', $screens, 'msg_screen', ' ', 'value', 'text', $screen);

		// Create refresh button and screen filter
		$html[] = '
	<div class="jsn-fieldset-filter">
		<fieldset>
			<div class="pull-left jsn-fieldset-search">
				<a class="btn" title="' . JText::_('JSN_EXTFW_MESSAGE_REFRESH_LIST') . '" id="jsn-button-refresh" ajax-request="yes" ajax-target="#jsn-config-form > div" href="javascript:void(0)"><i class="icon-refresh"></i> ' . JText::_('JSN_EXTFW_MESSAGE_REFRESH_LIST') . '</a>
			</div>
			<div class="pull-right jsn-fieldset-select">
				' . $screens . '
			</div>
			<div class="clearbreak"></div>
		</fieldset>
	</div>
';

		// Get message list
		$msgs = JSNUtilsMessage::getList($screen, true);

		// Render message list
		$html[] = JSNUtilsMessage::showConfig($msgs);
		$html[] = '<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" /></div>';

		return implode($html);
	}

	/**
	 * Get the field options for screen list.
	 *
	 * @return  array
	 */
	protected function getOptions()
	{
		// Preset options array
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element
			$tmp = JHtml::_(
				'select.option',
				(string) $option['value'],
				JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
				'value', 'text'
			);

			// Add the option object to the options array
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
