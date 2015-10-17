<?php

/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2012 JOOCODE. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

class JsonExportControllerSettings extends JControllerAdmin {

  public function save() {
    $app = JFactory::getApplication();
    $settings = $app->input->get('settings', '', 'raw');

    $table = JTable::getInstance('extension');
    $db = JFactory::getDBO();
    $db->setQuery('SELECT extension_id FROM #__extensions WHERE type="component" AND element="com_jsonexport"');
    $extension_id = $db->loadResult();
    $table->load($extension_id);

    $post['option'] = 'com_jsonexport';
    $post['params']['settings'] = $settings;

    // End checkboxes

    $table->bind($post);
    $table->store();
    exit();
  }

}
