/**
 * @subpackage	com_poweradmin (JSN POWERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/
(function($){
	$.com_content_category = function(Itemid){
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
			this.setData( 'layout', '' );
			this.setData( 'id', $('#category_id').val()  );
			this.setData( 'Itemid', Itemid );
			this.setData( 'requestType', 'only' );			
			//Scan elements approved context menu
			this.contextElements = new Array( $( '.'+this.classApprovedContextMenu ).length );
			var $this = this;
			$( '.'+this.classApprovedContextMenu ).each(function(){
				if ( $(this).parents('div.category').length || $(this).hasClass('category-desc') || $(this).hasClass('empty-category')){
					$(this).data('edit-category', true);
				}else{
					$(this).data('edit-category', false);
				}
				if ( $(this).hasClass('article-tablist')){
					$(this).data('set-content-layout', true);
				}else{
					$(this).data('set-content-layout', false);
				}
				if ( $(this).hasClass('cat-children')){
					$(this).data('set-subcategories', true);
				}else{
					$(this).data('set-subcategories', false);
				}
				if ( $(this).hasClass('display-default') ){
					$(this).data('show', true);
				}else{
					$(this).data('show', false);
				}				
				$this.contextElements[$(this).attr('id')] = $(this);
			});
		};
		/**
		 * Ajax request task function
		 */
		this.beforeAjaxRequest = function(task){
			this.currentElement.showImgStatus({status : 'request'});
			if (this.currentElement.parents('div.article-item').length && !this.currentElement.hasClass('article-table-listing') ){
				this.setData('prefix_params', true);
			}else{
				this.setData('prefix_params', false);
			}
			this.setData( 'requestTask', task );
			this.ajaxRequest();
		};
		/**
		 * Edit category
		 */
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
		/**
		 * Show modal window setting 
		 */
		this.setLayoutContent = function(){
			var $this = this;
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=component.redirect_setting&layout_setting=set_layout_content&menuid='+$this.getData('Itemid')+'&request_from_extension='+$this.getData('option')+'&request_from_view='+$this.getData('view')+'&tmpl=component',
				{
					modal  : true,
					width  : 500,
					height : 650,
					scrollContent: false,
					title  : JSNLang.translate( 'JSN_RAWMODE_COMPONENT_ACTION_SET_CONTENT_LAYOUT_TITLE' ),
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
		/**
		 * Set sub-category
		 */
		this.setSubCategories = function(){
			var $this = this;
			var pop = $.JSNUIWindow
			(
			baseUrl+'administrator/index.php?option=com_poweradmin&task=component.redirect_setting&layout_setting=set_sub_categories&menuid='+$this.getData('Itemid')+'&request_from_extension='+$this.getData('option')+'&request_from_view='+$this.getData('view')+'&tmpl=component',
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
		/**
		 * Add context menu
		 */
		this.addContextMenu = function(){
			this.contextMenu = this.getContextMenu();
			var $this = this;
			if ( this.contextMenu != null ){
				if ( $this.contextMenu.isNew() ){
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
					 * Setting content layout
					 */
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SET_CONTENT_LAYOUT') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setLayoutContent();
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
						if ($this.currentElement.hasClass('filter-search')){
							$this.setParams( $this.currentElement.attr('id'), 'hide' );
						}else{
							$this.setParams( $this.currentElement.attr('id'), 0 );
						}
						$this.beforeAjaxRequest();
					});
					/**
					 * Add item and assign to process hide this article
					 */
					parentHideItem.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setData( 'requestType', 'only' );
						if ($this.currentElement.hasClass('filter-search')){
							$this.setParams( $this.currentElement.attr('id'), 'hide' );
						}else{
							$this.setParams( $this.currentElement.attr('id'), 0 );
						}
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
						if ($this.currentElement.hasClass('filter-search')){
							$this.setParams( $this.currentElement.attr('id'), 'title' );
						}else if( $this.currentElement.hasClass('list-date')){
							$this.setParams( $this.currentElement.attr('id'), 'published' );
						}else{
							$this.setParams( $this.currentElement.attr('id'), 1 );
						}
						
						$this.beforeAjaxRequest();
					});
					/**
					 * Add item and assign to process show this article
					 */
					parentShowItem.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOWHIDE_ARTICLE_ONLY_THIS_PAGE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setData( 'requestType', 'only' );
						if ($this.currentElement.hasClass('filter-search')){
							$this.setParams( $this.currentElement.attr('id'), 'title' );
						}else if( $this.currentElement.hasClass('list-date')){
							$this.setParams( $this.currentElement.attr('id'), 'published' );
						}else{
							$this.setParams( $this.currentElement.attr('id'), 1 );
						}
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
						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
 						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
 						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
 						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES') );
 						$this.contextMenu.hideItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SET_CONTENT_LAYOUT') );
 						
 						if ( current.data('edit-category') ){
 							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
 						}
 						if ( current.data('set-content-layout') ){
 							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SET_CONTENT_LAYOUT') );
 						}
 						if ( current.data('set-subcategories') ){
 							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_ACTION_SETSUBCATEGORIES') );
 						}
 						
 						if ( current.data('show') && !current.data('set-content-layout') && !current.data('set-subcategories')){
							var itemAction = $this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
						}else if( !current.data('set-content-layout') && !current.data('set-subcategories')){
							var itemAction = $this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
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