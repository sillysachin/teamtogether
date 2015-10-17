/**
 * Show block
 * 
 * Added 24/08/2011
 * Power by JoomlaShine - http://www.joomlashine.com
 * Package JSNPOWERADMIN
 * Subpackage - Show block
 * 
 **/
(function($){
	$.extend({
		_block:{
			_showOutline: function(el){
				if (!(el instanceof jQuery)) el = $(el);
				el.find('.jsn-show-module-container').remove();				
				var mid		= el.attr('id').replace('showmodule-', '').replace('-jsnposition', '').replace('-unpublished', '').replace('-published', '');				
				var moduleContent   = $('#'+mid+'-content');				
				var children = $('#'+mid+'-content').children();				
				$('<div/>').appendTo(children).attr('class', 'clearbreak');				
				var showmodulecontainer = $('<div/>').appendTo(children)
				                                     .attr('class', 'jsn-show-module-container')
				                                     .attr('id', 'show-module-container-'+mid);				
				var showmodule = $('<div/>').appendTo(showmodulecontainer)
				                            .attr('class', 'jsn-show-module');
				// Calculate width and height of module position outline
				moduleContent.children().css('position', 'relative')
				                        .addClass('clearafter');
				showmodule.css('width', showmodulecontainer.width()-6);
				if ( showmodulecontainer.height() < 29 ) {
					showmodule.css('height', '28px');
					if ( showmodulecontainer.height() < 24 ) {
						showmodule.css('top', '-5px');
					}
				} else {
					showmodule.css('height', showmodulecontainer.height()-6);
				}
			},
	
			_shows: function(){
				$('.poweradmin-module-item').each(function(e){
					$._block._showOutline($(this));
					var moduleContextMenu = $(this).jsnSubmenu({rebuild:false, attrs:{'class': 'jsnpw-submenu module-context-menu'}});
					try{
						var showtitle = moduleContextMenu.getRootAttr('showtitle');
						var showtitletext = (showtitle == 0?'Show title':'Hide title');
						moduleContextMenu.setVal("showTitle", (showtitle=='1'?true:false));
						if (moduleContextMenu.isNew()){
							//Item edit
							moduleContextMenu.addItem('Edit').click(function(){
								moduleContextMenu.hide({});
								if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
								window.parent.jQuery._visualmode.fullEditModule(mid);
							});
							//Item hide/show title
							moduleContextMenu.addItem(showtitletext).click(function(){
								if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
								var showTitle = moduleContextMenu.getVal('showTitle');								
								moduleContextMenu.setVal('showTitle', !showTitle);
								var title = moduleContextMenu.getRootAttr('title');
								var text = (showTitle?'Show Title':'Hide Title');
								$(this).text(text);
								if(!showTitle){
									$.post
									(
										baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.showTitle',
										{
											showtitle:1,
											moduleid :mid
										}, function(res){
											if (res != 'error'){
												$('*', moduleContextMenu).each(function(){
													if ($(this).text().trim() == title){
														$(this).show();
													}
												});											
												
												var _blueBoxContainer = $('.jsn-show-module-container', moduleContextMenu);
												_blueBoxContainer.children('.jsn-show-module').css('width', _blueBoxContainer.width()-6);
												if (_blueBoxContainer.height() < 29 ) {
													_blueBoxContainer.children('.jsn-show-module').css('height', '28px');
													if ( _blueBoxContainer.height() < 24 ) {
														_blueBoxContainer.children('.jsn-show-module').css('top', '-5px');
													}
												} else {
													_blueBoxContainer.children('.jsn-show-module').css('height', _blueBoxContainer.height()-6);
												}
											}
										}
									);
								}else{
									$.post
									(
										baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.showTitle',
										{
											showtitle:0,
											moduleid :mid
										}, function(res){
											if (res != 'error'){
												$('*', moduleContextMenu).each(function(){
													if ($(this).text().trim() == title){
														$(this).hide();
													}
												});
												
												var _blueBoxContainer = $('.jsn-show-module-container', moduleContextMenu);
												_blueBoxContainer.children('.jsn-show-module').css('width', _blueBoxContainer.width()-6);
												if (_blueBoxContainer.height() < 29 ) {
													_blueBoxContainer.children('.jsn-show-module').css('height', '28px');
													if ( _blueBoxContainer.height() < 24 ) {
														_blueBoxContainer.children('.jsn-show-module').css('top', '-5px');
													}
												} else {
													_blueBoxContainer.children('.jsn-show-module').css('height', _blueBoxContainer.height()-6);
												}
											}
										}
									);
								}
								moduleContextMenu.hide({});
							});
							
							//Item change position
							moduleContextMenu.addItem('Change position').click(function(){
								moduleContextMenu.hide({});
								if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
								window.parent.jQuery._visualmode.changePosition(mid);
							});
							
							
							
							//Item assign to pages
							moduleContextMenu.addItem('Assign to pages').click(function(){
								moduleContextMenu.hide({});
								if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
								window.parent.jQuery._visualmode.assignPages(mid);
							});
							
							/**
							 * Publish subpanel
							 */
							var publishSubPanel = moduleContextMenu.addParentItem('Publish');
							
							//Publish on all pages
							publishSubPanel.addItem('On all pages').click(function(){
								moduleContextMenu.hide({});
								var  moduleid   = moduleContextMenu.getRootAttr('id').split('-')[0];
								$.post
								(
									ops.baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.publish&lang='+ops.lang,
									{
										publish_area: 'all',
										moduleid: moduleid,
										menuid: ops.currItemid
									},function()
									{
										moduleContextMenu.setRootAttr('id', moduleid+'-jsnposition-published');
										moduleContextMenu.rootRemoveClass('jsn-module-unpublish');
									}
								);
							});
							
							//Publish only one page
							publishSubPanel.addItem('Only on this page').click(function(){
								moduleContextMenu.hide({});
								var  moduleid   = moduleContextMenu.getRootAttr('id').split('-')[0];
								$.post
								(
									ops.baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.publish&lang='+ops.lang,
									{
										publish_area: 'one',
										moduleid: moduleid,
										menuid:ops.currItemid
									},function(){
										moduleContextMenu.setRootAttr('id', moduleid+'-jsnposition-published');
										moduleContextMenu.rootRemoveClass('jsn-module-unpublish');
									}
								);
							});
							
							/**
							 * Unpublish subpanel 
							 */
							var unpublishSubpanel = moduleContextMenu.addParentItem('Unpublish');
							
							//Unpublish on all page
							unpublishSubpanel.addItem('From all pages').click(function(){
								submenu.hide({});								
								var  moduleid   = moduleContextMenu.getRootAttr('id').split('-')[0];
								$.post( 
									ops.baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.unpublish&lang='+ops.lang, 
									{
										moduleid: moduleid,
										unpublish_area: 'all'
									}, function( message ){									
									moduleContextMenu.setRootAttr('id', moduleid+'-jsnposition-unpublished');
									moduleContextMenu.rootAddClass('jsn-module-unpublish');
								});
							});
							
							//Unpublish only one page
							unpublishSubpanel.addItem('Only from this page').click(function(){
								moduleContextMenu.hide({});								
								var  moduleid   = moduleContextMenu.getRootAttr('id').split('-')[0];
								
								$.post( 
									ops.baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.unpublish&lang='+ops.lang, 
									{
										moduleid: moduleid,
										menuid:ops.currItemid,
										unpublish_area: 'one'
									}, function( message ){									
									moduleContextMenu.setRootAttr('id', moduleid+'-jsnposition-unpublished');
									moduleContextMenu.rootAddClass('jsn-module-unpublish');
								});
							});
							
							//add divider
				            moduleContextMenu.addDivider();
				            
				            //add submenu
				            var more = moduleContextMenu.addParentItem('More');
				            more.addItem('Duplicate').click(function(){
				            	moduleContextMenu.hide({});
				            	if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
				            	$.post
				            	(
				            		baseUrl+'administrator/index.php?option=com_poweradmin&task=module.duplicate',
				            		{
				            			cid: [mid]
				            		},
				            		function(res){
				            			if (res==''){

				            			}
				            		}
				            	);
				            });
				            more.addItem('Trash').click(function(){
				            	moduleContextMenu.hide({});
				            	if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
				            	$.post
				            	(
				            		baseUrl+'administrator/index.php?option=com_poweradmin&task=module.trash',
				            		{
				            			cid: [mid]
				            		},
				            		function(res){
				            			if (res==''){
				            				moduleContextMenu.getRoot().remove();
				            				moduleContextMenu.remove();
				            			}
				            		}
				            	);
				            });
				            more.addItem('Options').click(function(){
				            	moduleContextMenu.hide({});
				            	if (typeof moduleContextMenu.getParentRoot().attr == 'function'){
				            		var mid = moduleContextMenu.getRootAttr('id').split('-')[0];
				            	}else{
				            		var mid = '';
				            	}
			            		window.parent.jQuery._visualmode.moduleOptions(mid);				            	
				            });
						}
						
						$(this).unbind("mousedown").mousedown(function(e){							
				    		if (e.which === 3 ){
				    			moduleContextMenu.show({x:$.jsnmouse.getX()+5, y:$.jsnmouse.getY()+10});
				    		}else{
				    			moduleContextMenu.hide({});
				    		}
					    });
					    return moduleContextMenu;
					    
					}catch(e){
						throw e.message;
					}finally{
						return;
					}
				});
	
				$('.jsn-component-container').each(function(){
					var el = $(this);
					el.unbind('mouseenter');
					el.addClass('jsn-show-block');
	
					// Calculate width and height of component outline					
					var component = $('.jsn-show-component', el);
					var componentContainer = $('.jsn-show-component-container', el);
					
					component.css('width', componentContainer.width() - 4)
					         .css('height', componentContainer.height() - 8)
					         .css('z-index', $.topZIndex());
				});			
	
				$('#button-remove-position').attr('disabled', '');
				$('#button-show-position').attr('disabled', 'disabled');
				
				$.draganddrop();
			},
	
			_removes: function(){
				$.draganddrop_destroy();
				$('.jsn-module-settings-subpanel').remove();
				$('.jsn-modulecontainer').removeClass('clearafter').attr('style', '');
				$('.clearbreak').remove();
				$('.jsn-show-module-container').remove();
				$('.jsn-component-container').removeClass('jsn-show-block');	
				$('#button-remove-position').attr('disabled', 'disabled');
				$('#button-show-position').attr('disabled', '');
				$('.poweradmin-module-item').css('cursor', '');
			}
		}
	});
})(JoomlaShine.jQuery);