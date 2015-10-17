<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_templates/helpers/templates.php';

JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_modules/helpers/html');
$clientId       = $this->item->client_id;
$appliedPosition = JRequest::getVar('position', '');

$templates      = array_keys(ModulesHelper::getTemplates($clientId));
$templateGroups = array();

// Add an empty value to be able to deselect a module position
$option = ModulesHelper::createOption();
$templateGroups[''] = ModulesHelper::createOptionGroup('', array($option));

// Add positions from templates
$isTemplatePosition = false;
foreach ($templates as $template)
{
	$options = array();

	$positions = TemplatesHelper::getPositions($clientId, $template);
	foreach ($positions as $position)
	{
		$text = ModulesHelper::getTranslatedModulePosition($clientId, $template, $position) . ' [' . $position . ']';
		$options[] = ModulesHelper::createOption($position, $text);

		if (!$isTemplatePosition && $this->item->position === $position)
		{
			$isTemplatePosition = true;
		}
	}

	$templateGroups[$template] = ModulesHelper::createOptionGroup(ucfirst($template), $options);
}

// Add custom position to options
$customGroupText = JText::_('COM_MODULES_CUSTOM_POSITION');
if (!empty($this->item->position) && !$isTemplatePosition)
{
	$option = ModulesHelper::createOption($this->item->position);
	$templateGroups[$customGroupText] = ModulesHelper::createOptionGroup($customGroupText, array($option));
}

// Build field
$attr = array(
	'id'          => 'jform_position',
	'list.select' => $appliedPosition	
);
echo JHtml::_('select.groupedlist', $templateGroups, 'jform[position]', $attr);
