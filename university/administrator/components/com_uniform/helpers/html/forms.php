<?php

/**
 * @version     $Id: forms.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Helper
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
defined('_JEXEC') or die('Restricted access');

/**
 * JSNUniform form helper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 */
class JHtmlForms
{

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value      Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i          The index
	 * @param   boolean  $canChange  Can Change
	 * 
	 * @return  string
	 *
	 * @since   11.1
	 */
	public static function published($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states = array(1 => array('tick.png', 'forms.unpublish', 'JENABLED', 'JSN_UNIFORM_PUBLISHED'), 0 => array('publish_x.png', 'forms.publish', 'JDISABLED', 'JSN_UNIFORM_UNPUBLISHED'));
		$state = JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html = JHtml::_('image', 'admin/' . $state[0], JText::_($state[2]), NULL, true);
		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" title="' . JText::_($state[3]) . '">'
			. $html . '</a>';
		}
		return $html;
	}

}
