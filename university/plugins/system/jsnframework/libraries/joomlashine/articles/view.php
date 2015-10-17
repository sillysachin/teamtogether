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

// Import necessary Joomla libraries
require_once JPATH_ROOT . '/administrator/components/com_content/views/articles/view.html.php';
require_once JPATH_ROOT . '/administrator/components/com_content/models/articles.php';
require_once JPATH_ROOT . '/plugins/system/jsnframework/libraries/joomlashine/articles/content.php';

// Load language
$lang = JFactory::getLanguage();
$lang->load('com_content');

/**
 * Article selection view class.
 *
 * @package  JSN_Framework
 * @since    1.0.3
 */
class JSNArticlesView extends ContentViewArticles
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 */
	public function __construct($config = array ())
	{
		// Display only the component output
		JFactory::getApplication()->input->def('tmpl', 'component');

		parent::__construct($config);

		// Load article model
		$model = JSNBaseModel::getInstance('Articles', 'ContentModel');
		$this->setModel($model, true);

		// Include the component HTML helpers
		$this->addTemplatePath(dirname(__FILE__) . '/tmpl');
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		// Load assets
		JSNBaseHelper::loadAssets();
		echo JSNHtmlAsset::loadScript('jsn/selectorFilter',array(),true);
	}
}
