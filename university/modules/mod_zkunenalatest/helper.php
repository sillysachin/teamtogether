<?php 
/**
 * @version		0.40
 * @package		zKunenaLatest
 * @author		Aaron Gilbert {@link http://www.nzambi.braineater.ca}
 * @author		Created on 22-Dec-2010
 * @copyright	Copyright (C) 2009 - 2012 Aaron Gilbert. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

defined('_JEXEC') or die('Restricted access');


class modZKunenaLatestHelper {
	
	function getItems($params) {
		
		require_once KPATH_SITE . '/models/common.php';
		require_once KPATH_SITE . '/models/topics.php';
		
		$model 			= new KunenaModelTopics ();
		$state 			= $model->getState();
		$functionName 	= 'getTopics';
		$items 			= array();
		
		
		$cats = $params->get ( 'categoryLimit', 0  );
		if (!is_array($cats)) $cats = explode ( ',', $cats );
		if (empty($cats) || in_array(0, $cats)) {
			$cats = false;
		}
		
		$model->setState ( 'list.limit', $params->get ( 'numberPosts', 6 ) );
		$model->setState ( 'list.categories', $cats );
		$model->setState ( 'list.categories.in', (bool)$params->get('categoryFilter', 1) );
		$model->setState ( 'list.time', $params->get ( 'topicsTime', '-1' ) );
		$model->setState ( 'layout', 'default' );
		
		if ( !in_array($params->get( 'modelType' ), array('users-latest','users-subscriptions','users-favorites','posts-mythanks','users-posted') ) )
			$model->setState ( 'user', 0 ); 
			
		switch ( $params->get( 'modelType' ) ) {
				
				case 'topics-noreplies' :
					$model->setState ( 'list.mode', 'noreplies' );
					break;
				case 'topics-replies' :
					$model->setState ( 'list.mode', 'replies' );
					break;
				case 'topics-sticky' :
					$model->setState ( 'list.mode', 'sticky' );
					break;
				case 'topics-locked' :
					$model->setState ( 'list.mode', 'locked' );
					break;
				case 'topics-unapproved' :
					$model->setState ( 'list.mode', 'unapproved' );
					break;
				case 'topics-deleted' :
					$model->setState ( 'list.mode', 'deleted' );
					break;
				// POSTS	
				case 'posts-latest' :
					$model->setState ( 'layout', 'posts' );
					$model->setState ( 'list.mode', 'recent' );
					$functionName = 'getMessages';
					break;
				case 'posts-thankyou' :
					$model->setState ( 'layout', 'posts' );
					$model->setState ( 'list.mode', 'thankyou' );
					$functionName = 'getMessages';
					break;
				case 'posts-mythanks' :
					$model->setState ( 'layout', 'posts' );
					$model->setState ( 'list.mode', 'mythanks' );
					$functionName = 'getMessages';
					break;
				case 'posts-unapproved' :
					$model->setState ( 'layout', 'posts' );
					$model->setState ( 'list.mode', 'unapproved' );
					$functionName = 'getMessages';
					break;
				case 'posts-deleted' :
					$model->setState ( 'layout', 'posts' );
					$model->setState ( 'list.mode', 'deleted' );
					// ToDo   this is wrong
					$model->setState ( 'user', 0 );
					$functionName = 'getMessages';
					break;
				//USER
				case 'users-latest' :
					$model->setState ( 'layout', 'user' );
					$model->setState ( 'list.mode', 'default' );
					break;
				case 'users-posted' :
					$model->setState ( 'layout', 'user' );
					$model->setState ( 'list.mode', 'posted' );
					break;	
				case 'users-subscriptions' :
					$model->setState ( 'layout', 'user' );
					$model->setState ( 'list.mode', 'subscriptions' );
					break;
				case 'users-favorites' :
					$model->setState ( 'layout', 'user' );
					$model->setState ( 'list.mode', 'favorites' );
					break;
				case 'topics-latest' :
				default :
					
			}	
			  		
		$items = $model-> $functionName ();
		// pre-process each post item
		if ( count($items)){
			foreach ($items as $item ) {
				
				$fullSubject	= htmlspecialchars ($item->subject); 
				$subject 		= JString::substr(htmlspecialchars ($item->subject), '0', $params->get ('subjectLength', 50) );
				$title 			= JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', htmlspecialchars ($item->subject));
				$topTitle 		= JText::sprintf('COM_KUNENA_TOPIC_FIRST_LINK_TITLE', htmlspecialchars ($item->subject));
				
				if ($functionName === 'getTopics' ) { // using a Topic Object
					$item ->last_post_Kuser = $item->getLastPostAuthor();
					$item ->last_post_KTime = $item->getLastPostTime();
					$item ->last_post_Icon 	= $item->getIcon();
					$item ->message 				= $item ->last_post_message;
					
				} else { // using a Message Object
					$item ->last_post_Kuser = $item->getAuthor();
					$item ->last_post_KTime = new KunenaDate($item->time);
					$item ->last_post_Icon 	= $item->getTopic()->getIcon();
				}
				
				$item ->last_post_Avatar = $item->last_post_Kuser->getLink( 
									$item->last_post_Kuser->getAvatarImage('', $params->get('avatarsize', 40 ), $params->get('avatarsize', 40 ) ) );
									
				$item ->last_post_Author = $item->last_post_Kuser->getLink( 
									$item->last_post_Kuser->getName() );
														
				 
				$item ->last_post_Link	= JHTML::_('kunenaforum.link', $item->getUri('', 'last'), $subject, $title, '', 'nofollow');
				$item ->last_post_FLink	= JHTML::_('kunenaforum.link', $item->getUri('', 'last'), $fullSubject, $title, '', 'nofollow');
				$item ->first_post_Link	= JHTML::_('kunenaforum.link', $item->getUri('', 'first'), JText::_('MOD_ZKUENALATEST_TOPIC'), $topTitle, '', 'nofollow');
				 
			} // for each item
		} // if items
		return $items;
		
	} //getItems
	
	
	function trimStringLong($str, $limit, $break=".", $pad="...") {
		if(strlen($str) <= $limit) return $str;
		if(false !== ($breakpoint = strpos($str, $break, $limit))) { 
			if($breakpoint < strlen($str) - 1) { 
				$str = substr($str, 0, $breakpoint) . $pad; 
			} 
		} 
		return $str; 
	}
	function trimString($string, $limit, $break=" ", $pad="...") { 
		if(strlen($string) <= $limit) return $string; 
		$string = substr($string, 0, $limit); 
		if(false !== ($breakpoint = strrpos($string, $break))) { 
			$string = substr($string, 0, $breakpoint); 
		} 
		return $string . $pad; 
	}

	
	
	function getTopic($item, $params) {
		return $item ->first_post_Link ;
	}
	function getNew($item, $params) {
		return '('.$item->unread. ' '. JText::_('MOD_ZKUENALATEST_NEW').')';
	}
	function getSubject($item, $params, $full = false ) {
		return $full ? $item ->last_post_FLink : $item ->last_post_Link ;
	}
	function getAuthor($item, $params) {
		return $item ->last_post_Author ;
	}
	function getAvatar($item, $params) {
		return $item ->last_post_Avatar ;
	}
	function getTopicIcon($item, $params) {
		return $item ->last_post_Icon ;
	}
	function getPostDate($item, $params) {
		return $item ->last_post_KTime->toKunena($params->get('timeFormat','datetime_today'));
	}
	function getCredit() {
		return JText::_('MOD_ZKUENALATEST_POWERED_BY')." <a href=\"http://nzambi.braineater.ca\" target=\"_blank\"><em>nZambi!</em></a>";
	}
	
	function getMessage($item, $params) {
		
		$user      	= &JFactory::getUser();
		// first strip BBCode
		// remove Quoted Text to save room.
		$item->message = preg_replace('#\[quote(.*?)\[/quote\]#si', '', $item->message);
		// remove Spoilers,  we don't want to spoil anything...
		$item->message = preg_replace('#\[spoiler(.*?)\[/spoiler\]#si', JText::_('MOD_ZKUNENALATEST_BBC_SPOILER'), $item->message);
		// remove code block...
		$item->message = preg_replace('#\[code(.*?)\[/code\]#si', JText::_('MOD_ZKUNENALATEST_BBC_CODE'), $item->message);
		// remove Ebay...
		$item->message = preg_replace('#\[ebay(.*?)\[/ebay\]#si', JText::_('MOD_ZKUNENALATEST_BBC_EBAY'), $item->message);																		 						 		// remove Maps,  we don't want to spoil anything...
		$item->message = preg_replace('#\[map(.*?)\[/map\]#si', JText::_('MOD_ZKUNENALATEST_BBC_MAP'), $item->message);
		// remove Video...
		$item->message = preg_replace('#\[video(.*?)\[/video\]#si', JText::_('MOD_ZKUNENALATEST_BBC_VIDEO'), $item->message);
		// remove Image links...
		$item->message = preg_replace('#\[img(.*?)\[/img\]#si', JText::_('MOD_ZKUNENALATEST_BBC_IMAGE'), $item->message);
		//Don't show hiden stuff to guests... that would be rude...
		if ($user->guest){
			$item->message = preg_replace('#\[hide(.*?)\[/hide\]#si', JText::_('MOD_ZKUNENALATEST_BBC_HIDDEN') , $item->message);
		}
		// Strip the rest of the bbCodes	
		$item->message = preg_replace('|[[\/\!]*?[^\[\]]*?]|si', '', $item->message);
		
		return modZKunenaLatestHelper::trimString($item->message, $params->get ( 'messageTrim', 150 ), " ");
		//return  JString::substr( htmlspecialchars ( KunenaParser::stripBBCode($item->message) ), '0', $params->get ( 'messageTrim', 150 ) );
	}
	
	function getExtraCss($params, $moduleId ) {
		$fluid		=  $params->get('fluidWidth',0);
		$setWidth 	= $fluid ? '100%' : (int)$params->get('modulewidth',200) .'px';
		$bottom 	= $fluid ? '100%' : (int)($params->get('modulewidth',200)) .'px';
		$minWidth 	= $fluid ? ( (int)$params->get('minwidth',0 ) ? 'min-width: '.(int)$params->get('minwidth',0 ).'px; ' : '') : '' ;
		
		$boxLarge	= ((int)$params->get('boxLarge',160))   ;
		
		$CSS 	= '#zKlatest { width: '.$setWidth.'; '.$minWidth. ' } '
				. '#Kboxwrap'.$moduleId .' { width: '.$setWidth.'; } '
				. '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid { height: '.(int)$params->get('boxNormal',60).'px; ';
		$CSS   .= ( $params->get('borderClr') ? ' border: 1px solid '.$params->get('borderClr') : '' );
		$CSS   .= ' ;} ' 
				. '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .zKlatestAvatar_left { width: '.((int)$params->get('avatarsize',50)+8).'px; } '
				. '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .Kboxcaption { height: '.$boxLarge.'px; } '
				. '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .Kboxcaption .Kboxbottom { width: '.$bottom.'; } ';
				
		$CSS   .= '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .topKboxcaption { '
				. 'background: ' 
				. $params->get('topSlideBkg', '#000000' ) . '; color: '.$params->get('topSlideClr', '' ).' }';
				
		$CSS   .= '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .Kboxcaption { '
				. 'background: ' 
				. $params->get('botSlideBkg', '#000000' ) . '; color: '.$params->get('botSlideClr', '' ).' }';
		
		$CSS   .= ( $params->get('contentBkg')  ? '#Kboxwrap'.$moduleId .' { background: '.$params->get('contentBkg'). '; }' : '' );
					
		if ($params->get('borderClr')) {
										
			$CSS   .= '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .topKboxcaption { '
					. 'border-bottom-width: 3px; '
					. 'border-bottom-style: double; '
					. 'border-bottom-color: '
					. $params->get('borderClr') . '; }';
		}
		if ($params->get('botNavBkg')) {
			$CSS   .= '#Kboxwrap'.$moduleId .' .Kboxgridwrap .Kboxgrid .Kboxcaption .Kboxbottom { '
						. 'background-color: '
						. $params->get('botNavBkg') . '; }';
		}
					
		return $CSS;
	} // function CSS
	
	function getScript($params, $module) {
	
		$avSize 	= ((int)$params->get('avatarsize',50) > 40 ? (int)$params->get('avatarsize',50) : 40 );
		$edge 		= ((int)$params->get('boxLarge',160) - $avSize - 10 );
		if ($params->get('moo_trans1')) {
			$transition	= "fx.options.transition = Fx.Transitions.".$params->get('moo_trans1').($params->get('moo_trans2') ? ".".$params->get('moo_trans2') : "" ).";";
	
		} else {
			$transition	= '';
		}
		
		$js = "
	var szNormal = ".$params->get('boxNormal',60).", szSmall  = ".$params->get('boxSmall',40).", szFull   = ".$params->get('boxLarge',160).";
	var szNormal".$module->id." = ".$params->get('boxNormal',60).", szSmall".$module->id."  = ".$params->get('boxSmall',40).", szFull".$module->id."   = ".$params->get('boxLarge',160).";
	var mTrans".$module->id." = \"".$params->get('moo_trans1','Back') ."\" ;
	var mEase".$module->id." = \"".$params->get('moo_trans2','easeOut') ."\" ;
	var topEdge".$module->id." =\"".  $edge ."\";
	
	window.addEvent('domready', function() {
		
		var wrap = $('Kboxwrap".$module->id."');
		var gridz = $$('.captionfull".$module->id."');
		
		var fx = new Fx.Elements(gridz, {wait: false, duration: 700});
		".$transition."
		
		gridz.each(function(el, i){
			var boxcaption = el.getElement('.cover');
			var profilelink = el.getElement('.profilelink');
			
			var capin = new Fx.Morph(boxcaption, {duration:500, wait:false});
			
			
			
			el.addEvent('mouseenter', function(event){
					fx.cancel();
					capin.cancel();
					var o = {};
					o[i] = {height: [el.getStyle(\"height\").toInt(), szFull".$module->id."]}
					
					gridz.each(function(other, j) {
										
						if(i != j) {
							var h = other.getStyle(\"height\").toInt();
							if(h != szSmall".$module->id.") o[j] = {height: [h, szSmall".$module->id."]};
						}
					});
					
					fx.start(o);
					capin.start({
					'top' : '0px',
					'opacity' : 1
					});
			});
			
			el.addEvent('mouseleave', function(event){
					capin.cancel();
					capin.start({
					'top' : '80px',
					'opacity' : .2
					});			   
			});
		});
	
		wrap.addEvent(\"mouseleave\", function(event) {
			fx.cancel();								   
			var o = {};
			gridz.each(function(el, i) {
				o[i] = {height: [el.getStyle(\"height\").toInt(), szNormal".$module->id."]}
			});
			fx.start(o);
		})
	
	});
	";
		return $js;
	} // function getScript
	
}// class
?>