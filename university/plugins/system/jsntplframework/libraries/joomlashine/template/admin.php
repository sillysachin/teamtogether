<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
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
 * Helper class to generate admin UI for template
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplTemplateAdmin
{
	/**
	 * Instance of template administrator object
	 *
	 * @var  JSNTplTemplateAdmin
	 */
	private static $_instance;

	/**
	 * Joomla version object
	 * @var JVersion
	 */
	protected $version;

	/**
	 * Joomla document instance
	 * @var JDocumentHTML
	 */
	protected $doc;

	/**
	 * Base URL of joomla instance
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * Base URL of template administrator assets
	 * @var string
	 */
	protected $baseAssetUrl;

	/**
	 * Template form context
	 * @var JForm
	 */
	protected $context;

	/**
	 * Template form data
	 * @var JObject
	 */
	protected $data;

	/**
	 * Template config XML document
	 * @var SimpleXMLDocument
	 */
	protected $configXml;

	/**
	 * Template details XML document
	 * @var SimpleXMLDocument
	 */
	protected $templateXml;

	/**
	 * Template admin form
	 * @var JForm
	 */
	protected $adminForm;

	/**
	 * Original template admin form
	 * @var JForm
	 */
	protected $templateForm;

	/**
	 * Template edition manager
	 * @var JSNTplTemplateEdition
	 */
	protected $templateEdition;

	/**
	 * Retrieve initialized instance of template admin object
	 *
	 * @param   JForm  $context  Current context of template admin.
	 *
	 * @return  JSNTplTemplateAdmin
	 */
	public static function getInstance (JForm $context)
	{
		if (self::$_instance == null || !(self::$_instance instanceOf JSNTplTemplateAdmin))
		{
			self::$_instance = new JSNTplTemplateAdmin($context);
		}

		return self::$_instance;
	}

	/**
	 * Register asset files for the template admin
	 *
	 * @return void
	 */
	public function registerAssets ()
	{
		// Specified asset files for joomla 2.5
		if (version_compare($this->version->getShortVersion(), '3.0', '<'))
		{
			$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery/jquery-1.8.2.js');

			$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/bootstrap/css/bootstrap.min.css');
			$this->doc->addScript($this->baseAssetUrl . '/3rd-party/bootstrap/js/bootstrap.min.js');
		}
		// Manual load jQuery on Joomla 3.2
		elseif (version_compare($this->version->getShortVersion(), '3.2', '>='))
		{
			$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery/jquery-1.8.2.js');
		}

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery-ui/js/jquery-ui-1.9.1.custom.min.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/jquery-dynatree/skin/ui.dynatree.css');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery-dynatree/jquery.dynatree.min.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/jquery-tipsy/tipsy.css');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery-tipsy/jquery.tipsy.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/chosen/chosen.min.css');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/chosen/chosen.jquery.min.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/bxslider/jquery.bxslider.css');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/bxslider/jquery.bxslider.min.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/colorbox/colorbox.css');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/colorbox/jquery.colorbox-min.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/3rd-party/font-icomoon/css/icomoon.css');

		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery-layout/jquery.layout.min.js');
		$this->doc->addScript($this->baseAssetUrl . '/3rd-party/jquery-ck/jquery.ck.js');

		$this->doc->addStyleSheet($this->baseAssetUrl . '/joomlashine/css/jsn-gui.css');
		$this->doc->addStyleSheet($this->baseAssetUrl . '/joomlashine/css/jsn-admin.css');
		$this->doc->addStyleSheet($this->baseAssetUrl . '/joomlashine/css/jsn-fonticomoon.css');

		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/media.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/sample-data.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/update.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/upgrade.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/quickstart.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/core.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/font.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/layout.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/maintenance.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/social-integration.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/validate.js');
		$this->doc->addScript($this->baseAssetUrl . '/joomlashine/js/width-type.js');

		$templateEdition = JSNTplHelper::getTemplateEdition($this->data->template);
		$templateName = JText::_($this->data->template);

		$this->doc->addScriptDeclaration(
			"!function ($) {
				\"use strict\";

				$(function () {
					new $.JSNTPLFrameworkCore({
						template: '{$this->data->template}',
						templateName: '{$templateName}',
						edition: '{$templateEdition}',
						styleId : '{$this->data->id}'
					});
				});
			}(jQuery);"
		);
	}

	/**
	 * Render HTML Markup for administrator UI
	 *
	 * @return  string
	 */
	public function render ()
	{
		$adminFormXml = $this->_generateFormXML();

		// Create form instance
		$this->adminForm = new JForm('template-setting');
		$this->adminForm->addFieldPath(JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/form/fields');
		$this->adminForm->load($adminFormXml->asXML());

		$params = $this->helper->loadParams($this->data->params, $this->data->template);

		// Bind value of parameters to form
		foreach ($params AS $key => $value)
		{
			$this->adminForm->setValue($key, 'jsn', $value);
		}

		// Get Joomla application object
		$app = JFactory::getApplication();

		// Store current compression parameters
		$app->setUserState('jsn.template.maxCompressionSize',	$params['maxCompressionSize']);
		$app->setUserState('jsn.template.cacheDirectory',		$params['cacheDirectory']);

		// Start rendering
		ob_start();
		include JSN_PATH_TPLFRAMEWORK_LIBRARIES . '/template/tmpl/default.php';
		$body = ob_get_clean();

		// Detect method to use for getting and setting response body
		if (version_compare(JVERSION, '3.2.0', 'ge'))
		{
			$get = array($app, 'getBody');
			$set = array($app, 'setBody');
		}
		else
		{
			$get = array('JResponse', 'getBody');
			$set = array('JResponse', 'setBody');
		}

		// Parse current response body
		list($head, $tmp) = preg_split('/<form[^>]+name="adminForm"[^>]*>/', call_user_func($get), 2);
		list($tmp, $foot) = explode('</form>', $tmp, 2);

		// Replace current response body
		call_user_func($set, $head . $body . $foot);
	}

	/**
	 * Add nodes to SimpleXMLElement object.
	 *
	 * @param   SimpleXMLElement  $nodes       Nodes to add.
	 * @param   SimpleXMLElement  $parentNode  Parent node to add nodes to.
	 * @param   array             $context     Nodes context.
	 *
	 * @return  void
	 */
	private function _addNodes($nodes, $parentNode, $context)
	{
		foreach ($nodes as $node)
		{
			$nodeType = $node->getName();
			$nodeName = (string) $node['name'];
			$nodeText = trim((string) $node);

			if (isset($context['remove'][$nodeName]))
			{
				continue;
			}

			if ($nodeType == 'field' && isset($context['replace'][$nodeName]))
			{
				$newNode = $parentNode->addChild($nodeType, trim((string) $context['replace'][$nodeName]));

				foreach ($context['replace'][$nodeName]->attributes() as $key => $value)
				{
					$newNode->addAttribute($key, $value);
				}

				$this->_addNodes($context['replace'][$nodeName]->children(), $newNode, $context);

				continue;
			}

			$newNode = $parentNode->addChild($nodeType, $nodeText);

			foreach ($node->attributes() as $key => $value)
			{
				$newNode->addAttribute($key, $value);
			}

			if (isset($context['replace'][$nodeName]))
			{
				$this->_addNodes($context['replace'][$nodeName]->children(), $newNode, $context);
			}
			elseif (isset($context['prepend'][$nodeName]))
			{
				$this->_addNodes($context['prepend'][$nodeName]->children(), $newNode, $context);
				$this->_addNodes($node->children(), $newNode, $context);
			}
			else
			{
				$this->_addNodes($node->children(), $newNode, $context);

				if (isset($context['append'][$nodeName]))
				{
					$this->_addNodes($context['append'][$nodeName]->children(), $newNode, $context);
				}
			}
		}
	}

	/**
	 * This method use to generate XML for template form definition
	 *
	 * @return  object
	 */
	private function _generateFormXML()
	{
		$adminXml = simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><form><fields name="jsn"></fields></form>');
		$optionsXml = $this->templateXml->options;
		$context = array();

		if (JSNTplVersion::isCompatible($this->data->template, JSNTplHelper::getTemplateVersion($this->data->template)))
		{
			$formXml = simplexml_load_file(JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/template/params.xml');
		}
		else
		{
			// Template is not compatible with framework v2, load old params declaration file
			$formXml = simplexml_load_file(JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/template/params_v1.xml');
		}

		foreach ($optionsXml->xpath('//*[@method]') AS $node)
		{
			$nodeType = (string) $node->getName();
			$method = (string) $node['method'];

			if ( ! in_array($nodeType, array('fieldset', 'field')))
			{
				continue;
			}

			if ( ! isset($context[$method]))
			{
				$context[$method] = array();
			}

			$context[$method][(string) $node['name']] = $node;
		}

		$this->_addNodes($formXml->fields->children(), $adminXml->fields, $context);

		// Disable fieldset when edition is free
		if (strtolower($this->templateEdition->getEdition()) == 'free')
		{
			foreach ($adminXml->xpath('//fieldset[@pro="true"]') AS $fieldset)
			{
				foreach ($fieldset->children() AS $input)
				{
					if ($input->getName() == 'fieldset')
					{
						foreach ($input->children() AS $_input)
						{
							$_input->addAttribute('disabled', 'true');
						}

						continue;
					}

					$input->addAttribute('disabled', 'true');
				}
			}
		}

		$replacement = array(
			'{templateUrl}' => JUri::root(true) . '/templates/' . $this->data->template
		);

		// Set default values
		foreach ($this->templateXml->xpath('//defaults/option') AS $option)
		{
			$name = (string) $option['name'];
			$value = '';

			if (isset($option['value']))
			{
				$value = (string) $option['value'];
			}
			elseif (count($option->children()) > 0)
			{
				$_value = array();

				foreach ($option->children() AS $item)
				{
					$_value[] = (string) $item;
				}

				$value = implode("\r\n", $_value);
			}

			foreach ($adminXml->xpath('//field[@name="' . $name . '"]') AS $field)
			{
				$field['defaultValue'] = str_replace(array_keys($replacement), array_values($replacement), $value);
			}
		}

		$logoField = current($adminXml->xpath('//field[@name="logoFile"]'));
		$logoField['defaultValue'] = 'templates/' . $this->data->template . '/images/logo.png';

		return $adminXml;
	}

	/**
	 * Constructor for template admin
	 *
	 * @param   JForm  $context  Current context of template admin.
	 */
	private function __construct(JForm $context)
	{
		if (class_exists('JModelLegacy'))
		{
			$templateModel = JModelLegacy::getInstance('Style', 'TemplatesModel');
		}
		else
		{
			$templateModel = JModel::getInstance('Style', 'TemplatesModel');
		}

		$request            = JFactory::getApplication()->input;
		$this->baseUrl      = JUri::root(true);
		$this->baseAssetUrl = $this->baseUrl . '/plugins/system/jsntplframework/assets';
		$this->context      = $context;
		$this->data         = $templateModel->getItem($request->getInt('id'));
		$this->version      = new JVersion;
		$this->doc          = JFactory::getDocument();
		$this->helper       = JSNTplTemplateHelper::getInstance($this->data->template);
		$this->templateXml  = JSNTplHelper::getManifest($this->data->template);

		// Retrieve template form instance
		$this->templateForm    = JForm::getInstance('com_templates.style', 'style', array('control' => 'jform', 'load_data' => true));
		$this->templateEdition = JSNTplTemplateEdition::getInstance($this->data);

		// Load cache engine
		$this->cache = JFactory::getCache('plg_system_jsntplframework');

		// Load language
		$language = JFactory::getLanguage();

		$language->load('tpl_' . $this->data->template, JPATH_ROOT);
	}

	/**
	 * Disable object cloneable for template admin
	 *
	 * @return  void
	 */
	private function __clone()
	{
	}
}
