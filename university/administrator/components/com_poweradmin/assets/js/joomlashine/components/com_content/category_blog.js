/**
 * @subpackage	com_poweradmin (JSN POERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/
(function($){
	/**
 	 * View category
 	 */	
	$.com_content_category_blog = function(Itemid){
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
			this.setData( 'view', 'category' );
			this.setData( 'layout', 'blog' );
			this.setData( 'id', $('#category_id').val() );
			this.setData( 'Itemid', Itemid );
			this.setData( 'requestType', 'only' );
			
			//Scan elements approved context menu
			this.contextElements = new Array( $( '.'+this.classApprovedContextMenu ).length );
			var $this = this;
			$( '.'+this.classApprovedContextMenu ).each(function(){
				//Children items is of category
				if ( ( $(this).parents('div.category').length || $(this).hasClass('category-desc') ) && $(this).attr('id') != 'show_description_image' || $(this).hasClass('empty-category')){
					$(this).data('category', true);
				}else{
					$(this).data('category', false);
				}
				//Children items is of article
				if ( $(this).parents('div.article_layout').length && (/show_intro/.test($(this).attr('id')) || /show_title/.test($(this).attr('id'))) ){
					$(this).data('article', true);
				}else{
					$(this).data('article', false);
				}
				//Children items is of linkitems
				if ( $(this).parents('div.link-items').length ){
					$(this).data('link_items', true);
				}else{
					$(this).data('link_items', false);
				}
				//Children items is of children categories
				if ( $(this).hasClass('cat-children') ){
					$(this).data('categories_childrens', true);
				}else{
					$(this).data('categories_childrens', false);
				}
				//Children items is of pagination
				if ( $(this).parents('div.jsn-rawmode-pagination').length ){
					$(this).data('pagination', true);
				}else{
					$(this).data('pagination', false);
				}
				
				if ( $(this).hasClass('display-default') ){
					$(this).data('show', true);
				}else{
					$(this).data('show', false);
				}
				
				if ( $(this).hasClass('createdby') && $(this).attr('contactid') != ''){
					$(this).data('author', true);
				}else{
					$(this).data('author', false);
				}
				
				if (/show_intro/.test($(this).attr('id'))){
					$(this).data('introtext', true);
				}else{
					$(this).data('introtext', false);
				}
				$this.contextElements[$(this).attr('id')] = $(this);
			});
		};
		/**
		 * Ajax request task function
		 */
		this.beforeAjaxRequest = function(task){
			this.currentElement.showImgStatus({status : 'request'});
			if (this.currentElement.parents('div.article_layout').length || this.currentElement.parents('div.cat-children').length){
				this.setData('prefix_params', true);
			}else{
				this.setData('prefix_params', false);
			}
			this.setData( 'requestTask', task );
			this.ajaxRequest();
		};
		/**
		 * For element settings
		 * 
		 */
		this.editArticle = function(){
			var $this = this;
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8;
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_content&task=article.edit&tmpl=component&id='+$this.getData('aid'),
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
									//iframe[0].contentWindow.contentResize.setSize('articletext', wWidth, wHeight);
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
						'Save': {
							text: 'Save',
							click: function(){
								var _this  = $(this);
								var iframe = $(this).find('iframe');
								_this.addClass('jsn-loading');
								if ( pop.submitForm('article.apply', 'Save') ){
									iframe.load(function(){
										_this.removeClass('jsn-loading');
										$this.beforeAjaxRequest('brankNewData');
									});
								}
							}
						},
						'Close': {
							text: 'Close',
							click: function(){
								$(this).dialog("close");
							}
						}
					}
				}
			);
			//$('.ui-button').removeClass('ui-button');
			//$('button').button();
		};
		this.editCategory = function(){
			var $this = this;
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8;
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_categories&task=category.edit&extension=com_content&id='+$this.getData('catid')+'&tmpl=component',
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
		};
		this.editAuthor = function(){
			var $this = this;
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8;
			var pop = $.JSNUIWindow
			(
			baseUrl+'administrator/index.php?option=com_contact&task=contact.edit&id='+$this.getData('id')+'&tmpl=component',
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
			});
		};
		this.setArticleLayout = function(){
			var $this = this;
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=component.redirect_setting&layout_setting=set_article_layout&menuid='+$this.getData('Itemid')+'&request_from_extension='+$this.getData('option')+'&request_from_view='+$this.getData('view')+'&request_from_layout='+$this.getData('layout')+'&tmpl=component',
				{
					modal  : true,
					width  : 570, 
					height : 520,
					scrollContent: false,
					title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_ACTION_SET_ARTICLE_LAYOUT_PAGE_TITLE' ),
					open   : function(){
						var _this  = $(this);
						var iframe = $(this).find('iframe');
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

							if (pop.submitForm('component.custompageSave', 'Save')){
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
				});
		};
		this.setSubCategories = function(){
			var $this = this;
			var pop = $.JSNUIWindow
			(
			baseUrl+'administrator/index.php?option=com_poweradmin&task=component.redirect_setting&layout_setting=set_sub_categories&menuid='+$this.getData('Itemid')+'&request_from_extension='+$this.getData('option')+'&request_from_view='+$this.getData('view')+'&request_from_layout='+$this.getData('layout')+'&tmpl=component',
			{
				modal  : true,
				width  : 500, 
				height : 180,
				scrollContent: false,
				title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES_PAGE_TITLE' ),
				open   : function(){
					var _this  = $(this);
					var iframe = $(this).find('iframe');
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
						if (pop.submitForm('component.custompageSave', 'Save')){
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
			});
		};
		this.readmoreSettings = function(){
				var $this = this;
				var pop = $.JSNUIWindow
				(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=component.redirect_setting&layout_setting=readmore_settings&menuid='+$this.getData('Itemid')+'&request_from_extension='+$this.getData('option')+'&request_from_view='+$this.getData('view')+'&request_from_layout='+$this.getData('layout')+'&tmpl=component',
				{
					modal  : true,
					width  : 500, 
					height : 250,
					scrollContent: false,
					title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_ACTION_EDITSETTINGS_READMORE_PAGE_TITLE' ),
					open   : function(){
						var _this  = $(this);
						var iframe = $(this).find('iframe');
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
							if (pop.submitForm('component.custompageSave', 'Save')){
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
				});
			};
 		
		
		//Add context menu
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
						var articleId = $this.currentElement.attr('id').split('_');
						$this.setData( 'aid', articleId[articleId.length-1]);
						$this.editArticle();
					});
					/**
					 * 
					 * Add menu for edit the category
					 */
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						var catId = $this.currentElement.attr('catid');
						$this.setData( 'catid', catId);
						$this.editCategory();
					});
					/**
					 * 
					 * Add menu for edit the author
					 */
//					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR') ).addEventHandler("click", function(){
//						$this.contextMenu.hide({});
//						$this.setData( 'id', $this.currentElement.attr('contactid') );
//						$this.editAuthor();
//					});
					/**
					 * Readmore settings
					 */
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_EDITSETTINGS_READMORE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.readmoreSettings();
					});
					/**
					 * Setting article layout
					 */
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SET_ARTICLE_LAYOUT') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setArticleLayout();
					});
					/**
					 * Setting sub-categories
					 */
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setSubCategories();
					});
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
						$this.beforeAjaxRequest('hide');
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
						if (/link_title/.test(paramKey)){
							paramKey = 'link_titles';
						}
						$this.setParams( paramKey, 1);
						$this.beforeAjaxRequest();
					});
					parentShowLink.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setData( 'requestType', 'only' );
						var paramKey = $this.currentElement.attr('id').replace('show', 'link');
						if (/link_title/.test(paramKey)){
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
						if (/link_title/.test(paramKey)){
							paramKey = 'link_titles';
						}
						$this.setParams( paramKey, 0);
						$this.beforeAjaxRequest();
					});
					parentHideLink.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTIONLINK_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setData( 'requestType', 'only' );
						var paramKey = $this.currentElement.attr('id').replace('show', 'link');
						if (/link_title/.test(paramKey)){
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
						$this.setParams( paramKey, 1);
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
						//$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SET_ARTICLE_LAYOUT'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ENABLE_LINK'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_DISABLE_LINK'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_ICON'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_TEXT'));
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_EDITSETTINGS_READMORE') );

						if ( current.data('category') ){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
						}else if( current.data('article') ){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_ARTICLE'));
						}else if( current.data('link_items') || current.hasClass('article-layout-grid')){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SET_ARTICLE_LAYOUT'));
						}else if( current.data('categories_childrens') ){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES'));
						}else if( current.data('pagination') ){
						}
						if (current.hasClass('readmore')){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_EDITSETTINGS_READMORE') );
						}

						if ( current.attr('icon') != undefined){
							if (current.attr('icon') == 1){
 								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_TEXT'));
							}else{
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ICON_SHOW_ICON'));
							}
						}

						if ( current.data('show') && !current.data('categories_childrens') && !current.data('introtext') && !current.data('link_items') && !current.hasClass('article-layout-grid')){
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
						}else if( !current.data('categories_childrens') && !current.data('introtext') && !current.data('link_items') && !current.hasClass('article-layout-grid')){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
						}

						if ( current.data('author') ){
							//$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_AUTHOR'));
						}

						if ( current.attr('link') != undefined ){
							if ( current.attr('link') == 1 ){
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_DISABLE_LINK'));
							}else{
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ENABLE_LINK'));
							}
						}

						if ( current.hasClass('item-category') ){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
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