<?php
/**
 * @package AkeebaBackup
 * @subpackage backuponupdate
 * @copyright Copyright (c)2009-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */
defined('_JEXEC') or die();

// PHP version check
if (defined('PHP_VERSION'))
{
	$version = PHP_VERSION;
}
elseif (function_exists('phpversion'))
{
	$version = phpversion();
}
else
{
	$version = '5.0.0'; // all bets are off!
}

if (!version_compare($version, '5.3.0', '>='))
	return;

// Make sure Akeeba Backup is installed
if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_akeeba'))
{
	return;
}

// Load FOF
if (!defined('FOF_INCLUDED'))
{
	include_once JPATH_SITE . '/libraries/fof/include.php';
}

if (!defined('FOF_INCLUDED') || !class_exists('FOFLess', true))
{
	return;
}

// If this is not the Professional release, bail out. So far I have only
// received complaints about this feature from users of the Core release
// who never bothered to read the documentation. FINE! If you are bitching
// about it, you don't get this feature (unless you are a developer who can
// come here and edit the code). Fair enough.
JLoader::import('joomla.filesystem.file');
$db = JFactory::getDBO();

// Is Akeeba Backup enabled?
$query	 = $db->getQuery(true)
	->select($db->qn('enabled'))
	->from($db->qn('#__extensions'))
	->where($db->qn('element') . ' = ' . $db->q('com_akeeba'))
	->where($db->qn('type') . ' = ' . $db->q('component'));
$db->setQuery($query);
$enabled = $db->loadResult();

if (!$enabled)
{
	return;
}

// Is it the Pro release?
include_once JPATH_ADMINISTRATOR . '/components/com_akeeba/version.php';

if (!defined('AKEEBA_PRO'))
{
	return;
}

if (!AKEEBA_PRO)
{
	return;
}

JLoader::import('joomla.application.plugin');

class plgSystemBackuponupdate extends JPlugin
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Akeeba Backup version check
		JLoader::import('joomla.filesystem.file');
		$file = JPATH_ROOT . '/administrator/components/com_akeeba/version.php';
	}

	public function onAfterInitialise()
	{
		// Make sure this is the back-end
		$app = JFactory::getApplication();

		if (!in_array($app->getName(), array('administrator', 'admin')))
		{
			return;
		}

		// Get the input variables
		$ji			 = new JInput();
		$component	 = $ji->getCmd('option', '');
		$task		 = $ji->getCmd('task', '');
		$view		 = $ji->getCmd('view', '');
		$backedup	 = $ji->getInt('is_backed_up', 0);

		// Perform a redirection on Joomla! Update download or install task, unless we have already backed up the site
		if (($component == 'com_joomlaupdate') && ($task == 'update.install') && !$backedup)
		{
			$return_url = JURI::base() . 'index.php?option=com_joomlaupdate&task=update.install&is_backed_up=1';

			$redirect_url = JURI::base() . 'index.php?option=com_akeeba&view=backup&autostart=1&returnurl=' . urlencode($return_url);

			$app = JFactory::getApplication();
			$app->redirect($redirect_url);
		}
	}
}
