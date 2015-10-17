<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
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
 * Process remote authentication to download
 * quickstart package
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplWidgetQuickstart extends JSNTplWidgetBase
{
	/**
	 * Render login form
	 *
	 * @return  void
	 */
	public function loginAction ()
	{
		// Retrieve version data
		try
		{
			$versionData = JSNTplHelper::getVersionData();
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Find template information by identify name
		foreach ($versionData['items'] AS $item)
		{
			if ($item['identified_name'] == $this->template['id'])
			{
				$templateInfo = $item;

				break;
			}
		}

		if (isset($templateInfo))
		{
			if (isset($templateInfo['editions']) AND is_array($templateInfo['editions']))
			{
				foreach ($templateInfo['editions'] AS $info)
				{
					$edition = trim($info['edition']);

					if (str_replace('PRO ', '', $this->template['edition']) == str_replace('PRO ', '', $edition))
					{
						break;
					}
				}
			}
			elseif (isset($templateInfo['edition']) AND ! empty($templateInfo['edition']))
			{
				$edition = trim($templateInfo['edition']);

				if (str_replace('PRO ', '', $this->template['edition']) == str_replace('PRO ', '', $edition))
				{
					$info = $templateInfo;
				}
			}

			if ( ! isset($info) OR $info['authentication'] == false)
			{
				$this->setResponse(array(
					'auth'			=> false,
					'id'			=> $this->template['id'],
					'edition'		=> $info['edition'],
					'joomlaVersion'	=> JSNTplHelper::getJoomlaVersion(2)
				));
			}
			else
			{
				// Render login view
				$this->render('login', array('template' => $this->template));
			}
		}
	}

	/**
	 * Process checking customer information
	 *
	 * @return  void
	 */
	public function authAction ()
	{
		// Process posted back data that sent from client
		if ($this->request->getMethod() == 'POST')
		{
			$username = $this->request->getString('username', '');
			$password = $this->request->getString('password', '');

			// Create new HTTP Request
			try
			{
				$orderedEditions = JSNTplApiLightcart::getOrderedEditions($this->template['id'], $username, $password);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			$edition = $this->template['edition'];

			if ($edition != 'FREE' AND strpos($edition, 'PRO ') === false)
			{
				$edition = 'PRO ' . $edition;
			}

			if (in_array($edition, $orderedEditions))
			{
				$this->setResponse(array(
					'id' => $this->template['id'],
					'edition' => $edition,
					'joomlaVersion' => JSNTplHelper::getJoomlaVersion(2),
					'username' => urlencode($username),
					'password' => urlencode($password)
				));
			}
			else
			{
				throw new Exception(JText::_('JSN_TPLFW_ERROR_API_ERR02'));
			}
		}
	}
}
