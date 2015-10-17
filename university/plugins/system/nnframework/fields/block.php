<?php
/**
 * Element: Block
 * Displays a block with optionally a title and description
 *
 * @package         NoNumber Framework
 * @version         13.4.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2012 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

class JFormFieldNN_Block extends JFormField
{
	public $type = 'Block';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$title = $this->def('label');
		$description = $this->def('description');
		$class = $this->def('class');

		$start = $this->def('start', 0);
		$end = $this->def('end', 0);

		$html = array();

		if ($start || !$end) {
			$html[] = '</div>';
			if (!(strpos($class, 'alert') === false)) {
				$html[] = '<div class="alert ' . $class . '">';
			} else {
				$html[] = '<div class="well well-small nn_well ' . $class . '">';
			}
			if ($title) {
				$title = NNText::html_entity_decoder(JText::_($title));
				$html[] = '<h4>' . $title . '</h4>';
			}
			if ($description) {
				// variables
				$v1 = JText::_($this->def('var1'));
				$v2 = JText::_($this->def('var2'));
				$v3 = JText::_($this->def('var3'));
				$v4 = JText::_($this->def('var4'));
				$v5 = JText::_($this->def('var5'));

				$description = NNText::html_entity_decoder(trim(JText::sprintf($description, $v1, $v2, $v3, $v4, $v5)));
				$description = str_replace('span style="font-family:monospace;"', 'span class="nn_code"', $description);
				$html[] = '<div>' . $description . '</div>';
			}
			$html[] = '<div><div>';
		}
		if (!$start && !$end) {
			$html[] = '</div>';
		}

		return '</div>' . implode('', $html);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}
