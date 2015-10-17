<?php
/**
 * @version     $Id: view.html.php 17617 2012-10-29 08:14:12Z cuongnm $
 * @package     JSN_Poweradmin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import JSN Installer library
require_once JPATH_COMPONENT_ADMINISTRATOR . '/libraries/joomlashine/installer/view.php';

/**
 * Installer view
 *
 * @package     JSN_Poweradmin
 * @since       1.1.0
 */
class PoweradminViewInstaller extends JSNInstallerView
{
	/**
	 * Method to get the model object
	 *
	 * @param   string  $name  The name of the model (optional)
	 *
	 * @return  object
	 */
	public function getModel($name = '')
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/installer.php';

		$model =new PoweradminModelInstaller;

		return $model;
	}
}
