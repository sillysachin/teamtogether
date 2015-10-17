<?php
/**
 * Element: Agents
 * Displays a multiselectbox of different browsers
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

class JFormFieldNN_Agents extends JFormField
{
	public $type = 'Agents';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$group = $this->def('group');

		if (!is_array($this->value)) {
			$this->value = explode(',', $this->value);
		}

		$agents = array();
		switch ($group) {
			/* OS */
			case 'os':
				$agents[] = array('Windows ('.JText::_('JALL').')', 'Windows');
				$agents[] = array('Windows 8', 'Windows nt 6.2');
				$agents[] = array('Windows 7', 'Windows nt 6.1');
				$agents[] = array('Windows Vista', 'Windows nt 6.0');
				$agents[] = array('Windows Server 2003', 'Windows nt 5.2');
				$agents[] = array('Windows XP', 'Windows nt 5.1');
				$agents[] = array('Windows 2000 sp1', 'Windows nt 5.01');
				$agents[] = array('Windows 2000', 'Windows nt 5.0');
				$agents[] = array('Windows NT 4.0', 'Windows nt 4.0');
				$agents[] = array('Windows Me', 'Win 9x 4.9');
				$agents[] = array('Windows 98', 'Windows 98');
				$agents[] = array('Windows 95', 'Windows 95');
				$agents[] = array('Windows CE', 'Windows ce');
				$agents[] = array('Mac OS ('.JText::_('JALL').')', '#(Mac OS|Mac_PowerPC|Macintosh)#');
				$agents[] = array('Mac OSX ('.JText::_('JALL').')', 'Mac OS X');
				$agents[] = array('Mac OSX Mountain Lion', 'Mac OS X 10.8');
				$agents[] = array('Mac OSX Lion', 'Mac OS X 10.7');
				$agents[] = array('Mac OSX Snow Leopard', 'Mac OS X 10.6');
				$agents[] = array('Mac OSX Leopard', 'Mac OS X 10.5');
				$agents[] = array('Mac OSX Tiger', 'Mac OS X 10.4');
				$agents[] = array('Mac OSX Panther', 'Mac OS X 10.3');
				$agents[] = array('Mac OSX Jaguar', 'Mac OS X 10.2');
				$agents[] = array('Mac OSX Puma', 'Mac OS X 10.1');
				$agents[] = array('Mac OSX Cheetah', 'Mac OS X 10.0');
				$agents[] = array('Mac OS (classic)', '#(Mac_PowerPC|Macintosh)#');
				$agents[] = array('Linux', '#(Linux|X11)#');
				$agents[] = array('Open BSD', 'OpenBSD');
				$agents[] = array('Sun OS', 'SunOS');
				$agents[] = array('QNX', 'QNX');
				$agents[] = array('BeOS', 'BeOS');
				$agents[] = array('OS/2', 'OS/2');
				break;

			/* Browsers */
			case 'browsers':
				$agents[] = array('Chrome ('.JText::_('JALL').')', 'Chrome');
				//$agents[] = array('Chrome 29', 'Chrome/29.');
				//$agents[] = array('Chrome 28', 'Chrome/28.');
				//$agents[] = array('Chrome 27', 'Chrome/27.');
				$agents[] = array('Chrome 26', 'Chrome/26.');
				$agents[] = array('Chrome 25', 'Chrome/25.');
				$agents[] = array('Chrome 24', 'Chrome/24.');
				$agents[] = array('Chrome 23', 'Chrome/23.');
				$agents[] = array('Chrome 22', 'Chrome/22.');
				$agents[] = array('Chrome 21', 'Chrome/21.');
				$agents[] = array('Chrome 10', 'Chrome/10.');
				$agents[] = array('Chrome 11-20', '#Chrome/(1[1-9]|20)\.#');
				$agents[] = array('Chrome 1-10', '#Chrome/([1-9]|10)\.#');
				$agents[] = array('Firefox ('.JText::_('JALL').')', 'Firefox');
				$agents[] = array('Firefox 22', 'Firefox/22.');
				$agents[] = array('Firefox 21', 'Firefox/21.');
				$agents[] = array('Firefox 20', 'Firefox/20.');
				$agents[] = array('Firefox 19', 'Firefox/19.');
				$agents[] = array('Firefox 18', 'Firefox/18.');
				$agents[] = array('Firefox 17', 'Firefox/17.');
				$agents[] = array('Firefox 16', 'Firefox/16.');
				$agents[] = array('Firefox 15', 'Firefox/15.');
				$agents[] = array('Firefox 14', 'Firefox/14.');
				$agents[] = array('Firefox 13', 'Firefox/13.');
				$agents[] = array('Firefox 12', 'Firefox/12.');
				$agents[] = array('Firefox 11', 'Firefox/11.');
				$agents[] = array('Firefox 1-10', '#Firefox/([1-9]|10)\.#');
				$agents[] = array('Internet Explorer ('.JText::_('JALL').')', 'MSIE');
				$agents[] = array('Internet Explorer 10', 'MSIE 10.');
				$agents[] = array('Internet Explorer 9', 'MSIE 9.');
				$agents[] = array('Internet Explorer 8', 'MSIE 8.');
				$agents[] = array('Internet Explorer 7', 'MSIE 7.');
				$agents[] = array('Internet Explorer 1-6', '#MSIE [1-6]\.#');
				$agents[] = array('Opera ('.JText::_('JALL').')', 'Opera');
				$agents[] = array('Opera 13', 'Opera/13.');
				$agents[] = array('Opera 12', 'Opera/12.');
				$agents[] = array('Opera 11', 'Opera/11.');
				$agents[] = array('Opera 10', 'Opera/10.');
				$agents[] = array('Opera 1-9', '#Opera/[1-9]\.#');
				$agents[] = array('Safari ('.JText::_('JALL').')', 'Safari');
				//$agents[] = array('Safari 8', '#Version/8\..*Safari/#');
				$agents[] = array('Safari 7', '#Version/7\..*Safari/#');
				$agents[] = array('Safari 6', '#Version/6\..*Safari/#');
				$agents[] = array('Safari 5', '#Version/5\..*Safari/#');
				$agents[] = array('Safari 4', '#Version/4\..*Safari/#');
				$agents[] = array('Safari 1-3', '#Version/[1-3]\..*Safari/#');
				break;

			/* Mobile browsers */
			case 'mobile':
				$agents[] = array(JText::_('JALL'), 'mobile');
				$agents[] = array('Android', 'Android');
				$agents[] = array('Blackberry', 'Blackberry');
				$agents[] = array('IE Mobile', 'IEMobile');
				$agents[] = array('iPad', 'iPad');
				$agents[] = array('iPhone', 'iPhone');
				$agents[] = array('iPod Touch', 'iPod');
				$agents[] = array('NetFront', 'NetFront');
				$agents[] = array('Nokia', 'NokiaBrowser');
				$agents[] = array('Opera Mini', 'Opera Mini');
				$agents[] = array('Opera Mobile', 'Opera Mobi');
				$agents[] = array('UC Browser', 'UC Browser');
				break;
		}

		$options = array();
		foreach ($agents as $agent) {
			$option = JHtml::_('select.option', $agent['1'], $agent['0']);
			$options[] = $option;
		}

		$attr = '';
		$attr .= $this->def('size') ? ' size="'.(int) $this->def('size').'"' : '';
		$attr .= ' multiple="multiple"';

		return JHtml::_('select.genericlist', $options, $this->name . '[]', trim($attr), 'value', 'text', $this->value, $this->id);
	}

	private function def($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}
