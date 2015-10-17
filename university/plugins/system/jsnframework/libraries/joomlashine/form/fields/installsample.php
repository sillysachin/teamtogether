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
 * Create sample data installation form.
 *
 * Below is a sample field declaration for generating sample data installation
 * form:
 *
 * <code>&lt;field
 *     name="installsample" type="installsample" task="data.installSample"
 *     download-url="http://localhost/jsn/framework/jsn-sample-data-j25.zip"
 *     warning-message="JSN_SAMPLE_SAMPLE_DATA_INSTALLATION_WARN"
 *     confirm-message="JSN_SAMPLE_SAMPLE_DATA_INSTALLATION_CONFIRM"
 * /&gt;</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldInstallSample extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var	string
	 */
	protected $type = 'InstallSample';

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
	 * Get the markup for sample data installation form.
	 *
	 * @return  string
	 */
	protected function getInput()
	{
		// Preset output
		$html = array();

		// Get input object
		$input = JFactory::getApplication()->input;

		// Generate data backup form
		$html[] = '
<form name="JSNDataInstallSample" action="' . JRoute::_('index.php') . '" method="POST" onsubmit="return false;">
	<div id="jsn-data-install-sample-action">
		<div class="alert alert-warning">
			<p><span class="label label-important">' . JText::_('JSN_EXTFW_GENERAL_WARNING') . '</span></p>
			' . JText::_((string) $this->element['warning-message']) . '
		</div>
		<div class="control-group">
			<label for="jsn-data-install-sample-agreement" class="checkbox">
				<input id="jsn-data-install-sample-agreement" name="installsample[agree]" value="1" type="checkbox" />
				<strong>' . JText::_((string) $this->element['confirm-message']) . '</strong>
			</label>
		</div>
		<div class="form-actions">
			<button id="jsn-data-install-sample-button" class="btn btn-primary" value="' . ($this->element['task'] ? (string) $this->element['task'] : 'data.installSample') . '" track-change="yes" ajax-request="yes" disabled="disabled">' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_BUTTON') . '</button>
		</div>
	</div>
	<div id="jsn-data-install-sample-indicator" style="display: none;">
		<p>' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_START') . '</p>
		<ul>
			<li id="jsn-data-install-sample-downloading">
				' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_START_DOWNLOADING') . '
				<span id="jsn-data-install-sample-downloading-indicator" class="jsn-icon16 jsn-icon-loading"></span>
				<span id="jsn-data-install-sample-downloading-notice" class="jsn-processing-message"></span>
				<br />
				<p id="jsn-data-install-sample-downloading-unsuccessful-message" class="jsn-text-important">
					' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_DOWNLOAD_FAIL') . '
				</p>
			</li>
			<li id="jsn-data-install-sample-installing" style="display: none;">
				' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_START_INSTALLING') . '
				<span id="jsn-data-install-sample-installing-indicator" class="jsn-icon16 jsn-icon-loading"></span>
				<span id="jsn-data-install-sample-installing-notice" class="jsn-processing-message"></span>
				<br />
				<p id="jsn-data-install-sample-installing-unsuccessful-message" class="jsn-text-important"></p>
				<div id="jsn-data-install-sample-installing-warnings" class="alert alert-warning">
					<p><span class="label label-important">' . JText::_('JSN_EXTFW_GENERAL_WARNING') . '</span></p>
				</div>
			</li>
		</ul>
	</div>
	<div id="jsn-data-install-sample-manual-installation" style="display: none;">
		<ol>
			<li>1. ' . JText::_('JSN_EXTFW_DATA_MANUAL_DOWNLOAD') . ': <a href="' . (string) $this->element['download-url'] . '" class="btn"><span>' . JText::_('JSN_EXTFW_GENERAL_DOWNLOAD') . '</span></a></li>
			<li>2. ' . JText::_('JSN_EXTFW_DATA_MANUAL_INSTALL') . ': <input type="file" name="sampleDataPackage" size="40" /></li>
		</ol>
		<div class="form-actions">
			<button class="btn btn-primary">' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_BUTTON') . '</button>
		</div>
	</div>
	<div id="jsn-data-install-sample-successfully" style="display: none;">
		<hr>
		<h3 class="jsn-text-success">' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_SUCCESS') . '</h3>
		<p>' . JText::_('JSN_EXTFW_DATA_INSTALL_SAMPLE_SUCCESS_MESSAGE') . '</p>
	</div>
	<input type="hidden" name="installSampleStep" value="" />
	<input type="hidden" name="sampleDownloadUrl" value="' . (string) $this->element['download-url'] . '" />
	<input type="hidden" name="' . $this->name . '" value="JSN_CONFIG_SKIP_SAVING" />
</form>
';

		// Load script to handle remote sample data installation
		$html[] = JSNHtmlAsset::loadScript('jsn/data',
			array(
				'language' => JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_GENERAL_STILL_WORKING', 'JSN_EXTFW_GENERAL_PLEASE_WAIT'))
			),
			true
		);

		return implode($html);
	}
}
