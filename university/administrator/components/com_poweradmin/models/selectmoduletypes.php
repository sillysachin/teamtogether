<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: selectmoduletypes.php 12779 2012-05-18 02:55:18Z binhpt $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JSNFactory::import('components.com_modules.models.select');

class PoweradminModelSelectmoduletypes extends ModulesModelSelect
{
	/**
	 * Method to get the client object
	 *
	 * @return	void
	 * @since	1.6
	 */
	function &getClient()
	{
		return $this->_client;
	}
	
	/**
	 * Custom clean cache method for different clients
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache('com_poweradmin', $this->getClient());
	}
}