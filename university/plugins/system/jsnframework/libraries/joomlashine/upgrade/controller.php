<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class of JSN Upgrade library.
 *
 * To implement <b>JSNUpgradeController</b> class, create a controller file
 * in <b>administrator/components/com_YourComponentName/controllers</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixControllerUpgrade extends JSNUpgradeController
 * {
 * }</code>
 *
 * The <b>JSNUpgradeController</b> class pre-defines <b>download</b> and
 * <b>install</b> method to handle product upgrade task. So, you <b>DO NOT
 * NEED</b> to re-define those methods in your controller class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUpgradeController extends JSNUpdateController
{
	/**
	 * This task will be send list of editions to the client in JSON format.
	 *
	 * @return  void
	 */
	public function editions()
	{
		// Initialize variables
		$input	= JFactory::getApplication()->input;
		$jVer	= new JVersion;
		$info	= JSNUtilsXml::loadManifestCache();

		// Get product edition and identified name
		$edition	= strtolower(JSNUtilsText::getConstant('EDITION'));
		$identified	= ($identified = JSNUtilsText::getConstant('IDENTIFIED_NAME')) ? $identified : strtolower($info->name);

		// Build query string
		$query[] = 'joomla_version=' . urlencode($jVer->RELEASE);
		$query[] = 'username=' . urlencode($input->getUsername('customer_username'));
		$query[] = 'password=' . urlencode($input->getString('customer_password'));
		$query[] = 'identified_name=' . ($input->getCmd('id') ? urlencode($input->getCmd('id')) : urlencode($identified));

		// Finalize link
		$url = str_replace('upgrade=yes', 'upgrade=no', JSN_EXT_DOWNLOAD_UPDATE_URL) . '&' . implode('&', $query);

		// Get results
		try
		{
			$result = JSNUtilsHttp::get($url);

			if (substr($result['body'], 0, 3) == 'ERR')
			{
				jexit(json_encode(array('message' => JText::_('JSN_EXTFW_LIGHTCART_ERROR_' . $result['body']), 'type' => 'error')));
			}

			// JSON-decode the result
			$result = json_decode($result['body']);

			if (is_null($result))
			{
				jexit(json_encode(array('message' => JText::_('JSN_EXTFW_VERSION_CHECK_FAIL'), 'type' => 'error')));
			}

			if ($edition != 'free')
			{
				if ( ! in_array('PRO UNLIMITED', $result->editions))
				{
					jexit(json_encode(array('message' => JText::sprintf('JSN_EXTFW_UPGRADE_YOUR_ACCOUNT_IS_NOT_PROVIDED_WITH_UNLIMITED_EDITION', JText::_($info->name) . ' PRO Unlimited'), 'type' => 'error')));
				}

				$result->editions = array('PRO UNLIMITED');
			}

			jexit(json_encode($result->editions));
		}
		catch (Exception $e)
		{
			jexit(json_encode(array('message' => JText::_('JSN_EXTFW_VERSION_CHECK_FAIL'), 'type' => 'error')));
		}
	}
}
