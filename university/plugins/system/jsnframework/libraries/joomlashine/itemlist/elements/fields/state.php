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

defined('_JEXEC') or die('Restricted access');

/**
 * Form Field Place class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldState extends JFormField
{
	public $type = 'State';

	/**
	 * Method to get the field input.
	 *
	 * @return  html code
	 */
	protected function getInput()
	{
		$app	 = JFactory::getApplication();
		$state   = $app->getUserStateFromRequest('filter.' . $this->name, $this->name, '', 'string');
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('JSN_EXTFW_ITEMLIST_SELECT_STATE'));
		$options[] = JHtml::_('select.option', '1', JText::_('JSN_EXTFW_ITEMLIST_SELECT_PUBLISHED'));
		$options[] = JHtml::_('select.option', '0', JText::_('JSN_EXTFW_ITEMLIST_SELECT_UNPUBLISHED'));
		$html	  = JHTML::_('select.genericList', $options, $this->name, 'class="inputbox jsn-select-value" onchange="this.form.submit()"', 'value', 'text', $state);

		return $html;
	}
}
