/**
 * @subpackage	com_poweradmin (JSN POERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/
(function ($) {
	$(function () {
		var root		= $('.template-item');
		var contextMenu = root.jsnSubmenu({ rebuild:true, rightClick:false });
		var activeItem  = null;
		var openedSid = $('#openedSid').attr('value');		
		
		if (contextMenu.isNew()) 
		{
			contextMenu.addItem(JSNLang.translate( 'TITLE_SUBMENU_EDIT_MENU_ITEM')).addEventHandler('click', function () {
				var templateId = $(activeItem).attr('sid');
				var editLink   = 'index.php?option=com_templates&task=style.edit&id=' + templateId;
				
				window.open(editLink);
				contextMenu.hide({});
			}).css('font-weight', 'bold');
			
			contextMenu.addItem(JSNLang.translate( 'JSN_POWERADMIN_TM_MAKE_DEFAULT')).addEventHandler('click', function () {
				var templateId = $(activeItem).attr('sid');
				var clientId   = $(activeItem).attr('clientId');
				var makeDefaultLink = 'index.php?option=com_poweradmin&task=templates.makeDefault&id=' + templateId + '&clientId=' + clientId + '&tmpl=component';
				
				$.getJSON(makeDefaultLink, function (response) {
					root.removeClass('default');
					activeItem.addClass('default');
				});
				
				contextMenu.hide({});
			});
			
			contextMenu.addItem(JSNLang.translate( 'TITLE_SUBMENU_DUPLICATE')).addEventHandler('click', function () {
				var templateId = $(activeItem).attr('sid');
				var actionLink = 'index.php?option=com_poweradmin&task=templates.duplicate&id=' + templateId + '&tmpl=component';
				
				$.getJSON(actionLink, function (response) {
					_appendNewStyle(response);
				});
				
				contextMenu.hide({});
			});
			
			var candelete = $('#candelete').attr('value');
			var canuninstall = $('#canuninstall').attr('value');
			if(candelete){
				contextMenu.addItem(JSNLang.translate( 'TITLE_SUBMENUTITLE_DELETE')).addEventHandler('click', function () {
					if (openedSid == $(activeItem).attr('sid')) {
						alert(JSNLang.translate( 'JSN_POWERADMIN_TM_CLOSE_BEFORE_DELETE'));
						contextMenu.hide({});
						return;
					}
					var templateName = $('.template-item-thumb', activeItem).text().trim();
					var answer = confirm (JSNLang.translate( 'JSN_POWERADMIN_TM_DELETE_STYLE_CONFIRM', {'{JSN_TMP_NAME}' : templateName}));
					if (!answer){
						return;
					}
					$.getJSON('index.php?option=com_poweradmin&task=templates.delete&id=' + $(activeItem).attr('sid'), function (response) {
						if (response.isDeleted == true)
							$(activeItem).remove();
					});
					contextMenu.hide({});
				});
			
			}
			
			if(canuninstall){
				contextMenu.addDivider();
				contextMenu.addItem(JSNLang.translate( 'JSN_POWERADMIN_TM_UNINSTALL_TEMPLATE')).addEventHandler('click', function () {
					if (openedSid == $(activeItem).attr('sid') || openedTid == $(activeItem).attr('tid')) {
						alert(JSNLang.translate( 'JSN_POWERADMIN_TM_CLOSE_BEFORE_UNINSTALL'));
						contextMenu.hide({});
						return;
					}
					var templateName = $('.template-item-thumb', activeItem).text().trim();
					var answer = confirm (JSNLang.translate( 'JSN_POWERADMIN_TM_UNINSTALL_TEMPLATE_CONFIRM', {'{JSN_TMP_NAME}' : templateName}));
					if (!answer) {
						return;
					}
	
					var templateId = $(activeItem).attr('sid');
					var actionLink = 'index.php?option=com_poweradmin&task=templates.uninstall&id=' + templateId + '&tmpl=component';
					
					$.getJSON(actionLink, function (response) {
						if(response.isUninstalled == true){
							$('div[tid=' + $(activeItem).attr('tid') + ']').remove();
						}					
					});
					
					contextMenu.hide({});
				});
			}
			// Disable browser context menu
			root.bind('contextmenu', function () {
				return false;
			});
		}
		
		$(document).ajaxStart(function () {
			$(activeItem).find('span').addClass('loading');
		});
		
		$(document).ajaxComplete(function () {
			$(activeItem).find('span').removeClass('loading');
		});
		
		// Handle mouse down event to display context menu
		$(document).click(function (e) {
			var parent = $(e.target);
			var depth  = 0;
			
			while(depth < 3) {
				if (parent.is('div') && (parent.hasClass('template-item') || parent.hasClass('jsnpw-submenu')))
					break;
				
				parent = parent.parent();
				depth++;
			}
			
			if (parent.hasClass('template-item') && e.which == 1) {
				var isDefault 	= parent.hasClass('default');
				var isOne		= root.size() == 1;
				var isLastStyle = $('div[tid=' + parent.attr('tid') + ']').size() == 1;
								
				_setMenuEnabled(contextMenu.getItem(JSNLang.translate( 'JSN_POWERADMIN_TM_MAKE_DEFAULT')), !isDefault, JSNLang.translate( 'JSN_POWERADMIN_TM_ALREADY_DEFAULT'));
				if(candelete){
					_setMenuEnabled(contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENUTITLE_DELETE')), (!isDefault && !isOne && !isLastStyle), JSNLang.translate( 'JSN_POWERADMIN_TM_CANNOT_DELETE_DEFAULT'));										
				}
				if(canuninstall){
					_setMenuEnabled(contextMenu.getItem(JSNLang.translate( 'JSN_POWERADMIN_TM_UNINSTALL_TEMPLATE')), (!isDefault && !isOne), JSNLang.translate( 'JSN_POWERADMIN_TM_CANNOT_UNINSTALL_DEFAULT'));
				}
				
				activeItem = parent;
				contextMenu.show({
					x : e.pageX+5, 
					y : e.pageY+10
				});
			}
			else if (!parent.hasClass('jsnpw-submenu')) {
				contextMenu.hide({});
			}
		});

		$('a.template-item-thumb').click(function (e) {
			e.preventDefault();
		});
		
		$('#manage-styles').accordion({
			header: "h3",
			icons: false,
			autoHeight: false
		});
		
		/**
		 * Function to change state of menu item to enable or disable
		 * @param menuItem
		 * @param state
		 * @param disableHint
		 */
		function _setMenuEnabled(menuItem, state, disableHint) {
			if (state == true) {
				menuItem
					.removeClass('disable')
					.enableEventHandler("click")
					.removeAttr('title');
			}
			else {
				menuItem
					.addClass('disable')
					.disableEventHandler('click')
					.attr('title', disableHint);
			}
		}
		
		/**
		 * Append duplicated template style to style list
		 * @param templateStyle
		 */
		function _appendNewStyle(templateStyle) {
			$(activeItem).after(' ');
			$('<div/>', { id: 'jTemplate-' + templateStyle.tid, 'class': 'template-item', tid: templateStyle.tid, sid: templateStyle.id })
				.append(
					$('<a/>', { 'class': 'template-item-thumb', 'href': 'javascript:void(0);' })
						.append($('<img/>', { 'class': 'template-thumbnail', 'align': 'middle', 'alt': templateStyle.title, 'src': templateStyle.thumbnail }))
						.append($('<span/>', { text: templateStyle.title }))
				)
				.insertAfter($(activeItem))
				.effect('highlight');
				
			$('.template-item').after(" ");
			
			// Disable browser context menu
			$('.template-item').bind('contextmenu', function () {
				return false;
			});
		}
	});
	
})(JoomlaShine.jQuery);
