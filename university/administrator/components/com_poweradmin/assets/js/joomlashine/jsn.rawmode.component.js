/**
* 
* Rawmode Component
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html 
 Descriptions:
	1. Required files/libs:
		1. jQuery
		2. jsn.submenu.jquery.js
		3. jsnlang.js
		4. languages/{lang}.js
		5. jstorage.js
**/
(function($){
	/**
	* Global variable Instance for rawmode component
	*/
	var GlobalInstances = new Array();
	
	 /**
	  * 
	  * All component not support in this lib will call this function to get object
	  * 
	  * @param: (string) (view) is string of current view
	  * @return: object
	  */
	 $.com404 = function(){
		return new function(){
			  var message_not_supported_yet = $('#show-message-not-supported-yet');
			  this.addContextMenu  = function(){ return false; };
		};
	 };
	
	/**
	  * 
	  * Controller to control build javascript to support rawmode component
	  *
	  * @param: (string) (option) is string value of option viewing
	  * @param: (string) (view) is string value of view viewing
	  * @param: (string) (layout) is string value of view layout file
	  * @param: (string) (mId) is id of menu
	  * @return: Object to support current view
	  */
	 $.JSNComponent = function( option, view, layout, itemid ){
		/**
		 * Variables of current view
		 */
		this.option = option;
		this.view   = view;
		this.layout = layout;
		this.itemid = itemid;	
		/**
		 * jQuery variable reference to component container
		 */		
		this.container = $('#jsn-component-details');
		/**
		 * jQuery variable reference button component mode
		 */
		this.mode = $('#component-manager');
		 /**
		  * Array store all switch element show/hide
		  */
		this.switchElements;
		 /**
		  * HTML class to filter all switch element on document
		  */
		this.switchClassApproved = 'element-switch';
		/**
		 * HTML class to filter all element approved context menu
		 */
		this.classApprovedContextMenu = 'contextmenu-approved';
		/**
		 * Config array store
		 */
		this.dataStore    = new Array();
		/**
		 * Set config field to array fields
		 */
		this.setData  = function( name, value ){
			this.dataStore[name] = value;
		};
		/**
		 * Get config field in array store
		 */
		this.getData = function( name ){
			return this.dataStore[name];
		};
		/**
		 * Set params
		 */
		this.setParams = function( pName, pValue ){
			var param = new Array(1);
			param[pName] = pValue;
			this.setData('params', param);
		};
		/**
		 * Get config fields
		 */
		this.getDataStore = function( type )
		{
			if (type == 'Array'){
				return this.dataStore;
			}else{
				return $.arrayToJSON( this.dataStore );
			}
		};
		//Scan to remove all link attributes of a tag
		this.scanRemoveLinks = function(){
			this.container.find('a').each(function(){
				$(this).removeAttr('href').removeAttr('onclick');
			});
		};
		/**
		 * Ajax request task function
		 */
		this.ajaxRequest = function(){
			var com = this;
			$.post
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=component.request', 
				{
					data: com.getDataStore()
				}
			).success(function( response ){
				$.checkResponse( response );
				if ( response == 'success' ){
					com.setData( 'requestTask', 'brankNewData' );
					com.ajaxRequest();
				}else if ( com.getData('requestTask') == 'brankNewData' ){
					com.container.html(response);
					var JSNComponent = new $.JSNComponent( com.option, com.view, com.layout, com.itemid );
					JSNComponent.__destruct();
					JSNComponent.__construct( com.option, com.view, com.layout, com.itemid );
				}
			});
		};
		/**
		 * Default function to init all variables
		 */
		this.initVariables   = function(){ return false; };
		/**
		 * Get context menu
		 */
		this.getContextMenu = function(){
			return this.container.jsnSubmenu({rebuild: true, rightClick:false, attrs:{'class': 'rawmode-subpanel'}});
		};
		/**
		 * Default function to add context menu
		 */
		this.addContextMenu  = function(){ return false; };
		/**
		 * Add event to check show/hide context when click on document
		 */
		this.addEventsContextmenu = function(){
			if ( this.contextMenu != undefined ){
				var $this = this;
				this.contextMenu.unbind("component.context.show").bind("component.context.show", function(e){
					if ( $.browser.msie ){
						this.container.unbind("click").bind("click", function(e){
							if ( !$(e.target).parents('div.'+$this.classApprovedContextMenu).length && !$(e.target).hasClass($this.classApprovedContextMenu) ){
								$this.contextMenu.hide({});
							}
						});
					}else{
						$('body').unbind("click").bind("click", function(e){
							if ( !$(e.target).parents('.'+$this.classApprovedContextMenu).length && !$(e.target).hasClass($this.classApprovedContextMenu) ){
								$this.contextMenu.hide({});
							}
						});
					}
				});
			}
		};
		/**
		 * Show switch elements
		 */
		this.showUnpublished = function(){			
			for( k in this.switchElements ){
				if ( (this.switchElements[k] instanceof JoomlaShine.jQuery || this.switchElements[k] instanceof jQuery) && this.switchElements[k].length ){					
					this.switchElements[k].removeClass('hide-item').addClass('display-item');
				}
			}
		};
		/**
		 * Hide switch elements
		 */
		this.hideUnpublished = function(){
			for( k in this.switchElements ){
				if ( (this.switchElements[k] instanceof JoomlaShine.jQuery || this.switchElements[k] instanceof jQuery) && this.switchElements[k].length ){			
					if ( !this.switchElements[k].hasClass('display-default') ){
						this.switchElements[k].removeClass('display-item').addClass('hide-item');
					}
				}
			}
		};
		/**
		 * Scan all switch element store to array
		 */
		this.scanSwitchElements = function(){			
			//Scan all elements switch on/off
			this.switchElements = new Array( $( '.'+this.switchClassApproved ).length );
			var com = this;
			var i=0;
			this.container.find( '.'+this.switchClassApproved ).each(function(){
				com.switchElements[i] = $(this);
				i++;
			});
		};
		/**
		 * Load script to support layout
		 */
		this.loadScript = function( option, view, layout ){
			/**
			 * 
			 * Load language file
			 *
			 * @param : (string) lang is string name of language file ( like: en, fr, vn, ... (name of language file == prefix language joomla setting))
			 * @return: None/ get javascript to your page
			 */
			var scriptName = '';
			if (view != '' && layout != ''){
				scriptName = view+'_'+layout;
			}else{
				scriptName = view;
			}
			$.post
			(
				baseUrl+'administrator/index.php?option=com_poweradmin&task=component.checkScript', 
				{
					scriptFolder: option,
					scriptName  : scriptName
				}
			)
				.success(function( response ){
				//$.checkResponse( response );
				if ( response == 'Not Found' ){
					$(window).triggerHandler('jsn.script.loaded.error');
				}else{
					$.getScript
					(
						response, 	function( data, textStatus ){
							if ( textStatus == 'success' ){
								$(window).triggerHandler('jsn.script.loaded.success');
							}else{
								$(window).triggerHandler('jsn.script.loaded.error');
							}
						}
					);
				}
			})
			.error(function(msg){
				$(window).triggerHandler('jsn.script.loaded.error');
			});
		};
		/**
		   * Save cookie mode
		   *
		   * @param: (boolean) (value) is on/off mode
		   * @return: Save value of current mode
		   */
		 this.setMode = function( value ){
			$.jStorage.set('rawmode_component_enabled', value);
		};
		/**
		   * Get cookie mode
		   *	 	   
		   * @return: Boolean value of current mode
		   */
		this.getMode = function(){
			  return ( $.jStorage.get('rawmode_component_enabled') == null ? false : $.jStorage.get('rawmode_component_enabled') );
		};
		/**
		  * 
		  * Apply an object to this
		  *
		  * @param: (jQuery object) (object) is javascript object
		  * @return: Apply all methods to this
		  */
		 this.apply = function( object ){
		 	for( k in object){
		 		this[k] = object[k];
		 	}
		 };
		 /**
		  * 
		  * Constructor for this
		  *
		  * @param: (string) (option) is string value of option viewing
		  * @param: (string) (view) is string value of view viewing
		  * @return: Object to support current view
		  */
		this.__construct = function( option, view, layout, mId ){
			/**
			 * Get object for each component
			 */
			
			/**
		 	 * Get object extension and apply to current object
		 	 */
			if ( option != 'com404' ){
				var scriptName = '';
				if (option != ''){
					scriptName = option;
				}
				if (option != '' && view != ''){
					scriptName = option+'_'+view;
				}
				if (option != '' && view != '' && layout != ''){
					scriptName = option+'_'+view+'_'+layout;
				}
				
				if ( typeof $[scriptName] == 'function' ){
					this.mode.show();
					this.apply( new $[scriptName]( mId ) );
				}else{
					this.loadScript( option, view, layout );
					
					/**
					 * After load script success then build object
					 */
					$(window).unbind('jsn.script.loaded.success').bind('jsn.script.loaded.success', function(){
						var JSNComponent = new $.JSNComponent( option, view, layout, mId);
						JSNComponent.__destruct();
						JSNComponent.__construct( option, view, layout, mId );
					});
					/**
					 * After can load script then build object 404
					 */
					$(window).unbind('jsn.script.loaded.error').bind('jsn.script.loaded.error', function(){
						var JSNComponent = new $.JSNComponent( 'com404', view, layout, mId);
						JSNComponent.__destruct();
						JSNComponent.__construct( 'com404', view, layout, mId );
					});
				}
			}
			/**
	 	 	 * Component not default of joomla or not support then redirect to com404
	 	 	 */
			else{
				this.mode.hide();
				this.apply( new $.com404() );
			}
			/**
			 * Scan to remove links
			 */
			this.scanRemoveLinks();
			/**
			 * Scan switch element
			 */
			this.scanSwitchElements();
			/**
			 * Init all variables
			 */
			this.initVariables();
			 /**
			  * Call view function to build context menu
			  */
			this.addContextMenu();
			/**
			 * Call add event show/hide when click on document
			 */
			this.addEventsContextmenu();
			 /**
			 * Click to show/hide module options
			 */
			var com = this;
			this.mode.unbind("click").click(function(){
				if ( !$(this).hasClass('btn-enabled') ){
					com.setMode(true);
					com.mode.addClass('btn-success');
					com.mode.removeClass('btn-disabled').addClass('btn-enabled').attr('title', JSNLang.translate('TITLE_HIDE_DISABLED_COMPONENT_ELEMENTS') );
					com.container.addClass('active-mode');
					com.showUnpublished();
				}else{
					com.setMode(false);
					com.mode.removeClass('btn-success');
					com.mode.removeClass('btn-enabled').addClass('btn-disabled').attr('title', JSNLang.translate('TITLE_SHOW_DISABLED_COMPONENT_ELEMENTS'));
					com.container.removeClass('active-mode');
					com.hideUnpublished();
				}
			});
			
			//Restore show/hide component options
			if ( this.getMode() ){
				this.mode.addClass('btn-success');
				this.mode.removeClass('btn-disabled').addClass('btn-enabled').attr('title', JSNLang.translate('TITLE_HIDE_DISABLED_COMPONENT_ELEMENTS') );
				this.container.addClass('active-mode');
				this.showUnpublished();
			}else{
				this.mode.removeClass('btn-success');
				this.mode.removeClass('btn-enabled').addClass('btn-disabled').attr('title', JSNLang.translate('TITLE_SHOW_DISABLED_COMPONENT_ELEMENTS'));
				this.container.removeClass('active-mode');
				this.hideUnpublished();
			}
			return this;
		};
		/**
		  * 
		  * Clear object, clear all variables and method not have in current object
		  */
		this.__destruct = function(){
			for( k in this ){
				if ( !/getContextMenu||addEventsContextmenu||initVariables||ajaxRequest||getDataStore||setParams||getData||setData||configFields||switchClassApproved||switchElements||scanSwitchElements||container||componentMode||setMode||getMode||apply||addContextMenu||showUnpublished||hideUnpublished||__construct||__destruct||getInstance/.test(k)){
					if ( k == 'contextMenu' ){
						this[k].remove();
					}
					delete this[k];
				}
			}
		};
		/**
		  * 
		  * Get Instance object after built
		  *
		  * @param: (string) (option) is string value of option viewing
		  * @param: (string) (view) is string value of view viewing
		  * @return: Object to support current view
		  */
		this.getInstance = function( option, view, layout, itemid){
			if (GlobalInstances['JSNComponent'] == undefined){
				GlobalInstances['JSNComponent'] = this.__construct( option, view, layout, itemid );
			}else{
				GlobalInstances['JSNComponent'].option = option;
				GlobalInstances['JSNComponent'].view   = view;
				GlobalInstances['JSNComponent'].layout = layout;
				GlobalInstances['JSNComponent'].itemid = itemid;
			}
			return GlobalInstances['JSNComponent'];
		 };

		 return this.getInstance(option, view, layout, itemid);
	};
})(JoomlaShine.jQuery);
