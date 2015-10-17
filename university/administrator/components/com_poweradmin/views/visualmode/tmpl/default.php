<?php
/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id: default.php 12506 2012-05-09 03:55:24Z hiennh $
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>	

<div id="jsn-page-container" class="jsn-visual-layout">
	<!-- LEFT LAYOUT (MENU AREA) -->
	<div class="pane ui-layout-west" id="jsn-vsleft-panel">
		<div class="jsn-leftpanel-header" id="jsn-leftpanel-header">
			<h3 class="jsn-leftpanel-header-title">
				<?php echo JText::_('MENU');?>
			</h3>
		</div>
		<div class="jsn-menu-selector-container">
			<div class="jsn-menu-selector-container_inner">
				<!-- Dropdown list menu type for choose -->
				<div id="jsn-rawmode-menu-selector" class="jsn-menu-selector">
					<?php echo $this->jsnmenuitems->menuTypeListing(); ?>
				</div>
				<!-- Menu items render -->
				<div id="jsn-menu-listing-area">
					<?php echo $this->jsnmenuitems->render();?>
				</div>
			</div>
		</div>
	</div>
	<!-- RIGHT LAYOUT (LAYOUT RENDER) -->
	<div class="pane ui-layout-center" id="jsn-vsright-panel">
		<!-- TOOLS BAR FOR RIGHT LAYOUT -->
		<div class="jsn-rightpanel-header" id="jsn-rightpanel-header">
			<h3 class="jsn-rightpanel-header-title">
				<?php echo JText::_('PAGE');?>
			</h3>
			<div class="jsn-rightpanel-header-tools">			
				<span class="jsn-toolbar-button unpublish-modules">
					<input type="checkbox" id="unpublish-modules" /><label for="unpublish-modules" ><span class="button-state">&nbsp;</span><?php echo JText::_('JSN_TOOLBAR_UNPUBLISH_MODULES');?></label>
				</span>
				<span class="jsn-toolbar-button inactive-positions">
					<input type="checkbox" id="inactive-positions" /><label for="inactive-positions" ><span class="button-state">&nbsp;</span><?php echo JText::_('JSN_TOOLBAR_INACTIVE_POSITIONS');?></label>
				</span>
				<span class="jsn-toolbar-button module-highlights">
					<input type="checkbox" id="module-highlights" /><label for="module-highlights" ><span class="button-state">&nbsp;</span><?php echo JText::_('JSN_TOOLBAR_MODULE_HIGHLIGHTS');?></label>
				</span>
			</div>
			<div class="clearbreak"></div>
		</div>		
		<!-- IFRAME SHOW RENDER PAGE -->
		<div class="visualmode-frame" id="visualmode-frame">
			<iframe id="jsn-visual-layout-frame" class="overview" name="jsnrender" src="<?php JSN_RENDER_PAGE_URL.$this->render_url;?>" scrolling="yes" ></iframe>
		</div>
	</div>
</div>