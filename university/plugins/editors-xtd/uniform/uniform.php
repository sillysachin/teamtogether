<?php
/**
 * @version     $Id: uniform.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Plugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Plugin button select form in articles view
 *
 * @package     Joomla.Plugin
 *
 * @subpackage  Content.joomla
 *
 * @since       1.6
 */
class plgButtonUniform extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param   object  &$subject  The object to observe
	 *
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since 1.5
	 */
	function plgButtonUniform(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Display the button
	 *
	 * @param   String  $name  Name button
	 *
	 * @return array A two element array of (imageName, textToInsert)
	 */
	function onDisplay($name)
	{
		$js = "
		  function jsnSelectForm(id) {
				jInsertEditorText('{uniform form='+id+'/}', '" . $name . "');
				SqueezeBox.close();
		  }";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		$link = 'index.php?option=com_uniform&amp;view=forms&amp;task=forms.viewSelectForm&amp;tmpl=component';

		JHtml::_('behavior.modal');

		$button = new JObject;

		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('JSN_UNIFORM'));
		$button->set('name', 'list');
		$button->set('options', "{handler: 'iframe', size: {x: 600, y: 200}}");

		return $button;
	}
}
