<?php
/**
 * Element: Header
 * Displays a title with a bunch of extras, like: description, image, versioncheck
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

class JFormFieldNN_Header extends JFormField
{
	public $type = 'Header';

	protected function getLabel()
	{
		return '';
	}

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$title = $this->def('label');
		$description = $this->def('description');
		$xml = $this->def('xml');
		$url = $this->def('url');

		if ($description) {
			// variables
			$v1 = $this->def('var1');
			$v2 = $this->def('var2');
			$v3 = $this->def('var3');
			$v4 = $this->def('var4');
			$v5 = $this->def('var5');

			$description = NNText::html_entity_decoder(trim(JText::sprintf($description, $v1, $v2, $v3, $v4, $v5)));
		}

		if ($title) {
			$title = JText::_($title);
		}

		if ($description) {
			$description = str_replace('span style="font-family:monospace;"', 'span class="nn_code"', $description);
			if ($description['0'] != '<') {
				$description = '<p>' . $description . '</p>';
			}
		}

		if ($xml) {
			$xml = JApplicationHelper::parseXMLInstallFile(JPATH_SITE . '/' . $xml);
			$version = 0;
			if ($xml && isset($xml['version'])) {
				$version = $xml['version'];
			}
			if ($version) {
				if (!(strpos($version, 'PRO') === false)) {
					$version = str_replace('PRO', '', $version);
					$version .= ' <small style="color:green">[PRO]</small>';
				} else if (!(strpos($version, 'FREE') === false)) {
					$version = str_replace('FREE', '', $version);
					$version .= ' <small style="color:green">[FREE]</small>';
				}
				if ($title) {
					$title .= ' v';
				} else {
					$title = JText::_('Version') . ' ';
				}
				$title .= $version;
			}
		}
		$html = array();

		if ($title) {
			if ($url) {
				$title = '<a href="' . $url . '" target="_blank" title="' . preg_replace('#<[^>]*>#', '', $title) . '">' . $title . '</a>';
			}
			$html[] = '<h4>' . NNText::html_entity_decoder($title) . '</h4>';
		}
		if ($description) {
			$html[] = $description;
		}
		if ($url) {
			$html[] = '<p><a href="' . $url . '" target="_blank" title="' . JText::_('NN_MORE_INFO') . '">' . JText::_('NN_MORE_INFO') . '...</a></p>';
		}

		return '</div><div>' . implode('', $html);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}
