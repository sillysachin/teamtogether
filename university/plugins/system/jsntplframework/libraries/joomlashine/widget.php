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
abstract class JSNTplWidget
{
	/**
	 * Helper method to dispatch request to widget action
	 *
	 * @return  boolean
	 */
	public static function dispatch ()
	{
		// Retrieve application instance
		$app = JFactory::getApplication();

		// Execute widget action if needed
		$action  = $app->input->getCmd('action', null);
		$widget  = $app->input->getCmd('widget', null);

		if (empty($widget) OR empty($action))
		{
			return false;
		}

		try
		{
			// Checking user permission
			if ( ! JFactory::getUser()->authorise('core.manage', 'com_templates'))
			{
				throw new Exception('JERROR_ALERTNOAUTHOR');
			}

			$widgetClass = 'JSNTplWidget' . ucfirst($widget);

			if ( ! class_exists($widgetClass))
			{
				throw new Exception('Class not found: ' . $widgetClass);
			}

			// Create widget instance if widget class is loaded
			$widgetObject = new $widgetClass();
			$widgetAction = str_replace('-', '', $action) . 'Action';

			if (method_exists($widgetObject, $widgetAction))
			{
				call_user_func(array($widgetObject, $widgetAction));
			}
			elseif (method_exists($widgetObject, 'invoke'))
			{
				call_user_func(array($widgetObject, 'invoke'), $action);
			}
			else
			{
				throw new Exception('Invalid widget action: ' . $action);
			}

			// Send action result to client
			echo json_encode(
				array(
					'type' => 'success',
					'data' => $widgetObject->getResponse()
				)
			);
		}
		catch (Exception $e)
		{
			echo json_encode(
				array(
					'type' => $e->getCode() == 99 ? 'outdate' : 'error',
					'data' => $e->getMessage()
				)
			);
		}

		return true;
	}
}
