<?php
/**
 * @version     $Id: controller.php 19094 2012-11-30 02:27:22Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Uniform master display controller.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 *
 */
class JSNUniformController extends JSNBaseController
{
		/**
		 * Constructor
		 *
		 * @param   array  $config  An optional associative array of configuration settings.
		 *
		 * @return  void
		 */
		public function __construct($config = array())
		{
				// Get input object
				$this->input = JFactory::getApplication()->input;

				parent::__construct($config);
		}

		/**
		 * Display the page
		 *
		 * @param   boolean  $cachable   If true, the view output will be cached.
		 * @param   array    $urlparams  An array of safe url parameters and their variable types.
		 *
		 * @return  void
		 */
		public function display($cachable = false, $urlparams = false)
		{
				// Set default view
				$this->input->set('view', $this->input->getCmd('view', 'forms'));

				parent::display($cachable, $urlparams);
		}

		/**
		 * Method for hiding a message
		 *
		 * @return  void
		 */
		function hideMsg()
		{
				jexit(JSNUtilsMessage::hideMessage($this->input->getInt('msgId')));
		}

		/**
		 * Launch adapter
		 *
		 * @return  boolean
		 */
		function launchAdapter()
		{
				// Get user input
				$app = JFactory::getApplication();
				$type = $app->input->getCmd('type');
				$formId = $app->input->getInt('form_id');
				// Store user state
				$app->setUserState('com_uniform.add.form_id', $formId);
				switch($type)
				{
						case 'module':
								// Get module info
								$moduleInfo = JSNUniformHelper::getModuleInfo();

								// Generate redirect link
								$link = 'index.php?option=com_modules&task=module.add&eid=' . $moduleInfo->extension_id;

								$this->setRedirect($link);
								break;

						case 'menu':
								// Get component info
								$componentInfo = JSNUniformHelper::getComponentInfo();

								// Generate data for creating new menu item
								$data = array(
										'type' => 'component',
										'title' => '',
										'alias' => '',
										'note' => '',
										'link' => 'index.php?option=com_uniform&view=form',
										'published' => '1',
										'access' => '1',
										'menutype' => $this->input->getCmd('menutype'),
										'parent_id' => '1',
										'browserNav' => '0',
										'home' => '0',
										'language' => '*',
										'template_style_id' => '0',
										'id' => '0',
										'component_id' => $componentInfo->extension_id
								);

								// Fake user state for add/edit menu item page
								$app->setUserState('com_menus.edit.item.data', $data);
								$app->setUserState('com_menus.edit.item.type', 'component');
								$app->setUserState('com_menus.edit.item.link', 'index.php?option=com_uniform&view=form');

								// Generate redirect link
								$link = 'index.php?option=com_menus&view=item&layout=edit';

								$this->setRedirect($link);
								break;
				}

				return true;
		}
}
