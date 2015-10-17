<?php

/**
 * @version     $Id: submission.php 19013 2012-11-28 04:48:47Z thailv $
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

/**
 * Layout controllers of JControllerAdmin
 * 
 * @package     Controllers
 * @subpackage  Submission
 * @since       1.6
 */
class JSNUniformControllerSubmission extends JControllerForm
{

	protected $option = JSN_UNIFORM;

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   11.1
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$post = $_POST;

		// Get the model.
		$model = $this->getModel('submission');

		// Remove the items.
		if ($model->save($post))
		{
			$this->setMessage(JText::_("JLIB_APPLICATION_SAVE_SUCCESS"));
		}
		else
		{
			$this->setMessage($model->getError());
		}
		$task = $this->getTask();
		if ($task == "save")
		{
			// Redirect to the list screen.
			$this->setRedirect(
			JRoute::_(
			'index.php?option=' . $this->option . '&view=' . $this->view_list
			. $this->getRedirectToListAppend(), false
			)
			);
		}
		else
		{
			$dataId = isset($_POST['cid']) ? $_POST['cid'] : 0;
			$this->setRedirect(JRoute::_('index.php?option=com_uniform&view=submission&layout=detail&submission_id=' . (int) $dataId, false), JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
		}
	}

}
