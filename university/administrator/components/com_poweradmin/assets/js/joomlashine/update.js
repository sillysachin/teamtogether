(function ($) {
	$(function () {
		$.ajaxSetup({
			error: function () {
				if (!$('#jsn-updater-download').hasClass('update-success')) {
					$('#jsn-updater-download').addClass('update-failed');
				}
				
				if (!$('#jsn-updater-install').hasClass('update-success')) {
					$('#jsn-updater-install').addClass('update-failed');
				}
			}
		});
		
		$('#jsn-updater-button-update')
			.click(function () {
				$(this).hide();
				$('#jsn-updater-link-cancel').hide();
				$('#jsn-updater-download').show();
				
				$.getJSON('index.php?option=com_poweradmin&task=update.download&format=json', function (response) {
					if (response.status != 'done') {
						$('#jsn-updater-download').addClass('update-failed');
						return;
					}
					
					$('#jsn-updater-download').addClass('update-success');
					$('#jsn-updater-install').show();
					
					$.getJSON('index.php?option=com_poweradmin&task=update.install&format=json', function (response) {
						if (response.status != 'done') {
							$('#jsn-updater-install').addClass('update-failed');
							return;
						}
						
						$('#jsn-updater-install').addClass('update-success');
						$('#jsn-updater-successfully').show();
						$('#jsn-updater-button-finish')
							.click(function () {
								window.location = 'index.php?option=com_poweradmin';
							})
							.show();
					});
				});
			});
	});
})(JoomlaShine.jQuery);