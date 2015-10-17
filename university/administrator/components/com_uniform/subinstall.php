<?php
/**
 * @version    $Id: subinstall.php 19029 2012-11-28 07:42:29Z thailv $
 * @package    JSNUniform
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get path to JSN Installer class file
is_readable($base = dirname(__FILE__) . '/admin/jsninstaller.php')
	OR is_readable($base = dirname(__FILE__) . '/jsninstaller.php')
	OR is_readable($base = JPATH_COMPONENT_ADMINISTRATOR . '/jsninstaller.php')
	OR $base = null;

if ( ! empty($base))
{
	require_once $base;
}

/**
 * Class for finalizing JSN Sample installation.
 *
 * @package  JSNSample
 * @since    1.0.0
 */
class Com_UniformInstallerScript extends JSNInstallerScript
{
}
