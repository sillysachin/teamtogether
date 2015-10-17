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

function is_jquery($link)
{
	if ( strpos($link, 'com_poweradmin') !== false || strpos($link, 'com_imageshow') !== false ){
		return false;
	}

	if (strpos($src, 'jquery.') !== false ){
		return true;
	}

	return false;
}
class JSNMedia{
	private $_scripts;
	private $_styles;
	private $_styleDeclaration;
	private $_scriptDeclaration;
	private $_customs;
	private $_lang = 'en';
	private $_dispatch = false;
	private $_docType;
	private $_conflict;
	private $_load_js_language;

	public function __construct()
	{
		$this->_scripts = Array();
		$this->_styles  = Array();
		$this->_customs = Array();
		$this->_scriptDeclaration = Array();
		$this->_styleDeclaration  = Array();
		$this->_docType = JFactory::getDocument()->getType();
		$this->_conflict = true;
		if ( !$this->_dispatch ){
			if(class_exists('JSNHtmlAsset')){
				if(method_exists('JSNHtmlAsset','addScript') && method_exists('JSNHtmlAsset','addStyle')){
		 			JSNHtmlAsset::addStyle( JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
		            JSNHtmlAsset::addStyle(JSN_FRAMEWORK_ASSETS . '/joomlashine/css/jsn-gui.css');
		            JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.js');
		            JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI . 'jsn.jquery.noconflict.js');
		            JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');
		            JSNHtmlAsset::addScript(JURI::root(true) . '/media/jui/js/jquery.ui.sortable.js');
		            JSNHtmlAsset::addScript( JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-ck/jquery.ck.js');
		            JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JS_URI . 'jquery.topzindex.js');
		 			JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI . 'jsn.window.js');
		 			JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI . 'jsn.lang.js');
		            JSNHtmlAsset::addStyle(JSN_POWERADMIN_STYLE_URI ."poweradmin.css");
				}
			}

			//Load js lang
			if ( JRequest::getVar('option', '') == 'com_poweradmin' ){
				JSNFactory::localimport('libraries.joomlashine.language.javascriptlanguages');
				$jsLang = JSNJavascriptLanguages::getInstance();
				$this->addScriptDeclaration($jsLang->loadLang());
			}
		}
	}
	/**
	 *
	 * Get instance
	 *
	 */
	public static function getInstance()
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}
		if ( empty($instances['JSNMedia']) ) {
			$instance	= new JSNMedia();
			$instances['JSNMedia'] = &$instance;
		}

		return $instances['JSNMedia'];
	}

	public function setLoadJSLang($load = true)
	{
		$this->_load_js_language = $load;
	}
	/**
	 *
	 * Set doctype to render
	 *
	 * @param String $type
	 */
	public function setType($type)
	{
		if ( !in_array($type, Array('raw', 'html') ) )
		{
			$this->_docType = 'html';
		}else{
			$this->_docType = $type;
		}
	}
	/**
	 *
	 * Return language key
	 */
	public function getLang()
	{
		return $this->_lang;
	}
	/**
	 *
	 * Queue store script file to array
	 *
	 * @param String $filename
	 */
	public function addScript( $filename )
	{
		if ( !in_array($filename, $this->_scripts) ){
			JSNFactory::localimport('helpers.poweradmin');
			$currentVersion		= PoweradminHelper::getVersion();
			$filename			.= '?v=' . $currentVersion;
			$this->_scripts[] 	= $filename;
		}
	}
	/**
	 *
	 * Conflict
	 *
	 * @param Boolean $conflict
	 */
	public function conflict($conflict = true)
	{
		$this->_conflict = ( $conflict == true || $conflict == false ) ? $conflict : true;
	}
	/**
	 *
	 * Queue store style file to array
	 *
	 * @param String $filename
	 */
	public function addStyleSheet( $filename )
	{
		if ( !in_array($filename, $this->_styles) ){
			JSNFactory::localimport('helpers.poweradmin');
			$currentVersion		= PoweradminHelper::getVersion();
			$filename			.= '?v=' . $currentVersion;
			$this->_styles[] = $filename;
		}
	}
	/**
	 *
	 * Queue store custom tag to array
	 *
	 * @param String $str
	 */
	public function addCustomTag( $str )
	{
		$this->_customs[] = $str;
	}
	/**
	 *
	 * Queue store style declaration to array
	 *
	 * @param String $str
	 */
	public function addStyleDeclaration( $str )
	{
		$this->_styleDeclaration[] = $str;
	}
	/**
	 *
	 * Queue store script declaration to array
	 *
	 * @param String $str
	 */
	public function addScriptDeclaration( $str )
	{
		$this->_scriptDeclaration[] = $str;
	}
	/**
	 *
	 * Parse all queue to page
	 *
	 */
	public function addMedia()
	{
		$document = JFactory::getDocument();
		$docType  = $document->getType();
		if ( $this->_load_js_language )
		{
			JSNFactory::localimport('libraries.joomlashine.language.javascriptlanguages');
			$jsLang = JSNJavascriptLanguages::getInstance();
			$this->addScriptDeclaration($jsLang->loadLang());
		}
		if ( $this->_docType == 'raw' ){
			$medias = Array();
			//Add all style file to page
			if ( count( $this->_styles  ) ){
				foreach( $this->_styles as $style ){
					$medias[] = '<link  type="text/css" rel="stylesheet" href="'.$style.'" />';
				}
			}
			//Add all script file to page
			if ( count( $this->_scripts ) ){
				foreach( $this->_scripts as $script ){
					$medias[] = '<script type="text/javascript" src="'.$script.'"></script>';
				}

				if ( !in_array( PoweradminHelper::makeUrlWithSuffix(JSN_POWERADMIN_LIB_JSNJS_URI. 'conflict.js'), $this->_scripts ) ){
					$medias[] = '<script type="text/javascript" src="'.PoweradminHelper::makeUrlWithSuffix(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.conflict.js').'"></script>';
				}
			}
			//Add all custom tag to page
			if ( count( $this->_customs ) ){
				foreach( $this->_customs as $custom ){
					$medias[] = $custom;
				}
			}
			//Add all style declaration to page
			if ( count( $this->_styleDeclaration ) ){
				$medias[] = '<style type="text/css">'. implode( PHP_EOL, $this->_styleDeclaration ) .'</style>';
			}
			//Add all script declaration to page
			if ( count( $this->_scriptDeclaration ) ){
				$medias[] = '<script type="text/javascript">'. implode( PHP_EOL, $this->_scriptDeclaration ) .'</script>';
			}

			echo implode(PHP_EOL, $medias);
		}else{
			//behavior mootools
			JHtmlBehavior::framework();
			//behavior modal
			JHtml::_('behavior.modal');
			//behavior tooltip
			JHtml::_('behavior.tooltip');
			//behavior formvalidation
			JHtml::_('behavior.formvalidation');
			//behavior combobox
			JHtml::_('behavior.combobox');

			//Add all style file to page
			if ( count( $this->_styles  ) ){
				foreach( $this->_styles as $style ){
					$document->addStyleSheet( $style );
				}
			}
			$system_js = Array();
			$user_js   = Array();
			$docScripts = $document->_scripts;
			if ( count($docScripts) ){
				foreach ($docScripts as $key => $script ){
					if ( strpos($key, '/media/system/' ) !== false ){
						$system_js[$key] = $script;
					}else if ( !is_jquery($key) ){
						$user_js[$key] = $script;
					}

				}
				$document->_scripts = Array();
			}
			//Add all script file to page
			if ( count( $this->_scripts ) ){
				foreach( $this->_scripts as $script ){
					JSNHtmlAsset::addScript( $script );
				}

				if ( !in_array( PoweradminHelper::makeUrlWithSuffix(JSN_POWERADMIN_LIB_JSNJS_URI. 'conflict.js'), $this->_scripts ) ){
					JSNHtmlAsset::addScript( PoweradminHelper::makeUrlWithSuffix(JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.conflict.js') );
				}
			}
			$docScripts = $document->_scripts;
			$index = 0;
			$jsn_jquery = Array();
			foreach ( $docScripts as $key => $script ){
				if ( $index < 2 ){
					$jsn_jquery[$key] = $script;
				}else{
					$user_js[$key] = $script;
				}
				$index++;
			}

			$document->_scripts = $system_js + $jsn_jquery + $user_js;

			//Add all custom tag to page
			if ( count( $this->_customs ) ){
				foreach( $this->_customs as $custom ){
					$document->addCustomTag( $custom );
				}
			}
			//Add all style declaration to page
			if ( count( $this->_styleDeclaration ) ){
				$document->addStyleDeclaration( implode( PHP_EOL, $this->_styleDeclaration ) );
			}
			//Add all script declaration to page
			if ( count( $this->_scriptDeclaration ) ){
				$document->addScriptDeclaration( implode( PHP_EOL, $this->_scriptDeclaration ) );
			}
		}

		$this->_dispatch = true;
		$this->__construct();
	}
}