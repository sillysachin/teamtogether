<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id: view.html.php 15407 2012-08-23 07:27:04Z hiepnv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// Import Joomla view library
jimport('joomla.application.component.view');
JSNFactory::localimport('libraries.joomlashine.html.pwgenerate');
/**
 * About view of JSN Poweradmin component
 */
class PoweradminViewAbout extends JViewLegacy
{

	/**
	 * Display method
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		// Get config parameters
		$config = JSNConfigHelper::get();
		$this->_document = JFactory::getDocument();		

		JToolBarHelper::title(JText::_('JSN_POWERADMIN_ABOUT_TITLE'), 'about');
		//PoweradminHelper::addSubmenu(JRequest::getCmd('view'));
		// Assign variables for rendering
		$this->assignRef('msgs', $msgs);			
		// Display the template
		parent::display($tpl);
	}
	
}
