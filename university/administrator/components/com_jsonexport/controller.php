<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class JsonExportController extends JControllerLegacy
{
  function display($cachable = false, $urlparams = false) {
    if (!isset($this->input)) $this->input = JFactory::getApplication()->input;

    $view = $this->input->get('view', 'Tables');
    $this->input->set('view', $view);

    if (!$this->tableHasItems()) {
      $this->populateItems();
    } else {
      $this->checkIfTablesHaveChanged();
    }

    parent::display($cachable);
  }

  protected function tableHasItems() {
      // Build the query for the ordering list.
     $query = 'SELECT id FROM #__jsonexport_tables';
     $db = JFactory::getDBO();
     $db->setQuery($query);
     return count($db->loadObjectList());
  }

   protected function checkIfTablesHaveChanged() {
     $db = JFactory::getDBO();

      // Build the query for the ordering list.
     $query = 'SELECT tableName FROM #__jsonexport_tables';
     $db->setQuery($query);
     $tablesAlreadyProcessed = $db->loadObjectList();

     $query = 'SHOW TABLES';
     $db->setQuery($query);
     $tablesInDb = $db->loadObjectList();

     if (count($tablesAlreadyProcessed) != count($tablesInDb)) {

       $identifier = '';
       foreach ($tablesInDb[0] as $key => $value) {
         $identifier = $key;
       }

       //delete tables I don't have any more
       foreach($tablesAlreadyProcessed as $mytable) {
         $weStillHaveIt = false;

         foreach($tablesInDb as $table) {
           if ($mytable->tableName == $table->$identifier) {
             $weStillHaveIt = true;
           }
         }

         if (!$weStillHaveIt) {
           $query = 'DELETE FROM #__jsonexport_tables WHERE tableName = "'.$mytable->tableName.'"';
           $db->setQuery($query);
           $db->query();
         }
       }

       //add new tables
       foreach($tablesInDb as $table) {
         $weHaveIt = false;
         foreach($tablesAlreadyProcessed as $mytable) {
           if ($mytable->tableName == $table->$identifier) {
             $weHaveIt = true;
           }
         }

         if (!$weHaveIt) {
           $query = 'INSERT INTO #__jsonexport_tables (tableName, status) VALUES ("'.$table->$identifier.'", 0)';
           $db->setQuery($query);
           $db->query();
         }
       }
     }
   }

   protected function populateItems() {
      // Build the query for the ordering list.
     $query = 'SHOW TABLES';
     $db = JFactory::getDBO();
     $db->setQuery($query);
     $tables = $db->loadObjectList();

     $identifier = '';
     foreach ($tables[0] as $key => $value) {
       $identifier = $key;
     }

     foreach($tables as $table) {
       $query = 'INSERT INTO #__jsonexport_tables (tableName, status) VALUES ("'.$table->$identifier.'", 0)';
       $db->setQuery($query);
       $db->query();
     }
   }

}
