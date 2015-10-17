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

jimport('joomla.plugin.plugin');

if ( !function_exists('JSNFactory') ){
	//include global function
	include_once(JPATH_ADMINISTRATOR.'/components/com_poweradmin/libraries/joomlashine/factory.php');
}

//include defines
if(file_exists(JPATH_ROOT . '/administrator/components/com_poweradmin/poweradmin.defines.php')){
	require_once JPATH_ROOT . '/administrator/components/com_poweradmin/poweradmin.defines.php' ;
}else if(file_exists(JPATH_ROOT . '/administrator/components/com_poweradmin/defines.poweradmin.php')){
	require_once JPATH_ROOT . '/administrator/components/com_poweradmin/defines.poweradmin.php' ;
}

JSNFactory::import('plugins.system.jsnpoweradmin.libraries.jsnplghelper', 'site');

// Load plugin dependencies
require_once JPATH_ADMINISTRATOR.'/components/com_poweradmin/helpers/history.php';
require_once JPATH_ADMINISTRATOR.'/components/com_poweradmin/helpers/poweradmin.php';
require_once JPATH_ADMINISTRATOR.'/components/com_poweradmin/helpers/extensions.php';
require_once dirname(__FILE__).'/libraries/preview.php';

class plgSystemJsnpoweradmin extends JPlugin
{
	private $_helper = null;
	private $_templateAuthor = '';

	/**
	 * Paramenters for AdminBar plugin
	 * @var JRegistry
	 */
	private $_params = null;

	/**
	 * @var JApplication
	 */
	private $_application = null;

	/**
	 * @var JUser
	 */
	private $_user = null;

	/**
	 * @var JDocumentHTML
	 */
	private $_document = null;

	/**
	 * @var JSession
	 */
	private $_session = null;

	/**
	 * @var JSNPowerAdminBarPreview
	 */
	private $_preview = null;

	/**
	 * @var array
	 */
	private $_defaultStyles = array();

	/**
	 * @var string
	 */
	private $_menuContent = '';

	private $_adminTemplate = null;

	private $_ext = 'com_poweradmin';

	/** Constructor function **/
	function __construct(&$subject, $config)
	{
		// Check if JSN Framework installed & enabled.
		$jsnframework = JPluginHelper::getPlugin('system','jsnframework');
		if(!$jsnframework || !file_exists(JPATH_ROOT . '/plugins/system/jsnframework')){
			return ;
		}
	    JSNFactory::import('plugins.system.jsnframework.libraries.joomlashine.config.helper', 'site');
        JSNFactory::import('plugins.system.jsnframework.libraries.joomlashine.utils.xml', 'site');
		$this->_params 		= JSNConfigHelper::get('com_poweradmin');
		$this->_application = JFactory::getApplication();
		$this->_user 		= JFactory::getUser();
		$this->_session 	= JFactory::getSession();
		$this->_preview 	= new JSNPowerAdminBarPreview();
		$this->loadLanguage('plg_system_jsnpoweradmin');
		$this->_removeAdminBarPlugin();

		$app = JFactory::getApplication();
		$poweradmin           = JRequest::getCmd('poweradmin', 0);
		$showTemplatePosition = JRequest::getCmd('tp', 0);

		if ($app->isAdmin()){
			$user = JFactory::getUser();
			if (JRequest::getVar('view', '') == 'jsnrender' && $user->id == 0){
				jimport('joomla.application.component.controller');
				JController::setRedirect(JSN_VISUALMODE_PAGE_URL);
				JController::redirect();
			}
		}
		if ( $poweradmin == 1 ){
			/**
			 * Auto-enable Preview Module Positions of template setting
			 */
			if ( $showTemplatePosition == 1 ){
				$PreviewModulePositionsIsEnabled = ( JComponentHelper::getParams('com_content')->get('template_positions_display', 0) == 1 ) ? true : false ;
				if ( !$PreviewModulePositionsIsEnabled ){
					/**
					 * Get config class
					 */
					JSNFactory::localimport('libraries.joomlashine.config');
					JSNConfig::extension( 'com_templates', array( 'template_positions_display' => 1 ) );
				}
			}

			/** load JSNPOWERADMIN template library **/
			$template = JSNFactory::getTemplate();
			$this->_templateAuthor = $template->getAuthor();

			/*if T3 Framework*/
			if ($this->_templateAuthor == 'joomlart'){
				if ( !class_exists('T3Common') ){
					jimport ('joomla.html.parameter');
					JSNFactory::import('plugins.system.jat3.jat3.core.common', 'site');
				}
				if ( !class_exists('T3Framework') ){
					JSNFactory::import('plugins.system.jat3.jat3.core.framework', 'site');
					$jt3Plg = JPluginHelper::getPlugin('system', 'jat3');
					T3Framework::t3_init( $jt3Plg->params );
				}
				JSNFactory::import('plugins.system.jsnpoweradmin.libraries.jsnjoomlart', 'site');
			}
			/* if YooTheme */
			else if ($this->_templateAuthor == 'yootheme'){
				return;
			}
			/* If gavickpro */
			else if ($this->_templateAuthor == 'gavick'){
				JSNFactory::import('libraries.joomla.environment.browser', 'site');
				$browser = JBrowser::getInstance();
				$browser->setBrowser('JSNPoweradmin');
			}
			//If JoomlaXTC
			else if($this->_templateAuthor == 'joomlaxtc'){
				JSNFactory::import('plugins.system.jsnpoweradmin.libraries.jsnjoomlaxtc', 'site');
			}
			$this->_helper = JSNPLGHelper::getInstance();
		}

		parent::__construct($subject, $config);
	}

	/**
	* Before render needs using this function to make format of HTML of modules
	*
	* @return: Changed HTML format
	*/
	public function onBeforeRender()
	{
		// Load custom assets for each menu
		if(!$this->_application->isAdmin() && $this->_params->get('custom_assets_enhance', true)){
			require_once JPATH_ROOT . '/administrator/components/com_poweradmin/models/menuitem.php';
			$itemid 	= JRequest::getVar('Itemid');
			$menuCss 	= PoweradminModelMenuitem::loadMenuCustomAssets($itemid, 'css');
			$menuJs 	= PoweradminModelMenuitem::loadMenuCustomAssets($itemid, 'js');

			// Load css files from parent menu items
			$finalCssFiles = array();
			$parentLoadableCssFiles = array();
			PoweradminModelMenuitem::getInheritedAssetsFromParents($parentLoadableCssFiles, $itemid, 'css');
			if (count($parentLoadableCssFiles)) {
				foreach ($parentLoadableCssFiles as $key=>$value) {
					if ($value->loaded == 'true') {
						array_push ($finalCssFiles, $key);
					}
				}
			}

			if(count($menuCss) && isset($menuCss->assets)){
				foreach ($menuCss->assets as $key=>$value){
					if($value->loaded === "true"){
						if (in_array($key, $finalCssFiles)) {
							$_k	= array_search($key, $finalCssFiles);
							unset ($finalCssFiles[$_k]);
						}
						array_push($finalCssFiles, $key);
					}
				}
			}

			// Add computed css files
			if (count($finalCssFiles) ) {
				foreach ($finalCssFiles as $_file) {
					$this->_document->addStyleSheet($_file);
				}
			}


			// Load JS files from parent menu items
			$finalJsFiles = array();
			$parentLoadableJsFiles = array();
			PoweradminModelMenuitem::getInheritedAssetsFromParents($parentLoadableJsFiles, $itemid, 'js');
			if (count($parentLoadableJsFiles)) {
				foreach ($parentLoadableJsFiles as $key=>$value) {
					if ($value->loaded == 'true') {
						array_push ($finalJsFiles, $key);
					}
				}
			}

			if(count($menuJs) && isset($menuJs->assets)){
				foreach ($menuJs->assets as $key=>$value){
					if($value->loaded === "true"){
						if (in_array($key, $finalJsFiles)) {
							$_k	= array_search($key, $finalJsFiles);
							unset ($finalJsFiles[$_k]);
					}
						array_push($finalJsFiles, $key);
					}
				}
			}

			// Add computed css files
			if (count($finalJsFiles) ) {
				foreach ($finalJsFiles as $_file) {
					$this->_document->addScript($_file);
				}
			}
		}

		if ($this->_application->isAdmin() && $this->_user->id > 0 && JRequest::getVar('tmpl') != 'component' && $this->_params->get('enable_adminbar', true) == true)
		{
		    JHtml::_('behavior.framework');
			$document = JFactory::getDocument();
			$template = isset($document->template) ? $document->template : null;

			if ($template != null && !in_array($template, array('rt_missioncontrol', 'hathor')) && (JRequest::getCmd('tmpl') != 'component' && JRequest::getCmd('format') != 'raw')) {
				require_once dirname(__FILE__).'/libraries/administrator.menu.php';

				$this->loadLanguage('mod_menu');
				$this->_menuContent = JSNPowerAdminMenuHelper::renderMenus();

				$modules = JModuleHelper::getModules('menu');
				foreach ($modules as $module) {
					if ($module->module == 'mod_menu') {
						continue;
					}

					$this->_menuContent.= JModuleHelper::renderModule($module);
				}

				$this->_menuContent = '<div id="module-menu">' . $this->_menuContent . '</div>';
			}
		}

		if ($this->_application->isAdmin()) return;
	}

	/**
	* fix conflict with mambot plugins
	**/
	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		$app = JFactory::getApplication();
		if($app->isAdmin()) {
			$article->text = str_replace("{","{* ",$article->text);
		}
	}

	/**
	*support for T3 Framework
	**/
	public function onRenderModule (&$module, $attribs) {}
    public function onAfterRender(){
    	$document = JFactory::getDocument();

    	if ($document instanceOf JDocumentHTML) {
    		$template = $document->template;
			$content = JResponse::getBody();

	    	if ($this->_application->isAdmin() && $this->_user->id > 0) {
	    		PowerAdminHistoryHelper::onAfterRender();

	    		preg_match('/<body([^>]+)>/is', $content, $matches);
	    		$pos = strpos(@$matches[0], 'jsn-master');

				if (JRequest::getCmd('tmpl') != 'component' && JRequest::getCmd('format') != 'raw') {
					if ($this->_params->get('enable_adminbar', true) == true)
	    			{
						$content = preg_replace('/<body([^>]*)>(.*)<\/body>/is', '<body\\1 data-template="'.$template.'"><div id="jsn-adminbar">'.$this->_menuContent.'</div><div id="jsn-body-wrapper">\\2</div></body>', $content);
	    			}
					else
					{
						$content = preg_replace('/<body([^>]*)>(.*)<\/body>/is', '<body\\1" data-template="'.$template.'">\\2</body>', $content);
					}
	    		}
				if (!$pos)
				{
					if(preg_match('/<body([^>]*)class\s*=\s*"([^"]+)"([^>]*)>/is', $content)) {
						$content = preg_replace('/<body([^>]*)class\s*=\s*"([^"]+)"([^>]*)>/is', '<body \\1 class="jsn-master tmpl-'.$template.' \\2" \\3>', $content);
					}
					else {
						$content = preg_replace('/<body([^>]+)>/is', '<body \\1 class="jsn-master tmpl-'.$template.'">', $content);
					}
				}
			}

			JResponse::setBody($content);
		}

    }

	public function onAfterRoute(){
		$this->_document 	= JFactory::getDocument();
		if ($this->_application->isAdmin() && $this->_user->id > 0 && JRequest::getVar('tmpl') != 'component' && $this->_params->get('enable_adminbar', true) == true) {
		    $this->_addAssets();
		}

		$option = JRequest::getCmd("option", '');
		$view 	= JRequest::getCmd("view", '');
		$uri	= JUri::root(true);
		if($option == 'com_modules' && $view == 'module' || $option == 'com_advancedmodules' && $view == 'module'){
			$module = JSNPositionsHelper::getModule(JRequest::getInt('id'));
			if($this->_params->get('position_chooser_enhance', true)){
				if((!JRequest::getInt('id') || ($module && $module->client_id == 0)) && $this->canEnablePosChooser()){
					JSNHtmlAsset::addScript($uri . '/media/jui/js/jquery.min.js');
					JSNHtmlAsset::addScript(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-ui/js/jquery-ui-1.9.0.custom.min.js');
					JSNHtmlAsset::addScript($uri . '/administrator/components/com_poweradmin/assets/js/joomlashine/jsn.jquery.noconflict.js');
					if(file_exists('../media/system/js/mootools-core.js')){
						$this->_document->addScript($uri.'/media/system/js/mootools-core.js');
						$this->_document->addScript($uri.'/media/system/js/core.js');
						$this->_document->addScript($uri.'/media/system/js/mootools-more.js');
					}else{
						JHtml::_('behavior.framework');
					}
					JSNHtmlAsset::addScript($uri . '/plugins/system/jsnpoweradmin/assets/js/window.js');
					JSNHtmlAsset::addScript($uri . '/plugins/system/jsnpoweradmin/assets/js/module/position.js');
					$this->_document->addStyleSheet($uri . '/plugins/system/jsnpoweradmin/assets/css/window.css');
					$customScript = "
					var baseUrl = '" . JUri::root() . "';
					var PLG_DEFAULT_TEXT_CHANGE_POSITION_TITLE = '" . JText::_('PLG_DEFAULT_TEXT_CHANGE_POSITION_TITLE') . "';
					var PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION = '" . JText::_('PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION') . "';
					var COM_MODULES_CHANGE_POSITION_BUTTON = '" . JText::_('PLG_MODULES_CHANGE_POSITION_BUTTON') . "';
					var moduleid = " . JRequest::getInt('id') . ";
					";
					$this->_document->addScriptDeclaration($customScript);
				}
			}

		}

	}

	/**
	 * Method to check if we should turn
	 * position chooser feature On or not
	 * @return boolean
	 */
	private function canEnablePosChooser()
	{
		// Bypass if CUrl not enabled.
		if(!JSNFactory::_cURLCheckFunctions()) return false;

		// Bypass if current site template not supported
// 		global $notSupportedTemplateAuthors;
// 		$template = JSNTemplateHelper::getInstance();
// 		$templateAuthor = $template->getAuthor();
// 		if (in_array($templateAuthor, $notSupportedTemplateAuthors)) return false;

		// Bypass if site is offline mode
		$app = $this->_application;
		if($app->getCfg('offline')) return false;
		return true;
	}

    public function onAfterDispatch()
    {
		if ($this->_application->isAdmin() &&
			JRequest::getVar('format', '') != 'raw' &&
			JRequest::getVar('option', '') == 'com_poweradmin'  &&
			JRequest::getVar('view') != 'update') {
			$JSNMedia = JSNFactory::getMedia();
			$JSNMedia->addMedia();
			return;
		}
    }

    /** This trigger function to help customize layout of some pages **/
    public function onAfterInitialise(){
    	if (!$this->_application->isAdmin()) {
			return;
		}

		if ($this->_application->isAdmin() && $this->_user->id > 0 && JRequest::getVar('tmpl') != 'component' && $this->_params->get('enable_adminbar', true) == true) {
			$this->_sendCookies();
			$this->_target = (JRequest::getBool('hidemainmenu') == true) ? '_blank' : '_parent';

		}

		if ($this->_user->id > 0) {
			PowerAdminHistoryHelper::onAfterInitialise();
		}

    	if (JRequest::getVar('format', '') == 'raw' || JRequest::getVar('format', '') == 'rss') {
			return;
		}

    	$this->_helper = JSNPLGHelper::getInstance();


    }

	/**
	 * Send requirement cookies to client
	 * @return void
	 */
	private function _sendCookies ()
	{
		setcookie('session-life-time', JFactory::getConfig()->get('lifetime', 15));
		setcookie('last-request-time', time());
		setcookie('session-infinite', $this->_params->get('admin_session_timer_infinite', false));
		setcookie('session-warning-time', $this->_params->get('admin_session_timeout_warning', 1));
		setcookie('session-disable-warning', $this->_params->get('admin_session_timeout_warning_disabled', false));
	}

	/**
	 * Attach css, javascript files to document
	 * @return void
	 */
	private function _addAssets ()
	{
		$this->_getDefaultStyles();

		$file = dirname(__FILE__).'/assets/js/supports/'.$this->_defaultStyles['admin']->template.'.js';
		if (!is_file($file)) {
			return;
		}


		require_once JPATH_ADMINISTRATOR.'/components/com_poweradmin/helpers/poweradmin.php';

		$currentVersion = PoweradminHelper::getVersion();

		$config 	= $this->_getJSConfiguration();
		$language 	= $this->_getJSLanguage();
		$uri 		= JUri::root(true);
		$template 	= $this->_defaultStyles['admin']->template;

		$this->_document->addStyleSheet(JSN_FRAMEWORK_ASSETS . '/joomlashine/css/jsn-bootstrap.css');
		$this->_document->addStylesheet($uri.'/plugins/system/jsnpoweradmin/assets/css/adminbar.css?v=' . $currentVersion);
		$this->_document->addStylesheet($uri.'/plugins/system/jsnpoweradmin/assets/css/window.css?v=' . $currentVersion);
		$this->_document->addStylesheet($uri.'/plugins/system/jsnpoweradmin/assets/css/print.css?v=' 		. $currentVersion, 'text/css', 'print');

		if (in_array($template, array('minima', 'aplite'))) {
			$this->_document->addStylesheet($uri.'/plugins/system/jsnpoweradmin/assets/css/adminbar.menu.css?v=' 		. $currentVersion);
		}

		if ($template == 'hathor') {
			$this->_document->addStylesheet($uri.'/plugins/system/jsnpoweradmin/assets/css/adminbar.hathor.css?v=' 		. $currentVersion);
		}

		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/jquery.noconflict.js?v='   . $currentVersion);
		if(file_exists('../media/system/js/mootools-core.js')){
            $this->_document->addScript($uri.'/media/system/js/mootools-core.js');
            $this->_document->addScript($uri.'/media/system/js/core.js');
            $this->_document->addScript($uri.'/media/system/js/mootools-more.js');
		}else{
            JHtml::_('behavior.framework');
		}

		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/mootools/mooml.js?v=' 	. $currentVersion);
		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/scrollbar.js?v=' 			. $currentVersion);
		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/window.js?v=' 			. $currentVersion);
		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/supports/'				. $template . '.js?v=' . $currentVersion);
		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/adminbar.js?v=' 			. $currentVersion);
		$this->_document->addScript($uri.'/plugins/system/jsnpoweradmin/assets/js/history.js?v=' 			. $currentVersion);
		$this->_document->addScriptDeclaration("
			if (JoomlaShine === undefined) { var JoomlaShine = {}; }
			if (typeof(jQuery) !== 'undefined') { jQuery.noConflict(); }

			JoomlaShine.language = {$language};
			window.addEvent('domready', function () {
				if(!document.getElementById('jsn-adminbar-wrapper')){
					setTimeout(function(){
							new JSNAdminBar({$config});
							new JSNHistory();
						}, 500);				
					}
			});
		");
	}

	/**
	 * Return language declaration for javascript
	 */
	private function _getJSLanguage ()
	{
		$language = array(
			// Language for open toolbar button
			'JSN_ADMINBAR_BUTTON' => JText::_('PLG_JSNADMINBAR_MENUTAB'),

			// Template menu
			'JSN_ADMINBAR_STYLES'	=>	JText::_('PLG_JSNADMINBAR_MANAGE_STYLES'),
			'JSN_ADMINBAR_STYLES_MANAGER' => JText::_('PLG_JSNADMINBAR_MANAGE_STYLES_TITLE'),

			// Extension menu
			'JSN_ADMINBAR_EXT_INSTALL'	=> JText::_('PLG_JSNADMINBAR_INSTALL'),
			'JSN_ADMINBAR_EXT_MANAGE'	=> JText::_('PLG_JSNADMINBAR_MANAGE'),
			'JSN_ADMINBAR_EXT_UPDATE'	=> JText::_('PLG_JSNADMINBAR_UPDATE'),

			// Site menu
			'JSN_ADMINBAR_SITEMANAGER' => JText::_('PLG_JSNADMINBAR_SITEMENU_MANAGER'),
			'JSN_ADMINBAR_SITEPREVIEW' => JText::_('PLG_JSNADMINBAR_SITEMENU_PREVIEW'),

			// User menu
			'JSN_ADMINBAR_USERMENU_WELCOME' => JText::sprintf('PLG_JSNADMINBAR_USERMENU_WELCOME', $this->_user->username),
			'JSN_ADMINBAR_USERMENU_PROFILE' => JText::_('PLG_JSNADMINBAR_USERMENU_PROFILE'),
			'JSN_ADMINBAR_USERMENU_EDITOR' => JText::_('PLG_JSNADMINBAR_USERMENU_EDITOR'),
			'JSN_ADMINBAR_USERMENU_MESSAGE' => JText::_('PLG_JSNADMINBAR_USERMENU_MESSAGES'),
			'JSN_ADMINBAR_USERMENU_LOGOUT'  => JText::_('JLOGOUT'),
			'JSN_ADMINBAR_EDIT_PROFILE'  => JText::_('PLG_JSNADMINBAR_EDIT_PROFILE'),

			// History
			'JSN_ADMINBAR_HISTORY_EMPTY'	=> JText::_('PLG_JSNADMINBAR_HISTORY_EMPTY'),
			'JSN_ADMINBAR_HISTORY_TITLE'	=> JText::_('PLG_JSNADMINBAR_HISTORY_TITLE'),
			// Favourite
			'JSN_ADMINBAR_FAVOURITE_TITLE'	=> JText::_('PLG_JSNADMINBAR_FAVOURITE_TITLE'),
			'JSN_ADMINBAR_FAVOURITE_REMOVE'	=> JText::_('PLG_JSNADMINBAR_FAVOURITE_REMOVE'),

			// Spotlight
			'JSN_ADMINBAR_SPOTLIGHT_SEARCH'	=> 'search...',
			'JSN_ADMINBAR_SPOTLIGHT_EMPTY'  => JText::_('PLG_JSNADMINBAR_SEARCH_EMPTY'),
			'JSN_ADMINBAR_SPOTLIGHT_SEE_MORE' => JText::_('PLG_JSNADMINBAR_SEARCH_SEE_MORE'),

			// Common
			'JSN_ADMINBAR_UNINSTALL'		=> JText::_('PLG_JSNADMINBAR_UNINSTALL'),
			'JSN_ADMINBAR_UNINSTALL_TITLE'		=> JText::_('PLG_JSNADMINBAR_UNINSTALL_TITLE'),
			'JSN_ADMINBAR_UNINSTALL_CONFIRM' => JText::_('PLG_JSNADMINBAR_UNINSTALL_CONFIRM_MESSAGE'),
			'JSN_ADMINBAR_TIMEOUT_WARNING'	=> JText::_('PLG_JSNADMINBAR_USERMENU_TIMEOUT_WARNING'),

			'JSN_ADMINBAR_ADMINMENUS'		=> JText::_('PLG_JSNADMINBAR_SEARCH_COVERAGE_ADMINMENUS'),
			'JSN_ADMINBAR_PARENT_MENUS'		=> JText::_('PLG_JSNADMINBAR_PARENT_MENUS'),

			'JYES'							=> JText::_('JYES'),
			'JNO'							=> JText::_('JNO'),

			'JSAVE'							=> JText::_('JAPPLY'),
			'JCLOSE'						=> JText::_('PLG_JSNADMINBAR_CLOSE'),
			'JCANCEL'						=> JText::_('JCANCEL')
		);

		return json_encode($language);
	}

	/**
	 * Return parameters for client side as JSON format
	 * @return string
	 */
	private function _getJSConfiguration ()
	{
		$defaultStyles = $this->_getDefaultStyles();
		$installedComponents 	= PoweradminHelper::getInstalledComponents();
		$supportedExtList 		= JSNPaExtensionsHelper::getSupportedExtList();


		if (!$this->_params->get('search_coverage'))
		{
			$coverages	= PoweradminHelper::getSearchCoverages();
		}
		else
		{
			$coverages = json_decode($this->_params->get('search_coverage', PoweradminHelper::getSearchCoverages()));
		}

		foreach ($supportedExtList as $_supportedExt=>$value)
		{
			$supportedExtAlias	= str_replace('com_', JSN_3RD_EXTENSION_STRING . '-', $_supportedExt);
			$_extShortName		= str_ireplace('com_', '', $_supportedExt);
			$_plg = JPluginHelper::getPlugin('jsnpoweradmin', $_extShortName);

			if (in_array($_supportedExt, $installedComponents)
					&& !in_array($_supportedExt, $coverages)
					&& !count($_plg)
					&& !in_array($supportedExtAlias, explode(',', $this->_params->get('search_coverage_order'))))
			{
				array_push($coverages, JSN_3RD_EXTENSION_NOT_INSTALLED_STRING . '-' . $_extShortName);
			}

			if (in_array($_supportedExt, $installedComponents)
					&& count($_plg)
					&& !in_array($supportedExtAlias, explode(',', $this->_params->get('search_coverage_order')))
					)
			{
				array_push($coverages, JSN_3RD_EXTENSION_NOT_ENABLED_STRING . '-' . $_extShortName);
			}
		}

		$logoFile = $this->_params->get('logo_file', 'administrator/components/com_poweradmin/assets/images/logo-jsnpoweradmin.png');
		$logoFile = ($logoFile == 'N/A') ? '' :  JURI::root(true).'/'.$logoFile;

		$canInstall = $this->_user->authorise('core.manage', 'com_installer');

		// Get editors
		$curEditor = JFactory::getUser()->getParam('editor');
		$editorOptions = array();
		foreach ($this->_getEditorOptions() as $option) {
			$_isAtive =  $curEditor == $option->value ? true : false;
			$editorOptions[]  = array('value'=> $option->value, 'name'=> $option->text, 'active'=> $_isAtive);
		}

		$conf = array(
			'currentUrl'		=> $_SERVER["REQUEST_URI"],
			'baseUrl'			=> JURI::base(true).'/',
			'rootUrl'			=> JURI:: root(true).'/',
			'userId'			=> $this->_user->id,
			'protected'			=> $this->_getProtectedComponents(),
			'defaultStyles'		=> $defaultStyles,
			'logoFile'			=> $logoFile,
			'logoLink'			=> $this->_params->get('logo_link', 'http://www.joomlashine.com/joomla-extensions/jsn-poweradmin.html'),
			'logoLinkTarget'	=> $this->_params->get('logo_target', '_blank'),
			'logoTitle'			=> JText::_($this->_params->get('logo_slogan', JText::_('PLG_JSNADMINBAR_CONFIG_LOGO_SLOGAN_DEFAULT'))),
			'allowUninstall'	=> $this->_params->get('allow_uninstall', true) && $canInstall,

			'linkTarget'		=> $this->_target,

			'preloadImages'		=> array('bg-overlay.png', 'loader.gif', 'dark-loader.gif', 'ui-window-buttons.png'),

			// Admin bar configuration
			'pinned' 			=> $this->_params->get('pinned_bar', true),
			'sessionInfinite' 	=> $this->_params->get('admin_session_timer_infinite', false),
			'warningTime'		=> $this->_params->get('session_timeout_warning', 1),
			'disableWarning'	=> $this->_params->get('admin_session_timeout_warning_disabled', false),
			'searchCoverages'	=> $coverages,

			'sitemenu' => array(
				'preview' => $this->_preview->getPreviewLink(),
				'manager' => JRoute::_('index.php?option=com_poweradmin&view=rawmode', false),
			),

			'usermenu' => array(
				'messages'    => $this->_getMessagesCount(),
				'profileLink' => "index.php?option=com_admin&task=profile.edit&id={$this->_user->id}&tmpl=component",
				'messageLink' => "index.php?option=com_messages",
				'logoutLink'  => "index.php?option=com_login&task=logout&".JSession::getFormToken()."=1",
			),

			'history' => array(
				'url'	=> 'index.php?option=com_poweradmin&task=history.load',
			),

			'spotlight' => array(
				'limit'			=> $this->_params->get('search_result_num', 10),
			),

			'urlparams' => array(
				'option'		=> JRequest::getVar('option'),
				'task'			=> JRequest::getVar('task'),
				'view'			=> JRequest::getVar('view'),
				'layout'		=> JRequest::getVar('layout'),
				'id'			=> JRequest::getInt('id')

			),

			'editors' => $editorOptions
		);

		return json_encode($conf);
	}

	/**
	 * Get all edtior options
	 * @return array
	 * */
	private function _getEditorOptions()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('element AS value, name AS text');
		$query->from('#__extensions');
		$query->where('folder = ' . $db->quote('editors'));
		$query->where('enabled = 1');
		$query->order('ordering, name');

		// Set the query and load the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		$lang = JFactory::getLanguage();
		foreach ($options as $i => $option)
		{
			$lang->load('plg_editors_' . $option->value, JPATH_ADMINISTRATOR, null, false, false)
			|| $lang->load('plg_editors_' . $option->value, JPATH_PLUGINS . '/editors/' . $option->value, null, false, false)
			|| $lang->load('plg_editors_' . $option->value, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			|| $lang->load('plg_editors_' . $option->value, JPATH_PLUGINS . '/editors/' . $option->value, $lang->getDefault(), false, false);
			$options[$i]->text = JText::_($option->text);
		}

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}


	/**
	 * Retrieve all protected components
	 * @return array
	 */
	private function _getProtectedComponents ()
	{
		$dbo = JFactory::getDBO();
		$dbo->setQuery(
			$dbo->getQuery(true)
				->select('element')
				->from('#__extensions')
				->where('protected=1 AND type=\'component\'')
		);

		return $dbo->loadColumn();
	}

	/**
	 * Retrieve default styles
	 * @return array
	 */
	private function _getDefaultStyles ()
	{
		if (empty($this->_defaultStyles)) {
			$dbo = JFactory::getDbo();
			$dbo->setQuery(
				$dbo->getQuery(true)
					->select('id, client_id, title, template')
					->from('#__template_styles')
					->where('home=1')
			);

			foreach ($dbo->loadObjectList() as $template) {
				$this->_defaultStyles[$template->client_id == 1 ? 'admin' : 'site'] = $template;
			}
		}

		return $this->_defaultStyles;
	}

	/**
	 * Retrieve number of unread messages for current user
	 * @return int
	 * @author binhpt
	 */
	private function _getMessagesCount()
	{
		$dbo = JFactory::getDBO();
		$user = JFactory::getUser();

		$dbo->setQuery(
			$dbo->getQuery(true)
				->select('COUNT(*)')
				->from('#__messages')
				->where('state = 0 AND user_id_to = '.(int) $user->get('id'))
		);

		return (int)$dbo->loadResult();
	}

	/**
	 * Delete adminbar plugin from old version of poweradmin
	 * @return void
	 */
	private function _removeAdminBarPlugin ()
	{
		$dbo = JFactory::getDBO();
		$dbo->setQuery(
			$dbo->getQuery(true)
				->select('COUNT(*)')
				->from('#__extensions')
				->where('element="jsnadminbar"')
		);

		$hasPlugin = intval($dbo->loadResult()) > 0;
		if ($hasPlugin) {
			$pluginPath = JPATH_ROOT.'/plugins/system/jsnadminbar';
			$dbo->setQuery("DELETE FROM #__extensions WHERE element='jsnadminbar' LIMIT 1");
			if ($dbo->query()) {
				JFolder::delete($pluginPath);
			}
		}
	}
}





