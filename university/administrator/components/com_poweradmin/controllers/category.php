<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: category.php 12645 2012-05-14 07:45:58Z binhpt $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
JSNFactory::import('components.com_categories.controllers.category');
JSNFactory::localimport('libraries.joomlashine.modules');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_categories
 * @since		1.7
 */
class PoweradminControllerCategory extends CategoriesControllerCategory
{
	/**
	 * 
	 * Redirect to poweradmin article-edit
	 */
	public function edit()
	{
		$editId = JRequest::getVar('id', 0, 'int');
		$app = JFactory::getApplication();
		$app->setUserState('com_poweradmin.edit.category.id', $editId);
		$this->setRedirect('index.php?option=com_poweradmin&view=category&layout=edit&tmpl=component&id='.$editId);
		$this->redirect();
	}
}