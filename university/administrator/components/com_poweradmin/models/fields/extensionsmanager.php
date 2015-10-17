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
class JFormFieldExtensionsManager extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'ExtensionsManager';

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
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/extensions.php';
		// Generate field container id
		$id = str_replace('_', '-', $this->id) . '-field';

		// Preset output

		$supportedExtList	= JSNPaExtensionsHelper::getSupportedExtList();
		JSNHtmlAsset::addScript(JURI::root(true) . '/plugins/system/jsnframework/assets/3rd-party/jquery-tipsy/jquery.tipsy.js');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JS_URI . 'joomlashine/configuration/extmanager.js');
		$customScript = "var baseUrl       = '".JURI::root()."';";
		JSNHtmlAsset::addInlineScript($customScript );

		if (count($supportedExtList))
		{
			$installedComponents	= PoweradminHelper::getInstalledComponents();
			foreach ($supportedExtList as $key=>$ext)
				{
				$_shortName	= str_ireplace("com_", "", $key);
				$ext->name = $_shortName;
				$ext->comInstalled	= in_array($key, $installedComponents) ? true : false;
				$extStatus	=	JSNPaExtensionsHelper::checkInstalledPlugin($_shortName);

				if ($extStatus['isInstalled'])
					{
					$ext->plgInstalled = true;
					$ext->enabled = $extStatus['isEnabled'];
				}
				else
					{
					$ext->plgInstalled	= false;
					$ext->enabled = false;
				}
				$list[$_shortName]	= $ext;
			}
		}

		$html[]	= '<div class="jsn-supported-ext-list">
					<input type="hidden" id="label-disable" value="' . JText::_('JSN_POWERADMIN_EXTPAGE_DISABLE') . '">
					<input type="hidden" id="label-enable" value="' . JText::_('JSN_POWERADMIN_EXTPAGE_ENABLE') . '">
					<input type="hidden" id="label-install" value="' . JText::_('JSN_POWERADMIN_EXTPAGE_INSTALL') . '">
					';
		$html[]	= '<ul class="thumbnails">';
		foreach ($list as $ext){
			$_className	= '';
			$_alt		= '';
			$posibleAct = '';
			$_id	= JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX . $ext->name;

			if (!$ext->comInstalled)
			{
				$_className	= 'item-locked';
				$_alt	= JText::_('JSN_POWERADMIN_EXTPAGE_COM_NOT_INSTALLED_EXPLAIN');
				$posibleAct = '<a class="btn btn-primary disabled" href="#" title="' .  JText::_('JSN_POWERADMIN_EXTPAGE_COM_NOT_INSTALLED_EXPLAIN') . '">' . JText::_('JSN_POWERADMIN_EXTPAGE_INSTALL') . '</a>';
			}
			else if ($ext->plgInstalled)
			{
				if (!$ext->enabled)
				{
					$_className	= 'item-installed item-disabled';
					$_alt		= JText::_('JSN_POWERADMIN_EXTPAGE_CLICK_TO_ENABLE');
					$posibleAct	= '<a class="btn btn-primary" id="' . $_id . '" act="enable" href="#">' . JText::_('JSN_POWERADMIN_EXTPAGE_ENABLE') . '</a>';
				}
				else
				{
					$_className	= 'item-installed item-enabled';
					$_alt		= JText::_('JSN_POWERADMIN_EXTPAGE_CLICK_TO_DISABLE');
					$posibleAct = '<a class="btn btn-primary" id="' . $_id . '" act="disable" href="#">' . JText::_('JSN_POWERADMIN_EXTPAGE_DISABLE') . '</a>';
				}
			}
			else
			{
				$_className	= 'item-notinstalled';
				$_alt		= JText::_('JSN_POWERADMIN_EXTPAGE_CLICK_TO_INSTALL');
				$posibleAct = '<a class="btn btn-primary" id="' . $_id . '" act="install" href="#">' . JText::_('JSN_POWERADMIN_EXTPAGE_INSTALL') . '</a>';
			}


			$html[]	= 	'	 <li class="span4">
								<div class="thumbnail">
									<img src="'. $ext->thumbnail.'" alt="">
									<div class="caption">
										<h2>'. ucfirst($ext->name) .'</h2>
										<p>
											' . $posibleAct . '
										</p>
									</div>
								</div>
							</li>';
		}

		$html[] = '</ul>';

		return implode($html);
	}

}
