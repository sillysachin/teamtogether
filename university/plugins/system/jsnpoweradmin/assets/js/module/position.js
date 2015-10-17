/**
 * @version     $Id$ * 
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function ($){
	var positionsModalWindow;
	$(document).ready(function (){
		if ($('#jform_client_id').val() == 0) { 
			// Remove default position chooser of Joomla!
			var positionField = $('#jform_position');
			var inputWrapper = $('<div class="input-append"></div>');
			var newPositionField = $('<input class="input-medium" type="text" id="jform_position" name="jform[position]" value="' + positionField.val() + '">');
			var newButton = $("<button value='" + COM_MODULES_CHANGE_POSITION_BUTTON + "' type='button' class='btn'>" + COM_MODULES_CHANGE_POSITION_BUTTON + "</button>");
			
			inputWrapper.append(newPositionField);
			inputWrapper.append(newButton);
			positionField.parent().append(inputWrapper);
			positionField.remove();
			newButton.bind('click', function (){	
				var wWidth  = $(window).width()*0.85;
				var wHeight = $(window).height()*0.8;
				var buttons = {};
				buttons['Close'] = function (ui) {
					ui.close();
				};
				modal = new JSNWindow({
					handle: 'iframe',
					source: 'index.php?option=com_poweradmin&view=changeposition&tmpl=component&moduleedit=1&moduleid=' + moduleid,
					width: wWidth,
					height: wHeight,
					title: PLG_DEFAULT_TEXT_CHANGE_POSITION_TITLE,
					buttons: buttons,
					toolbarPosition: 'bottom',
					scrollable: true
				});
				
				modal.open();
				appendFilter($('.jsn-ui-window .window-wrapper .window-header'), modal);
			});
		}
	});	
	
	
	// Append position filter box
	appendFilter = function (ele, modal){
		var search   = $('<input type="text"/>');
		var searchWrapper = $('<span class="jsn-bootstrap ui-window-searchbar-wrapper"/>');
		searchWrapper.appendTo($('.ui-dialog-titlebar'));
		search.appendTo(searchWrapper);
				
		search.val(PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION);		
		search.addClass('ui-window-searchbar');
		search.change(function(){
			var iframe = modal.getIframe();
			console.log(iframe);
			iframe.contentWindow.changeposition.filterResults($(this).val().trim());
		});
		
		search.keyup(function (){
			//fire change event
			$(this).change();
		});
		
		search.blur(function(){
			if ($(this).val().trim() == ''){
				$(this).val( PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION );
				$(this).css('color', '#CCCCCC');
			}
		});
		
		search.focus(function(){
			if ($(this).val().trim() == PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION){
				$(this).css('color', '#000').val('');
			}
		});
		
		
		var closeTextKeyword = $('<a />', {
			'class'  : 'ui-window-closetext-keyword',
			'id'     : 'ui-window-closetext-keyword',
			'href'   : 'javascript:void(0);'
		}).click(function(){
			
			$(this).hide();
			var iframe = modal.getIframe();
			iframe.contentWindow.changeposition.filterResults('');
			searchbox.css('color', '#CCCCCC').val( PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION);
		});
				
		search.after(closeTextKeyword);
		search.change(function(){
			if ($(this).val().trim() == PLG_DEFAULT_TEXT_SEARCH_CHANGE_POSITION || $(this).val().trim() == ''){
				closeTextKeyword.hide();
			}else{
				closeTextKeyword.show();
			}
		});					
		searchWrapper.appendTo(ele);
		$('.header-title').after(searchWrapper);
	}
	
})(jQuery);
