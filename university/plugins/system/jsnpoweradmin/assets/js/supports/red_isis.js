/*------------------------------------------------------------------------
 # JSN PowerAdmin
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # @version $Id
 -------------------------------------------------------------------------*/

var JSNAdminBarUIHelper = new Class
({
	getMenuPosition: function () {
		var moduleMenu = $('module-menu');		
		return moduleMenu;
		
	},

	getStatusPosition: function () {		
		return $('jsn-body-wrapper').getElement('#module-status');
	},

	getComponentMenu: function (menubar) {		
		var menu = menubar.getElement('#jsn-adminbar-menu'),
			poweradminMenu = menu.getElement('a[href="index.php?option=com_poweradmin"]');
			
		if (poweradminMenu != null) {
			return poweradminMenu
				.getParent()
				.getParent()
				.getChildren();
		}
	},

	getExtensionMenu: function (menubar) {		
		var menu = menubar.getElement('#jsn-adminbar-menu'),
			templateMenu = menu.getElement('a[href="index.php?option=com_templates"]') || menu.getElement('a[href="index.php?option=com_installer"]');

		if (templateMenu != null) {
			return templateMenu
				.getParent()
				.getParent()
				.getChildren();
		}
	},

	getMenuSeparator: function () {
		
		return new Element('li', { 'class': 'divider' }).adopt(new Element('span'));
	},

	createMenuItem: function (title, url, target, className) {		
		return new Element('li').adopt(
			//new Element('ins', {'class': 'icon-remove'})	
			).adopt(
			new Element('a', { 'class': className, 'href': url, 'target': target, 'text': title, 'addedby': 'jsn' })
		);
	},

	createMenuContainer: function () {		
		return new Element('ul');
	},

	formatParentMenu: function (menu) {		
		menu.getElement('a').addClass('node');
	},
	
	beforeRenderHook: function (obj)
	{
		 jQuery('nav>.navbar-inner ').hide();
	},
	
	afterCreateUiHook: function ()
	{		
		var moduleMenu = $('module-menu');
		moduleMenu.getElement('#menu').addClass('nav');
		$('jsn-adminbar').addClass('navbar');
		moduleMenu.getElements('#menu>.has-child').setProperty('class','dropdown');
		moduleMenu.getElements('#menu>.dropdown>ul').setProperty('class','dropdown-menu');
		moduleMenu.getElements('#menu>.dropdown>ul>li>a').setProperty('class','');
		moduleMenu.getElements('#menu>.dropdown>a').setProperty('class','dropdown-toggle').setProperty('data-toggle','dropdown');
		moduleMenu.getElements('#menu>.dropdown>ul .has-child').setProperty('class','dropdown-submenu');
		moduleMenu.getElements('#menu>.dropdown>ul .dropdown-submenu>a').addClass('class','dropdown-toggle').setProperty('data-toggle','dropdown');
		moduleMenu.getElements('#menu>.dropdown>ul .dropdown-submenu>ul').addClass('dropdown-menu');
		moduleMenu.getElements("#menu>.dropdown>ul .dropdown-submenu>ul a").setProperty('class','');
		$('jsn-body-wrapper').setStyle('margin-top','8px');
	},
	
	afterRenderHook: function (obj)
	{
		
	},
	
	getPageTitle: function (urlparams) {
		if ($$('header') == null) {
			return '';
		}
		var _wrapper = $$('header').getElement('.page-title')[0];		
		var pagetitle	= '';
		if (_wrapper != null) {
			var pagetitle = _wrapper.textContent;
			if (pagetitle != undefined && pagetitle != '') {
				pagetitle	= pagetitle;
			}else{
				pagetitle	= '';
			}
		}
		
		return pagetitle;		
	}
});