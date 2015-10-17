<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: poweradmin.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');
/**
 * @package		Joomla.Site
 * @subpackage	com_poweradmin
 * @since		1.5
 */
class PoweradminModelComponent extends JModelList
{
	protected $_context = 'com_poweradmin.component';
	/**
	 * 
	 * Config
	 * @param Array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		
	}
	/**
	 * 
	 * 
	 */
	public function getParams($pks)
	{
		$app	= JFactory::getApplication('site');
		$siteMenu = JMenu::getInstance('site');
		$menuParams = $siteMenu->getItem( $pks['Itemid'] );
		if ( isset($menuParams->params) ){
			$params = $menuParams->params;
		}else{
			$params = $this->getState()->get('params');
		}
		
		$JSNConfig = JSNFactory::getConfig();
		$JSNConfig->megreGlobalParams( $pks['option'], $params );
		
		return $params;
	}
	/**
	 * Get the articles in the category
	 *
	 * @return	mixed	An array of articles or false if an error occurs.
	 * @since	1.5
	 */
	function getItems()
	{
		
	}
}