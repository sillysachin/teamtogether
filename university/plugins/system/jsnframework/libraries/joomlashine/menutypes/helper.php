<?php
/**
 * @version    $Id: helper.php 18023 2012-11-06 07:57:54Z cuongnm $
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
 * Helper class of JSN Config library.
 *
 * @package     JSN_Framework
 * @since       1.0.3
 *
 * @deprecated  1.2.0  Simply extends JSNMenusView, no default template creation required.
 */
class JSNMenutypesHelper
{
	public static function render($this)
	{
		require dirname(__FILE__) . '/tmpl/default.php';
	}
}
