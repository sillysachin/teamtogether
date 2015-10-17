<?php

/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2012 JOOCODE. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

class JsonExportControllerTable extends JControllerAdmin
{
  public function save() {
    $app = JFactory::getApplication();
    $item = $app->input->get('item', '', 'ARRAY');

    // ob_start();
    // var_dump($item);
    // $contents = ob_get_contents();
    // ob_end_clean();
    // error_log($contents);

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);

    if ($item['id']) { //update
      $query->update('#__jsonexport_tables');
      if (isset($item['status'])) $query->set($db->quoteName('status') . ' = ' . $db->Quote($item['status']));
      $query->where('id='.$db->quote($item['id']));
    }

    $db->setQuery((string) $query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      error_log($e->getMessage());
    }

    $item_id = $db->insertid();
    echo $item_id; exit();
  }

}
