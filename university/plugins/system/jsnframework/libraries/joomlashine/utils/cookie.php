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
 * Helper class for working with cookie.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUtilsCookie
{
	/**
	 * Read cookie value.
	 *
	 * @param   string  $name       Cookie name.
	 * @param   mixed   $default    Default value if cookie not set (optional).
	 * @param   string  $component  Component name (optional).
	 *
	 * @return  mixed
	 */
	public static function get($name, $default = null, $component = '')
	{
		// Initialize component
		! empty($component) OR $component = JFactory::getApplication()->input->getCmd('option');
		$component = preg_replace('/^com_/i', '', $component);

		// Generate real cookie name
		$name = "jsn_{$component}_{$name}";

		// Get cookie value
		$value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;

		// Decode value if it is a JSON encoded string
		if (substr($value, 0, 1) == '{' AND substr($value, -1) == '}')
		{
			$value = json_decode($value);
		}

		return $value;
	}

	/**
	 * Create/update cookie.
	 *
	 * When creating/updating cookie, you can set options as following:
	 *
	 * <code>JSNUtilsCookie::set(
	 *     'test-cookie',
	 *     'some-value',
	 *     array(
	 *         'expire' => time() + 60*60*24*30, // Set the cookie to expire in 30 days
	 *         'path' => '/administrator/', // The cookie will only be available within the /administrator/ directory and all sub-directories
	 *         'domain' => 'domain.tld', // The domain that the cookie is available to
	 *         'secure' => false, // When set to TRUE, the cookie will only be set if a secure connection exists
	 *         'httponly' => false // When TRUE the cookie will be made accessible only through the HTTP protocol.
	 *                             // This means that the cookie won't be accessible by scripting languages, such as JavaScript.
	 *     )
	 * );</code>
	 *
	 * In most case, you can simple call this method as:
	 *
	 * <code>JSNUtilsCookie::set('test-cookie', 'some-value');</code>
	 *
	 * to set a cookie that will expire at the end of the session (when the browser closes).
	 *
	 * @param   string  $name       Cookie name.
	 * @param   mixed   $value      Cookie value.
	 * @param   array   $options    Options for creating/updating cookie.
	 * @param   string  $component  Component name (optional).
	 *
	 * @return  void
	 */
	public static function set($name, $value, $options = array(), $component = '')
	{
		// Initialize component
		! empty($component) OR $component = JFactory::getApplication()->input->getCmd('option');
		$component = preg_replace('/^com_/i', '', $component);

		// Generate real cookie name
		$name = "jsn_{$component}_{$name}";

		// Encode value if it is either an array or object
		if (is_array($value) OR is_object($value))
		{
			$value = json_encode($value);
		}

		// Create/update cookie
		setcookie(
			$name,
			$value,
			isset($options['expire'])	? $options['expire']	: 0,
			isset($options['path'])		? $options['path']		: '/' . trim(JURI::root(true), '/') . '/',
			isset($options['domain'])	? $options['domain']	: '',
			isset($options['secure'])	? $options['secure']	: '',
			isset($options['httponly'])	? $options['httponly']	: ''
		);
	}

	/**
	 * Remove cookie.
	 *
	 * @param   string  $name       Cookie name.
	 * @param   string  $component  Component name (optional).
	 *
	 * @return  void
	 */
	public static function delete($name, $component = '')
	{
		// Initialize component
		! empty($component) OR $component = JFactory::getApplication()->input->getCmd('option');
		$component = preg_replace('/^com_/i', '', $component);

		// Generate real cookie name
		$name = "jsn_{$component}_{$name}";

		// Remove cookie by setting expire to one hour ago
		self::set(
			$name,
			'',
			array(
				'expire' => (time() - 3600)
			),
			$component
		);
	}

	/**
	 * Clean all cookies set by a component.
	 *
	 * @param   string  $component  Component name (optional).
	 *
	 * @return  void
	 */
	public static function clean($component = '')
	{
		// Initialize component
		! empty($component) OR $component = JFactory::getApplication()->input->getCmd('option');
		$component = preg_replace('/^com_/i', '', $component);

		// Get all cookies set by component
		foreach ($_COOKIE AS $k => $v)
		{
			if (strpos($k, "jsn_{$component}_") === 0)
			{
				// Remove cookie
				self::remove(str_replace("jsn_{$component}_", '', $k), $component);
			}
		}
	}
}
