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

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Template upgrade
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplWidgetUpgrade extends JSNTplWidgetBase
{
	/**
	 * Render intro view
	 *
	 * @return  void
	 */
	public function introAction ()
	{
		// Get upgrade details from JoomlaShine server
		$response = null;

		try
		{
			$response = JSNTplHttpRequest::get(JSN_TPLFRAMEWORK_UPGRADE_DETAILS);
			$response = json_decode($response['body'], true);
		}
		catch (Exception $e)
		{
			// Do nothing
		}

		if ($response != null)
		{
			// Get response belonging to current template
			if (isset($response[$this->template['id']]))
			{
				$response = $response[$this->template['id']];
			}
			elseif (isset($response['template']))
			{
				$response = $response['template'];
			}
			else
			{
				$response = $response['default'];
			}

			// Get current template edition
			$currentEdition = strcasecmp($this->template['edition'], 'free') == 0 ? 'free' : 'pro';

			// Prepare content
			$content = isset($response['pro']) ? $response['pro'] : '';

			if ($currentEdition == 'free')
			{
				$content = $response['free'] . $content;
			}
		}

		$this->render('intro', array('template' => $this->template, 'data' => isset($content) ? $content : $response));
	}

	/**
	 * Render login view
	 *
	 * @return  void
	 */
	public function loginAction ()
	{
		$this->render('login', array('template' => $this->template));
	}

	/**
	 * Authentication action before install sample data
	 *
	 * @return  void
	 */
	public function loadEditionsAction ()
	{
		// Process posted back data that sent from client
		if ($this->request->getMethod() == 'POST')
		{
			$username = $this->request->getString('username', '');
			$password = $this->request->getString('password', '');

			// Create new HTTP Request
			try
			{
				// Prepare current edition
				$currentEdition = strtoupper($this->template['edition']);

				if ( $currentEdition != 'FREE' && ! preg_match('/^PRO /i', $currentEdition) )
				{
					$currentEdition = "PRO {$currentEdition}";
				}

				foreach (JSNTplApiLightcart::getOrderedEditions($this->template['id'], $username, $password) AS $edition)
				{
					if (strcasecmp($currentEdition, $edition) != 0)
					{
						$editions[] = $edition;
					}
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}

			if ( ! isset($editions))
			{
				throw new Exception(JText::_('JSN_TPLFW_ERROR_API_ERR02'));
			}

			$this->setResponse($editions);
		}
	}

	/**
	 * Render upgrade view
	 *
	 * @return  void
	 */
	public function upgradeAction ()
	{
		$edition = $this->request->getString('edition');

		if ( ! in_array($edition, array('PRO STANDARD', 'PRO UNLIMITED')))
		{
			throw new Exception('Invalid template edition: ' . $edition);
		}

		$this->render(
			'upgrade',
			array(
				'template' => $this->template,
				'edition' => $edition
			)
		);
	}

	/**
	 * Replace edition in templateDetails.xml
	 *
	 * @return  void
	 */
	public function replaceAction ()
	{
		// Update template's manifest file
		$content = JFile::read(JPATH_ROOT . "/templates/{$this->template['name']}/templateDetails.xml");
		$content = preg_replace('#<edition>(PRO )?STANDARD</edition>#i', '<edition>PRO UNLIMITED</edition>', $content);

		JFile::write(JPATH_ROOT . "/templates/{$this->template['name']}/templateDetails.xml", $content);

		// Update template's definition file
		$content = JFile::read(JPATH_ROOT . "/templates/{$this->template['name']}/template.defines.php");
		$content = preg_replace('/\$JoomlaShine_Template_Edition = \'(PRO )?STANDARD\';/i', '$JoomlaShine_Template_Edition = \'PRO UNLIMITED\';', $content);

		JFile::write(JPATH_ROOT . "/templates/{$this->template['name']}/template.defines.php", $content);
	}

	/**
	 * Download template package
	 *
	 * @return  void
	 */
	public function downloadPackageAction ()
	{
		JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

		try
		{
			$packageFile = JSNTplApiLightcart::downloadPackage(
				$this->template['id'],
				$this->request->getString('edition'),
				$this->request->getString('username'),
				$this->request->getString('password')
			);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		$this->session->set($this->template['id'] . '.upgradePackage', $packageFile);
	}

	/**
	 * Install downloaded package to joomla system
	 *
	 * @return  void
	 */
	public function installAction ()
	{
		JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

		$config = JFactory::getConfig();
		$config->set('debug', 0);

		if ( ! is_file($packageFile = $this->session->get($this->template['id'] . '.upgradePackage')))
		{
			throw new Exception("Package file not found: {$packageFile}");
		}

		// Load extension installation library
		jimport('joomla.installer.helper');

		$language = JFactory::getLanguage();
		$language->load('lib_joomla', JPATH_SITE);

		// Unpack downloaded upgrade package
		$unpackedInfo = JInstallerHelper::unpack($packageFile);

		if (empty($unpackedInfo) OR ! isset($unpackedInfo['dir']))
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_TEMPLATE_PACKAGE_FILE'));
		}

		// Check if template is copied to another name
		if ($xml = simplexml_load_file($unpackedInfo['dir'] . '/template/templateDetails.xml'))
		{
			// Parse template name
			@list($prefix_old, $name_old, $suffix_old) = explode('_', strtolower($this->template['name']), 3);
			list($prefix_new, $name_new, $suffix_new) = explode('_', strtolower(trim((string) $xml->name)), 3);

			if (@$prefix_old != $prefix_new OR @$name_old != $name_new OR ! in_array(@$suffix_old, array('free', 'pro')))
			{
				// Update templateDetails.xml with user defined name
				$content = str_replace((string) $xml->name, $this->template['name'], JFile::read($unpackedInfo['dir'] . '/template/templateDetails.xml'));

				JFile::write($unpackedInfo['dir'] . '/template/templateDetails.xml', $content);

				// State that template name must be reserved
				$reserve = true;
			}
		}

		// Upgrade now
		$installer = JInstaller::getInstance();
		$installer->setUpgrade(true);

		$installResult = $installer->install($unpackedInfo['dir']);

		if ($installResult === false)
		{
			foreach (JError::getErrors() AS $error)
			{
				throw $error;
			}
		}

		// Clean up temporary data
		JInstallerHelper::cleanupInstall($packageFile, $unpackedInfo['dir']);

		// Retrieve style id of installed package
		$q = $this->dbo->getQuery(true);

		$q->select('id');
		$q->from('#__template_styles');
		$q->where('template = ' . $q->quote((isset($reserve) AND $reserve) ? $this->template['name'] : (string) $xml->name));

		$this->dbo->setQuery($q);

		$styleId = $this->dbo->loadResult();

		$q = $this->dbo->getQuery(true);

		$q->update('#__template_styles');
		$q->set('home = 0');
		$q->where('client_id = 0');

		$this->dbo->setQuery($q);
		$this->dbo->{$this->queryMethod}();

		$q = $this->dbo->getQuery(true);

		$q->update('#__template_styles');
		$q->set('home = 1');
		$q->where('id = ' . (int) $styleId);

		$this->dbo->setQuery($q);
		$this->dbo->{$this->queryMethod}();

		$this->setResponse(
			array(
					'styleId' => $styleId
			)
		);
	}

	/**
	 * Migrate all settings from free edition to upgraded edition
	 *
	 * @return  void
	 */
	public function migrateAction ()
	{
		$from	= $this->request->getInt('from');
		$to		= $this->request->getInt('to');

		if ($from == $to)
		{
			return $this->setResponse('OK');
		}

		if ($from <= 0 OR $to <= 0)
		{
			throw new Exception("Invalid style ID");
		}

		$q = $this->dbo->getQuery(true);

		$q->select('COUNT(*)');
		$q->from('#__template_styles');
		$q->where('id IN (' . (int) $from . ', ' . (int) $to . ')');

		$this->dbo->setQuery($q);

		$count = intval($this->dbo->loadResult());

		if ($count != 2)
		{
			throw new Exception("Invalid style ID");
		}

		$q = $this->dbo->getQuery(true);

		$q->select('params');
		$q->from('#__template_styles');
		$q->where('id = ' . (int) $from);

		$this->dbo->setQuery($q);

		$fromParams = $this->dbo->loadResult();

		$q = $this->dbo->getQuery(true);

		$q->update('#__template_styles');
		$q->set('params = ' . $q->quote($fromParams));
		$q->where('id = ' . (int) $to);

		$this->dbo->setQuery($q);
		$this->dbo->{$this->queryMethod}();

		$this->setResponse('OK');
	}
}
