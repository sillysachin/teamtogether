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

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Helper class for JSN Update implementation.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUpdateHelper
{
	/**
	 * Parsed check update URL.
	 *
	 * @var	array
	 */
	protected static $versions;

	/**
	 * Communicate with JoomlaShine server for latest product version.
	 *
	 * If a product does not have sub-product, the <b>$products</b> parameter
	 * does not required when calling this method:
	 *
	 * <pre>JSNUpdateHelper::check();</pre>
	 *
	 * Result will be returned in the following format:
	 *
	 * <pre>If product update is available:
	 * <code>array(
	 *     'identified_name' => object(
	 *         'name' => 'The product name',
	 *         'identified_name' => 'The product identification',
	 *         'description' => 'The product description',
	 *         'version' => 'The latest product version',
	 *         'authentication' => 'Indicates whether authentication is required when updating product'
	 *     )
	 * )</code>
	 * If the product does not have update:
	 * <code>array(
	 *     'identified_name' => false
	 * )</code></pre>
	 *
	 * If a product has sub-product, this method need to be called as below to
	 * check all sub-product for latest version:
	 *
	 * <pre>JSNUpdateHelper::check(
	 *     array(
	 *         // Core component
	 *         'imageshow' => '4.2.0',
	 *
	 *         // Themes
	 *         'themeclassic' => '1.1.5',
	 *         'themeslider'  => '1.0.4',
	 *         'themegrid'    => '1.0.0',
	 *
	 *         // Sources
	 *         'picasa'      => '1.1.2',
	 *         'flickr'      => '1.1.2',
	 *         'phoca'       => '1.0.1',
	 *         'joomgallery' => '1.0.1',
	 *         'rsgallery2'  => '1.0.1',
	 *         'facebook'    => '1.0.1'
	 *     )
	 * );</pre>
	 *
	 * In this case, the returned result might look like below:
	 *
	 * <pre>array(
	 *     // Core component
	 *     'imageshow' => object(
	 *         'name' => 'JSN ImageShow',
	 *         'identified_name' => 'imageshow',
	 *         'description' => 'Something about JSN ImageShow',
	 *         'version' => '4.3.0',
	 *         'editions' => array(
	 *             0 => object(
	 *                 'edition' => 'PRO STANDARD',
	 *                 'authentication' => 1
	 *             ),
	 *             1 => object(
	 *                 'edition' => 'PRO UNLIMITED',
	 *                 'authentication' => 1
	 *             ),
	 *             2 => object(
	 *                 'edition' => 'FREE',
	 *                 'authentication' => 0
	 *             )
	 *         )
	 *     ),
	 *
	 *     // Themes
	 *     'themeclassic' => false, // Product update not available
	 *     'themeslider' => false,  // Product update not available
	 *     'themegrid' => object(
	 *         'name' => 'Theme Grid',
	 *         'identified_name' => 'themegrid',
	 *         'description' => 'JSN ImageShow Theme Grid plugin',
	 *         'version' => '1.0.1',
	 *         'edition' => 'FREE',
	 *         'authentication' => 0
	 *     ),
	 *
	 *     // Sources
	 *     'picasa' => false,      // Product update not available
	 *     'flickr' => false,      // Product update not available
	 *     'phoca' => false,       // Product update not available
	 *     'joomgallery' => false, // Product update not available
	 *     'rsgallery2' => false,  // Product update not available
	 *     'facebook' => object(
	 *         'name' => 'FaceBook',
	 *         'identified_name' => 'facebook',
	 *         'description' => 'JSN ImageShow Image Source Facebook plugin',
	 *         'version' => '1.0.2',
	 *         'edition' => 'FREE',
	 *         'authentication' => 0
	 *     )
	 * )</pre>
	 *
	 * @param   array   $products               Array of identified name for checking latest version.
	 * @param   string  $requiredJoomlaVersion  Joomla version required by extension, e.g. 2.5, 3.0, etc.
	 * @param   array   $latestUpdates          Latest update response (for use in self recursive call only).
	 * @param   array   $results                Check update results (for use in self recursive call only).
	 *
	 * @return  mixed
	 */
	public static function check($products = array(), $requiredJoomlaVersion = JSN_FRAMEWORK_REQUIRED_JOOMLA_VER, $latestUpdates = '', $results = '')
	{
		// Only communicate with server if check update URLs is not load before
		if (empty($latestUpdates))
		{
			if ( ! isset(self::$versions))
			{
				try
				{
					// Get Joomla config and input object
					$config	= JFactory::getConfig();
					$input	= JFactory::getApplication()->input;

					// Generate cache file path
					$cache = $config->get('tmp_path') . '/JoomlaShineUpdates.json';

					// Get current option and view
					$option	= $input->getCmd('option');
					$view	= $input->getCmd('view');

					// Get latest version from local file if not in about page or cache is not timed out
					if (( ! in_array($option, JSNVersion::$products) OR $view != 'about') AND is_readable($cache) AND time() - filemtime($cache) < CHECK_UPDATE_PERIOD)
					{
						// Decode JSON encoded update details
						self::$versions = json_decode(JFile::read($cache));
					}
					else
					{
						// Always update cache file modification time
						is_writable($cache) AND touch($cache, time());

						// Communicate with JoomlaShine server via latest version checking URL
						try
						{
							self::$versions = JSNUtilsHttp::get(JSN_EXT_VERSION_CHECK_URL);
							self::$versions = isset(self::$versions['body']) ? self::$versions['body'] : '{"items":[]}';

							// Cache latest version to local file system
							JFile::write($cache, self::$versions);

							// Decode JSON encoded update details
							self::$versions = json_decode(self::$versions);
						}
						catch (Exception $e)
						{
							throw $e;
						}
					}
				}
				catch (Exception $e)
				{
					throw new Exception(JText::_('JSN_EXTFW_VERSION_CHECK_FAIL'));
				}
			}

			$latestUpdates = self::$versions;
		}

		// Prepare product identification
		if ( ! is_array($products) OR ! count($products))
		{
			is_array($products) OR $products = array();

			// Get the product info
			$version = JSNUtilsText::getConstant('VERSION');

			// Is identified name defined?
			if ($const = JSNUtilsText::getConstant('IDENTIFIED_NAME'))
			{
				$products[$const] = $version;
			}
			// Generate product identified name
			else
			{
				$component = substr(JFactory::getApplication()->input->getCmd('option'), 4);
				$products[$component] = $version;
				$products['ext_' . $component] = $version;
			}
		}

		// Get Joomla version
		$joomlaVersion = new JVersion;

		// Preset return results
		is_array($results) OR $results = array();

		// Get the latest product version
		foreach ($products AS $product => $current)
		{
			if ( ! isset($results[$product]))
			{
				foreach ($latestUpdates->items AS $item)
				{
					if (isset($item->items))
					{
						$results = self::check(array($product => $current), $requiredJoomlaVersion, $item, $results);
						continue;
					}

					if (isset($item->identified_name) AND $item->identified_name == $product)
					{
						$results[$product] = $item;
						break;
					}
				}

				// Does latest product info found?
				if (isset($results[$product]) AND is_object($results[$product]))
				{
					// Does product support installed Joomla version?
					$tags = explode(';', $results[$product]->tags);

					if ( ! in_array($joomlaVersion->RELEASE, $tags))
					{
						$results[$product] = false;
					}

					// Does product upgradable?
					if ($results[$product] AND ! empty($requiredJoomlaVersion) AND ! JSNVersion::isJoomlaCompatible($requiredJoomlaVersion) AND ! version_compare($results[$product]->version, $current, '>='))
					{
						$results[$product] = false;
					}

					// Does product have newer version?
					if ($results[$product] AND (empty($requiredJoomlaVersion) OR JSNVersion::isJoomlaCompatible($requiredJoomlaVersion)) AND ! version_compare($results[$product]->version, $current, '>'))
					{
						$results[$product] = false;
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Render the product update page.
	 *
	 * If a product does not have sub-product, the <b>$products</b> parameter
	 * does not required when calling this method:
	 *
	 * <pre>JSNUpdateHelper::render($info);</pre>
	 *
	 * If a product has sub-product, this method need to be called similar to
	 * the example below to update all sub-product to latest version:
	 *
	 * <pre>JSNUpdateHelper::render(
	 *     $info,
	 *     array(
	 *         // Core component
	 *         'imageshow' => '4.2.0',
	 *
	 *         // Themes
	 *         'themeclassic' => '1.1.5',
	 *         'themeslider'  => '1.0.4',
	 *         'themegrid'    => '1.0.0',
	 *
	 *         // Sources
	 *         'picasa'      => '1.1.2',
	 *         'flickr'      => '1.1.2',
	 *         'phoca'       => '1.0.1',
	 *         'joomgallery' => '1.0.1',
	 *         'rsgallery2'  => '1.0.1',
	 *         'facebook'    => '1.0.1'
	 *     )
	 * );</pre>
	 *
	 * @param   object  $info              JSON decoded extension's manifest cache.
	 * @param   array   $products          Array of identified name for checking latest version.
	 * @param   string  $redirAfterFinish  Whether to redirect to another page after finish or not?
	 *
	 * @return  void
	 */
	public static function render($info, $products = array(), $redirAfterFinish = '')
	{
		require dirname(__FILE__) . '/tmpl/default.php';
	}
}
