<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: history.php 16339 2012-09-24 10:22:59Z hiepnv $
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class PowerAdminTableHistory extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jsn_poweradmin_history', 'id', $db);
	}
}
