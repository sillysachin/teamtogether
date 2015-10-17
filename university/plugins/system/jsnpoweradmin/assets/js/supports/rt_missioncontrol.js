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
		return $('mctrl-menu');
	},

	getStatusPosition: function () {
		return null;
	},

	getComponentMenu: function (menubar) {
		return $$('.li-extend .level2>li');
	},

	getExtensionMenu: function (menubar) {
		$(menubar).getElements('.level2').setStyles({'min-width': '200px', 'margin-left': '0px'});
		return this.getComponentMenu(menubar);
	},

	getMenuSeparator: function () {
		return new Element('li', { 'class': 'misioncontrol separator', 'style': 'background-color:#4D4D4D; height: 5px; padding: 0;' }).adopt(new Element('span'))
	},

	createMenuItem: function (title, url, target, className) {
		return new Element('li').adopt(
			new Element('a', { 'class': 'item', 'href': url, 'target': target, 'text': title })
		);
	},

	createMenuContainer: function () {
		return new Element('ul', { 'class': 'parent level3' })
	},

	formatParentMenu: function (menu) {	
		menu.getElement('a').addClass('daddy');
	},
	
	getParentMenuClass: function (){
		return 'daddy';
	},
	
	getParentSubMenuClass: function (){
		return 'daddy';
	},
	
	getPageTitle: function (urlparams) {
		if ($('toolbar-box') == null) {
			return '';
		}
		var _wrapper = $('toolbar-box').getElement('.pagetitle');
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