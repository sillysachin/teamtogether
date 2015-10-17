<?php

/**
 * @version     $Id: emailsettings.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');

/**
 * Email settings controllers of Jcontroller
 * 
 * @package     Controllers
 * @subpackage  Emailsettings
 * @since       1.6
 */
class JSNUniformControllerEmailsettings extends JSNBaseController
{
	/*
	 * set option view
	 */

	protected $option = JSN_UNIFORM;

	/**
	 *  Save data email settings of form
	 * 
	 * @return redirect
	 */
	public function form()
	{
		// Get items to remove from the request.
		$post = $_POST;

		// Get the model.
		$model = $this->getModel('emailsettings');

		// Remove the items.
		if ($model->saveForm($post))
		{
			$this->setMessage(JText::_("JLIB_APPLICATION_SAVE_SUCCESS"));
		}
		else
		{
			$this->setMessage($model->getError());
		}

		$this->setRedirect(JRoute::_('index.php?option=com_uniform&view=emailsettings&tmpl=component&action=' . $post['jform']['template_notify_to'] . '&control=form&form_id=' . $post['jform']['form_id'], false), JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
	}

	/**
	 *  Save data email settings of configuration
	 * 
	 * @return redirect
	 */
	public function config()
	{
		// Get items to remove from the request.
		$post = $_POST;

		// Get the model.
		$model = $this->getModel('emailsettings');

		// Remove the items.
		if ($model->saveConfig($post))
		{
			$this->setMessage(JText::_("JLIB_APPLICATION_SAVE_SUCCESS"));
		}
		else
		{
			$this->setMessage($model->getError());
		}
		$this->setRedirect(JRoute::_('index.php?option=com_uniform&view=emailsettings&layout=config&tmpl=component&action=' . $post['jform']['template_notify_to'] . '&control=config', false), JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
	}

}
