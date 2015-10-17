/**
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
       - menuitem.jquery.js
       - window.js       
**/

if (typeof(JoomlaShine.jQuery) == undefined){
	var JoomlaShine = {};
	JoomlaShine.jQuery = jQuery.noConflict();
}

(function($){	
	/**
	 * 
	 * Check response if redirected to logon page then refresh current page
	 * 
	 * @param: (string) (res) response text when ajax completed
	 * @return: None
	 */
	$.checkResponse = function(res){
		try{
			$('input[type="hidden"]', res).each(function(i){
				if ($(this).attr('name') == 'task' && $(this).val() == 'login'){
					window.location.reload(true);
				}
	        });
		}catch (e) {
			// TODO: handle exception
		}
		
	};
	
	/**
	 *
	 * Route call function to add new module in rawmode/visualmode
	 *
	 * @param: (string) (eid) 
	 * @param: (string) (position) template position
	 * @return: None
	 */
	$.addNewModule = function(eid, position){
		$.closeAllJSNWindow();
		if ($._menuitems.mode == 'visualmode'){
			$._visualmode.addNewModule(eid, position);
		}else{
			if (JSNGrid != undefined){
				JSNGrid.newModule(eid, position);
			}
		}
	};
	
	/**
	 * 
	 * Add new menu item
	 *
	 * @param: (string basecode64) (data) params
	 * @param: (string) (menutype) joomla menutype
	 * @param: (string) (menutypeid) joomla menutypeid
	 * @param: (string) (parentid) joomla parent menuid of new menu
	 * @return: None
	 */
	$.addNewMenuItem = function(data, menutype, menutypeid, parentid){
		$.closeAllJSNWindow();
		$._menuitems.addMenuItem(data, menutype, menutypeid, parentid);
	};
	
	/**
	 * 
	 * Help select menu item type and return to current page
	 *
	 * @param: (string basecode64) (data) is params
	 * @param: (string) (iframeid)
	 * @return: None
	 */
	$.selectMenuItemType = function(data, iframeid){
		var JFORM = $.parseJSON($.base64Decode(data));
		$.post
		(
			baseUrl + 'administrator/index.php?option=com_menus&view=item&task=item.setType', 
			{
				jform:JFORM, 
				fieldtype:'type'
			}
		).success(function(){
			$('#'+iframeid)[0].contentWindow.location.reload(true);
		}).error(function(msg){
			$.JSNUIMessage(msg, 1000);
		});
	};
	
	/**
	 *
	 * Convert array to json data
	 *
	 * @param: (Array) (arr) array need convert to json
	 * @return: (string) json format
	 */
	$.arrayToJSON = function(arr){
		var json = new Array();
		var i = 0;
		for(k in arr){
			if (typeof arr[k] != 'function'){
				if (typeof arr[k] == 'Array' || typeof arr[k] == 'object'){
					json[i] = '"'+k+'":'+$.arrayToJSON(arr[k]);
				}else{
					json[i] = '"'+k+'":"'+arr[k]+'"'; 
				}
				i++;
			}
		}
		return '{'+json.join(',')+'}';
	};
	/**
	 * 
	 * Replace all htmlspecialchars to html
	 *
	 * @param: (String)
	 * @return: String
	 */
	$.unhtmlspecialchars = function( string ){
		return string.replace(/&amp;/g, '&' )
				     .replace(/&#039;/g, '\'' )
				     .replace(/&quot;/g, '\"' )
				     .replace(/&lt;/g, '<')
				     .replace(/&gt;/g, '>')
				     .replace(/&#013;/g, '\n');
	};
	/**
	*	
	 * Set position for module (ajax request)
	 * 
	 * @param: (string) (moduleid) is joomla module id
	 * @param: (string) (position) is joomla template position
	 * @return: None
	*/
	$.setPosition = function(moduleid, position){
		$.post
		(
			baseUrl+'administrator/index.php?option=com_poweradmin&view=changeposition&task=changeposition.setPosition',
			{
				moduleid: moduleid,
				position: position
			}
		).success( function(res){
			$.checkResponse( res );
			var i = 0;
			for(k in moduleid){
				if ( typeof moduleid[k] !== 'function' && moduleid[k] != undefined){					
					window.parent.JoomlaShine.jQuery.multipleDrag
					(
						{
							element    : moduleid[k]+'-jsnposition',
							toElement  : position,
							dropElement: true,
							dragShow   : false,
							cloneData  : true,
							keyPrefix  : true,
							delayDrag  : true,
							delayTime  : 300*i++,
							timeMoving : 1000,
							end        : function(obj){								
								if ( window.parent.JSNGrid != undefined ){
									
									obj['item']
									.removeClass('jsn-module-multiple-select')
									.unbind("click").click(function(e){
										if ( e.which == 1 ){
											if ( e.ctrlKey ){
												if ($(this).hasClass('jsn-module-multiple-select')){
													$(this).removeClass('jsn-module-multiple-select');
												}else{
													$(this).addClass('jsn-module-multiple-select');
												}
											}else{
												window.parent.JSNGrid.editModule( $(this) );
											}
										}
									});

									window.parent.JSNGrid.moveObjItem( obj['fromPos'], obj['toPos'], obj['mId'] );
									window.parent.JSNGrid.toActivePosition( obj['toPos'] );
									window.parent.JSNGrid.toInactivePosition( obj['fromPos'] );
									window.parent.JSNGrid.toInactivePosition( obj['toPos'] );
									window.parent.JSNGrid.buildModulesContextMenu( obj['goalElement'].find('div.poweradmin-module-item:last'), false );
									window.parent.JSNGrid.initEvents();
									if (!window.parent.JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || !window.parent.JSNGrid.publishing.cookie.isEnableUnpublished() ){
										window.parent.JSNGrid.hideEmptyPositions();
									}
								}
							}
						}
					);
				}else{
					return;
				}
			}	
		}).error(function(msg){
			$.JSNUIMessage(msg, 1000);
		});
	};
	
	/**
     *	
	 * Set default template (ajax request)
	 * 
	 * @param: (string) (itemID) is joomla template id
	 * @return: None
	 */
	$.setTemplate = function(temID){
		$.post
		(
			baseUrl+'administrator/index.php?option=com_poweradmin&view=templates&task=templates.setDefaultTemplate&id='+temID
		).success(function(){
			window.parent.jQuery.selectedTemplate();
		}).error(function(msg){
			$.JSNUIMessage(msg, 1000);
		});
	};
	
	/**
	 *
	 * Route call mode when select template is saved
	 *
	 * @return: None
	 */
	$.selectedTemplate = function(){
		$.closeAllJSNWindow();
		setTimeout(function(){
			if ($._visualmode != undefined){
				$._visualmode.iFrameReload();
			}else{
				$('body').showLoading({autoClose:false, showLoadingRate:true});
				window.location.reload(true);
			}
		}, 200);
	};
	
	/**
	 *
	 * Change url of window
	 *
	 * @param: (string) (uiIframe) is element id of iframe
	 * @param: (string) (src) is url need request to
	 * @return: None
	 */
	$.jsnUIWindowChangeUrl = function(uiIframe, src){
		if ($('iframe#'+uiIframe).length > 0){
			$('iframe#'+uiIframe).attr('src', src);
		}
	};
	
	/**
	 *
	 * Add an new trigger. This function will bi call from child page
	 * 
	 * @param: (string) (_strigger) is string name trigger need to add
	 * @param: (string) (_elementHandler) is ID of element need to add trigger
	 * @return: EventListener
	*/
	$.addTriggerHandler = function(_trigger, _elementHandler){
		if (_elementHandler !== undefined){
			$(_elementHandler).trigger(_trigger);
		}else{
			$(window).triggerHandler(_trigger);
		}
	};
	
	/**
	 * 
	 * Plugin to add overlay loading 
	 *
	 * @param: jQuery object
	 * @return: jQuery object element
	 */
	$.fn.showLoading = function(ops){
		//Option and overwrite option. jQuery extend 
		var _ops = $.extend
		(
			{
				left           : 0,
				top            : 0,
				width          : $(document).width(),
				height         : $(document).height(),
				zIndex         : $.topZIndex(),
				showImgLoading : true,
				showLoadingRate: false,
				autoClose      : true,
				closeTimeout   : 8000,
				removeall      : false
			}, 
			ops
		);		
		if ( _ops.removeall ){
			$('.ui-widget-overlay').remove();
			return;
		}
		if ($('body').children('.ui-widget-overlay').length > 0){
			return;
		} 
		if ( $(this).find('.ui-widget-overlay').length == 0 ){
			var loading = $('<div />', {
				              'class' : 'ui-widget-overlay'
			               })
			               .css({
			               		'top'    : _ops.top,
			               		'left'   : _ops.left,
			               		'width'  : _ops.width,
			               		'height' : _ops.height,
			               		'z-index': _ops.zIndex	
			               	})
			               .appendTo($(this));

			//Add image loading
			if ( _ops.showImgLoading ){

				if ($('.ui-widget-overlay').find('.img-box-loading').length){
					$('.ui-widget-overlay').find('.img-box-loading').remove();
				}

				var imgBoxLoading = $('<div />', {
					                   'class' : 'img-box-loading'
				                    })
				                    .appendTo($('.ui-widget-overlay'))
	                                .css({
	                                	'position': 'relative',
	                                	'top'     : $(this).height()/2-12+'px',
	                                	'left'    : $(this).width()/2-12+'px'
	                                });

				$('<img />', {
					'src' : baseUrl+'plugins/system/jsnframework/assets/joomlashine/images/icons-24/icon-24-dark-loading-circle.gif'
				})
				.appendTo(imgBoxLoading)
	            .css({
            		'position': 'relative',
            		'left'    : '12px',
            		'top'     : '12px'
	            });

			    if (_ops.showLoadingRate){
			    	var rateLoading = $('<div />', {
			    		'id'   : 'rate-loading', 
						'class': 'rate-loading'
			    	}).css
					({
					 		'position': 'relative',
					 		'left'    : $(this).width()/2 - 11 + 'px',
					 		'top'     : $(this).height()/2 - 10 + 'px'
					}).appendTo($('.ui-widget-overlay'));
					
			    	var processbar = $('<div />', {
						'id'    : 'progressbar',
						'class' : 'progressbar'
					}).appendTo(rateLoading);
			    }
			}
		}else{
			var loading = $('.ui-widget-overlay');
		}
		
		/**
		 *
		 * Remove loading on element
		 *
		 * @return: None
		 */
		this.remove = function(){
			loading.remove();
		};
		/**
		 * 
		 * Bind event window resize
		 *		 
		 */
		$(window).resize(function(){
			loading.css({
				'width' : 0,
				'height': 0
			}).css({
				'width' : $(document).width(),
				'height': $(document).height()
			});
			if ( loading.find('div.img-box-loading').length ){
				loading.find('div.img-box-loading').css
				(
					{
						'position': 'relative',
						'top'     : $(this).height()/2-12+'px',
						'left'    : $(this).width()/2-12+'px'
					}
				);
			}
			if ( loading.find('div#rate-loading').length ){
				loading.find('div#rate-loading').css
				(
					{
				 		'position': 'relative',
				 		'left'    : $(this).width()/2 - 11 + 'px',
				 		'top'     : $(this).height()/2 - 10 + 'px'
				 	}
				);
			}
		});
		//Auto close after 8 second
		if (_ops.autoClose){
			setTimeout(function(){
				loading.remove();
			}, _ops.closeTimeout);
		}
		return this;
	};
	/**
	 * 
	 * Plugin to add images show status
	 *
	 * @param: (array) options
	 * @return: Add HTML 
	 */
    $.fn.showImgStatus = function( options ){
    	$(this).find('span.ajaxrequeststatus').remove();
    	if (options  == "remove"){
    		return;
    	}
		var ops = $.extend({
			status   : 'request',
    		position : 'right-middle',
    		css      : undefined
    	}, options);
    	var ajaxrequest = $('<span />', {'id':'ajaxrequeststatus', 'class':'ajaxrequeststatus'}).appendTo($(this)[0]);
    	if ( ops.status == 'request' ){
    		var img = $('<img />', {
    			'src': baseUrl+'plugins/system/jsnframework/assets/joomlashine/images/icons-16/icon-16-loading-circle.gif'
    		});
    	}else if( ops.status == 'success' ){
    		var img = $('<img />', {
    			'src': baseUrl+'administrator/components/com_poweradmin/assets/images/icons-16/ajax-success.gif'
    		});
    	}else if( ops.status == 'error' ){
    		var img = $('<img />', {
    			'src': baseUrl+'administrator/components/com_poweradmin/assets/images/icons-16/ajax-error.gif'
    		});
    	}else{
    		ajaxrequest.html('');
    	}
    	if ( ops.css != undefined ){
    		ajaxrequest.css(ops.css);
    	}else{
    		if ( $(this).css('float') == 'left' ){
    		ajaxrequest.css('position', 'relative');
	    		var css = {
					'left'  : '', 
					'right' : '0px', 
					'top'   : $(this).height()/2 - 10
	    		};
	    	}else{
	    		var css = {
					'left'  : '', 
					'right' : '16px', 
					'top'   : $(this).position().top + $(this).height()/2
	    		};
	    		
	    	}
    		ajaxrequest.css(css);
    	}
    	if ( img != undefined ){
    		img.appendTo(ajaxrequest);
    	}
    	if ( ops.status != 'request' ){
    		setTimeout(function(){
    			ajaxrequest.fadeOut(300, function(){
    				$(window).triggerHandler("imgstatus.remove");
    				ajaxrequest.remove();
    			});
    		}, 500);
    	}
    }
	
	/**
	 * Validate empty required fields
	 */
    $.fn.validateEmptyFields	= function (iframe){
		var _isValid	= true;
		iframe.contents().find('[aria-required="true"]').each(function (){	
    		if (!$(this).val()) {
    			$(this).addClass('invalid');
    			_isValid	= false;
    			return;
    		}
    	});
		return _isValid;
	}
})(JoomlaShine.jQuery);
