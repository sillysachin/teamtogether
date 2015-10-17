<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Load base class
if (is_file(JPATH_ROOT . '/libraries/joomla/filesystem/archive/zip.php')) {
	require_once JPATH_ROOT . '/libraries/joomla/filesystem/archive/zip.php';
}
else {
	require_once JPATH_ROOT . '/libraries/joomla/archive/zip.php';
}

/**
 * @package     JSNTPLFramework
 * @subpackage  Widget
 * @since       1.0.0
 */
class JSNTplArchiveZip extends JArchiveZip
{
	/**
	 * Converts a UNIX timestamp to a 4-byte DOS date and time format
	 * (date in high 2-bytes, time in low 2-bytes allowing magnitude
	 * comparison).
	 *
	 * @param   integer  $unixtime  The current UNIX timestamp.
	 *
	 * @return  integer  The current date in a 4-byte DOS format.
	 *
	 * @since   11.1
	 */
	protected function _unix2DOSTime($unixtime = null)
	{
		$timearray = (is_null($unixtime)) ? getdate() : getdate($unixtime);

		if ($timearray['year'] < 1980)
		{
			$timearray['year'] = 1980;
			$timearray['mon'] = 1;
			$timearray['mday'] = 1;
			$timearray['hours'] = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		}

		return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) |
			($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
	}
}
