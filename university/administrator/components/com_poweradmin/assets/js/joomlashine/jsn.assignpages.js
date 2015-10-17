/**
* 
* Functions for assignment pages
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html

Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
		- jstree.js
**/
(function($){
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
			var currentId   = $('#jsn-menu-dropdown-list').find('li.selected').attr('id').split('-')[1];
			var currentType = $('#jsn-menutype-title-'+currentId).attr('menutype');
			var currentMenu = $('#'+currentType);			
			return currentMenu;
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
			var menuTypesList        = $('#jsn-menutypes');
			
			if (_selected == undefined || $('#jsn-menutype-title-'+_selected).length == 0){
				_selected = $('.jsn-menutype-title:first').attr('menuid');
			}else if ( dropDownList.css('display') != 'none' ){
				menuTypeSelector.addClass('jsn-menu-selector-disabled');				
			}
			
			
			$('li', dropDownList).removeClass("selected");
			$('#dropdownmenutype-'+_selected).addClass('selected');
			
			$('div.jsn-menuitem-assignment').hide();
			
			menuTypeSelector.find('div.jsn-menutype-title').hide();
			menuTypeSelector.find('div#jsn-menutype-title-'+_selected).show().css('visibility', 'visible');
			
			var activeMenu  = $('#jsn-menu-details-'+_selected);
			var currentMenu = menuTypes.getSelected();

			menuTypesList.stop();
			menuTypesList.css({ "height"  : 0, "opacity" : 0 });
				menuTypeSelector.addClass('jsn-menu-selector-disabled');
				menuTypesList.hide();
			
			var currentMenu = menuTypes.getSelected();
			currentMenu.slideDown(500, function(){
				$(this).show();
			});
			
			$('ul > li', currentMenu ).each(function(){
				var obj = $(this);
				if ( obj.find('input[type="checkbox"]:checked').length || obj.find('li.default').length ){
					if ( obj.find('ul').length > 0 && !obj.hasClass('jstree-open') ){
						obj.children("ul").animate(
						{
							"height"  : "toggle", 
							"opacity" : "toggle"
						}, 500, function(){
							tree.expand_node(obj);
							//obj.show();
						});
					}
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
			//menuTypeSelector.width($('#jsn-assign-module').width() - 50);
			var moduleTypeWidth	= 0;
			
			if (dropDownList.parents('.tab-content').length > 0) {
				
				moduleTypeWidth	= dropDownList.parents('.tab-content').width() - 50;
			}else{
				moduleTypeWidth	= $('#jsn-assign-module').width() - 50;
			}
			
			menuTypeSelector.find('.menutype-title').width(moduleTypeWidth).css('float', 'left');
			dropDownList.find('li').unbind("click").click(function(){
				var selected = $(this).attr("id").split('-')[1];
				menuTypes.selectMenu( selected );
			});

			//show submenu when right-click
			menuTypeSelector.unbind("mousedown").mousedown(function(e){
				var selected = $('.selected', dropDownList).attr('id').split('-')[1];
				var submenu  = $('#jsn-menutype-title-'+selected).jsnSubmenu({});
				var $this = $(this);
				if ( e.which === 3 && $this.mouseIsIn() ){
					submenu.show({x : $.jsnmouse.getX() + 5, y : $.jsnmouse.getY() + 10	});					
					$(window).click(function(){
						if (!submenu.mouseIsIn() && !$this.mouseIsIn()){
							submenu.hide({});							
						}
					});
				}else{
					submenu.hide({});					
				}
			});
			
			menuTypeSelector.find('.dropdown-toggle').unbind("click").click(function(e){				
				e.stopPropagation();
				if (menuTypeSelector.hasClass('jsn-menu-selector-disabled')){
					var containerWidth = menuTypeSelector.width();
										
					menuTypesList.
						css({ 'visibility': 'hidden', 
								'display': 'block', 
								'position':'absolute',
								'width':containerWidth+'px',
								'border':'1px solid #CCCCCC',
								'margin-left':'-1px'});

					var overviewHeight    = $('.overview', menuTypesList).height(),
						viewport          = $('.viewport', menuTypesList),
						maxHeight         = $('.jsn-menu-selector-container_inner').height() - 100,
						viewportHeight    = (overviewHeight > maxHeight) ? maxHeight : overviewHeight,
						scrollableElement = $('.jsn-scrollable');					
						
					// Init scrollbar
					menuTypesList.css('height', 'auto');
					viewport.css('height', viewportHeight);					
//					scrollableElement.data('tsb') ? scrollableElement.tinyscrollbar_update() : 
					scrollableElement.tinyscrollbar();

					var menuHeight = menuTypesList.height();	
					
					// Show popup menu
					menuTypesList.css({ 'visibility': 'visible', 'display': 'block', 'height': 0, 'opacity': 1  });
					menuTypesList.stop();
					menuTypesList
						.css({ "height"  : menuHeight, "opacity" : 1 });
							menuTypeSelector.removeClass('jsn-menu-selector-disabled');
				}else{
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
		}
	};
	
	var tree = {
		/**
		* 
		* Expand an item in tree
		*
		* @param: (JoomlaShine.jQuery) (node)
		* @return: None
		*/
		expand_node: function(node){
			node.removeClass("jstree-closed").addClass("jstree-open").children("a").removeClass("jstree-loading");
		},
		/**
		* 
		* Expand all node in tree
		*
		* @param: (JoomlaShine.jQuery) (root)
		* @param: (number) (duration) is number of time duration
		* @return: change elements
		*/
		expand_all: function(root, duration){
			$('li.jstree-closed', root).each(function(){
				var obj = $(this);
				if ( obj.children('ul').length > 0 ){
					if ( obj.children('ul').css('display') == 'none' ){
						tree.expand_all(obj.children("ul"), 0);
						obj.children("ul").animate(
						{
							"height"  : "toggle", 
							"opacity" : "toggle"
						}, duration, function(){
							tree.expand_node(obj);
							$(this).show();
						});
					}else{
						tree.expand_node(obj);
					}
				} 
			});
		},
		/**
		* 
		* Collapse an node in tree
		* 
		* @return: None
		*/
		collapse_node: function(node){
			node.removeClass("jstree-open").addClass("jstree-closed");
		},
		/**
		* 
		* Collapse all node in tree
		*
		* @param: (JoomlaShine.jQuery) (root) 
		* @param: (number) (duration) is number time tick of animate
		*/
		collapse_all: function(root, duration){
			$('li.jstree-open', root).each(function(){
				var obj   = $(this);
				if (obj.children('ul').length > 0){
					if ( obj.children('ul').css('display') != 'none' ){
						tree.collapse_all(obj.children("ul"), 0);
						obj.children("ul").animate(
						{
							"height"  : "toggle", 
							"opacity" : "toggle"
						}, duration, function(){
							tree.collapse_node(obj);
							$(this).hide();
						});
					}else{
						tree.collapse_node(obj);
					}
				}
			});	
		},
		/**
		* 
		* Show an item unpublished
		*
		* @param: (JoomlaShine.jQuery) (node)
		* @return: None
		*/
		show_unpublished_node: function(node){
			node.css('display', 'block');
		},
		/**
		* 
		* Show all unpublished items
		*
		* @param: (JoomlaShine.jQuery) (root)
		* @return: None
		*/
		show_unpublished_all: function(root){
			var parents = new Array();
			root.find('li.unpublish').each(function(){
				parents[$(this).parent().attr('id')] = $(this).parent();
				tree.show_unpublished_node($(this));
			});
			for( k in parents ){
				if ( parents[k].length > 0 && typeof parents[k].addClass == 'function' ){
					parents[k].show().parent().addClass('jstree-open');
				}
			}
			tree.markup_tree(root);
		},
		/**
		* 
		* Hide an item unpublished
		*
		* @param: (JoomlaShine.jQuery) (node)
		* @return: None
		*/
		hide_unpublished_node: function(node){
			node.css('display', 'none');
		},
		/**
		* 
		* Hide all unpublised items
		*
		* @param: (JoomlaShine.jQuery) (root)
		* @return: None
		*/
		hide_unpublished_all: function(root){
			var parents = new Array();
			root.find('li.unpublish').each(function(){
				parents[$(this).parent().attr('id')] = $(this).parent();
				tree.hide_unpublished_node($(this));
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
			tree.markup_tree(root);
		},
		/**
		 * Remove last tree background repeat
		 */
		markup_tree : function(tree){
			tree.find('li.mark-last').removeClass('mark-last').removeClass('jstree-last');
			var lastItem = $({});
			tree.children('ul').children().each(function(){
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
		* Select an item
		*
		* @param: (JoomlaShine.jQuery) (node)
		* @return: None		 
		*/
		select_node: function(node){
			node.attr("checked", "checked");
		},
		/**
		*
		* Select all items
		*
		* @param: (JoomlaShine.jQuery) (root)
		* @return: None
		*/
		select_all: function(root){
			$('input[type="checkbox"]', root).each(function(){
				tree.select_node($(this));
			});
		},
		/**
		* 
		* Deselect an item
		* 
		* @param: (JoomlaShine.jQuery) (node)
		* @return: None
		*/
		deSelect_node: function(node){
			node.removeAttr("checked");
		},
		/**
		* 
		* Deselect all items
		*
		* @param: (JoomlaShine.jQuery) (root)
		* @return: None
		*/
		deSelect_all: function(root){
			$('input[type="checkbox"]', root).each(function(){
				tree.deSelect_node($(this));
			});
		},
		/**
		* 
		* Invert an item
		*
		* @param: (JoomlaShine.jQuery) (node)
		* @return: None
		*/
		invert_selection_node: function(node){
			if (node.attr('checked') == 'checked'){
				node.removeAttr("checked");
			}else{
				node.attr("checked", "checked");
			}
		},
		/**
		* 
		* Invert selection all items
		*
		* @param: (JoomlaShine.jQuery) (root)
		* @return: None
		*/
		invert_selection_all: function(root){
			var totalCheckbox = $('input[type="checkbox"]', root).length;
			if (totalCheckbox > 1){
				$('input[type="checkbox"]', root).each(function(){
					tree.invert_selection_node($(this));
				});
			}else if(totalCheckbox == 1){
				tree.invert_selection_node(root.find('input[type="checkbox"]'));
			}
		}
	};
	/**
	* 
	* For assignment page
	*
	*/
	var assignmentPages = {
		/**
		* 
		* Init events
		*
		* @return: None
		*/
		initEvents: function(){
			//Save select page to cookie
			$('#assignment_dropdown_list_chzn').hide();
			$('#assignment-dropdown-list').show().change(function(){
				var selected = $(this).find('option:selected').val();
				if (selected == 2 || selected == 3){
					$('.jsn-menu-selector-container').find('li').each(function(){
						$(this).children('input[type="checkbox"]').removeAttr('disabled');
					});
				}else if(selected == 1){
					$('.jsn-menu-selector-container').find('li').each(function(){
						$(this).children('input[type="checkbox"]').attr('checked', 'checked').attr('disabled', "disabled");
					});
				}else{
					$('.jsn-menu-selector-container').find('li').each(function(){
						$(this).children('input[type="checkbox"]').removeAttr('checked').attr('disabled', "disabled");
					});
				}
				/**
				 * Add ellipse indicate 
				 */
				$('div.jsn-menutype-title').each(function(){
					var menuid = $(this).attr('menuid');
					var ItemDropDownList = $('#dropdownmenutype-'+menuid);  
					if ( $('#'+$(this).attr('menutype')).find('input[type="checkbox"]:checked').length )
					{
						if ( ItemDropDownList.children('a').find('ins.has-selected').length == 0 ){
							ItemDropDownList.children('a').append('<ins class="has-selected"></ins>');
						}
					}else{
						ItemDropDownList.children('a').find('ins.has-selected').remove();
					}
				});
				
			});

			$(window).bind("jsn.jstree.loaded", function(){
				$('.jstree-icon',$('.jsn-assignment-form a')).remove();
				var selected = $('#assignment-dropdown-list').find('option:selected').val();
				if (selected == 2 || selected == 3){
					$('.jsn-menu-selector-container').find('li').each(function(){
						$(this).children('input[type="checkbox"]').removeAttr('disabled');
					});
				}else if(selected == 1){
					$('.jsn-menu-selector-container').find('li').each(function(){
						$(this).children('input[type="checkbox"]').attr('disabled', "disabled");
					});
				}else{
					$('.jsn-menu-selector-container').find('li').each(function(){
						$(this).children('input[type="checkbox"]').removeAttr('checked').attr('disabled', "disabled");
					});
				}
				
				/**
				 * Add ellipse indicate 
				 */
				$('div.jsn-menutype-title').each(function(){
					var menuid   = $(this).attr('menuid');
					var menutype = $(this).attr('menutype');
					var ItemDropDownList = $('#dropdownmenutype-'+menuid);
					if ( $('#'+menutype).find('input[type="checkbox"]:checked').length )
					{
						if ( ItemDropDownList.children('a').find('ins.has-selected').length == 0 ){
							ItemDropDownList.children('a').append('<ins class="has-selected"></ins>');
						}
					}else{
						ItemDropDownList.children('a').find('ins.has-selected').remove();
					}
				});
				
				
			});
			
			$('#jsn-toggle-publish-module').unbind('click').click(function(e){				
				e.stopPropagation();
				if ( !$(this).hasClass('btn-enabled') ){
					$(this).removeClass('btn-disabled').addClass('btn-enabled');
					$(this).addClass('btn-success');
					$.jStorage.set('menuitem_publishing_options', true);
					$(this).attr('title', JSNLang.translate('TITLE_HIDE_UNPUBLISHED'));
					$('.jsn-menuitem-assignment').each(function(){
						tree.show_unpublished_all($(this));
					});
				}else{
					$(this).removeClass('btn-enabled').addClass('btn-disabled');
					$(this).removeClass('btn-success');
					$.jStorage.set('menuitem_publishing_options', false);
					$(this).attr('title', JSNLang.translate('TITLE_SHOW_UNPUBLISHED'));
					$('.jsn-menuitem-assignment').each(function(){
						tree.hide_unpublished_all($(this));
					});
				}
			});
			
			if ( $.jStorage.get('menuitem_publishing_options') ){
				$('#jsn-toggle-publish-module').addClass('btn-success');
				$('#jsn-toggle-publish-module').removeClass('btn-disabled').addClass('btn-enabled').attr('title', JSNLang.translate('TITLE_HIDE_UNPUBLISHED'));
			}else{
				$('#jsn-toggle-publish-module').removeClass('btn-success');
				$('#jsn-toggle-publish-module').removeClass('btn-enabled').addClass('btn-disabled').attr('title', JSNLang.translate('TITLE_SHOW_UNPUBLISHED'));
			}
			
			//Show/hide dropdown select menutypes
			$('.jsn-menu-selector-container').unbind("scroll").bind("scroll", function () {
				var menuTypeSelector = $('.jsn-assignment-form').find('.jsn-menu-selector');
				var scrollTop = $(this).scrollTop();
				if ( scrollTop <= 10 ){
					menuTypeSelector.show();
				}else{
					menuTypeSelector.hide();
				}
			});
			
			setTimeout(function(){
				menuTypes.selectMenu();
			}, 500)
		},
		/**
		* 
		* Build context menu in tree area
		*
		* @return: None
		*/
		contextMenu: function(){
			$('.jsn-menu-selector-container').each(function(){
				var root = $(this);
				var contextMenu = root.jsnSubmenu({rebuild:false, rightClick: true, attrs:{'class': 'jsnpw-submenu jsn-assignment-contextmenu'}});
			
				if (contextMenu.isNew()){
	
					/**
					 * Expand All item
					 */
					contextMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_EXPAND_ALL' ) ).click(function(){
						contextMenu.hide({});
						if (tree.onNode != null){
							tree.expand_all(tree.onNode, 0);
							if (tree.onNode.find('ul').length > 0 && tree.onNode.hasClass('jstree-closed')){
								tree.onNode.children("ul").animate(
								{
									"height"  : "toggle", 
									"opacity" : "toggle"
								}, 600, function(){
									tree.expand_node(tree.onNode);
									tree.onNode = null;
									$(this).show();
								});
							}
						}else{
							tree.expand_all(root, 600);
						}
					});

					/**
					* Collapse all item
					*/
					contextMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_COLLAPSE_ALL' ) ).click(function(){
						contextMenu.hide({});
						tree.collapse_all(root, 600);
					});

					/**
					* Add divider
					*/
					contextMenu.addDivider();
					
					/**
					* Select all items
					*/
					contextMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_SELECT_ALL' ) ).click(function(){
						contextMenu.hide({});
						tree.select_all(root);
					});

					/**
					* Deselect all items
					*/
					contextMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ALL' ) ).click(function(){
						contextMenu.hide({});
						tree.deSelect_all(root);
					});

					/**
					* Invert selection
					*/
					contextMenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_INVERT_SELECTION' ) ).click(function(){
						contextMenu.hide({});
						tree.invert_selection_all(root);
					});
				}

				$(this).mousedown(function(e){
					try{
						if (e.which === 1 && e.target.tagName != 'A' && $(e.target).parents('div.jsn-menuitem-assignment').length > 0 && !$(e.target).hasClass('jstree-icon') ){
							var root = menuTypes.getSelected();
							var hideDividers = false;
							if (root.children('ul').children('li.jstree-open').length == 0 && root.children('ul').children('li.jstree-closed').length == 0){
								contextMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_EXPAND_ALL' ) );
								contextMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_COLLAPSE_ALL' ) );
								contextMenu.hideDividers();
								hideDividers = true;
							}else{
								contextMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_EXPAND_ALL' ) );
								contextMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_COLLAPSE_ALL' ) );
								contextMenu.showDividers();
							}
							
							var disabled = root.find('input[type="checkbox"]:disabled').length;
							var totalCheckbox = root.find('input[type="checkbox"]').length;
							if (disabled == totalCheckbox){
								contextMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_SELECT_ALL' ) );
								contextMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ALL' ) );
								contextMenu.hideItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_INVERT_SELECTION' ) );
								contextMenu.hideDividers();
							}else{
								contextMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_SELECT_ALL' ) );
								contextMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ALL' ) );
								contextMenu.showItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_INVERT_SELECTION' ) );
								if (!hideDividers){
									contextMenu.showDividers();
								}
							}
							contextMenu.show({x: e.pageX+5, y: e.pageY+10});
						}else{
							contextMenu.hide({});
						}
					}catch(e){
						throw e.message;
					}finally{
						return;
					}
				});

				$(window).bind("click", function(){
					if (!contextMenu.mouseIsIn()){
						contextMenu.hide({});
					}
				});
			});
		},
		/**
		* 
		* Build tree. Using jstree lib
		*
		*/
		jstreeInstance: function(){
			var treeIndex = 0, totalTree = $('.jsn-menuitem-assignment').length;
			//Init jstree
			$('.jsn-menuitem-assignment').jstree({
				// the `plugins` array allows you to configure the active plugins on this instance
				"plugins" : ["themes","crrm", , "hotkeys", "contextmenu", "html_data",  "ui"],
				// each plugin you have included can have its own config object
				"themes" : { "theme":"jsnpa", "url" : baseUrl+'/administrator/components/com_poweradmin/assets/css/jstree/style.css' },
				"contextmenu" : {
					items:{
					    "Expand_all" : {
							"separator_before"	: false,
							"separator_after"	: false,
							"label"				: JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_EXPAND_ALL' ),
							"action"			: function(obj){
								tree.expand_all(obj, 0);
								if (obj.find('ul').length > 0 && obj.hasClass('jstree-closed')){
									obj.children("ul").animate(
									{
										"height"  : "toggle", 
										"opacity" : "toggle"
									}, 600, function(){
										tree.expand_node(obj);
										$(this).show();
									});
								}
							}
						},
						"Collapse_all" : {
							"separator_before"	: false,
							"separator_after"	: true,
							"label"				: JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_COLLAPSE_ALL' ),
							"action"			: function(obj){
								tree.collapse_all(obj, 0);
								if ( obj.children('ul').length > 0 && obj.hasClass('jstree-open') ){
									obj.children("ul").animate(
									{
										"height"  : "toggle", 
										"opacity" : "toggle"
									}, 600, function(){
										tree.collapse_node(obj);
										$(this).hide();
									});
								}else if(obj.children('ul').length){
									obj.find('ul').css('display', 'none');
								}
							}
						},
						"Select_item" : {
							"separator_before"	: false,
							"icon"				: false,
							"separator_after"	: false,
							"label"				: JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_SELECT_ITEM' ),
							"action"			: function(obj){
								tree.select_node(obj.children('input[type="checkbox"]'));
							}
						},
						"Deselect_item" : {
							"separator_before"	: false,
							"icon"				: false,
							"separator_after"	: false,
							"label"				: JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ITEM' ),
							"action"			: function(obj){
								tree.deSelect_node(obj.children('input[type="checkbox"]'));
							}
						},
						"Select_all" : {
							"separator_before"	: false,
							"icon"				: false,
							"separator_after"	: false,
							"label"             : JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_SELECT_ALL' ),
							"action"            : function(obj){
								tree.select_node(obj.children('input[type="checkbox"]'));
								tree.select_all(obj);
							}
						},
						"Deselect_all" : {
							"separator_before"	: false,
							"icon"				: false,
							"separator_after"	: false,
							"label"             : JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_DESELECT_ALL' ),
							"action"            : function(obj){
								tree.deSelect_node(obj.children('input[type="checkbox"]'));
								tree.deSelect_all(obj);
							}
						},
						"Invert_selection" : {
							"separator_before"	: false,
							"icon"				: false,
							"separator_after"	: false,
							"label"				: JSNLang.translate( 'TITLE_SUBMENU_ASSIGNPAGE_INVERT_SELECTION' ),
							"action"			: function(obj){
								tree.invert_selection_all(obj);
							}
						}
					}	
				},
				"core" :   { "initially_open" : [ "phtml_1" ]	}
			})			
			.bind("loaded.jstree", function (event, data) {
				$(this).find('a').each(function(){
					if ( $(this).hasClass('default') ){
						$(this).removeClass('default').parent().addClass('default');
					}
					if( $(this).hasClass('unpublish') ){
						$(this).removeClass('unpublish').parent().addClass('unpublish');
					}
				});
				//Hide all unpublished items
				if ( $.jStorage.get('menuitem_publishing_options') ){
					tree.show_unpublished_all($(this));
				}else{
					tree.hide_unpublished_all($(this));
				}
				
				$(document).unbind("showmenu.jstree").bind("showmenu.jstree", function(event, obj, items){
					$.jsnSubmenu.hideAll();
					if (obj.children('ul').length > 0 && (obj.hasClass('jstree-open') || obj.hasClass('jstree-closed'))){
						var selected = $('#assignment-dropdown-list').find('option:selected').val();
						if (selected == 1 || selected == 0){
							items['Select_all'].show       = false;
							items['Deselect_all'].show     = false;
							items['Invert_selection'].show = false;
						}else{
							items['Select_all'].show       = true;
							items['Deselect_all'].show     = true;
							items['Invert_selection'].show = true;
						}
						items['Expand_all'].show    = true;
						items['Collapse_all'].show  = true;
						items['Select_item'].show   = false;
						items['Deselect_item'].show = false;
					}else{
						items['Expand_all'].show    = false;
						items['Collapse_all'].show  = false;
						items['Select_item'].show   = false;
						items['Deselect_item'].show = false;
						items['Select_all'].show    = false;
						items['Deselect_all'].show  = false;
						items['Invert_selection'].show = false;
						var node = obj.children('input[type="checkbox"]')[0];
						if (!node.disabled){
							if (node.checked){
								items['Deselect_item'].show = true;
							}else{
								items['Select_item'].show = true;
							}
						}
					}
				});

				treeIndex++;
				if ( treeIndex == totalTree ){
					$(window).triggerHandler("jsn.jstree.loaded");
				}
			});
		}
	};
	/**
	* 
	* Ready window
	* 
	*/
	$(document).ready(function(){
		
		menuTypes.initEvent();
		assignmentPages.jstreeInstance();
		setTimeout(function (){
			assignmentPages.initEvents();
			
		}, 200);		
		//assignmentPages.contextMenu();	
	});	
})(JoomlaShine.jQuery);
