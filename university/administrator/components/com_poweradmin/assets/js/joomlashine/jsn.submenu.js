/**
* 
* Created an submenu for element and show under it.
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html

Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
		- jsn.mouse.check.js
*/

//add arrays submenu objects
var jsnSubmenuObjs = new Array();
(function($){
	var intervalCheck = null;	
	/**
	*
	* Plugin to make HTML element menu
		[root]: 
			<tagName ></tagName> (Anywhere)
		[self]: (Children element of <body>)	  
			<div  (Description: show-down for menu)>  
			<ul >
				<li [item]>
				</li >
			</ul >
		</div>
	*
	* @param: (object) is object to extend jQuery object
	* @return: jQuery object   
	*/
	$.fn.jsnSubmenu = function(ops){
		//Extend object option
		var options = $.extend
		({
			rebuild   : false,
			rightClick: true,
			attrs     : {}
		}, ops);	
		//Set jQuery element to root
		var root  = $(this);

		//Check if exist id attribute then get it and check exist menu build in array instance
		if ( root.attr('id') !== undefined ){
			var objId = root.attr('id');
		}else{
			//Get random id attribute 
			var date = new Date();
			var objId = 'jsn-submenu-' + Math.random().toString() + date.getTime().toString(); 
			root.attr('id', objId);
		}

		if ( options.rebuild ){
			//remove old submenu 
			if ( jsnSubmenuObjs[objId] !== undefined ){
				jsnSubmenuObjs[objId].remove();
				delete jsnSubmenuObjs[objId];
			}
		}
		
		//check if menu for this element has exists then return it
		if ( jsnSubmenuObjs[objId] !== undefined && !options.rebuild){
			return jsnSubmenuObjs[objId].convertTo(this);
		}

		//created new submenu
		var self = $('<div/>').appendTo('body').css('position', 'absolute');

		if ( options.attrs !== undefined ){
			for(k in options.attrs){
				self.attr(k, options.attrs[k]);
			}
		}
		//default class for menu		
		self.addClass('jsnpw-submenu');
		self.addClass('jsn-bootstrap');
		
		var wrapper = $('<div/>').appendTo(self);
		wrapper.addClass('dropdown');
		//add menu elements type <ul> <li>
		var elements =  $('<ul/>').appendTo(wrapper);
		elements.addClass('dropdown-menu');
		elements.css('display','block');	
		
		
		/**
		* 
		* Get element parent of root menu
		* 
		* @return: jQuery element
		*/
		this.getParentRoot = function(){
			return root.parent();
		};
		/**
		* 
		* Get root element
		*
		* @return: jQuery element
		*/
		this.getRoot = function(){
			return root;
		};
		/**
		* 
		* Get Self
		*
		* @return: jQuery element
		*/
		this.getSelf = function(){
			return self;
		};
		/**
		*
		* Set css atrributes for menu
		*
		* @param: string key
		* @param: string value
		* @return: Set attribute to HTML element
		*/
		this.cssHooks = function(key, value){
			self.css(key, value);
		};
		/**
		*
		* Set attributes for root
		*
		* @param: string name
		* @param: string value
		* @return: Set attribute to HTML element
		*/
		this.setRootAttr  = function(name, value){ 
			root.attr(name, value); 
		};
		/**
		* 
		* Get attributes of root
		*
		* @param: string name
		* @return: Attribute of HTML element
		*/
		this.getRootAttr  = function(name){ 
			return ( root.attr(name) !== undefined ? root.attr(name) : null );
		};
		/**
		* 
		* Check root menu is has attribute
		*
		* @param: string name
		* @return: boolean (true/false)
		*/
		this.rootHasAttr  = function(name){ 
			return ( root.attr(name) !== undefined );
		};
		/**
		* 
		* Check root menu is has class
		*
		* @param: string className
		* @return: boolean (true/false)
		*/
		this.rootHasClass = function(className){ 
			return root.hasClass(className);
		};
		/**
		* 
		* Add class to root menu
		*
		* @param: string className
		* @return: None/Add atrribute for HTML element
		*/
		this.rootAddClass = function(className){ 
			root.addClass(className);
		};
		/**
		* 
		* Remove an class of root menu
		* 
		* @param: string className
		* @return: None/remove atrribite of HTML element
		*/
		this.rootRemoveClass = function(className){ 
			root.removeClass(className);
		};		
		/**
		* 
		* Add new item for menu
		*
		* @param: string text ( caption )
		* @param: array/object attributes for element
		* @return: jQuery element
		*/		 
		this.addItem  = function(text, attrs)
		{
			var item = $('<li/>').appendTo(elements);
			var txt  = $('<a/>').appendTo(item).text(text).attr('href','javascript:void(0)'); 
			for(key in attrs){
				item.attr(key, attrs[key]);
			}
			
			item.attr('idt', text.trim().toLowerCase());
			item.attr('hidestat', '0');
			elements.append(item);

			return this.addSwapEvents( item );
		};
		
		/**
		 * 
		 * Add functions to swap events
		 *
		 * @param: (jQuery Object) (mItem) is jquery element
		 * @return: return jQuery Element after added swap functions
		 */
		this.addSwapEvents = function( mItem ){
			/**
			 * 
			 * Disable an jQuery event
			 * 
			 * @param: (String) (eventName) is name of event
			 * @return: jQuery element after remove event
			 */
			mItem.disableEventHandler = function(eventName){
				if (mItem.data(eventName)){
					mItem.unbind(eventName);
				}
				
				return mItem;
			};
			/**
			 * 
			 * Enable an jQuery event
			 * 
			 * @param: (String) (eventName) is name of event
			 * @return: jQuery element after add event
			 */
			mItem.enableEventHandler = function(eventName){
				if (mItem.data(eventName)){
					mItem.disableEventHandler(eventName).bind(eventName, mItem.data(eventName));
				}
				
				return mItem;			
			};
			
			/**
			 * 
			 * Add jQuery event to element and store it
			 *
			 * @param: (string) (eventName) is name of event
			 * @param: (jQuery function) (proxy) is proxy function axecute after trigger this event
			 * @return: Set data and set event
			 */
			mItem.addEventHandler = function(eventName, proxy){
				mItem.data(eventName, proxy);
				mItem.disableEventHandler(eventName).bind(eventName, proxy);
				
				return mItem;
			};
			/**
			 * 
			 * Remove an event
			 *
			 * @param: (string) (eventName) is name of event
			 * @return: Remove jQuery event and data store
			 */
			mItem.removeEventHandler = function(eventName){
				mItem.data(eventName, null);
				mItem.bind(eventName);
				
				return mItem;
			};
			/**
			 * 
			 * Store proxy function
			 *
			 * @param: (String) (proxyName) is name of proxy to store
			 * @param: (function) (proxyFunc) is proxy function
			 * @return: Store to jQuery element
			 */
			mItem.proxyStore = function(proxyName, proxyFunc){
				mItem.data(proxyName, proxyFunc);
			};
			/**
			 * 
			 * Change proxy function
			 *
			 * @param: (string) (eventName) is name of event
			 * @param: (string) (proxyName) is name of proxy store
			 * @return: Set new proxy function to event
			 */
			mItem.changeProxy = function(eventName, proxyName){
				if (mItem.data(proxyName)){
					mItem.disableEventHandler(eventName).bind(eventName, mItem.data(proxyName));
				}
			};
						
			return mItem;
		};
		/**
		 * 
		 * Hide all items
		 *
		 * @return: Change HTML
		 */
		this.hideAllItems = function(){
			$('>li', elements).hide();
		};
		
		/**
		 * 
		 * Show all items
		 *
		 * @return: Change HTML
		 */
		this.showAllItems = function(){
			$('li,a', elements).attr('hidestat', '0');
			$('li,a', elements).show();			
		};
		/**
		*
		* Add parent HTML element 
		*
		* @param: string text ( caption )
		* @param: Array/Object attributes for element
		* @return: jQuery element 
		*/
		this.addParentItem = function(text, attrs){			
			var parentItem   = this.addItem(text, attrs).append('<span class="jsn-submenu-arrow" style="top:5px;"></span>');
			parentItem.addClass('dropdown-submenu');
			var moreElements = $('<ul />', 
			{
				'class': 'dropdown-menu '
			}).appendTo(parentItem)
			.css({'z-index'   : $.topZIndex()
			});
			
			/**
			* Event mouse enter/leave
			*/			
			parentItem.mouseenter(function(e){				
				moreElements.css({
					'left': '',
					'top' : ''
				}).show();
				
				if (self.offset().left + self.width() + moreElements.width() > $(window).width()){
					parentItem.removeClass('pull-left').addClass('pull-left');
				}

			});
			/**
			* Add subitem to parent item
			*
			* @param: string text ( caption )
			* @param: Array/Object attributes for element
			* @return: jQuery element
			*/
			parentItem.addItem = function(text, attrs){
				var item = $('<li/>').appendTo($('<a/>').attr('href','javascript:void(0)').text(text)).appendTo(moreElements);
				var txt  = $('<a/>').appendTo(item).text(text).attr('href','javascript:void(0)'); 
				for(key in attrs){
					item.attr(key, attrs[key]);
				}
				
				item.attr('idt', text.trim().toLowerCase());
				item.attr('hidestat', '0');
				moreElements.append(item);

				return $this.addSwapEvents( item );

			};
			
			/**
			* 
			* Add divider item
			* 
			* @return: None/Add HTML element
			*/
			parentItem.addDivider = function(label){
				label = label != undefined? label : '';
				return  $('<li/>').appendTo(moreElements)
								  .addClass('divider').attr('idt', label.trim().toLowerCase());
			}; 

			return parentItem;
		};
		/**
		* 
		* Add divider item
		* 
		* @return: None/Add HTML element
		*/
		this.addDivider = function(label){
			label = label != undefined? label : '';
			return  $('<li/>').appendTo(elements)
							.addClass('divider').attr('idt', label.trim().toLowerCase());;
		};
		/**
		* 
		* Remove divider
		*
		* @param: None/Remove HTML element
		*/
		this.removeDividers = function(){
			$('.divider', elements).remove();
		};
		/**
		* 
		* Hide divider
		*
		* @return: None/hide HTML element
		*/
		this.hideDividers = function(){
			$('.divider', elements).css('display', 'none');
		};
		/**
		* 
		*
		* Show dividers
		*
		* @param: None/Show HTML element
		*/
		this.showDividers = function(){
			$('.divider', elements).css('display', 'block');
		};
		/**
		* 
		* Get an menu item
		*
		* @param: string text
		* @return: jQuery Object
		*/
		this.getItem = function(text, index, parent)
		{			
			var found = [$({})],
			    i = 0;
			index = ( index == undefined ) ? 0 : index;
			parent = ( index == undefined ) ? '' : parent;
			if (parent) {
				found = $('[idt="' + text.trim().toLowerCase() + '"]', parent);				
			}else{
			found = $('[idt="' + text.trim().toLowerCase() + '"]', elements);		
			}
			
			return this.addSwapEvents(found);
		};
		/**
		* 
		* Show item
		*
		* @param: (string) text (caption)
		* @return: None/Show HTML elements
		*/
		this.showItem = function(text, index, parent){
			this.getItem(text, index, parent).attr('hidestat', '0');
			return this.getItem(text, index, parent).show();
		};
		/**
		* 
		* Hide item
		*
		* @param: string text (caption)
		* @return: None/Hide HTML elements
		*/
		this.hideItem = function(text, index){
			this.getItem(text, index).attr('hidestat', '1');
			this.getItem(text, index).hide();
		};
		
		/**
		* 
		* Remove item
		* 
		* @param: string text is text of new menu
		* @return: None/Remove HTML elements
		*/
		this.removeItem = function(text)
		{
			this.getItem(text, index).remove();			
		};
		/**
		* 
		* Show menu
		*
		* @param: jQuery object ops
		* @return: None/Show HTML element
		*/
		this.show = function(ops)
		{				
			var _x = root.offset().left;
			var _y = root.offset().top;
			
			var showMenu = ($('li[hidestat="0"]', elements).length) > 0 ? true : false ;			
			if (!showMenu){
				return;
			}
			
			this.hideAll();
			var pos = $.extend({
				x : _x,
				y : _y
			}, ops);
			
			
			if (pos.x + self.width() > $(document).width()-5){
				pos.x = pos.x - self.width();
			}

			if (pos.y + self.height() > $(document).height()-5){
				pos.y = pos.y - self.height();
			}

			this.cssHooks('top',  pos.y);
			this.cssHooks('left', pos.x);

			self.mouseIsInSubmenu = function(){
				var isIn = false;
				self.find('li > ul').each(function(){
					isIn = $(this).mouseIsIn() || isIn;
				});
				return isIn;
			};

			self.trigger("jsnsubmenu.show");
			self.show().css({'height': elements.height(), 'width': elements.width(), 'z-index': '999'});

			clearInterval( intervalCheck );
			intervalCheck = setInterval(function(){
				
				if (!self.mouseIsIn() && !self.mouseIsInSubmenu()){
					clearInterval( intervalCheck );
					if (self.css('display') != 'none'){
						self.hide();
					}
				}
			}, 4000);
		};
		/**
		* 
		* Hide menu
		*
		* @param: jQuery object	     
		* @return: None/Hide HTML elements
		*/
		this.hide = function(options)
		{
			var delay = ( options.timeDelay ? options.timeDelay : 0 );
			if (options.fade){
				self.fadeOut(delay);
			}else{
				self.hide();
			}
		};
		/**
		* 
		* Remove menu
		*
		* @return: None/Remove HTML elements
		*/
		this.remove = function()
		{
			delete jsnSubmenuObjs[objId];
			self.remove();
		};
		/**
		*
		* Convert an old menu to new element
		*
		* @param: jQuery element
		* @return: this
		*/
		this.convertTo = function(_this){
			if (!(_this instanceof jQuery)){
				_this = $(_this);
			}			
			root = _this;
			this.renew = true;
			if (options.rightClick){
				 //show submenu when right-click
				_this.mousedown(function(e) {					
					if (e.which === 3){
						$this.show({x:e.pageX + 10, y:e.pageY + 10});
					}
				});
			}
			root.attr('builtmenu', true);
			return this;
		};
		/**
		* 
		* Check object is new/old
		*
		* @return: boolean (true/false)
		*/
		this.isNew = function(){
			return ( this.renew === undefined );
		};
		/**
		* 
		* Check mouse in in menu
		*
		* @return: boolean (true/false)
		*/
		this.mouseIsIn = function(){			
			var xMin = self.offset().left;
			var yMin = self.offset().top;		
			var xMax = xMin + self.width();
			var yMax = yMin + self.height();
			return ($.jsnmouse.x >= xMin && $.jsnmouse.x <= xMax && $.jsnmouse.y >= yMin && $.jsnmouse.y <= yMax);
		};
		/**
		* 
		* Hide all menu objects created
		*
		* @return: None/Hide all menus
		*/
		this.hideAll = function()
		{
			$('.jsnpw-submenu').hide();
		};

		/**
		* 
		* Show menu when rightclick on root element
		* 
		* @return: None/Show HTML elements
		*/
		this.rightClickShow = function(){
			 //show submenu when right-click
			$(this).mousedown(function(e) {
				if ( e.which === 3 ){
					$this.show({x:e.pageX + 10, y:e.pageY + 10});
					//no show browser context menu
					$(this)[0].oncontextmenu = function() {
						return false;
					}
				}
			});
		};
		//Check setting
		if ( options.rightClick ){
			this.rightClickShow();
		}
		
		/**
		* 
		* Set/save value
		*
		* @param: string name
		* @param: string/number/bool value
		* @return: None/set to array
		*/
		this.setVal = function(name, value){
			if ( !root.data(name) ){
				root.data(name, value);
			}
		};
		
		/**
		* 
		* Get value
		*
		* @param: string name
		* @return: Array value
		*/
		this.getVal = function(name){
			return root.data(name);
		};
		
		//default after menu added then hide
		this.hide({});
		//set id for menu element
		this.setRootAttr('id', objId);
		//store it to array menu objects
		jsnSubmenuObjs[objId] = this;

		root.data('builtmenu', true);
		var $this = this;
		return this;
	};
	/**
	 * An plugin to manager context-menu
	 */
	$.fn.subMenuReferences = function(subMenuOptions, classReferences, __mousedownCallback){
		if (!$(this).length){
			return;
		}
		//Save current element
		this.currentElement = $(this);
		//Store all data of elements 
		this.dataStore = new Array();
	 	//New an contextmenu
	 	this.contextMenu = $(this).jsnSubmenu(subMenuOptions);
	 	/**
	 	 * Convert context menu to reference another elements
	 	 */
		this.setReference = function( el ){
			if (!el instanceof jQuery){
				el = $(el);
			}
			
			//Push data to queue
			this.dataStore[this.currentElement.attr('id')] = this.currentElement.data();
			//Restore all data store to element
			if ( el.attr('id') == undefined ){
				//Get random id attribute 
				var date = new Date();
				var objId = 'jsn-submenu-' + Math.random().toString() + date.getTime().toString(); 
				el.attr('id', objId);
			}
			if (this.dataStore[el.attr('id')] != undefined){
				var dataStore = this.dataStore[el.attr('id')];
				for( k in dataStore ){
					this.contextMenu.setVal(k, dataStore[k]);
				}
			}
			
			this.contextMenu.convertTo(el);
			//Apply context menu methods to current object
			return this.contextMenu;
		};
		/**
		 * Get current context menu
		 */
		this.getMenu = function(){
			return this.contextMenu;
		};
		/**
		 * Init all element avaiables to queue will reference to context-menu
		 */
		if ( /./.test(classReferences) && $(classReferences).length ){
			var $this = this;
			$(classReferences).mousedown(function(e){				
				$this.setReference(this);
				if ( e.which === 3 ){
					if ( typeof __mousedownCallback == 'function'){
						__mousedownCallback(e);
					}
				}
			});
		}

		return this;
	};
	/**
	* Extend functions 
	*/
	$.extend({
		/**
		* jsnSubmenu namespace
		*/
		jsnSubmenu: {
			/**
			* 
			* Hide all menu
			* 
			* @return: None/Hide all HTML menu elements
			*/
			hideAll: function(){
				$('.jsnpw-submenu').hide();
			},
			/**
			* 
			* Remove menu by class
			*
			* @param: string className
			* @return: None/Remove HTML elements
			*/
			removeSubpanelByClass: function(className){
				for(k in jsnSubmenuObjs){
					if (jsnSubmenuObjs[k] instanceof jQuery){
						if(jsnSubmenuObjs[k].hasClass(className)){
							jsnSubmenuObjs[k].remove();
							delete jsnSubmenuObjs[k];
						}
					}
				}
				$('.'+className).remove();
			}
		}
	});
})(JoomlaShine.jQuery);