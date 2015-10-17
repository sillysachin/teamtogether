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
	// Define function to show a message box
	$.JSNTplMessage = function(msg, type) {
		var msgBox = $('#jsn-template-config').children('.jsn-form-validation-failed');

		if (type && type == 'success') {
			msgBox.removeClass('alert-error').addClass('alert-success');
		}

		// Show message box
		msgBox.children('span').html(msg);
		msgBox.removeClass('hide').css('margin-left', '-' + (msgBox.outerWidth() / 2) + 'px');

		// Schedule to hide error message box
		msgBox.timer && clearTimeout(msgBox.timer);

		msgBox.timer = setTimeout(function() {
			msgBox.fadeOut(1000, function() {
				msgBox.addClass('hide').css('display', '');
			});
		}, 5000);

		return (type && type == 'success') ? true : false;
	};

	$.JSNFormValidation = function(params) {
		// Initialize parameters
		this.params = $.extend({
			id: 'style-form',
			event: 'both', // Either 'change', 'submit' or 'both'
			lang: {
				JSN_TPLFW_INVALID_VALUE_TYPE: '',
				JSN_TPLFW_ERROR_FORM_VALIDATION_FAILED: '',
				JSN_TPLFW_SYSTEM_CUSTOM_ASSETS_INVALID: ''
			}
		}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init, this));
	};

	$.JSNFormValidation.prototype = {
		init: function() {
			var self = this;

			self.$form = $('#' + self.params.id);
			self.$fields = self.$form.find('input[class*="validate-"], textarea[class*="validate-"]');

			// Setup event handler to validate field value
			if (self.params.event != 'submit') {
				self.$fields.change(function(event) {
					event.preventDefault();

					// Validate field value
					var result = self.validate(this);

					if (result !== true) {
						$.JSNTplMessage(result);
					} else {
						$(this).removeClass('invalid');
					}
				});
			}

			if (self.params.event != 'change') {
				var	oldJoomlaSubmitButton = Joomla.submitbutton,
					validateForm = function() {
						var valid = true, result;

						self.$fields.each(function(i, e) {
							result = self.validate(e);

							result === true || (valid = false);
						});

						return valid;
					};

				if (typeof oldJoomlaSubmitButton == 'function') {
					Joomla.submitbutton = function(task) {
						if (task != 'style.cancel') {
							var result = document.formvalidator.isValid(self.$form[0]);

							// Do additional check
							result ? (result = validateForm()) : validateForm();

							if (!result) {
								// Hide default error message box in Joomla 3.x
								$('#system-message-container').addClass('hide');
	
								// Show error message box
								return $.JSNTplMessage(self.params.lang['JSN_TPLFW_ERROR_FORM_VALIDATION_FAILED']);
							}
						}

						// Trigger submit button function
						oldJoomlaSubmitButton(task);
					};
				} else {
					self.$form.submit(function(event) {
						var task = self.$form.find('input[name="task"]'), result;

						if (!task.length || task.val() == 'style.cancel') {
							return true;
						} else {
							result = validateForm();

							if (!result) {
								event.preventDefault();

								// Show error message box
								$.JSNTplMessage(self.params.lang['JSN_TPLFW_ERROR_FORM_VALIDATION_FAILED']);
							}

							return result;
						}
					});
				}
			}
		},

		validate: function(field) {
			var	self = this,
				require = $(field).attr('class').match(/validate-([^\s]+)/)[1],
				value = $(field).val(),
				valid = true;

			switch (require) {
				case 'number':
				case 'positive-number':
				case 'negative-number':
					var number_value = Number(value);

					if (
						(value == '' || isNaN(number_value))
						||
						(require == 'positive-number' && number_value <= 0)
						||
						(require == 'negative-number' && number_value >= 0)
					) {
						valid = self.params.lang['JSN_TPLFW_INVALID_VALUE_TYPE'].replace('%s', require.replace('-', ' '));
					}
				break;

				case 'asset-file-list':
					var files = value.split("\n"), invalid = [];

					for (var i = 0; i < files.length; i++) {
						!files[i] || files[i].match(/\.(js|css)$/i) || invalid.push(files[i]);
					}

					if (invalid.length) {
						valid = self.params.lang['JSN_TPLFW_SYSTEM_CUSTOM_ASSETS_INVALID'] + '<ul><li>' + invalid.join('</li><li>') + '</li></ul>';
					}
				break;
			}

			// Add 'invalid' class if field has invalid value
			valid === true || $(field).addClass('invalid');

			return valid === true ? true : valid;
		}
	};
})(jQuery);
