<?php

/**
 * @version     $Id: mod_uniform.php 19014 2012-11-28 04:48:56Z thailv $
 * @package     JSNUniform
 * @subpackage  Modules
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_uniform' . DS . 'uniform.defines.php';
require JModuleHelper::getLayoutPath('mod_uniform', $params->get('layout', 'default'));
