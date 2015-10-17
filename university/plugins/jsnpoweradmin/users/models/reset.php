<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN PowerAdmin support for com_content
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

JSNFactory::import('components.com_users.models.reset', 'site');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminUsersModelReset extends UsersModelReset
{
	/**
	 *
	 * Get params of current view
	 */
	protected function populateState()
	{
		$params = JComponentHelper::getParams('com_users');
		$this->setState('params', $params);
	}

	public function getItems($pk)
	{
		return;
	}

	/**
	 *
	 * Get data
	 * @param Array $pk
	 */
	public function &prepareDisplayedData( $pk )
	{
		$params = $this->getState('params');

		$form = JForm::getInstance('loginform', JPATH_ROOT .  '/components/com_users/models/forms/reset_request.xml');
		if (empty($form)) {
			return false;
		}
		$form->removeField("captcha");

		$data	=	null;
		$data->params 	= $params;
		$data->form 	= $form;
		return $data;
	}
}