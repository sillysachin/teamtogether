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

/**
 * Model class of JSN Positions library.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNPositionsModel extends JSNBaseModel
{
	/** private variable **/
	private $_template = '';

	/** private variable **/
	private $_document  = '';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_template = JSNTemplateHelper::getInstance();
		$this->_document = JFactory::getDocument();
	}
	/**
	 * Return global JSNTemplate object.
	 *
	 * @return  object
	 */
	public static function _getInstance()
	{
		static $instances;

		if ( ! isset($instances))
		{
			$instances = array();
		}

		if (empty($instances['JSNPositionsModel']))
		{
			$instance = new JSNPositionsModel;
			$instances['JSNPositionsModel'] = $instance;
		}

		return $instances['JSNPositionsModel'];
	}
	/**
	 * Change format of HTML when render modules using base in joomla
	 *
	 * @return  void
	 */
	public function renderModules()
	{
		$renderer	= $this->_document->loadRenderer('module');
		$positions	= $this->_template->getTemplatePositions();

		if ($positions != null)
		{
			/** if template using joomla modules load **/
			foreach ($positions AS $position)
			{
				if ($this->_document->countModules($position->name))
				{
					$buffer  = JSNHtmlHelper::openTag('div', array('class' => "jsn-element-container_inner"));
					$buffer .= JSNHtmlHelper::openTag('div', array('class' => "jsn-position", 'id' => $position->name . '-jsnposition'));

					foreach (JModuleHelper::getModules($position->name) AS $mod)
					{
						$buffer .= JSNHtmlHelper::openTag('div', array('class' => "poweradmin-module-item", 'id' => $mod->id . '-jsnposition-published', 'title' => $mod->title, 'showtitle' => $mod->showtitle))
								. JSNHtmlHelper::openTag('div', array('id' => $mod->id . '-content', 'class' => 'jsnpw-module-content'))
								. $renderer->render($mod, $position->params)
								. JSNHtmlHelper::closeTag('div')
								. JSNHtmlHelper::closeTag('div');
					}
					$buffer .= JSNHtmlHelper::closeTag('div');
					$buffer .= JSNHtmlHelper::closeTag('div');

					$this->_document->setBuffer($buffer, 'modules', $position->name);
				}
			}
		}
		else
		{
			/** if template not set load positions in index.php file **/
			$positions = $this->_template->loadXMLPositions();

			foreach ($positions AS $position)
			{
				if ($this->_document->countModules($position->name))
				{
					$buffer  = JSNHtmlHelper::openTag('div', array('class' => "jsn-element-container_inner"));
					$buffer .= JSNHtmlHelper::openTag('div', array('class' => "jsn-position", 'id' => $position->name . '-jsnposition'));

					foreach (JModuleHelper::getModules($position) as $mod)
					{
						$buffer .= JSNHtmlHelper::openTag('div', array('class' => "poweradmin-module-item", 'id' => $mod->id . '-jsnposition-published', 'title' => $mod->title, 'showtitle' => $mod->showtitle))
								. JSNHtmlHelper::openTag('div', array('id' => "moduleid-' . $mod->id . '-content"))
								. $renderer->render($mod, $position->params)
								. JSNHtmlHelper::closeTag('div')
								. JSNHtmlHelper::closeTag('div');
					}
					$buffer .= JSNHtmlHelper::closeTag('div');
					$buffer .= JSNHtmlHelper::closeTag('div');

					$this->_document->setBuffer($buffer, 'modules', $position->name);
				}
			}
		}
	}

	/**
	 * Only render positions and set data to joomla document.
	 *
	 * @return  void
	 */
	public function renderEmptyModule()
	{
		$positions  = $this->_template->getTemplatePositions();

		if ($positions == null)
		{
			/** if template not set load positions in index.php file **/
			@$positions = $this->_template->loadXMLPositions();
		}

		if (count($positions))
		{
			foreach ($positions AS $position)
			{
				if ($this->_document->countModules($position->name))
				{
					$buffer  = JSNHtmlHelper::openTag('div', array('class' => "jsn-element-container_inner"));
					$buffer .= JSNHtmlHelper::openTag('div', array('class' => "jsn-position", 'id' => $position->name . '-jsnposition'));
					$buffer .= JSNHtmlHelper::openTag('p') . $position->name . JSNHtmlHelper::closeTag('p');
					$buffer .= JSNHtmlHelper::closeTag('div');
					$buffer .= JSNHtmlHelper::closeTag('div');

					$this->_document->setBuffer($buffer, 'modules', $position->name);
				}
			}
		}
	}

	/**
	 *
	 * Only render empty component
	 */
	public function renderEmptyComponent()
	{
		$component = $this->_document->getBuffer( 'component' );
		$component_buffer =  JSNHtmlHelper::openTag('div',  array('class'=>"jsn-component-container", 'id'=>"jsnrender-component"))
		.JSNHtmlHelper::openTag('p').$this->_document->getTitle().JSNHtmlHelper::closeTag('p')
		.JSNHtmlHelper::closeTag('div');
		$this->_document->setBuffer($component_buffer, 'component');
	}
}
