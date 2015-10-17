!function ($) {
	"use strict";

	var	JSNTemplateUpdate = function (button, params)
	{
		var self = this;

		this.defaultParams = {
			basePath: '/',
			title: 'Update Template to the latest version',
			width: 800,
			height: 600
		};

		this.params = $.extend(this.defaultParams, params);
		this.button = $(button);
		this.panel  = $('<div />', { 'class': 'jsn-template-update jsn-bootstrap' });

		self.init = function() {
			// Initialize modal window
			self.panel.dialog({
				width: self.params.width,
				height: self.params.height,
				title: self.params.title,
				resizable: false,
				draggable: false,
				autoOpen : false,
				modal: true,
				open: self.loadConfirmScreen,
				closeOnEscape: false
			});

			// Handle click event for install button to open dialog
			self.button.on('click', function(event) {
				event.preventDefault();

				// Detect update target
				self.target = $(this).attr('data-target');
				self.target == 'framework'
					? self.panel.dialog('option', 'title', 'Update Framework to the latest version')
					: self.panel.dialog('option', 'title', 'Update Template to the latest version');

				// Show update panel
				self.panel.empty().dialog('open');
			});

			// Setup button to close update panel
			self.panel.delegate('button[id^="btn-finish"]', 'click', function(event) {
				event.preventDefault();
				self.finishUpdate();
			});

			// Handle window resize event to update modal position
			$(window).on('resize', function() {
				self.panel.dialog('option', 'position', 'center');
			});

			// Always check for latest update
			self.checkVersionUpdate();
		};

		self.checkVersionUpdate = function() {
			// Send ajax request to receive update information
			$.getJSON('index.php?widget=update&action=check-update&template=' + self.params.template, function(result) {
				var container = $('#jsn-version-info > div');

				container.each(function() {
					var	el = $(this),
						status = el.find('.jsn-status'),
						version = el.find('.jsn-new-version');

					if (result.type == 'success') {
						var info = result.data[el.attr('data-target')];

						if (info.hasUpdate == true) {
							el.toggleClass('jsn-version-checking jsn-version-outdated');
							status.text('');
							version.text(info.newVersion).parent().removeClass('hide');

							// Show update link in footer menu
							$('#jsn-global-check-version-result').css('display', '');
						} else {
							el.toggleClass('jsn-version-checking jsn-version-latest');
							status.text('The latest version.');
						}

						self.versionData = result.data;
					} else {
						status.text(result.data);
					}
				});
			});
		};

		self.loadConfirmScreen = function() {
			// Set loading state
			self.panel.html('').addClass('jsn-loading');

			$.getJSON('index.php?widget=update&action=confirm&template=' + self.params.template + '&target=' + self.target, function(response) {
				self.panel.html(response.data);
				self.panel.removeClass('jsn-loading');

				var	confirmUpdateButton = self.panel.find('button#btn-confirm-update'),
					updateBothButton = self.panel.find('button#btn-confirm-update-both'),
					customerInfoFields = self.panel.find('input[name="username"], input[name="password"]');

				// Setup cancel button
				self.panel.find('#btn-cancel-update').click(function(event) {
					event.preventDefault();
					self.panel.dialog('close');
				});

				// Setup update both button
				if (updateBothButton.length) {
					updateBothButton.click(function() {
						// Switch to update template mode
						self.target = 'template';

						// Reload confirmation screen
						self.loadConfirmScreen();
					});
				}

				// Setup update button
				if (customerInfoFields.size() == 0) {
					// Setup event handler for confirm button when edition is FREE
					confirmUpdateButton.click(function() {
						confirmUpdateButton.attr('disabled', 'disabled');
						self.loadInstallScreen();
					});
				} else {
					customerInfoFields.on('keyup change', function(event) {
						self.customerInfo = {
							username: self.panel.find('input[name="username"]').val(),
							password: self.panel.find('input[name="password"]').val()
						};

						if (self.customerInfo.username != '' && self.customerInfo.password != '') {
							confirmUpdateButton.removeAttr('disabled');

							if (event.type == 'keyup' && event.keyCode == 13) {
								confirmUpdateButton.trigger('click');
							}
						} else {
							confirmUpdateButton.attr('disabled', 'disabled');
						}
					});

					confirmUpdateButton.click(function() {
						confirmUpdateButton.attr('disabled', 'disabled');

						// Send request to checking customer information
						$.ajax({
							url: 'index.php?widget=update&action=confirm&template=' + self.params.template,
							type: 'POST',
							dataType: 'JSON',
							data: {
								username: self.customerInfo.username,
								password: self.customerInfo.password
							},
							success: function(response) {
								if (response.type == 'success') {
									self.customerInfo = {
										username: self.customerInfo.username,
										password: self.customerInfo.password
									};

									self.loadInstallScreen();
								} else {
									alert(response.data);
								}
							}
						});
					});
				}
			});
		};

		self.loadInstallScreen = function() {
			self.panel.dialog('option', 'buttons', {});

			$.getJSON('index.php?widget=update&action=install&template=' + self.params.template + '&target=' + self.target, function(response) {
				self.panel.html(response.data);

				if (self.target == 'framework') {
					// Start framework installation process
					self.startInstallFramework();
				} else {
					// Start download template package
					self.downloadPackage(self.customerInfo);
				}
			});
		};

		self.startInstallFramework = function() {
			var	downloadPackage = self.panel.find('li#jsn-download-package'),
				downloadStatus = downloadPackage.find('.jsn-status'),
				installUpdate = self.panel.find('li#jsn-install-update'),
				installStatus = installUpdate.find('.jsn-status');

			// Set in progress message
			self.inProgress(downloadStatus);

			$.getJSON('index.php?widget=update&action=download-framework&template=' + self.params.template, function(response) {
				// Unset in progress message
				self.inProgress(downloadStatus, true);

				if (response.type == 'success') {
					downloadPackage.toggleClass('jsn-loading jsn-success');
					installUpdate.removeClass('hide');

					// Set in progress message
					self.inProgress(installStatus);

					$.getJSON('index.php?widget=update&action=install-framework&template=' + self.params.template, function(result) {
						// Unset in progress message
						self.inProgress(installStatus, true);

						if (result.type == 'success') {
							self.panel.find('#jsn-success-message').removeClass('hide');
							installUpdate.toggleClass('jsn-loading jsn-success');
						} else {
							installUpdate.toggleClass('jsn-loading jsn-error');
							installStatus.text(result.data);
						}

						// Stop update process
						self.panel.find('#btn-finish-install').removeClass('hide').click(function(event) {
							event.preventDefault();
							self.finishUpdate();
						}).parent().removeClass('hide');
					});
				} else {
					downloadPackage.toggleClass('jsn-loading jsn-error');
					downloadStatus.text(response.data);
				}
			});
		};

		self.downloadPackage = function(loginData) {
			var	liDownload = $('#jsn-download-package').removeClass('hide'),
				spanStatus = liDownload.find('span.jsn-status'),
				btnFinish = self.panel.find('#btn-finish-install');

			// Set in progress message
			self.inProgress(spanStatus);

			$.ajax({
				url: 'index.php?widget=update&action=download&template=' + self.params.template,
				type: 'POST',
				dataType: 'JSON',
				data: loginData,
				success: function(response) {
					// Unset in progress message
					self.inProgress(spanStatus, true);

					if (response.type == 'error') {
						liDownload.removeClass('jsn-loading').addClass('jsn-error');
						spanStatus.text(response.data).addClass('alert alert-error');

						// Stop update process
						btnFinish.removeClass('hide').parent().removeClass('hide');
					} else {
						liDownload.removeClass('jsn-loading').addClass('jsn-success');

						// Start checking for file integrity
						self.checkFilesModification();
					}
				}
			});
		};

		self.checkFilesModification = function() {
			var	liCreateList = self.panel.find('#jsn-backup-modified-files').removeClass('hide'),
				spanStatus = liCreateList.find('span.jsn-status'),
				putOnHold = self.panel.find('#jsn-put-update-on-hold'),
				btnFinish = self.panel.find('#btn-finish-install');

			// Set in progress message
			self.inProgress(spanStatus);

			$.getJSON('index.php?widget=update&action=checkBeforeUpdate&template=' + self.params.template, function(response) {
				// Unset in progress message
				self.inProgress(spanStatus, true);

				liCreateList.removeClass('jsn-loading').addClass('jsn-success');

				if (response.type == 'success') {
					if (response.data.hasModification == true) {
						self.hasModification = true;
	
						// Temporary hold the update process
						liCreateList.find('#jsn-download-backup-of-modified-files').removeClass('hide');
	
						// Setup continue and cancel button
						putOnHold.children('#btn-continue-install').click(function(event) {
							event.preventDefault();
	
							// Hide warning message
							liCreateList.find('#jsn-download-backup-of-modified-files').addClass('hide');
	
							// Hide put-on-hold buttons
							putOnHold.addClass('hide');
	
							// Prepare for update installation
							self.prepareUpdate();
						});
	
						putOnHold.children('#btn-cancel-install').click(function(event) {
							event.preventDefault();
							self.finishUpdate();
						});
	
						// Show put-on-hold buttons
						putOnHold.removeClass('hide').parent().removeClass('hide');
					} else {
						// Prepare for update installation
						self.prepareUpdate();
					}
				} else {
					liCreateList.removeClass('jsn-loading').addClass('jsn-error');
					spanStatus.text(response.data).addClass('alert alert-error');

					// Stop update process
					btnFinish.removeClass('hide').parent().removeClass('hide');
				}
			});
		};

		self.prepareUpdate = function() {
			if (self.versionData['framework'].hasUpdate === true) {
				var	downloadPackage = self.panel.find('li#jsn-download-framework').removeClass('hide'),
					downloadStatus = downloadPackage.find('.jsn-status'),
					installUpdate = self.panel.find('li#jsn-install-framework'),
					installStatus = installUpdate.find('.jsn-status');

				// Set in progress message
				self.inProgress(downloadStatus);

				// Update template framework
				$.getJSON('index.php?widget=update&action=download-framework&template=' + self.params.template, function(response) {
					// Unset in progress message
					self.inProgress(downloadStatus, true);

					if (response.type == 'success') {
						downloadPackage.toggleClass('jsn-loading jsn-success');
						installUpdate.removeClass('hide');

						// Set in progress message
						self.inProgress(installStatus);

						$.getJSON('index.php?widget=update&action=install-framework&template=' + self.params.template, function(result) {
							// Unset in progress message
							self.inProgress(installStatus, true);

							if (result.type == 'success') {
								installUpdate.toggleClass('jsn-loading jsn-success');

								// Update the template
								self.installUpdate();
							} else {
								installUpdate.toggleClass('jsn-loading jsn-error');
								installStatus.text(result.data);

								// Stop update process
								self.panel.find('#btn-finish-install').removeClass('hide').click(function(event) {
									event.preventDefault();
									self.finishUpdate();
								}).parent().removeClass('hide');
							}
						});
					} else {
						downloadPackage.toggleClass('jsn-loading jsn-error');
						downloadStatus.text(response.data);
					}
				});
			} else {
				// Update the template
				self.installUpdate();
			}
		};

		self.installUpdate = function() {
			var	liInstall = self.panel.find('#jsn-install-update').removeClass('hide'),
				successMessage = self.panel.find('#jsn-success-message'),
				spanStatus = liInstall.find('span.jsn-status'),
				btnFinish = self.panel.find('#btn-finish-install');

			// Set in progress message
			self.inProgress(spanStatus);

			// Send request to install template update
			$.getJSON('index.php?widget=update&action=install-package&template=' + self.params.template, function(response) {
				// Unset in progress message
				self.inProgress(spanStatus, true);

				if (response.type == 'success') {
					liInstall.removeClass('jsn-loading').addClass('jsn-success');
					successMessage.removeClass('hide');

					if (self.hasModification) {
						successMessage.find('#jsn-backup-information').removeClass('hide');
					}
				} else {
					liInstall.removeClass('jsn-loading').addClass('jsn-error');
					spanStatus.html(response.data).addClass('alert alert-error');
				}
				
				btnFinish.removeClass('hide').click(function(event) {
					event.preventDefault();
					self.finishUpdate();
				}).parent().removeClass('hide');
			});
		};

		self.finishUpdate = function() {
			// Close the dialog
			self.panel.dialog('close');

			// Reload the page
			window.location.reload();
		};

		self.inProgress = function(element, stop) {
			stop = typeof stop == 'undefined' ? false : stop;

			if ( ! stop) {
				// Schedule still loading notice
				self.timer = setInterval(function() {
					var msg = element.html();

					if (msg == 'Still in progress...') {
						element.html('Please wait...');
					} else {
						element.html('Still in progress...');
					}
				}, 3000);
			} else if (self.timer) {
				clearInterval(self.timer);
				element.html('');
			}
		};

		self.init();
	};

	/**
	 * Register jQuery plugin
	 *
	 * @param   element  button  Button that will triggered event to start install sample data
	 * @param   object   params  Object parameters
	 * 
	 * @return  void
	 */
	$.initTemplateUpdate = function (button, params) {
		if ($.__template_admin_auto_update__ === undefined)
			$.__template_admin_auto_update__ = new JSNTemplateUpdate(button, params);
	};
}(jQuery);