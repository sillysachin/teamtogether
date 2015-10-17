<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: article.php 15009 2012-08-13 09:18:32Z hiepnv $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;
JSNFactory::import('components.com_content.controllers.article');
JSNFactory::localimport('libraries.joomlashine.modules');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminControllerArticle extends ContentControllerArticle
{
	/**
	 * 
	 * Redirect to poweradmin article-edit
	 */
	public function edit()
	{
		$editId = JRequest::getVar('id', 0, 'int');
        $app    = JFactory::getApplication();
        $app->setUserState('com_poweradmin.edit.article.id', $editId);
		$this->setRedirect('index.php?option=com_poweradmin&view=article&layout=edit&tmpl=component&id='.$editId);
		$this->redirect();
	}
}