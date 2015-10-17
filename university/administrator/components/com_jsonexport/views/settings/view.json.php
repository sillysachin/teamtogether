<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class JsonExportViewSettings extends JViewLegacy
{
  function display($tpl = NULL) {
    $db = JFactory::getDBO();
    $db->setQuery('SELECT params FROM #__extensions WHERE type="component" AND element="com_jsonexport"');
    $params = $db->loadResult();
    echo $params;
    exit();
  }
}
