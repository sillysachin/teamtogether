(function ($) {
	
	// Implement DOM ready event
	$(function () {
		/**
		 * Check for update link
		 */
		$('#jsn-check-version').click(function () {
			var cssClasses = {
				400: 'jsn-connection-fail',
				200: 'jsn-outdated-version',
				204: 'jsn-latest-version'
			};
			
			$(this).hide();
			$('#jsn-check-version-result').append($('<span/>', { 'class': 'jsn-version-checking', text: 'Checking...' }));
			
			$.getJSON('index.php?option=com_poweradmin&task=checkUpdate', function (data) {
				$('#jsn-check-version-result')
					.empty()
					.append(
						$('<span/>', {
							'class': cssClasses[data.status], 
							'html' : data.message 
						})
					);
			});
		});
		
		/**
		 * Open modal to show other products
		 */
		$('#see-other-products').click(function (e) {
			$.JSNUIWindow(baseUrl + 'administrator/' + $(this).attr('href'), 
			{
				modal: true,
				resizable: false,
				draggable: false,
				scrollContent: false,
				title: JSNLang.translate('JSN_POWERADMIN_JOOMLA_TEMPLATES'),
				width: 660,
				height: 590,
				buttons: {
					'Close': function () {
						$(this).dialog('close');
					},
				}
			});
			
			e.preventDefault();
		});
	});
	
})(JoomlaShine.jQuery);