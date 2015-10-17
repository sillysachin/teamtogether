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
 * Controller class of JSN Config library.
 *
 * To implement <b>JSNConfigController</b> class, create a controller file
 * in <b>administrator/components/com_YourComponentName/controllers</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixControllerConfig extends JSNConfigController
 * {
 * }</code>
 *
 * The <b>JSNConfigController</b> class pre-defines <b>save</b> method for
 * validating then saving configuration data. So, you <b>DO NOT NEED</b> to
 * re-define that method in your controller class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNConfigController extends JSNBaseController
{
	/**
	 * Validate then save configuration data.
	 *
	 * @return  void
	 */
	public function save()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input);

		// Initialize variables
		$this->model	= $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));
		$config			= $this->model->getForm();
		$data			= $input->get('jsnconfig', array(), 'array');

		// Attempt to save the configuration
		$return = true;

		try
		{
			$this->model->save($config, $data, true);
		}
		catch (Exception $e)
		{
			$return = $e;
		}

		// Complete request
		$this->finalizeRequest($return, $input);
	}
}
