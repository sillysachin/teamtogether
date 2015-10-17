<?php
/**
 * @version    $Id$
 * @package    JSN_Poweradmin
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JSNPowerAdminBarSupportBase extends JSNPowerAdminBarPreviewAdapter
{
	/**
	 * Preview links mapping
	 * @var array
	 */
	private $maps = array();

	/**
	 * (non-PHPdoc)
	 * @see JSNPowerAdminBarPreviewAdapter::getPreviewLink()
	 */
	public function getPreviewLink ()
	{
		$matchedMap = null;

		foreach ($this->maps as $map) {
			$params = array();
			parse_str($map['params'], $params);

			$isMatched = true;
			foreach ($params as $key => $value) {
				if (!isset($this->params[$key]) || $this->params[$key] != $value) {
					$isMatched = false;
					break;
				}
			}

			if ($isMatched) {
				$matchedMap = $map;
				break;
			}
		}

		if ($matchedMap != null) {
			$link = $matchedMap['link'];
			$_linkParts = array();
			if(preg_match_all('/{@*([^\}]+)}/i', $link, $_linkParts)){
				$found 		= $_linkParts[0];
				$replaced 	= $_linkParts[1];
				foreach ($found as $k=>$value){
					$link = str_replace($value, @$this->params[$replaced[$k]], $link);
				}
			}

			if (strpos($link, 'option=') === false)
				$link = 'option='.$this->option.'&'.$link;

			return sprintf('index.php?%s', $link);
		}

		return parent::getPreviewLink();
	}

	public function parseXml ($xmlFile)
	{
		if (!is_file($xmlFile))
			return;

		$xml = simplexml_load_file($xmlFile);
		foreach ($xml->xpath('/preview/map') as $map) {
			$attributes = $map->attributes();
			$links 		= array();

// 			foreach ($map->link as $link) {
// 				$linkAttr = $link->attributes();
// 				$links[]  = urldecode($linkAttr['params']);
// 			}

			$this->maps[] = array(
				'params' 	=> urldecode($attributes['params']),
				'link'		=> urldecode($attributes['link'])
			);
		}
	}
}