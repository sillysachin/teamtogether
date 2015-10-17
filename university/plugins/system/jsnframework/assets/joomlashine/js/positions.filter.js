/**
 * 
 * To help filter elements on page and support sorting. Thank to jQueryUI
 *
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 
 Descriptions:
    1. Required files/libs:
       - jQuery lib
       - jQuery UI       
**/
if (typeof(JoomlaShine.jQuery) == undefined){
	var JoomlaShine = {};
	JoomlaShine.jQuery = jQuery.noConflict();
}

(function($){
	$.visualmodeFilter = function(options){
		//Interval value
		this.intervalFilter = null;
		//Time tick to filter 
		this.timeTicker = 0;
		//prev keywork
		this.prevKeyword = '';
		//Setting options
		this.ops = {
			timeShowResult : 60,
			containerClass : '.jsn-position',
			defaultText    : 'Search...'
		};
		 /**
		   *
		   * Save setting or edit setting
		   *
		   * @return: Save setting
		   */
		this.config = function( options ){
				$.extend(this.ops, options);
				// custom css expression for a case-insensitive contains()
				$.expr[':'].contains = function(a,i,m){
				    return (a.textContent || a.innerText || "").toLowerCase().indexOf(m[3].toLowerCase())>=0;
				};
				return this;
		};
		/**
		   * 
		   * Filter result
		   *
		   * @param: (string) (filter) is keyword 
		   * @return: None/Change attr of HTML elements
		   */
		this.filterResults = function(filter){
				var $this = this;
				if ($this.prevKeyword == filter) return;
				$this.timeTicker = 0;
				clearInterval($this.intervalFilter);
				$this.intervalFilter = setInterval(function(){
					$this.timeTicker++;
					if ($this.timeTicker == $this.ops.timeShowResult){
						var container = $($this.ops.containerClass);
						var found, notFound;
						if (container.length == 0) {
							container = $('.mod-preview');
							notFound	= container.find("div:not(div:contains('"+filter+"'))");
							found		= container.find("div:contains('"+filter+"')");
						}else{
							notFound	= container.find("p:not(div:contains('"+filter+"'))");
							found		= container.find("p:contains('"+filter+"')");
						}		
						
						if (filter.trim() == $this.ops.defaultText) filter = '';
						if (filter){
							//Hide all position not contain the input
							notFound.parent().removeClass('notmarkitem').addClass('notmarkitem');
							//Show all position contain the input
							found.parent().removeClass('notmarkitem').addClass('markitem');
						}else{
							container.removeClass('markitem').removeClass('notmarkitem');
						}
						container.each(function(){
							if ($(this).hasClass('notmarkitem')){
								$(this).animate({
									"opacity" : 0.15
								});
							}else if($(this).hasClass('markitem')){
								$(this).animate({
									"opacity" : 1
								});
							}else{
								$(this).animate({
									"opacity" : 1
								});
							}
						});
						$this.prevKeyword = filter;
						clearInterval($this.intervalFilter);
					}
				}, 1);
				
				return false;
		};
		return this.config();
	};
})(JoomlaShine.jQuery);