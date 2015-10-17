<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: jsnswitchmode.php 12627 2012-05-12 08:41:39Z binhpt $
-------------------------------------------------------------------------*/

defined('JPATH_PLATFORM') or die;


/**
 * Renders a JSNSwitchmode button
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JButtonJSNSwitchmode extends JSNButton
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'JSNSwitch';

	public function fetchButton($type = 'JSNSwitch', $icon, $text, $enmodeTitle = '', $offmodeTitle = '')
	{
		$class	= $this->fetchIconClass($icon);
		$html = "<a href=\"#\" onclick=\"jsnToolbars._switchmode(this, '$enmodeTitle', '$offmodeTitle');\" rel=\"$text\" class=\"toolbar turn-off\" id=\"toolbar-switch-help-mode\" title=\"$offmodeTitle\">\n";
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
	public function fetchId($type='JSNSwitch', $html = '', $id = 'JSNSwitch')
	{
		return $this->_parent->getName().'-'.$id;
	}

	public function setClass() {
		
	}
}
?>