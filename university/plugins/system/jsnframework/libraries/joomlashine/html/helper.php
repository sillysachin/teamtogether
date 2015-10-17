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
 * HTML helper class.
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNHtmlHelper
{
	/**
	 * To valid W3C types.
	 *
	 * @param   string  &$tagName  Tag name
	 * @param   array   &$attrs    Attributes
	 *
	 * @return  void
	 */
	public static function W3CValid(&$tagName, &$attrs)
	{
		$tagName = strtolower(trim($tagName));

		switch ($tagName)
		{
			case 'img':
				if ( ! array_key_exists('alt', $attrs))
				{
					$attrs += array('alt' => '');
				}
			break;

			case 'a':
				if ( ! array_key_exists('title', $attrs))
				{
					$attrs += array('title' => '');
				}
			break;

			case 'link':
				if ( ! array_key_exists('rel', $attrs))
				{
					$attrs += array('rel' => 'stylesheet');
				}
			break;
		}
	}

	/**
	 * Open HTML tag and add attributes.
	 *
	 * @param   string  $tagName  Tag name
	 * @param   array   $attrs    Attributes
	 *
	 * @return  string
	 */
	public static function openTag($tagName, $attrs = array())
	{
		self::W3CValid($tagName, $attrs);

		$openTag = '<' . $tagName . ' ';

		if (count($attrs))
		{
			foreach ($attrs AS $key => $val)
			{
				$openTag .= $key . '="' . $val . '" ';
			}
		}

		return $openTag . '>';
	}

	/**
	 * Close HTML tag.
	 *
	 * @param   string  $tagName  Tag name
	 *
	 * @return  string
	 */
	public static function closeTag($tagName)
	{
		$tagName = strtolower(trim($tagName));

		return '</' . $tagName . '>';
	}

	/**
	 * Add an input tag and attributes.
	 *
	 * @param   string  $type   Input type
	 * @param   array   $attrs  Attributes
	 *
	 * @return  string
	 */
	public static function addInputTag($type, $attrs = array())
	{
		$tagName = 'input';

		self::W3CValid($tagName, $attrs);

		$inputTag = '<' . $tagName . ' type="' . $type . '" ';

		if (count($attrs))
		{
			foreach ($attrs AS $key => $val)
			{
				$inputTag .= $key . '="' . $val . '" ';
			}
		}

		return $inputTag . ' />';
	}

	/**
	 * Add an single HTML tag. <br />, <hr />,
	 *
	 * @param   string  $tagName  Tag name
	 * @param   array   $attrs    Attributes
	 *
	 * @return  string
	 */
	public static function addSingleTag($tagName, $attrs)
	{
		self::W3CValid($tagName, $attrs);

		$singleTag = '<' . $tagName . ' ';

		if (count($attrs))
		{
			foreach ($attrs AS $key => $val)
			{
				$singleTag .= $key . '="' . $val . '" ';
			}
		}

		return $singleTag . '/>';
	}

	/**
	 * Make an html select dropdown list
	 *
	 * @param   string  $items  Items for dropdown list generation.
	 * @param   array   $attrs  Attributes
	 *
	 * @return  void
	 */
	public static function makeDropDownList($items, $attrs = array())
	{
		$HTML  = self::openTag('select', $attrs);

		for ($i = 0; $i < count($items); $i++)
		{
			$HTML .= self::openTag('option', array('value' => $items[$i]->value)) . $items[$i]->text . self::closeTag('option');
		}

		$HTML .= self::closeTag('select');

		return $HTML;
	}

   /**
	 * Return javascript tag.
	 *
	 * @param   string  $base_url  The base url.
	 * @param   string  $filename  Script file name.
	 * @param   string  $code      Javascript code.
	 *
	 * @return  string
	 */
	public static function addCustomScript($base_url = '', $filename = '', $code = '')
	{
		$tagName = 'script';

		if ($code)
		{
			return self::openTag($tagName, array('type' => 'text/javascript')) . $code . self::closeTag($tagName);
		}
		else
		{
			return self::openTag($tagName, array('src' => $base_url . $filename, 'type' => 'text/javascript')) . self::closeTag($tagName);
		}
	}

	/**
	 * Return style tag and add css file to your page.
	 *
	 * @param   string  $base_url  The base url.
	 * @param   string  $filename  Stylesheet file name.
	 * @param   string  $code      CSS code.
	 *
	 * @return  string
	 */
	public static function addCustomStyle($base_url = '', $filename = '', $code = '')
	{
		if ($code)
		{
			$tagName = 'style';

			return self::openTag($tagName, array('type' => 'text/css')) . $code . self::closeTag($tagName);
		}
		else
		{
			$tagName = 'link';

			return self::addSingleTag($tagName, array('href' => $base_url . $filename, 'type' => 'text/css', 'rel' => 'stylesheet'));
		}
	}
}
