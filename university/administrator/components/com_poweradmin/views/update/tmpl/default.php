<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id: default.php 16038 2012-09-14 05:10:06Z hiepnv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$products	=	JSNPaExtensionsHelper::getDependentExtensions();
// Display config form
JSNUpdateHelper::render($this->product, $products, $this->redirAfterFinish);

