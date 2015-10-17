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

defined('_JEXEC') or die('Restricted access');

// Call XTC framework
require JPATH_THEMES . '/' . $this->template . '/XTC/XTC.php';

// Load template parameters
$templateParameters = xtcLoadParams();

// Get the selected layout
$layout = $templateParameters->templateLayout;

// Call layout from layouts folder to create HTML
if ( ! class_exists('JSNJoomlaXTCHelper'))
{
	require JPATH_THEMES . '/' . $this->template . '/layouts/' . $layout . '/layout.php';
}
else
{
	/**
	 * Get content to variable and make new format
	 */
	ob_start();
	require JPATH_THEMES . '/' . $this->template . '/layouts/' . $layout . '/layout.php';
	$document = ob_get_contents();
	ob_end_clean();

	$JSNJoomlaXTCHelper = JSNJoomlaXTCHelper::getInstance($this, $document);
	$JSNJoomlaXTCHelper->render();
}
