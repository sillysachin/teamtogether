/**
* 
* JSN Helpers
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
**/
var jsnToolbars;
(function($){
	$.jsnToolbars = function(){
		/**
		 * Megre all option to an format 
		 */
		this._megreOptions = function( _ops )
		{
			return $.extend({
				modal  : false,
				title  : 'JSN help contents',
				width  : 750,
				height : 550		
			}, _ops);
		};
		/**
		 * Open an popup child page
		 * 
		 * @param: String url is web url
		 * @param: Array _ops is option
		 */
		this.openChildPage = function( url, _ops ){
			var ops = this.megreOptions( _ops );
			var pop     = $.JSNUIWindow
			(
				url, 
				{
					modal : ops.modal,
					width : ops.width,
					height: ops.height,
					title : ops.title,
			 		buttons:{
			 			'Close': function(){
			 				$(this).dialog("close");
			 			}
			 		}
			 	}
		    );
		};

		/**
		 * Open an new child page
		 * 
		 * @param: String url is web url
		 * @param: Array _ops is option
		 */
		this._openNewPage = function( url, _ops )
		{
			var ops = this.megreOptions( _ops );
			window.open(url,'jsnHelpContent','width='+ops.width+',height='+ops.height+',left=0,top=100,screenX=0,screenY=100');
		};
		/**
		 * 
		 * Tool bar switch mode
		 * 
		 * @param: javascript object
		 * @param: String enmodeTitle
		 * @param: String offmodeTitle
		 */
		this._switchmode = function( obj, enmodeTitle, offmodeTitle ){
			obj = $(obj);
			if ( obj.hasClass('turn-on') ){
				obj.removeClass('turn-on').addClass('turn-off').attr('title', enmodeTitle).trigger('turnoffmode');
				$.jStorage.set("sitemanager_show_help", false );
			}else{
				obj.removeClass('turn-off').addClass('turn-on').attr('title', offmodeTitle).trigger('turnonmode');
				$.jStorage.set("sitemanager_show_help", true );
			}
		};
	};
	jsnToolbars = new $.jsnToolbars();
})(JoomlaShine.jQuery);