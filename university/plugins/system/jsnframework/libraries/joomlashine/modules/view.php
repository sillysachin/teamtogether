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

// Import necessary Joomla libraries
require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/modules/view.html.php';
require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/modules/modules.php';
require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/modules/helper/modules.php';
// Load language
$lang = JFactory::getLanguage();
$lang->load('com_modules');

/**
 * Module selection view class.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNModulesView extends ModulesViewModules
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
		// Load module model
		$model = JSNBaseModel::getInstance('Modules', 'ModulesModel');
		$this->setModel($model, true);
	}

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		try
		{
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');
			$this->state		= $this->get('State');
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Include the component HTML helpers.
		$this->_path['template'] = array (JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/modules/tmpl');
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// Load assets
		JSNBaseHelper::loadAssets();

		// Do not call parent method to skip showing warning message when there is no any module to list
		$result = $this->loadTemplate($tpl);

		if ($result instanceof Exception)
		{
			return $result;
		}
		
		echo $result;
		echo JSNHtmlAsset::loadScript('jsn/selectorFilter',array(),true);
	}
}
