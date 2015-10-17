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
 * Base helper class for use across JSN libraries and extensions.
 *
 * @method   loadAssets()  Load common stylesheets used in JSN extensions.
 *
 * @package  JSN_Framework
 * @since    1.1.0
 */
class JSNBaseHelper
{
	/**
	 * Load common assets.
	 *
	 * @param   boolean  $inline  Whether to load assets inline or load in header?
	 *
	 * @return  void
	 */
	public static function loadAssets($inline = false)
	{
		// Define common stylesheets
		$stylesheets = array();

		if (JSNVersion::isJoomlaCompatible('3.0'))
		{
			$stylesheets[] = JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css';

			if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$stylesheets[] = JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery.ui.1.9.0.ie.css';
			}
		}
		else
		{
			$stylesheets[] = JSN_URL_ASSETS . '/3rd-party/bootstrap/css/bootstrap.min.css';
			$stylesheets[] = JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.custom.css';

			if (preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']))
			{
				$stylesheets[] = JSN_URL_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.ie.css';
			}
		}

		$stylesheets[] = JSN_URL_ASSETS . '/joomlashine/css/jsn-gui.css';

		// Load stylesheets
		if ( ! $inline)
		{
			JSNHtmlAsset::addStyle($stylesheets);
		}
		else
		{
			foreach ($stylesheets AS $stylesheet)
			{
				$html[] = '<link type="text/css" href="' . $stylesheet . '" rel="stylesheet" />';
			}

			echo implode("\n", $html);
		}

		// Load scripts
		if (JSNVersion::isJoomlaCompatible('3.2'))
		{
			JSNHtmlAsset::addScript(JUri::root(true) . '/media/jui/js/jquery.min.js');
		}
	}
}
