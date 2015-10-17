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
 * Search box renderer.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JFormFieldTextSearch extends JFormField
{
	public $type = 'Textsearch';

	/**
	 * Method to get the field input.
	 *
	 * @return  html code
	 */
	protected function getInput()
	{
		$app			= JFactory::getApplication();
		$value			= $app->getUserStateFromRequest('filter.' . $this->name, $this->name, '', 'string');
		$inputSearch	= "<label class=\"control-label\">" . JText::_('JSN_EXTFW_ITEMLIST_FILTER') . "</label>
								<input type=\"text\" class=\"input-xlarge\" name=\"{$this->name}\" id=\"{$this->name}\" value=\"{$value}\" />
								<button class=\"btn btn-icon\" onclick=\"this.form.submit();\"><i class=\"icon-search\"></i></button>
								<button class=\"btn jsn-field-btn-reset btn-icon\" onclick=\"document.getElementById('{$this->name}').value='';this.form.submit();\"><i class=\"icon-remove\"></i></button>
							 ";

		return $inputSearch;
	}
}
