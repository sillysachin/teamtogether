<?php
/**
 * @version    $Id$
 * @package    JSNPoweradmin
 * @subpackage helpers
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

JSNFactory::localimport('helpers.html.layouts.jsnlayouthelper');
/**
 * @package		Joomla.Administrator
 * @subpackage	com_poweradmin
 * @since		1.7
 */
abstract class JSNRenderHelper
{
	/**
	 *
	 * Get view information
	 *
	 * @param Array $vars
	 */
	public function getInfoView( $vars )
	{
		$componentName = $vars['option'];
		$view = $vars['view'];

		// load language
		$lang = JFactory::getLanguage();
		$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR.'/components/'.$componentName, null, false, false)
		||	$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($componentName.'.sys', JPATH_ADMINISTRATOR.'/components/'.$componentName, $lang->getDefault(), false, false);

		$value = Array();
		$value['layout'] = '';
		$value['extension'] = '';

		if ( !empty($componentName) ) {
			$value['extension']	= JText::_( $componentName,true );
			if (isset($vars['view'])) {
				// Attempt to load the view xml file.
				$file = JPATH_SITE.'/components/'.$componentName.'/views/'.$vars['view'].'/metadata.xml';
				if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
					// Look for the first view node off of the root node.
					if ($view = $xml->xpath('view[1]')) {
						if (!empty($view[0]['title'])) {
							$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

							// Attempt to load the layout xml file.
							// If Alternative Menu Item, get template folder for layout file
							if (strpos($vars['layout'], ':') > 0)
							{
								// Use template folder for layout file
								$temp = explode(':', $vars['layout']);
								$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$componentName.'/'.$vars['view'].'/'.$temp[1].'.xml';
								// Load template language file
								$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, null, false, false)
								||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], null, false, false)
								||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, $lang->getDefault(), false, false)
								||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], $lang->getDefault(), false, false);
							}
							else
							{
								// Get XML file from component folder for standard layouts
								$file = JPATH_SITE.'/components/'.$componentName.'/views/'.$vars['view'].'/tmpl/'.$vars['layout'].'.xml';
							}
							if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
								// Look for the first view node off of the root node.
								if ($layout = $xml->xpath('layout[1]')) {
									if (!empty($layout[0]['title'])) {
										$value['layout'] = JText::_(trim((string) $layout[0]['title']),true);
									}
								}
							}
						}
					}
					unset($xml);
				}
				else {
					// Special case for absent views
					$value['layout'] = JText::_($componentName.'_'.$vars['view'].'_VIEW_DEFAULT_TITLE',true);
				}
			}
		}

		return $value;
	}
	/**
	 *
	 * Dispatch component
	 *
	 * @param Array $params
	 */
	static public function dispatch( $params = Array() )
	{
		$currentOption = JString::strtolower( $params['option'] );
		$currentView   = JString::strtolower( $params['view'] );
		$layout        = @JString::strtolower( $params['layout'] );
		$task          = @JString::strtolower( $params['task'] );
		$modelName     = JString::trim( $currentView );
		$modelName[0]  = JString::strtoupper( $params['view'][0] );
		$shortComponentName = str_replace("com_", "", $currentOption);
		$extensionDirectory =  JPATH_ROOT . '/plugins/jsnpoweradmin/' . $shortComponentName;
		$lang = JFactory::getLanguage();

		// Load front-end global language.
		$lang->load("",  JPATH_ROOT);
		// Load front com_content language.
		$lang->load($currentOption,  JPATH_ROOT);

		$modelSuffix = explode('_', $currentOption);
		$modelSuffix = ucfirst(JString::trim($modelSuffix[1]));
		$_plgClassName = 'plgJsnpoweradmin'.$shortComponentName;

		JPluginHelper::importPlugin('jsnpoweradmin', $shortComponentName);
		$lang->load('plg_jsnpoweradmin_'.$shortComponentName, JPATH_ADMINISTRATOR , null, true, false);

		/**
		 * Render HTML of current view
		 */
		ob_start();

		if (!file_exists(JPATH_ROOT . '/components/' . $currentOption ))
		{
			self::printErrorMessage(array(), JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}
		else
		{
			if (is_file($extensionDirectory . '/models/' . $currentView .'.php') && class_exists( $_plgClassName ))
			{
				if ($msg = call_user_func(array($_plgClassName, 'checkSupportedVersion')))
				{
					self::printErrorMessage(array() ,$msg);
				}
				else
				{
					include_once $extensionDirectory . '/models/' . $currentView .'.php';

					// Load plugin language.
					include_once JPATH_ROOT . '/administrator/components/com_poweradmin/libraries/joomlashine/language/javascriptlanguages.php';
					$dispatcher = JDispatcher::getInstance();
					$pluginsLang 	= $dispatcher->trigger('loadJavascriptLang');

					$modelSuffix[0] = JString::strtoupper($modelSuffix[0]);
					$modelSuffix .=  'Model'.$modelName;

					$PoweradminExtensionModel = 'Poweradmin'.$modelSuffix;

					if ( class_exists( $PoweradminExtensionModel ) ){
						$model = new $PoweradminExtensionModel();
						$data  = $model->prepareDisplayedData( $params );
					}



					if ( !empty($layout) ){
						$layoutName = $layout;
					}else{
						$layoutName = 'default';
					}

					//Set current option to global
					JRequest::setVar('jsnCurrentOption', $currentOption);
					//Set current layout to global
					JRequest::setVar('jsnCurrentLayout', $layout);
					JRequest::setVar('jsnCurrentView', $currentView);
					JRequest::setVar('jsnCurrentItemid', $params['Itemid']);

					$layoutPath = $extensionDirectory . '/views/' . $currentView . '/' . $layoutName . '.php';

					if ( file_exists( $layoutPath ) ){
						include( $layoutPath );
					}else{
						self::printErrorMessage($params);
					}
				}

			}else{

				self::printErrorMessage($params);
			}
		}
		$contents = ob_get_contents();

		$tmp = explode('<script', $contents);
		$new = $tmp[0];

		for ($i = 1, $n = count($tmp); $i < $n; $i++)
		{
			$line =& $tmp[$i];
			$new .= substr($line, strpos($line, '</script>') + 9);
		}

		$contents = $new;

		// Load javascript lang
		if (isset($pluginsLang[0])) {
			$contents .= '<script type="javascript">'. $pluginsLang[0] . '</script>';
		}

		ob_end_clean();

		return $contents;
	}

	private function printErrorMessage($params = array(), $message = '')
	{
		if(!$message){
			$info = JSNRenderHelper::getInfoView( $params );
			$message = JText::sprintf('JSN_RAWMODE_MESSAGE_NOT_SUPPORTED_YET', '"'.JString::strtoupper($info['layout']).'"', '"'.JString::strtoupper($info['extension']).'"');
		}

		echo JSNHtmlHelper::openTag('div', array('class' => 'show-message-not-supported-yet'))
		.$message
		.JSNHtmlHelper::closeTag('div');

		return;
	}
}
