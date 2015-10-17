<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Poweradmin
 * @version   $Id: view.html.php 14934 2012-08-10 07:53:33Z thailv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration view of JSN Poweradmin component
 */
class PowerAdminViewConfiguration extends JSNConfigView
{

	/**
	 * Display method
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		// Get config parameters
		$config = JSNConfigHelper::get('com_poweradmin');
		$this->_document = JFactory::getDocument();

		// Set the toolbar
		JToolBarHelper::title(JText::_('JSN_POWERADMIN_CONFIGURATION_TITLE'), 'maintenance');

		$this->_addAssets();
		// Display the template
		parent::display($tpl);
	}

	private function _addAssets()
	{
 		JSNHtmlAsset::addStyle(JSN_FRAMEWORK_ASSETS . '/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
	}

}
