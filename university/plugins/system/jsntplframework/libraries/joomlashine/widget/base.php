<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Media widget
 *
 * @package     JSNTPLFramework
 * @subpackage  Widget
 * @since       1.0.0
 */
abstract class JSNTplWidgetBase
{
	/**
	 * Application instance
	 * @var JApplication
	 */
	protected $app;

	/**
	 * Request handler
	 * @var JRequest | JInput
	 */
	protected $request;

	/**
	 * Session handler
	 * @var JSession
	 */
	protected $session;

	/**
	 * Database object
	 * @var JDatabase
	 */
	protected $dbo;

	/**
	 * Template detailed information
	 * @var array
	 */
	protected $template = array();

	/**
	 * Database execution method name
	 * @var string
	 */
	protected $queryMethod;

	/**
	 * Content will be sent to client
	 * @var mixed
	 */
	protected $responseContent;

	/**
	 * Language management library
	 * @var JLanguage
	 */
	protected $language;

	/**
	 * Constructor method for widget base object
	 */
	public function __construct ()
	{
		$this->app			= JFactory::getApplication();
		$this->session		= JFactory::getSession();
		$this->request		= $this->app->input;
		$this->dbo			= JFactory::getDBO();
		$this->queryMethod	= version_compare(JSNTplHelper::getJoomlaVersion(), '3.0', '>=') ? 'execute' : 'query';
		$this->language		= JFactory::getLanguage();

		$this->language->load('tpl_' . $this->request->getCmd('template'), JPATH_ROOT);
		$this->language->load('lib_joomla');
		$this->_parseTemplateInfo($this->request->getCmd('template'));
	}

	/**
	 * Render action template
	 *
	 * @param   string  $tmpl  Template file name to render
	 * @return  void
	 */
	public function render ($tmpl, $data = array())
	{
		$widgetName = $this->request->getCmd('widget');
		$tmplFile   = JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/widget/tmpl/' . $widgetName . '/' . $tmpl . '.php';

		if (!is_file($tmplFile) || !is_readable($tmplFile))
			throw new Exception('Template file not found: ' . $tmplFile);

		// Extract data to seperated variables
		extract($data);

		// Start output buffer
		ob_start();

		// Load template file
		include $tmplFile;

		// Send rendered content to client
		$this->responseContent = ob_get_clean();
	}

	/**
	 * Set response content to send to client
	 *
	 * @param   mixed  $content  Content will be sent to client
	 * @return  void
	 */
	public function setResponse ($content)
	{
		$this->responseContent = $content;
	}

	/**
	 * Retrieve response content after executed an action
	 *
	 * @return mixed
	 */
	public function getResponse ()
	{
		return $this->responseContent;
	}

	/**
	 * Retrieve template detailed information and store
	 * it in the memory
	 *
	 * @param   string  $name  The template name
	 * @return  void
	 */
	private function _parseTemplateInfo ($name)
	{
		if ( ! ($details = JSNTplTemplateRecognization::detect($name)))
		{
			JFactory::getApplication()->enqueueMessage("The template {$name} is not a valid JoomlaShine template!");
		}

		$this->template = array(
			'name'		=> $name,
			'realName'	=> JText::_($name),
			'id'		=> JSNTplHelper::getTemplateId($name),
			'edition'	=> JSNTplHelper::getTemplateEdition($name),
			'version'	=> JSNTplHelper::getTemplateVersion($name)
		);
	}
}
