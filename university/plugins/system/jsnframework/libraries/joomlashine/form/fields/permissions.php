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

// Import Joomla rules form field renderer
require_once JPATH_ROOT . '/libraries/joomla/form/fields/rules.php';

/**
 * Create permissions form.
 *
 * Below is a sample field declaration for generating permission manager form:
 *
 * <code>&lt;field
 *     name="permissions" type="permissions" class="inputbox" filter="rules" validate="rules"
 *     component="com_sample" section="component"
 * /&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldPermissions extends JFormFieldRules
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	public $type = 'Permissions';

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
	 * Get the field input markup for Access Control Lists.
	 *
	 * Optionally can be associated with a specific component and section.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Initialise some field attributes.
		$section = $this->element['section'] ? (string) $this->element['section'] : '';
		$component = $this->element['component'] ? (string) $this->element['component'] : '';
		$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';

		// Get the actions for the asset.
		$actions = JAccess::getActions($component, $section);

		// Iterate over the children and add to the actions.
		foreach ($this->element->children() as $el)
		{
			if ($el->getName() == 'action')
			{
				$actions[] = (object) array('name' => (string) $el['name'], 'title' => (string) $el['title'],
					'description' => (string) $el['description']);
			}
		}

		// Get the explicit rules for this asset.
		if ($section == 'component')
		{
			// Need to find the asset id by the name of the component.
			try
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__assets'));
				$query->where($db->quoteName('name') . ' = ' . $db->quote($component));
				$db->setQuery($query);
				$assetId = (int) $db->loadResult();

				if ($error = $db->getErrorMsg())
				{
					throw new Exception($error);
				}
			}
			catch (Exception $e)
			{
				return $e->getMessage();
			}
		}
		else
		{
			// Find the asset id of the content.
			// Note that for global configuration, com_config injects asset_id = 1 into the form.
			$assetId = $this->form->getValue($assetField);
		}

		// Get the rules for just this asset (non-recursive).
		$assetRules = JAccess::getAssetRules($assetId);

		// Get the available user groups.
		$groups = $this->getUserGroups();

		// Prepare output
		$html	= array();
		$html[]	= '<div id="permissions-sliders" class="pane-sliders">';
		$html[]	= '<p class="rule-desc">' . JText::_('JLIB_RULES_SETTINGS_DESC') . '</p>';
		$html[]	= '<ul id="rules">';

		// Generate markup for all user groups.
		foreach ($groups as $group)
		{
			$html[] = '<h3 class="pane-toggler title"><a href="javascript:void(0);"><span>';
			$html[] = str_repeat('<span class="level">|&ndash;</span> ', $curLevel = $group->level) . $group->text;
			$html[] = '</span></a></h3>';
			$html[] = '<div class="pane-slider content">';
			$html[] = '<div class="mypanel">';
			$html[] = '<table class="table table-bordered" border="0">';
			$html[] = '<thead>';
			$html[] = '<tr>';

			$html[] = '<th class="center" id="actions-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_ACTION') . '</span>';
			$html[] = '</th>';

			$html[] = '<th class="center" id="settings-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_SELECT_SETTING') . '</span>';
			$html[] = '</th>';

			// The calculated setting is not shown for the root group of global configuration.
			if ($canCalculateSettings = ($group->parent_id || !empty($component)))
			{
				$html[] = '<th class="center" id="aclactionth' . $group->value . '">';
				$html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_CALCULATED_SETTING') . '</span>';
				$html[] = '</th>';
			}

			$html[] = '</tr>';
			$html[] = '</thead>';
			$html[] = '<tbody>';

			foreach ($actions as $action)
			{
				$html[] = '<tr>';
				$html[] = '<td headers="actions-th' . $group->value . '">';
				$html[] = '<label class="control-label" for="' . $this->id . '_' . $action->name . '_' . $group->value . '" original-title="'
					. htmlspecialchars(JText::_($action->description), ENT_COMPAT, 'UTF-8') . '">';
				$html[] = JText::_($action->title);
				$html[] = '</label>';
				$html[] = '</td>';

				$html[] = '<td class="center" headers="settings-th' . $group->value . '">';

				$html[] = '<select name="permissions[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name
					. '_' . $group->value . '" title="'
					. JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text)) . '">';

				$inheritedRule = JAccess::checkGroup($group->value, $action->name, $assetId);

				// Get the actual setting for the action for this group.
				$assetRule = $assetRules->allow($action->name, $group->value);

				// Build the dropdowns for the permissions sliders

				// The parent group has "Not Set", all children can rightly "Inherit" from that.
				$html[] = '<option value=""' . ($assetRule === null ? ' selected="selected"' : '') . '>'
					. JText::_(empty($group->parent_id) && empty($component) ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
				$html[] = '<option value="1"' . ($assetRule === true ? ' selected="selected"' : '') . '>' . JText::_('JLIB_RULES_ALLOWED')
					. '</option>';
				$html[] = '<option value="0"' . ($assetRule === false ? ' selected="selected"' : '') . '>' . JText::_('JLIB_RULES_DENIED')
					. '</option>';

				$html[] = '</select>&#160; ';

				// If this asset's rule is allowed, but the inherited rule is deny, we have a conflict.
				if (($assetRule === true) && ($inheritedRule === false))
				{
					$html[] = JText::_('JLIB_RULES_CONFLICT');
				}

				$html[] = '</td>';

				// Build the Calculated Settings column.
				// The inherited settings column is not displayed for the root group in global configuration.
				if ($canCalculateSettings)
				{
					$html[] = '<td class="center" headers="aclactionth' . $group->value . '">';
					$html[] = '<label class="control-label" style="text-align: center;">';

					// This is where we show the current effective settings considering currrent group, path and cascade.
					// Check whether this is a component or global. Change the text slightly.
					if (JAccess::checkGroup($group->value, 'core.admin') !== true)
					{
						if ($inheritedRule === null)
						{
							$html[] = '<span class="jsn-icon16 jsn-icon-remove" style="opacity: .33;"></span> ' . JText::_('JLIB_RULES_NOT_ALLOWED');
						}
						elseif ($inheritedRule === true)
						{
							$html[] = '<span class="jsn-icon16 jsn-icon-ok"></span> ' . JText::_('JLIB_RULES_ALLOWED');
						}
						elseif ($inheritedRule === false)
						{
							if ($assetRule === false)
							{
								$html[] = '<span class="jsn-icon16 jsn-icon-remove"></span> ' . JText::_('JLIB_RULES_NOT_ALLOWED');
							}
							else
							{
								$html[] = '<span class="jsn-icon16 jsn-icon-remove"></span> <span class="jsn-icon16 jsn-icon-lock"></span> ' . JText::_('JLIB_RULES_NOT_ALLOWED_LOCKED');
							}
						}
					}
					elseif (!empty($component))
					{
						$html[] = '<span class="jsn-icon16 jsn-icon-ok"></span> <span class="jsn-icon16 jsn-icon-lock"></span> ' . JText::_('JLIB_RULES_ALLOWED_ADMIN');
					}
					else
					{
						// Special handling for  groups that have global admin because they can't  be denied.
						// The admin rights can be changed.
						if ($action->name === 'core.admin')
						{
							$html[] = '<span class="jsn-icon16 jsn-icon-ok"></span> ' . JText::_('JLIB_RULES_ALLOWED');
						}
						elseif ($inheritedRule === false)
						{
							// Other actions cannot be changed.
							$html[] = '<span class="jsn-icon16 jsn-icon-remove"></span> <span class="jsn-icon16 jsn-icon-lock"></span> ' . JText::_('JLIB_RULES_NOT_ALLOWED_ADMIN_CONFLICT');
						}
						else
						{
							$html[] = '<span class="jsn-icon16 jsn-icon-ok"></span> <span class="jsn-icon16 jsn-icon-lock"></span> ' . JText::_('JLIB_RULES_ALLOWED_ADMIN');
						}
					}

					$html[] = '</label>';
					$html[] = '</td>';
				}

				$html[] = '</tr>';
			}

			$html[] = '</tbody>';
			$html[] = '</table>';
			$html[] = '</div></div>';
		}

		$html[] = '</ul><div class="rule-notes">';

		if ($section == 'component' || $section == null)
		{
			$html[] = JText::_('JLIB_RULES_SETTING_NOTES');
		}
		else
		{
			$html[] = JText::_('JLIB_RULES_SETTING_NOTES_ITEM');
		}

		$html[] = '</div></div>';
		$html[] = '<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" />';

		// Override default Joomla accordion style
		$html[] = '<style type="text/css">';
		$html[] = '#jsn-config-form #permissions-sliders ul#rules .pane-slider { border: 0; padding: 0; }';
		$html[] = '#jsn-config-form .pane-sliders .title { padding: 0; }';
		$html[] = '.jsn-bootstrap #jsn-config-form .form-horizontal .control-label { float: none; text-align: left; width: auto; }';
		$html[] = '</style>';

		return implode("\n", $html);
	}
}
