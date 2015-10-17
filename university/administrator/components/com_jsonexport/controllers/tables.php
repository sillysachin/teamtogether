<?php

/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2012 JOOCODE. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

class JsonExportControllerTables extends JControllerAdmin
{
  public function getModel($name = 'Tables', $prefix = 'JsonExportModel')
  {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }
}
