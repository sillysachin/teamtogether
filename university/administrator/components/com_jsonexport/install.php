<?php
/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

defined('_JEXEC') or die('Restricted access');

class com_jsonexportInstallerScript
{
  function preflight($type, $parent)
  {
    // $parent is the class calling this method
    // $type is the type of change (install, update or discover_install)
    $version = new JVersion();

    if (!$version->isCompatible('2.5')) {
      Jerror::raiseWarning(null, 'Sorry, only Joomla 2.5 and above is supported by this extension.');
      return false;
    }

    //echo '<style>.adminform {width: 100%;}</style><div style="margin: 0 auto; text-align: center"><a href="index.php?option=com_jsonexport"><img src="../media/com_jsonexport/img/install.png" /></a></div>';
  }
}
