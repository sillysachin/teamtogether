<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: com_menus.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class JSNPowerAdminBarSupportMenus extends JSNPowerAdminBarPreviewAdapter
{
	public function getPreviewLink ()
	{
		if (!isset($this->params['view']) || 
			!isset($this->params['layout']) || 
			!isset($this->params['id']) ||
			$this->params['view'] != 'item' ||
			$this->params['layout'] != 'edit')
			return parent::getPreviewLink();
		
		$dbo = JFactory::getDBO();
		$dbo->setQuery(sprintf('SELECT id, link FROM #__menu WHERE id=%d', $this->params['id']));
		$result = $dbo->loadObject();

		if (empty($result)) {
			return parent::getPreviewLink();
		}
		
		return sprintf('%s&Itemid=%d', $result->link, $result->id);
	}
}