<?php
/**
 * @version     $Id$
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JSNLayoutHelper{
	/**
	 * 
	 * Fix links
	 * 
	 * @param String $contents
	 */
	public static function fixImageLinks( &$contents )
	{
		$regex = '/src=(["\'])(.*?)\1/';
		$count = preg_match_all($regex, $contents, $match);
		if ($count > 0)
		{
			$changes = $match[2];
			foreach($changes as $change)
			{
				$uri = new JURI($change);
				if ($uri->getHost() == '') {
					$uri = new JURI(JURI::root().$change);
				}
				$contents = str_replace('src="'.$change.'"', 'src="'.$uri->toString().'"', $contents);
			}
		}
		
		return $contents;
	}
	/**
	 * 
	 * Add image tag to 
	 * 
	 * @param String $src
	 * @param String $attrs
	 */
	public static function showImage($src, $attrs = "")
	{
		$uri = new JURI($src);
		return '<img src="'.JURI::root().$uri->toString().'" '.$attrs.' />';
	}
	/**
	 * 
	 * Show rate box 
	 * 
	 * @param Number $rating
	 * @param Number $ratingCount
	 */
	public static function showArticleRating( $rating, $ratingCount )
	{
		JFactory::getLanguage()->load('plg_content_vote');
		$rating = intval( $rating );
		$rating_count = intval( $ratingCount );

		$view = JRequest::getString('view', '');
		$img = '';

		// look for images in template if available
		$starImageOn  = JHtml::_('image','system/rating_star.png', NULL, NULL, true);
		$starImageOff = JHtml::_('image','system/rating_star_blank.png', NULL, NULL, true);

		for ($i=0; $i < $rating; $i++) {
			$img .= $starImageOn;
		}
		for ($i=$rating; $i < 5; $i++) {
			$img .= $starImageOff;
		}
		$html  = '<span class="content_rating">';
		$html .= JText::sprintf( 'PLG_VOTE_USER_RATING', $img, $rating_count );
		$html .= "</span>\n<br />\n";
		return $html;
	}
	
	/**
	 * @since	1.6
	 */
	public static function showNavigation($row, $params)
	{
		$html   = '';
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();
		$lang	= JFactory::getLanguage();
		$nullDate = $db->getNullDate();

		$date	= JFactory::getDate();
		$config	= JFactory::getConfig();
		

		$uid	= $row->id;
		$option	= 'com_content';
		$canPublish = $user->authorise('core.edit.state', $option.'.article.'.$row->id);

		// The following is needed as different menu items types utilise a different param to control ordering.
		// For Blogs the `orderby_sec` param is the order controlling param.
		// For Table and List views it is the `orderby` param.
		$params_list = $params->toArray();
		if (array_key_exists('orderby_sec', $params_list)) {
			$order_method = $params->get('orderby_sec', '');
		} else {
			$order_method = $params->get('orderby', '');
		}
		// Additional check for invalid sort ordering.
		if ($order_method == 'front') {
			$order_method = '';
		}

		// Determine sort order.
		switch ($order_method) {
			case 'date' :
				$orderby = 'a.created';
				break;
			case 'rdate' :
				$orderby = 'a.created DESC';
				break;
			case 'alpha' :
				$orderby = 'a.title';
				break;
			case 'ralpha' :
				$orderby = 'a.title DESC';
				break;
			case 'hits' :
				$orderby = 'a.hits';
				break;
			case 'rhits' :
				$orderby = 'a.hits DESC';
				break;
			case 'order' :
				$orderby = 'a.ordering';
				break;
			case 'author' :
				$orderby = 'a.created_by_alias, u.name';
				break;
			case 'rauthor' :
				$orderby = 'a.created_by_alias DESC, u.name DESC';
				break;
			case 'front' :
				$orderby = 'f.ordering';
				break;
			default :
				$orderby = 'a.ordering';
				break;
		}

		$xwhere = ' AND (a.state = 1 OR a.state = -1)' .
		' AND (publish_up = '.$db->Quote($nullDate).' OR publish_up <= '.$db->Quote($date).')' .
		' AND (publish_down = '.$db->Quote($nullDate).' OR publish_down >= '.$db->Quote($date).')';

		// Array of articles in same category correctly ordered.
		$query	= $db->getQuery(true);
		$query->select('a.id, '
				.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, '
				.'CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__content AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
		$query->where('a.catid = '. (int)$row->catid .' AND a.state = '. (int)$row->state
					. ($canPublish ? '' : ' AND a.access = ' .(int)$row->access) . $xwhere);
		$query->order($orderby);
		if ($app->isSite() && $app->getLanguageFilter()) {
			$query->where('a.language in ('.$db->quote($lang->getTag()).','.$db->quote('*').')');
		}

		$db->setQuery($query);
		$list = $db->loadObjectList('id');

		// This check needed if incorrect Itemid is given resulting in an incorrect result.
		if (!is_array($list)) {
			$list = array();
		}

		reset($list);

		// Location of current content item in array list.
		$location = array_search($uid, array_keys($list));

		$rows = array_values($list);

		$row->prev = null;
		$row->next = null;

		if ($location -1 >= 0)	{
			// The previous content item cannot be in the array position -1.
			$row->prev = $rows[$location -1];
		}

		if (($location +1) < count($rows)) {
			// The next content item cannot be in an array position greater than the number of array postions.
			$row->next = $rows[$location +1];
		}

		$pnSpace = "";
		if (JText::_('JGLOBAL_LT',true) || JText::_('JGLOBAL_GT',true)) {
			$pnSpace = " ";
		}

		// Output.
		if ($row->prev || $row->next) {
			$html = '
			<ul class="pagenav">'
			;
			if ($row->prev) {
				$html .= '
				<li class="pagenav-prev">
					<a rel="next">'
						. JText::_('JGLOBAL_LT',true) . $pnSpace . JText::_('JPREV',true) . '</a>
				</li>'
				;
			}
			if ($row->next) {
				$html .= '
				<li class="pagenav-next">
					<a rel="prev">'
						. JText::_('JNEXT',true) . $pnSpace . JText::_('JGLOBAL_GT',true) .'</a>
				</li>'
				;
			}
			$html .= '
			</ul>'
			;

			return $html;
		}
		return '<ul class="pagenav">pagenav</ul>';
	}
}