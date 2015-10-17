<?php
/**
 * @version     $Id$
 * @package     JSNPoweradmin
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<a href="" id="jsn-fullscreen">&nbsp;</a>
<div class="jsn-master" style="width: 100%; height: 100%; z-index: 1001;">
<div id="page-loading" class="jsn-bgloading"><i class="jsn-icon32 jsn-icon-loading"></i></div>
</div>
<div id="page-loading" class="jsn-bgloading hide"><i class="jsn-icon32 jsn-icon-loading"></i></div>
<div id="jsn-rawmode-layout" class="jsn-bootstrap">
	<div id="jsn-rawmode-leftcolumn" class="pane ui-layout-west">
		<div class="jsn-heading-panel clearafter">
			<span class="jsn-toggle-button">
			   <button class="btn-disabled btn btn-success" id="menu-manager" title="<?php echo JText::_('JSN_RAWMODE_SHOW_PUBLISHED_MENUITEM');?>">
			   	<i class="icon-eye-open icon-white"></i>
			   	<i class="icon-eye-close"></i>
			   </button>
			</span>
			<h3 class="jsn-heading-panel-title">
				<?php echo strtoupper(JText::_('JSN_RAWMODE_MENU'));?>
			</h3>
		</div>
		<div class="jsn-menu-selector-container">
			<div class="jsn-menu-selector-container_inner">
				<!-- Dropdown list menu type for choose -->
				<?php echo $this->jsnmenuitems->menuTypeDropDownList(); ?>

				<div id="jsn-rawmode-menuitem-container">
					<!-- Menu items render -->
					<?php echo $this->jsnmenuitems->render();?>
				</div>
			</div>
		</div>
	</div>
	<div id="jsn-rawmode-center" class="pane ui-layout-center">
		<div class="jsn-heading-panel clearafter">
			<span class="jsn-toggle-button">
				<button class="btn-disabled btn btn-success" id="component-manager">
					<i class="icon-eye-open icon-white"></i>
			   		<i class="icon-eye-close"></i>
				</button>
			</span>
			<h3 class="jsn-heading-panel-title">
				<?php echo strtoupper(JText::_('JSN_RAWMODE_COMPONENT'));?>
			</h3>
		</div>
		<div id="jsn-component-details">
			<?php echo $this->component; ?>
		</div>
	</div>
	<div id="jsn-rawmode-rightcolumn" class="ui-layout-east">
		<div class="jsn-heading-panel clearafter">
			<h3 class="jsn-heading-panel-title jsn-module-panel-header">
				<?php echo strtoupper(JText::_('JSN_RAWMODE_MODULES'));?>
			</h3>
			<div class="jsn-module-spotlight-filter" id="module_spotlight_container">
				<input type="text" id="module_spotlight_filter" />
				<a class="close" href="javascript:void(0)"></a>
			</div>
			<span class="jsn-toggle-button">
			   <button class="btn-disabled btn btn-success" id="module-manager">
					<i class="icon-eye-open icon-white"></i>
			   		<i class="icon-eye-close"></i>
			   </button>
			</span>
	   </div>
	   <div id="module-show-options" class="module-show-options">
	   		<div class="option">
	   			<label for="show_unpublished_positions" class="checkbox">
	   			<input type="checkbox" id="show_unpublished_positions" />
	   			<?php echo JText::_('SHOW_UNPUBLISHED_POSITIONS');?></label>
	   		</div>
	   		<div class="option">
	   			<label for="show_unpublished_modules" class="checkbox">
	   			<input type="checkbox" id="show_unpublished_modules" />
	   			<?php echo JText::_('SHOW_UNPUBLISHED_MODULES');?></label>
	   		</div>
	   </div>
	   <div id="modules-list">
			<div id="module-list-container">
				<?php echo $this->modules; ?>
			</div>
		</div>
    </div>
</div>
<div class="jsn-context-menu" id="position-context"></div>
<div class="jsn-context-menu" id="module-context"></div>
<div class="jsn-context-menu" id="site-manager-eader-context"></div>
<div class="jsn-context-menu" id="site-manager-switch-context"></div>

<?php
$products	=	JSNPaExtensionsHelper::getDependentExtensions();
// Display footer.
JSNHtmlGenerate::footer($products);
?>