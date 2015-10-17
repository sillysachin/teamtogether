/**
 * @subpackage	jsnpoweradmin (JSN POERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/

(function($){
	$.com_content_categories = function(Itemid){
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
			this.setData( 'view', 'categories' );
			this.setData( 'layout', '' );
			this.setData( 'task', $('#task').val()  );
			this.setData( 'id', $('#category_id').val()  );
			this.setData( 'Itemid', Itemid );
			this.setData( 'requestType', 'only' );		
			
			//Scan elements approved context menu
			this.contextElements = new Array( $( '.'+this.classApprovedContextMenu ).length );
			
			var $this = this;
			$( '.'+this.classApprovedContextMenu ).each(function(){
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
			this.setData( 'requestTask', task );
			this.ajaxRequest();
		};
		
		this.addContextMenu = function(){
			this.contextMenu = this.getContextMenu();
			var $this = this;			
			if ( this.contextMenu != null ){
				
				if ( $this.contextMenu.isNew() ){
										
					/**
					 * 
					 * Add menu for editing the category
					 */
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.editCategory($this.currentElement.attr('category'));
					});
					
					/**
					 * 
					 * Menu for showing element
					 */
					
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setData( 'requestType', 'only' );
						$this.setParams( $this.currentElement.attr('parname'), 1);
						$this.beforeAjaxRequest('show');
					});
					
					$this.contextMenu.addItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE') ).addEventHandler("click", function(){
						$this.contextMenu.hide({});
						$this.setData( 'requestType', 'only' );
						$this.setParams( $this.currentElement.attr('parname'), 0);
						$this.beforeAjaxRequest('hide');
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
						$this.contextMenu.hideAllItems();
						var current = $this.contextElements[tagId];										
						if(current.attr('category')){
							$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_EDIT_CATEGORY'));
						}
						
						if (current.hasClass('element-switch')) {
							if ( current.data('show') ){
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_HIDE_ARTICLE'));
							}else{
								$this.contextMenu.showItem( JSNLang.translate('JSN_RAWMODE_COMPONENT_SHOW_ARTICLE'));
							}
						}
												
						// Build context menu position.
						$this.contextMenu.show({
							x : e.pageX,
							y : e.pageY
						});
						
						// IMPORTANT - trigger show event.
						$this.contextMenu.trigger('component.context.show');
					}else{
						$this.currentElement = $({});
					}
				});
			}
		}
		
		this.editCategory = function(catId){
			var $this = this;
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8;
			var pop = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_categories&task=category.edit&extension=com_content&id='+catId+'&tmpl=component',
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

							if (!$.fn.validateEmptyFields(iframe)) {
								return false;
							}
							
							if (pop.submitForm('category.apply', 'Save', function (){
									_this.removeClass('jsn-loading');
									$this.beforeAjaxRequest('brankNewData');
							} )){
								_this.addClass('jsn-loading');								
							}
						},
						'Close': function(){
							$(this).dialog("close");
						}
					}
				}
			);
		};
	}
})(JoomlaShine.jQuery);
