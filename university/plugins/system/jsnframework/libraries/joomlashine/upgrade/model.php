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

// Import Joomla libraries
jimport('joomla.filesystem.archive');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

/**
 * Model class of JSN Upgrade library.
 *
 * To implement <b>JSNUpgradeModel</b> class, create a model file in
 * <b>administrator/components/com_YourComponentName/models</b> folder
 * then put following code into that file:
 *
 * <code>class YourComponentPrefixModelUpgrade extends JSNUpgradeModel
 * {
 * }</code>
 *
 * The <b>JSNUpgradeModel</b> class pre-defines <b>download</b> and
 * <b>install</b> method to handle product upgrade task. So, you <b>DO NOT
 * NEED</b> to re-define those methods in your model class.
 *
 * <b>JSNUpgradeModel</b> class has following protected methods that you can
 * overwrite in your model class to customize product upgrade task:
 *
 * <ul>
 *     <li>beforeDownload()</li>
 *     <li>afterDownload($path)</li>
 *     <li>beforeInstall($path)</li>
 *     <li>afterInstall($path)</li>
 * </ul>
 *
 * If you overwrite any of 4 methods above, remember to call parent method
 * either before or after your customization in order to make JSN Upgrade
 * library working properly. See example below:
 *
 * <code>class YourComponentPrefixModelUpgrade extends JSNUpgradeModel
 * {
 *     protected function beforeDownload()
 *     {
 *         parent::beforeDownload();
 *
 *         // Do some additional preparation...
 *     }
 *
 *     protected function afterInstall($path)
 *     {
 *         // Do some additional finalization...
 *
 *         parent::afterInstall($path);
 *     }
 * }</code>
 *
 * @package  JSN_Framework
 * @since    1.0.0
 */
class JSNUpgradeModel extends JSNUpdateModel
{
}
