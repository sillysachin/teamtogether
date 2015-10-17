/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

define([
	'jquery',
	'jsn/libs/modal'
],

function ($, modal)
{
	// Declare JSN Upgrade contructor
	JSNUpgrade = function(params) {
		// Object parameters
		this.params = $.extend({}, params);
		this.lang = this.params.language || {};

		$(document).ready($.proxy(function() {
			// Get update button object
			this.button = document.getElementById(this.params.button);
			
			// Set event handler to update product
			$(this.button).click($.proxy(function(event) {
				event.preventDefault();
				this.install();
			}, this));
		}, this));
	};
	
	// Declare JSN Upgrade methods
	JSNUpgrade.prototype = {
		install: function() {
			// Mark installation step
			this.step = 1;
			
			// Hide form action
			$('#jsn-upgrade-action').hide();
			
			// Execute current installation step
			this.execute();
		},
	
		execute: function() {
			// Call appropriate method
			this['step' + this.step]();
		},
	
		step1: function() {
			// Show login form
			$('#jsn-upgrade-login').show();
			
			// Setup login form
			$(document.JSNUpgradeLogin).delegate('input[type="text"], input[type="password"]', 'keyup', $.proxy(function() {
				var canLogin = true;

				$('input[type="text"], input[type="password"]', document.JSNUpgradeLogin).each(function() {
					canLogin = canLogin && this.value != '';
				});

				canLogin
					? $('button', document.JSNUpgradeLogin).removeAttr('disabled') 
					: $('button', document.JSNUpgradeLogin).attr('disabled', 'disabled');
			}, this));
	
			$('button', document.JSNUpgradeLogin).click($.proxy(function(event) {
				event.preventDefault();

				// Execute next upgrade step
				this.step++;
				this.execute();
			}, this));
		},

		step2: function () {
			var loginForm = $('form[name=JSNUpgradeLogin]'),
				editions  = $('#jsn-upgrade-editions'),
				message   = $('#jsn-upgrade-message'),
				txtUsername = loginForm.find('#username'),
				txtPassword = loginForm.find('#password'),
				lstEditions = loginForm.find('select[name=edition]'),
				self		= this;

			txtUsername.attr('readonly', 'readonly');
			txtPassword.attr('readonly', 'readonly');

			message
				.empty()
				.hide();

			$.getJSON(
				'index.php?option=' + this.params.component + '&task=upgrade.editions&id=' + 
				this.params.identifiedName + '&customer_username=' + encodeURIComponent(txtUsername.val()) + '&customer_password=' + encodeURIComponent(txtPassword.val()),

				function (response) {
					if (response.type == 'error') {
						txtUsername.removeAttr('readonly');
						txtPassword.removeAttr('readonly');

						message
							.text(response.message)
							.show();

						self.step--;
						return;
					}

					lstEditions.empty();
					$.map(response, function (edition) {
						lstEditions.append($('<option/>', { value: edition, text: edition }));
					});

					if (response.length == 1) {
						// Execute next upgrade step
						self.step++;
						self.execute();
					} else {
						editions.css('display', 'block');
					}
				}
			);
		},
		
		step3: function() {
			// Update indicators
			$('#jsn-upgrade-cancel').hide();
			$('#jsn-upgrade-login').hide();
			$('#jsn-upgrade-indicator').show();
			$('#jsn-upgrade-downloading-unsuccessful-message').hide();

			// Request server-side to download update package
			$.ajax({
				url: $(this.button).attr('data-source'),
				type: document.JSNUpgradeLogin.method,
				data: $(document.JSNUpgradeLogin).serialize() + '&tmpl=component&ajax=1',
				context: this
			}).done(function(data) {
				this.clearTimer('#jsn-upgrade-downloading-indicator');

				if (data.substr(0, 4) == 'DONE') {
					// Update indicators
					$('#jsn-upgrade-downloading-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-ok');

					// Update download link to install link
					$(this.button).attr('data-source', $(this.button).attr('data-source').replace('.download', '.install'));
					this.button.data = 'path=' + data.replace(/^DONE:(\s+)?/, '');

					// Execute next installation step
					this.step++;
					this.execute();
				} else {
					// Update indicators
					$('#jsn-upgrade-downloading-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
					$('#jsn-upgrade-downloading-unsuccessful-message').html(data.replace(/^FAIL:(\s+)?/, '')).show();
				}
			});

			this.setTimer('#jsn-upgrade-downloading-indicator');
		},
	
		step4: function() {
			// Update indicators
			$('#jsn-upgrade-installing').show();
			$('#jsn-upgrade-installing-unsuccessful-message').hide();
			$('#jsn-upgrade-installing-warnings').hide();
			
			// Request server-side to install dowmloaded package
			this.modal = new modal({
				url: $(this.button).attr('data-source') + ($(this.button).attr('data-source').indexOf('?') ? '&' : '?') + this.button.data + '&tmpl=component&ajax=1&tool_redirect=' + this.params.redirect,
				loaded: $.proxy(function() {
					var data = this.modal.iframe[0].contentDocument.doctype == null ? this.modal.iframe[0].contentDocument.body.innerHTML : 'DONE';
					this.modal.container.parent().css('visibility', 'hidden');
					this.clearTimer('#jsn-upgrade-installing-indicator');
					
					if (data.substr(0, 4) == 'DONE') {
						// Update indicators
						$('#jsn-upgrade-installing-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-ok');
		
						// State that installation is completed successfully
						$('#jsn-upgrade-successfully').show();
					} else {
						// Update indicators
						$('#jsn-upgrade-installing-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
						
						// Displaying any error/warning message
						if (data.substr(0, 4) == 'FAIL') {
							$('#jsn-upgrade-installing-unsuccessful-message').html(data.replace(/^FAIL:(\s+)?/, '')).show();
						} else {
							$('#jsn-upgrade-installing-warnings').append(data).show();
						}
					}
				}, this)
			});

			this.modal.iframe.attr('src', this.modal.options.url);
			this.setTimer('#jsn-upgrade-installing-indicator');
		},
	
		setTimer: function(element) {
			// Schedule still loading notice
			this.timer = setInterval($.proxy(function() {
				if ($(element).hasClass('jsn-icon-loading')) {
					var msg = $(element).next('.jsn-processing-message').html();
					if (msg == this.lang['JSN_EXTFW_GENERAL_STILL_WORKING']) {
						$(element).next('.jsn-processing-message').html(this.lang['JSN_EXTFW_GENERAL_PLEASE_WAIT']);
					} else {
						$(element).next('.jsn-processing-message').html(this.lang['JSN_EXTFW_GENERAL_STILL_WORKING']);
					}
				}
			}, this), 3000);
		},
	
		clearTimer: function(element) {
			clearInterval(this.timer);
			$(element).next('.jsn-processing-message').hide();
		}
	};

	return JSNUpgrade;
});
