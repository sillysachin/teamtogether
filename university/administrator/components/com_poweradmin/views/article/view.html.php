<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: view.html.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * View to edit an article.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since		1.7
 */
class PoweradminViewArticle extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Include the component HTML helpers.
		JSNFactory::import('components.com_content.helpers.content');
		JSNFactory::import('components.com_content.helpers.html.contentadministrator');

		$JSNMedia = JSNFactory::getMedia();
		$JSNMedia->addStyleSheet(JSN_POWERADMIN_STYLE_URI. 'content.css');
		JSNHtmlAsset::addScript(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.content.js');

		if ($this->getLayout() == 'pagebreak') {
			// TODO: This is really dogy - should change this one day.
			$eName	  = JRequest::getVar('e_name');
			$eName	  = preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
			$document = JFactory::getDocument();
			$document->setTitle(JText::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
			$this->assignRef('eName', $eName);
			parent::display($tpl);
			return;
		}

		// Initialiase variables.
		$this->form	 = $this->get('Form');
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = ContentHelper::getActions($this->state->get('filter.category_id'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$language = JFactory::getLanguage();
		$language->load('com_content');

		parent::display($tpl);
	}
}