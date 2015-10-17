<?php
/**
 * @version    $Id: controller.php 15357 2012-08-22 07:52:45Z hiepnv $
 * @package    JSNPoweradmin
 * @subpackage Item
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

// Import Joomla controller library.
jimport('joomla.application.component.controller');

/**
 * General controller of JSN Poweradmin component
 *
 * Controller (Controllers are where you put all the actual code.) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @package    Joomla.Platform
 * @subpackage Com_Poweradmin
 * @since      11.1
 */
class PoweradminController extends JControllerLegacy
{

    /**
     * Typical view method for MVC based architecture
     *
     * This function is provide as a default implementation, in most cases
     * you will need to override it in your own controllers.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController  A JController object to support chaining.
     *
     * @since   11.1
	 */
	function display($cachable = false, $urlparams = false)
	{
		// Set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'rawmode'));

		PoweradminHelper::addSubmenu(JRequest::getCmd('view'));
		// Call parent behavior
		parent::display($cachable);
	}

	/**
	 * Method for removing extension
	 *
	 * @return	void
	 */
	function removeExtension()
	{
		$user	= JFactory::getUser();
		$component = JRequest::getCmd('component');

		$coreComponents = array(
				'com_content', 'com_admin', 'com_config', 'com_checkin',
				'com_cache', 'com_login', 'com_users', 'com_menus',
				'com_categories', 'com_media',
				'com_messages', 'com_redirect',
				'com_search'
		);

		if ($user->get('id') && preg_match('/^com_/i', $component) && !in_array($component, $coreComponents))
		{
			$dbo = JFactory::getDBO();
			$dbo->setQuery("SELECT extension_id FROM #__extensions WHERE element LIKE '{$component}' AND type LIKE 'component' LIMIT 1");
			$componentId = $dbo->loadResult();

			if (empty($componentId) || !is_numeric($componentId)) {
				$this->setRedirect('index.php');
				return;
			}

			JFactory::getLanguage()->load('com_installer');
			JSNFactory::import('components.com_installer.models.manage');

			$model	= $this->getModel('manage','InstallerModel',array('ignore_request'=>true));
			$result = $model->remove(array($componentId));
			$this->setRedirect('index.php?option=com_installer&view=manage');

			return;
		}

		$this->setRedirect('index.php');
	}
}
