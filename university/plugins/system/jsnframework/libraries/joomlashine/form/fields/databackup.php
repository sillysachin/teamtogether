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
 * Create data backup form.
 *
 * Below is a sample field declaration for generating data backup form:
 *
 * <code>&lt;field name="databackup" type="databackup" label="JSN_SAMPLE_DATA_BACKUP" task="data.backup"&gt;
 *     &lt;option label="JSN_SAMPLE_CONFIGURATION" type="tables"&gt;
 *         &lt;table&gt;#__jsn_sample_config&lt;/table&gt;
 *     &lt;/option&gt;
 *     &lt;option label="JSN_SAMPLE_DATABASE" type="tables"&gt;
 *         &lt;table&gt;#__jsn_sample_item_list&lt;/table&gt;
 *         &lt;table&gt;#__jsn_sample_messages&lt;/table&gt;
 *     &lt;/option&gt;
 *     &lt;option label="JSN_SAMPLE_MEDIA" type="files"&gt;
 *         &lt;folder filter="\.ini$"&gt;administrator/components/com_sample/language&lt;/folder&gt;
 *         &lt;folder filter="\.(png|jpg)$"&gt;images/banners&lt;/folder&gt;
 *     &lt;/option&gt;
 * &lt;/field&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldDataBackup extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'DataBackup';

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
	 * Get the markup for data backup form.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Preset output
		$html = array();

		// Generate data backup form
		$html[] = '
<form name="JSNDataBackupForm" action="' . JRoute::_('index.php') . '" method="POST" onsubmit="return false;">
	<fieldset>
		<legend>' . JText::_('JSN_EXTFW_DATA_BACKUP') . '</legend>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label">' . JText::_('JSN_EXTFW_DATA_BACKUP_FILE') . ':</label>
					<div class="controls">
						<input type="text" id="jsn-data-backup-name" name="databackup[name]" class="jsn-input-large-fluid" />
						<label class="checkbox" for="jsn-data-backup-timestamp">
							<input type="checkbox" id="jsn-data-backup-timestamp" name="databackup[timestamp]" value="1" />
							' . JText::_('JSN_EXTFW_DATA_BACKUP_TIMESTAMP') . '
						</label>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label">' . JText::_('JSN_EXTFW_DATA_BACKUP_OPTIONS') . ':</label>
					<div class="controls">';

		// Get backup options
		$options = $this->getOptions();

		// Generate options markup
		foreach ($options AS $i => $option)
		{
			// Generate HTML code
			$options[$i] = '
					<label class="checkbox" for="' . $this->id . $i . '">
						<input type="checkbox" id="' . $this->id . $i . '" name="databackup[' . $option->type . '][]" value="' . $option->value . '" />
						' . JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
		}

		// Finalize data backup form
		$html[] = implode($options) . '
					</div>
				</div>
			</div>
		</div>
	</fieldset>
	<div class="form-actions">
		<div class="jsn-bootstrap"></div>
		<button class="btn btn-primary" value="' . ($this->element['task'] ? (string) $this->element['task'] : 'data.backup') . '" disabled="disabled" ajax-request="disabled">' . JText::_('JSN_EXTFW_DATA_BACKUP_BUTTON') . '</button>
	</div>
	<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" />
</form>
';

		return implode($html);
	}

	/**
	 * Get the data backup options.
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

			// Parse option parameters
			$value = array();

			if ( (string) $option['type'] == 'tables')
			{
				// Generate option value
				foreach ($option->table AS $param)
				{
					$value[] = (string) $param;
				}
			}
			elseif ( (string) $option['type'] == 'files')
			{
				// Generate option value
				foreach ($option->folder AS $param)
				{
					$value[(string) $param] = (string) $param['filter'];
				}
			}
			else
			{
				continue;
			}

			// Create a new option object based on the <option /> element
			$tmp = JHtml::_(
				'select.option',
				htmlentities(json_encode($value), ENT_QUOTES, 'UTF-8', false),
				JText::alt(trim((string) $option['label']), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
				'value', 'text'
			);

			// Store the option type
			$tmp->type = (string) $option['type'];

			// Add the option object to the options array
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
