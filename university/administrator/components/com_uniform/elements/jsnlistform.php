<?php

/**
 * @version     $Id: jsnlistform.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Elements
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . '/administrator/components/com_uniform/uniform.defines.php';
$lang = JFactory::getLanguage();
$lang->load('com_uniform');

/**
 * Abstract Form Field class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldJsnlistform extends JFormField
{

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$name = $this->name;
		$app = JFactory::getApplication();
		if (!$this->value)
		{
			$this->value = $app->getUserState('com_uniform.add.form_id');
		}
		return JSNUniformHelper::getSelectForm($name, $this->id, "menuform", $this->value,true);
	}

}
