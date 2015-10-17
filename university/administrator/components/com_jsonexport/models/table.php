<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

class JsonExportModelTable extends JModelItem {

  public function getTable($type = 'Tables', $prefix = 'JsonExportTable', $config = array()) {
    return JTable::getInstance($type, $prefix, $config);
  }

  public function getItem($pk = null)
  {
    $app = JFactory::getApplication('administrator');
    $pk = (!empty($pk)) ? $pk : (int) $app->input->get('id', 0, 'INT');
    $table = $this->getTable();

    if ($pk > 0)
    {
      // Attempt to load the row.
      $return = $table->load($pk);

      // Check for a table object error.
      if ($return === false && $table->getError())
      {
        $this->setError($table->getError());
        return false;
      }
    }

    // Convert to the JObject before adding other data.
    $properties = $table->getProperties(1);
    $item = JArrayHelper::toObject($properties, 'JObject');

    if (property_exists($item, 'params'))
    {
      $registry = new JRegistry;
      $registry->loadString($item->params);
      $item->params = $registry->toArray();
    }

    return $item;
  }

}
