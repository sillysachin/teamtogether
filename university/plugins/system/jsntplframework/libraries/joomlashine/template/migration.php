<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPL
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
 * Template parameters migration from v1 to v2.
 *
 * @package     JSNTPL
 * @subpackage  Migration
 * @since       2.0.6
 */
class JSNTplTemplateMigration
{
	/**
	 * Method to migrate v1 parameter to v2.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  mixed  Migrated value.
	 */
	public static function migrate(&$params)
	{
		foreach (get_class_methods(__CLASS__) AS $method)
		{
			if ($method != __FUNCTION__ AND substr($method, 0, strlen(__FUNCTION__)) == __FUNCTION__)
			{
				call_user_func_array(array(__CLASS__, $method), array(&$params));
			}
		}

		return $params;
	}

	/**
	 * Migrate layoutWidth parameter.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  void
	 */
	public static function migrateLayoutWidth(&$params)
	{
		if (isset($params['layoutWidth']))
		{
			if (is_array($params['layoutWidth']) AND isset($params['layoutWidth']['type']) AND isset($params['layoutWidth'][$params['layoutWidth']['type']]))
			{
				$params['templateWidth'] = $params['layoutWidth'];
			}
			else
			{
				$value = array('type' => $params['layoutWidth'] == 'float' ? 'float' : 'fixed');

				! isset($params['layoutNarrowWidth']) OR $value['fixed'] = $params['layoutNarrowWidth'];
				! isset($params['layoutFloatWidth']) OR $value['float'] = $params['layoutFloatWidth'];

				if ($params['layoutWidth'] == 'wide')
				{
					$value['type'] = 'responsive';
					$value['responsive'] = array('wide');
				}

				$params['templateWidth'] = $value;
			}
		}
	}

	/**
	 * Migrate column width parameters.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  void
	 */
	public static function migrateColumnWidth(&$params)
	{
		// Prepare v2 parameters
		foreach (array('promoColumns', 'mainColumns', 'contentColumns') AS $row)
		{
			foreach ($params[$row] AS $col => $value)
			{
				$name = preg_replace('/^\d+:/', '', $col);

				$width[$row][$name] = intval(str_replace('span', '', $value));
				$order[$row][$name] = substr($col, 0, strpos($col, ':'));
				$order[$row][$name] = empty($order[$row][$name]) ? '' : $order[$row][$name] . ':';
			}
		}

		// Migrate v1 parameters
		foreach (array('columnPromoLeft', 'columnPromoRight', 'columnLeft', 'columnRight', 'columnInnerleft', 'columnInnerright') AS $col)
		{
			if (isset($params[$col]) AND $params[$col])
			{
				// Determine new row name
				$row = (strpos($col, 'Promo') !== false ? 'promo' : (strpos($col, 'Inner') !== false ? 'content' : 'main')) . 'Columns';

				// Determine new column name
				$name = strtolower(trim(substr($row == 'promoColumns' ? preg_replace('/([A-Z])/', '-\\1', $col) : $col, 6), '-'));

				// Determine related column
				$relation = $row == 'promoColumns' ? 'promo' : ($row == 'mainColumns' ? 'content' : 'component');

				// Migrate percentage based width to span based width
				$span = round($params[$col] / (100 / 12));
				$curr = $width[$row][$name];

				if ($span != $curr)
				{
					$width[$row][$name] = $span;
					$width[$row][$relation] = $width[$row][$relation] + ($curr - $span);

					$params[$row][$order[$row][$name] . $name] = "span{$width[$row][$name]}";
					$params[$row][$order[$row][$relation] . $relation] = "span{$width[$row][$relation]}";
				}
			}
		}
	}

	/**
	 * Migrate mobileSupport parameter.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  void
	 */
	public static function migrateMobileSupport(&$params)
	{
		// Check if mobile support is enabled
		if (isset($params['mobileSupport']))
		{
			if ( ! isset($params['templateWidth']['responsive']))
			{
				$params['templateWidth']['responsive'] = array();
			}

			if ($params['mobileSupport'] AND ! in_array('mobile', $params['templateWidth']['responsive']))
			{
				$params['templateWidth']['responsive'][] = 'mobile';
			}
			elseif ( ! $params['mobileSupport'] AND in_array('mobile', $params['templateWidth']['responsive']))
			{
				$params['templateWidth']['responsive'] = array_flip($params['templateWidth']['responsive']);

				unset($params['templateWidth']['responsive']['mobile']);

				$params['templateWidth']['responsive'] = array_keys($params['templateWidth']['responsive']);
			}
		}
	}

	/**
	 * Migrate menuSticky parameter.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  void
	 */
	public static function migrateMenuSticky(&$params)
	{
		if (isset($params['menuSticky']) AND ! is_array($params['menuSticky']))
		{
			$params['menuSticky'] = array('mobile' => $params['menuSticky'] ? true : false);
		}
	}

	/**
	 * Migrate templateStyle parameter.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  void
	 */
	public static function migrateTemplateStyle(&$params)
	{
		if (isset($params['templateStyle']) AND ! is_array($params['templateStyle']))
		{
			$params['fontStyle'] = array('style' => $params['templateStyle']);
		}
	}

	/**
	 * Migrate site tools parameters.
	 *
	 * @param   array   &$params   Template parameters array.
	 *
	 * @return  void
	 */
	public static function migrateSiteTools(&$params)
	{
		if (isset($params['colorSelector']) AND ! $params['colorSelector'])
		{
			$params['sitetoolStyle'] = '';
		}
	}
}
