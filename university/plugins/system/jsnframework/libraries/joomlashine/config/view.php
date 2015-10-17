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

// Import JSN Config Helper class
jsnimport('joomlashine.config.helper');

/**
 * View class of JSN Config library.
 *
 * To implement <b>JSNConfigView</b> class, create a view file in
 * <b>administrator/components/com_YourComponentName/views</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixViewConfig extends JSNConfigView
 * {
 * }</code>
 *
 * Finally, put the method call below into the <b>tmpl/default.php</b> template
 * file of that view to display configuration page:
 *
 * <code>JSNConfigHelper::render($this->config);</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNConfigView extends JSNBaseView
{
	/**
	 * Display method.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		// Get config declaration
		$configDeclaration = $this->get('Form');

		// Pass data to view
		$this->assignRef('config', $configDeclaration);

		// Load assets
		JSNBaseHelper::loadAssets();

		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css');

		JSNHtmlAsset::loadScript(
			'jsn/core',
			array('lang' => JSNUtilsLanguage::getTranslated(array('JSN_EXTFW_GENERAL_LOADING', 'JSN_EXTFW_GENERAL_CLOSE')))
		);

		JSNHtmlAsset::loadScript('jsn/config');

		// Display the template
		parent::display($tpl);
	}
}
