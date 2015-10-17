/**
 * @subpackage	com_poweradmin (JSN POERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/
(function($){
	$.com_content_article = function(Itemid){
		
			/**
		 	 * Variable to store context menu
		 	 */
			this.contextMenu;
			this.contextElements;
			this.currentElement;
			/**
			 * Init all variables
			 */
			this.initVariables = function(){
				this.setData( 'option', 'com_content' );
				this.setData( 'view', 'article' );
				this.setData( 'layout', '' );
				this.setData( 'id', $('#articleId').val() );
				this.setData( 'Itemid', Itemid );
				this.setData( 'requestType', 'only' );
				
				//Scan elements approved context menu
				this.contextElements = new Array( $( '.'+this.classApprovedContextMenu ).length );
				var $this = this;
				$( '.'+this.classApprovedContextMenu ).each(function(){
					if  ( $(this).hasClass('article_text') || $(this).hasClass('article-header-title') || $(this).hasClass('introtext')){
						$(this).data('edit', true);
					}else{
						$(this).data('edit', false);
					}
					if ( $(this).hasClass('display-default') ){
						$(this).data('show', true);
					}else{
						$(this).data('show', false);
					}
					if  ( $(this).attr('id') == 'show_parent_category' || $(this).attr('id') == 'show_category' ){
						$(this).data('category', true);
					}else{
						$(this).data('category', false);
					}
					if  ( $(this).attr('id') == 'show_author' ){
						$(this).data('author', true);
					}else{
						$(this).data('author', false);
					}
					$this.contextElements[$(this).attr('id')] = $(this);
				});
				
				//Clear all href attribute of article
				$('a', $('.jsn-article-layout')).each(function(){
					$(this).removeAttr('href');
				});
			};
			/**
			 * Ajax request task function
			 */
			this.beforeAjaxRequest = function(task){
				this.currentElement.showImgStatus({status : 'request'});
				this.setData( 'requestTask', task );
				this.ajaxRequest();
			};
			this.editElement = function()
			{
				var $this = this;
				var editFuncs  = $.extend({
					article : function(){
			 			var wWidth  = $(window).width()*0.85;
						var wHeight = $(window).height()*0.8;
						var pop = $.JSNUIWindow
						(
							baseUrl+'administrator/index.php?option=com_content&task=article.edit&tmpl=component&id='+$this.getData('id'),
							{
								modal  : true,
								width  : wWidth, 
								height : wHeight,
								scrollContent: true,
								title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_EDIT_ARTICLE_PAGE_TITLE' ),
								open   : function(){									
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.load(function(){
										setTimeout(function(){
											if ( iframe[0].contentWindow != undefined ){
												var head = iframe.contents().find('head');
												//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
												//iframe[0].contentWindow.contentResize.setSize('articletext', wWidth, wHeight, 140);
											}
										}, 400);
									});
									//bind trigger press enter submit form from child page
									$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
										iframe.load(function(){
											$this.beforeAjaxRequest('brankNewData');
										});
									});
								},
								buttons: {									
									'Save': function(){
										var _this  = $(this);
										var iframe = $(this).find('iframe');
										_this.addClass('jsn-loading');

										if ( pop.submitForm('article.apply', 'Save') ){
											iframe.load(function(){
												_this.removeClass('jsn-loading');
												$this.beforeAjaxRequest('brankNewData');
											});
										}
									},
									'Close': function(){
										$(this).dialog("close");
									}
								}
							}
						);
					},
					
					category : function(){
						var wWidth  = $(window).width()*0.85;
						var wHeight = $(window).height()*0.8;
						var pop = $.JSNUIWindow
						(
							baseUrl+'administrator/index.php?option=com_categories&task=category.edit&extension=com_content&id='+$this.currentElement.attr('catid')+'&tmpl=component',
							{
								modal  : true,
								width  : wWidth, 
								height : wHeight,
								scrollContent: true,
								title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_EDIT_CATEGORY_PAGE_TITLE' ),
								open   : function(){
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.load(function(){
										setTimeout(function(){
											if ( iframe[0].contentWindow != undefined ){
												var head = iframe.contents().find('head');
												//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
												//iframe[0].contentWindow.contentResize.setSize('description', wWidth, wHeight, 140);
											}
										}, 400);
									});
									//bind trigger press enter submit form from child page
									$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
										iframe.load(function(){
											$this.beforeAjaxRequest('brankNewData');
										});
									});
								},
								buttons: {									
									'Save': function(){
										var _this  = $(this);
										var iframe = $(this).find('iframe');
										_this.addClass('jsn-loading');
										if (pop.submitForm('category.apply', 'Save' )){
											iframe.load(function(){
												_this.removeClass('jsn-loading');
												$this.beforeAjaxRequest('brankNewData');
											});
										}
									},
									'Close': function(){
										$(this).dialog("close");
									}
								}
							}
						);
					},
					
					author : function(){
						var wWidth  = $(window).width()*0.85;
						var wHeight = $(window).height()*0.8;
						var pop = $.JSNUIWindow
						(
							baseUrl+'administrator/index.php?option=com_contact&task=contact.edit&id=='+$this.currentElement.attr('contactid')+'&tmpl=component',
							{
								modal  : true,
								width  : wWidth, 
								height : wHeight,
								scrollContent: true,
								title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_EDIT_AUTHOR_PAGE_TITLE' ),
								open   : function(){
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									//bind trigger press enter submit form from child page
									$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
										iframe.load(function(){
											var head = iframe.contents().find('head');
											//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
											$this.beforeAjaxRequest('brankNewData');
										});
									});
								},
								buttons: {									
									'Save': function(){
										var _this  = $(this);
										var iframe = $(this).find('iframe');
										_this.addClass('jsn-loading');

										if (pop.submitForm('contact.apply', 'Save')){
											iframe.load(function(){
												_this.removeClass('jsn-loading');
												$this.beforeAjaxRequest('brankNewData');
											});
										}
									},
									'Close': function(){
										$(this).dialog("close");
									}
								}
							}
						);
					}
				});
				
				var elementType = $this.currentElement.attr('id');
				if ( elementType == 'show_parent_category' || elementType == 'show_category' ){
					editFuncs.category();
				}else if( elementType == 'show_author' ){
					editFuncs.author();
				}else{
					editFuncs.article();
				}
			};
			/**
			 * 
			 * Add function build context menu
			 * 
			 * @return: Build submenu with jsnSubmenu
			 */
			this.addContextMenu = function(){
				this.contextMenu = this.getContextMenu();
				var $this = this;
				if ( this.contextMenu != null ){
					if ( $this.contextMenu.isNew() ){
						/**
						 * 
						 * Add menu for edit the article
						 */
						$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_ARTICLE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.editElement();
						});
						/**
						 * 
						 * Add menu for edit the category
						 */
						$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.editElement();
						});
						/**
						 * 
						 * Add menu for edit the author
						 */
//						$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR') ).addEventHandler("click", function(){
//							$this.contextMenu.hide({});
//							$this.editElement();
//						});
						/**
						 * Add parent item 
						 */
						var parentHideItem = $this.contextMenu.addParentItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE') );
						/**
						 * Add item and assign to process hide globally for all articles
						 */
						parentHideItem.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_GLOBAL_FOR_ALL_PAGES') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'globally' );
							$this.setParams( $this.currentElement.attr('id'), 0);
							$this.beforeAjaxRequest();
						});
						/**
						 * Add item and assign to process hide this article
						 */
						parentHideItem.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'only' );
							$this.setParams( $this.currentElement.attr('id'), 0);
							$this.beforeAjaxRequest();
						});
						
						/**
						 * Add parent item 
						 */
						var parentShowItem = $this.contextMenu.addParentItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE') );
						/**
						 * Add item and assign to process show globally for all articles
						 */
						parentShowItem.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_GLOBAL_FOR_ALL_PAGES') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'globally' );
							$this.setParams( $this.currentElement.attr('id'), 1 );
							$this.beforeAjaxRequest();
						});
						/**
						 * Add item and assign to process show this article
						 */
						parentShowItem.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'only' );
							$this.setParams( $this.currentElement.attr('id'), 1 );
							$this.beforeAjaxRequest();
						});
						/**
						 * Show link
						 */
						var parentShowLink = $this.contextMenu.addParentItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ENABLE_LINK') );
						parentShowLink.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_GLOBAL_FOR_ALL_PAGES') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'globally' );
							var paramKey = $this.currentElement.attr('id').replace('show', 'link');
							if (paramKey == 'link_title'){
								paramKey = 'link_titles';
							}
							$this.setParams( paramKey, 1);
							$this.beforeAjaxRequest();
						});
						parentShowLink.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'only' );
							var paramKey = $this.currentElement.attr('id').replace('show', 'link');
							if (paramKey == 'link_title'){
								paramKey = 'link_titles';
							}
							$this.setParams( paramKey, '1');
							$this.beforeAjaxRequest();
						});
						/**
						 * Hide link
						 */
						var parentHideLink = $this.contextMenu.addParentItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_DISABLE_LINK') );
						parentHideLink.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_GLOBAL_FOR_ALL_PAGES') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'globally' );
							var paramKey = $this.currentElement.attr('id').replace('show', 'link');
							if (paramKey == 'link_title'){
								paramKey = 'link_titles';
							}
							$this.setParams( paramKey, 0);
							$this.beforeAjaxRequest();
						});
						parentHideLink.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'only' );
							var paramKey = $this.currentElement.attr('id').replace('show', 'link');
							if (paramKey == 'link_title'){
								paramKey = 'link_titles';
							}
							$this.setParams( paramKey, 0);
							$this.beforeAjaxRequest();
						});
						/**
						 * Show icon as icon
						 */
						var parentShowicon = $this.contextMenu.addParentItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_ICON') );
						parentShowicon.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_GLOBAL_FOR_ALL_PAGES') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'globally' );
							$this.setParams( 'show_icons', 1);
							$this.beforeAjaxRequest();
						});
						parentShowicon.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'only' );
							$this.setParams( 'show_icons', 1);
							$this.beforeAjaxRequest();
						});
						/**
						 * Show icon as text
						 */
						var parentShowtext = $this.contextMenu.addParentItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_TEXT') );
						parentShowtext.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_GLOBAL_FOR_ALL_PAGES') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'globally' );
							$this.setParams( 'show_icons', 0);
							$this.beforeAjaxRequest();
						});
						parentShowtext.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
							$this.contextMenu.hide({});
							$this.setData( 'requestType', 'only' );
							$this.setParams( 'show_icons', 0);
							$this.beforeAjaxRequest();
						});
	 				}
					$this.container.unbind("mousedown").mousedown(function(e){
						if ($(e.target).hasClass($this.classApprovedContextMenu.replace('.', ''))){
							$this.currentElement = $(e.target);
						}else{
							$this.currentElement = $(e.target).parents('.'+$this.classApprovedContextMenu);
						}
						var tagId = ( $this.currentElement.attr('id') != undefined ? $this.currentElement.attr('id') : '' );
						if ( e.which == 1 && $this.contextElements[tagId] != undefined ){
							var current = $this.contextElements[tagId];
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_ARTICLE'));
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
							//$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR'));
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ENABLE_LINK'));
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_DISABLE_LINK'));
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_TEXT')); 
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_ICON'));
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
							$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
							
							if ( current.attr('icon') != undefined){
								if (current.attr('icon') == 1){
									$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_TEXT'));
								}else{
									$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_ICON'));
								}
							}
							
							if ( current.attr('link') != undefined ){
								if ( current.attr('link') == 1 ){
									$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_DISABLE_LINK'));
								}else{
									$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ENABLE_LINK'));
								}
							}
							
							if ( current.data('category') ){
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
							}else if ( current.data('author') ){
								//$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR'));
							}
							
							if ( current.data('edit') ){
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_ARTICLE'));
								if (current.hasClass('article-header-title') || current.hasClass('introtext')){
									if ( current.data('show') ){
										$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
									}else{
										$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
									}
								}
							}else{
								if ( current.data('show') ){
									$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
								}else{
									$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
								}
							}
							
							$this.contextMenu.show({
								x : e.pageX,
								y : e.pageY
							});							
							$this.contextMenu.trigger('component.context.show');
						}else{
							$this.currentElement = $({});
						}
					});
				}
			};
		};	
})(JoomlaShine.jQuery);