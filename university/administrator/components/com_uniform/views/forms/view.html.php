<?php

/**
 * @version     $Id: view.html.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Forms
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of Forms.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewForms extends JSNBaseView
{

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	function display($tpl = null)
	{
		// Get config
		$config = JSNConfigHelper::get();

		// Get messages
		$msgs = '';

		if (!$config->get('disable_all_messages'))
		{
			$msgs = JSNUtilsMessage::getList('FORMS');
			$msgs = count($msgs)?JSNUtilsMessage::showMessages($msgs):'';
		}

		// Initialize toolbar
		$this->initToolbar();

		// Load the submenu.
		$input = JFactory::getApplication()->input;
		JSNUniformHelper::addSubmenu($input->get('view', 'forms'));

		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);

		// Display the view
		parent::display($tpl);

		// Load assets
		JSNUniformHelper::addAssets();
		$this->addAssets();
	}

	/**
	 * Load assets.
	 *
	 * @return void
	 */
	protected function addAssets()
	{
		$arrayTranslated = array('JSN_UNIFORM_UPGRADE_EDITION_TITLE', 'JSN_UNIFORM_UPGRADE_EDITION', 'JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0');
		echo JSNHtmlAsset::loadScript('uniform/forms', array('language' => JSNUtilsLanguage::getTranslated($arrayTranslated)), true);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since        1.6
	 */
	protected function initToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');
		$edition = defined('JSN_UNIFORM_EDITION')?JSN_UNIFORM_EDITION:"free";

		if (strtolower($edition) == "free")
		{
			$dataListForm = JSNUniformHelper::getForms();
			$countForm = 3 - count($dataListForm) > 0?3 - count($dataListForm):0;
			$msg = JText::sprintf('JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0', (int) $countForm) . ' <a class="jsn-link-action" href="index.php?option=com_uniform&view=upgrade">' . JText::_("JSN_UNIFORM_UPGRADE_EDITION") . '</a>';

			if (count($dataListForm) < 3)
			{
				JToolBarHelper::addNew('form.add', 'JTOOLBAR_NEW');
				JToolBarHelper::editList('form.edit', 'JTOOLBAR_EDIT');
				JToolBarHelper::custom('forms.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				JFactory::getApplication()->enqueueMessage($msg);
			}
			else
			{

				$bar->appendButton('Custom', '<button class="btn btn-small btn-success disabled jsn-popup-upgrade" onclick="return false;"><i class="icon-new icon-white"></i>' . JText::_('JTOOLBAR_NEW') . '</button>');


				JToolBarHelper::editList('form.edit', 'JTOOLBAR_EDIT');


				$bar->appendButton('Custom', '<button class="btn btn-small disabled jsn-popup-upgrade" onclick="return false;"><i class="icon-copy "></i>' . JText::_('JTOOLBAR_DUPLICATE') . '</button>');


				$session = JFactory::getSession();
				$seesionQueue = $session->get('application.queue');

				if ($seesionQueue[0]['type'] != "error")
				{
					JError::raiseNotice(100, $msg);
				}
			}
		}
		else
		{
			JSNUniformHelper::buttonAddNewForm();
			JToolBarHelper::editList('form.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::custom('forms.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}

		JToolBarHelper::publish('forms.publish', 'JSN_UNIFORM_PUBLISH', true);
		JToolBarHelper::unpublish('forms.unpublish', 'JSN_UNIFORM_UNPUBLISH', true);
		JToolBarHelper::deleteList('JSN_UNIFROM_CONFIRM_DELETE', 'forms.delete', 'JTOOLBAR_DELETE');
		JSNUniformHelper::initToolbar('JSN_UNIFORM_FORMS_MANAGER', 'uniform-forms');
	}


	/**
	 * Render custom date last submission
	 *
	 * @param   type  $item  object item
	 *
	 * @return type
	 */
	public function renderCustomDateLastSubmission($item)
	{
		$lastSubmit = "";
		if (!empty($item->form_last_submitted))
		{
			$checkdate = explode(' ', $item->form_last_submitted);
			$checkdate = explode('-', $checkdate[0]);

			if ($checkdate[0] != '0000' && $checkdate[1] != '00' && $checkdate[2] != '00')
			{
				$dateLastSubmitted = new DateTime($item->form_last_submitted);
				$lastSubmit = $dateLastSubmitted->format('j F Y');
			}
		}
		return $lastSubmit?$lastSubmit:'N/A';
	}

}
