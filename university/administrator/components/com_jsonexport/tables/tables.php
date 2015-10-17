<?php
/** @package   com_jsonexport
 * @copyright Copyright (C) 2012 JOOCOMMERCE. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.database.table');

class JsonExportTableTables extends JTable {
  function __construct(&$db) {
    parent::__construct('#__jsonexport_tables', 'id', $db);
  }
}
