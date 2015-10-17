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
 * Create data restore form.
 *
 * Below is a sample field declaration for generating data restore form:
 *
 * <code>&lt;field name="datarestore" type="datarestore" label="JSN_SAMPLE_DATA_RESTORE" task="data.restore" /&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldDataRestore extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'DataRestore';

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
	 * Get the markup for data restore form.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Preset output
		$html = array();

		// Generate data backup form
		$html[] = '
<form name="JSNDataRestoreForm" action="' . JRoute::_('index.php') . '" method="POST" enctype="multipart/form-data" onsubmit="return false;">
	<fieldset>
		<legend>' . JText::_('JSN_EXTFW_DATA_RESTORE') . '</legend>
		<div class="control-group">
			<label class="control-label">' . JText::_('JSN_EXTFW_DATA_RESTORE_FILE') . ':</label>
			<div class="controls">
				<input name="datarestore" type="file" size="70" class="input-file" />
			</div>
		</div>
	</fieldset>
	<div class="form-actions">
		<div class="jsn-bootstrap"></div>
		<button class="btn btn-primary" value="' . ($this->element['task'] ? (string) $this->element['task'] : 'data.restore') . '" disabled="disabled" track-change="yes" ajax-request="disabled">' . JText::_('JSN_EXTFW_DATA_RESTORE_BUTTON') . '</button>
	</div>
	<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" />
</form>
';

		return implode($html);
	}
}
