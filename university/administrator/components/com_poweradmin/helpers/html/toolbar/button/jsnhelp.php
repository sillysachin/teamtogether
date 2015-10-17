<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: jsnhelp.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;


/**
 * Renders a JSNHelp button
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JButtonJSNHelp extends JSNButton
{	
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'JSNHelp';

	public function fetchButton($type = 'JSNHelp', $url, $task, $text, $title, $pageWidth = 750, $pageHeight = 550, $newPage = false)
	{
		$class	= $this->fetchIconClass($task);
		
		if ( !$newPage ){
			$popupPage = "jsnToolbars._openChildPage('".$url."', {title:'".$title."', width:$pageWidth, height:$pageHeight});";
		}else{
			$popupPage = "jsnToolbars._openNewPage('".$href."', {title:'".$title."'});";
		}

		$html = "<a href=\"#\" onclick=\"$popupPage\" rel=\"$title\" class=\"toolbar\" title=\"$title\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "</span>\n";
		$html .= "$text\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @return  string  Button CSS Id
	 * @since   11.1
	 */
	public function fetchId($type='JSNHelp', $html = '', $id = 'JSNHelp')
	{
		return $this->_parent->getName().'-'.$id;
	}
}
?>