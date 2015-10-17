<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class JsonExportViewTables extends JViewLegacy
{
  function display($tpl = NULL) {
    $output = new JObject();
    $output->pagination = $this->get('Pagination');
    $output->items = $this->get('Items');

    echo json_encode($output);
    exit();
  }
}
