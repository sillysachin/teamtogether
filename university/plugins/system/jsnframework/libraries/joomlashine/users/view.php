<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
require_once JPATH_ROOT . '/administrator/components/com_users/views/users/view.html.php';
require_once JPATH_ROOT . '/administrator/components/com_users/models/users.php';
require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/users/users.php';

// Load language
$lang = JFactory::getLanguage();
$lang->load('com_users');

/**
 * User selection view class.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNUsersView extends UsersViewUsers
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 */
	public function __construct($config = array())
	{
		// Display only the component output
		JFactory::getApplication()->input->def('tmpl', 'component');

		parent::__construct($config);

		// Load user model
		$model = JSNBaseModel::getInstance('Users', 'UsersModel');
		$this->setModel($model, true);
	}

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Include the component HTML helpers.
		if (!JFactory::getUser()->authorise('core.manage', 'com_users'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		// Include the component HTML helpers.
		$this->_path['template'] = array (JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/users/tmpl');
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// Load assets
		JSNBaseHelper::loadAssets();

		parent::display($tpl);
		echo JSNHtmlAsset::loadScript('jsn/selectorFilter',array(),true);
	}
}
