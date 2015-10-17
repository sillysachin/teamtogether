<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: icon.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

/**
 * Poweradmin Component HTML Helper
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin extend com_content
 * @since 1.5
 */
class JHtmlIcon
{
	/**
	 * 
	 * Add HTML tag
	 * 
	 * @param Object $article
	 * @param Object $params
	 */
	static function create($article, $params)
	{
		if ($params->get('show_icons')) {
			$text = JHtml::_('image','system/new.png', JText::_('JNEW'), NULL, true);
		} else {
			$text = JText::_('JNEW').'&#160;';
		}

		$output = '<span class="hasTip" title="'.JText::_('COM_CONTENT_CREATE_ARTICLE').'">'.$text.'</span>';
		return $output;
	}
	
	/**
	 * 
	 * Render an email HTML button
	 * 
	 * @param Object $article
	 * @param Object $params
	 */
	static function email($article, $params )
	{
		if ($params->get('show_icons')) {
			$text = JHtml::_('image','system/emailButton.png', JText::_('JGLOBAL_EMAIL'), NULL, true);
		} else {
			$text = JText::_('JGLOBAL_EMAIL');
		}

		return '<a>'. $text .'</a>';
	}
	/**
	 * 
	 * Render print button/text HTML
	 * 
	 * @param Object $article
	 * @param Object $params
	 */
	static function print_popup($article, $params )
	{
		if ($params->get('show_icons')) {
			$text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
		} else {
			$text = JText::_('JGLOBAL_PRINT');
		}

		return '<a>'.$text.'</a>';
	}
	/**
	 * 
	 * Render print button/text HTML
	 * 
	 * @param Object $article
	 * @param Object $params
	 */
	static function print_screen($article, $params )
	{
		if ($params->get('show_icons')) {
			$text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
		} else {
			$text = JText::_('JGLOBAL_PRINT');
		}
		return '<a >'.$text.'</a>';
	}

}
