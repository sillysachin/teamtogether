<?php

/**
 * @version     $Id: view.html.php 14957 2012-08-10 11:47:52Z thailv $
 * @package     JSNUniform
 * @subpackage  Email settings
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');


jimport('joomla.application.component.view');
jimport('joomla.application.helper');

/**
 * View class for a list of Email settings.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.5
 */
class JSNUniformViewEmailsettings extends JSNBaseView
{
	protected $items;
	protected $_document;

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
	public function display($tpl = null)
	{
		// Initialize variables
		$this->_document = JFactory::getDocument();
		$this->_form = $this->get('Form');
		$this->_item = $this->get('Item');

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);

		// Load assets
		JSNUniformHelper::addAssets();
		$this->_addAssets();
	}

	/**
	 * Load extra assets.
	 *
	 * @return void
	 */
	private function _addAssets()
	{
		JSNHtmlAsset::addStyle(JSN_URL_ASSETS . '/3rd-party/jquery-tipsy/tipsy.css');

		$templateReplyTo = isset($this->_item->template_reply_to) ? $this->_item->template_reply_to : '';
		$this->_document->addScriptDeclaration(" var templateRelyTo = '{$templateReplyTo}'");

		$viewLayout = JFactory::getApplication()->input->getWord('layout', 'default');
		$session = JFactory::getSession();
		$seesionQueue = $session->get('application.queue');
		JSNHtmlAsset::registerDepends('uniform/libs/jquery.placeholder', array('jquery'));

		$language = array(
			'JSN_UNIFORM_NO_FIELD_DES',
			'JSN_UNIFORM_NO_FIELD',
			'JSN_UNIFORM_NO_EMAIL_DES',
			'JSN_UNIFORM_SELECTED',
			'JSN_UNIFORM_NO_EMAIL',
			'JSN_UNIFORM_SELECT_FIELD',
			'JSN_UNIFORM_SELECT_FIELDS',
			'JSN_UNIFORM_PLACEHOLDER_EMAIL_FROM_0',
			'JSN_UNIFORM_PLACEHOLDER_EMAIL_REPLY_TO_0',
			'JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_0',
			'JSN_UNIFORM_PLACEHOLDER_EMAIL_FROM_1',
			'JSN_UNIFORM_PLACEHOLDER_EMAIL_REPLY_TO_1',
			'JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_1'
		);

		if ($viewLayout == "default")
		{
			$jCfg = JFactory::getConfig();
			echo JSNHtmlAsset::loadScript('uniform/emailsettings', array('language' => JSNUtilsLanguage::getTranslated($language), 'editor' => $jCfg->get('editor')), true);
		}
		else
		{
			echo JSNHtmlAsset::loadScript('uniform/configemailsettings', array('language' => JSNUtilsLanguage::getTranslated($language)), true);
		}

		if (!empty($seesionQueue[0]['message']))
		{
			$this->_document->addScriptDeclaration(' window.parent.jQuery.closeModalBox(); ');
		}
	}
}
