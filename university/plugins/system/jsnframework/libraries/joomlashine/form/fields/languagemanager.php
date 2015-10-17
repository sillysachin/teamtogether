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

// Import necessary Joomla library
jimport('joomla.filesystem.folder');

/**
 * Create language manager form.
 *
 * Below is a sample field declaration for generating language manager form:
 *
 * <code>&lt;field name="languagemanager" type="languagemanager" /&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldLanguageManager extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'LanguageManager';

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
	 * Get the language manager markup.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Generate field container id
		$id = str_replace('_', '-', $this->id) . '-field';

		// Preset output
		$html[] = '
<style type="text/css">
	#' . $id . ' {
		position: relative;
	}
	.jsn-bootstrap #' . $id . ' label.checkbox a:hover {
		text-decoration: inherit;
	}
	.jsn-bootstrap #' . $id . ' .alert {
		position: absolute;
		top: 0;
		left: 50%;
		white-space: nowrap;
	}
	.jsn-bootstrap #' . $id . ' .alert ul {
		margin-bottom: 0;
	}
	.ui-dialog-buttonpane .ui-dialog-buttonset button.jsn-loading {
		display: inline-block;
		border: 0;
		padding: 0;
		width: 42px;
		height: 42px;
		background: url(../plugins/system/jsnframework/assets/joomlashine/images/icons-32/icon-32-loading-circle.gif) 50% 50% no-repeat;
		font-size: 0;
	}
</style>
<p class="item-title">' . JText::_('JSN_EXTFW_LANGUAGE_SELECT') . '</p>';

		foreach ($this->getOptions() AS $lang)
		{
			// Initialize variables
			$component = JFactory::getApplication()->input->getCmd('option');
			$langText = JText::_('JSN_EXTFW_LANGUAGE_' . strtoupper(str_replace('-', '', $lang)));
			$editLink = JUri::root() . 'plugins/system/jsnframework/libraries/joomlashine/editors/language/index.php?component=' . $component;
			$revertLink = $editLink . '&task=post.revert';

			// Check necessary attributes
			$aChecked	= JSNUtilsLanguage::installed($lang) ? ' checked="checked"' : '';
			$aDisabled	= ( ! JSNUtilsLanguage::installable($lang) OR JSNUtilsLanguage::installed($lang) OR ! JSNUtilsLanguage::supported($lang))
						? ' disabled="disabled"'
						: '';
			$aEditable	= JSNUtilsLanguage::installed($lang)
						? ' <a href="javascript:void(0)" data-source="' . $editLink . '&client=admin&lang=' . $lang . '" title="' . JText::_('JSN_EXTFW_EDITORS_LANG_CLICK_TO_EDIT') . '" class="jsn-language-editor icon16 icon-pencil"></a>'
						: '';
			$aRevert	= JSNUtilsLanguage::edited($lang)
						? '<a href="javascript:void(0)" action="' . $revertLink . '&client=admin&lang=' . $lang . '" title="' . JText::_('JSN_EXTFW_EDITORS_LANG_CLICK_TO_REVERT') . '" class="jsn-language-revert icon16 icon-refresh"></a>'
						: '';

			$sChecked	= JSNUtilsLanguage::installed($lang, true) ? ' checked="checked"' : '';
			$sDisabled	= ( ! JSNUtilsLanguage::installable($lang, true) OR JSNUtilsLanguage::installed($lang, true) OR ! JSNUtilsLanguage::supported($lang, true))
						? ' disabled="disabled"'
						: '';
			$sEditable	= JSNUtilsLanguage::installed($lang, true)
						? ' <a href="javascript:void(0)" data-source="' . $editLink . '&client=site&lang=' . $lang . '" title="' . JText::_('JSN_EXTFW_EDITORS_LANG_CLICK_TO_EDIT') . '" class="jsn-language-editor icon16 icon-pencil"></a>'
						: '';
			$sRevert	= JSNUtilsLanguage::edited($lang, true)
						? '<a href="javascript:void(0)" action="' . $revertLink . '&client=site&lang=' . $lang . '" title="' . JText::_('JSN_EXTFW_EDITORS_LANG_CLICK_TO_REVERT') . '" class="jsn-language-revert icon16 icon-refresh"></a>'
						: '';

			// Generate markup for language manager
			$html[] = '
<div class="jsn-language-item ' . $lang . '">
	<span class="jsn-icon24 jsn-icon-flag ' . strtolower($lang) . '"></span>
	<label class="checkbox">
		<input type="checkbox" name="languagemanager[a][]" value="' . $lang . '"' . $aDisabled . $aChecked . ' />
		<span>' . $lang . ' - ' . $langText . ' (' . JText::_('JADMINISTRATOR') . ')</span>' . " {$aEditable} {$aRevert}" . '
	</label>
	<label class="checkbox">
		<input type="checkbox" name="languagemanager[s][]" value="' . $lang . '"' . $sDisabled . $sChecked . ' />
		<span>' . $lang . ' - ' . $langText . ' (' . JText::_('JSITE') . ')</span>' . " {$sEditable} {$sRevert}" . '
	</label>
</div>';
		}

		$html[] = '
<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" />
<div class="clearbreak"></div>
';

		// Load language editor script
		$html[] = JSNHtmlAsset::loadScript(
			'jsn/languagemanager',
			array(
				'editSelector'		=> 'a.jsn-language-editor',
				'revertSelector'	=> 'a.jsn-language-revert',
				'language'			=> JSNUtilsLanguage::getTranslated(
					array(
						'JSN_EXTFW_EDITORS_LANG',
						'JSN_EXTFW_EDITORS_LANG_REVERT_CONFIRM',
						'JSN_EXTFW_EDITORS_LANG_LAST_REVERT_FAIL',
						'JSN_EXTFW_EDITORS_LANG_REVERT_SUCCESS'
					)
				)
			),
			true
		);

		return implode($html);
	}

	/**
	 * Get the field options for supported language list.
	 *
	 * @return  array
	 */
	protected function getOptions()
	{
		// Looking for language packages
		$admin	= JFolder::folders(JPATH_COMPONENT_ADMINISTRATOR . '/language/admin');
		$site	= JFolder::folders(JPATH_COMPONENT_ADMINISTRATOR . '/language/site');

		if ($admin AND $site)
		{
			$options = array_merge($admin, $site);
		}
		elseif ($admin OR $site)
		{
			$options = $admin ? $admin : $site;
		}

		return isset($options) ? array_unique($options) : array();
	}
}
