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
	'jquery.ui'
], 

function ($)
{
	// Declare JSN Data contructor
	var JSNData = function(params) {
		this.params = $.extend({}, params);
		this.lang = this.params.language || {};

		$(document).ready($.proxy(function() {
			// Store form object
			this.form = $('form[name="JSNDataInstallSample"]');
			this.btnInstall = $('#jsn-data-install-sample-button', this.form);
			
			// Set event handler to install sample data
			this.btnInstall.unbind('click').click($.proxy(function(event) {
				event.stopPropagation();
				this.install();
			}, this));
	
			this.initDataBackup();
		}, this));
	};

	// Declare JSN Data methods
	JSNData.prototype = {
		initDataBackup: function () {
			this.backupForm = $('form[name="JSNDataBackupForm"]');
			this.backupFile = $('#jsn-data-backup-name', this.backupForm);
			this.btnBackup	= $('.form-actions button', this.backupForm);

			this.backupForm.bind('formStateChanged', $.proxy(function (event, state) {
				event.stopPropagation();

				var fileName = this.backupFile.val().trim(),
					backupOptions = $('input[id^="jsnconfig_databackup"]:checked', this.backupForm);

				(fileName != '' && backupOptions.length > 0)
					? this.btnBackup.removeAttr('disabled')
					: this.btnBackup.attr('disabled', 'disabled');

			}, this));
		},

		install: function() {
			// Mark installation step
			this.step = 1;
			
			// Hide form action
			$('#jsn-data-install-sample-action').hide();
			
			// Execute current installation step
			this.execute();
		},

		execute: function() {
			// Get defined task
			var task = $('#jsn-data-install-sample-button').attr('value');

			// Preset form parameters
			$('input[name="task"]', this.form).val(task);
			$('input[name="installSampleStep"]', this.form).val(this.step);

			if (task.indexOf('.') != -1) {
				$('input[name="view"]', this.form).val(task.substr(0, task.indexOf('.')));
			}

			// Call appropriate method
			this['step' + this.step].apply(this, arguments);
		},

		step1: function() {
			// Update indicators
			$('#jsn-data-install-sample-indicator').show();
			$('#jsn-data-install-sample-downloading-unsuccessful-message').hide();
			
			// Request server-side to download sample package
			$.ajax({
				url: this.form.attr('action'),
				type: this.form.attr('method'),
				data: this.form.serialize() + '&tmpl=component&ajax=1',
				context: this
			})
			.done(function(data) {
				this.clearTimer('#jsn-data-install-sample-downloading-indicator');

				if (data.substr(0, 4) == 'DONE') {
					// Update indicators
					$('#jsn-data-install-sample-downloading-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-ok');

					// Switch download link to local path in server 
					$('input[name="sampleDownloadUrl"]', this.form).val(data.replace(/^DONE:(\s+)?/, ''));

					// Execute next installation step
					this.step++;
					this.execute();
				} else if (data == 'DOWNLOAD FAIL') {
					// Update indicators
					$('#jsn-data-install-sample-downloading-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
					$('#jsn-data-install-sample-downloading-unsuccessful-message').show();
					
					// Set event for manual installation
					$('#jsn-data-install-sample-downloading-unsuccessful-message').delegate('a.install-method-switcher', 'click', $.proxy(
						function() {
							this.switchForm();
						},
						this
					));
				} else {
					// Update indicators
					$('#jsn-data-install-sample-downloading-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
					$('#jsn-data-install-sample-downloading-unsuccessful-message').html(data.replace(/^FAIL:(\s+)?/, '')).show();
				}
			});

			this.setTimer('#jsn-data-install-sample-downloading-indicator');
		},

		step2: function(manualInstall) {
			manualInstall = manualInstall || false;

			// Update indicators
			$('#jsn-data-install-sample-installing').show();
			$('#jsn-data-install-sample-installing-unsuccessful-message').hide();
			$('#jsn-data-install-sample-installing-warnings').hide();
			
			if (manualInstall) {
				// Update indicators
				$('#jsn-data-install-sample-manual-installation').hide();
				$('#jsn-data-install-sample-indicator').show();
				$('#jsn-data-install-sample-downloading-indicator').removeClass('jsn-icon-remove').addClass('jsn-icon-ok');
				$('#jsn-data-install-sample-downloading-unsuccessful-message').hide();

				// Create an iframe for submit form
				this.iframe = $('<iframe src="javascript:false;" name="jsn-data-install-sample-manually" />');
				this.iframe.attr('id', 'jsn-data-install-sample-manually').attr('_title', $('title').text()).load($.proxy(
					function(event) {
						var data = event.target.contentDocument ? event.target.contentDocument : window.frames[event.target.getAttribute('id')].document;
						data = data.doctype == null ? data.body.innerHTML : 'DONE';
		    			this.finalize(data);
					},
					this
				)).hide().appendTo(document.body);
				
				// Update form to upload then install sample data package
				this.form.attr('enctype', 'multipart/form-data').attr('target', this.iframe.attr('name'));
				this.form[0].submit();
			} else {
				// Request server-side to install sample package
				$.ajax({
					url: this.form.attr('action'),
					type: this.form.attr('method'),
					data: this.form.serialize() + '&tmpl=component&ajax=1',
					context: this
				}).done(function(data) {
					this.finalize(data);
				});
			}

			this.setTimer('#jsn-data-install-sample-installing-indicator');
		},
		
		switchForm: function() {
			// Switch installation method form
			$('#jsn-data-install-sample-indicator').hide();
			$('#jsn-data-install-sample-manual-installation').show();
			
			// Set event handler to install sample data manually
			$('#jsn-data-install-sample-manual-installation button').unbind('click').click($.proxy(function(event) {
				event.preventDefault();

				// Execute next installation step
				this.step++;
				this.execute(true);

				return false;
			}, this));
		},

		finalize: function(data) {
			this.clearTimer('#jsn-data-install-sample-installing-indicator');

			if (this.iframe) {
				// Restore page title
				$('title').text(this.iframe.attr('_title'));

				// Remove temporary iframe
				this.iframe.remove();

				// Restore original form attributes
				this.form.removeAttr('enctype').removeAttr('target');
			}

			if (data.substr(0, 4) == 'DONE') {
				// Update indicators
				$('#jsn-data-install-sample-installing-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-ok');

				// State that installation is completed successfully
				$('#jsn-data-install-sample-successfully').show();
			} else {
				// Update indicators
				$('#jsn-data-install-sample-installing-indicator').removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
				
				// Displaying any error/warning message
				if (data.substr(0, 4) == 'FAIL') {
					$('#jsn-data-install-sample-installing-unsuccessful-message').html(data.replace(/^FAIL:(\s+)?/, '')).show();
				} else {
					$('#jsn-data-install-sample-installing-warnings').append(data).show();
				}
			}
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

	return JSNData;
});
