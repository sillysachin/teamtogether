<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id: configuration.php 14643 2012-07-30 11:20:44Z thailv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration controller of JSN Poweradmin component
 */
class PowerAdminControllerConfiguration extends JSNConfigController
{
	public function changeExtStatus()
	{
		$status	= (int)JRequest::getInt('status');
		$idName	= str_ireplace(JSN_POWERADMIN_EXT_IDENTIFIED_NAME_PREFIX, "", JRequest::getVar('identified_name')) ;

		if (JSNPaExtensionsHelper::enableExt($idName, 'jsnpoweradmin', $status))
		{
			exit('success');
		}
	}

	public function changeEditor()
	{
		include_once JPATH_ROOT . '/administrator/components/com_users/models/user.php';
		$model 	= new UsersModelUser();
		$editor = JRequest::getVar('editor', 'none');

		$user = JFactory::getUser();
		$user->setParam('editor', $editor);

		if ($user->save()) {
			jexit('success');
		}else{
			jexit('error');
		}

	}
}
