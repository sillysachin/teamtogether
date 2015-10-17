(function ($) {	
	$(document).ready(function (){
		$('.jsn-supported-ext-list .thumbnails a.btn').not('.disabled').click(function (){
			var self	= $(this);
			var id 		= self.attr('id');
			var action 	= self.attr('act');
			var url		= baseUrl;
			var loadingIndicator = '<div id="loadingIndicator" class="loading-indicator-overlay" ><div class="loading-indicator"></div> </div>';			
			$(loadingIndicator).appendTo($(this).parent().parent());
			//$(".jsn-modal-indicator").show();		
			if (action == 'install'){
				url += 'administrator/index.php?option=com_poweradmin&task=installer.installPaExtension&view=configuration&identified_name=' + id;
			} else if (action == 'enable'){
				url += 'administrator/index.php?option=com_poweradmin&task=configuration.changeExtStatus&view=configuration&identified_name=' + id +'&status=1';
			}
			else if (action == 'disable'){
				url += 'administrator/index.php?option=com_poweradmin&task=configuration.changeExtStatus&view=configuration&identified_name=' + id +'&status=0';
			}
				
			
			$.post
			(
				url
			).success(function( response ){
				$('#loadingIndicator').remove();
				if ( response == 'success' ){
					if (action == 'install'){						
						self.attr('act', 'disable');
						self.text($('#label-disable').attr('value'));
					}else if (action == 'disable'){						
						self.attr('act', 'enable');
						self.text($('#label-enable').attr('value'));
					}else{						
						self.attr('act', 'disable');
						self.text($('#label-disable').attr('value'));
					}
						
					
				}
				else if (response == 'notenabled')
				{
					self.removeClass('item-notinstalled').addClass('item-disabled');
					self.attr('action', 'enable');
					self.text($('#label-enable').attr('value'));
				}
				else {
					alert('There was an error during the installation.')
				}
			});
				
		});
		
		$('.disabled').tipsy({
			gravity: 's',
			fade: true
		});	
	});	
})(JoomlaShine.jQuery);