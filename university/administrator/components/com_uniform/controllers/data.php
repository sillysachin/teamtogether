<?php

/**
 * @version     $Id: data.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Data controller of JSN Framework Sample component
 * 
 * @package     Controllers
 * @subpackage  Data
 * @since       1.6
 */
class JSNUniformControllerData extends JSNDataController
{

	/**
	 * Finalize task request.
	 *
	 * @param   mixed   $return  Model execution results.
	 * @param   object  &$input  JInput object.
	 *
	 * @return  void
	 */
	protected function finalizeRequest($return, &$input)
	{
		// Check the return value
		if ($return instanceof Exception)
		{
			if ($input->getInt('ajax') == 1)
			{
				jexit(JText::sprintf('JERROR_SAVE_FAILED', $return->getMessage()));
			}
			else
			{
				// Save failed, go back to the screen and display a notice.
				JFactory::getApplication()->redirect(
				JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=' . $input->getCmd('view')), JText::sprintf('JERROR_SAVE_FAILED', $return->getMessage()), 'error'
				);
			}
		}

		// Save successed, complete the task
		if ($input->getInt('ajax') == 1)
		{
			jexit(JText::_('JSN_EXTFW_CONFIG_SAVE_SUCCESS'));
		}
		else
		{
			JFactory::getApplication()->redirect(
			JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=' . $input->getCmd('view')), JText::_('JSN_EXTFW_CONFIG_BACKUP_SUCCESS'),'message'
			);
		}
	}

}
