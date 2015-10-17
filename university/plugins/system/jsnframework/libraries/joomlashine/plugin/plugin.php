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
 * Base plugin class.
 *
 * @package  JSN_Framework
 * @since    1.2.5
 */
abstract class JSNPlugin extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An array that holds the plugin configuration.
	 */
	function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Load plugin language
		$this->loadLanguage();
	}

	/**
	 * Load a view template from plugin directory
	 *
	 * @param   object  &$view  View object reference.
	 * @param   string  $path   Path to template directory.
	 * @param   string  $tpl    Template to load.
	 *
	 * @return  string
	 */
	protected function loadTemplate(&$view, $path, $tpl)
	{
		// Set include path for view template
		$view->addTemplatePath($path);

		// Load template file
		$view->loadTemplate($tpl);

		// Get the array of include path stored in view object
		$path	= $view->get('_path');
		$tplDir	= $path['template'];

		// Remove the recently added path off the array
		array_shift($tplDir);

		// Replace current array with the truncated array
		$path['template'] = $tplDir;

		// Reset the array of include path into view object
		$view->set('_path', $path);

		// Get rendered button to add items slideshow content block
		$html = $view->get('_output');

		// Clean the output buffer stored in view object
		$view->set('_output', null);

		return $html;
	}
}
