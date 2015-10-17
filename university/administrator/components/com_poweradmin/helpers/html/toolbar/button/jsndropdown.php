<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: jsndropdown.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;


/**
 * Renders a JSNDropdown button
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JButtonJSNDropdown extends JSNButton
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'JSNDropdown';

	public function fetchButton($type = 'JSNDropdown', $text = '', $title = '', $icon = 'jsn-parent', $childs = '', $href = '', $action = 'popup')
	{
		if ( $action == 'popup' ){
			$onClick = "jsnToolbars._openChildPage('".$href."', {title:'".$title."'});";
			$href = '#';
		}else if( $action == 'newpage' ){
			$onClick = "jsnToolbars._openNewPage('".$href."', {title:'".$title."'});";
			$href = '#';
		}else{
			$onClick = 'javascript:void(0);';
		}
		$class	= $this->fetchIconClass($icon);
		$html = "<a href=\"$href\" onclick=\"$onClick\" rel=\"$title\" class=\"toolbar\" title=\"$title\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "</span>\n";
		$html .= "$text\n";
		$html .= "</a>\n";
		$html .= $childs;
		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @return  string  Button CSS Id
	 * @since   11.1
	 */
	public function fetchId($type='JSNDropdown', $html = '', $id = 'JSNDropdown')
	{
		return $this->_parent->getName().'-'.$id;
	}
}
?>