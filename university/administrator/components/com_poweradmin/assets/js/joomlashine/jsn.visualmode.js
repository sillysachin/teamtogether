/**
*power by JoomlaShine
*Added date: 18 June 2011
*/
var selectedPosition = '';
var jsnpoweradmin = true;
var jsnpwiframeready = false;
var showLoading;
(function($){
	var resizeRate = {};
	var UILayout = null;
	var vsInternalLoad = null;
	$.extend({
		_visualmode:{
			init: function(){
                
                $('span.unpublish-modules').click(function(){
                	if ($(this).children('label').hasClass('ui-state-active')){
                		$.jStorage.set('unpublish_module', true);
						window.frames.jsnrender.jQuery._visualmode.showAllModule();
                	}else{
                		$.jStorage.set('unpublish_module', false);
						window.frames.jsnrender.jQuery._visualmode.showAllPublishModule();
                	}
                });
                
                $('span.inactive-positions').click(function(){
                	if ($(this).children('label').hasClass('ui-state-active')){
                		$.jStorage.set('inactive_position', true);
						$._visualmode.showPosition();
                	}else{
                		$.jStorage.set('inactive_position', false);
						$._visualmode.hidePosition();
                	}
                });
                
                $('span.module-highlights').click(function(){
                	if ($(this).children('label').hasClass('ui-state-active')){
                		window.frames.jsnrender.jQuery._block._shows();
						$._visualmode.componentSetting();
						$.jStorage.set('module_hightlight', true);
                	}else{
                		$.jStorage.set('module_hightlight', false);
						window.frames.jsnrender.jQuery._block._removes();
                	}
                });
			
				if ($.jStorage.get('inactive_position')){
					$('input#inactive-positions').attr('checked', true);
					$('label[for="inactive-positions"]').addClass('ui-state-active').attr('aria-pressed', true);
					$._visualmode.showPosition();
				}
				
				if ($.jStorage.get('module_hightlight')){
					$('input#module-highlights').attr('checked', true);
					$('label[for="module-highlights"]').addClass('ui-state-active').attr('aria-pressed', true);
				}
				
				if ($.jStorage.get('unpublish_module')){
					$('input#unpublish-modules').attr('checked', true);
					$('label[for="unpublish-modules"]').addClass('ui-state-active').attr('aria-pressed', true);
				}
			},
			/**
			 * Canculator rate layout
			 */			
			calculatorRate: function(){
				var leftWidth  = $('#jsn-vsleft-panel').width()+50;
			    var rightWidth = $('#jsn-vsright-panel').width();
			    var fullWidth  = $('.jsn-visual-layout').width();
			    resizeRate     = {west: leftWidth*100/fullWidth, right: rightWidth*100/fullWidth};
			},
			/**
			 * Init layout and resize
			 */
			initLayout: function(){
				var visualLayout = $('.jsn-visual-layout');
				var heightSpace  = $(window).height() - $('body').height();
				var newHeight    = heightSpace + visualLayout.height()-30;				
				visualLayout.css('height', newHeight);
    
			   	UILayout = visualLayout.layout
				(
					{
						west__onresize: function(){
							$._menuitems.layoutResize();
						},
						center__onresize: function(){
							var iframe       = $('#jsn-visual-layout-frame');
							var rightPanel   = $('#jsn-vsright-panel');
							iframe.css('width', rightPanel.width());
							if ($.browser.msie && $.browser.version < 8){
								iframe.css('width', rightPanel.width()+40);
							} 
							iframe.css('height', rightPanel.height()-45);
						},						
						onresizeall_end: function(){
						    var fullWidth  = $('.jsn-visual-layout').width();
						    var westWidth  = resizeRate.west*fullWidth/100;
						    if (westWidth < 300) westWidth = 300;
							UILayout.sizePane("west", westWidth);
						},
						ondrag_end: function(){
							setTimeout(function(){
								$._visualmode.calculatorRate();
							}, 300);
						}
					}
				);
				if (parseInt($.jStorage.get("visual_layout_resize")) > 300){
					UILayout.sizePane("west", $.jStorage.get("visual_layout_resize"));
				}else{
					UILayout.sizePane("west", 300);
				}
				UILayout.resizeAll();
			},
			/**
			 * Show all template positions
			 */
			showPosition: function(){
				var url = baseUrl+'administrator/index.php?option=com_poweradmin&view=jsnrender&format=raw&render_url='+$.base64Encode($.jStorage.get('render_url')+'&tp=1');							
				$._visualmode.setIframeSRC(url);
			},
			
			/**
			 * Hide inactive positions
			 */
			hidePosition: function(){
				var url    = baseUrl+'administrator/index.php?option=com_poweradmin&view=jsnrender&format=raw&render_url='+$.base64Encode($.jStorage.get('render_url'));
				$._visualmode.setIframeSRC(url);
			},
			
			/**
			 * Change the status of module
			 */
			changeStatus: function(elementid, status){
				var iframebody = $('#jsn-visual-layout-frame').contents();
				switch(status)
				{
					case 'publish':
						var item = iframebody.find('#'+elementid);
						item.removeClass('jsn-module-unpublish');
						var $newId = elementid.replace('unpublished', 'published');
						item.attr('id', $newId);
						window.frames.jsnrender.jQuery._block._showOutline('#'+$newId);
						break;
						
					case 'unpublish':
						if ($('.unpublish-modules-active').length > 0){
                            var item = iframebody.find('#'+elementid);
                            item.addClass('jsn-module-unpublish');
							var $newId = elementid.replace('published', 'unpublished');
							item.attr('id', $newId);
							window.frames.jsnrender.jQuery._block._showOutline('#'+$newId);
						}else{
							iframebody.find('#'+elementid).remove();
						}
						break;
				}
				
				iframebody.find('.jsn-poweradmin-position').each(function(){
					if ($(this).find('.poweradmin-module-item').length == 0){
						$(this).html('');
						$(this).addClass('inactive-position');
						$('<label/>').appendTo(this).addClass('jsn-position-name').html($(this).attr('id').replace('-jsnposition', ''));
					}
				});
				
				$._visualmode.componentSetting();
				window.frames.jsnrender.jQuery.draganddrop();
			},
			
			/**
			 * Publishing module
			 */
			publishModule: function( elementid, itemid, moduleid, publish ){
				if (publish == 1){
                    $.JSNUIWindow(
							baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.publishConfirmation&moduleid='+moduleid+'&menuid='+itemid, 
							{
								modal: false,
								width: 350,
								height: 180,
								title: 'Publishing Confirmation',
								buttons: {
									Save: function(){
										var _this  = $(this);
										var iframe = $(this).find('iframe');
										iframe.contents().find('form[name="adminForm"]').submit();
										iframe.load(function(){
											$._visualmode.changeStatus(elementid, 'publish');
											_this.dialog("close");
										});
									},
									Cancel: function(){
										$(this).dialog("close");
									}
								}
							} 
						);				
				}else{
					$.post( baseUrl+'index.php?option=com_poweradmin&view=modules&task=modules.unpublish&lang='+lang, {moduleid: moduleid}, function( msg ){
						window.parent.jQuery._visualmode.showMessage(msg);
						$._visualmode.changeStatus(elementid, 'unpublish');
					});
				}
			},
	        
	        /**
	         * Show all module
	         */
			showAllModule: function(){
				var isShowHighlight = $('.jsn-show-module-container').length;
				var totalPosition   = $('.jsn-poweradmin-position').length;
				var ajaxCompleted   = 0;
				$('.jsn-poweradmin-position').each(function(){
					var position = $(this).attr('id').replace('-jsnposition', '');
				
					var params = '';
					if ( positions[position] !== undefined ){
						params = $.base64Encode( positions[position] );
					}else{
						params = $.base64Encode( position.trim().toLowerCase()+'||'+'style=none' );
					}
					$('#'+position+'-jsnposition').find('.poweradmin-module-item').remove();
					$.post(baseUrl + 'index.php?option=com_poweradmin&view=modules&format=raw&lang='+lang, {params:params, currItemid:$('span#tableshow').attr('itemid'), showtype:'all'}, function( response ){
						if ( response != ''){
							$('#'+position+'-jsnposition').html( response );
						}
						ajaxCompleted++;
						if (ajaxCompleted == totalPosition){
							if (isShowHighlight > 0){
								$._block._shows();
								window.parent.jQuery._visualmode.componentSetting();
							}
						}
						
					});
				});
                
			},
	        
	        /**
	         * Show all published module
	         */
			showAllPublishModule: function(){
				var isShowHighlight = $('.jsn-show-module-container').length;
				var totalPosition   = $('.jsn-poweradmin-position').length;
				var ajaxCompleted   = 0;

				$('.jsn-poweradmin-position').each(function(){
					var position = $(this).attr('id').replace('-jsnposition', '');
					var params = '';
					if ( positions[position] !== undefined ){
						params = $.base64Encode( positions[position] );
					}else{
						params = $.base64Encode( position.trim().toLowerCase()+'||'+'style=none' );
					}
					$('#'+position+'-jsnposition').find('.poweradmin-module-item').remove();
					$.post(baseUrl + 'index.php?option=com_poweradmin&view=modules&format=raw&lang='+lang, {params:params, currItemid:currItemid, showtype:'publish'}, function( response ){
						if ( response != ''){
							$('#'+position+'-jsnposition').html( response );
						}
						ajaxCompleted++;
						if (ajaxCompleted == totalPosition){
							if (isShowHighlight > 0){
								$._block._shows();
								window.parent.jQuery._visualmode.componentSetting();
							}
						}
					});
				});
				
			},
			
			/** 
			 * Set ready when render page is ready
			 */
			jsnRenderReady: function ()
			{
				jsnpwiframeready = true;
				$('body').children('.ui-widget-overlay').remove();
				$._visualmode.showLoading(false);
				
				if ($.jStorage.get('module_hightlight')){
					window.frames.jsnrender.jQuery._block._shows();
					$._visualmode.componentSetting();
				}
				
				if ($.jStorage.get('unpublish_module')){
					window.frames.jsnrender.jQuery._visualmode.showAllModule();
				}
				
				if ($.jStorage.get('inactive_position')){
					var iframe = $('#jsn-visual-layout-frame');
					iframe.contents().find('a.add-new-module').unbind("click").click(function(){
				  	   var inactive_position = $(this).parent().attr('id').replace('-jsnposition', '');
				  	   $._visualmode.selectModuleType(inactive_position);
				    });
				}

			},
			
			/**
			 * Set events for elements in render page
			 */
			miniEditModule: function(mid){
				var pop = $.JSNUIWindow
				(
					baseUrl + 'administrator/index.php?option=com_modules&task=module.edit&tmpl=component&pwadvisual=1&id='+mid, 
					{
						modal:true, 
						width: 700, 
						height: 320, 
						title: 'Module Details',
						open: function(){
							var iframe = $(this).find('iframe');
							var _this  = $(this);
							iframe.load(function(){
								var head = iframe.contents().find('head');
								//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
								var el = iframe.contents().find('#module-form');
								el.parent().css('width', 'auto');											
								el.find('legend').each(function(){
									if ($(this).html() != 'Details'){
										$(this).parent().parent().css('display', 'none');
									}else{
										$(this).css('display', 'none');
									}
								});
								el.find('#module-sliders').parent().css('display', 'none');
								el.find('.pane-sliders').css('display', 'none');
								el.find('.width-60').attr('class', 'width-100 fltlft');		
								el.find('#jform_note-lbl').parent().css('display', 'none');
								el.find('#jform_ordering-lbl').parent().css('display', 'none');
								el.find('#jform_publish_up-lbl').parent().css('display', 'none');
								el.find('#jform_publish_down-lbl').parent().css('display', 'none');
								el.find('#jform_language-lbl').parent().css('display', 'none');
								el.find('#jform_id-lbl').parent().css('display', 'none');
								el.find('#jform_module').parent().css('display', 'none');
								if ( el.find('#jform_params_moduleclass_sfx-lbl').length > 0 ){
									var module_class_sfx = '<li>'+$('#jform_params_moduleclass_sfx-lbl').parent().html()+'</li>';
									$('#jform_params_moduleclass_sfx-lbl').parent().remove();
									$('#jform_note-lbl').parent().parent().append(module_class_sfx);
								}		
								var positionArea   = el.find('#jform_position').parent();
								el.find('.button2-left').css('display', 'none');
								el.find('.mod-desc').css('display', 'none');
								el.find('label').each(function(){
									if ($(this).html().trim().toLowerCase() == 'module description'){
										$(this).css('display', 'none');
									}
								});
								var selected       = el.find('#jform_position').val();
								el.find('#jform_position').remove();
								var jform_position = $('<select name="jform[position]" id="jform_position" class="required" />').appendTo(positionArea);
								jform_position.append('<option value="" selected="selected">---Select One Position---</option>');
								for(k in positions){
									if (typeof positions[k].split === 'function'){
										var position = positions[k].split('||')[0];
										jform_position.append('<option value="'+position+'">'+position+'</option>');
									}
								}
								jform_position.val(selected);										
							});
							
							//bind trigger press enter submit form from child page
						    $(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
								iframe.unbind("load").load(function(){
									_this.dialog("close");								
								});
							});


						},
						buttons: {
							Save: function(){
								if (pop.submitForm('module.save')){
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.load(function(){
										_this.dialog("close");
									});
								}
							},
							
							Cancel: function(){
								$(this).dialog("close");
							}
						}
					} 
				);
			},
			
			/**
			 * Full edit screen
			 */
			fullEditModule: function(mid){
				var wWidth  = $(window).width()*0.85;
				var wHeight = $(window).height()*0.8;
				var pop     = $.JSNUIWindow
				(
					baseUrl + 'administrator/index.php?option=com_modules&task=module.edit&tmpl=component&pwadvisual=1&id=' + mid, 
					{
						modal:true, 
						width: wWidth, 
						height: wHeight,  
						title: 'Module Details',
						open: function(){
							var iframe = $(this).find('iframe');
							var _this  = $(this);
							iframe.load(function(){
								var head = iframe.contents().find('head');
								//head.append('<link rel="stylesheet" href="' + baseUrl + 'plugins/system/jsnframework/assets/joomlashine/css/jsn-gui.css" type="text/css" />');
								
								var el = iframe.contents().find('#module-form');										
								el.parent().css('width', '100%');
								var positionArea   = el.find('#jform_position').parent();
								el.find('.button2-left').css('display', 'none');
								el.find('label').each(function(){
									if ($(this).html().trim().toLowerCase() == 'module description'){
										$(this).css('display', 'none');
									}
								});
								var selected       = el.find('#jform_position').val();
								el.find('#jform_position').remove();
								var jform_position = $('<select name="jform[position]" id="jform_position" class="required" />').appendTo(positionArea);
								jform_position.append('<option value="" selected="selected">---Select One Position---</option>');
								for(k in positions){
									if (typeof positions[k].split === 'function'){
										var position = positions[k].split('||')[0];
										jform_position.append('<option value="'+position+'">'+position+'</option>');
									}
								}
								jform_position.val(selected);
							});
							
							//bind trigger press enter submit form from child page
						    $(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
								iframe.unbind("load").load(function(){
									_this.dialog("close");
								});
							});

						}, 
						buttons: {
							Save: function(){
								if (pop.submitForm('module.save')){
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.load(function(){
										_this.dialog("close");
									});
								}
							},
							Cancel: function(){
								$(this).dialog("close");
							}
						}
					} 
				);
			},
			
			/**
			 * Change position
			 */
			changePosition: function(mid){
				var pop = $.JSNUIWindow
				(
					baseUrl+'administrator/index.php?option=com_poweradmin&view=changeposition&tmpl=component&moduleid='+mid,
					{
						modal : true,
						title : 'Select Position',
						width : 750,
						height: 430,
						open  : function(){
							var _this = $(this);
							var iframe = $(this).find('iframe');
							iframe.load(function(){
								iframe.contents().find('div.position-selector').click(function(){
									setTimeout(function(){
										_this.dialog("close");
									}, 800);
								});
							});
						},
						search: {
							text: '',
							classSet: 'ui-window-searchbar',
							onChange: function(){
								var iframe = $(this).parent().next().find('iframe');
								iframe[0].contentWindow.changeposition.filterResults($(this).val().trim());
							},
							onKeyup: function(){
								//fire change event
								$(this).change();
							},
							onBlur: function(){
							   if ($(this).val().trim() == ''){
						  	   	  $(this).val('search...');
						  	   }
							},
							onFocus: function(){
							   if ($(this).val().trim() == 'search...'){
						  	   	  $(this).val('');
						  	   }
							}
						}
					}
				);
			},
			
			/**
			 * Assign pages
			 */
			assignPages: function(mid){
				var pop = $.JSNUIWindow
				(
					baseUrl+'administrator/index.php?option=com_poweradmin&view=assignpages&tmpl=component&moduleid='+mid,
					{
						modal : true,
						title : 'Assign to Pages',
						width : 550,
						height: 500,
						buttons: {
							'Save': function(){
								if (pop.submitForm('assignpages.save')){
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.load(function(){
										_this.dialog("close");
										$._visualmode.iFrameReload();
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
			 * Component setting
			 */
			componentSetting: function()
			{
				var iframe = $('#jsn-visual-layout-frame');
				iframe.contents().find('span#tableshow').unbind("click").click(function(){
					var wWidth  = $(window).width()*0.85;
					var wHeight = $(window).height()*0.8; 
					var task    = $(this).attr('task');
					var link    = $.base64Decode($(this).attr('editlink'));
					var editMenu= (link.indexOf('com_menus') != -1)?true:false;
					var pop     = $.JSNUIWindow
					(
						baseUrl+'administrator/index.php?'+link, 
						{
							modal:true, 
							width: wWidth, 
							height: wHeight, 
							title: $(this).attr('title'),
							open: function(){
								var iframe = $(this).find('iframe');
								var _this  = $(this);
								iframe.load(function(){
									if (editMenu){
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
											$(this).attr('onClick', "window.parent.jQuery.selectMenuItemType('"+jform+"', '"+iframe.attr('id')+"');");													
										});	
									}	
								});
								
								//bind trigger press enter submit form from child page
							    $(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
									iframe.unbind("load").load(function(){
										_this.dialog("close");								
									});
								});

							},
							buttons: {
								Save: function(){
									if (pop.submitForm(task)){
										var _this  = $(this);
										var iframe = $(this).find('iframe');
										iframe.load(function(){
											_this.dialog("close");
										});
									}
								},								
								Cancel: function(){
									$(this).dialog("close");
								}
							}
						});
				}
			  );			  
			  
			},
			
			selectModuleType: function(position){
				var pop     = $.JSNUIWindow
				(
					baseUrl+'administrator/index.php?option=com_poweradmin&view=selectmoduletypes&tmpl=component&pwadvisual=1&position='+position, 
					{
						modal:  true,
						width:  760, 
						height: 450, 
						title:'Select Module Type',
						search: {
							text: '',
							classSet: 'ui-window-searchbar',
							onChange: function(){
								var iframe = $(this).parent().next().find('iframe');
								iframe[0].contentWindow.changeposition.filterResults($(this).val().trim());
							},
							onKeyup: function(){
								//fire change event
								$(this).change();
							},
							onBlur: function(){
							   if ($(this).val().trim() == ''){
						  	   	  $(this).val('search...');
						  	   }
							},
							onFocus: function(){
							   if ($(this).val().trim() == 'search...'){
						  	   	  $(this).val('');
						  	   }
							}
						}
					} 
				);

			},
			
			/**
			 * Add new module
			 */
			addNewModule: function(eid, position){
				var wWidth  = $(window).width()*0.85;
				var wHeight = $(window).height()*0.8; 
				var pop = $.JSNUIWindow
				(
					baseUrl+'administrator/index.php?option=com_poweradmin&task=selectmoduletypes.setModuleType&pwadvisual=1&eid='+eid+'&position='+position, 
					{
						modal:  true, 
						width:  wWidth, 
						height: wHeight, 
						title: 'Create new module',
						open: function(){
							var iframe = $(this).find('iframe');
							var _this  = $(this);
							iframe.load(function(){
								var newModuleList = iframe.contents().find('#new-modules-list');
								$('.modal-title').css('display', 'none');
								newModuleList.css('border', 'none');
								$('li', newModuleList).css('float', 'left');
								$('li', newModuleList).css('width', '30%');
								$('a',  newModuleList).each(function(){
									var href = $(this).attr('href');
									$(this).attr('href', 'javascript:void(0);').unbind("click").bind("click", function(){
										$.jsnUIWindowChangeUrl(iframe.attr('id'), href+'&tmpl=component&pwadvisual=1');
									});
								});								
							});
							
							//bind trigger press enter submit form from child page
						    $(window).unbind("pressEnterSubmitForm").bind("pressEnterSubmitForm", function(){
								iframe.unbind("load").load(function(){
									_this.dialog("close");
								});
							});
						},
						buttons:{
							Save: function(){
		                    	if (pop.submitForm('item.save')){
		                    		var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.unbind("load").load(function(){
										_this.dialog("close");
									});
		                    	}
							},
							Cancel: function(){
								$(this).dialog("close");
							}
						}
					}
				);
			},
			
			moduleOptions: function(mid){
				var wWidth  = $(window).width()*0.7;
				var wHeight = $(window).height()*0.7;
            	var pop = $.JSNUIWindow
            	(
            		baseUrl+'administrator/index.php?option=com_poweradmin&view=module&layout=edit&tmpl=component&id='+mid, 
            		{
            			modal : true,
            			width : wWidth, 
            			height: wHeight,
            			title : 'Module options',
            			buttons: {
            				'Save': function(){
								if (pop.submitForm('module.save')){
									var _this  = $(this);
									var iframe = $(this).find('iframe');
									iframe.load(function(){
										_this.dialog("close");
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
			
			iFrameReload: function(){
				$._visualmode.showLoading(true);
				$('#jsn-visual-layout-frame')[0].contentWindow.location.reload(true);
			},
			
			/**
			 * Set url ofr iframe
			 */
			setIframeSRC: function( src )
			{
				jsnpwiframeready = false;
				$._visualmode.showLoading(true);
				$('#jsn-visual-layout-frame').attr('src', src);
			},
			
			/**
			 * Refresh render page
			 */
			iFrameRefresh: function()
			{
				$._visualmode.showLoading(true);			
				$('jsn-visual-layout-frame').attr('src', baseUrl + 'administrator/index.php?option=com_poweradmin&view=jsnrender&format=raw');
			},
			/**
			 * Redirect to rawmode page
			 */
			rawmode: function()
			{
				window.location.href = baseUrl + 'administrator/index.php?option=com_poweradmin&view=rawmode&render_url='+$.base64Encode($.jStorage.get('render_url'));
			},
			/**
			 * Change the toolbar buttons
			 */
			changeToolbar: function( url )
			{
				$.jStorage.set('render_url', url);
				var switchMode = $('#toolbar-rawmode').children('.toolbar');
				switchMode.attr('onclick', '');
				switchMode.unbind("click").click(function(){$._visualmode.rawmode();});
				
				var selectTemplate = $('#toolbar-selecttemplate');
				var redirectUrl    = $.base64Encode(window.location.href);				
				selectTemplate.unbind("click").click(function(){
					$.JSNUIWindow
					(
						baseUrl+'administrator/index.php?option=com_poweradmin&view=templates&tmpl=component',
						{
							modal : true,
							title : 'Select Template Style',
							width : 755,
							height: 530
						}
					);
				});
				selectTemplate.children('a').attr('onClick', '');
			},
			/**
			 * Show messages
			 */
			showMessage: function(msg){
				$.JSNUIMessage(msg, 3000);
			},
			/**
			 * Show loading...
			 */
			showLoading: function(_true)
			{
				var body = $('body');
				if (_true){
					showLoading = body.showLoading({autoClose:false});
				}else if(typeof showLoading != "undefined"){
					if (typeof showLoading.remove == 'function'){
						clearInterval(vsInternalLoad);
						showLoading.remove();
					}
				}else{
					$('.ui-widget-overlay').remove();
				}
			}
		}
	});	
	
})(JoomlaShine.jQuery);

