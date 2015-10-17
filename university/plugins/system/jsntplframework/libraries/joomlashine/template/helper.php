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

// Import necessary libraries
jimport('joomla.filesystem.file');

class JSNTplTemplateHelper
{
	private static $_instance;

	private $_templateName;
	private $_templateXml = array();
	private $_templateOptions = array();

	private $_defaultParams = array(
		// Logo settings
		'logoFile'		=> '',
		'mobileLogo'	=> '',
		'logoLink'		=> 'index.php',
		'logoSlogan'	=> '',
		'logoColored'	=> false,

		// Layout settings
		'templateWidth'		=> array('type' => 'fixed', 'fixed' => 960, 'float' => 90, 'responsive' => array('mobile', 'wide')),
		'showFrontpage'		=> true,
		'desktopSwitcher'	=> true,
		'promoColumns'		=> array('promo-left' => 'span3', 'promo' => 'span6', 'promo-right' => 'span3'),
		'mainColumns'		=> array('left' => 'span3', 'content' => 'span6', 'right' => 'span3'),
		'contentColumns'	=> array('innerleft' => 'span3', 'component' => 'span6', 'innerright' => 'span3'),
		'userColumns'		=> array('user5' => 'span4', 'user6' => 'span4', 'user7' => 'span4'),

		// Styling settings
		'templateColor'	=> '',
		'fontStyle'		=> array('style' => 'business'),

		// Menu & Sitetools settings
		'mainMenuWidth'			=> 200,
		'sideMenuWidth'			=> 200,
		'menuSticky'			=> array('mobile' => true, 'desktop' => false),
		'sitetoolStyle'			=> 'menu',
		'sitetoolsColors'		=> '{"list":["blue","red","green","violet","orange","grey"],"colors":["blue","red","green","violet","orange","grey"]}',

		// SEO & System settings
		'gotoTop'				=> true,
		'autoIconLink'			=> false,
		'printOptimize'			=> false,
		'socialIcons'			=> array('status' => array('facebook', 'twitter', 'youtube')),
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
		'color'		=> array('type' => 'string', 'name' => 'templateColor'),
		'direction'	=> array('type' => array('ltr', 'rtl'), 'name' => 'direction'),
		'mobile'	=> array('type' => 'boolean', 'name' => 'mobileView'),
		'textstyle'	=> array('type' => array('business', 'personal', 'news'), 'name' => 'fontStyle', 'key' => 'style')
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
		if ( ! isset(self::$_instance))
		{
			self::$_instance = new JSNTplTemplateHelper;
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
	 * @param   array   $override      Parameters override.
	 * @param   string  $templateName  Template name.
	 *
	 * @return  array
	 */
	public function loadParams ($override = null, $templateName)
	{
		static $loadedParams;

		if ( ! isset($loadedParams) OR ! isset($loadedParams[$templateName]))
		{
			if ( ! is_array($override))
			{
				$override = array();
			}

			$xml			= JSNTplHelper::getManifest($templateName, true);
			$templateUrl	= JUri::root(true) . 'templates/' . $templateName;

			// Load default template parameters
			foreach ($xml->xpath('//defaults/option') AS $parameter)
			{
				$name	= (string) $parameter['name'];
				$value	= (string) $parameter['value'];

				// Ignore v1 parameter
				if ($name == 'templateStyle')
				{
					continue;
				}

				if ($name == 'cssFiles')
				{
					$files = array();

					foreach ($parameter->children() AS $item)
					{
						$files[] = (string) $item;
					}

					$this->_defaultParams[$name] = implode("\r\n", $files);

					continue;
				}

				if (@count($parameter->children()))
				{
					$value = array();

					foreach ($parameter->children() AS $child)
					{
						if (isset($child['name']))
						{
							$key = (string) $child['name'];

							if (substr($key, -2) == '[]')
							{
								$key = substr($key, 0, -2);

								$value[$key][] = isset($child['value']) ? (string) $child['value'] : (string) $child;
							}
							else
							{
								$value[$key] = isset($child['value']) ? (string) $child['value'] : (string) $child;
							}
						}
						else
						{
							$value[] = isset($child['value']) ? (string) $child['value'] : (string) $child;
						}
					}
				}

				$type = isset($this->_defaultParams[$name]) ? gettype($this->_defaultParams[$name]) : 'string';
				$this->_defaultParams[$name] = $this->convertType($value, $type);

				if ($type == 'string')
				{
					$this->_defaultParams[$name] = str_replace('{templateUrl}', $templateUrl, $this->_defaultParams[$name]);
				}
			}

			if ( ! isset($this->_defaultParams['logoFile']) OR empty($this->_defaultParams['logoFile']))
			{
				$this->_defaultParams['logoFile'] = 'templates/' . $templateName . '/images/logo.png';
			}

			// Store finalized parameters
			$loadedParams[$templateName] = array_merge($this->_defaultParams, $override);

			// Migrate v1 parameters to v2 if necessary
			if (JSNTplVersion::isCompatible($templateName, JSNTplHelper::getTemplateVersion($templateName)))
			{
				$loadedParams[$templateName] = JSNTplTemplateMigration::migrate($loadedParams[$templateName]);
			}
		}

		return $loadedParams[$templateName];
	}

	/**
	 * Calculate the number of modules in positions.
	 *
	 * @return  int
	 */
	public function countPositions()
	{
		$utils = JSNTplUtils::getInstance();

		return call_user_func_array(array($utils, 'countPositions'), func_get_args());
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

		// Get template information
		$this->_template = JSNTplTemplateRecognization::detect($templateName);

		// Update show content on frontpage parameter
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu()->getActive();

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_jsntplframework', JPATH_ADMINISTRATOR);

		$manifest = JSNTplHelper::getManifest($templateName);

		$this->_document->app			= JFactory::getApplication();
		$this->_document->template		= $templateName;
		$this->_document->version		= JSNTplHelper::getTemplateVersion($templateName);
		$this->_document->isFree		= empty($manifest->edition) || $manifest->edition == 'FREE';

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

		if (count($customColumns) > 0)
		{
			$columns = array();

			foreach (end($customColumns)->children() AS $column)
			{
				$columns[] = (string) $column['name'];
			}
		}

		// Add columns to overriable parameter list
		foreach ($columns AS $column)
		{
			$className = $column;

			if (strpos($column, 'column') === 0)
			{
				$className = substr($column, 6);
			}

			$this->_overrideAttributes[strtolower($className . 'width')] = array('type' => 'string', 'name' => $column);
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

		// Prepare logo parameter
		if ($params['logoColored'])
		{
			$params['logoFile'] = "templates/{$templateName}/images/colors/{$params['templateColor']}/logo.png";
		}

		if ( ! empty($params['logoFile']) AND ! preg_match('/^[a-zA-Z]+:\/\//i', $params['logoFile']))
		{
			$params['logoFile'] = JUri::root(true) . '/' . $params['logoFile'];
		}

		// Prepare color variation to show in site tool
		if ( ! isset($params['sitetoolsColorsItems']) AND $colorSettings = json_decode($params['sitetoolsColors']))
		{
			if ( ! count($colorSettings->colors))
			{
				$params['sitetoolsColorsItems'] = array();
			}
		}

		if ($params['sitetoolsColors'] AND ! isset($params['sitetoolsColorsItems']))
		{
			$params['sitetoolsColorsItems'] = $manifest->xpath('//*[@name="sitetoolsColors"]/option');

			if ( ! $params['sitetoolsColorsItems'] OR ! @count($params['sitetoolsColorsItems']))
			{
				$xml = simplexml_load_file(JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/template/params.xml');
				$params['sitetoolsColorsItems'] = $xml->xpath('//*[@name="sitetoolsColors"]/option');
			}

			if ($params['sitetoolsColorsItems'])
			{
				foreach ($params['sitetoolsColorsItems'] AS & $color)
				{
					$color = (string) $color['value'];
				}
			}

			$params['sitetoolsColorsItems'] != false OR $params['sitetoolsColorsItems'] = array();
		}

		$params['colorSelector'] = count($params['sitetoolsColorsItems']) ? true : false;

		// Check if site tools has tool to show
		if ($params['sitetoolStyle'])
		{
			$visible = count($params['sitetoolsColorsItems']);

			if ( ! $visible)
			{
				$params['sitetoolStyle'] = false;
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

		// Check if user specified custom width for any column
		foreach ($columns AS $column)
		{
			if (isset($params[$column]))
			{
				// Re-generate column name
				$columnName = str_replace('column-', '', strtolower(preg_replace('/([A-Z])/', '-\\1', $column)));

				foreach (array('promoColumns', 'mainColumns', 'contentColumns') AS $row)
				{
					foreach ($params[$row] AS $id => $class)
					{
						// Re-fine ID
						$realId = preg_replace('/^\d+:/', '', $id);

						if (str_replace('-', '', $realId) == str_replace('-', '', $columnName))
						{
							if (strcasecmp(substr($params[$column], 0, 4), 'span') != 0)
							{
								// Convert framework v1 value type to compatible with framework v2
								$span = round($params[$column] / (100 / 12));
							}
							else
							{
								$span = intval(substr($params[$column], 4));
							}

							// Alter current parameter value
							$currentSpan = intval(substr($class, 4));

							if ($currentSpan != $span)
							{
								$params[$row][$id] = "span{$span}";

								foreach ($params[$row] AS $id => $class)
								{
									if (preg_match('/(\d+:)?(promo|content|component)/', $id))
									{
										$params[$row][$id] = 'span' . (intval(substr($class, 4)) + ($currentSpan - $span));

										// Done altering
										break;
									}
								}
							}

							// Done altering
							break(2);
						}
					}
				}
			}
		}

		// Process column width
		if (strcasecmp(get_class($this->_document), 'JDocumentHTML') == 0)
		{
			$utils = JSNTplUtils::getInstance();

			foreach (array('promoColumns', 'mainColumns', 'contentColumns', 'userColumns') AS $row)
			{
				$visible = count($params[$row]);
				$spacing = 0;
				$columns = array();

				foreach ($params[$row] AS $id => $class)
				{
					// Re-fine ID
					$realId = preg_replace('/^\d+:/', '', $id);

					// Detect the visibility of this column
					if ( ! in_array($realId, array('content', 'component')) AND ! $utils->countModules($realId))
					{
						$visible--;
						$spacing += intval(str_replace('span', '', $class));
						$columns[$id] = 0;
					}
					else
					{
						$columns[$id] = 1;
					}
				}

				// Expand visible columns if neccessary
				if ($visible < count($params[$row]))
				{
					foreach ($columns AS $id => $status)
					{
						if ( ! $status)
						{
							// Column is invisible, unset data
							unset($params[$row][$id]);
						}
						elseif ($visible > 0)
						{
							// Alter column spanning
							if (count($columns) > 3)
							{
								$params[$row][$id] = preg_replace('/span\d+/i', 'span' . (12 / $visible), $params[$row][$id]);
							}
							elseif ($visible == 1)
							{
								$params[$row][$id] = preg_replace('/span\d+/i', 'span12', $params[$row][$id]);
							}
						}
					}

					if (count($columns) == 3 AND $visible == 2)
					{
						// Sort columns to ensure correct source code order
						ksort($columns);

						$ordering = array_keys($columns);

						// Always expand main column if left or right is invisible
						if ($columns[$ordering[0]] AND $row != 'userColumns')
						{
							$span = intval(str_replace('span', '', $params[$row][$ordering[0]]));

							$params[$row][$ordering[0]] = preg_replace('/span\d+/i', 'span' . ($span + $spacing), $params[$row][$ordering[0]]);
						}
						// If main column is invisible then expand both columns
						else
						{
							foreach ($ordering AS $key)
							{
								if ($columns[$key])
								{
									$params[$row][$key] = preg_replace('/span\d+/i', 'span6', $params[$row][$key]);
								}
							}
						}
					}
				}

				// Set visual column ordering
				$columns = array();
				$ordering = 0;

				foreach ($params[$row] AS $id => $class)
				{
					// Add class to indicate the order of this column
					$params[$row][$id] .= ' order' . $ordering++;
				}

				// Sort columns for correct source code ordering
				ksort($params[$row]);

				foreach ($params[$row] AS $id => $class)
				{
					// Store data for processing visual ordering later
					$columns[] = array('id' => $id, 'class' => $class);
				}

				// Process visual ordering for visible columns
				foreach ($columns AS $ordering => $column)
				{
					$visualOrdering = intval(preg_replace('/^.*order(\d+).*$/', '\\1', $column['class']));
					$offset = 0;

					if ($ordering < $visualOrdering)
					{
						for ($i = $ordering + 1; $i < $visible; $i++)
						{
							$nextOrdering = intval(preg_replace('/^.*order(\d+).*$/', '\\1', $columns[$i]['class']));

							if (($ordering == 0 OR $nextOrdering > 0) AND $nextOrdering < $visualOrdering)
							{
								$offset += intval(preg_replace('/^.*span(\d+).*$/', '\\1', $columns[$i]['class']));
							}
						}
					}
					elseif ($ordering > 0 AND $ordering == $visualOrdering AND $ordering + 1 < $visible)
					{
						for ($i = 0; $i < $ordering; $i++)
						{
							if (preg_match('/offset\d+/', $columns[$i]['class']))
							{
								$offset -= intval(preg_replace('/^.*span(\d+).*$/', '\\1', $columns[$i]['class']));
							}
						}

						if ($offset < 0)
						{
							$offset -= intval(preg_replace('/^.*span(\d+).*$/', '\\1', $columns[$ordering]['class']));
						}
					}
					elseif ($ordering > $visualOrdering)
					{
						for ($i = 0; $i < $ordering; $i++)
						{
							$prevOrdering = intval(preg_replace('/^.*order(\d+).*$/', '\\1', $columns[$i]['class']));

							if ($prevOrdering > $visualOrdering)
							{
								if (preg_match('/offset\d+/', $columns[$i]['class']))
								{
									$offset -= intval(preg_replace('/^.*span(\d+).*$/', '\\1', $columns[$i]['class']));
									$offset -= intval(preg_replace('/^.*offset(\d+).*$/', '\\1', $columns[$i]['class']));
								}
								elseif (strpos($columns[$i]['class'], 'offset-') === false)
								{
									$offset -= intval(preg_replace('/^.*span(\d+).*$/', '\\1', $columns[$i]['class']));
								}
							}
							elseif ($i > 0 AND $prevOrdering < $visualOrdering)
							{
								$offset += intval(preg_replace('/^.*span(\d+).*$/', '\\1', $columns[$i]['class']));
							}
						}
					}

					// Set offset so the column display in correct visual ordering
					if ($offset != 0)
					{
						$columns[$ordering]['class'] = preg_replace('/(order\d+)/', "\\1 offset{$offset}", $columns[$ordering]['class']);
					}
				}

				// Update column IDs and classes
				foreach ($columns AS $column)
				{
					unset($params[$row][$column['id']]);

					// Re-fine ID
					$realId = preg_replace('/^\d+:/', '', $column['id']);

					// Re-fine column order
					$ordering = intval(preg_replace('/^.*order(\d+).*$/', '\\1', $column['class'])) + 1;

					// Split classes to span, order and offset
					$classes = explode(' ', preg_replace('/order\d+/', "order{$ordering}", $column['class']));

					// Reset column data
					$params[$row][$realId] = array(
						'span' => $classes[0],
						'order' => isset($classes[1]) ? $classes[1] : '',
						'offset' => isset($classes[2]) ? $classes[2] : ''
					);
				}
			}

			// Prepare social icons
			$socialIcons = array();

			foreach ((array) @$params['socialIcons']['status'] AS $channel)
			{
				// Set default value
				if (@empty($params['socialIcons'][$channel]['link']) AND in_array($channel, array('facebook', 'twitter', 'youtube')))
				{
					if ( ! @isset($params['socialIcons'][$channel]['title']))
					{
						$params['socialIcons'][$channel]['title'] = JText::_('JSN_TPLFW_SOCIAL_NETWORK_INTEGRATION_' . strtoupper($channel));
					}

					$params['socialIcons'][$channel]['link'] = "http://www.{$channel}.com/joomlashine";
				}

				if ( ! @empty($params['socialIcons'][$channel]['link']))
				{
					$socialIcons[$channel] = $params['socialIcons'][$channel];
				}
			}
		}

		$params['socialIcons'] = $socialIcons;

		// Backward compatible: set templateStyle parameter as it still be used in component output only template file
		$params['templateStyle'] = $params['fontStyle']['style'];

		// Binding parameters to document object
		$this->_document->params = new JRegistry();

		foreach ($params AS $key => $value)
		{
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

		$bodyClass[] = "jsn-textstyle-{$this->_document->fontStyle['style']}";
		$bodyClass[] = "jsn-color-{$this->_document->templateColor}";
		$bodyClass[] = "jsn-direction-{$this->_document->direction}";

		// Desktop style on mobile
		$mobileSupport = $this->_document->mobileSupport = false;

		if ($this->_document->templateWidth['type'] == 'responsive')
		{
			$bodyClass[] = 'jsn-responsive';

			// Get enabled layout
			$enabledLayout = $this->_document->templateWidth[$this->_document->templateWidth['type']];

			if (in_array('mobile', $enabledLayout))
			{
				$mobileSupport = $this->_document->mobileSupport = true;
			}
			else
			{
				// Turn off desktop/mobile switcher
				$this->_document->desktopSwitcher = false;
			}
		}

		if (isset($this->_document->mobileView) AND ! $this->_document->mobileView)
		{
			$this->_document->mobileSupport = false;
		}

		if (isset($this->_document->mobileView) AND ! $this->_document->mobileView AND $mobileSupport)
		{
			$bodyClass[] = 'jsn-desktop-on-mobile';
		}

		// Mobile/desktop class
		$bodyClass[] = $this->_document->mobileSupport ? 'jsn-mobile' : 'jsn-desktop';

		// Add class for Joomla version
		$bodyClass[] = $this->_isJoomla3() ? 'jsn-joomla-30' : 'jsn-joomla-25';

		// Add page class suffix
		$bodyClass[] = implode(' ', $this->_getPageClass());

		// Add class for requested component
		if (($option = substr($this->_document->app->input->getCmd('option', null), 4)) != null)
		{
			$bodyClass[] = "jsn-com-{$option}";
		}

		// Add class for requested view
		if (($view = $this->_document->app->input->getCmd('view', null)) != null)
		{
			$bodyClass[] = "jsn-view-{$view}";
		}

		// Add class for requested layout
		if (($layout = $this->_document->app->input->getCmd('layout', null)) != null)
		{
			$bodyClass[] = "jsn-layout-{$layout}";
		}

		// Add class for requested Itemid
		if (($itemid = $this->_document->app->input->getInt('Itemid', null)) != null)
		{
			$bodyClass[] = "jsn-itemid-{$itemid}";
		}

		// Add class for home page
		if (is_object($this->_document->activeMenu) && $this->_document->activeMenu->home == 1)
		{
			$bodyClass[] = 'jsn-homepage';
		}

		// Set body class to document object
		$this->_document->bodyClass = preg_replace('/custom-[^\-]+width-span\d+/', '', implode(' ', $bodyClass));
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
		JHtml::_('behavior.framework', true);

		// Load optional Bootstrap's right-to-left CSS file
		! class_exists('JHtmlBootstrap') OR JHtmlBootstrap::loadCss(true);

		// Load Bootstrap stylesheets
		if (is_readable(JPATH_ROOT . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap/css/bootstrap-frontend.min.css'))
		{
			$this->_document->addStylesheet($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap/css/bootstrap-frontend.min.css');
		}
		else
		{
			$this->_document->addStylesheet($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap/css/bootstrap.min.css');
		}

		if ( ! $this->_document->isFree AND $this->_document->mobileSupport AND $this->_document->templateWidth['type'] == 'responsive')
		{
			if (is_readable(JPATH_ROOT . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap/css/bootstrap-responsive-frontend.min.css'))
			{
				$this->_document->addStylesheet($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap/css/bootstrap-responsive-frontend.min.css');
			}
			else
			{
				$this->_document->addStylesheet($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/3rd-party/bootstrap/css/bootstrap-responsive.min.css');
			}
		}

		// Prepare custom font style
		if (
			@is_array($this->_document->fontStyle[$this->_document->fontStyle['style']])
			AND
			is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/styles/' . $this->_document->fontStyle['style'] . '.css.php')
		)
		{
			$this->_document->customStyle = $this->_document->fontStyle[$this->_document->fontStyle['style']];

			// Generate custom style
			ob_start();
			require_once JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/styles/' . $this->_document->fontStyle['style'] . '.css.php';
			$this->_document->customStyle = ob_get_clean();

			// Create custom style declaration file
			if ( ! JFile::write(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/styles/' . $this->_document->fontStyle['style'] . '.css', $this->_document->customStyle))
			{
				$this->_document->addStyleDeclaration($this->_document->customStyle);
			}
		}

		// Load other stylesheets
		if ($this->_loadTemplateCSS == true)
		{
			// Print optimize
			if ($this->_document->printOptimize)
			{
				$this->_document->addStylesheet($this->_document->templateUrl . "/css/print.css", 'text/css', 'Print');
			}

			// Load general stylesheets
			$this->_document->addStylesheet($this->_document->rootUrl . '/templates/system/css/system.css');
			$this->_document->addStylesheet($this->_document->rootUrl . '/templates/system/css/general.css');
			$this->_document->addStylesheet($this->_document->templateUrl . '/css/template.css');

			// Load PRO template styles
			if ($this->_template->edition != 'FREE')
			{
				if (is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/template_pro.css'))
				{
					$this->_document->addStylesheet($this->_document->templateUrl . '/css/template_pro.css');
				}
			}

			// Load customization styles
			$this->_document->addStylesheet($this->_document->templateUrl . '/css/colors/' . $this->_document->templateColor . '.css');

			if (is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/styles/' . $this->_document->fontStyle['style'] . '.css'))
			{
				$this->_document->addStylesheet($this->_document->templateUrl . '/css/styles/' . $this->_document->fontStyle['style'] . '.css');
			}

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

			// Enable mobile support
			if ($this->_document->mobileSupport)
			{
				$this->_document->addCustomTag('<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />');
			}

			// IE7 Specific stylesheet
			if ($this->_document->isIE7)
			{
				$this->_document->addStylesheet($this->_document->rootUrl . '/css/jsn_fixie7.css');
			}

			// Process template width
			switch ($this->_document->templateWidth['type'])
			{
				case 'fixed':
				case 'float':
					$unit = $this->_document->templateWidth['type'] == 'fixed' ? 'px' : '%';
					$this->_document->customWidth = $this->_document->templateWidth[$this->_document->templateWidth['type']] . $unit;
				break;

				case 'responsive':
				default:
					if ( ! $this->_document->isFree)
					{
						$enabledLayout = $this->_document->templateWidth[$this->_document->templateWidth['type']];
						$hasWideLayout = is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/layouts/jsn_wide.css');
						$hasMobileLayout = is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/layouts/jsn_mobile.css');

						if (in_array('wide', $enabledLayout) AND $hasWideLayout)
						{
							$this->_document->addStylesheet($this->_document->templateUrl . '/css/layouts/jsn_wide.css');
						}

						if ($this->_document->mobileSupport AND in_array('mobile', $enabledLayout) AND $hasMobileLayout)
						{
							$this->_document->addStylesheet($this->_document->templateUrl . '/css/layouts/jsn_mobile.css');
						}
					}

					$this->_document->customWidth = 'responsive';
				break;
			}

			// Load custom css that declared by the template
			if (is_file(JPATH_ROOT . "/templates/{$this->_document->template}/template_custom.php"))
			{
				include_once JPATH_ROOT . "/templates/{$this->_document->template}/template_custom.php";
			}

			// Load social icons stylesheet
			if (@count($this->_document->socialIcons))
			{
				if (is_readable(JPATH_ROOT . '/templates/' . basename($this->_document->templateUrl) . '/css/jsn_social_icons.css'))
				{
					$this->_document->addStylesheet($this->_document->templateUrl . '/css/jsn_social_icons.css');
				}
			}

			// Load custom css files from the parameter
			foreach (preg_split('/[\r\n]+/', $this->_document->cssFiles) AS $file)
			{
				if (empty($file) OR strcasecmp(substr($file, -4), '.css') != 0)
				{
					continue;
				}

				preg_match('#^([a-z]+://|/)#i', $file)
					? $this->_document->addStylesheet(trim($file))
					: $this->_document->addStylesheet($this->_document->templateUrl . '/css/' . trim($file));
			}
		}

		// Load scripts
		if ($this->_loadTemplateJS == true)
		{
			// Load Javascript files
			$this->_document->addScript($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/joomlashine/js/noconflict.js');
			$this->_document->addScript($this->_document->rootUrl . '/plugins/system/jsntplframework/assets/joomlashine/js/utils.js');
			$this->_document->addScript($this->_document->templateUrl . '/js/jsn_template.js');

			// Custom template JS declarations
			$mobileEnabled = 0;
			$enabledLayout = '[]';

			if (@$this->_document->templateWidth['type'] == 'responsive')
			{
				if (@in_array('mobile', $this->_document->templateWidth['responsive']))
				{
					$mobileEnabled = 1;
				}

				$enabledLayout = json_encode($this->_document->templateWidth['responsive']);
			}

			$enableMobileMenuSticky = (is_array($this->_document->menuSticky) AND isset($this->_document->menuSticky['mobile']))
				? ($this->_document->menuSticky['mobile'] ? 1 : 0)
				: (( ! is_array($this->_document->menuSticky) AND $this->_document->menuSticky) ? 1 : 0);

			$enableDesktopMenuSticky = (is_array($this->_document->menuSticky) AND isset($this->_document->menuSticky['desktop']))
				? ($this->_document->menuSticky['desktop'] ? 1 : 0)
				: 0;

			$this->_document->addScriptDeclaration('
				JSNTemplate.initTemplate({
					templatePrefix			: "' . $this->_document->template . '_",
					templatePath			: "' . $this->_document->rootUrl . '/templates/' . $this->_document->template . '",
					enableRTL				: ' . ($this->_document->direction == "rtl" ? 1 : 0) . ',
					enableGotopLink			: ' . ($this->_document->gotoTop ? 1 : 0) . ',
					enableMobile			: ' . ((isset($this->_document->mobileView) AND ! $this->_document->mobileView) ? 0 : $mobileEnabled) . ',
					enableMobileMenuSticky	: ' . $enableMobileMenuSticky .',
					enableDesktopMenuSticky	: ' . $enableDesktopMenuSticky .',
					responsiveLayout		: ' . $enabledLayout . '
				});
			');

			// Always add class `jsn-desktop-on-mobile` to document body for free template
			if (strcasecmp($this->_template->edition, 'FREE') == 0)
			{
				$this->_document->addScriptDeclaration('
					window.addEvent("domready", JSNUtils.setDesktopOnMobile);
				');
			}

			// Load Squeezebox
			JHTML::_('behavior.modal', 'a.modal');

			// Load custom js files from the parameter
			foreach (preg_split('/[\r\n]+/', $this->_document->cssFiles) AS $file)
			{
				if (empty($file) OR strcasecmp(substr($file, -3), '.js') != 0)
				{
					continue;
				}

				preg_match('#^([a-z]+://|/)#i', $file)
					? $this->_document->addScript(trim($file))
					: $this->_document->addScript($this->_document->templateUrl . '/js/' . trim($file));
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
		foreach ($pageClass AS $class)
		{
			if (strpos($class, 'custom-') !== false && substr_count($class, '-') >= 2)
			{
				list($prefix, $param, $value) = explode('-', $class);

				if ( ! isset($this->_overrideAttributes[$param]))
				{
					continue;
				}

				// Update custom parameter
				$attribute = $this->_overrideAttributes[$param];

				// Prepare parameter for overriding
				if (isset($attribute['key']))
				{
					$override = & $params[$attribute['name']][$attribute['key']];
				}
				else
				{
					$override = & $params[$attribute['name']];
				}

				// Checking attribute type
				if (is_array($attribute['type']) && in_array($value, $attribute['type']))
				{
					$override = $value;
				}
				elseif ($attribute['type'] == 'integer' && is_numeric($value))
				{
					$override = intval($value);
				}
				else
				{
					$override = trim($value);
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

						// Prepare parameter for overriding
						if (isset($attribute['key']))
						{
							$override = & $params[$attribute['name']][$attribute['key']];
						}
						else
						{
							$override = & $params[$attribute['name']];
						}

						// Checking attribute type
						if (is_array($attribute['type']) && in_array($value, $attribute['type']))
						{
							$override = $value;
						}
						elseif ($attribute['type'] == 'integer' && is_numeric($value))
						{
							$override = intval($value);
						}
						elseif ($attribute['type'] == 'boolean')
						{
							$override = $value == 'yes';
						}
						else
						{
							$override = $value;
						}
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
