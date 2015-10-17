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
 * Controller class of JSN Update library.
 *
 * To implement <b>JSNUpdateController</b> class, create a controller file
 * in <b>administrator/components/com_YourComponentName/controllers</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixControllerUpdate extends JSNUpdateController
 * {
 * }</code>
 *
 * The <b>JSNUpdateController</b> class pre-defines <b>download</b> and
 * <b>install</b> method to handle product update task. So, you <b>DO NOT
 * NEED</b> to re-define those methods in your controller class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUpdateController extends JSNBaseController
{
	/**
	 * Download update package.
	 *
	 * @return  void
	 */
	function download()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input, false);

		// Initialize variables
		$this->model = $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));

		// Attempt to download update package
		$return = true;

		try
		{
			$this->model->download();
		}
		catch (Exception $e)
		{
			$return = $e;
		}

		// Complete request
		$this->finalizeRequest($return, $input);
	}

	/**
	 * Install update package.
	 *
	 * @return  void
	 */
	function install()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input, false);

		// Initialize variables
		$this->model = $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));
		$path        = $input->getVar('path');

		// Attempt to install update package
		$return = true;

		try
		{
			$this->model->install($path);
		}
		catch (Exception $e)
		{
			$return = $e;
		}

		// Complete request
		$this->finalizeRequest($return, $input, '', '%s');
	}
}
