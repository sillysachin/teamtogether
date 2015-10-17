/**
* 
* Javascript functions for menu manager
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html

Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
		- rawmode.jquery.js
		- visualmode.jquery.js
		- window.js
**/
(function($){
	/**
	* 
	* Extend functions for menu actions
	*
	*/
	$.extend({
		/**
		 * menuActions object 
		 */
		menuActions:{
			selectItem: function(){				
				var obj = $(this).parent();				
				obj.children('a').trigger("selected_node.jsntree");
				$._menuitems.selectedItem( obj.attr('id') );
			},
			/**
			*
			* Open popup page to edit menu item
			*
			* @param: (jQuery element) (obj) is jquery item
			* @return: None
			*/
			editItem: function () {
				var obj = $(this).parent();	
				var wWidth  = $(window).width()*0.85;
				var wHeight = $(window).height()*0.8;
				var itemid  = obj.attr('id').split('-')[2];
				var mid     = obj.attr('id').split('-')[1].replace('menutypeid', '');
				obj.children('a').showImgStatus({
					css : {
						'margin-top'  : '3px',
						'padding-left': '5px'
					}
				});

				var pop     = $.JSNUIWindow
				(
					baseUrl+'administrator/index.php?option=com_menus&tmpl=component&task=item.edit&id='+itemid, 
					{
						modal : true,
						width : wWidth,
						height: wHeight,
						title : JSNLang.translate( 'TITLE_EDIT_MENU_ITEM_PAGE' ),
						open  : function(){
							var iframe = $(this).find('iframe');
							var _this  = $(this);
							iframe.load(function(){
								var menu_types = iframe.contents().find('div#menu-types');
								iframe.contents().find('label#jform_type-lbl').next().attr('id', 'jsn-jform-type');

								var data = new Array(iframe.contents().find("[name^=jform]").length+10);
								iframe.contents().find("[name^=jform]").each(function(){
									data[$(this).attr('name').split('[')[1].replace(']', '')] = $(this).val();
								});

								$('.choose_type', menu_types).each(function(){
									var $onclick = $(this).attr('onClick').split(',')[1].replace("'", '').replace("')", "");
									var jform = data; 
									jform['type'] = $onclick;
									jform = $.base64Encode($.arrayToJSON(jform));		
									$(this).attr('onClick', "window.parent.JoomlaShine.jQuery.selectMenuItemType('"+jform+"', '"+iframe.attr('id')+"');");
								});
								var head = iframe.contents().find('head');
								//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
							});

							//bind trigger press enter submit form from child page
							$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
								iframe.load(function(){
									$._menuitems.resetMenu(mid);
								});
							});
						},	
				 		close: function(event, e){
							obj.children('a').showImgStatus("remove");
				 		},
				 		buttons:{				 			
				 			'Save': function(){
				 				var iframe = $(this).find('iframe');
								var _this  = $(this);
								if (!$.fn.validateEmptyFields(iframe)) {
									return false;
								}	

								if (pop.submitForm('item.apply', 'Save', function(){
									_this.removeClass('jsn-loading');
								})) {
								_this.addClass('jsn-loading');
								}
							},
							'Close': function(){
								var that = $(this);
								pop.getDialogBox().parent().fadeOut(300);
								$.post( baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.checkinmenuitem',{itemid:itemid}, function(){
									$._menuitems.resetMenu(mid);
									obj.addClass('checked-in');
									that.dialog("close");
								});
							}
						}
					}
				);
			},

			/**
			*
			* Publishing menu item, ajax request setting
			*
			* @param: (jquery element) (obj)
			* @return: 
			*     if (seccess)
			*            reset menu	
			*/
			publishing: function () {
				var obj = $(this).parent();
				var id = obj.attr('id').split('-'),
					self = this,
					isSelected = obj.find('a.jstree-clicked').size() > 0;

				obj.children('a').showImgStatus({
					css : {
						'margin-top'  : '3px',
						'padding-left': '5px'
					}
				});
				$.post( 
					baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.menuitempublishing', 
					{
						itemid : id[2], 
						publish: id[0]
					}
				).success(function(res){
					if (res == 'success'){
						$._menuitems.resetMenu(id[1].replace('menutypeid', ''));
					}else{
						$.checkResponse(res);
						obj.children('a').showImgStatus("remove");
					}

					if (id[0].toLowerCase() == 'unpublish' && isSelected) {
						var nextMenu = obj.next('[id^=Unpublish]');
						
						if (nextMenu.size() == 0) {
							nextMenu = $(obj.prevAll('[id^=Unpublish]').get(0));
						}

						if (nextMenu.size() > 0) {
							nextMenu.children('a').trigger("selected_node.jsntree");
							$._menuitems.selectedItem(nextMenu);
						}
					}

				}).error(function(msg){
					obj.children('a').showImgStatus("remove");
				});
			},

			/**
			*
			* Check in menu item, ajax request setting
			*
			* @param: (JoomlaShine.jQuery) (obj)
			* @return: None
			*/
			checkin: function () {
				var obj = $(this).parent();
				var id = obj.attr('id').split('-');
				obj.children('a').showImgStatus({
					css : {
						'margin-top'  : '3px',
						'padding-left': '5px'
					}
				});
				$.post( 
					baseUrl +'administrator/index.php?option=com_poweradmin&task=menuitem.checkinmenuitem', 
					{
						itemid: id[2]
					}
				).success(function(res){
					$.checkResponse(res);
					obj.children('a').showImgStatus("remove");
				}).error(function(msg){
					console.log(msg);
					obj.children('a').showImgStatus("remove");
				});
			},

			/**
			* 
			* Make item to default, ajax request setting
			*
			* @param: (jQuery element) (obj)
			* @return: 
			*     if (seccess)
			*            change HTML element to default
			*/
			makehome: function(){
				var obj = $(this).parent();
				var id = obj.attr('id').split('-');
				obj.children('a').showImgStatus({
					css : {
						'margin-top'  : '3px',
						'padding-left': '5px'
					}
				});
				$.post( 
					baseUrl + 'administrator/index.php?option=com_poweradmin&task=menuitem.setdefault', 
					{
						itemid: id[2]
					}
				).success(function(res){
					if (res == 'success'){
						var cur_default	= $('.default')
						var default_ind	= $('.menu-default-indicator', cur_default);
						cur_default.removeClass('default');		
						$('>a', obj).append(default_ind);
						$('.menu-default-indicator', cur_default).remove();
						obj.addClass('default');
					}else{
						$.checkResponse(res);
					}
					obj.children('a').showImgStatus("remove");
					menuTypes.addIndicate();
				}).error(function(msg){
					console.log(msg);
					obj.children('a').showImgStatus("remove");
				});
			},

			/**
			*
			* Trash menu item
			*
			* @param: (jQuery element) (obj)
			* @return: 
			*     if (seccess)
			*            remove HTML element
			*/
			trash: function () {
				var obj = $(this).parent();
				var nodeid = obj.attr('id');
				var id     = nodeid.split('-');
				
				var answer = confirm(JSNLang.translate( 'CONFIRM_DELETE_MENU_ITEM', {"JSN_TEXT1":$.trim(obj.children('a').text())} ))
				if (answer){
					obj.children('a').showImgStatus({
						css : {
							'margin-top'  : '3px',
							'padding-left': '5px'
						}
					});
					$.post( 
						baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.trashmenuitem', 
						{
							itemid:id[2]
						}
					).success(function(res){
						if (res == 'success'){
							obj.remove();
						}else{
							$.checkResponse(res);
						}
						obj.children('a').showImgStatus("remove");
					}).error(function(msg){
						console.log(msg);
						obj.children('a').showImgStatus("remove");
					});					
				}
				else{
					return;
				}				
			},
			
			/**
			 * Load custom assets for each menu item
			 */
			loadCustomAssets: function (){
				var obj = $(this).parent();
				var itemid  = obj.attr('id').split('-')[2];
				 
				var pop     = $.JSNUIWindow
				(
					baseUrl+'administrator/index.php?option=com_poweradmin&view=menuassets&tmpl=component&id='+itemid, 
					{
						modal : true,
						width : 700,
						height: 580,
						title : JSNLang.translate('JSN_POWERADMIN_MENUASSETS_LOAD_CUSTOM_ASSETS') + ': ' + obj.find('a:first').text(),
						scrollContent: 'auto',
				 		close: function(event, e){
							obj.children('a').showImgStatus("remove");
				 		},
				 		buttons:{				 			
				 			'Save': function(){
				 				var iframe = $(this).find('iframe');
								var _this  = $(this);

								
								if (pop.submitForm('menuitem.saveassets', 'Save', function(){
									_this.removeClass('jsn-loading');
									_this.dialog("close");
									}))
								{
									_this.addClass('jsn-loading');
								};
							},
							'Close': function(){															
								$(this).dialog("close");
								
							}
						}
					}
				);	
			},
			/**
			*
			* Rebuild menu item, ajax request
			*
			* @param: (jQuery element) (obj)
			* @return: None
			*/
			rebuild: function () {
				var obj = $(this).parent();
				var id = obj.attr('id').split('-');
				obj.children('a').showImgStatus({
					css : {
						'margin-top'  : '3px',
						'padding-left': '5px'
					}
				});
				$.post(
					baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.rebuilditem', 
					{
						itemid: id[2]
					}
				).success(function(res){
					$.checkResponse(res);
					obj.children('a').showImgStatus("remove");
				}).error(function(msg){
					console.log(msg);
					obj.children('a').showImgStatus("remove");
				});
			},
			/**
			* 
			* Expand current item and all child items. Expand is using jQueryUI animate widget
			*
			* @param: (jstree item extend jquery element ) (obj) is current item avaiable
			* @return: Change HTML Attribute and add triggers  
			*/
			expand_all: function(){
				var obj = $(this).parent();
				if (obj.hasClass('jstree-closed') && obj.children('ul').length > 0){
					$._menuitems.expand_all(obj, 0)

					obj.children("ul").animate(
					{
					    "height" : "toggle", 
						"opacity": "toggle"
					}, 500, function(){
						$._menuitems.expand_node(obj);
						$(this).show();
					});
				}else if(obj.find('.jstree-closed').length){
					$._menuitems.expand_all(obj, 500);
				}
				
				setTimeout(function(){
					$._menuitems.layoutResize();
					$._menuitems.saveList();
				}, 600);
			},
			
			/**
			* 
			* Collapse current item and all child items. Expand is using jQueryUI animate widget
			*
			* @param: (jstree item extend jquery element ) (obj) is current item avaiable
			* @return: Change HTML Attribute and add triggers  
			*/
			collapse_all: function(){
				var obj = $(this).parent();
				if (obj.hasClass('jstree-open') && obj.children('ul').length > 0){
					$._menuitems.collapse_all(obj, 0);

					obj.children("ul").animate(
					{
						"height" : "toggle", 
						"opacity": "toggle"
					}, 500, function(){
						$._menuitems.collapse_node(obj);
						$(this).hide();
					});
				}else if(obj.find('.jstree-open').length){
					$._menuitems.collapse_all(obj, 0);
					obj.find('ul').css('display', 'none');
				}
				setTimeout(function(){
					$._menuitems.layoutResize();
					$._menuitems.saveList();
				}, 600);
			},

			/**
			*
			* Add new item and set it to child current selected
			*
			* @param: (JoomlaShine.jQuery) (obj)
			* @return: 
			*     if (seccess)
			*            selectmenutype
			*/
			addmenuitem: function(){
				var obj = $(this).parent();
				var params   = obj.attr('id').split('-');
				var mid      = params[1].replace('menutypeid', '');
				var menutype = $('#jsn-menutype-title-'+mid).attr('menutype');
				var parentid = params[2];
				$._menuitems.selectmenutype(menutype, mid, parentid);
			}
		}
	});
	/**
	*
	* Instance jstree
	*
	*/
	$.fn.jstreeInstance = function(){
		var tree = $(this);		
		//Init jstree
		$(this).jstree({
			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","crrm", "hotkeys", "jsn_contextmenu", "html_data",  "ui", "dnd"],
			// each plugin you have included can have its own config object
			"themes" : { "theme":"jsnpa", "url" : baseUrl+'/administrator/components/com_poweradmin/assets/css/jstree/style.css' },
			"jsn_contextmenu" : {
				items:{
					"SelectItem": {
						"separator_before"	: false,
						"separator_after"	: true,
						"label"				: JSNLang.translate( 'TITLE_SUBMENU_SELECTITEM_MENU_ITEM' ),
						"_class"            : 'bold-item',
						"action"			: $.menuActions.selectItem
					},
				    "Edit" : {
						"separator_before"	: false,
						"separator_after"	: false,
						"label"				: JSNLang.translate( 'TITLE_SUBMENU_EDIT_MENU_ITEM' ),
						"action"			: $.menuActions.editItem
					},
					"Unpublish" : {
						"separator_before"	: false,
						"separator_after"	: false,
						"label"				: JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' ),
						"action"			: $.menuActions.publishing
					},
					"Rebuild" : {
						"separator_before"	: false,
						"icon"				: false,
						"separator_after"	: false,
						"label"				: JSNLang.translate( 'TITLE_SUBMENU_REBUILD_MENU_ITEM' ),
						"action"			: $.menuActions.rebuild
					},
					"ccp":{
						"separator_before"	: false,
						"icon"				: false,
						"separator_after"	: false,
						"label"				: JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_MORE' ),
						"_class"            : 'parent',
						"action"			: false,
						"submenu" : {
							"Makehome" : {
								"separator_before"	: false,
								"icon"				: false,
								"separator_after"	: false,
								"label"				: JSNLang.translate( 'TITLE_SUBMENU_MAKEHOME_MENU_ITEM' ),
								"action"			: $.menuActions.makehome
							},
							"Checkin" : {
								"separator_before"	: false,
								"icon"				: false,
								"separator_after"	: false,
								"label"				: JSNLang.translate( 'TITLE_SUBMENU_CHECKIN_MENU_ITEM' ),
								"action"			: $.menuActions.checkin
							},
							"Trash" : {
								"separator_before"	: false,
								"icon"				: false,
								"separator_after"	: false,
								"label"				: JSNLang.translate( 'TITLE_SUBMENU_TRASH_MENU_ITEM' ),
								"action"			: $.menuActions.trash
							},
							"LoadCustomAssets"		: {
								"separator_before"	: true,
								"icon"				: false,
								"separator_after"	: false,
								"label"				: JSNLang.translate('JSN_POWERADMIN_MENUASSETS_LOAD_CUSTOM_ASSETS'),		
								"action"			: $.menuActions.loadCustomAssets
							}
						}
					},
					"Expand_all" : {
						"separator_before"	: true,
						"icon"				: false,
						"separator_after"	: false,
						"label"             : JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_EXPAND_ALL' ),
						"action"            : $.menuActions.expand_all
					},
					"Collapse_all" : {
						"separator_before"	: false,
						"icon"				: false,
						"separator_after"	: false,
						"label"             : JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_COLLAPSE_ALL' ),
						"action"            : $.menuActions.collapse_all
					},
					"Addmenuitem" : {
						"separator_before"	: true,
						"icon"				: false,
						"separator_after"	: false,
						"label"				: JSNLang.translate( 'TITLE_SUBMENU_ADD_MENU_ITEM' ),
						"action"			: $.menuActions.addmenuitem
					}
				}	
			},
			"core" : { "initially_open" : [ "phtml_1" ]	},
			"dnd"  : { "drop_target":false }
		})	
		/**
		* Bind event move node in jstree
		*/
		.bind("move_node.jstree", function (e, data) {
			if ( !$.jStorage.get('rawmode_showunpublished_menuitem') ){
				$._menuitems.hideUnpublished();
			}
			data.rslt.o.each(function (i) {
				var move_id   = $(this).attr('id').split('-')[2];
				if ($(this).parent().attr('id') === undefined){
					var parent = $(this).parent().parent();
					if (parent[0].tagName.toLowerCase() == 'div'){
						var parent_id = 1;
					}else{
						var parent_id = parent.attr('id').split('-')[2];
					}
				}else{
					var parent_id = $(this).parent().attr('id').split('-')[1];
				}

				var orders    = new Array();
				var pos       = 0;
				$(this).parent().children('li').each(function(){
					orders[pos++] = $(this).attr('id').split('-')[2];
				});
				
				var move_item = $(this);
				//Show save 
				move_item.children('a').showImgStatus({
					css : {
						'margin-top'  : '3px',
						'padding-left': '5px'
					}
				});
				$.post
				(
					baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.moveItem',
					{
						itemid   : move_id, 
						parentid : parent_id, 
						orders   : orders
					}
				).success(function(res){
					$.checkResponse(res);
					$(this).unbind("click");
					move_item.children('a').showImgStatus("remove");
				}).error(function(msg){
					move_item.children('a').showImgStatus("remove");
				});
			});
		})
		/**
		* Tree is loaded
		*/
		.bind("loaded.jstree", function (event, data) {			
			setTimeout(function(){
				$._menuitems.layoutResize();				
			}, 1000);
			// you get two params - event & data - check the core docs for a detailed description
			$(document).unbind("nodeopen.jstree").bind("nodeopen.jstree", function(){
				setTimeout(function(){
					$._menuitems.layoutResize();
					$._menuitems.saveList();
				}, 500);

			}).unbind("nodeclosed.jstree").bind("nodeclosed.jstree", function(){
				setTimeout(function(){
					$._menuitems.layoutResize();
					$._menuitems.saveList();
				}, 500);
			});

			$(this).find('a').each(function(){
				if ( $(this).hasClass('default') ){
					$(this).removeClass('default').parent().addClass('default');
				}
				if($(this).hasClass('unpublish')){
					$(this).removeClass('unpublish').parent().addClass('unpublish');
				}
			})
			//remove node selected and add new node select
			.unbind("selected_node.jsntree").bind("selected_node.jsntree", function(){
				$('a.jstree-clicked').each(function(){
					$(this).removeClass('jstree-clicked');
				});
				$(this).addClass("jstree-clicked");
				menuTypes.addIndicate();
			});

			//Show unpublished items 
			if ( $.jStorage.get('rawmode_showunpublished_menuitem') ){
				$._menuitems.showUnpublished();
			}else{
				$._menuitems.hideUnpublished();
			}
			
			//Set selected page in history			
			if ( $.jStorage.get('selected_node') ){
				if ( $('#'+$.jStorage.get('selected_node')).length == 0 ){
					$('li[id*="'+$.jStorage.get('selected_node')+'"]').children('a').addClass('jstree-clicked');
				}else{
					$('#'+$.jStorage.get('selected_node')).children('a').addClass('jstree-clicked');
				}
			}
			
			$('#jsn-rawmode-menu-selector').show();
			//Add window triggerHandler check finshed load tree
			$(this).children('ul').addClass('jsntree-root');
			$(window).triggerHandler('finshed_load.jstree');
		})
		.bind('jstree.jsncontext.show', function (){
			$('#jsn-menutypes').hide();
		});	
	};	
	/**
	* 
	* DropDown List menu
	*
	* @param: (string) (_task)
	* @param: (string) (_selected)
	* @return: 
	*/
	var menuTypes = {
		/**
		 * 
		 * Get current menu selected
		 *
		 * @return: jQuery Element
		 */
		getSelected: function(){
			return $('#jsn-menu-elements-'+$('#jsn-menu-dropdown-list').find('li.selected').attr('id').split('-')[1]);
		},
		/**
		 * 
		 * Select menu
		 *
		 * @param: (number) (_selected) is menutypeid
		 * @return: jQuery Element
		 */
		selectMenu: function(_selected){
			$('.jsn-menu-details').hide();
			var dropDownList     = $('#jsn-menutypes');
			var menuTypeSelector = $('#jsn-rawmode-menu-selector');
			
			if ( _selected == undefined || $('#jsn-menu-elements-'+_selected).length == 0 ){
				_selected = $('li:first', dropDownList).attr('id').split('-')[1];
			}
			
			$('li', dropDownList).removeClass("selected");
			$('#dropdownmenutype-'+_selected).addClass('selected');
			
			$.jStorage.set('selected_menuid', _selected);
			
			menuTypeSelector.find('div.jsn-menutype-title').hide();
			menuTypeSelector.find('div#jsn-menutype-title-'+_selected).show().css('visibility','visible');			
			var activeMenu  = $('#jsn-menu-details-'+_selected);
			var currentMenu = menuTypes.getSelected();

			activeMenu.delay(300).slideDown(500, function(){
				$._menuitems.layoutResize();
			});

			//Show unpublished items 
			if ( $.jStorage.get('rawmode_showunpublished_menuitem') ){
				$._menuitems.showUnpublished();
			}else{
				$._menuitems.hideUnpublished();
			}
		},
		addIndicate: function(){
			/**
			 * Add ellipse indicate 
			 */
			$('div.jsn-menutype-title').each(function(){
				var menuid = $(this).attr('menuid');
				var ItemDropDownList = $('#dropdownmenutype-'+menuid); 
				var menu   = $('#jsn-menu-elements-'+menuid);
				
				if ( menu.find('li.default').length ){
					if ( ItemDropDownList.children('a').find('ins.has-default').length == 0 ){
						ItemDropDownList.children('a').append('<ins class="has-default"></ins>');
					}
				}else{
					ItemDropDownList.children('a').find('ins.has-default').remove();
				}
				
				if ( menu.find('a.jstree-clicked').length )
				{
					if ( ItemDropDownList.children('a').find('ins.has-selected').length == 0 ){
						ItemDropDownList.children('a').append('<ins class="has-selected"></ins>');
					}
				}else{
					ItemDropDownList.children('a').find('ins.has-selected').remove();
				}
				
			});
		},
		/**
		 * 
		 * Init dropdown select menu type
		 *
		 * @return: None
		 */
		initEvent: function(){
			var dropDownList     = $('#jsn-menu-dropdown-list');
			var menuTypeSelector = $('#jsn-rawmode-menu-selector');
			var menuTypesList        = $('#jsn-menutypes');


			if ( $.jStorage.get('selected_menuid') ){
				menuTypes.selectMenu( $.jStorage.get('selected_menuid') );
			}else{
				$('.jsn-menu-details').each(function(){
					if ( $(this).find('li[id*="'+$.jStorage.get('selected_node')+'"]').length ){
						$.jStorage.set('selected_menuid', $(this).attr('id').split('-')[3] );
					}
				});
				menuTypes.selectMenu($.jStorage.get('selected_menuid'));
			}
			dropDownList.find('li').unbind("click").click(function(){
				var selected = $(this).attr("id").split('-')[1];
				$.jStorage.set('selected_menuid', selected);
				menuTypes.selectMenu( selected );
			});

			$('#jsn-menutypes #add-new-menu a').click(function (e) {
				$._menuitems.addMenu(dropDownList.attr('next-id'));
			});
			//show submenu when right-click
			menuTypeSelector.unbind("mousedown").mousedown(function(e){
				var selected = $('.selected', dropDownList).attr('id').split('-')[1];
				var submenu  = $('#jsn-menutype-title-'+selected).jsnSubmenu({});
				var $this = $(this);
				if ( e.which === 3 && $this.mouseIsIn() ){						
					submenu.show({x : $.jsnmouse.getX() + 5, y : $.jsnmouse.getY() + 10	});
					$._menuitems.contentMenuTitleShowing = true;
					$(window).click(function(){
						if (!submenu.mouseIsIn() && !$this.mouseIsIn()){
							submenu.hide({});
							$._menuitems.contentMenuTitleShowing = false;
						}
					});
				}else{
					submenu.hide({});
					$._menuitems.contentMenuTitleShowing = false;
				}
			});
			
			menuTypeSelector.find('.dropdown-toggle').unbind("click").click(function(e){		
				$('.jsnpw-submenu').hide();
				e.stopPropagation();
				if (menuTypeSelector.hasClass('jsn-menu-selector-disabled')){
					menuTypesList.css({ 'visibility': 'hidden', 'display': 'block' });

					var overviewHeight    = $('.overview', menuTypesList).height(),
						viewport          = $('.viewport', menuTypesList),
						maxHeight         = $('#jsn-rawmode-menuitem-container').height() - 100,
						viewportHeight    = (overviewHeight > maxHeight) ? maxHeight : overviewHeight,
						scrollableElement = $('.jsn-scrollable');

					// Init scrollbar
					menuTypesList.css('height', 'auto');
					viewport.css('height', viewportHeight);

					scrollableElement.data('tsb') ? scrollableElement.tinyscrollbar_update() : scrollableElement.tinyscrollbar();

					var menuHeight = menuTypesList.height();

					// Show popup menu
					menuTypesList.css({ 'visibility': 'visible', 'display': 'block', 'height': 0, 'opacity': 0  });
					menuTypesList.stop();
					menuTypesList
						.css({ "height"  : menuHeight, "opacity" : 1 });
							menuTypeSelector.removeClass('jsn-menu-selector-disabled');
				}
				else{
					menuTypesList.stop();
					menuTypesList.css({ "height"  : 0, "opacity" : 0 });
						menuTypeSelector.addClass('jsn-menu-selector-disabled');
						menuTypesList.hide();
				}
			});

			$(window).click(function () {
				if (!menuTypeSelector.hasClass('jsn-menu-selector-disabled')) {
					menuTypesList.stop();
					menuTypesList.css({ "height"  : 0, "opacity" : 0 });
						menuTypeSelector.addClass('jsn-menu-selector-disabled');
						menuTypesList.hide();
				}
			});

			//Show/hide dropdown select menutypes
			$(window).unbind("jstree.jsn.drag.scroll").bind("jstree.jsn.drag.scroll", function(){
				var menu = menuTypes.getSelected();
				if ( menu.scrollTop() <= 10 ){
					menuTypeSelector.show();
				}else{
					menuTypeSelector.hide();
				}
			});
			//redraw dropdown menu when window resized
			$(window).resize(function() {
				var menu = menuTypes.getSelected();
				if ( menu.scrollTop() <= 10 ){
					menuTypeSelector.show();
				}else{
					menuTypeSelector.hide();
				}			  
			});
		}
	};
	/**
	* 
	* Extend functions for menu manager
	*
	*/
  $.extend({
	_menuitems:{
		mode:'visualmode',
		jstreeContextMenuShowing: false,
		/**
		* Init 
		*
		* @return: None
		*/	
		init: function(){
			//Drop down list
			menuTypes.initEvent();

			//Call function init submenu titles
			$._menuitems.headerContextMenu( $('.menutype-title') );

			//$._menuitems.submenuSwitch($('.jsn-menuitem-assignment'));
			
			//Restore list
			$._menuitems.restoreList();

			//call function init jstree
			$(".jsn-menuitem-assignment").each(function(){
				//currently when found old object then pop it
				$(this).jstreeInstance();
			});

			//Menu items unpublished
			$('#menu-manager').unbind("click").click(function(){
				if ( !$(this).hasClass('btn-enabled') ){
					$(this).removeClass('btn-disabled').addClass('btn-enabled');
					$(this).addClass('btn-success');					
					$.jStorage.set('rawmode_showunpublished_menuitem', true);
					$(this).attr('title', JSNLang.translate('TITLE_HIDE_UNPUBLISHED_MENUITEMS'));
					$._menuitems.showUnpublished();
				}else{
					$(this).removeClass('btn-enabled').addClass('btn-disabled');
					$(this).removeClass('btn-success');
					$.jStorage.set('rawmode_showunpublished_menuitem', false);
					$(this).attr('title', JSNLang.translate('TITLE_SHOW_UNPUBLISHED_MENUITEMS'));
					$._menuitems.hideUnpublished();
				}
				
				setTimeout(function(){
					$._menuitems.layoutResize();
				}, 500);
			});
			
			$(window).unbind('finshed_load.jstree').bind('finshed_load.jstree', function(){
				menuTypes.addIndicate();
			});
			
			if ( $.jStorage.get('rawmode_showunpublished_menuitem') ){
				$('#menu-manager').removeClass('btn-disabled').addClass('btn-enabled').attr('title', JSNLang.translate('TITLE_HIDE_UNPUBLISHED_MENUITEMS'));
				$('#menu-manager').addClass('btn-success');
			}else{
				$('#menu-manager').removeClass('btn-enabled').addClass('btn-disabled').attr('title', JSNLang.translate('TITLE_SHOW_UNPUBLISHED_MENUITEMS'));
				$('#menu-manager').removeClass('btn-success');
			}
			
			setTimeout(function(){
				$._menuitems.layoutResize();
			}, 600);
		},

		/**
		* 
		* Resize layout
		*
		* @return: None
		*/		
		layoutResize: function(){
			var menuArea			= $('#jsn-rawmode-menuitem-container');
			var menuSelector		= $('#jsn-rawmode-menu-selector');

			if ($._menuitems.mode == 'visualmode'){
				var leftWidth  = $('#jsn-vsleft-panel').width();
				var leftHeight = $('#jsn-vsleft-panel').height();
				$.jStorage.set("visual_layout_resize", leftWidth);
				$('#jsn-leftpanel-header').css('width', leftWidth);
			}else if($._menuitems.mode == 'rawmode'){
				var leftWidth  = $('#jsn-rawmode-leftcolumn').width();
				var leftHeight = $('#jsn-rawmode-leftcolumn').height();
				var headerPanelHeight	= $('#jsn-rawmode-leftcolumn').find('.jsn-heading-panel').height();
				var menuSelectorPanel	= $('#jsn-rawmode-leftcolumn').find('.jsn-menu-selector-container_inner');

				var currentMenu = menuTypes.getSelected();
				currentMenu.css( 'height', leftHeight - 37 );
				
				$('.jstree-jsnpa').css({
					'overflow-y' : 'hidden',
					'overflow-x' : 'hidden'
				});
				if ( currentMenu.children('ul').height() + 50 > currentMenu.height() ){
					menuSelector.find('.menutype-title').css('width', leftWidth - 90);
					menuSelector.css('width', leftWidth - 35);
					currentMenu.css({
						'overflow-y' : 'scroll',
						'overflow-x' : 'hidden'
					});
				}else{
					menuSelector.css('width', leftWidth - 20);
					menuSelector.find('.menutype-title').css('width', leftWidth - 75);
					currentMenu.css({
						'overflow-y' : 'hidden',
						'overflow-x' : 'hidden'
					});
				}
				
				$.jStorage.set("rawmode_west_layout_resize", leftWidth);
			}

			menuSelectorPanel.css('height', leftHeight - headerPanelHeight - 33);
		},
		
		headerContextMenu : function(objs, rebuild){
			try{
				rebuild = ( rebuild == undefined )? true : false;
				headerContextMenu = $('#site-manager-eader-context').subMenuReferences({rebuild:false, rightClick:false, attrs:{'class': 'jsnpw-submenu header-context-menu'}});
				var menuops = headerContextMenu.getMenu();
				if (menuops != null ){
					if ( menuops.isNew()){
						/**
						 *
						 * Edit menu type. Open popup page to setting for menu
						 *
						 */
						menuops.addItem( JSNLang.translate( 'TITLE_SUBMENUTITLE_EDIT' ) ).bind("click", function(){
							menuops.hide({});
							var menuid = menuops.getRootAttr('menuid');
							var pop    = $.JSNUIWindow
							(
								baseUrl+'administrator/index.php?option=com_menus&tmpl=component&task=menu.edit&id='+menuid, 
								{
									modal : true,
									width : 500,
									height: 240,
									scrollContent: false,
							 		title : JSNLang.translate( 'TITLE_PAGE_MENU_SETTINGS' ),
							 		open  : function(){
							 			var _this  = $(this);
										var iframe = _this.find('iframe');
										iframe.load(function(){
											var head = iframe.contents().find('head');
											//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
											var el = iframe.contents().find('form#item-form');
												el.find('legend').css('display', 'none');
												el.find('fieldset[class="adminform"]').css({
													'border' : 'none',
													'margin' : '15px 10px',
													'padding': '0px',
													'width'  : '460px'
												});
										});
										//bind trigger press enter submit form from child page
									    $(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
											iframe.load(function(){
												var newtitle = iframe.contents().find('input[name="jform[title]"]').val();
												$('#jsn-menutype-title-'+menuid+' btn.menutype-title').html(newtitle).attr('title', newtitle);												
												$('#dropdownmenutype-'+menuid).children().html(newtitle);
												menuTypes.initEvent();
												$._menuitems.resetMenu(menuid);
											});
										});
								},
								buttons: {									
									'Save': function(){										
										var _this  = $(this);
										var iframe = $(this).find('iframe');
										if (!$.fn.validateEmptyFields(iframe)) {
											return false;
										}
									
										if (pop.submitForm('menu.apply', 'Save', function (){
											_this.removeClass('jsn-loading');
											var newtitle = iframe.contents().find('input[name="jform[title]"]').val();
												if (iframe.contents().find('#system-message').size() > 0) {
													_this.css('height', 170);
													iframe.css('height', 170);
												}
											
											if (iframe.contents().find('input.invalid').size() > 0) {
												_this.css('width', 600);
												iframe.css('width', 600);
											}
												$('#jsn-menutype-title-'+menuid+' btn.menutype-title').html(newtitle).attr('title', newtitle);
												$('#dropdownmenutype-'+menuid).children().html(newtitle);
												menuTypes.initEvent();
												$._menuitems.resetMenu(menuid);
											
										})){
											_this.addClass('jsn-loading');	
										}
									},
									'Close': function(){
										$(this).dialog("close");
									}
								}
							});
						});
						
						/**
						*
						* Rebuild menu type, ajax request
						*
						* @return: 
						*  if (success)
						*      resetmenu
						*/
						menuops.addItem('Rebuild').bind("click", function(){
							menuops.hide({});
							var menuid = menuops.getRootAttr('menuid');
							$('#jsn-menutype-title-'+menuid).showImgStatus({
								css : {
									'position' : 'absolute',
									'top'	   : '10px',
									'right'    : '40px'
								}
							});
							$.post(baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.rebuild', 
							{
								mid	:	menuid
							}).success(function(res){
								$.checkResponse(res);
								$._menuitems.resetMenu(menuid);
							}).error(function(msg){
								console.log(msg);
							});
						});
						
						var subPanelMore = menuops.addParentItem( JSNLang.translate('TITLE_SUBPANEL_MORE') );
							/**
							*
							* Delete menu type. Open popup confirmation page to delete/cancel delete menu
							*
							*/
							subPanelMore.addItem( JSNLang.translate( 'TITLE_SUBMENUTITLE_DELETE' ) ).bind("click", function(){
								menuops.hide({});
								var menuid    = menuops.getRootAttr('menuid');
								var menutitle = menuops.getRootAttr('menutitle');
								var answer = confirm(JSNLang.translate( 'CONFIRM_DELETE_MENU', {'JSN_TEXT1': menutitle}));
								if (answer) {
									$.post(baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.deleteMenu', 
									{
										menuid:menuid
									}).success(function(res){
										if ( res.indexOf('success') != -1 ){
											$('#jsn-menu-details-'+menuid).remove();
											$('#dropdownmenutype-'+menuid, $('#jsn-menu-dropdown-list')).remove();
											menuTypes.selectMenu();
										}else{
											$.checkResponse(res);
										}
									}).error(function(msg){
										console.log(msg);
									});
								}else{
									return;									
								}
							});
							
							/**
							*
							* Permissions settings. Open popup permission settings page
							*
							*/
							subPanelMore.addItem( JSNLang.translate( 'TITLE_SUBMENUTITLE_OPTIONS' ) ).bind("click", function(){
								menuops.hide({});
								var pop = $.JSNUIWindow
								(
									baseUrl+'administrator/index.php?option=com_config&view=component&component=com_menus&path=&tmpl=component', 
									{
										modal : true,
										width : 756,
										height: 560,
										title : JSNLang.translate( 'TITLE_SUBMENUTITLE_PAGE_OPTIONS' ),
										open  : function(){
											var iframe = $(this).find('iframe');
											iframe.load(function(){
												var head = iframe.contents().find('head');
												//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
												var el = iframe.contents().find('form#component-form');
												el.children('fieldset').hide();
												// Hide Joomla Sidebar
												iframe.contents().find('#sidebar').hide();
											});
										},
										buttons:{											
											'Save & Close': function(){
												var iframe	= $(this).find("iframe");
												if (!$.fn.validateEmptyFields(iframe)) {
													return false;
												}
												pop.submitForm('component.apply', 'Save & Close');
											},
											'Cancel': function(){
												$(this).dialog("close");
											}
										}
									}
								);
							});
	
						if ($.browser.msie && $.browser.version < 8){
							menuops.addDivider().css('margin-top', '-10px');
						}else{
							menuops.addDivider();
						}
						/**
						*
						* Add new menu item. Open popup selectmenutype page
						*
						*/
						menuops.addItem('Add menu item').bind("click", function(){
							var menutype = menuops.getRootAttr('menutype');
							var menuid   = menuops.getRootAttr('menuid');
							$._menuitems.selectmenutype(menutype, menuid, 0);
						});
					}
					/**
					* Bind event mousedown
					*/
					objs.unbind("mousedown").mousedown(function(e){
						try{
							if (  e.which === 1 && !e.ctrlKey){
								headerContextMenu.setReference($(this));
								menuops = headerContextMenu.getMenu();
								menuops.show
								(
									{
										x : $.jsnmouse.getX()+5, 
										y : $.jsnmouse.getY()+10
									}
								);
								
								$('body').unbind("click").bind("click", function(e){
									if ( !$(e.target).parents('div.menutype-title').length && !$(e.target).hasClass('menutype-title') ){
										menuops.hide({});
									}
								});
							}
						}catch(e){
							throw e.message;
						}finally{
							return;
						}
					});
			    }
			}catch(e){
				console.log(e.message);
			}
		},
		submenuSwitch : function(objs){
			try{
				var switchMenuReference = $('#site-manager-switch-context').subMenuReferences({rebuild:false, rightClick:false, attrs:{'class': 'jsnpw-submenu switch-menu'}});
				var switchMenu  = switchMenuReference.getMenu();
				if (switchMenu.isNew()){
					/**
					 * Add item expand all and call expand_all function when click
					 */
					switchMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_EXPAND_ALL' ) ).click(function(){
						switchMenu.hide({});
						$._menuitems.expand_all(menuTypes.getSelected(), 600);
						setTimeout(function(){
							$._menuitems.layoutResize();
							$._menuitems.saveList();
						}, 600);
					});
					/**
					* Add item collapse all and call collapse_all function when click
					*/
					switchMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_COLLAPSE_ALL' ) ).click(function(){
						switchMenu.hide({});
						$._menuitems.collapse_all(menuTypes.getSelected(), 600);
						setTimeout(function(){
							$._menuitems.layoutResize();
							$._menuitems.saveList();
						}, 600);
					});
	
					if ($.browser.msie && $.browser.version < 8){
						switchMenu.addDivider().addClass('jsn-submenu-divider-ie7');
					}else{
						switchMenu.addDivider();
					}
					/**
					 * Add item add menu item and show add item page when click
					 */
					var addItem = switchMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_ADD_MENU_ITEM' ) ).click(function(){
						var currMenu = menuTypes.getSelected();
						var menuid   = currMenu.attr('id').replace('jsn-menu-elements-', '');
						var menutype = $('#jsn-menutype-title-'+menuid).attr('menutype');
						$._menuitems.selectmenutype(menutype, menuid, 0);
						switchMenu.hide({});
					});
	
					if ($.browser.msie && $.browser.version < 8){
						addItem.css
						(
							{
								'position': 'relative', 
								'top'     : '-13px'
							}
						);
						switchMenu.cssHooks('height', '96px');
					}
				}
				
				/**
				* Bind event mousedown
				*/
				objs.each(function(){
					$(this).unbind("click").click(function(e){
						try{
							if ( !$._menuitems.jstreeContextMenuShowing && ( $(e.target).parents('ul.jsntree-root').length || $(e.target).hasClass('jsntree-root')) ){
								switchMenuReference.setReference($(this).children('ul'));
								switchMenu   = switchMenuReference.getMenu();
								var currMenu = menuTypes.getSelected();
								var currMenu = menuTypes.getSelected().children('ul');
								var showAllItem = false;
								currMenu.children('li').each(function(){
									if ( $(this).hasClass('jstree-open') || $(this).hasClass('jstree-closed')){
										if ($(this).css('display') != 'none'){
											showAllItem = true;
										}
									}
								});
								if (!showAllItem){
									switchMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_EXPAND_ALL' ) );
									switchMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_COLLAPSE_ALL' ) );
									switchMenu.hideDividers();
								}else{
									switchMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_EXPAND_ALL' ) );
									switchMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_SWITCH_COLLAPSE_ALL' ) );
									switchMenu.showDividers();
								}
								
								switchMenu.show
								(
									{
										x : $.jsnmouse.getX()+5, 
										y : $.jsnmouse.getY()+10
									}
								);
								
								var $this = $(this);
								$('body').unbind("click").bind("click", function(e){
									if ( !$(e.target).parents('div.jsn-menu-details').length || e.target.tagName == 'A' || e.target.tagName == 'INS' ){
										switchMenu.hide({});
									}
								});
							}else{
								switchMenu.hide({});
								$._menuitems.jstreeContextMenuShowing = false;
							}
							
						}catch(e){
							throw e.message;
						}finally{
							return;
						}
					});
				});
			}catch(e){
				throw e.message;
			}finally{
				return;
			}
		},
		/**
		*
		* Get next menu id
		*
		* @param: (string) (nextId) is joomla ID AUTO_INCREMENT
		* @param: (string) (title) is title of menu type 
		*/
		getNewAdd: function(newId, menutype, title){
			$.post(baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.getMenu', 
			{
				mid : newId
			}).success(function(res){
				$.checkResponse(res);
				if (res != 'error'){
					var nextId = parseInt(newId)+1,
						menuList = $('#jsn-menutypes ul');
					$('#jsn-menu-dropdown-list').attr('next-id', nextId);
					$('#jsn-rawmode-menuitem-container').append(res);
					menuList.append($('<li id="dropdownmenutype-'+newId+'"><a class="text">'+title+'</a></li>'));
					var newTitle = $('<div />', {
						'id'       : 'jsn-menutype-title-'+newId,
						'class'    : 'jsn-menutype-title',
						'menutitle': title,
						'menuid'   : newId,
						'menutype' : menutype
					});
					var _itemWrapper	= $('<div />', {
						'class': 'btn-group'
					});
					var _btn	= $('<btn />', {
						'id'       : 'jsn-submenu-'+newId,
						'class'    : 'btn menutype-title',
						'menutitle': title,
						'menuid'   : newId,
						'menutype' : menutype
					}).html(title);
					
					var _caret	= $('<button />', {						
						'class'    : 'btn dropdown-toggle',
						'data-toggle': 'dropdown',
						'type': 'button'	
					}).html('<span class="caret"></span>');
					_btn.appendTo(_itemWrapper);
					_caret.appendTo(_itemWrapper);
					_itemWrapper.appendTo(newTitle);
					$('#jsn-rawmode-menu-selector').find('div.jsn-menutype-title:first').before(newTitle);
					
					$._menuitems.headerContextMenu($('#jsn-menutype-title-'+newId+ ' .menutype-title'), false);
					//drop down list
					menuTypes.initEvent();
					setTimeout(function(){
						menuTypes.selectMenu(newId);
					}, 500);
					
				}
			}).error(function(msg){
				console.log(msg);
			});
		},

		/**
		*
		* Reset menu
		*
		* @param: (string) (mid) is joomla menutype id
		* @return: 
		*   if (success)
		*       reset menu
		*/	
		resetMenu: function(mid, _resetMenuCallbackFunc){
			var oldStatus = new Array();
			var i = 0;

			$('li', $('#jsn-menu-elements-'+mid)).each(function(){
				if (!$(this).hasClass('jstree-leaf')){
					oldStatus[i++] = {_nodeid: '#'+$(this).attr('id'),	_class: $(this).attr('class'), _style:$(this).children('ul').attr('style') };
				}
			});
			var currentMenu = menuTypes.getSelected(mid).html('');
			$.post(baseUrl+'administrator/index.php?option=com_poweradmin&task=menuitem.getMenuType', 
			{
				mid:mid
			}).success(function(res){
				$('#jsn-menutype-title-'+mid).showImgStatus("remove");
				$.checkResponse(res);
				if (res != 'error'){
					try{
						currentMenu.html(res);
						//Call plugin submenu for header title
						$._menuitems.headerContextMenu($('#jsn-menutype-title-'+mid+ ' .menutype-title'), false);
						//Call plugin init jstree for this menu items 
						currentMenu.jstreeInstance();
						$(window).unbind("finshed_load.jstree").bind("finshed_load.jstree", function(){
							for(i = 0; i < oldStatus.length; i++){
								if ($(oldStatus[i]['_nodeid']).length > 0){
									if (oldStatus[i]['_class'].indexOf('jstree-open') != -1){
										$(oldStatus[i]['_nodeid']).attr('class', oldStatus[i]['_class']);
										$(oldStatus[i]['_nodeid']).children('ul').attr('style', oldStatus[i]['_style']);
									}
								}
							}
							//Show unpublished items 
							if ( $.jStorage.get('rawmode_showunpublished_menuitem') ){
								$._menuitems.showUnpublished();
							}else{
								$._menuitems.hideUnpublished();
							}

							$._menuitems.layoutResize();
							$('#'+$.jStorage.get('selected_node')).children('a').addClass('jstree-clicked');
							
							//Callback function
							if ($.isFunction(_resetMenuCallbackFunc)){
								_resetMenuCallbackFunc();
							}
						});

					}catch(e){
						throw e.message;
					}
				}
			}).error(function(msg){
				console.log(msg);
			});
		},
		/**
		* 
		* Show unpublished menu items
		*
		* @return: None
		*/
		showUnpublished: function(){
			var parents = new Array();
			var activeMenu = menuTypes.getSelected();
			activeMenu.find('li.unpublish').each(function(){
				if (/parentid/.test($(this).parent().attr('id'))) {
					parents[$(this).parent().attr('id')] = $(this).parent();
				}
				$(this).css('display', 'block');
			});
			for( k in parents ){
				if ( parents[k].length > 0 && typeof parents[k].addClass == 'function'){
					if (!parents[k].parent().hasClass('jstree-closed')){
						parents[k].show().parent().addClass('jstree-open');
					}
				}
			}
			
			$._menuitems.markup_tree();
		},
		/**
		* 
		* Hide unpublished menu items
		*
		* @return: None
		*/
		hideUnpublished: function(){
			var parents = new Array();
			var activeMenu = menuTypes.getSelected();
			activeMenu.find('li.unpublish').each(function(){
				if (/parentid/.test($(this).parent().attr('id'))) {
					parents[$(this).parent().attr('id')] = $(this).parent();
				}
				$(this).css('display', 'none');
			});
			for( k in parents ){
				if ( parents[k].length > 0 && typeof parents[k].addClass == 'function' ){
					var parent     = parents[k];
					var hideParent = true;
					parent.children('li').each(function(){
						if ($(this).css('display') != 'none'){
							hideParent = false;
						}
					});
					if (hideParent){
						parent.parent().removeClass('jstree-open').removeClass('jstree-closed').addClass('jstree-leaf');
					}
				}
			}
			$._menuitems.markup_tree();
		},
		/**
		 * Remove last tree background repeat
		 */
		markup_tree : function(){
			var activeMenu = menuTypes.getSelected();
			activeMenu.find('li.mark-last').removeClass('mark-last').removeClass('jstree-last');
			var lastItem = $({});
			activeMenu.children('ul').children().each(function(){
				if ( $(this).css('display') != 'none' ){
					lastItem = $(this);
				}
			});
			if ( lastItem.children('ul').length > 0 ){
				lastItem.addClass('mark-last').addClass('jstree-last');
			}
		},
		/** 
		* 
		* Expand all tree-closed. Expand callback
		*
		* @param: (jQuery element) (node) is HTML 
		* @return: None
		*/
		expand_node: function(node){
			node.removeClass("jstree-closed").addClass("jstree-open").children("a").removeClass("jstree-loading");
		},
		expand_all: function(root, duration){
			$('li.jstree-closed', root).each(function(){
				var obj = $(this).parent();
				if (obj.children('ul').length > 0 && $(this).hasClass('jstree-closed')){
					$._menuitems.expand_all(obj.children("ul"), 0);
					obj.children("ul").animate(
					{
						"height" : "toggle", 
							"opacity": "toggle"
					}, duration, function(){
						$._menuitems.expand_node(obj);
						$(this).show();
					});
				}
			});
		},

		/**
		* 
		* Collapse all tree-opend. Collapse callback
		*
		* @param: (jQuery element) (node) is HTML
		* @return: None
		*/
		collapse_node: function(node){
			node.removeClass("jstree-open").addClass("jstree-closed");
		},
		collapse_all: function(root, duration){
			$('.jstree-open', root).each(function(){
				var obj   = $(this);
				if (obj.find('ul').length > 0 && $(this).hasClass('jstree-open')){
					$._menuitems.collapse_all(obj.children("ul"), 0);
					obj.children("ul").animate(
					{
						"height" : "toggle", 
						"opacity": "toggle"
					}, duration, function(){
						$._menuitems.collapse_node(obj);
						$(this).hide();
					});
				}
			});
		},

		/**
		*
		* Add new menu
		* 
		* @param: (string) (nextId) is joomla ID AUTO_INCREMENT
		* @return: setting page
		*/
		addMenu: function(nextId){
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_menus&tmpl=component&task=menu.add', 
				{
					modal : true,
					width : 550,
					height: 250,
					title : JSNLang.translate( 'TITLE_PAGE_MENUITEM_ADD_MENU' ),
					scrollContent: false,
					open  : function(){
						var _this  = $(this);
						var iframe = _this.find('iframe');
						iframe.load(function(){
							var head = iframe.contents().find('head');
							//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
							var el = iframe.contents().find('form#item-form');
							el.find('legend').css('display', 'none');
							el.find('fieldset[class="adminform"]').css({
											'border' : 'none',
											'margin' : '15px 10px',
											'padding': '0px',
											'width'  : '400px'
										});
						});

						//bind trigger press enter submit form from child page
						$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
							var newtitle = iframe.contents().find('input[name="jform[title]"]').val();
							var menutype = iframe.contents().find('input[name="jform[menutype]"]').val();
							iframe.load(function(){
								$._menuitems.getNewAdd(nextId, menutype, newtitle);
							});
						});
					},
					buttons: {						
						'Save & Close': function(){
							var _this  = $(this);
							var iframe = $(this).find('iframe');
							
							if (!$.fn.validateEmptyFields(iframe)) {
								return false;
							}
							
							if (pop.submitForm('menu.save', 'Save & Close')){		
								var newtitle = iframe.contents().find('input[name="jform[title]"]').val();
								var menutype = iframe.contents().find('input[name="jform[menutype]"]').val();
								iframe.load(function(){
									$._menuitems.getNewAdd(nextId, menutype, newtitle);
								});
							}
						},
						'Cancel': function(){
							$(this).dialog("close");
						}
					}
				}
			);
		},
		/**
		*		
		* Select menutype and redirect to create new menu item
		* 
		* @param: (string) (menutype) is joomla menutype
		* @param: (string) (menutypeid) is joomla menutype id
		* @param: (string) (parentid) is joomla parent menu item id need add to children
		*/
		selectmenutype: function(menutype, menutypeid, parentid){
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&view=selectmenutypes&menutypeid='+menutypeid+'&parentid='+parentid+'&tmpl=component&pwadvisual=1', 
				{
					modal : true,
					width : 750, 
					height: 550,
					buttons: {
						'Close': function(){
							$(this).dialog("close");
						}
					},
					title : JSNLang.translate( 'TITLE_PAGE_MENUITEM_SELECT_MENU_TYPE' ),
					search: {
						text    : JSNLang.translate( 'TITLE_PAGE_MENUITEM_SELECT_MENU_TYPE_TEXT_SEARCH' ),
						classSet: 'ui-window-searchbar',
						onChange: function(){
							var iframe = pop.getIframe();
							iframe[0].contentWindow.selectMenuType.filterResults($(this).val().trim());
						},
						onKeyup : function(){
							//fire change event
							$(this).change();
						},
						onBlur  : function(){
							if ($(this).val().trim() == ''){
								$(this).val( JSNLang.translate( 'TITLE_PAGE_MENUITEM_SELECT_MENU_TYPE_TEXT_SEARCH' ) );
								$(this).css('color', '#CCCCCC');
							}
						},
						onFocus : function(){
							if ($(this).val().trim() == JSNLang.translate( 'TITLE_PAGE_MENUITEM_SELECT_MENU_TYPE_TEXT_SEARCH' )){
								$(this).css('color', '#000').val('');
							}
						},
						closeTextKeyword : true,
						afterAddTextCloseSearch : function(obj){
						},
						defaultText      : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ),
						closeTextClick   : function( obj, searchbox ){
							obj.hide();
							var iframe = pop.getIframe();
							iframe[0].contentWindow.selectMenuType.filterResults('');
							searchbox.css('color', '#CCCCCC').val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ) );
						}
					}
				} 
			);
		},

		/**
		*
		* Add new menu item
		*
		* @param: (string basecode64) (params)
		* @param: (string) (menutype) is joomla menutype
		* @param: (string) (menutypeid) is joomla menutype id
		* @param: (string) (parentid) is joomla parent id need add to children
		* @return: open page add
		*/
		addMenuItem: function(params, menutype, menutypeid, parentid){
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8; 
			var pop     = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=selectmenutypes.setType&tmpl=component&params='+params+'&menutype='+menutype, 
				{
					modal : true, 
					width : wWidth, 
					height: wHeight, 
					title : JSNLang.translate( 'TITLE_PAGE_MENUITEM_ADD_MENU_ITEM' ),
					open  : function(){
						var iframe = $(this).find('iframe');
						var _this  = $(this);
						
						//bind trigger press enter submit form from child page
						$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
							iframe.load(function(){
								$._menuitems.resetMenu(menutypeid);
							});
						});
					},
					buttons:{						
						'Save & Close': function(){
							if (pop.submitForm('item.apply', 'Save & Close')){
								var _this  = $(this);
								var iframe = $(this).find('iframe');
								iframe.load(function(){
									$._menuitems.resetMenu(menutypeid);
								});
							}
						},
						'Cancel': function(){
							$(this).dialog("close");
						}
					}
				}
			);
		},

		/**
		*
		* Select an menu item. Route mode 
		* 
		* @param: (string) (itemid)
		* @return: reload page
		*/
		selectedItem: function(itemid, backgroundLoad){			
			var link = (typeof(itemid) == 'string') ? 
							$('#'+itemid).children('a').addClass('jstree-clicked').attr('link'):
							itemid.children('a').addClass('jstree-clicked').attr('link');							
			if (link != undefined && link != '#'){
				$.jStorage.set('selected_node', (typeof(itemid) == 'string') ? itemid : itemid.attr('id'));
				if ($.jStorage.get('add_new_module') || $.jStorage.get('inactive_position')){
					if (link.indexOf('?') != -1){
						link += '&tp=1';
					}else{
						link += '?tp=1';
					}
				}
				link = $.base64Encode(link);
				if ($._menuitems.mode == 'visualmode'){
					if (sef == 1){
						$.post(
							baseUrl+'index.php?option=com_poweradmin&task=getRouterLink',
							{
								link: link
							}
						).success(function(res){
							if (res != 'error'){
								$._visualmode.setIframeSRC( baseUrl +'administrator/index.php?option=com_poweradmin&view=jsnrender&format=raw&render_url='+res);
							}
						}).error(function(msg){
							console.log(msg);
						});
					}else{
						$._visualmode.setIframeSRC( baseUrl +'administrator/index.php?option=com_poweradmin&view=jsnrender&format=raw&render_url='+link);
					}
				}else{
					JSNGrid.loadPage( link, backgroundLoad );
				}
			}else if(link == '#'){
				alert(JSNLang.translate( 'CONFIRM_SELECT_EMPTY_MENU_ITEM' ));				
			}
		},
		/**
		*
		* Save active tree status
		*
		* @return: baseencode64 save to cookie
		*/
		saveList: function(){
			var oldStatus = new Array();
			var i = 0;
			var menuActive = menuTypes.getSelected();

			$('li', menuActive).each(function(){
				if (!$(this).hasClass('jstree-leaf')){
					oldStatus[i++] = {_nodeid: '#'+$(this).attr('id'),	_class: $(this).attr('class'), _style:$(this).children('ul').attr('style') };
				}
			});
			
			var store = $.base64Encode( $.arrayToJSON(oldStatus) );
			$.jStorage.set($._menuitems.mode+'_menuitem_items_status', store);
		},
		
		/**
		*
		* Restore tree list status
		*
		* @return: restore menu items status
		*/
		restoreList: function(){
			var storeList = $.jStorage.get($._menuitems.mode+'_menuitem_items_status');
			if (storeList){
				var oldStatus = $.parseJSON($.base64Decode(storeList));
				for(k in oldStatus){
					if ($(oldStatus[k]['_nodeid']).length > 0){
						if (oldStatus[k]['_class'].indexOf('jstree-open') != -1 && $(oldStatus[k]['_nodeid']).children('ul').length){
							$(oldStatus[k]['_nodeid']).attr('class', oldStatus[k]['_class']);
							$(oldStatus[k]['_nodeid']).children('ul').attr('style', oldStatus[k]['_style']).css('opacity', '1').css('height', 'auto');
						}
					}
				}
				$._menuitems.layoutResize();
			}
			//Scan to fix menu items
			$('.jsn-menuitem-assignment').find('li').each(function(){
				if ($(this).children("ul").length){
					if ($(this).children("ul").css('display') == 'block'){
						$(this).removeClass("jstree-closed").addClass("jstree-open").children("a").removeClass("jstree-loading");
					}else if($(this).children("ul").css('display') == 'none'){
						$(this).removeClass("jstree-open").addClass("jstree-closed");
					}
				}
			});
		}
	}
});
})(JoomlaShine.jQuery);