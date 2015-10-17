<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: selectmoduletypes.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
JSNFactory::import('components.com_modules.controllers.module');

class PoweradminControllerSelectmoduletypes extends ModulesControllerModule
{
	public function setModuleType()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the result of the parent method. If an error, just return it.
		$result = parent::add();
		if (JError::isError($result)) {
			return $result;
		}

		// Look for the Extension ID.
		$extensionId = JRequest::getInt('eid');
		$position = JRequest::getVar('position');
		if (empty($extensionId)) {
			$this->setRedirect(JRoute::_('index.php?option=com_poweradmin&view=selectmoduletypes&tmpl=component&position=' . $position, false));
		}else{
			$this->setRedirect(JRoute::_('index.php?option=com_poweradmin&view=module&layout=edit&tmpl=component&id=0&position=' . $position, false));
		}

		$app->setUserState('com_poweradmin.add.module.extension_id', $extensionId);		
	}
}
?>