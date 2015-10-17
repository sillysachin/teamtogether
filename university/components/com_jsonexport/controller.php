<?php

/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2012 JOOCODE. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Hello World Component Controller
 */
class JsonExportController extends JControllerLegacy
{
  function display($cachable = false, $urlparams = null) {
    $componentParams = json_decode(JComponentHelper::getParams('com_jsonexport')->get('settings'))->general;

    if (isset($componentParams->requireAuthentication) && $componentParams->requireAuthentication == "1") {
      $username = JRequest::getVar('username', '', 'get');
      $password = JRequest::getVar('password', '', 'get');

      if ($username != $componentParams->requiredUsername || $password != $componentParams->requiredPassword) {
        exit();
      }
    }

    $db = JFactory::getDBO();
    $table = JRequest::getVar('export_table', '', 'get');
    if (!$table) $table = JRequest::getVar('table', '', 'get'); //old parameter, still valid
    
    $query = "SELECT * FROM #__jsonexport_tables WHERE tableName = '$table' AND status = 1";
    $db->setQuery($query);

    if (count($db->loadObjectList()) > 0) {

      $return_arr = array();

      if (JRequest::getVar('fields', '', 'get')) {
        $fields = JRequest::getVar('fields', '', 'get');
        if (is_array($fields)) {
          $fields = $fields[0];
        }
        $fields = $db->escape($fields);
      }

      if (!isset($fields) || !$fields) $fields = '*';

      $limit = '';
      if (JRequest::getVar('limit', '', 'get')) {
        $limit = ' LIMIT ' . (int)$db->escape(JRequest::getVar('limit', '', 'get'));
      }

      $offset = '';
      if (JRequest::getVar('offset', '', 'get')) {
        $offset = ' OFFSET ' . (int)$db->escape(JRequest::getVar('offset', '', 'get'));
      }

      // Build the query for the ordering list.

      $query = "SELECT $fields FROM $table $limit $offset";
      $db->setQuery($query);
      $rows = $db->loadAssocList();


      echo json_encode($rows);
    }

    exit();
  }
}
