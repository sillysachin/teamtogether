/**
 * 
 * Funtions and plugins to check mouse 
 *
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 
 Descriptions:
    1. Required files/libs:
       - jQuery lib
       - jQuery UI       
*/
(function($){
	/**
	 *
	 * Plugin check mouse hover top of HTML element
	 *
	 * @return: boolean (true/false)
	 */
	$.fn.mouseHoverTop = function(){
		if (!$.jsnmouse.isInit) $.jsnmouse.init();
		var _top    = $(this).offset().top;
		var _center = _top + $(this).height()/2;
		return ($.jsnmouse.y <= _center && $.jsnmouse.y >= _top);
	};
	/**
	 * 
	 * Plugin check mouse hover bottom of HTML element
	 *
	 * @return: boolean (true/false)
	 */
	$.fn.mouseHoverBottom = function(){
		if (!$.jsnmouse.isInit) $.jsnmouse.init();
		var _top    = $(this).offset().top;
		var _height = $(this).height();
				
		var _center = _top + _height/2;
		var _bottom = _top + _height;
				
		return ($.jsnmouse.y > _center && $.jsnmouse.y <= _bottom);
	};
	/**
	 * 
	 * Plugin to check mouse move in HTML element
	 *
	 * @return: boolean (true/false) 
	 */
	$.fn.mouseIsIn = function(){
		if (!$.jsnmouse.isInit) $.jsnmouse.init();		
		var xMin = $(this).offset().left;
		var yMin = $(this).offset().top;		
		var xMax = xMin + $(this).width();
		var yMax = yMin + $(this).height();		
		return ($.jsnmouse.x >= xMin && $.jsnmouse.x <= xMax && $.jsnmouse.y >= yMin && $.jsnmouse.y <= yMax);
	};
	
	/**
	 *
	 * Check a not in b. a is value, b is array/value
	 * 
	 * @return: boolean (true/false)
	*/
	$.isIn = function(a, b){
		if (typeof b !== object || typeof b !== 'Array') return true;
		//sort array to ASC
		b.sort();
		var first_value = 0;
		var last_value  = 0;
		var first       = false;
		for(k in b){
			if (!first){
				first_value = b[k];
				fisrt = true;
			}
			last_value = b[k];
		}
		return (a >= first_value && a <= last_value);
	};
	/**
	 * Extend object to get mouse position
	 */
	$.extend({
		/**
		 * jsnmouse Object 
		 */
		jsnmouse: {
			x: 0,
			y: 0,
			isInit: false,
			/**
			 * 
			 * Init mouse move
			 *
			 * @return: None
			 */
			init: function(){
				if (!$.jsnmouse.isInit){
					$(document).mousemove( function(e) {
					    $.jsnmouse.x = e.pageX; 
					    $.jsnmouse.y = e.pageY;
					});						
					$.jsnmouse.isInit = true;
				}
			},
			/**
			 * 
			 * Get current mouse x
			 *
			 * @return: int
			 */
			getX: function(){
				if (!$.jsnmouse.isInit) $.jsnmouse.init();
				return $.jsnmouse.x;
			},
			/**
			 * 
			 * Get current mouse y
			 *
			 * @return: int
			 */
			getY: function(){
				if (!$.jsnmouse.isInit) $.jsnmouse.init();
				return $.jsnmouse.y;
			}		
		}		
	});
})(JoomlaShine.jQuery);