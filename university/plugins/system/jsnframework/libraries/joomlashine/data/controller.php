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
 * Controller class of JSN Data library.
 *
 * To implement <b>JSNDataController</b> class, create a controller file
 * in <b>administrator/components/com_YourComponentName/controllers</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixControllerData extends JSNDataController
 * {
 * }</code>
 *
 * The <b>JSNDataController</b> class pre-defines <b>backup</b>, <b>restore</b>
 * and <b>installSample</b> method to handle data backup/restore and sample
 * data installation task. So, you <b>DO NOT NEED</b> to re-define those methods
 * in your controller class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNDataController extends JSNBaseController
{
	/**
	 * Create data backup then force user to download the backup file.
	 *
	 * @return  void
	 */
	function backup()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input);

		// Initialize variables
		$this->model = $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));
		$options     = $input->getVar('databackup', array(), 'post', 'array');

		// Attempt to backup data
		$return = true;

		try
		{
			$this->model->backup($options);
		}
		catch (Exception $e)
		{
			$return = $e;
		}

		// Complete request
		$this->finalizeRequest($return, $input);
	}

	/**
	 * Restore data from uploaded backup file.
	 *
	 * @return  void
	 */
	function restore()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input);

		// Initialize variables
		$this->model = $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));
		$backup      = $_FILES['datarestore'];

		// Attempt to restore data
		$return = true;

		try
		{
			$this->model->restore($backup);
		}
		catch (Exception $e)
		{
			$return = $e;
		}

		// Complete request
		$this->finalizeRequest($return, $input);
	}

	/**
	 * Install sample data.
	 *
	 * @return  void
	 */
	function installSample()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input);

		// Initialize variables
		$this->model = $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));
		$step        = $input->getInt('installSampleStep');

		// Attempt to install sample data
		$return = true;

		try
		{
			$this->model->installSample($step);
		}
		catch (Exception $e)
		{
			$return = $e;
		}

		// Complete request
		$this->finalizeRequest($return, $input);
	}
}
