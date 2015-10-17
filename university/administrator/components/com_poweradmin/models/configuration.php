<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmn
 * @version   $Id: configuration.php 14643 2012-07-30 11:20:44Z thailv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration model of JSN Poweradmin component
 */
class PowerAdminModelConfiguration extends JSNConfigModel
{

	/**
	 * Method to do additional instant update according config change
	 *
	 * @param	string	Name of changed config parameter
	 * @param	mixed	Recent config parameter value
	 * @return	void
	 */
	protected function instantUpdate($name, $value)
	{
		$input = JFactory::getApplication()->input;
		if($name == 'disable_all_messages')
		{
			// Get name of messages table
			$table = '#__jsn_' . substr(JRequest::getCmd('option'), 4) . '_messages';

			// Enable/disable all messages
			$db = JFactory::getDbo();
			$db->setQuery("UPDATE `{$table}` SET published = " . (1 - $value) . " WHERE 1");
			$db->query();
		}elseif ($name == 'languagemanager'){
			// Get variable
			$lang = $input->getVar('languagemanager', array(), 'default', 'array');

			// Install backend languages
			if (isset($lang['a']))
			{
				JSNFactory::import('components.com_poweradmin.helpers.language');
				JSNLanguageHelper::installSupportedExtLangs($lang['a']);
				JSNUtilsLanguage::install($lang['a']);
			}

			// Install frontend languages
			if (isset($lang['s']))
			{
				JSNUtilsLanguage::install($lang['s'], true);
			}


		}else
		{
			return parent::instantUpdate($name, $value);
		}

		return true;
	}
}
