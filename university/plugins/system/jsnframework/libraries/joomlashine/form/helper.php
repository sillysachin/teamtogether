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
 * Helper class for renderering form.
 *
 * @package  JSN_Framework
 * @since    1.1.7
 */
class JSNFormHelper
{
	/**
	 * Method to render HTML markup for a form as declared in an JForm object.
	 *
	 * @param   object  $form       JForm object.
	 * @param   string  $nameSpace  Prefix field name with the given name-space, e.g. jform[params]
	 *
	 * @return  string  Generated HTML markup.
	 */
	public static function render($form, $nameSpace = '')
	{
		$html = array();

		// Get fieldsets
		foreach ($form->getFieldsets() AS $fieldset)
		{
			if (isset($fieldset->skipRender) AND (int) $fieldset->skipRender)
			{
				continue;
			}

			// Get fieldset attributes
			$tag	= isset($fieldset->markupTag) ? strtolower($fieldset->markupTag) : 'fieldset';
			$label	= $tag == 'fieldset' ? 'legend' : 'h4';
			$class	= isset($fieldset->class) ? ' class="' . $fieldset->class . '"' : '';

			// Generate open tag
			$html[] = "<{$tag}{$class}>";

			// Generate form legend if declared
			if ($fieldset->label)
			{
				$html[] = "\t<{$label}" . ($label == 'h4' ? ' class="jsn-section-header"' : '') . '>' . JText::_($fieldset->label) . "</{$label}>";
			}

			foreach ($form->getFieldset($fieldset->name) AS $field)
			{
				// Generate field container ID
				$id = ' id="' . $field->id . '-container' . '"';

				if ($field->label)
				{
					// Initialize field label markup
					if (strpos($field->label, 'class=') === false)
					{
						$label = str_replace('<label', '<label class="control-label"', $field->label);
					}
					else
					{
						$label	= strpos($field->label, 'control-label') === false
								? preg_replace('/<label(\s+[^>]*)class="([^"]*)"([^>]*)>/', '<label\1class="control-label \2"\3>', $field->label)
								: $field->label;
					}

					// Initialize field label tooltips
					if (strpos($label, ' hasTip'))
					{
						$label = preg_replace(
							array('/ hasTip/', '/title="[^:]*::/'),
							array('', 'original-title="'),
							$label
						);
					}

					// Generate markup for input field with field label
					$html[] = "\t" . '<div' . $id . ' class="control-group">' . $label . '<div class="controls">' . $field->input . '</div></div>';
				}
				else
				{
					// Generate markup for input field without field label
					$html[] = "\t" . "<div{$id}>{$field->input}</div>";
				}
			}

			// Generate close tag
			$html[] = "</{$tag}>";
		}

		// Finalize form markup
		$html = implode("\n", $html);

		// Set name-space prefix
		if ( ! empty($nameSpace))
		{
			$html = str_replace('name="jform[', 'name="' . $nameSpace . '[', $html);
		}

		// Setup tooltips
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css');
		JSNHtmlAsset::loadScript('jsn/tooltips');

		// Setup form validation
		JSNHtmlAsset::loadScript(
			'jsn/validate',
			array(
				'id' => 'jsn-config-form',
				'lang' => JSNUtilsLanguage::getTranslated(
					array('JSN_EXTFW_INVALID_VALUE_TYPE', 'JSN_EXTFW_ERROR_FORM_VALIDATION_FAILED', 'JSN_EXTFW_SYSTEM_CUSTOM_ASSETS_INVALID')
				)
			)
		);

		return $html;
	}
}
