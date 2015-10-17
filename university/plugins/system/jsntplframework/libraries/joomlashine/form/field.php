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
 * Base class for all custom fields
 * 
 * @package     JSNTPLFramework
 * @subpackage  Form
 * @since       1.0.0
 */
abstract class JSNTPLFormField extends JFormField
{
	/**
	 * Field constructor
	 * 
	 * @param   JForm  $form  Form object
	 */
	public function __construct ($form = null)
	{
		// Call parent constructor
		parent::__construct($form);
	}

	/**
	 * Generate html for the field
	 * 
	 * @return  void
	 */
	protected function renderLayout ()
	{
		$className = get_class($this);
		$className = strtolower(substr($className, 10));

		$layoutPath = JSN_PATH_TPLFRAMEWORK . '/libraries/joomlashine/form/fields/tmpl/' . $className . '.php';
		$content    = '';

		if (is_file($layoutPath))
		{
			ob_start();
			include $layoutPath;
			$content = ob_get_clean();
		}

		return $content;
	}

	/**
	 * Return the string that use as label of the input
	 * 
	 * @return  string
	 */
	protected function getLabel ()
	{
		return parent::getLabel();
	}

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 * 
	 * @return  string
	 */
	protected function getInput()
	{
		return $this->renderLayout();
	}
}
