<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldJSNFrameworkCheck extends JFormField
{
	protected $type = 'JSNFrameworkCheck';

	protected function getLabel ()
	{
		return  '';
	}

	protected function getInput ()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_jsntplframework');

		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration("
			#templatestyleOptions .accordion-group,
			#templatestyleOptions .accordion-heading,
			#templatestyleOptions .accordion-inner,
			#templatestyleOptions .accordion-body,
			#templatestyleOptions .controls {
				margin: 0;
				border: 0;
			}

			#templatestyleOptions .accordion-heading {
				display: none;
			}
			.jsn-link-action:link,
			.jsn-link-action:hover {
				color: #025A8D;
				cursor: pointer;
				padding: 1px 2px;
				font-weight: bold;
				text-decoration: underline;
				transition: color 0.3s linear, background 0.3s ease-out;
				-webkit-transition: color 0.3s linear, background 0.3s ease-out;
			}
			.jsn-link-action:hover {
				color: #fff !important;
				text-decoration: none !important;
				background-color: #025A8D;
			}
			.jsn-text-center {
				text-align: center;
			}
		");

		$dbo = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('element LIKE ' . $dbo->quote('jsntplframework'));

		$dbo->setQuery($query);
		$pluginId = $dbo->loadResult();

		return sprintf('<div class="jsn-message">%s</div>', JText::sprintf('JSN_TPLFW_FRAMEWORK_DISABLED', $pluginId));
	}
}
