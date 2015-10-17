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

// Import JSN Update Helper class
jsnimport('joomlashine.update.helper');

/**
 * View class of JSN Update library.
 *
 * To implement <b>JSNUpdateView</b> class, create a view file in
 * <b>administrator/components/com_YourComponentName/views</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixViewUpdate extends JSNUpdateView
 * {
 * }</code>
 *
 * Finally, put the method call below into the <b>tmpl/default.php</b> template
 * file of that view to display product update page:
 *
 * <code>JSNUpdateHelper::render($this->product);</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUpdateView extends JSNBaseView
{
	/**
	 * Display method.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get product info
		$info = JSNUtilsXml::loadManifestCache();

		// Pass data to view
		$this->assignRef('product', $info);

		// Display the template
		parent::display($tpl);
	}
}
