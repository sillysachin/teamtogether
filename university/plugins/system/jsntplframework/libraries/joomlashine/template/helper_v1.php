<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JSNTplTemplateHelper
{
	private static $_instance;

	private $_templateName;
	private $_templateXml = array();
	private $_templateOptions = array();
	private $_defaultParams = array(
		// Logo settings
		'logoFile'			=> '',
		'logoLink'			=> 'index.php',
		'logoSlogan'		=> '',
		'logoColored'		=> false,

		// Layout settings
		'layoutWidth'		=> 'narrow',
		'layoutNarrowWidth'	=> 960,
		'layoutWideWidth'	=> 1150,
		'layoutFloatWidth'	=> 90,
		'showFrontpage'		=> true,

		// Mobile settings
		'mobileSupport'		=> true,
		'menuSticky'		=> true,
		'desktopSwitcher'	=> true,
		'mobileLogo'		=> '',

		// Color & Style settings
		'templateColor'		=> '',
		'templateStyle'		=> 'business',
		'textSize'			=> 'medium',
		'useSpecialFont'	=> true,
		'useCSS3Effect'		=> true,

		// Menu & Sitetools settings
		'mainMenuWidth'			=> 200,
		'sideMenuWidth'			=> 200,
		'sitetoolStyle'			=> 'menu',
		'textSizeSelector'		=> true,
		'widthSelector'			=> true,
		'colorSelector'			=> true,
		'sitetoolsColors'		=> array(),
		'sitetoolsColorsItems'	=> array(),

		// SEO & System settings
		'enableH1'				=> true,
		'gotoTop'				=> true,
		'autoIconLink'			=> false,
		'printOptimize'			=> false,
		'codePosition'			=> 0,
		'codeAnalytic'			=> '',
		'cssFiles'				=> '',
		'compression'			=> 0,
		'maxCompressionSize'	=> 100,
		'compressionExclude'	=> '',
		'cacheDirectory'		=> 'cache/',
		'useSqueezeBox'			=> false,
		'scriptMovement'		=> false
	);

	private $_overrideAttributes = array(
		'width'				=> array('type' => array('narrow', 'wide', 'float'), 'name' => 'layoutWidth'),
		'textstyle'			=> array('type' => array('business', 'personal', 'news'), 'name' => 'templateStyle'),
		'textsize'			=> array('type' => array('small', 'medium', 'big'), 'name' => 'textSize'),
		'direction'			=> array('type' => array('ltr', 'rtl'), 'name' => 'direction'),
		'color'				=> array('type' => 'string', 'name' => 'templateColor'),
		'specialfont'		=> array('type' => 'boolean', 'name' => 'useSpecialFont'),
		'mobile'			=> array('type' => 'boolean', 'name' => 'mobileView'),
		'preset'			=> ''
	);

	private static $_manifest;

	private $_loadTemplateCSS = true;
	private $_loadTemplateJS = true;

	/**
	 * Constructor
	 *
	 * @param   string  $name  Name of the template
	 */
	private function __construct ()
	{
		$this->_document = JFactory::getDocument();
	}

	/**
	 * Get active instance of template helper object
	 *
	 * @param   string  $name  Name of the template
	 *
	 * @return  JSNTplTemplateHelper
	 */
	public static function getInstance ()
	{
		if (self::$_instance == null) {
			self::$_instance = new JSNTplTemplateHelper();
		}

		return self::$_instance;
	}

	/**
	 * Alias of _prepare method
	 *
	 * @return  void
	 */
	public static function prepare ($loadTemplateCss = true, $loadTemplateJs = true)
	{
		self::getInstance()->_prepare($loadTemplateCss, $loadTemplateJs);
	}

	/**
	 * Load template parameters
	 *
	 * @param   array    $override  Parameters override
	 *
	 * @return  array
	 */
	public function loadParams ($override = null, $templateName)
	{
		if (!is_array($override)) {
			$override = array();
		}

		$xml			= JSNTplHelper::getManifest($templateName);
		$templateUrl	= JUri::root(true) . 'templates/' . $templateName;

		// Load default template Parameters
		foreach ($xml->xpath('//defaults/option') as $parameter)
		{
			$name	= (string) $parameter['name'];
			$value	= (string) $parameter['value'];

			if ($name == 'cssFiles') {
				$files = array();

				foreach ($parameter->children() as $item) {
					$files[] = (string) $item;
				}

				$this->_defaultParams[$name] = implode("\r\n", $files);
				continue;
			}

			$type = isset($this->_defaultParams[$name]) ? gettype($this->_defaultParams[$name]) : 'string';
			$this->_defaultParams[$name] = $this->convertType($value, $type);

			if ($type == 'string') {
				$this->_defaultParams[$name] = str_replace('{templateUrl}', $templateUrl, $this->_defaultParams[$name]);
			}
		}

		if (!isset($this->_defaultParams['logoFile']) || empty($this->_defaultParams['logoFile'])) {
			$this->_defaultParams['logoFile'] = 'templates/' . $templateName . '/images/logo.png';
		}

		return array_merge($this->_defaultParams, $override);
	}

	/**
	 * Calculate the number of modules in positions
	 *
	 * @param   array  $positions  Positions to get number of modules
	 * @return  int
	 */
	public function countPositions ()
	{
		$this->_document	= JFactory::getDocument();
		$positionCount		= 0;

		foreach (func_get_args() as $position) {
			if ($this->_document->countModules($position)) {
				$positionCount++;
			}
		}

		return $positionCount;
	}

	/**
	 * Preparing template parameters for the template
	 *
	 * @return  void
	 */
	private function _prepare ($loadTemplateCSS, $loadTemplateJS)
	{
		$this->_loadTemplateCSS	= $loadTemplateCSS;
		$this->_loadTemplateJS	= $loadTemplateJS;

		$templateParams	= isset($this->_document->params)	? $this->_document->params		: null;
		$templateName	= isset($this->_document->template)	? $this->_document->template	: null;

		if (empty($templateParams) OR empty($templateName) OR $templateName == 'system')
		{
			$templateDetails	= JFactory::getApplication()->getTemplate(true);
			$templateParams		= $templateDetails->params;
			$templateName		= $templateDetails->template;
		}

		// Update show content on frontpage parameter
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu()->getActive();

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_jsntplframework', JPATH_ADMINISTRATOR);

		$manifest = JSNTplHelper::getManifest($templateName);

		$this->_document->app			= JFactory::getApplication();
		$this->_document->template		= $templateName;
		$this->_document->version		= JSNTplHelper::getTemplateVersion($templateName);
		$this->_document->isFree		= !isset($manifest->edition) || $manifest->edition == 'FREE';

		$this->_document->uri			= JFactory::getUri();
		$this->_document->rootUrl		= $this->_document->uri->root(true);
		$this->_document->templateUrl	= $this->_document->rootUrl . '/templates/' . $this->_document->template;

		$columns = array(
			'columnPromoLeft',
			'columnPromoRight',
			'columnLeft',
			'columnRight',
			'columnInnerleft',
			'columnInnerright'
		);

		// Find customizable columns
		$customColumns = $manifest->xpath('//fieldset[@name="jsn-columns-size"]');

		if (count($customColumns) > 0) {
			$columns = array();
			foreach (end($customColumns)->children() as $column) {
				$columns[] = (string) $column['name'];
			}
		}

		// Add columns to overriable parameter list
		foreach ($columns as $column) {
			$className = $column;
			if (strpos($column, 'column') === 0) {
				$className = substr($column, 6);
			}

			$this->_overrideAttributes[strtolower($className . 'width')] = array('type' => 'integer', 'name' => $column);
		}

		// Load template parameters
		$params = $this->loadParams($templateParams->toArray(), $templateName, true);

		// Detect browser information
		$this->_document->browserInfo	= JSNTplUtils::getInstance()->getBrowserInfo();
		$this->_document->isIE			= @$this->_document->browserInfo['browser'] == 'msie';
		$this->_document->isIE7			= @$this->_document->browserInfo['browser'] == 'msie' && (int) @$this->_document->browserInfo['version'] == 7;

		// Custom direction from url parameter
		$direction = JFactory::getApplication()->input->getCmd('jsn_setdirection', $this->_document->direction);
		$this->_document->direction = $direction;

		// Apply custom params
		$params = $this->_overrideCustomParams($params);
		$params['showFrontpage'] = (is_object($menu) && $menu->home == 1) ? $params['showFrontpage'] == 1 : true;

		if ($this->_document->isFree === true) {
			$params['mobileSupport'] = false;
			$params['useCSS3Effect'] = false;
		}

		// Prepare logo parameter
		if ($params['logoColored']) {
			$params['logoFile'] = "templates/{$templateName}/images/colors/{$params['templateColor']}/logo.png";
		}

		if ($params['mobileSupport'] == false) {
			$params['desktopSwitcher'] = false;
		}

		if (!preg_match('/^[a-zA-Z]+:\/\//i', $params['logoFile'])) {
			$params['logoFile'] = JUri::root(true) . '/' . $params['logoFile'];
		}

		// Prepare color variation to show in site tool
		if ($params['colorSelector'] AND ! @count($params['sitetoolsColorsItems']))
		{
			$params['sitetoolsColorsItems'] = $manifest->xpath('//*[@name="sitetoolsColors"]/option');

			if ( ! $params['sitetoolsColorsItems'] OR ! @count($params['sitetoolsColorsItems']))
			{
				$xml = simplexml_load_file(JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/template/params.xml');
				$params['sitetoolsColorsItems'] = $xml->xpath('//*[@name="sitetoolsColors"]/option');
			}

			foreach ($params['sitetoolsColorsItems'] AS & $color)
			{
				$color = (string) $color['value'];
			}
		}

		// Prepare Google Analytics code
		$params['codeAnalytic'] = trim($params['codeAnalytic']);

		if ( ! empty($params['codeAnalytic']))
		{
			if (strpos($params['codeAnalytic'], '<script') === false)
			{
				$params['codeAnalytic'] = '<script type="text/javascript">' . $params['codeAnalytic'];
			}

			if (strpos($params['codeAnalytic'], '</script>') === false)
			{
				$params['codeAnalytic'] = $params['codeAnalytic'] . '</script>';
			}
		}

		// Binding parameters to document object
		$this->_document->params = new JRegistry();

		foreach ($params as $key => $value) {
			$this->_document->params->set($key, $value);
			$this->_document->{$key} = $value;
		}

		// Assign helper object
		$this->_document->helper			= $this;

		$this->_document->attributes		= $this->_overrideAttributes;
		$this->_document->templatePrefix	= $this->_document->template . '_';

		// Prepare body class
		$this->_prepareBodyClass();

		// Prepare template styles
		$this->_prepareHead();
	}

	/**
	 * Generate class for body element
	 *
	 * @return  string
	 */
	private function _prepareBodyClass ()
	{
		// Generate body class
		$bodyClass = array();
		$bodyClass[] = "jsn-textstyle-{$this->_document->templateStyle}";
		$bodyClass[] = "jsn-textsize-{$this->_document->textSize}";
		$bodyClass[] = "jsn-color-{$this->_document->templateColor}";
		$bodyClass[] = "jsn-direction-{$this->_document->direction}";

		// Special font class
		if ($this->_document->useSpecialFont) $bodyClass[] = "jsn-specialfont";

		// CSS3 class
		if ($this->_document->useCSS3Effect) $bodyClass[] = "jsn-css3";
		if ($this->_document->direction == 'rtl') $bodyClass[] = "jsn-direction-rtl";

		// Desktop style on mobile
		$mobileSupport = $this->_document->mobileSupport;
		if (isset($this->_document->mobileView) && $this->_document->mobileView == false) {
			$this->_document->mobileSupport = false;
		}

		// Mobile/desktop class
		$bodyClass[] = $this->_document->mobileSupport ? 'jsn-mobile' : 'jsn-desktop';
		$bodyClass[] = implode(' ', $this->_getPageClass());
		$bodyClass[] = $this->_isJoomla3() ? 'jsn-joomla-30' : 'jsn-joomla-25';

		if (isset($this->_document->mobileView) && $this->_document->mobileView == false && $mobileSupport == true) {
			$bodyClass[] = 'jsn-desktop-on-mobile';
		}

		// Add custom class based from query string
		$input = $this->_document->app->input;
		$option = substr($input->getCmd('option'), 4);
		$bodyClass[] = "jsn-com-{$option}";

		// View parameter
		$view = $input->getCmd('view');

		if (!empty($view)) {
			$bodyClass[] = "jsn-view-{$view}";
		}

		// Layout parameter
		$layout = $input->getCmd('layout');

		if (!empty($layout)) {
			$bodyClass[] = "jsn-layout-{$layout}";
		}

		// Itemid parameter
		$itemid = $input->getInt('Itemid');

		if (!empty($itemid)) {
			$bodyClass[] = "jsn-itemid-{$itemid}";
		}

		if (is_object($this->_document->activeMenu) && $this->_document->activeMenu->home == 1) {
			$bodyClass[] = 'jsn-homepage';
		}

		// Set body class to document object
		$this->_document->bodyClass = implode(' ', $bodyClass);
	}

	/**
	 * Preparing head section for the template
	 *
	 * @return void
	 */
	private function _prepareHead ()
	{
		// Only continue if requested return format is html
		if (strcasecmp(get_class($this->_document), 'JDocumentHTML') != 0)
		{
			return;
		}

		// Load Joomla script framework
		JHTML::_('behavior.framework', true);

		if ($this->_loadTemplateCSS == true)
		{
			if ($this->_isJoomla3())
			{
				// Add JavaScript Frameworks
				JHtml::_('bootstrap.framework');

				// Add Css Frameworks
				JHtmlBootstrap::loadCss();

				// Load optional rtl Bootstrap css and Bootstrap bugfixes
				JHtmlBootstrap::loadCss(true, $this->_document->direction);
			}

			// Print optimize
			if ($this->_document->printOptimize)
			{
				$this->_document->addStylesheet($this->_document->templateUrl . "/css/print.css", 'text/css', 'Print');
			}

			// Load general styles
			$this->_document->addStylesheet($this->_document->rootUrl . '/templates/system/css/system.css');
			$this->_document->addStylesheet($this->_document->rootUrl . '/templates/system/css/general.css');
			$this->_document->addStylesheet($this->_document->templateUrl . '/css/template.css');

			// Load PRO template styles
			if (substr($this->_document->templateUrl, -4) == '_pro')
			{
				if (is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/template_pro.css'))
				{
					$this->_document->addStylesheet($this->_document->templateUrl . '/css/template_pro.css');
				}
			}

			// Load customization styles
			$this->_document->addStylesheet($this->_document->templateUrl . '/css/colors/' . $this->_document->templateColor . '.css');
			$this->_document->addStylesheet($this->_document->templateUrl . '/css/styles/' . $this->_document->templateStyle . '.css');

			// Auto icon link stylesheet
			if ($this->_document->autoIconLink)
			{
				$this->_document->addStylesheet($this->_document->templateUrl . '/css/jsn_iconlinks.css');
			}

			// Right to left stylesheet
			if ($this->_document->direction == "rtl")
			{
				$this->_document->addStylesheet($this->_document->templateUrl . '/css/jsn_rtl.css');
			}

			// Mobile support stylesheet
			if ($this->_document->mobileSupport)
			{
				$this->_document->addCustomTag('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />');
				$this->_document->addStylesheet($this->_document->templateUrl . '/css/jsn_mobile.css');
			}

			// CSS3 effect stylesheet
			if ($this->_document->useCSS3Effect)
			{
				$this->_document->addStylesheet($this->_document->templateUrl . '/css/jsn_css3.css');
			}

			// IE7 Specific stylesheet
			if ($this->_document->isIE7)
			{
				$this->_document->addStylesheet($this->_document->rootUrl . '/css/jsn_fixie7.css');
			}

			// Squeezebox stylesheet
			if ($this->_document->useSqueezeBox)
			{
				$this->_document->addStylesheet($this->_document->rootUrl . '/media/system/css/modal.css');
			}

			// Template width
			$unit = $this->_document->layoutWidth == 'float' ? '%' : 'px';
			$this->_document->templateWidth = $this->_document->{'layout' . ucfirst($this->_document->layoutWidth) . 'Width'} . $unit;

			// Load custom css that declared by the template
			if (is_file(JPATH_ROOT . "/templates/{$this->_document->template}/template_custom.php"))
			{
				include_once JPATH_ROOT . "/templates/{$this->_document->template}/template_custom.php";
			}

			// Load custom css files from the parameter
			foreach (explode("\n", $this->_document->cssFiles) AS $file)
			{
				if (empty($file))
				{
					continue;
				}

				preg_match('/^[a-z]+:\/\//i', $file)
					? $this->_document->addStylesheet(trim($file))
					: $this->_document->addStylesheet($this->_document->templateUrl . '/css/' . trim($file));
			}
		}

		if ($this->_loadTemplateJS == true)
		{
			// Load Javascript files
			$this->_document->addScript($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/joomlashine/js/noconflict.js');
			$this->_document->addScript($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/joomlashine/js/utils.js');
			$this->_document->addScript($this->_document->templateUrl . '/js/jsn_template.js');

			// Load javascript file for squeezebox
			if ($this->_document->useSqueezeBox)
			{
				$this->_document->addScript($this->_document->rootUrl . '/media/system/js/modal.js');
			}

			// Custom template JS declarations
			$this->_document->addScriptDeclaration('
				JSNTemplate.initTemplate({
					templatePrefix			: "' . $this->_document->template . '_",
					templatePath			: "' . $this->_document->rootUrl . '/templates/' . $this->_document->template . '",
					enableRTL				: ' . ($this->_document->direction == "rtl" ? 'true' : 'false') . ',
					enableGotopLink			: ' . ($this->_document->gotoTop ? 'true' : 'false') . ',
					enableMobile			: ' . (((isset($this->_document->mobileView) AND $this->_document->mobileView) OR $this->_document->mobileSupport) ? 'true' : 'false') . ',
					enableMobileMenuSticky	: ' . ($this->_document->menuSticky ? 'true' : 'false') .'
				});
			');

			// load Squeezebox
			if ($this->_document->useSqueezeBox)
			{
				$this->_document->addScriptDeclaration('
					window.addEvent("domready", function() {
						SqueezeBox.initialize({});
						SqueezeBox.assign($$("a.modal"), {
							parse: "rel"
						});
					});
				');
			}
		}
	}

	/**
	 * Retrieve parameter pageclass_sfx from active menu
	 *
	 * @return string
	 */
	private function _getPageClass ()
	{
		$pageClass		= '';
		$notHomePage	= true;
		$menus			= $this->_document->app->getMenu();
		$menu			= $menus->getActive();
		$this->_document->activeMenu = $menu;

		if (is_object($menu))
		{
			// Set page class suffix
			$params = JMenu::getInstance('site')->getParams($menu->id);
			$pageClass = $params->get('pageclass_sfx', '');

			// Set homepage flag
			$lang = JFactory::getLanguage();
			$defaultMenu = $menus->getDefault($lang->getTag());

			if (is_object($defaultMenu)) {
				$notHomePage = ($menu->id != $defaultMenu->id);
			}
		}

		return explode(' ', $pageClass);
	}

	/**
	 * Override parameters with value retrieved from cookie and body class
	 *
	 * @param   array  $params  Template parameters to override
	 *
	 * @return  array
	 */
	private function _overrideCustomParams ($params)
	{
		$pageClass = $this->_getPageClass();
		$cookieName = $this->_document->template . '_params';

		// Override params from page class
		foreach ($pageClass as $class)
		{
			if (strpos($class, 'custom-') !== false && substr_count($class, '-') >= 2)
			{
				list($prefix, $param, $value) = explode('-', $class);

				if (!isset($this->_overrideAttributes[$param])) {
					continue;
				}

				// Update custom parameter
				$attribute = $this->_overrideAttributes[$param];

				// Checking attribute type
				if (is_array($attribute['type']) && in_array($value, $attribute['type'])) {
					$params[$attribute['name']] = $value;
				}
				elseif ($attribute['type'] == 'integer' && is_numeric($value)) {
					$params[$attribute['name']] = intval($value);
				}
				else {
					$params[$attribute['name']] = trim($value);
				}
			}
		}

		// Check template cookie is existing
		if (isset($_COOKIE[$cookieName]))
		{
			// Prepare cookie value
			if (get_magic_quotes_runtime() || get_magic_quotes_gpc())
			{
				$_COOKIE[$cookieName] = stripslashes($_COOKIE[$cookieName]);
			}

			// Fix known conflict with MijoShop
			if (strpos($_COOKIE[$cookieName], '&quot;') !== false)
			{
				$_COOKIE[$cookieName] = html_entity_decode($_COOKIE[$cookieName]);
			}

			// Decode template cookie
			$cookieParams = json_decode($_COOKIE[$cookieName], true);

			if (!empty($cookieParams) && is_array($cookieParams))
			{
				// Override template parameters
				foreach ($cookieParams as $key => $value)
				{
					if (isset($this->_overrideAttributes[$key])) {
						$attribute = $this->_overrideAttributes[$key];

						// Checking attribute type
						if (is_array($attribute['type']) && in_array($value, $attribute['type']))
							$params[$attribute['name']] = $value;
						elseif ($attribute['type'] == 'integer' && is_numeric($value))
							$params[$attribute['name']] = intval($value);
						elseif ($attribute['type'] == 'boolean')
							$params[$attribute['name']] = $value == 'yes';
						else
							$params[$attribute['name']] = $value;
					}
				}
			}
		}

		return $params;
	}

	/**
	 * Convert data to specified type
	 *
	 * @param   mixed   $data  Data to be converted
	 * @param   string  $type  Type of the data after converted
	 *
	 * @return  mixed
	 */
	protected function convertType ($data, $type)
	{
		switch ($type) {
			case 'string':
				$data = (string) $data;
			break;

			case 'integer':
				$data = (int) $data;
			break;

			case 'float':
				$data = (float) $data;
			break;

			case 'boolean':
				$data = $data == 'true' || $data == 'yes' || $data == '1';
			break;
		}

		return $data;
	}

	/**
	 * Return true if current joomla version is 3.0
	 *
	 * @return  boolean
	 */
	private function _isJoomla3 ()
	{
		if (!isset($this->isJoomla3)) {
			$version = new JVersion();
			$this->isJoomla3 = version_compare($version->getShortVersion(), '3.0', '>=');
		}

		return $this->isJoomla3;
	}
}
