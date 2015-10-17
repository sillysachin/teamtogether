<?php
/**
 * @version     $Id$
 * @package     JSNPoweradmin
 * @subpackage
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class JSNT3Template extends T3Template{

	public function __construct( $template = NULL )
	{
		if ( $template )
		{
			parent::T3Template( $template );
		}
	}
	/**
	 *
	 * Disable JT3 infomode
	 *
	 * @return: Save setting to file params.ini
	 */
	public function disableInfoMode()
	{
		JSNFactory::localimport('libraries.joomlashine.database');
		$template = JSNDatabase::getDefaultTemplate();
		$client   = JApplicationHelper::getClientInfo($template->client_id);
		$file = $client->path.'/templates/'.$template->element.'/params.ini';
		$data = JFile::read($file);
		$data = explode("\n", $data);
		$params = array();
		$needChange = false;
		foreach( $data as $val){
			$spos  = strpos($val, "=");
			$key   = substr($val, 0, $spos);
			$value = substr($val, $spos + 1, strlen($val) - $spos);
			if ($key == 'infomode'){
				if ( $value == '"1"' ){
					$value = '"0"';
					$needChange = true;
				}
			}
			$params[$key] = $value;
		}

		if ( $needChange  ){
			$data = array();
			foreach( $params as $key => $val){
				$data[] = $key.'='.$val;
			}
			$data = implode("\n", $data);

			if( JFile::exists($file) ){
				@chmod($file, 0777);
			}
			JFile::write($file, $data);
		}
	}
	/**
	 *
	 * JT3 Framework render
	 */
	public function render()
	{
		$replace = array();
		$matches = array();
		parent::loadLayout();
		$data = $this->_html;

		if(preg_match_all('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $data, $matches)) {
			$cache_exclude = parent::getParam ('cache_exclude');
			$cache_exclude = new JRegistry ($cache_exclude);
			$nc_com = explode (',',$cache_exclude->get ('component'));
			$nc_pos = explode (',',$cache_exclude->get ('position'));

			$replace = array();
			$matches[0] = array_reverse($matches[0]);
			$matches[1] = array_reverse($matches[1]);
			$matches[2] = array_reverse($matches[2]);

			$count = count($matches[1]);
			$option = JRequest::getCmd ('option');

			$headindex = -1;

			//for none cache items
			$nonecachesearch = array();
			$nonecachereplace = array();

			//search for item load in template (css, images, js)
			$regex = '/(href|src)=("|\')([^"\']*\/templates\/' . T3_ACTIVE_TEMPLATE . '\/([^"\']*))\2/';

			for($i = 0; $i < $count; $i++)
			{
				$attribs = JUtility::parseAttributes( $matches[2][$i] );
				$type  = $matches[1][$i];

				$name  = isset($attribs['name']) ? $attribs['name'] : null;
				//no cache => no cache for all jdoc include except head
				//cache: exclude modules positions & components listed in cache exclude param
				//check if head
				if ($type == 'head') $headindex = $i;
				else {
					$content    = parent::getBuffer($type, $name, $attribs);
					$renderer	= $this->loadRenderer('module');
					$poweradmin         = JRequest::getCmd('poweradmin', 0);
					$vsm_changeposition = JRequest::getCmd('vsm_changeposition', 0);

					//Add a div wrapper for showing block information
					if ( $poweradmin == 1 ) {
						//If the page requested to render position only
						if ($vsm_changeposition == 1){
							if ($type == 'modules') {
								$content = '<div class="jsn-element-container_inner">'.
											'<div class="jsn-poweradmin-position clearafter" id="'.$name.'-jsnposition">
												<p>'.$name.'</p>
											</div>
											</div>
										    ';
							} else if ($type == 'module') {
								$key = "mod.$name";
							} else if ($type == 'component') {
								$content = '<div class="jsn-component-container" id="jsnrender-component"><div class="jsn-show-component-container"><p>'.parent::getTitle().'</p></div></div>';
							} else $key = "$type.$name";
						}else{
							if ($type == 'modules') {
								$buffer = '';
								foreach (JModuleHelper::getModules($name) as $mod) {
									$buffer .= '<div class="poweradmin-module-item" id="'.$mod->id.'-jsnposition" ><div id="moduleid-'.$mod->id.'-content">'.$renderer->render($mod, $attribs).'</div></div>';
								}
								$content = '<div class="jsn-element-container_inner">'.
											'<div class="jsn-poweradmin-position clearafter" id="'.$name.'-jsnposition">
											'.$buffer.'
											</div>
											</div>
										    ';
							} else if ($type == 'module') {
								$key = "mod.$name";
							} else if ($type == 'component') {
								$app	   = JFactory::getApplication();
								$itemid    = JRequest::getVar('itemid', '');
								$menu	   = $app->getMenu();

								if ($itemid){
									$menuItem = $menu->getItem($itemid);
								}else{
									$menuItem = $menu->getActive();
								}
								$uri    = JURI::getInstance();
								$route  = JRouter::getInstance('site');
								$params = $route->parse($uri);
								if (empty($params['id']) && !empty($menuItem->id)){
									$uri->parse($menuItem->link);
									$params = $route->parse($uri);
								}

								if (!empty($params['option'])){
									$key = array_search($params['option'], array('', 'com_content', 'com_categories', 'com_banner', 'com_weblinks', 'com_contact', 'com_newsfeeds', 'com_search', 'com_redirect'));
									if ($key){
										if (!empty($params['id'])){
											if ($params['view'] == 'category'){
												$editLink = 'option=com_categories&task=category.edit&id='.$params['id'].'&extension='.$params['option'].'&tmpl=component';
												$task = 'category.apply';
											}else{
											    switch($key)
											    {
											    	case 1: //com_content
										    			$editLink = 'option=com_content&task=article.edit&id='.$params['id'].'&tmpl=component';
										    			$task = 'article.apply';
											    		break;
											    	case 2: //com_categories
											    		$editLink = 'option=com_categories&task=category.edit&id='.$params['id'].'&tmpl=component';
											    		$task = 'category.apply';
											    		break;
											    	case 3:
											    		if($params['view'] == 'client'){
											    			$editLink = 'option=com_banners&task=client.edit&id='.$params['id'].'&tmpl=component';
											    			$task = 'client.apply';
											    		}else{
											    			$editLink = 'option=com_banners&task=banner.edit&id='.$params['id'].'&tmpl=component';
											    			$task = 'bannber.apply';
											    		}
											    		break;
											    	case 4:
										    			$editLink = 'option=com_weblinks&task=weblink.edit&id='.$params['id'].'&tmpl=component';
										    			$task = 'weblink.apply';
											    		break;
											    	case 5:
										    			$editLink = 'option=com_contact&task=contact.edit&id='.$params['id'].'&tmpl=component';
										    			$task = 'contact.apply';
											    		break;
											    	case 6:
										    			$editLink = 'option=com_newsfeeds&task=newsfeed.edit&id='.$params['id'].'&tmpl=component';
										    			$task = 'newsfeed.apply';
											    		break;
											    	case 7:
										    			$editLink = 'option=com_search&task=search.edit&id='.$params['id'].'&tmpl=component';
										    			$task = 'search.apply';
											    		break;
											    	case 8:
										    			$editLink = 'option=com_redirect&task=link.edit&id='.$params['id'].'&tmpl=component';
										    			$task = 'link.apply';
											    		break;
											    }
											}
										}else{
											$editLink = 'option=com_menus&task=item.edit&id='.$menuItem->id.'&tmpl=component';
											$task = 'item.save';
										}
									}else{
										//in feature
										$editLink = '';
										$task = '';
									}
								}else{
									$editLink = '';
									$task = '';
								}

								$content = '<div class="jsn-component-container" id="jsnrender-component">'
								.'<div class="jsn-show-component-container">'
								.'<div class="jsn-show-component">'
								.'<span id="tableshow" itemid="'.$menuItem->id.'" editlink="'.base64_encode($editLink).'" title="'.parent::getTitle().'" task="'.$task.'"></span>'
								.'</div>'
								.'</div>'
								.$content
								.'</div>';

							} else $key = "$type.$name";

						}
					}
					//process url
					$content = preg_replace_callback ( $regex, array ($this, 'processReplateURL' ), $content );
				}
				if (!parent::getParam ('cache') || $type == 'head' || ($type == 'modules' && in_array($name, $nc_pos)) || ($type == 'component' && in_array($option, $nc_com))) {
					$replace[$i] = $matches[0][$i];
					$nonecachesearch[] = $replace[$i];
					$nonecachereplace[] = $content;
				} else {
					$replace[$i] = $content;
				}
			}

			//update head
			if ($headindex > -1) {
				T3Head::proccess();
				$head = parent::getBuffer('head');
				$replace[$headindex] = $head;
			}

			//replace all cache content
			$data = str_replace($matches[0], $replace, $data);
			//update cache
			$key = T3Cache::getPageKey ();
			if ($key) {
				T3Cache::store ( $data, $key );
			}

			//replace none cache content
			$data = str_replace($nonecachesearch, $nonecachereplace, $data);
		} else {
			$token	= JUtility::getToken();
			$search = '#<input type="hidden" name="[0-9a-f]{32}" value="1" />#';
			$replacement = '<input type="hidden" name="'.$token.'" value="1" />';
			$data = preg_replace( $search, $replacement, $data );

		}
		echo $data;
	}
}
?>