/**
* 
* Rawmode Drag&Drop
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 
 Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
		- rawmode.jsndraganddrop.js
		- menuitem.jquery.js
**/

(function($){
	/**
	* 
	* Rawmode
	* 
	* @param: (jQuery object) (ops)
	* @return: object
	*/	
	$.JSNModulesGrid = function()
	{
		var JSNGrid = this;
		JSNGrid.filterInput = '#module_spotlight_filter';
		JSNGrid.acceptedFilterLength = 3;		
		JSNGrid.UILayout;
		JSNGrid.resizeRate;
		JSNGrid.panels = {
			panelFull   : $('#jsn-rawmode-layout'),
			panelWest   : $('#jsn-rawmode-leftcolumn'),
			panelEast   : $('#jsn-rawmode-rightcolumn'),
			panelCenter : $('#jsn-rawmode-center'),
			cookie : {
				setSize : function( panelName, sizeValue ){
					if ( panelName == 'east' ){
						$.jStorage.set("rawmode_east_layout_resize", sizeValue );
					}else if( panelName == 'west' ){
						$.jStorage.set("rawmode_west_layout_resize", sizeValue );
					}
				},
				getSize: function( panelName ){
					if ( panelName == 'east' ){
						return parseInt( $.jStorage.get("rawmode_east_layout_resize") );
					}else if( panelName == 'west' ){
						return parseInt( $.jStorage.get("rawmode_west_layout_resize") );
					}
				},
				setParam : function(name, value){
					$.jStorage.set("panels_"+name, value );
				},
				getParam : function(name){
					return $.jStorage.get("panels_"+name);
				}
			}
		};
		JSNGrid.publishing = {
			elementMode						: $('#module-manager'),
			elementToolbar					: $('#module-show-options'),
			elementShowUnpublishModules		: $('#show_unpublished_modules'),
			elementShowUnpublishPositions	: $('#show_unpublished_positions'),
			cookie : {
				enableUnpublished : function(){
					$.jStorage.set('rawmode_unpublished_enabled', true);
					JSNGrid.publishing.elementMode.addClass('btn-success');
					JSNGrid.publishing.elementMode.removeClass('btn-disabled').addClass('btn-enabled').attr('title', JSNLang.translate('TITLE_HIDE_UNPUBLISHED_MODULES_POSITIONS'));
				},
				disableUnpublished : function(){
					$.jStorage.set('rawmode_unpublished_enabled', false);
					JSNGrid.publishing.elementMode.removeClass('btn-success');
					JSNGrid.publishing.elementMode.removeClass('btn-enabled').addClass('btn-disabled').attr('title', JSNLang.translate('TITLE_SHOW_UNPUBLISHED_MODULES_POSITIONS'));
				},
				isEnableUnpublished : function(){
					return $.jStorage.get('rawmode_unpublished_enabled');
				},
				enableShowUnpublishedModules : function(){
					$.jStorage.set('rawmode_show_all_modules', true);
				},
				disableShowUnpublishedModules : function(){
					$.jStorage.set('rawmode_show_all_modules', false);
				},
				isEnableShowUnpublishedModules : function(){
					return $.jStorage.get('rawmode_show_all_modules', true);
				},
				enableShowUnpublishedPositions : function(){
					$.jStorage.set('rawmode_show_all_positions', true);
				},
				disableShowUnpublishedPositions : function(){
					$.jStorage.set('rawmode_show_all_positions', false);
				},
				isEnableShowUnpublishedPositions : function(){
					return $.jStorage.get('rawmode_show_all_positions', true);
				}
			}
		};
		JSNGrid.grid = {
			elementArea			: $('#modules-list'),
			elementContainer	: $('#module-list-container'),
			modules : {
				getAll : function(){
					return $('.poweradmin-module-item');
				},
				getAllFromParent : function( obj ){
					return obj.find('div.poweradmin-module-item');
				},
				isPowerModule : function( obj ){
					var isPowerModule = false;
					isPowerModule = obj.hasClass( 'poweradmin-module-item' );
					if ( !isPowerModule ){
						isPowerModule = ( obj.parents('div.poweradmin-module-item').length > 0 ? true : false );
					}
					return isPowerModule;
				},
				hasPowerModule : function( obj ){
					return obj.find( '.poweradmin-module-item' ).length;
				},
				isChildrenOfElement : function( obj ){
					return obj.parents('div.poweradmin-module-item').length;
				}
			},
			multipleselect : {
				getAll : function(){
					return $('.jsn-module-multiple-select');
				},
				getTotal : function(){
					return $('.jsn-module-multiple-select').length;
				},
				select : function( obj ){
					obj.addClass('jsn-module-multiple-select');
				},
				deSelect: function( obj ){
					obj.removeClass('jsn-module-multiple-select');
				},
				deSelectAll : function(){
					JSNGrid.grid.multipleselect.getAll().removeClass('jsn-module-multiple-select');
				},
				hasSelected : function( obj ){
					return obj.hasClass('jsn-module-multiple-select');
				},
				hasChildSelected : function( obj ){
					return ( $('.jsn-module-multiple-select', obj ).length > 0 ? true : false );
				},
				hasItemMultipleSelect : function(){
					return ( $('.jsn-module-multiple-select').length > 1 ? true : false );
				}
			},
			unpublished : {
				disable : function( obj ){
					obj.removeClass('jsn-module-unpublish');
				},
				enable : function( obj ){
					obj.addClass('jsn-module-unpublish');
				},
				isEnable : function( obj ){
					return obj.hasClass('jsn-module-unpublish');
				},
				getAll : function(){
					return $('.jsn-module-unpublish');
				}
			},
			unassignment : {
				disable : function( obj ){
					obj.removeClass('jsn-module-unassignment');
				},
				enable : function( obj ){
					obj.addClass('jsn-module-unassignment');
				},
				isEnable : function( obj ){
					return obj.hasClass('jsn-module-unassignment');
				},
				getAll : function(){
					return $('.jsn-module-unassignment');
				}
			},
			imageStatus : {
				request : function( obj ){
					obj.showImgStatus();
				},
				error  : function( obj ){
					//obj.showImgStatus({'status':'error'});
					JSNGrid.grid.imageStatus.remove(obj);
				},
				success : function( obj ){
					//obj.showImgStatus({'status':'success'});
					JSNGrid.grid.imageStatus.remove(obj);
				},
				remove : function( obj ){
					obj.showImgStatus("remove");
				}
			},
			checkin : function (obj) {				
				obj.find('.checked-out').remove();
			}
		};
		JSNGrid.changePosition = {
			cookie : {
				setMode : function( modeValue ){
					$.jStorage.set("RAWMODE_CHANGEPOSITION_MODE", modeValue);
				},
				isTextMode : function(){
					return $.jStorage.get("RAWMODE_CHANGEPOSITION_MODE", "textmode") == "textmode";
				},
				isVisualMode : function(){
					return $.jStorage.get("RAWMODE_CHANGEPOSITION_MODE", "visualmode") == "visualmode";
				}
			}	
		};

		/**
		*
		* Init events
		* 
		* @return: Init event on HTML elements
		*/
		JSNGrid.initEvents = function(){
			/**
			 * Click to show/hide module options
			 */
			JSNGrid.publishing.elementMode.unbind("click").click(function(){
				if ( !$(this).hasClass('btn-enabled') ){					
					JSNGrid.publishing.cookie.enableUnpublished();
					JSNGrid.grid.elementArea.css('height', 'auto');
					
					// Default show unpublished module
					if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
						JSNGrid.publishing.elementShowUnpublishModules.attr("checked", true);
						JSNGrid.publishing.cookie.enableShowUnpublishedModules();
					}
					// Default show inactive position
					if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() ){
						JSNGrid.publishing.elementShowUnpublishPositions.attr("checked", true);
						JSNGrid.publishing.cookie.enableShowUnpublishedPositions();
					}
					JSNGrid.showModules();
					if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() ){
						JSNGrid.showAllPositions();
					}else{
						JSNGrid.hideEmptyPositions();
					}
					
					JSNGrid.publishing.elementToolbar.slideDown(500, function(){
						JSNGrid.moduleAreaResize();
					});
				}else{
					JSNGrid.publishing.cookie.disableUnpublished();
					JSNGrid.grid.elementArea.css('height', 'auto');
					JSNGrid.hideAllUnpublished();
					JSNGrid.publishing.elementToolbar.slideUp(500, function(){
						JSNGrid.moduleAreaResize();
					});
				}
				
				//redo filter when showing all positions or modules
				var filterVal = $(JSNGrid.filterInput).attr('value');
				var isFiltering = false;
				if( filterVal.length >= JSNGrid.acceptedFilterLength){					
					JSNFilter.doFilter(filterVal);
				}				
			});
			
			/**
			 * Click to show/hide unpublished module items
			 */
			JSNGrid.publishing.elementShowUnpublishModules.unbind("change").change(function(){
				if ( $(this)[0].checked ){
					JSNGrid.publishing.cookie.enableShowUnpublishedModules();
					JSNGrid.showModules();
					JSNGrid.moduleAreaResize();
				}else{
					JSNGrid.publishing.cookie.disableShowUnpublishedModules();
					JSNGrid.showModules();
					JSNGrid.moduleAreaResize();
				}
			});
			/**
			 * Click to show/hide inactive positions
			 */
			JSNGrid.publishing.elementShowUnpublishPositions.unbind("change").change(function(){
				if ($(this)[0].checked){
					JSNGrid.publishing.cookie.enableShowUnpublishedPositions();
					JSNGrid.showAllPositions();
					if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
						JSNGrid.showModules();
					}
					JSNGrid.moduleAreaResize();
				}else{
					JSNGrid.publishing.cookie.disableShowUnpublishedPositions();
					JSNGrid.hideEmptyPositions();
					JSNGrid.moduleAreaResize();
				}
			});
			
			//Restore show/hide module options
			if ( JSNGrid.publishing.cookie.isEnableUnpublished() ){
				//Restore show unpublished module
				if (JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
					JSNGrid.publishing.elementShowUnpublishModules.attr("checked", true);
				}
				JSNGrid.showModules();
				// Restore show inactive position
				if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() ){
					JSNGrid.publishing.elementShowUnpublishPositions.attr("checked", true);
					JSNGrid.showAllPositions();
				}
	
				JSNGrid.publishing.cookie.enableUnpublished();
				JSNGrid.publishing.elementToolbar.slideDown(500, function(){
					JSNGrid.moduleAreaResize();
				});
			}else{
				JSNGrid.publishing.cookie.disableUnpublished();
				JSNGrid.publishing.elementToolbar.slideUp(500, function(){
					JSNGrid.moduleAreaResize();
				});
				JSNGrid.hideAllUnpublished();
			}
			
			JSNGrid.grid.modules.getAll().unbind("mousedown").mousedown(function(e){
				if ( e.which == 1 ){
					if ( e.ctrlKey ){
						if ( JSNGrid.grid.multipleselect.hasSelected( $(this) )){
							JSNGrid.grid.multipleselect.deSelect( $(this) );
						}else{
							JSNGrid.grid.multipleselect.select( $(this) );
						}
					}
				}
			});
		};
		/**
		*
		* Build module context menu
		*
		* @param: (array) (objs) is array elements
		* @param: (boolean) (rebuild) is value to rebuild/not rebuild context-menu
		* @return: build menu
		*/
		JSNGrid.buildModulesContextMenu = function(objs, rebuild){
			rebuild = ( rebuild == undefined )? true : false;
			var moduleMenuReference = $('#module-context').subMenuReferences({rebuild:rebuild, rightClick:false, attrs:{'class': 'rawmode-subpanel'}});
			var submenu  = moduleMenuReference.getMenu();
				if ( submenu.isNew() ){
						/**
						 * Click to Edit module. Open popup page
						 */
						var editItem = submenu.addItem( JSNLang.translate('TITLE_SUBMENU_EDIT'), {'class':'bold-item'} ).addEventHandler("click", function(){
							submenu.hide({});
							JSNGrid.editModule( submenu.getRoot() );
						});
						
						submenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_CHECKIN' )).addEventHandler("click", function(){							
							submenu.hide({});
							var elementid = submenu.getRootAttr('id');
							var moduleid  = [elementid.split('-')[0]];
							
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=module.checkin&lang='+lang,
								{
									moduleid: moduleid
								}
							).success(function( response ){								
								JSNGrid.grid.checkin( submenu.getRoot() );
								JSNGrid.grid.imageStatus.success( submenu.getRoot() );
							}).error(function(msg){
								JSNGrid.grid.imageStatus.error( submenu.getRoot() );
							});
						}).proxyStore("multipleselect", function(){							
							submenu.hide({});
							var moduleid = new Array(), i = 0;
							var currpos   = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
							var elementid = submenu.getRootAttr('id');
							var currmid   = [elementid.split('-')[0]];
							JSNGrid.grid.multipleselect.getAll().each(function(){
								moduleid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=module.checkin&lang='+lang,
								{
									moduleid: moduleid
								}
							).success(function( response ){
								$.checkResponse( response );
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.imageStatus.success( $(this) );
									JSNGrid.grid.checkin( $(this) );
								});
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
							
						});
						
						submenu.hideItem(JSNLang.translate( 'TITLE_SUBMENU_CHECKIN' ));
						/**
						* Click to show page change the position. Open popup page
						*/
						submenu.addItem( JSNLang.translate( 'TITLE_CHANGE_POSITION' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var mid = [submenu.getRootAttr('id').split('-')[0]];
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							if ( JSNGrid.changePosition.cookie.isTextMode() && false){
								var visualmodeDefault = true;
								var textmodeDefault   = false;
								var wWidth  = 750;
								var wHeight = 550;
								var modeUrl = baseUrl+'administrator/index.php?option=com_poweradmin&task=changeposition.selectPosition&redirect_mode=texmode&moduleid='+mid;
							}else{
								var visualmodeDefault = false;
								var textmodeDefault   = true;
								var wWidth  = $(window).width()*0.85;
								var wHeight = $(window).height()*0.8;
								var modeUrl = baseUrl+'administrator/index.php?option=com_poweradmin&task=changeposition.selectPosition&redirect_mode=visualmode&moduleid='+mid;
							}

							var pop = $.JSNUIWindow
							(
								modeUrl,
								{
									modal : true,
									title : JSNLang.translate( 'TITLE_PAGE_CHANGE_POSITION' ),
									width : wWidth,
									height: wHeight,
									open  : function(){
										var _this = $(this);
										var iframe = $(this).find('iframe');
										iframe.load(function(){
											if (!visualmodeDefault){
												iframe.contents().find('div.jsn-position').click(function(){
													if ( !$(this).hasClass('active-position') ){
														setTimeout(function(){
															_this.dialog("close");
														}, 800);
													}
												});
											}else{
												iframe.contents().find('div.position-selector').click(function(){
													setTimeout(function(){
														_this.dialog("close");
													}, 800);
												});
											}
										});
									},
									buttons: {
										'Close': function(){
											$(this).dialog("close");
										}
									},
									close: function(){
										JSNGrid.grid.imageStatus.remove( submenu.getRoot() );
									},
									search: {
										text    : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' ),
										classSet: 'ui-window-searchbar',
										onChange: function(){
											var iframe = pop.getIframe();
											iframe[0].contentWindow.changeposition.filterResults($(this).val().trim());
										},
										onKeyup : function(){
											//fire change event
											$(this).change();
										},
										onBlur  : function(){
											if ($(this).val().trim() == ''){
												$(this).val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' ) );
												$(this).css('color', '#CCCCCC');
											}
										},
										onFocus : function(){
											if ($(this).val().trim() == JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' )){
												$(this).css('color', '#000').val('');
											}
										},
										closeTextKeyword : true,
										afterAddTextCloseSearch : function(obj){
											//obj.css({'margin-right': wWidth/2-100 +'px'});
										},
										defaultText      : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ),
										closeTextClick   : function( obj, searchbox ){
											obj.hide();
											var iframe = pop.getIframe();
											iframe[0].contentWindow.changeposition.filterResults('');
											searchbox.css('color', '#CCCCCC').val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ) );
										}
									}
									/*,switchMode : {
										modes :
										[
										    {
												name   : JSNLang.translate('JSN_CHANGE_POSITION_MODE_TEXTMODE'),
												action : function(event, obj, popup){
													JSNGrid.changePosition.cookie.setMode( "textmode" );
													popup.resize({
														width    : 750, 
														height   : 550,
														complete : function(){
															popup.changeIframeSrc(baseUrl+'administrator/index.php?option=com_poweradmin&task=changeposition.selectPosition&redirect_mode=texmode&moduleid='+mid);
															var iframe = popup.getIframe();
															iframe.load(function(){
																iframe.contents().find('div.position-selector').click(function(){
																	setTimeout(function(){
																		popup.close();
																	}, 800);
																});
															});
														}
													});
												},
												defaultMode : textmodeDefault
											},
											{
												name   : JSNLang.translate('JSN_CHANGE_POSITION_MODE_VISUALMODE'),
												action : function(event, obj, popup){
													JSNGrid.changePosition.cookie.setMode( "visualmode" );
													var wWidth  = $(window).width()*90/100;
													var wHeight = $(window).height()*90/100;
													popup.resize({
														width    : wWidth, 
														height   : wHeight, 
														complete : function(){
															popup.changeIframeSrc(baseUrl+'administrator/index.php?option=com_poweradmin&&task=changeposition.selectPosition&redirect_mode=visualmode&moduleid='+mid);
															var iframe = popup.getIframe();
															iframe.load(function(){
																iframe.contents().find('div.jsn-poweradmin-position').click(function(){
																	setTimeout(function(){
																		popup.close();
																	}, 800);
																});
															});
														}
													});
												},
												defaultMode : visualmodeDefault
											}
										]
									}*/
								}
							);
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var mid = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								mid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							if ( JSNGrid.changePosition.cookie.isTextMode() && false){
								var visualmodeDefault = true;
								var textmodeDefault   = false;
								var wWidth  = 750;
								var wHeight = 550;
								var modeUrl = baseUrl+'administrator/index.php?option=com_poweradmin&task=changeposition.selectPosition&redirect_mode=texmode&moduleid='+mid;
							}else{
								var visualmodeDefault = false;
								var textmodeDefault   = true;
								var wWidth  = $(window).width()*0.85;
								var wHeight = $(window).height()*0.8;
								var modeUrl = baseUrl+'administrator/index.php?option=com_poweradmin&&task=changeposition.selectPosition&redirect_mode=visualmode&moduleid='+mid;
							}

							var pop = $.JSNUIWindow
							(
								modeUrl,
								{
									modal : true,
									title : JSNLang.translate( 'TITLE_PAGE_CHANGE_POSITION' ),
									width : wWidth,
									height: wHeight,
									open  : function(){
										var _this = $(this);
										var iframe = $(this).find('iframe');
										iframe.load(function(){
											if (!visualmodeDefault){
												iframe.contents().find('div.jsn-poweradmin-position').click(function(){
													if ( !$(this).hasClass('active-position') ){
														setTimeout(function(){
															_this.dialog("close");
														}, 800);
													}
												});
											}else{
												iframe.contents().find('div.position-selector').click(function(){
													setTimeout(function(){
														_this.dialog("close");
													}, 800);
												});
											}
										});
									},
									buttons: {
										'Close': function(){
											$(this).dialog("close");
										}
									},
									close: function(){
										JSNGrid.grid.multipleselect.getAll().each(function(){
											JSNGrid.grid.imageStatus.remove( $(this) );
										});
									},
									search: {
										text    : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' ),
										classSet: 'ui-window-searchbar',
										onChange: function(){
											var iframe = pop.getIframe();
											iframe[0].contentWindow.changeposition.filterResults($(this).val().trim());
										},
										onKeyup : function(){
											//fire change event
											$(this).change();
										},
										onBlur  : function(){
											if ($(this).val().trim() == ''){
												$(this).val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' ) );
												$(this).css('color', '#CCCCCC');
											}
										},
										onFocus : function(){
											if ($(this).val().trim() == JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' )){
												$(this).css('color', '#000').val('');
											}
										},
										closeTextKeyword : true,
										closeTextKeywordCss : {
											'margin-right' : wWidth/2-100 +'px'
										},
										defaultText      : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ),
										closeTextClick   : function( obj, searchbox ){
											obj.hide();
											var iframe = pop.getIframe();
											iframe[0].contentWindow.changeposition.filterResults('');
											searchbox.css('color', '#CCCCCC').val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ) );
										}
									}
									/*,switchMode : {
										modes :
										[
										    {
												name   : JSNLang.translate('JSN_CHANGE_POSITION_MODE_TEXTMODE'),
												action : function(event, obj, popup){
													JSNGrid.changePosition.cookie.setMode( "textmode" );
													popup.resize({
														width    : 750, 
														height   : 550,
														complete : function(){
															popup.changeIframeSrc(baseUrl+'administrator/index.php?option=com_poweradmin&task=changeposition.selectPosition&redirect_mode=texmode&moduleid='+mid);
															var iframe = popup.getIframe();
															iframe.load(function(){
																iframe.contents().find('div.position-selector').click(function(){
																	setTimeout(function(){
																		popup.close();
																	}, 800);
																});
															});
														}
													});
												},
												defaultMode : textmodeDefault  
											},
											{
												name   : JSNLang.translate('JSN_CHANGE_POSITION_MODE_VISUALMODE'),
												action : function(event, obj, popup){
													JSNGrid.changePosition.cookie.setMode( "visualmode" );
													var wWidth  = $(window).width()*90/100;
													var wHeight = $(window).height()*90/100;
													popup.resize({
														width    : wWidth, 
														height   : wHeight, 
														complete : function(){
															popup.changeIframeSrc(baseUrl+'administrator/index.php?option=com_poweradmin&&task=changeposition.selectPosition&redirect_mode=visualmode&moduleid='+mid);
															var iframe = popup.getIframe();
															iframe.load(function(){
																iframe.contents().find('div.jsn-poweradmin-position').click(function(){
																	setTimeout(function(){
																		popup.close();
																	}, 800);
																});
															});
														}
													});
												},
												defaultMode : visualmodeDefault
											}
										]
									}*/
								}
							);
						});
						/**
						* Click to unpublish/publish module item. Open popup page
						*/
						submenu.addItem( JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var position  = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
							var elementid = submenu.getRootAttr('id');
							var moduleid  = [elementid.split('-')[0]];
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							if ( positions[position].modules[moduleid].unpublish ){
								$.post
								(
									baseUrl+'administrator/index.php?option=com_poweradmin&task=module.publish&lang='+lang, 
									{
										moduleid: moduleid

									}
								).success(function( response ){
									$.checkResponse( response );
									var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
									JSNGrid.grid.unpublished.disable( submenu.getRoot() );
									positions[position].modules[moduleid].unpublish = false;
									JSNGrid.toActivePosition(position);
									JSNGrid.grid.imageStatus.success( submenu.getRoot() );
								}).error(function(msg){
									JSNGrid.grid.imageStatus.error( submenu.getRoot() );
								});

							}else{
								$.post
								(
									baseUrl+'administrator/index.php?option=com_poweradmin&task=module.unpublish&lang='+lang, 
									{
										moduleid: moduleid

									}
								).success(function( response ){
									$.checkResponse( response );
									var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
									JSNGrid.grid.unpublished.enable( submenu.getRoot() );
									positions[position].modules[moduleid].unpublish = true;
									JSNGrid.toInactivePosition(position);
									
									if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedModules()  || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
										$(positions[position].modules[moduleid].element_id).hide();
									}
									
									if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
										JSNGrid.hideEmptyPositions();
									}
									JSNGrid.grid.imageStatus.success( submenu.getRoot() );
								}).error(function(msg){
									console.log(msg);
									JSNGrid.grid.imageStatus.error( submenu.getRoot() );
								});
							}
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var moduleid = new Array(), i = 0;
							var currpos   = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
							var elementid = submenu.getRootAttr('id');
							var currmid   = [elementid.split('-')[0]];
							JSNGrid.grid.multipleselect.getAll().each(function(){
								moduleid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							if ( positions[currpos].modules[currmid].unpublish ){
								$.post
								(
									baseUrl+'administrator/index.php?option=com_poweradmin&task=module.publish&lang='+lang, 
									{
										moduleid: moduleid

									}
								).success(function( response ){
									$.checkResponse( response );
									JSNGrid.grid.multipleselect.getAll().each(function(){
										JSNGrid.grid.imageStatus.success( $(this) );
										JSNGrid.grid.multipleselect.deSelect( $(this) );
										JSNGrid.grid.unpublished.disable( $(this) );
										var position = $(this).parent().attr('id').replace('-jsnposition', '');
										var moduleid = $(this).attr('id').split('-')[0];
										positions[position].modules[moduleid].unpublish = false;
										JSNGrid.toActivePosition(position);
									});
								}).error(function(msg){
									console.log(msg);
									JSNGrid.grid.multipleselect.getAll().each(function(){
										JSNGrid.grid.multipleselect.deSelect( $(this) );
										JSNGrid.grid.imageStatus.error( $(this) );
									});
								});

							}else{
								$.post
								(
									baseUrl+'administrator/index.php?option=com_poweradmin&task=module.unpublish&lang='+lang, 
									{
										moduleid: moduleid

									}
								).success(function( response ){
									$.checkResponse( response );
									JSNGrid.grid.multipleselect.getAll().each(function(){
										JSNGrid.grid.imageStatus.success( $(this) );
										JSNGrid.grid.multipleselect.deSelect( $(this) );
										JSNGrid.grid.unpublished.enable( $(this) );
										var position = $(this).parent().attr('id').replace('-jsnposition', '');
										var moduleid = $(this).attr('id').split('-')[0];
										positions[position].modules[moduleid].unpublish = true;
										JSNGrid.toInactivePosition(position);
										if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
											$(positions[position].modules[moduleid].element_id).hide();
										}
									});

									if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
										JSNGrid.hideEmptyPositions();
									}
									
								}).error(function(msg){
									console.log(msg);
									JSNGrid.grid.multipleselect.getAll().each(function(){
										JSNGrid.grid.multipleselect.deSelect( $(this) );
										JSNGrid.grid.imageStatus.error( $(this) );
									});
								});
							}
						});
						/**
						* Assign subpanel
						*/
						var assignSubPanel = submenu.addParentItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN' ) );
						
						/**
						* Assign to this page, ajax request
						*/
						assignSubPanel.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_ONLY_TO_THIS_PAGE' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var  moduleid = [submenu.getRootAttr('id').split('-')[0]];
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.assign&lang='+lang,
								{
									publish_area: 'one',
									moduleid    : moduleid,
									assignpages : [$('.jstree-clicked').parent().attr('id').split('-')[2]]
								}
							).success(function(response){
								$.checkResponse( response );
								var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								positions[position].modules[moduleid].assignment = 'selected';
								var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								JSNGrid.grid.unassignment.disable( submenu.getRoot() );
								JSNGrid.toActivePosition(position);
								JSNGrid.grid.imageStatus.success( submenu.getRoot() );
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.imageStatus.error( submenu.getRoot() );
							});
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var moduleid = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								moduleid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.assign&lang='+lang,
								{
									publish_area: 'one',
									moduleid    : moduleid,
									assignpages : [$('.jstree-clicked').parent().attr('id').split('-')[2]]
								}
							).success(function(response){
								$.checkResponse( response );
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.imageStatus.success( $(this) );
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.unassignment.disable( $(this) );
									var position = $(this).parent().attr('id').replace('-jsnposition', '');
									var moduleid = $(this).attr('id').split('-')[0];
									positions[position].modules[moduleid].assignment = 'selected';
									JSNGrid.toActivePosition(position);
								});
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
						});
						/**
						* Assign to all pages, ajax request.
						*/
						assignSubPanel.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_TO_ALL_PAGES' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var  moduleid = [submenu.getRootAttr('id').split('-')[0]];
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.assign&lang='+lang,
								{
									publish_area: 'all',
									moduleid    : moduleid
								}
							).success(function( response ){
								$.checkResponse( response );
								var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								positions[position].modules[moduleid].assignment = 'all';
								JSNGrid.grid.unassignment.disable( submenu.getRoot() );
								JSNGrid.toActivePosition(position);
								JSNGrid.grid.imageStatus.success( submenu.getRoot() );
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.imageStatus.error( submenu.getRoot() );
							});
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var moduleid = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								moduleid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.assign&lang='+lang,
								{
									publish_area: 'all',
									moduleid    : moduleid,
									assignpages : [$('.jstree-clicked').parent().attr('id').split('-')[2]]
								}
							).success(function(response){
								$.checkResponse( response );
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.imageStatus.success( $(this) );
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.unassignment.disable( $(this) );
									var position = $(this).parent().attr('id').replace('-jsnposition', '');
									var moduleid = $(this).attr('id').split('-')[0];
									positions[position].modules[moduleid].assignment = 'all';
									JSNGrid.toActivePosition(position);
								});
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
						});
						
						assignSubPanel.addDivider();
						/**
						* To custom page, ajax request
						*/
						assignSubPanel.addItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_TO_ONLY_TO_CUSTOM_PAGE' ) ).addEventHandler("click", function(){
							submenu.hide({});
							if ( JSNGrid.grid.multipleselect.getAll().length > 1){
								var mid = new Array(), i = 0;
								JSNGrid.grid.multipleselect.getAll().each(function(){
									mid[i++] = $(this).attr('id').split('-')[0];
								});
							}else{
								var mid = [submenu.getRootAttr('id').split('-')[0]];
								var position    = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								var fromElement = submenu.getRoot();
								JSNGrid.grid.imageStatus.request( fromElement );
							}
							
							var pop = $.JSNUIWindow
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.customPage&tmpl=component&moduleid='+mid,
								{
									modal  : true,
									title  : JSNLang.translate( 'TITLE_CUSTOM_PAGE_ASSIGN' ),
									width  : 500,
									height : 500,
									scrollContent: false,
									close: function(){
										JSNGrid.grid.imageStatus.remove( fromElement );
									},
									buttons: {										
										'Save': function(){
											if (pop.submitForm('assignpages.save', 'Save')){
												var _this  = $(this);
												var iframe = $(this).find('iframe');
												iframe.load(function(){
													JSNGrid.grid.imageStatus.success( fromElement );
													JSNGrid.loadModuleByPosition( position );
												});
											}
										},
										'Close': function(){
											JSNGrid.grid.imageStatus.remove( fromElement );
											$(this).dialog("close");
										}
									}
								}
							);
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var modules = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								modules[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							var pop = $.JSNUIWindow
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.customPage&tmpl=component&moduleid='+modules,
								{
									modal  : true,
									title  : JSNLang.translate( 'TITLE_CUSTOM_PAGE_ASSIGN' ),
									width  : 500,
									height : 500,
									scrollContent: false,
									buttons: {
										'Save' : function(){
											if (pop.submitForm('assignpages.save')){
												var _this  = $(this);
												var iframe = $(this).find('iframe');
												iframe.load(function(){
													var poss = new Array();
													JSNGrid.grid.multipleselect.getAll().each(function(){
														JSNGrid.grid.multipleselect.deSelect( $(this) );
														JSNGrid.grid.imageStatus.success( $(this) );
														JSNGrid.grid.imageStatus.request( $(this) );
														var pos = $(this).parent().attr('id').replace('-jsnposition', '');
														poss[pos+'jsn'] = pos;
														
													});
													for(k in poss){
														if (/jsn/.test(k)){
															JSNGrid.loadModuleByPosition(poss[k]);
														}
													}
												});
											}
										},
										// 'Save & Close': function(){
										// 	if (pop.submitForm('assignpages.save', 'Save & Close')){
										// 		var _this  = $(this);
										// 		var iframe = $(this).find('iframe');
										// 		iframe.load(function(){
										// 			var poss = new Array();
										// 			JSNGrid.grid.multipleselect.getAll().each(function(){
										// 				JSNGrid.grid.multipleselect.deSelect( $(this) );
										// 				JSNGrid.grid.imageStatus.success( $(this) );
										// 				var pos = $(this).parent().attr('id').replace('-jsnposition', '');
										// 				poss[pos+'jsn'] = pos;
														
										// 			});
										// 			for(k in poss){
										// 				if (/jsn/.test(k)){
										// 					JSNGrid.loadModuleByPosition(poss[k]);
										// 				}
										// 			}
										// 		});
										// 	}
										// },
										'Close': function(){
											JSNGrid.grid.multipleselect.getAll().each(function(){
												JSNGrid.grid.multipleselect.deSelect( $(this) );
												JSNGrid.grid.imageStatus.error( $(this) );
											});
											$(this).dialog("close");
										}
									}
								}
							);
						});
						/**
						* Unassign subpanel 
						*/
						var unassignSubpanel = submenu.addParentItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN' ) );
						
						/**
						* Unassign to this page
						*/
						unassignSubpanel.addItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_FROM_THIS_PAGE_ONLY' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var  moduleid   = submenu.getRootAttr('id').split('-')[0];
							var position    = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
							var fromElement = submenu.getRoot();
							JSNGrid.grid.imageStatus.request( fromElement );
							$.post( 
								baseUrl+'administrator/index.php?option=com_poweradmin&&task=assignpages.unassign&lang='+lang, 
								{
									moduleid		: moduleid,
									assignpages		: [$('.jstree-clicked').parent().attr('id').split('-')[2]],
									unpublish_area	: 'one'
								}
							).success(function( response ){
								$.checkResponse( response );
								JSNGrid.grid.unassignment.enable( fromElement );
								positions[position].modules[moduleid].assignment = 'except';
								
								if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() || !JSNGrid.publishing.cookie.isEnableUnpublished()){
									$(positions[position].modules[moduleid].element_id).hide();
								}
								JSNGrid.toInactivePosition(position);
								if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions()){
									JSNGrid.hideEmptyPositions();
								}
								JSNGrid.grid.imageStatus.success( fromElement );
							}).error(function(msg){
								JSNGrid.grid.imageStatus.error( fromElement );
							});
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var moduleid = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								moduleid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.unassign&lang='+lang,
								{
									unpublish_area	: 'one',
									moduleid		: moduleid,
									assignpages		: [$('.jstree-clicked').parent().attr('id').split('-')[2]]
								}
							).success(function(response){
								$.checkResponse( response );
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.imageStatus.success( $(this) );
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.unassignment.enable( $(this) );
									var position = $(this).parent().attr('id').replace('-jsnposition', '');
									var moduleid = $(this).attr('id').split('-')[0];
									positions[position].modules[moduleid].assignment = 'except';
									if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedModules()){
										$(positions[position].modules[moduleid].element_id).hide();
									}
									JSNGrid.toInactivePosition(position);
									if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions()){
										JSNGrid.hideEmptyPositions();
									}
								});
							}).error(function(msg){
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
						});

						/**
						* Unassign from all page
						*/
						unassignSubpanel.addItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_FROM_ALL_PAGES' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var  moduleid   = submenu.getRootAttr('id').split('-')[0];
							var position    = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
							var fromElement = submenu.getRoot();
							JSNGrid.grid.imageStatus.request( fromElement );
							$.post(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.unassign&lang='+lang, 
								{
									moduleid		: moduleid,
									unpublish_area	: 'all'
								}
							).success(function( response ){
								$.checkResponse( response );
								JSNGrid.grid.unassignment.enable( fromElement );
								positions[position].modules[moduleid].assignment = '';
								JSNGrid.toInactivePosition(position);
								if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedModules()){
									$(positions[position].modules[moduleid].element_id).hide();
								}
								if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() ){
									JSNGrid.hideEmptyPositions();
								}
								JSNGrid.grid.imageStatus.success( fromElement );
							}).error(function(msg){
								JSNGrid.grid.imageStatus.error( fromElement );
							});
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var moduleid = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								moduleid[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.unassign&lang='+lang,
								{
									unpublish_area	: 'all',
									moduleid		: moduleid,
									assignpages		: [$('.jstree-clicked').parent().attr('id').split('-')[2]]
								}
							).success(function(response){
								$.checkResponse( response );
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.imageStatus.success( $(this) );
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.unassignment.enable( $(this) );
									var position = $(this).parent().attr('id').replace('-jsnposition', '');
									var moduleid = $(this).attr('id').split('-')[0];
									positions[position].modules[moduleid].assignment = '';
									JSNGrid.toInactivePosition(position);
									if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedModules()){
										$(positions[position].modules[moduleid].element_id).hide();
									}
									if (!JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions()){
										JSNGrid.hideEmptyPositions();
									}
								});
								
							}).error(function(msg){
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
						});
						
						unassignSubpanel.addDivider();
						
						/**
						* To custom page
						*/
						unassignSubpanel.addItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_CUSTOM_PAGES' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var mid = [submenu.getRootAttr('id').split('-')[0]];
							var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
							var fromElement = submenu.getRoot();
							JSNGrid.grid.imageStatus.request( fromElement );
							var pop = $.JSNUIWindow
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.customPage&tmpl=component&moduleid='+mid,
								{
									modal  : true,
									title  : JSNLang.translate( 'TITLE_UNASSIGN_CUSTOM_PAGES' ),
									width  : 500,
									height : 500,
									scrollContent: false,
									close: function(){
										JSNGrid.grid.imageStatus.remove( submenu.getRoot() );
									},
									buttons: {										
										'Save': function(){
											if (pop.submitForm('assignpages.save', 'Save')){
												var _this  = $(this);
												var iframe = $(this).find('iframe');
												iframe.load(function(){
													JSNGrid.grid.imageStatus.success( fromElement );
													JSNGrid.loadModuleByPosition(position);
												});
											}
										},
										'Close': function(){
											JSNGrid.grid.imageStatus.remove( fromElement );
											$(this).dialog("close");
										}
									}
								}
							);
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var modules = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								modules[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							
							var pop = $.JSNUIWindow
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=assignpages.customPage&tmpl=component&moduleid='+modules,
								{
									modal  : true,
									title  : JSNLang.translate( 'TITLE_UNASSIGN_CUSTOM_PAGES' ),
									width  : 500,
									height : 500,
									scrollContent: false,
									buttons: {										
										'Save & Close': function(){
											if (pop.submitForm('assignpages.save', 'Save & Close')){
												var _this  = $(this);
												var iframe = $(this).find('iframe');
												iframe.load(function(){
													var poss = new Array();
													JSNGrid.grid.multipleselect.getAll().each(function(){
														JSNGrid.grid.multipleselect.deSelect( $(this) );
														JSNGrid.grid.imageStatus.success( $(this) );
														var pos = $(this).parent().attr('id').replace('-jsnposition', '');
														poss[pos+'jsn'] = pos;
														
													});
													for(k in poss){
														if (/jsn/.test(k)){
															JSNGrid.loadModuleByPosition(poss[k]);
														}
													}
												});
											}
										},
										'Cancel': function(){
											JSNGrid.grid.multipleselect.getAll().each(function(){
												JSNGrid.grid.multipleselect.deSelect( $(this) );
												JSNGrid.grid.imageStatus.error( $(this) );
											});
											$(this).dialog("close");
										}
									}
								}
							);
						});

						//add divider
						submenu.addDivider();

						/**
						* More 
						*/
						var more = submenu.addParentItem( JSNLang.translate( 'TITLE_SUBMENU_MORE' ) );

						/**
						* Duplicate an module
						*/
						more.addItem( JSNLang.translate( 'TITLE_SUBMENU_DUPLICATE' ) ).addEventHandler("click", function(){
							submenu.hide({});
							var mid = submenu.getRootAttr('id').split('-')[0];
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=module.duplicate',
								{
									cid: [mid]
								}
							).success(function(response){
								$.checkResponse(response);
								var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								JSNGrid.loadModuleByPosition(position);
								JSNGrid.grid.imageStatus.success( submenu.getRoot() );
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.imageStatus.error( submenu.getRoot() );
							});
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var modules = new Array(), i = 0;
							JSNGrid.grid.multipleselect.getAll().each(function(){
								modules[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=module.duplicate',
								{
									cid: modules
								}
							).success(function(response){
								$.checkResponse(response);
								var poss = new Array();
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.success( $(this) );
									var pos = $(this).parent().attr('id').replace('-jsnposition', '');
									poss[pos+'jsn'] = pos;
									
								});
								for(k in poss){
									if (/jsn/.test(k)){
										JSNGrid.loadModuleByPosition(poss[k]);
									}
								}
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
						});

						/**
						* Trash a module
						*/
						more.addItem( JSNLang.translate( 'TITLE_SUBMENU_TRASH' ) ).addEventHandler("click", function(){
							
							submenu.hide({});
							var mid = submenu.getRootAttr('id').split('-')[0];
							var clickedModuleTitle = $('.poweradmin-module-item-inner-text',submenu.getRoot()).text();
							var answer = confirm(JSNLang.translate( 'CONFIRM_DELETE_MODULE', {"JSN_TEXT1":clickedModuleTitle} ));
							if (!answer) {
								return;
							}
							JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=module.trash',
								{
									cid: [mid]
								}
							).success(function(response){
								$.checkResponse(response);
								var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								JSNGrid.grid.imageStatus.success( submenu.getRoot() );
								JSNGrid.loadModuleByPosition(position);
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.imageStatus.error( submenu.getRoot() );
							});
						}).proxyStore("multipleselect", function(){
							submenu.hide({});
							var modules = new Array(), i = 0;
							var answer = confirm(JSNLang.translate( 'CONFIRM_DELETE_MODULE_MULTIPLE'));
							if (!answer) {
								return;
							}
							JSNGrid.grid.multipleselect.getAll().each(function(){
								modules[i++] = $(this).attr('id').split('-')[0];
								JSNGrid.grid.imageStatus.request( $(this) );
							});							
							$.post
							(
								baseUrl+'administrator/index.php?option=com_poweradmin&task=module.trash',
								{
									cid: modules
								}
							).success(function(response){
								$.checkResponse(response);
								var poss = new Array();
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.success( $(this) );
									var pos = $(this).parent().attr('id').replace('-jsnposition', '');
									poss[pos+'jsn'] = pos;
									
								});
								for(k in poss){
									if (/jsn/.test(k)){
										JSNGrid.loadModuleByPosition(poss[k]);
									}
								}
							}).error(function(msg){
								console.log(msg);
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.multipleselect.deSelect( $(this) );
									JSNGrid.grid.imageStatus.error( $(this) );
								});
							});
						});
						
						/**
						* An custom edit option for module
						*/
						more.addItem( JSNLang.translate( 'TITLE_SUBMENU_OPTIONS' ) ).addEventHandler("click", function(){
							submenu.hide({});
							if ( JSNGrid.grid.multipleselect.getAll().length <= 1){
								JSNGrid.grid.imageStatus.request( submenu.getRoot() );
							}else{
								JSNGrid.grid.multipleselect.getAll().each(function(){
									JSNGrid.grid.imageStatus.request( $(this) );
								});
							}
							
							var pop = $.JSNUIWindow
							(
								baseUrl+'administrator/index.php?option=com_config&view=component&component=com_modules&path=&tmpl=component', 
								{
									modal : true,
									width : 750, 
									height: 550,
									title : JSNLang.translate( 'TITLE_MODULE_PAGE_OPTIONS' ),
									open  : function(){
										var iframe = $(this).find('iframe');
										iframe.load(function(){
											var head = iframe.contents().find('head');
											//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
											iframe.contents().find('form[name="adminForm"]').children('fieldset').hide();
											iframe.contents().find('#sidebar').hide();
										});
									},
									close: function(){
										JSNGrid.grid.multipleselect.getAll().each(function(){
											JSNGrid.grid.imageStatus.remove( $(this) );
										});
									},
									buttons: {										
										'Save': function(){
											var _this  = $(this);
											var iframe = $(this).find('iframe');

											if (pop.submitForm('component.apply', 'Save', function (){
													_this.removeClass('jsn-loading');

													if ( JSNGrid.grid.multipleselect.getAll().length <= 1 ){
														JSNGrid.grid.imageStatus.success( submenu.getRoot() );
													}else{
														JSNGrid.grid.multipleselect.getAll().each(function(){
															JSNGrid.grid.imageStatus.success( $(this) );
														});
													}
											})){
												_this.addClass('jsn-loading');												
											}
										},
										
										'Close': function(){
											if ( JSNGrid.grid.multipleselect.getAll().length <= 1){
												JSNGrid.grid.imageStatus.remove( submenu.getRoot() );
											}else{
												JSNGrid.grid.multipleselect.getAll().each(function(){
													JSNGrid.grid.imageStatus.remove( $(this) );
												});
											}
											$(this).dialog("close");
										}
									}
								}
							);
						});
					}

				if ( submenu != null){
					/**
					* Bind event mousedown
					*/
					objs.unbind("click").click(function(e){
						try{
							if ( !e.ctrlKey  ){
								moduleMenuReference.setReference($(this));
								submenu = moduleMenuReference.getMenu();
								submenu.showAllItems();
								var position = submenu.getParentRoot().attr('id').replace('-jsnposition', '');
								var moduleid = submenu.getRootAttr('id').split('-')[0];
								var assignPanel = submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN' ) );
								var unassignPanel = submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN' ) );
								var publishingItem = submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_PUBLISH' ) );
								if ( publishingItem.length <=0 ){
									publishingItem = submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH' ) );
								} 
																
								
								if(submenu.getRoot().find('.checked-out').length > 0) {
									submenu.showItem(JSNLang.translate('TITLE_SUBMENU_CHECKIN'));									
								}else{
									submenu.hideItem(JSNLang.translate('TITLE_SUBMENU_CHECKIN'));
								}
								
								assignPanel.removeClass('subpanel-disable');
								unassignPanel.removeClass('subpanel-disable');
								if ( positions[position].modules[moduleid].assignment == 'except' || positions[position].modules[moduleid].assignment == '' ){
									unassignPanel.addClass('subpanel-disable');
									unassignPanel.hide();
								}else{
									assignPanel.addClass('subpanel-disable');
									assignPanel.hide();
								}
								
								if ( positions[position].modules[moduleid].unpublish ){
									var publishingText = JSNLang.translate( 'TITLE_SUBMENU_PUBLISH' );
								}else{
									var publishingText = JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH' );
								}
								
								$('a', publishingItem).text( publishingText );

								if ( JSNGrid.grid.multipleselect.hasSelected( $(this) ) && JSNGrid.grid.multipleselect.hasChildSelected( JSNGrid.grid.elementContainer ) ){
									submenu.getItem( JSNLang.translate('TITLE_SUBMENU_EDIT') ).disableEventHandler("click").addClass('disabled').attr('title', JSNLang.translate("JSN_RAWMODE_EDIT_NOTALLOWED_SHOW_HINT"));		
									
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_CHECKIN' ) ).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_CHANGE_POSITION' ) ).changeProxy("click", "multipleselect");
									publishingItem.changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_ONLY_TO_THIS_PAGE' ), 0, assignPanel ).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_TO_ALL_PAGES' ) , 0, assignPanel).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_TO_ONLY_TO_CUSTOM_PAGE' ), 0, assignPanel ).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_FROM_THIS_PAGE_ONLY' ), 1,  unassignPanel).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_FROM_ALL_PAGES' ), 1, unassignPanel ).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_CUSTOM_PAGES' ), 1, unassignPanel ).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_DUPLICATE' ) ).changeProxy("click", "multipleselect");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_TRASH' ) ).changeProxy("click", "multipleselect");
									submenu.show
									(
										{
											x : $.jsnmouse.getX()+5, 
											y : $.jsnmouse.getY()+10
										}
									);
								}else {
									if(submenu.getRoot().find('.checked-out').length > 0) {
										submenu.getItem( JSNLang.translate('TITLE_SUBMENU_EDIT') ).disableEventHandler("click").addClass('disabled').attr('title', JSNLang.translate("JSN_RAWMODE_EDIT_CHECKEDOUT_SHOW_HINT"));	
									}else{
										submenu.getItem( JSNLang.translate('TITLE_SUBMENU_EDIT') ).enableEventHandler("click").removeClass('disabled').attr('title', '');										
									}
									submenu.getItem( JSNLang.translate( 'TITLE_CHANGE_POSITION' ) ).changeProxy("click", "click");
									publishingItem.changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_ONLY_TO_THIS_PAGE' ), 0, assignPanel ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_TO_ALL_PAGES' ), 0, assignPanel ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_ASSIGN_TO_ONLY_TO_CUSTOM_PAGE' ), 0, assignPanel ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_FROM_THIS_PAGE_ONLY' ), 1, unassignPanel ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_FROM_ALL_PAGES' ), 1, unassignPanel ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_UNASSIGN_CUSTOM_PAGES' ), 1, unassignPanel ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_DUPLICATE' ) ).changeProxy("click", "click");
									submenu.getItem( JSNLang.translate( 'TITLE_SUBMENU_TRASH' ) ).changeProxy("click", "click");
									submenu.show
									(
										{
											x : $.jsnmouse.getX()+5, 
											y : $.jsnmouse.getY()+10
										}
									);
								}
								
								$('body').unbind("mousedown").bind("mousedown", function(e){
									if ( !JSNGrid.grid.modules.isChildrenOfElement($(e.target)) && !$(e.target).parents('.jsnpw-submenu').length ){
										submenu.hideItem(JSNLang.translate('TITLE_SUBMENU_CHECKIN'));
										submenu.hide({});
									}
								});
							}else{
							//	submenu.hide({});
							}
						}catch(e){
							//throw e.message;
						}finally{
							return;
						}
					});
				}
				
		};
		/**
		 * Build context menu via position container
		 *
		 * @param: (array) (objs) is array elements
		 * @param: (number) (i) is index of item in array
		 * @return: build menu
		 */
		JSNGrid.buildPositionContextMenu = function(objs){
			var positionMenuReference = $('#position-context').subMenuReferences({rebuild:true, rightClick: false, attrs:{'class': 'rawmode-subpanel'}});
			var context  = positionMenuReference.getMenu();
			if (context.isNew() ){
				/**
				 * Add item to open view position page
				 */
				context.addItem( JSNLang.translate('JSN_RAWMODE_POSITION_CONTEXTMENU_VIEWPOSITIONS') ).addEventHandler("click", function(){ 
					context.hide({});
					var wWidth  = $(window).width()  * 0.85;
					var wHeight = $(window).height() * 0.8;
					var position_name = context.getRoot().children('.jsn-position-name').text();
					var pop = $.JSNUIWindow
					(
						baseUrl+'administrator/index.php?option=com_poweradmin&view=positionlisting&tmpl=component&positionname=' + position_name,
						{
							modal : true,
							title : JSNLang.translate( 'JSN_RAWMODE_VIEWPOSITIONS_PAGE_TITLE' ),
							width : wWidth,
							height: wHeight,
							buttons: {
								'Close': function(){
									$(this).dialog("close");
								}
							},
							search: {
								text    : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' ),
								classSet: 'ui-window-searchbar',
								onChange: function(){
									var iframe = pop.getIframe();
									iframe[0].contentWindow.changeposition.filterResults($(this).val().trim());
								},
								onKeyup : function(){
									//fire change event
									$(this).change();
								},
								onBlur  : function(){
									if ($(this).val().trim() == ''){
										$(this).val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' ) );
										$(this).css('color', '#CCCCCC');
									}
								},
								onFocus : function(){
									if ($(this).val().trim() == JSNLang.translate( 'DEFAULT_TEXT_SEARCH_CHANGE_POSITION' )){
										$(this).css('color', '#000').val('');
									}
								},
								closeTextKeyword : true,
								afterAddTextCloseSearch : function(obj){
									//obj.css({'margin-right': wWidth/2-100 +'px'});
								},
								defaultText      : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ),
								closeTextClick   : function( obj, searchbox ){
									obj.hide();
									var iframe = pop.getIframe();
									iframe[0].contentWindow.changeposition.filterResults('');
									searchbox.css('color', '#CCCCCC').val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ) );
								}
							}
						}
					);
				});
				context.addDivider();
				/**
				 * Add item to open create module page
				 */
				context.addItem( JSNLang.translate('JSN_RAWMODE_POSITION_CONTEXTMENU_ADDMODULE') ).addEventHandler("click", function(){
					context.hide({});
					var position = context.getRoot().find('div.jsn-poweradmin-position').attr('id').replace('-jsnposition', '');
					JSNGrid.selectModuleType(position);
				});
			}
			if (context != null){
				objs.unbind("mousedown").mousedown(function(e){
					positionMenuReference.setReference($(this));
					context = positionMenuReference.getMenu();
					try{
						if ( e.which === 1 && !e.ctrlKey   ){
							if ( ! JSNGrid.grid.modules.isPowerModule( $(e.target) ) && !$(this).hasClass('jsn-notdefault-element') ){
								context.show
								(
									{
										x : $.jsnmouse.getX()+5, 
										y : $.jsnmouse.getY()+10
									}
								);
								
								$('body').unbind("click").bind("click", function(e){
									if ( !$(e.target).parents('div.jsn-element-container').length ){
										context.hide({});
									}
								});
							}else{
								context.hide({});
							}
						}else{
							context.hide({});
						}
					}catch(e){
						//throw e.message;
					}finally{
						return;
					}
				});
			}
		};
		/**
		* 
		* Calculator rate size
		* 
		* @return: Calculator rate of width
		*/
		JSNGrid.calculatorRate = function(){
			var westWidth   = JSNGrid.panels.panelWest.innerWidth();
			var eastWidth   = JSNGrid.panels.panelEast.innerWidth();
			var fullWidth   = JSNGrid.panels.panelFull.outerWidth();
			resizeRate      = {west: westWidth*100/fullWidth, east: eastWidth*100/fullWidth};
		};
		/**
		* 
		* Init layout
		*
		* @return: None 
		*/
		JSNGrid.initLayout = function(){
			var heightSpace  = 0;
			var newHeight    = 0;
			var isFullScreen = $('body').hasClass('jsn-fullscreen');
			var template     = $('body').attr('data-template');

			if (isFullScreen === true) {
				var hasAdminBar = $('#jsn-body-wrapper').size() == 1;
				newHeight = $(window).height() - (hasAdminBar ? 115 : 90);

				if ($('body').hasClass('no-adminbar')) {
					newHeight = newHeight + 35;
				}
				
				newHeight =  newHeight - 30;
			}
			else {
				newHeight =  ($(window).height() - JSNGrid.panels.panelFull.offset().top) - 130;
				JSNGrid.panels.panelFull.css('height', newHeight);
			}

			JSNGrid.panels.panelFull.css('height', newHeight);
			$('body').css({
				'height': $(window).height(),
				'overflow': 'hidden'
			});
			
			JSNGrid.UILayout = JSNGrid.panels.panelFull.layout({
				west__onresize: function(){
					$._menuitems.layoutResize();
				},
				west__onopen: function(){
					JSNGrid.UILayout.state.east.maxSize -= JSNGrid.UILayout.state.west.size;
					JSNGrid.moduleAreaResize();
				},
				west__onclose: function(){
					JSNGrid.UILayout.state.east.maxSize += JSNGrid.UILayout.state.west.size;
					//JSNGrid.columnResize();
				},
				east__onresize: function(){
					JSNGrid.panels.cookie.setSize('east', JSNGrid.panels.panelEast.width() +  2);
					JSNGrid.moduleAreaResize();
				},
				center__onresize: function(){
					var component = $('#jsn-component-details');
					component.css('height', JSNGrid.panels.panelCenter.height() - 37);
					setTimeout(function(){
						$('#jsn-rawmode-layout').trigger('UILayout.resize.complete');
					}, 300);
				},
				onresizeall_end: function(){
					var fullWidth = JSNGrid.panels.panelFull.outerWidth();
					if (resizeRate.east !== undefined){
						var westWidth = resizeRate.west*fullWidth/100;
						var eastWidth = resizeRate.east*fullWidth/100;
						if (westWidth < 300) westWidth = 300;
						if (eastWidth < 303) eastWidth = 303;

						JSNGrid.UILayout.sizePane("west", westWidth);
						JSNGrid.UILayout.sizePane("east", eastWidth);
						setTimeout(function(){
							JSNGrid.columnResize();
							JSNGrid.eastContentResize();
							
						}, 200);
					}
				},
				ondrag_end: function(panel){
					setTimeout(function(){
						JSNGrid.calculatorRate();
						if (panel == 'east'){
							JSNGrid.columnResize();
						}
					}, 200);
				}
			});

			if ( JSNGrid.panels.cookie.getSize('west') > 350 ){
				JSNGrid.UILayout.sizePane("west", JSNGrid.panels.cookie.getSize('west') );
			}else{
				JSNGrid.UILayout.sizePane("west", 300);
			}

			if ( JSNGrid.panels.cookie.getSize('east') > 298 ){
				JSNGrid.UILayout.sizePane("east", JSNGrid.panels.cookie.getSize('east') );
			}else{
				JSNGrid.UILayout.sizePane("east", 298);
			}
			
			JSNGrid.UILayout.resizeAll();
			$('.ui-layout-resizer').unbind('jsn_dragging_resize').bind('jsn_dragging_resize', function(){
				if ( $(this).hasClass('ui-layout-resizer-east-drag') ){
					var east_dragging = $('.ui-layout-resizer-east-dragging');
					if ( $(this).offset().left > east_dragging.offset().left ){
						JSNGrid.panels.cookie.setParam('drag_east_resize', 'addition');
					}else{
						JSNGrid.panels.cookie.setParam('drag_east_resize', 'subtract');
					}
				}
			});
		};
		/**
		 * 
		 * Resize modules area
		 *
		 * @return: Change HTML
		 */
		JSNGrid.moduleAreaResize = function(){
			if ( JSNGrid.publishing.cookie.isEnableUnpublished() ){
	  			var subHeight = 71;
	  		}else{
	  			var subHeight = 37;
	  		}
			JSNGrid.grid.elementArea.css({
				'height': JSNGrid.panels.panelEast.height()-subHeight,
				'width' : JSNGrid.panels.panelEast.width()
			});
			JSNGrid.grid.elementContainer.css('width', JSNGrid.grid.elementArea.width());
			JSNGrid.eastContentResize();
		};
		/**
		 * Resize column area 
		 */
		JSNGrid.columnResize = function(){
			var oldWidth = JSNGrid.panels.cookie.getSize('east');
			var columnWidth  = 265;
			if (JSNGrid.grid.elementArea.css('overflow-y') != 'hidden'){
				var minEastWidth = 295;
			}else{
				var minEastWidth = 258;
			}
			var column, newWidth;
			if ( JSNGrid.panels.cookie.getParam('drag_east_resize') == 'addition' ){
				column   = Math.round(oldWidth/columnWidth);
				newWidth = column*columnWidth + 35;
				if ( newWidth > JSNGrid.UILayout.state.east.maxSize ){
					newWidth = (column - 1)*columnWidth + 35;
				}
			}else{
				column   = Math.round(oldWidth/columnWidth);
				newWidth = column*columnWidth + 35;
			}
			
			if (newWidth < minEastWidth){
				newWidth = minEastWidth;
			}
			
			$.when(
				JSNGrid.UILayout.sizePane("east", newWidth)
		    ).then(
		    	JSNGrid.eastContentResize(),
		    	$('#jsn-rawmode-layout').trigger('UILayout.resize.complete')
		    );
		};
		/**
		* 
		* Resize east content
		*
		* @return: None
		*/
		JSNGrid.eastContentResize = function(){
			try{
				var positionBoxs   = $('.jsn-element-container');
				var areaWidth      = JSNGrid.grid.elementContainer.outerWidth();
				var containerWidth = positionBoxs.outerWidth();
				var totalColumn    = Math.floor(areaWidth/containerWidth);
				var sumHeight      = 0;
				var columnWidth    = 265;
	
				positionBoxs.each(function(){
					if ($(this).css('display') != 'none'){
						sumHeight += $(this).outerHeight();
					}
				});
	
				if (totalColumn == 0){
					totalColumn = 1;
				}
				var columnMaxHeight = Math.round(sumHeight/totalColumn);
				var columns = new Array(totalColumn);
				for( var i = 0; i < totalColumn; i++ ){
					columns['column'+i] = { 'left' : i*containerWidth, 'top' : 0, 'height' : 0 };
				}
	
				var curColumn = 0;
				positionBoxs.each(function(){
					var topOffset  = $(this).outerHeight();
					if ($(this).css('display') != 'none'){
						if (columns['column'+curColumn].top + topOffset > columnMaxHeight && columns['column'+curColumn].top > 0){
							if (curColumn < totalColumn - 1){
								curColumn += 1;
							}else{
								curColumn = 0;
							}
						}
						$(this).css({ 'top' : columns['column'+curColumn].top,'left': columns['column'+curColumn].left });
						columns['column'+curColumn].top    += topOffset;
						columns['column'+curColumn].height += topOffset;
					}
				});
	
				var maxHeight = 0;
				for(var i = 0; i < totalColumn; i++){
					if ( maxHeight == 0 ){
						maxHeight = columns['column'+i].height;
					}
					if ( columns['column'+i].height > maxHeight ){
						maxHeight = columns['column'+i].height;
					}
				}

				JSNGrid.grid.elementContainer.css({ 'height' : maxHeight + 8, 'width' : areaWidth });
				if ( maxHeight > JSNGrid.grid.elementArea.height() - 10 ){
					JSNGrid.grid.elementArea.css({'overflow-y':'scroll', 'overflow-x':'hidden'});
					if ( JSNGrid.grid.elementArea.width() - columnWidth * columns.length < 33 ){
						JSNGrid.UILayout.sizePane("east", columnWidth * columns.length + 32);
					}
				}else{
					JSNGrid.grid.elementArea.css({'overflow-y':'hidden', 'overflow-x':'hidden'});
					if ( JSNGrid.grid.elementArea.width() - columnWidth * columns.length - 28 > 0 ){
						JSNGrid.UILayout.sizePane("east", columnWidth * columns.length + 17);
					}
				}
			}catch(e){
				return;
			}
		};
		/**
		* 
		* Show all modules in active positions
		*
		* @return: None
		*/	
		JSNGrid.loadModuleByPosition = function(position)
		{
			var currentItemid = $('.jstree-clicked').parent().attr('id').split('-')[2];
			$.getJSON
			(
				baseUrl + 'administrator/index.php?option=com_poweradmin&task=modules.loadModulesJsonData&lang='+lang, 
				{
					position  : position, 
					currItemid: currentItemid
				}).success(function( data ){
					$.checkResponse(data);
					if ( data != '' && data != null ){
						positions[position] = data;
						var objPos = $('#'+position+'-jsnposition');
						objPos.html('');
						$.each( data.modules, function( i, item ){
								var moduleHTML = $('<div />', {
										'id'    : item.element_id.replace('#', ''),
										'class' : item.classset,
										'title' : $.unhtmlspecialchars(item.moduletype)
								
								}).html('<div class="poweradmin-module-item-drag-handle"></div><div class="poweradmin-module-item-inner"><div class="poweradmin-module-item-inner-text">'+$.unhtmlspecialchars(item.title)+'</div><div class="clearbreak"></div></div><div class="clearbreak"></div>').appendTo(objPos);

								if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
									if ( item.unpublish || item.assignment == '' || item.assignment == 'except' ){
										moduleHTML.hide();
									}
								}
								positions[position].modules[item.module_id] = item;
						});

						setTimeout(function(){
							$(window).unbind("completed.buildsubmenu");
							JSNGrid.buildModulesContextMenu( JSNGrid.grid.modules.getAllFromParent( objPos ), false );
							//Drag and Drop modules
							$.JSNDragandDrop( [objPos], JSNGrid );
							JSNGrid.toInactivePosition(position);
							JSNGrid.toActivePosition(position);
							JSNGrid.initEvents();
							JSNGrid.eastContentResize();							
							JSNFilter.doFilter();
						}, 500);
					}
				}).error(function(msg){
					console.log(msg);
				});
		};
		/**
		* 
		* Reload all modules and component
		*
		* @return: None
		*/
		JSNGrid.loadPage = function(link, backgroundLoad){
			backgroundLoad = ( backgroundLoad == undefined )? true : backgroundLoad;
			$('body').showLoading({autoClose:false});
			$.jsnSubmenu.removeSubpanelByClass('rawmode-subpanel');
			$.post
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=rawmode.ajaxGetRender',
				{
					render_url: link
				}
			).success(function(response){
				$.checkResponse(response);
				try{
					if (currentUrl != undefined){
						currentUrl = link;
					}
					
					if (response == 'success'){
						/**
						* Get component
						*/
						$.post
						(
							baseUrl+'administrator/index.php?option=com_poweradmin&task=rawmode.getSessionData',
							{
								session_name: 'component'
							}
						).success(function(response){
							$.checkResponse(response);
							$('#jsn-component-details').html(response);
							$('#jsn-component-details').find("script").each(function(i) {
			                    eval($(this).text());
			                });
							var parseString = $.base64Decode(link).split('index.php');
							if (parseString.length > 1){
								var query  = parseString[1].split('&');
								var option = '', view = '', layout = '',
									Itemid = $('.jstree-clicked').parent().attr('id').split('-')[2],
									aliasoptions = undefined;
								for(k in query){
									if (/option=/.test(query[k])){
										option = query[k].split('=')[1];
									}else if(/view=/.test(query[k])){
										view = query[k].split('=')[1];
									}else if (/layout=/.test(query[k])){
										layout = query[k].split('=')[1];
									}else if (/aliasoptions=/.test(query[k])){
										aliasoptions = query[k].split('=')[1];
									}
								}
								
								if ( aliasoptions != undefined && parseInt(aliasoptions) > 0 ){
									Itemid = aliasoptions;
								}
								
								var JSNComponent = new $.JSNComponent( option, view, layout, Itemid);
								JSNComponent.__destruct();
								JSNComponent.__construct( option, view, layout, Itemid );
							}
						}).error(function(){
							$('body').showLoading({removeall:backgroundLoad});
						});
						/**
						 * Get json data modules array store
						 */
						$.getJSON
						(
							baseUrl+'administrator/index.php?option=com_poweradmin&task=rawmode.getSessionData',
						{
							session_name: 'jsondata'
						}
						).success( function(data){
							$.checkResponse(data);
							positions = data;
							JSNGrid.grid.elementContainer.html('');
							for(k in positions){
								if (positions[k].element_id != undefined){
									var objPos = JSNGrid.addPosition(k, positions[k].container_class, !positions[k].inactive_position);
									var tmpArr = positions[k].modules;
									positions[k].modules = new Array();
									$.each(tmpArr, function( i, item ){
											var moduleHTML = $('<div />', {
													'id'    : item.element_id.replace('#', ''),
													'class' : item.classset,
													'title' : $.unhtmlspecialchars(item.moduletype)
											
											}).html('<div class="poweradmin-module-item-drag-handle"></div><div class="poweradmin-module-item-inner"><div class="poweradmin-module-item-inner-text">'+$.unhtmlspecialchars(item.title)+'</div><div class="clearbreak"></div></div><div class="clearbreak"></div>').appendTo(objPos);
											
											if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
												if ( item.unpublish || item.assignment == '' || item.assignment == 'except' ){
													moduleHTML.hide();
												}
											}
											positions[k].modules[item.module_id] = item;
									});
								}
							}
							JSNGrid.load(backgroundLoad);
						}).error(function(){
							$('body').showLoading({removeall:backgroundLoad});
						});
					}
				}catch(e){
					//console.log(e.message);
				}
			}).error(function(){
				$('body').showLoading({removeall:backgroundLoad});
			});
		};
		/**
		 * 
		 * Hide all module unpublish, inactive position, empty position
		 *
		 * @return: Change HTML
		 */
		JSNGrid.hideAllUnpublished = function(){
			try{
				var changed = false;
				for(k in positions)
				{
					if ($(positions[k].element_id).length){
						if (positions[k].element_id != undefined)
						{
							changed = true;
							for(m in positions[k].modules){
								if ($(positions[k].modules[m].element_id) != undefined){
									if ( $(positions[k].modules[m].element_id).length ){
										if (positions[k].modules[m].unpublish || positions[k].modules[m].assignment == '' || positions[k].modules[m].assignment == 'except' || k == 'notdefault'){
											$(positions[k].modules[m].element_id).hide();
										}
									}
								}
							}
						}
					}
				}
				if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
					JSNGrid.hideEmptyPositions();
				}
			}catch(e){
				//throw e.message;
			}
			
			if (changed){
				JSNGrid.eastContentResize();
			}
		};
		/**
		* 
		* Load all modules
		*
		* @return: None
		*/
		JSNGrid.showModules = function()
		{
			try{
				var changed = false;
				for(k in positions)
				{
					if ($(positions[k].element_id).length){
						if (positions[k].element_id != undefined)
						{
							changed = true;
							if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
								for( m in positions[k].modules ){
									if ($(positions[k].modules[m].element_id).length){
										$(positions[k].modules[m].element_id).show();
									}
								}
							}else{
								for(m in positions[k].modules){
									if (positions[k].modules[m].element_id != undefined){
										if (positions[k].modules[m].unpublish || positions[k].modules[m].assignment == '' || positions[k].modules[m].assignment == 'except' || k == 'notdefault'){
											$(positions[k].modules[m].element_id).hide();
										}else{
											$(positions[k].modules[m].element_id).show();
										}
									}
								}
							}
						}
					}
				}
				if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
					JSNGrid.hideEmptyPositions();
				}
			}catch(e){
				//throw e.message;
			}
			
			if (changed){
				JSNGrid.eastContentResize();
			}
		};
		/**
		* 
		* Convert position to inactive
		*
		* @return: None
		*/
		JSNGrid.toInactivePosition = function(position){
			if (positions[position] == undefined || position == 'notdefault'){
				return;
			} 
			var currentPos = $('#'+position+'-jsnposition');
			var toInactive = true;
			for(m in positions[position].modules){
				if (positions[position].modules[m].unpublish != undefined){
					toInactive = toInactive && ( positions[position].modules[m].unpublish || positions[position].modules[m].assignment == '' || positions[position].modules[m].assignment == 'except' );
				}
			}
			if (toInactive){
				currentPos.parent().addClass('jsn-inactive-element');
				positions[position].inactive_position = true;
				if ( !JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() && !JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || !JSNGrid.publishing.cookie.isEnableUnpublished() ){
					currentPos.parent().parent().hide();
					JSNGrid.hideEmptyPositions();
				}
			}
			JSNGrid.eastContentResize();
		};
		/**
		* 
		* Convert position to active
		*
		* @return: None
		*/
		JSNGrid.toActivePosition = function(position){
 			if (positions[position] == undefined || position == 'notdefault'){ 
 				return;
 			}
			var currentPos = $('#'+position+'-jsnposition');
			var toAcitive = false;
			for( m in positions[position].modules ){
				if ( positions[position].modules[m].unpublish != undefined ){
					toAcitive = toAcitive || ( !positions[position].modules[m].unpublish && positions[position].modules[m].assignment != '' && positions[position].modules[m].assignment != 'except' );
				}
			}
			if ( toAcitive ) {
				currentPos.parent().removeClass('jsn-inactive-element');
				positions[position].inactive_position = false;
				currentPos.parent().parent().show();
			}
			JSNGrid.eastContentResize();
		};		
		/**
		* 
		* Add jsn-poweradmin-position
		*
		* @return: jQuery Element
		*/
		JSNGrid.addPosition = function(position, containerClass, boxDisplay){
			var positionBox  = $({});
			if ( $('#'+position+'-jsnposition').length == 0){
				var newPosition = $('<div/>', {
					'class': containerClass
				});
				
				if (position != 'notdefault'){
					$('<div />', {
						'class':'jsn-position-name'
					}).appendTo(newPosition)
					  .append('<h2>'+position+'</h2>');
				}else{
					newPosition.addClass('jsn-notdefault-element');
				}

				$('<div />', {
					'class':'clr'
				}).appendTo(newPosition);

				var positionBox = $('<div />', {
					'id':position+'-jsnposition',
					'class':'jsn-poweradmin-position'
				}).appendTo(newPosition);
				
				$('<div />', {
					'class':'clr'
				}).appendTo(newPosition);

				var containerBox = $('<div />', {'class':'jsn-element-container'}).appendTo( JSNGrid.grid.elementContainer ).append(newPosition).append('<div class="clearbreak"/>');
				if (boxDisplay){
					containerBox.show();
				}else{
					containerBox.hide();
				}
			}
			return positionBox;
		};
		/**
		* 
		* Show all position in template
		*
		* @return: None
		*/
		JSNGrid.showAllPositions = function(){			
			try{
				var changed = false;
				for(k in positions)
				{
					if (positions[k].element_id != undefined){
						changed = true;
						$(positions[k].element_id).parent().parent().show();	
						if ( JSNGrid.publishing.cookie.isEnableShowUnpublishedModules() ){
							for( m in positions[k].modules ){									
								$(positions[k].modules[m].element_id).show();
										
							}
						}						
					}
				}
			}catch(e){
				//throw e.message;
			}
			if (changed) JSNGrid.eastContentResize();
		};
		/**
		* 
		* Hide inactive positions
		*
		* @return: None
		*/
		JSNGrid.hideEmptyPositions = function(){
			try{
				var changed = false,
				    isEmptyPosition = true;
				for(k in positions)
				{
					if ( positions[k].element_id != undefined ){
						isEmptyPosition = true;
						for( m in positions[k].modules ){
							if ($(positions[k].modules[m].element_id).length > 0 && $(positions[k].modules[m].element_id).css('display') != 'none'){
								isEmptyPosition = false;
							}
						}
						if ( isEmptyPosition ){
							$(positions[k].element_id).parent().parent().hide();
						}else{
							$(positions[k].element_id).parent().parent().show();
						}
					}
				}
			}catch(e){
				//throw e.message;
			}
			JSNGrid.eastContentResize();
		};
		/**
		*
		* Move js item
		*
		* @return: None
		*/
		JSNGrid.moveObjItem = function(fromPos, toPos, mId){
			if (positions[fromPos] != undefined && positions[toPos] != undefined){
				if (positions[fromPos].modules[mId] != undefined){
					positions[toPos].modules[mId] = positions[fromPos].modules[mId];
					delete positions[fromPos].modules[mId];
				}
			}
		};
		/**
		* 
		* Edit module
		*
		* @return: None
		*/
		JSNGrid.editModule = function( moduleItem ){
			var mid     = moduleItem.attr('id').split('-')[0];
			var position= moduleItem.parent().attr('id').replace('-jsnposition', '');
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8;
			JSNGrid.grid.imageStatus.request( moduleItem );

			var hasAdvancedModules = $('a[href*=com_advancedmodules]').size() > 0,
				comModule = (hasAdvancedModules) ? 'com_advancedmodules' : 'com_modules';

			function resetViewStyles (iframe) {
				var content = iframe.contents()
					form = content.find('form');

				content.find('#toolbar-box').remove();
				content.find('#element-box')
					.removeAttr('id')
					.find('div.m')
						.removeClass('m');
			}

			var pop     = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=' + comModule + '&task=module.edit&tmpl=component&id='+mid,
				{
					modal : true,
					width : wWidth, 
					height: wHeight,
					title : JSNLang.translate( 'TITLE_EDIT_MODULE' ),
					open  : function(){
						var iframe = $(this).find('iframe');
						var _this  = $(this);

						iframe.load(function(){
							var head = iframe.contents().find('head');
							//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
							
							if (hasAdvancedModules) {
								resetViewStyles(iframe);
							}
						});

						//bind trigger press enter submit form from child page
						$(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
							iframe.unbind('load').load(function(){
								JSNGrid.grid.imageStatus.success( moduleItem );
								JSNGrid.loadModuleByPosition(position);
							});
						});
					},
					close: function(){
						JSNGrid.grid.imageStatus.remove( moduleItem );													
						$.post(baseUrl+'administrator/index.php?option=com_poweradmin&task=module.checkin',{moduleid: mid});
					},
					buttons: {						
						'Save': function(){
							var _this  = $(this);
							var iframe = $(this).find('iframe');
							if (!$.fn.validateEmptyFields(iframe)) {
								return false;
							}
							
							if (pop.submitForm('module.apply', 'Save', function (){
									if (hasAdvancedModules) {
										resetViewStyles(iframe);
									}
									_this.removeClass('jsn-loading');
									JSNGrid.loadModuleByPosition(position);									
								
							})){
								_this.addClass('jsn-loading');								
							}
						},
						'Close': function(){
							$(this).dialog("close");
						}
					}
				}
			);
		}
		/**
		* 
		* Select module type to create new module
		*
		* @return: None
		*/		
		JSNGrid.selectModuleType = function(position){
			var pop     = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&view=selectmoduletypes&tmpl=component&pwadvisual=1&position='+position, 
				{
					modal : true,
					width : 760, 
					height: 550,
					buttons: {
						'Close': function(){
							$(this).dialog("close");
						}
					},
					title : JSNLang.translate( 'TITLE_SELECT_MODULE_TYPE_PAGE' ),
					search: {
						text	: JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ),
						classSet: 'ui-window-searchbar',
						onChange: function(){
							var iframe = pop.getIframe();
							iframe[0].contentWindow.selectModuleType.filterResults($(this).val().trim());
						},
						onKeyup : function(){
							//fire change event
							$(this).change();
						},
						onBlur  : function(){
							if ($(this).val().trim() == ''){
								$(this).val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ) );
								$(this).css('color', '#CCCCCC');
							}
						},
						onFocus : function(){
							if ($(this).val().trim() == JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' )){
								$(this).css('color', '#000').val('');
							}
						},
						closeTextKeyword : true,
						afterAddTextCloseSearch : function(obj){
							//obj.css({'margin-right': '275px'});
						},
						defaultText      : JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ),
						closeTextClick   : function( obj, searchbox ){
							obj.hide();
							var iframe = pop.getIframe();
							iframe[0].contentWindow.selectModuleType.filterResults('');
							searchbox.css('color', '#CCCCCC').val( JSNLang.translate( 'DEFAULT_TEXT_SEARCH_SELECT_MODULE_TYPE' ) );
						}
					}
				} 
			);
		};
		/**
		* 
		* New module
		*
		* @return: None
		*/
		JSNGrid.newModule = function( eid, position ){
			var wWidth  = $(window).width()*0.85;
			var wHeight = $(window).height()*0.8; 
			var pop     = $.JSNUIWindow
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=selectmoduletypes.setModuleType&pwadvisual=1&eid='+eid, 
				{
					modal : true,
					width : wWidth, 
					height: wHeight, 
					title : JSNLang.translate( 'TITLE_NEW_MODULE_PAGE' ),
					open  : function(){
						var iframe = $(this).find('iframe');
						var _this  = $(this);
						iframe.load(function(){
							var newModuleList = iframe.contents().find('#new-modules-list');
							$('.modal-title').css('display', 'none');
							newModuleList.css('border', 'none');
							$('li', newModuleList).css('float', 'left');
							$('li', newModuleList).css('width', '30%');
							$('a', newModuleList).each(function(){
								var href = $(this).attr('href');
								$(this).unbind("click")
										.attr('href', 'javascript:void(0);')
										.attr('onClick', 'window.location.href = \''+href+'&tmpl=component&pwadvisual=1\'');
							});
							iframe.contents().find('#jform_position').val(position);
						});
						
						//bind trigger press enter submit form from child page
					    $(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
							iframe.load(function(){
								JSNGrid.loadModuleByPosition(position);
							});
						});
					},
					buttons:{						
						'Save & Close': function(){
							var _this  = $(this);
							var iframe = $(this).find('iframe');
							
							if (!$.fn.validateEmptyFields(iframe)) {
								return false;
							}
							if (pop.submitForm('module.save', 'Save & Close')){
								iframe.load(function(){
									JSNGrid.loadModuleByPosition(position);
								});
							}
						},
						'Cancel': function(){
							$(this).dialog("close");
						}
					}
				} 
			);
		};
		/**
		* 
		* Load setting
		*
		* @return: None
		*/
		JSNGrid.load = function(backgroundLoad){
			backgroundLoad = ( backgroundLoad == undefined )? true : backgroundLoad;
			$.when(
				JSNGrid.calculatorRate(),
				JSNGrid.initEvents(),
				JSNGrid.eastContentResize(),
				//Drag and Drop modules
				$.JSNDragandDrop( $('.jsn-poweradmin-position'), JSNGrid ),
				JSNGrid.buildPositionContextMenu( $('.jsn-element-container_inner') ),
				JSNGrid.buildModulesContextMenu(  JSNGrid.grid.modules.getAll() )
			).then(
				$('body').showLoading({removeall:backgroundLoad})
			);
		};
		/**
		* 
		* Resize layout when window resize
		*
		*/
		$(window).resize(function(){
			JSNGrid.initLayout();
		});
		JSNGrid.initLayout();
		JSNGrid.load();
		return JSNGrid;
  };
  
  /**
  * Global variable Instance for rawmode component
  */
  var JSNGridInstances = new Array();
  $.JSNGrid = function(){
  	  if ( JSNGridInstances['JSNGrid'] == undefined ){
  		JSNGridInstances['JSNGrid'] = new $.JSNModulesGrid();
  	  }
  	  
  	  return JSNGridInstances['JSNGrid'];
  };
  
})(JoomlaShine.jQuery);