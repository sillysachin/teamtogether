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


jimport('joomla.html.toolbar');
//Register the session storage class with the loader
JLoader::register('JSNButton', dirname(__FILE__) . '/toolbar/jsnbutton.php');

$JSNMedia = JSNFactory::getMedia();
JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI. 'jsn.toolbars.js' );

/**
 * Utility class for the button bar.
 *
 * @package		Joomla.Administrator
 * @subpackage	Application
 */
abstract class JSNToolBarHelper
{
	/**
	 * Title cell.
	 * For the title and toolbar to be rendered correctly,
	 * this title fucntion must be called before the starttable function and the toolbars icons
	 * this is due to the nature of how the css has been used to postion the title in respect to the toolbar.
	 *
	 * @param	string	$title	The title.
	 * @param	string	$icon	The space-separated names of the image.
	 * @since	1.5
	 */
	public static function title($title, $icon = 'generic.png')
	{
// 		// Strip the extension.
// 		$icons = explode(' ',$icon);
// 		foreach($icons as &$icon) {
// 			$icon = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
// 		}

// 		$html = '<div class="pagetitle '.htmlspecialchars(implode(' ', $icons)).'"><h2>'.$title.'</h2></div>';

// 		$app = JFactory::getApplication();
// 		$app->set('JComponentTitle', $html);
	}

	/**
	 *
	 * Add an help oolbar button
	 * @param String $href is url of button
	 * @param String $task is task and also icon class prefix
	 * @param String $text is text of button
	 * @param String $title is title of button
	 * @param String $pageWidth is width of window
	 * @param String $pageHeight is height of window
	 * @param String $newPage is variable you want set popup is child page/new page
	 */
	public static function help( $href, $task, $text, $title, $pageWidth = 750, $pageHeight = 550, $newPage = false )
	{
		JSNFactory::localimport('helpers.html.toolbar.button.jsnhelp');

		$bar = JToolBar::getInstance('toolbar');
		// Add a standard button.
		$bar->appendButton( 'JSNHelp', $href, $task, $text, $title, $pageWidth, $pageHeight, $newPage );
	}
	/**
	 *
	 * Add an toolbar dropdown list
	 *
	 * @param String $text is text of parent
	 * @param String $title is title of parent
	 * @param String $icon is icon class prefix
	 * @param String $childs is HTML
	 * @param String $href is url of parent
	 * @param String $action is action you want when click on parent
	 */
	public static function dropdown( $text = '', $title = '', $icon = 'jsn-parent', $childs = '', $href = '', $action = 'popup' )
	{
		JSNFactory::localimport('helpers.html.toolbar.button.jsndropdown');

		$bar = JToolBar::getInstance('toolbar');
		// Add a standard button.
		$bar->appendButton( 'JSNDropdown', $text, $title, $icon, $childs, $href, $action );
	}

	/**
	 *
	 * Add an toolbar switch mode
	 *
	 * @param: String $icon suffix class
	 * @param: String $text is string text of icon
	 * @param: String $enmodeTitle is enmode title
	 * @param: String $offmodeTitle is offmode title
	 */
	public static function switchmode( $icon = '', $text = '', $enmodeTitle = '', $offmodeTitle = '' )
	{
		$params = JSNConfigHelper::get('com_poweradmin');

		$JSNMedia = JSNFactory::getMedia();
		JSNHtmlAsset::addScript( JSN_POWERADMIN_LIB_JSNJS_URI. 'jquery.context-help.js' );

		$customScript = "
		(function($) {
			function getVisisblePosition () {
				var listOffset = $('#modules-list').offset();
				var listScrollTop = $('#modules-list').scrollTop();

				var items = $('.jsn-element-container_inner');
				var minOffset = null;
				var visibleItem = null;

				items.each(function () {
					var element = $(this);
					var offsetTop = element.offset().top - listOffset.top;

					if (element.find('.poweradmin-module-item').size() == 0)
						return true;

					if ((offsetTop > 0 && minOffset == null) || (offsetTop > 0 && minOffset > offsetTop)) {
						minOffset = offsetTop;
						visibleItem = element;
					}
				});

				return visibleItem;
			}

			$(function () {
				var helps =
				[{
					'element'	: '#jsn-rawmode-leftcolumn .jsn-heading-panel-title:eq(0)',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_01',true)."',
					'arrow'		: 'bottom',
					'width'		: 250,
					'height'	: 85,
					'offset'	: { left: 0, top: -40 }
				}, {
					'element'	: '.jsn-menu-selector-container',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_02',true)."',
					'arrow'		: 'top',
					'width'		: 300,
					'height'	: 130,
					'offset'	: { left: 60, top: 65 }
				}, {
					'element'	: '#jsn-rawmode-leftcolumn .jsn-toggle-button:eq(0)',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_03',true)."',
					'arrow'		: 'bottom',
					'width'		: 200,
					'height'	: 85,
					'offset'	: { left: 0, top: -40 }
				}, {
					'element'	: '#jsn-rawmode-center .jsn-heading-panel-title:eq(0)',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_04',true)."',
					'arrow'		: 'bottom',
					'width'		: 300,
					'height'	: 105,
					'offset'	: { left: 20, top: -40 }
				}, {
					'element'	: '#jsn-rawmode-rightcolumn .jsn-heading-panel-title:eq(0)',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_05',true)."',
					'arrow'		: 'bottom',
					'width'		: 250,
					'height'	: 85,
					'offset'	: { left: 0, top: -40 }
				}, {
					'element'	: '#jsn-rawmode-rightcolumn .jsn-toggle-button:eq(0)',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_06',true)."',
					'arrow'		: 'bottom',
					'width'		: 250,
					'height'	: 85,
					'offset'	: { left: 0, top: -37 }
				}, {
					'element'	: '#jsn-component-details',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_07',true)."',
					'arrow'		: 'top',
					'width'		: 300,
					'height'	: 70,
					'offset'	: { left: '50%', top: '40%' }
				}, {
					'element'	: '#jsn-rawmode-center .jsn-toggle-button',
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_08',true)."',
					'arrow'		: 'bottom',
					'width'		: 300,
					'height'	: 70,
					'offset'	: { left: -5, top: -40 }
				}, {
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_09',true)."',
					'arrow'		: 'top',
					'width'		: 250,
					'height'	: 70,
					'refresh'	: function (item) {
						var visibleItem = getVisisblePosition();
						if (visibleItem == null)
							return;

						item.setLocation(visibleItem.offset().left + 220, visibleItem.offset().top + 5);
					}
				}, {
					'text'		: '".JText::_('JSN_POWERADMIN_CONTEXT_10',true)."',
					'arrow'		: 'top',
					'width'		: 270,
					'height'	: 160,
					'refresh'	: function (item) {
						var visibleItem = getVisisblePosition();
						if (visibleItem == null)
							return;

						var elmOffset = null;
						visibleItem.find('.poweradmin-module-item').each(function () {
							if ($(this).css('display') != 'none') {
								elmOffset = $(this).offset();
								return false;
							}
						});

						if (elmOffset == null)
							return;

						item.setLocation(elmOffset.left + 100, elmOffset.top + 20);
					}
				}];

				var contextHelp = new JSNContextHelp(helps, {});
				var dismissHint = $('<span/>', { 'id': 'dismiss-hint' }).appendTo($('#toolbar-switch-help-mode'));
				var interval 	= null;

				$('#toolbar-switch-help-mode')
				.unbind('turnoffmode')
				.bind('turnoffmode', function(){
					contextHelp.hide();
					clearInterval(interval);
				})
				.unbind('turnonmode')
				.bind('turnonmode', function(){
					contextHelp.show();
					interval = setInterval(function () {
						if ($('#toolbar-switch-help-mode').hasClass('turn-on') && $('.ui-widget-overlay').size() > 0) {
							$('#toolbar-switch-help-mode').click();
							clearInterval(interval);
						}
					}, 500);
				});
			});
		})(JoomlaShine.jQuery);
		";

		$JSNMedia->addScriptDeclaration($customScript);

		// Proceed first
		$_firstRunScript = '';
		if (intval(@$params->get('show_help_on_first_run', 1)) == 1) {
			$_firstRunScript = '
				if(!$.jStorage.get("sitemanager_run", false)){
					$.jStorage.set("sitemanager_show_help", true);
					_helpOff = true;
				}
			';
		}

		$JSNMedia->addScriptDeclaration('
			JoomlaShine.jQuery(function ($) {
				var _helpOff = false;
				' . $_firstRunScript . '
				if($.jStorage.get("sitemanager_show_help") ){
					var interval = setInterval(function () {
						if (jQuery(\'.ui-widget-overlay\').size() == 0) {
							jQuery(\'#toolbar-switch-help-mode\').click();
							clearInterval(interval);
						}
						if(_helpOff){
							$.jStorage.set("sitemanager_show_help", false);
						}
						$.jStorage.set("sitemanager_run", true);
					}, 500);
				}

			})
		');


		//JSNFactory::localimport('helpers.html.toolbar.button.jsnswitchmode');

		//$bar = JToolBar::getInstance('toolbar');
		// Add a standard button.
		//$bar->appendButton( 'JSNSwitchmode', $icon, $text, $enmodeTitle, $offmodeTitle );
	}
}