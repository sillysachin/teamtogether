<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * View class of JSN Positions.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNPositionsView extends JSNBaseView
{
	/**
	 * Custom sript
	 *
	 * @var array
	 */
	protected $customScripts = array();

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 */
	public function __construct($config = array())
	{
		// Display only the component output
		JFactory::getApplication()->input->def('tmpl', 'component');

		parent::__construct($config);
	}

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$template = JSNTemplateHelper::getInstance();
		$onPositionClick = '';
		$initFilter = '';
		$displayNotice = $app->input->getInt('notice');
		$bypassNotif	= $app->input->getVar('bypassNotif', '');

		// Get template author.
		$templateAuthor = $template->getAuthor();

		JSNPositionsHelper::dispatchTemplateFramework($templateAuthor);

		$document->addStyleSheet(JSN_URL_ASSETS . '/joomlashine/css/jsn-positions.css');

		if (JSNVersion::isJoomlaCompatible('3.0'))
		{
			$document->addScript(JURI::root(true) . '/media/jui/js/jquery.js');
		}
		else
		{
			$document->addScript(JSN_URL_ASSETS . '/3rd-party/jquery/jquery-1.8.2.js');
		}

		if (isset($this->filterEnabled) AND $this->filterEnabled)
		{
			$document->addScript(JSN_URL_ASSETS . '/joomlashine/js/positions.filter.js');
			$initFilter = 'changeposition = new JoomlaShine.jQuery.visualmodeFilter({});';
		}

		if (isset($this->customScripts))
		{
			$document->addScriptDeclaration(implode('\n', $this->customScripts));
		}

		$onPositionClick = isset($this->onPositionClickCallBack) ? implode('\n', $this->onPositionClickCallBack) : '';

		// Get JSN Template Framework version
		$db	= JFactory::getDbo();
		$q	= $db->getQuery(true);

		$q->select('manifest_cache');
		$q->from('#__extensions');
		$q->where("element = 'jsntplframework'");
		$q->where("type = 'plugin'", 'AND');
		$q->where("folder = 'system'", 'AND');

		$db->setQuery($q);

		// Load dependency installation status.
		$res 	= $db->loadObject();
		$res	= json_decode($res->manifest_cache);
		$jsnTplFwVersion	=	$res->version;

		$jsnTemplateCustomJs	= '';
		if (version_compare($jsnTplFwVersion, '2.0.1', '<=')) {
			$jsnTemplateCustomJs	= "$('body').addClass('jsn-bootstrap');";
		}

		$_customScript = "
			var changeposition;
			(function($){
				$(document).ready(function (){
					var posOutline	= $('.jsn-position');
					var _idAlter	= false;
					if ($('.jsn-position').length == 0) {
						posOutline	= $('.mod-preview');
						_idAlter	= true;
					}else{
						posOutline.css({'z-index':'9999', 'position':'relative'});
					}
					posOutline.each(function(){
						if(_idAlter){
							previewInfo = $(this).children('.mod-preview-info').text();

							_splitted = previewInfo.split('[');
							if(_splitted.length > 1){
								posname	= _splitted[0];
							}
							_splitted = posname.split(': ');
							if(_splitted.length > 1){
								posname	= _splitted[1];
							}

							posname = $.trim(posname);

							$(this).attr('id', posname + '-jsnposition');
						}

						$(this)[0].oncontextmenu = function() {
							return false;
						}
					})
					.click(function () {
						" . $onPositionClick . "
					});
					" . $jsnTemplateCustomJs ."
				});
				" . $initFilter . "
				
			})(jQuery);
		";
		$document->addScriptDeclaration($_customScript);

		$jsnrender = JSNPositionsRender::getInstance();
		$jsnrender->renderPage(JURI::root() . 'index.php?poweradmin=1&vsm_changeposition=1&tp=1', 'changePosition');

		$this->assignRef('jsnrender', $jsnrender);

		parent::display($tpl);
	}

	/**
	 * Method to add customs javacript into page.
	 *
	 * @param   string  $customScript  Custom script
	 *
	 * @return  void
	 */

	public function addCustomScripts($customScript = '')
	{
		$this->customScripts[] = $customScript;

		return;
	}

	/**
	 * Method to add javascript callback functions after a position clicked.
	 *
	 * @param   string  $script  Script code
	 *
	 * @return  void
	 */
	public function addPositionClickCallBack($script = '')
	{
		$this->onPositionClickCallBack[] = $script;

		return;
	}

	/**
	 * Method to enable/disable position filter.
	 *
	 * @param   boolean  $filterEnabled  Whether to enable filter or not?
	 *
	 * @return  void
	 */
	public function setFilterable($filterEnabled = false)
	{
		$this->filterEnabled = $filterEnabled;
	}

	/**
	 * Add assets
	 *
	 * @return  void
	 */
	public function _addAssets()
	{
	}
}
