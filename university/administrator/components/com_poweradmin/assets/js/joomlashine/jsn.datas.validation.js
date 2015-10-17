/**
* 
* JSN Datas validations
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 
 Descriptions:
	1. Required files/libs:
		1. jQuery
**/

/**
 * 
 * Funtions and plugins to validation data on document
 *
 **/
(function($){
	/**
	 * Plugin to helps validations all text fields
	 */
	$.fn.textboxDataNumberic = function(options){
		var defaults = $.extend({}, $.fn.textboxDataNumberic.defaults(), options);
		$(this).find('input.numberic')
		.each(function(){
			if ( parseInt($(this).val()) != defaults.defaultData && parseInt($(this).val()) > 0){
				$(this).data('old_data', parseInt($(this).val()));
			}else{
				$(this).data('old_data', defaults.defaultData);
			}
			//Turn-off autocomplete
			$(this).attr('AUTOCOMPLETE', 'off');
		})
		.keyup(function(e){
			if (e.keyCode == 38){
				var upValue = parseInt( $(this).data('old_data') ) + defaults.offsetValue;
				if (upValue > defaults.defaultData){
					if ( upValue <= defaults.maxValue ){
						$(this).val(upValue);
						$(this).data('old_data', upValue);
					}else{
						$(this).val(defaults.maxValue);
						$(this).data('old_data', defaults.maxValue);
					}
				}else{
					$(this).val(defaults.defaultDataText);
					$(this).data('old_data', defaults.defaultData);
				}
			}else if( e.keyCode == 40 ){
				var downValue = parseInt( $(this).data('old_data') ) - defaults.offsetValue;
				if (downValue > defaults.defaultData){
					$(this).val(downValue);
					$(this).data('old_data', downValue);
				}else{
					$(this).val(defaults.defaultDataText);
					$(this).data('old_data', defaults.defaultData);
				}
			}else if( e.keyCode == 8 || e.keyCode == 46 ){
				$(this).data('old_data', parseInt($(this).val()));
			}else if ( $.fn.textboxDataNumberic.IsNumeric($(this).val()) ){
				$(this).data('old_data', $(this).val());
			}else{
				if ( parseInt($(this).data('old_data')) > defaults.defaultData ){
					$(this).val($(this).data('old_data'));
				}else{
					$(this).val(defaults.defaultDataText);
				}
			}
		});
	};
	/**
	 * Get default config fields
	 */
	$.fn.textboxDataNumberic.defaults = function(){
		return {
				offsetValue     : 1,
				defaultData     : 0,
				maxValue        : 20,
				defaultDataText : 0
		};
	};
	/**
	 * Check is numberic
	 */
	$.fn.textboxDataNumberic.IsNumeric = function(input){
	    var RE = /^-{0,1}\d*\.{0,1}\d+$/;
	    return (RE.test(input));
	};
})(JoomlaShine.jQuery);
