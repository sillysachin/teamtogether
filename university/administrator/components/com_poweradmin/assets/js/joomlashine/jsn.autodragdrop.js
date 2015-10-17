/**
* 
* Auto drag&drop elements
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
**/
(function($){
	/**
	*
	* Auto Drag&Drop 
	*
	* @param: (jQuery object) (_ops) to setting
	* @return: jQuery object 
	*/
	$.autoDragDrop = function(_ops){
		
		// Setting options
		this.settings = {
			element    : $({}),
			goalElement: $({}),
			startX     : 0,
			startY     : 0,
			endX       : 0,
			endY       : 0,
			dragShow   : false,
			dropElement: false,
			timeMoving : 800,
			start: function(obj){
				//TODO:
			},
			end: function(obj){
				//TODO:
			}
		};

		/**
		* 
		* Init and set parameters
		* 
		* @param: (jQuery object) ops to setting
		* @return: None
		*/
		this.init = function( ops ){
			var _ops = $.extend({
				//Set an elementID or jQuery object
				element    : undefined,
				//Set an elementID or jquery object
				toElement  : undefined,
				//When drag to goal will drop element to it
				dropElement: false,
				//Show moving with timer
				dragShow   : true,
				//Delay drag 
				delayDrag  : false,
				//Delay time
				delayTime  : 0,
				//key prefix
				keyPrefix  : false,
				//time moving
				timeMoving : 800,
				//Event start moving
				start      : function(obj){},
				//Event end moving
				end        : function(obj){}
			}, ops);

			// if set with prefix then check prefix options
			if ( _ops.keyPrefix ){
				if ($('#'+_ops.element+'-published').length > 0){
					_ops.element = $('#'+_ops.element+'-published');
				}else if($('#'+_ops.element+'-unpublished').length > 0){
					_ops.element = $('#'+_ops.element+'-unpublished');
				}else{
					console.log( JSNLang.translate( 'MSG_AUTO_DRAGDROP_ELEMENT_NOT_VALID' ) );
					return;
				}
			}
			
			if (!(_ops.element instanceof jQuery)){
				_ops.element = ($(_ops.element).length > 0 ? $(_ops.element) : _ops.element);
			} 
//			if (!(_ops.element instanceof jQuery)){
//				console.log( JSNLang.translate( 'MSG_AUTO_DRAGDROP_ELEMENT_NOT_VALID' ) );
//				return;
//			}
			if (!(_ops.toElement instanceof jQuery)){
				_ops.toElement = ($('#'+_ops.toElement+'-jsnposition').length > 0 ? $('#'+_ops.toElement+'-jsnposition') : _ops.toElement);
			} 
//			if (!(_ops.toElement instanceof jQuery)){
//				if (typeof $._menuitems != undefined){
//					
//					if ($._menuitems.mode == 'rawmode'){
//						var fromPosition = _ops.element.parent().attr('id').replace('-jsnposition', '');
//						_ops.element.remove();
//						var JSNGrid = new $.JSNGrid();
//						JSNGrid.toInactivePosition(fromPosition);
//						JSNGrid.addPosition(_ops.toElement);
//						JSNGrid.loadModuleByPosition(_ops.toElement);
//					}else{
//						_ops.element.remove();
//					}
//				}
//				return;
//			}
			this.settings.element     = _ops.element;
			//Set goal object
			this.settings.goalElement = _ops.toElement;
			this.settings.dragShow    = _ops.dragShow;
			this.settings.dropElement = _ops.dropElement;
			this.settings.timeMoving  = _ops.timeMoving;
			this.settings.start       = _ops.start;
			this.settings.end         = _ops.end;
			if ( _ops.delayDrag ){
				var $this = this;
				setTimeout(function(){
					$this.startDrag();
				}, _ops.delayTime);
			}else{
				this.startDrag();
			}
		};
		/**
		*
		* Get from position
		* 
		* @return: Get position of element
		* 
		*/
		this.getFromPosition = function(){
			//get from position
			if (typeof this.settings.element.offset == 'function'){
				this.settings.startX = this.settings.element.offset().left;
				this.settings.startY = this.settings.element.offset().top;
			}else if(typeof this.settings.element.position == 'function'){
				this.settings.startX = this.settings.element.position().left;
				this.settings.startY = this.settings.element.position().top;
			}			
		};		
		/**
		*
		* Get to position
		*
		* @return: Get position of goal element
		*/
		this.getToPosition = function(){
			//get to position
			if (typeof this.settings.goalElement.offset == 'function'){
				this.settings.endX   = this.settings.goalElement.offset().left;
				this.settings.endY   = this.settings.goalElement.offset().top;
			}else if(typeof this.settings.goalElement.position == 'function'){
				this.settings.endX   = this.settings.goalElement.position().left;
				this.settings.endY   = this.settings.goalElement.position().top;
			}			
		};		
		/**
		*
		* Start moving
		*
		* @return: None 
		*/
		this.startDrag = function(){
			this.settings.element.showImgStatus("remove");
			if (this.settings.dragShow){
				this.settings.start( this.settings.element );
				this.getFromPosition();
				this.getToPosition();
				this.moving();
			}else{
				this.endDrag();
			}
		};
		/**
		*
		* Move HTML element ( Thank to jQuery UI animate function)
		* 
		* @return: None/Set HTML element
		*/
		this.moving = function(){
			var  proxy = 
			this.settings.element.clone(false)
			.appendTo($('body'))
			.css(
				{
					'left'    : this.settings.startX,
					'top'     : this.settings.startY,
					'width'   : this.settings.element.innerWidth(),
					'position': 'absolute',
					'z-index' : $.topZIndex()
				}
			);
			this.settings.element.hide();
			var  $this = this;
			var innerHeight = 0;
			
			if (typeof $._menuitems != undefined){
				if ($._menuitems.mode == 'rawmode'){
					if (this.settings.goalElement.parent().parent().css('display') == 'none'){
						this.settings.goalElement.parent().parent().show();
						var JSNGrid = new $.JSNGrid();
						JSNGrid.eastContentResize();
						this.getToPosition();
					}
				}
			}
			this.settings.goalElement.children().each(function(){
				if ($(this).css('display') != 'none'){
					innerHeight += $(this).height();
				}
			});
			proxy.animate({
				left : this.settings.endX,
				top  : this.settings.endY + innerHeight
			}, this.settings.timeMoving, function(){
				$(this).remove();
				$this.endDrag();
			});
		};
		
		/**
		* End drag (and or drop)
		*/
		this.endDrag = function(){
			if ( this.settings.dropElement ){
				//Drop clone object to new position
				var dropObj = new Array();
				dropObj['item'] = $('<div />', {
					'id': this.settings.element.attr('id'),
					'class': this.settings.element.attr('class'),
					'style': this.settings.element.attr('style')
				}).html( this.settings.element.html() ).css("opacity", "0.05");

				dropObj['item'].appendTo(this.settings.goalElement).end();
				setTimeout(function(){
					dropObj['item'].data('builtmenu', false).animate({
						"opacity" : 1
					}, 500);
				}, 200);
				if (this.settings.element.parent().attr('id') != undefined){
					dropObj['fromPos']     = this.settings.element.parent().attr('id').replace('-jsnposition', '');
				}
				dropObj['toPos']       = this.settings.goalElement.attr('id').replace('-jsnposition', '');
				dropObj['mId']         = this.settings.element.attr('id').split('-')[0];
				dropObj['moduleTitle'] = this.settings.element.find('div.poweradmin-module-item-inner-text').text();
				dropObj['goalElement'] = this.settings.goalElement;
				this.settings.element.remove();
				this.settings.end( dropObj );
			}else{
				this.settings.element.show();
				var dropObj = new Array();
				dropObj['item'] = this.settings.element;
				this.settings.end( dropObj );
			}
		}
		//Init auto Drag&Drop
		this.init(_ops);
	};
	/**
	 * Drag multi elements
	 */
	$.multipleDrag = function(ops){
		return new $.autoDragDrop(ops);
	};
})(JoomlaShine.jQuery);
