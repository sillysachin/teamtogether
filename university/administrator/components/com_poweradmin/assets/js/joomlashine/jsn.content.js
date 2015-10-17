/**
* 
* View article
*
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*
* Descriptions:
	1. Required files/libs:
		- jQuery lib
		- jQuery UI
**/
var contentResize; 
(function($){
	/**
	*
	* Funtion to help resize content with popup size
	*/
	contentResize = new function()
	{
		this.setSize = function(editorName, wWidth, wHeight, wFreeHeight){
			//Resize textbox
			$('input[type="text"]').each(function(){
				var parentBox = $(this).parents('div.fltlft');
				if (parentBox.length){
					$(this).css('width', parentBox.width() - 200);
				}
			});
			
			//Resize tabs and editor
			if ( $('#system-message-container').height() > 0){
				wFreeHeight += $('#system-message-container').height() + 13;
			}
			$('.tabs').each(function(){
				if ( $(this).find('table#jform_'+editorName+'_tbl').length ){
					var editor        = $('#jform_'+editorName+'_tbl');
					var editorContent = $('#jform_'+editorName+'_ifr');
					var editorContentText = $('#jform_'+editorName);
					var freeWidth      = freeHeight = 0;
					var currEWidth     = editor.width();
					var currEHeight    = editor.height();
					var currECWidth    = editorContent.width();
					var currECHeight   = editorContent.height();
					var currECTWidth   = editorContentText.width();
					var currECTHeight  = editorContentText.height();
					
					if ( wWidth > 0 ){
						eWidth = wWidth - 52;
					}
					
					if ( wWidth > 0 ){
						var freeWidth  = eWidth - currEWidth;
					}
					
					if (wHeight > 0){
						var freeHeight = wHeight -  $(this).height() - wFreeHeight;
					}
					
					editor.animate({
						'width'  : currEWidth + freeWidth,
						'height' : currEHeight + freeHeight
					}, 300);
					
					editorContent.animate({
						'width'  : currECWidth + freeWidth,
						'height' : currECHeight + freeHeight
					}, 300);

					editorContentText.animate({
						'width'  : currECTWidth + freeWidth - 36,
						'height' : currECTHeight + freeHeight + 7
					}, 300);
				}
				$(this).animate({
					'width'  : wWidth - 50,
					'height' : wHeight - wFreeHeight
				}, 300);
			});			
		}
	}
	/**
	* Init tabs using jQuery UI
	 */
	$(window).ready(function(){
		$( "#jsn_tabs" ).tabs();
	});
})(JoomlaShine.jQuery);