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

var JSNHistory = new Class
({
	initialize: function ()
	{
		var self = this;
		
		// Skip if current page is configuration page
		if (window.location.search.indexOf('option=com_config') != -1) {
			return;
		}

		// Save last clicked link on page to cookie
		$$('#element-box a[href*="?option"], #menu a, #jsn-adminbar a').addEvent('click', function (e) {
			var href = this.href;
			if (href.indexOf('?') != -1) {
				Cookie.write('jsn-poweradmin-last-link', href.substring(href.indexOf('?') + 1));
			}
		});
		
		var title = this.findTitle(),
			description = this.findDesc(),
			checkboxes = $$('#element-box input[name=boxchecked]');
		
		// Save information of list page
		if (title.length == 0) {
			return Cookie.write('jsn-poweradmin-list-page', (checkboxes.length > 0) ? JSON.encode(this.findPage()) : null);
		}
		
		var page 		= JSON.decode(Cookie.read('jsn-poweradmin-list-page')),
			clickedLink = Cookie.read('jsn-poweradmin-last-link'),
			sessionKey  = Cookie.read('jsn-poweradmin-post-session'),
			pageKey		= Cookie.read('jsn-poweradmin-page-key');
			
		if (pageKey == null) {
			return;
		}
		
		var entry		= {
			title			: title[0].value,
			description		: (description.length > 0) ? this.stripTags(description[0].value) : '',
			pageKey			: pageKey,
			postSessionKey	: (sessionKey != null) ? sessionKey : undefined,
			lastClickedLink	: (clickedLink != null)? clickedLink: undefined,
			currentLink		: (clickedLink != null)? window.location.search.substring(1) : undefined
		};
		
		if (page != null) {
			entry.iconCss 	= page.css;
			entry.iconPath 	= page.path;
			entry.name 		= page.name;
			entry.parent 	= page.parent;
			entry.params 	= page.key
		}
		
		new Request.JSON({
			url: 'index.php?option=com_poweradmin&task=history.save',
			onSuccess: function (response) {
				self.attachHistory(response, title[0]);
			}
		})
		.post(entry);
	},
	
	/**
	 * Find title of entry that will save to database
	 * @return Element
	 */
	findTitle: function ()
	{
		var inputs = [];
		
		$$('input[name]').each(function (input) {
			var name = input.name.toLowerCase();

			if ((name.match(/^(title|name|subject|label)$/i)) ||
			   (name.match(/^jform\[(title|name|subject|label)\]$/i)) ||
			   (name.match(/^(title|name|subject|label)/i)) ||
			   (name.match(/^jform\[(title|name|subject|label)[^\]]*\]$/i)) ||
			   (name.match(/(title|name|subject|label)/i)) ||
			   (name.match(/(title|name|subject|label)$/i))) {
				if (input.value){
					inputs.push(input);
				}
			}
		});
		
		return inputs;
	},
	
	/**
	 * Find description of entry
	 * @return Element
	 */
	findDesc: function ()
	{
		var textareas = [];
		
		$$('textarea[name]').each(function (textarea) {
			var name = textarea.name.toLowerCase();
			
			if (name.match(/articletext|description|desc|intro|introtext|introduction|about|note|content/i) && !name.match(/meta/)) {
				textareas.push(textarea);
			}
		});
		
		if (textareas.length == 0) {
			$$('span.mod-desc, span.plg-desc').each(function (item) {
				textareas.push({
					value: item.get('text')
				});
			});
		}
		
		return textareas;
	},
	
	/**
	 * Find icon url of specified menu
	 * @param menu
	 * @return string
	 */
	findIconPath: function (menu)
	{
		var backgroundImage = menu.getStyle('background-image').trim(),
			regex = /^url\(\s*['|"]?\s*([^\)]+)\s*['|"]?\s*\)$/i;
			
		if (!backgroundImage.match(regex)) {
			return '';
		}
		
		var image = regex.exec(backgroundImage)[1];
		var uri   = window.location.pathname;
		
		if (uri.indexOf('index.php') != -1) {
			uri = uri.substring(0, uri.indexOf('index.php'));
		}
		
		if (image.indexOf(uri) != -1) {
			image = image.substring(image.indexOf(uri) + uri.length);
		}
		
		return image;
	},
	
	/**
	 * Extract information of an menu and return as object
	 * @param menu
	 * @return object
	 */
	getMenuInformation: function (menu, parentMenu)
	{
		return {
			css		: menu.getProperty('class'),
			icon	: this.findIconPath(menu),
			name	: menu.getProperty('text'),
			key		: Cookie.read('jsn-poweradmin-page-key'),
			parent	: (parentMenu != null) ? parentMenu.get('text') : ''
		};
	},
	
	/**
	 * Retrieve information of the active page
	 * @return object
	 */
	findPage: function ()
	{
		var menubar = $('menu') || $('jsn-adminbar-menu'),
			queryString = window.location.search,
			params = {},
			icon = {},
			self = this;
		
		// Skip if menubar is disabled
		if (menubar.hasClass('disabled')) { return null; }
		if (queryString.indexOf('?') != -1) { queryString = queryString.substring(1); }
		
		// Convert query string to object
		params = queryString.parseQueryString();
		
		// Find component name inside page when it isn't 
		// contains on query string
		if (params['option'] === undefined) {
			var inputs = $$('input[name=option]');
			if (inputs.length == 0) {
				return self.getMenuInformation(menubar.getElement('a[href="index.php"]'));
			}
			
			params['option'] = inputs[0].value;
		}
		
		// Find information for component that used com_categories
		// for category management
		if (params['option'] == 'com_categories' && params['extension'] !== undefined) {
			var menu 		= menubar.getElement('a[href*="option=com_categories&extension='+params['extension']+'"]'),
				component 	= menubar.getElement('a[href*="option='+params['extension']+'"]');
				
			if (menu == null) {
				menu = component;
			}
			
			return self.getMenuInformation(menu, component);
		}
		
		var menuRoot = menubar.getElement('a[href="index.php?option='+params['option']+'"]'),
			menu	 = menuRoot;
			
		menubar
			.getElements('a[href*="option='+params['option']+'"]')
			.each(function (menuItem) {
				var menuLink = menuItem.getProperty('href'),
					menuQueryString = (menuLink.indexOf('?') != -1) ? menuLink.substring(menuLink.indexOf('?') + 1) : '',
					menuParams = menuQueryString.parseQueryString();
					
				var sameController = (params['controller'] !== undefined && 
									  menuParams['controller'] !== undefined && 
									  params['controller'] == menuParams['controller']),
					sameView = (params['view'] !== undefined && menuParams['view'] !== undefined && params['view'] == menuParams['view']),
					sameTask = (params['task'] !== undefined && menuParams['task'] !== undefined && params['task'] == menuParams['task']),
					sameViewAndTask = sameView && sameTask,
					sameAll = sameViewAndTask && sameController;
					
				if (sameAll || sameViewAndTask || sameController || sameView || sameTask) {
					menu = menuItem;
					return false;
				}
			});
			
		if (menu == null)
			return null;
			
		return self.getMenuInformation(menu, menuRoot);
	},
	
	/**
	 * Append hidden fields to editing form to associate with
	 * saved history entry
	 * 
	 */
	attachHistory: function (id, field)
	{
		var id 		= new Element('input', { 'type': 'hidden', 'value': id, 'name': 'jsn_history_id' }),
			title 	= new Element('input', { 'type': 'hidden', 'value': field.value, 'name': 'jsn_history_title' });
			
		field.addEvent('change', function () {
			title.setProperty('value', field.value);
		});
		
		id.inject(field, 'after');
		title.inject(field, 'after');
	},
	
	/**
	 * Remove html tag in a string
	 * @param string
	 * @param allowed
	 * @return string
	 */
	stripTags: function (input, allowed)
	{
		allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
	        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
	        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	    });
	}
});










