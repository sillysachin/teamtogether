<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id$
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class PowerAdminViewUpdate extends JSNUpdateView
{
	public function display ($tpl = null)
	{
		// Get config parameters
		$config = JSNConfigHelper::get();

		// Set the toolbar
		JToolBarHelper::title(JText::_('JSN_POWERADMIN_UPDATE_TITLE'));

		// Add assets
		$document = JFactory::getDocument();
 		JSNHtmlAsset::addStyle(PoweradminHelper::makeUrlWithSuffix(JSN_URL_ASSETS.'/joomlashine/css/jsn-gui.css'));

		
		$redirAfterFinish = 'index.php?option=com_poweradmin&view=about';
		$this->assign('redirAfterFinish', $redirAfterFinish);
		// Display the template
		parent::display($tpl);
	}

	
}