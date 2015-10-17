<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id: default.php 16037 2012-09-14 05:08:42Z hiepnv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}

// Display about
JSNPwgenerate::about();
