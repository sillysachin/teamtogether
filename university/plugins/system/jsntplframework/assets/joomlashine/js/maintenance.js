/**
 * @version     $Id$
 * @package     JSNTPLFW
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

(function($) {
	$.JSNTplMaintenance = function(params) {
		// Initialize parameters
		this.params = $.extend({
			template: '',
			styleId: ''
		}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init, this));
	};

	$.JSNTplMaintenance.prototype = {
		init: function() {
			var	self = this,
				form = $('#style-form'),
				status = form.children('input[name="customized"]'),
				iframe = $('#jsn-silent-save'),
				btnBackup = $('#jsn-template-maintenance-backup-params'),
				inputFile = $('#jsn-template-maintenance-restore-params-form').children('input[type="file"]');

			// Setup backup button
			btnBackup.click(function(event, saved) {
				if (status.val() == 'no' && !saved) {
					// Validate form elements
					if (!document.formvalidator.isValid(form[0])) {
						return $.JSNTplMessage();
					}

					// Setup iframe to silently save template parameters
					iframe.unbind('load').bind('load', function() {
						// Reset form target
						form.removeAttr('target');

						// Trigger click event for backup button
						btnBackup.trigger('click', [true]);
					});

					// Template params are never saved, save now
					form.attr('target', 'jsn-silent-save');
					Joomla.submitbutton('style.apply');

					return false;
				}

				window.location.href = btnBackup.attr('href');
			});

			// Setup restore button
			$('#jsn-template-maintenance-restore-params').click(function(event) {
				event.preventDefault();

				// Trigger click event for the hidden file field
				inputFile.trigger('click');
			});

			// Setup iframe to silently restore template parameters
			inputFile.change(function() {
				// Handle iframe load event
				iframe.unbind('load').bind('load', function() {
					// Parse response data
					if (response = $(this).contents().text().match(/\{"type":[^,]+,"data":[^\}]+\}/)) {
						response = $.parseJSON(response[0]);
					} else {
						response = {type: 'failure', data: $(this).contents().text()};
					}

					if (response.type == 'success') {
						// Show success message
						$.JSNTplMessage(response.data, 'success');

						window.location.reload();
					} else {
						// Show error message
						$.JSNTplMessage(response.data);
					}
				});

				// Set form action
				inputFile.parent().attr('action', 'index.php?widget=maintenance&action=restore&template=' + self.params.template + '&styleId=' + self.params.styleId);

				// Submit form to iframe
				inputFile.parent().submit();
			});
		}
	};
})(jQuery);
