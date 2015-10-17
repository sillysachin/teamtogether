<?php
/**
 * @version     $Id$
 * @package     JSNTplFramework
 * @subpackage  Http
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla file library
jimport('joomla.filesystem.file');

// Import HTTP client library
class_exists('http_class') OR require_once JSN_PATH_TPLFRAMEWORK . '/libraries/3rd-party/httpclient/http.php';

/**
 * Http Client
 *
 * @package     JSNTplFramework
 * @subpackage  Http
 * @since       1.0.0
 */
abstract class JSNTplHttpRequest
{
	/**
	 * Fetch a remote URI then return results.
	 *
	 * If this method is triggered without the second parameter, <b>$target</b>, then
	 * result will be return in the following format:
	 *
	 * <pre>array(
	 *     'header' => array(
	 *         'header_1' => 'header_value_1',
	 *         'header_2' => 'header_value_2',
	 *         etc...
	 *     ),
	 *     'body' => 'fetched response body'
	 * )</pre>
	 *
	 * Otherwise, the fetched response body will be saved to the local file specified
	 * by the variable <b>$target</b>. The example below will download the remote image
	 * <b>http://placehold.it/300x200.gif</b> then save to the local file
	 * <b>/tmp/downloaded_image.gif</b>:
	 *
	 * <pre>JSNUtilsHttp::get(
	 *     'http://placehold.it/300x200.gif',
	 *     '/tmp/downloaded_image.gif'
	 * );</pre>
	 *
	 * When the second parameter is set in method call, the method will always return
	 * the boolean value <b>true</b> if file is successfully saved or <b>false</b>
	 * if file is not saved.
	 *
	 * @param   string   $uri             Remote URI for fetching content.
	 * @param   string   $target          Set to a file path to save fetched content as local file.
	 * @param   boolean  $validateHeader  Check for 200 OK header or not?
	 * @param   array    $options         Custom options to pass to http_class object.
	 *
	 * @return  array  array('header' => 'Associative array of fetched header', 'body' => 'Fetched content')
	 */
	public static function get($uri, $target = '', $validateHeader = true, $options = array())
	{
		// Preset return result
		$result = array();

		// Initialize HTTP client
		$http = new http_class;

		$http->follow_redirect		= 1;
		$http->redirection_limit	= 5;

		$http->GetRequestArguments($uri, $arguments);

		// Set custom options
		if (is_array($options) AND count($options))
		{
			foreach ($options AS $k => $v)
			{
				$arguments[$k] = $v;
			}
		}

		// Open connection
		if (($error = $http->Open($arguments)) == '')
		{
			if (($error = $http->SendRequest($arguments)) == '')
			{
				// Get response header
				$header = array();

				if (($error = $http->ReadReplyHeaders($header)) != '')
				{
					throw new Exception(JText::sprintf('JSN_TPLFW_HTTP_CONNECTION_ERROR', $error));
				}

				$result['header'] = $header;

				// Validate header
				if ($validateHeader)
				{
					foreach ($result['header'] AS $header => $value)
					{
						if (strtolower(substr($header, 0, 5)) == 'http/' AND strpos($header, '200') === false)
						{
							throw new Exception(JText::sprintf('JSN_TPLFW_HTTP_CONNECTION_ERROR', substr($header, strpos($header, ' '))));
						}
					}
				}

				// Get response body
				$result['body'] = '';

				while (true)
				{
					if (($error = $http->ReadReplyBody($body, 1000)) != '' OR strlen($body) == 0)
					{
						break;
					}

					$result['body'] .= $body;
				}

				// Validate header
				if (is_array($validateHeader))
				{
					foreach ($validateHeader AS $k => $v)
					{
						foreach ($result['header'] AS $header => $value)
						{
							if (strcasecmp($header, $k) == 0)
							{
								is_array($v) OR $v = array($v);

								if ( ! in_array($value, $v))
								{
									throw new Exception($result['body']);
								}
							}
						}
					}
				}
			}
			else
			{
				throw new Exception(JText::sprintf('JSN_TPLFW_HTTP_CONNECTION_ERROR', $error));
			}

			// Close connection
			$http->Close();
		}
		else
		{
			throw new Exception(JText::sprintf('JSN_TPLFW_HTTP_CONNECTION_ERROR', $error));
		}

		// Write to local file if target is given
		empty($target) OR JFile::write($target, $result['body']);

		return $result;
	}

	/**
	 * Convert either an object or associative array to query string.
	 *
	 * @param   mixed   $obj  Either an object or associative array.
	 * @param   string  $ns   A name-space to apply to variable name.
	 *
	 * @return  string
	 */
	public static function toQueryString($obj, $ns = '')
	{
		$qs = array();

		foreach ((array) $obj AS $k => $v)
		{
			if (is_object($v) OR is_array($v))
			{
				$qs[] = self::toQueryString($v, empty($ns) ? $k : "{$ns}[{$k}]");
			}
			else
			{
				// Replace special characters
				$v = urlencode($v);

				$qs[] = empty($ns) ? "{$k}={$v}" : "{$ns}[{$k}]={$v}";
			}
		}

		return implode('&', $qs);
	}
}
