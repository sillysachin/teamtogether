<?php
/**
 * @version     $Id: poweradmin.php 16454 2012-09-26 09:13:12Z hiepnv $
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Poweradmin component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_poweradmin
 * @since       1.6
 */
class PoweradminHelper
{

	/**
	 * Method to add side menu
	 *
	 * @param   string  $vName  The name of the active view
	 *
	 * @return	void
	 */
	public static function addSubmenu($vName)
	{
		if (JRequest::getVar('tmpl') != 'component'
				&&  JRequest::getVar('tmpl') != 'ajax'
				&& !JRequest::getVar('ajax')
				&& $vName != 'installer' )
		{
			JSNMenuHelper::addEntry(
					'pa-submenu-rawmode',
					'JSN_POWERADMIN_MENU_RAWMODE_TEXT',
					'index.php?option=' . JRequest::getCmd('option', 'com_poweradmin') . '&view=rawmode',
					$vName == 'rawmode',
					'administrator/components/com_poweradmin/assets/images/icons-16/icon-monitor.png',
					'pa-submenu'
			);

			JSNMenuHelper::addEntry(
					'pa-submenu-search',
					'JSN_POWERADMIN_MENU_SITESEARCH_TEXT',
					'index.php?option=' . JRequest::getCmd('option', 'com_poweradmin') . '&task=search.query',
					$vName == 'search',
					'administrator/components/com_poweradmin/assets/images/icons-16/icon-search.png',
					'pa-submenu'
			);

			JSNMenuHelper::addEntry(
					'pa-submenu-configuration',
					'JSN_POWERADMIN_MENU_CONFIGURATION_TEXT',
					'index.php?option=' . JRequest::getCmd('option', 'com_poweradmin') . '&view=configuration',
					$vName == 'configuration',
					'administrator/components/com_poweradmin/assets/images/icons-16/icon-cog.png',
					'pa-submenu'
			);

			JSNMenuHelper::addEntry(
					'pa-submenu-help',
					'JSN_POWERADMIN_MENU_HELP_TEXT',
					'index.php?option=' . JRequest::getCmd('option', 'com_poweradmin') . '&view=about',
					$vName == 'about',
					'administrator/components/com_poweradmin/assets/images/icons-16/icon-star.png',
					'pa-submenu'
			);


			// Add submenu of Config
			JSNMenuHelper::addEntry(
					'global-params', JText::_('JSN_EXTFW_CONFIG_GLOBAL_PARAMETERS'), 'index.php?option=com_poweradmin&view=configuration&s=configuration&g=configs', false, '', 'pa-submenu.pa-submenu-configuration'
			);

			JSNMenuHelper::addEntry(
					'languages',  JText::_('JSN_EXTFW_CONFIG_LANGUAGES'), 'index.php?option=com_poweradmin&view=configuration&s=configuration&g=langs', false, '', 'pa-submenu.pa-submenu-configuration'
			);

			JSNMenuHelper::addEntry(
					'permissions', JText::_('JSN_EXTFW_CONFIG_PERMISSIONS'), 'index.php?option=com_poweradmin&view=configuration&s=configuration&g=permissions', false, '', 'pa-submenu.pa-submenu-configuration'
			);


			JSNMenuHelper::addEntry(
					'update', JText::_('JSN_EXTFW_CONFIG_UPDATE'), 'index.php?option=com_poweradmin&view=configuration&s=configuration&g=update', false, '', 'pa-submenu.pa-submenu-configuration'
			);

// 			JSNMenuHelper::addEntry(
// 					'extensions', JText::_('JSN_POWERADMIN_EXTPAGE_SUPORTED_EXT'), 'index.php?option=com_poweradmin&view=configuration&s=maintainence&g=extensions', false, '', 'pa-submenu.pa-submenu-configuration'
// 			);

			// Render menu
			JSNMenuHelper::render('pa-submenu');

		}

	}

	private static $_cachedManifest = null;
    private static $_installedComponents = null;

    function getAssetsPath()
    {
        return JURI::root().'administrator/components/com_poweradmin/assets/';
    }

    /**
     * Retrieve current version of PowerAdmin from manifest file
     * @return string version
     */
    public static function getVersion ()
    {
        return self::getCachedManifest()->version;
    }


    /**
     * Retrieve cached manifest information from database
     * @return object
     */
    public static function getCachedManifest ($extension = 'com_poweradmin')
    {
        if (self::$_cachedManifest === null) {
            $dbo = JFactory::getDbo();
            $dbo->setQuery(
                sprintf(
                    'SELECT manifest_cache FROM #__extensions WHERE element=%s LIMIT 1',
                    $dbo->quote($extension)
                )
            );

            self::$_cachedManifest = json_decode($dbo->loadResult());
        }

        return self::$_cachedManifest;
    }

    /**
    * Return array of search coverage
    */
    public static function getSearchCoverages()
    {
    	$config = JSNConfigHelper::get('com_poweradmin');
    	$searchCoveragesOrder   = json_decode($config->search_coverage) ;
    	$coverages	= array();
    	if (count($searchCoveragesOrder) > 0) {
    		$configCoverages		= json_decode($config->search_coverage);
    		$configCoveragesOrder   = explode(",", $config->search_coverage_order) ;
    		if (count($configCoveragesOrder) > 0) {
    			foreach ($configCoveragesOrder as $_cov){
    				if (in_array($_cov, $configCoverages)){
    					array_push($coverages, $_cov);
    				}
   				}
   			}else{
   				$coverages	= $configCoverages;
   			}

    	}else{
	        $coverages = array(
	            'articles',
	            'categories',
	            'components',
	            'modules',
	            'plugins',
	            'menus',
	            'templates',
	            'users'
	        );

	        include_once (JPATH_ROOT . '/administrator/components/com_poweradmin/helpers/extensions.php');
	        $installedComponents = self::getInstalledComponents();
	        $supportedList	= JSNPaExtensionsHelper::getSupportedExtList();

	        if (count($supportedList))
	        {
				foreach ($supportedList as $extName=>$value)
				{
	 				if (in_array($extName, $installedComponents))
	 		        {
			            $coverages[] = $value->coverage;
			        }

				}
	        }

   		}

    	return $coverages;
    }

    /**
     * Retrieve list installed components
     * @return mixed
     */
    public static function getInstalledComponents ()
    {
        if (self::$_installedComponents == null) {
            $dbo = JFactory::getDBO();
            $dbo->setQuery("SELECT element FROM #__extensions WHERE type='component'");

            self::$_installedComponents = $dbo->loadColumn();
        }

        return self::$_installedComponents;
    }

    /**
     * Genarate url with suffix is current
     * version of jsn poweradmin
     */
    public static function makeUrlWithSuffix($fileUrl)
    {
        $currentVersion = '';
        if($fileUrl){
            $currentVersion     = self::getVersion();
            $fileUrl    .= '?v=' . $currentVersion;
        }
        return $fileUrl;
    }
}
