<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTplFramework
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
 * JSN Lightcart API
 *
 * @package     JSNTplFramework
 * @subpackage  Template
 * @since       1.0.0
 */
abstract class JSNTplApiLightcart
{
	/**
	 * Retrieve all product editions
	 *
	 * @param   string  $category  Category of the product
	 * @param   string  $id        Identified name of the product
	 *
	 * @return  array
	 */
	public static function getProductDetails($category, $id)
	{
		try
		{
			$response = JSNTplHttpRequest::get(JSN_TPLFRAMEWORK_VERSIONING_URL . "?category={$category}");
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Decoding content
		$responseContent	= trim($response['body']);
		$responseObject		= json_decode($responseContent);

		if ($responseObject == null)
		{
			throw new Exception($responseContent);
		}

		$productDetails = null;

		// Loop to each item to find product details
		foreach ($responseObject->items as $item)
		{
			if ($item->identified_name == $id)
			{
				$productDetails = $item;
				break;
			}
		}

		if (empty($productDetails))
		{
			throw new Exception(JText::_('JSN_TPLFW_INVALID_PRODUCT_ID'));
		}

		return $productDetails;
	}

	/**
	 * Retrieve all editions of the product that have bought by customer
	 *
	 * @param   string  $id        Identified name of the product
	 * @param   string  $username  Customer username
	 * @param   string  $password  Customer password
	 *
	 * @return  array
	 */
	public static function getOrderedEditions($id, $username, $password)
	{
		$joomlaVersion = JSNTplHelper::getJoomlaVersion(2);

		// Send request to joomlashine server to checking customer information
		$query = array(
			'controller=remoteconnectauthentication',
			'task=authenticate',
			'tmpl=component',
			'identified_name=' . $id,
			'joomla_version=' . $joomlaVersion,
			'username=' . urlencode($username),
			'password=' . urlencode($password),
			'upgrade=no',
			'custom=1',
			'language=' . JFactory::getLanguage()->getTag()
		);

		$link = JSN_TPLFRAMEWORK_LIGHTCART_URL . '&' . implode('&', $query);

		try
		{
			$response = JSNTplHttpRequest::get($link, '', true);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Retrieve response content
		$responseContent	= trim($response['body']);
		$responseObject		= json_decode($responseContent);

		if ($responseObject === null)
		{
			throw new Exception($responseContent);
		}

		return $responseObject->editions;
	}

	/**
	 * Download product installation package from lightcart.
	 * Return path to downloaded package when download successfull
	 *
	 * @param   string  $id        Identified name of the product
	 * @param   string  $edition   Product edition to download
	 * @param   string  $username  Customer username
	 * @param   string  $password  Customer password
	 * @param   string  $savePath  Path to save downloaded package
	 *
	 * @return  string
	 */
	public static function downloadPackage($id, $edition = null, $username = null, $password = null, $savePath = null)
	{
		$joomlaVersion = JSNTplHelper::getJoomlaVersion(2);

		// Send request to joomlashine server to checking customer information
		$query = array(
			'controller=remoteconnectauthentication',
			'task=authenticate',
			'tmpl=component',
			'identified_name=' . $id,
			'joomla_version=' . $joomlaVersion,
			'upgrade=yes',
			'custom=1',
			'language=' . JFactory::getLanguage()->getTag()
		);

		if (!empty($edition))
		{
			$query[] = 'edition=' . $edition;
		}

		if (!empty($username) && !empty($password))
		{
			$query[] = 'username=' . urlencode($username);
			$query[] = 'password=' . urlencode($password);
		}

		$config			= JFactory::getConfig();
		$tmpPath		= empty($savePath) && !is_dir($savePath) ? $config->get('tmp_path') : $savePath;
		$downloadUrl	= JSN_TPLFRAMEWORK_LIGHTCART_URL . '&' . implode('&', $query);
		$filePath		= $tmpPath . '/jsn-' . $id . '.zip';

		try
		{
			JSNTplHttpRequest::get(
				$downloadUrl,
				$filePath,
				array(
					'content-type' => array(
						'application/zip',
						'application/x-zip',
						'application/x-zip-compressed',
						'application/octet-stream',
						'application/x-compress',
						'application/x-compressed',
						'multipart/x-zip'
					)
				)
			);
		}
		catch (Exception $e)
		{
			// Check if we have LightCart error code?
			if (strlen($e->getMessage() == 5 AND preg_match('/^ERR[0-9]+$/', $e->getMessage())))
			{
				throw new Exception(JText::_('JSN_TPLFW_LIGHTCART_ERROR_' . $e->getMessage()));
			}
			else
			{
				throw $e;
			}
		}

		return $filePath;
	}
}
