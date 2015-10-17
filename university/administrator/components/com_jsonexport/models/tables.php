<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

class JsonExportModelTables extends JModelList {

  protected function getListQuery() {
    $app = JFactory::getApplication('administrator');
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($db->quoteName('#__jsonexport_tables').' AS a');

    $search = $app->input->get('search');
    if (!empty($search)) {
      $search = $db->Quote('%'.$db->escape($search, true).'%');
      $query->where('a.tableName LIKE '.$search);
    }

    $published = $app->input->get('status');
    if (is_numeric($published)) {
      $query->where('a.status = '.(int) $published);
    } elseif ($published === '') {
      $query->where('(a.status IN (0, 1))');
    }

    // Add the list ordering clause.
    $orderCol = $app->input->get('ordering', 'a.tableName');
    $orderDirn  = $app->input->get('direction', 'ASC');
    $query->order($db->escape($orderCol.' '.$orderDirn));
    return $query;
  }

  /**
   * Method to auto-populate the model state.
   * Note. Calling getState in this method will result in recursion.
   */
  protected function populateState($ordering = null, $direction = null) {
    $app = JFactory::getApplication('administrator');

    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
    $this->setState('filter.access', $accessId);

    $categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
    $this->setState('filter.category_id', $categoryId);

    $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
    $this->setState('filter.language', $language);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_jsonexport');
    $this->setState('params', $params);
  }

}
