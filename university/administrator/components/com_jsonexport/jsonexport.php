<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');

//Prevent Magic Quotes problem
if(function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime())
    set_magic_quotes_runtime(false);

if(get_magic_quotes_gpc()) {
    array_stripslashes($_POST);
    array_stripslashes($_GET);
    array_stripslashes($_COOKIES);
}

function array_stripslashes(&$array) {
    if(is_array($array))
        while(list($key) = each($array))
            if(is_array($array[$key]))
                array_stripslashes($array[$key]);
            else
                $array[$key] = stripslashes($array[$key]);
}
//End Prevent Magic Quotes problem

jimport('joomla.application.component.controller');

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$version = new JVersion();

if ($app->input->get('format') != 'json') {
  //Ensure the Mootools fix for Ember is put on top of the scripts
  $headerdata = $document->getHeadData();
  $scripts = $headerdata['scripts'];

  function array_unshift_assoc($arr, $key, $val) { 
      $arr = array_reverse($arr, true); 
      $arr[$key] = $val; 
      return array_reverse($arr, true); 
  } 

  $scripts = array_unshift_assoc($scripts, JURI::root().'media/com_jsonexport/js/lib/mootools-fix.js', array('mime' => 'text/javascript', 'defer' => false, 'async' => false));
  $headerdata['scripts'] = $scripts;
  $document->setHeadData($headerdata);
}

$document->addScriptDeclaration("var jqext = jQuery.prototype.extend;
  delete jQuery.prototype.extend;
  Function.prototype.extend = ext;
  delete jQuery.prototype.extend;
  jQuery.prototype.extend = jqext;
");

if ($version->isCompatible('3.0')) {
  JHtml::_('jquery.framework');
} else {
  $document->addScript(JURI::root().'media/com_jsonexport/jui/js/jquery.min.js');
  $document->addScript(JURI::root().'media/com_jsonexport/jui/js/jquery-noconflict.js');
  $document->addScript(JURI::root().'media/com_jsonexport/jui/js/bootstrap.js');
  $document->addStyleSheet(JURI::root().'media/com_jsonexport/jui/css/bootstrap.css');
}

$app->JComponentTitle = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $document->addScript(JURI::root().'media/com_jsonexport/js/lib/handlebars.runtime-1.3.0.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/lib/ember-1.4.0.min.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/lib/ember-rest.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/lib/bootstrap-tooltip.js');

  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/templates.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/app.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/router.js');

  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/models/common-model.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/models/setting.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/models/table.js');

  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/controllers/common.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/controllers/settings.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/controllers/tables.js');

  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/helpers/printFeaturedIcon.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/helpers/printStatusIcon.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/helpers/deferredHelper.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/helpers/calculateOrderTotal.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/helpers/printReadableCountryName.js');

  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/common.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/common__form.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/common__index.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/settings__general.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/settings__index.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/tables__index.js');
  $document->addScript(JURI::root().'media/com_jsonexport/js/admin/views/tables__index__single-item.js');

  $document->addStyleSheet(JURI::root().'media/com_jsonexport/css/admin/main.css');
  if (!$version->isCompatible('3.0')) {
    $document->addStyleSheet(JURI::root().'media/com_jsonexport/css/admin/joomla-2.5.css');
    $document->addStyleSheet(JURI::root().'media/com_jsonexport/jui/css/icomoon.css');
  }

  JToolBarHelper::title('', 'jsonexport');
}

$controller = JControllerLegacy::getInstance('JsonExport');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
