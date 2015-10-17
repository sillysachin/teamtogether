<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Custom field to output about section for the framework
 * 
 * @package     JSNTPL
 * @subpackage  Form
 * @since       1.0.0
 */
class JFormFieldJSNMedia extends JSNTplFormField
{
	public $type = 'JSNMedia';

	public function getInput ()
	{
		$attrs = array(
			'class'    => @$this->element['class'],
			'id'       => $this->id
		);

		$disable = '';
		if (isset($this->element['disabled']) && $this->element['disabled'] == 'true') {
			$attrs['class'] .= ' disabled';
			$attrs['disabled'] = 'disabled';
			$disable = 'disabled';
		}

		$html  = '<div class="input-append jsn-media-input">';
		$html .= JSNTplFormHelper::input($this->name, $this->value, $attrs);
		$html .= '<a href="index.php?widget=image-selector" id="' . $this->id . '_select" class="add-on btn btn-media ' . $disable . '" data-target="#' . $this->id . '"> ... </a>';
		$html .= '<a href="javascript:void(0)" class="add-on btn btn-media-clear ' . $disable . '" id="' . $this->id . '_clear" data-default="' . (string)$this->element['defaultValue'] . '" data-target="#' . $this->id . '">' . JText::_('JSN_TPLFW_DEFAULT') .'</a>';
		$html .= '</div>';

		return $html;
	}
}
