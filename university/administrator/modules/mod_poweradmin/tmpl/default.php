<?php
defined('_JEXEC') or die('Access denied');

/*------------------------------------------------------------------------
# Full Name of JSN Extension(e.g: JSN PowerAdmin)
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
# @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
# @version $Id$
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
$lang 			= JFactory::getLanguage();
$document 		= JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/administrator/modules/mod_poweradmin/assets/css/mod_poweradmin_quickicon.css');
?>
<div class="row-striped">
	<div class="row-fluid">
		<div class="span12">
			
				<div class="jsn-poweradmin-quickicon">
					<div class="icon">
						<a href="<?php echo JRoute::_('index.php?option=com_poweradmin&view=rawmode') ?>" class="site-manager">
							<img src="<?php echo JUri::base(true) ?>/modules/mod_poweradmin/assets/images/site-manager.png" alt="<?php echo JText::_('MOD_POWERADMIN_SITEMANAGER') ?>">
							<span><?php echo JText::_('MOD_POWERADMIN_SITEMANAGER') ?></span>
						</a>
					</div>
				</div>

				<div class="jsn-poweradmin-quickicon">
					<div class="icon">
						<a href="<?php echo JRoute::_('index.php?option=com_poweradmin&task=search.query') ?>" class="site-search">
							<img src="<?php echo JUri::base(true) ?>/modules/mod_poweradmin/assets/images/site-search.png" alt="<?php echo JText::_('MOD_POWERADMIN_SITESEARCH') ?>">
							<span><?php echo JText::_('MOD_POWERADMIN_SITESEARCH') ?></span>
						</a>
					</div>
				</div>

				<div class="jsn-poweradmin-quickicon">
					<div class="icon">
						<a href="<?php echo JRoute::_('index.php?option=com_poweradmin&view=configuration') ?>" class="configuration">
							<img src="<?php echo JUri::base(true) ?>/modules/mod_poweradmin/assets/images/configuration.png" alt="<?php echo JText::_('MOD_POWERADMIN_CONFIGURATION') ?>">
							<span><?php echo JText::_('MOD_POWERADMIN_CONFIGURATION') ?></span>
						</a>
					</div>
				</div>

				<div class="jsn-poweradmin-quickicon">
					<div class="icon">
						<a href="<?php echo JRoute::_('index.php?option=com_poweradmin&view=about') ?>" class="about">
							<img src="<?php echo JUri::base(true) ?>/modules/mod_poweradmin/assets/images/about.png" alt="<?php echo JText::_('MOD_POWERADMIN_ABOUT') ?>">
							<span><?php echo JText::_('MOD_POWERADMIN_ABOUT') ?></span>
						</a>
					</div>
				</div>
			</div>
	</div>	
</div>