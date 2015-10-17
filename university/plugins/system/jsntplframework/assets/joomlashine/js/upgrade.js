!function ($) {
	"use strict";

	var	JSNTemplateUpgrade = function (button, params)
	{
		var self = this;

		this.defaultParams = {
			basePath: '/',
			title: 'Upgrade Template to PRO edition',
			width: 800,
			height: 700
		};

		this.params = $.extend(this.defaultParams, params);
		this.button = $(button);
		this.panel  = $('<div />', { 'class': 'jsn-template-upgrade jsn-bootstrap' });

		function init () {
			// Initialize modal window
			self.panel
				.dialog({
					width: self.params.width,
					height: self.params.height,
					title: self.params.title,
					resizable: false,
					draggable: false,
					autoOpen : false,
					modal: true,
					open: loadIntroScreen,
					closeOnEscape: false
				});

			// Handle click event for install button to open dialog
			self.button.on('click', function (e) {
				e.preventDefault();
				self.panel.dialog('open');
			});

			// Handle window resize event to upgrade modal position
			$(window).on('resize', function () {
				self.panel.dialog('option', 'position', 'center');
			});
		}

		/**
		 * Render intro screen to opened dialog
		 * 
		 * @return void
		 */
		function loadIntroScreen ()
		{
			// Create close button in panel footer
			self.panel.addClass('jsn-loading').dialog('option', 'buttons', {Close: function() { self.panel.dialog('close'); }});

			$.getJSON('index.php?widget=upgrade&action=intro&template=' + self.params.template, function (response) {
				self.panel.removeClass('jsn-loading').html(response.data);
				self.panel
					.find('#btn-start-upgrade')
					.on('click', function (e) {
						e.preventDefault();
						loadLoginScreen();
					});
			});
		}

		/**
		 * Render customer login screen
		 * 
		 * @return  void
		 */
		function loadLoginScreen ()
		{
			// Remove close button in panel footer
			self.panel.dialog('option', 'buttons', null);

			$.getJSON('index.php?widget=upgrade&action=login&template=' + self.params.template, function (response) {
				self.panel.html(response.data);

				// Setup cancel button
				self.panel.find('#btn-cancel-upgrade').click(function(event) {
					event.preventDefault();
					self.panel.dialog('close');
				});

				var	btnNext = self.panel.find('#btn-load-editions'),
					loginInputs = self.panel.find('input[name="username"], input[name="password"]');

				btnNext.on('click', function (e) {
					e.preventDefault();
					loadProductEditions();
				});

				loginInputs.on('keyup change', function (event) {
					var	txtUsername = self.panel.find('input[name="username"]'),
						txtPassword = self.panel.find('input[name="password"]');

					if (txtUsername.val() != '' && txtPassword.val() != '')
					{
						btnNext.removeAttr('disabled');

						if (event.type == 'keyup' && event.keyCode == 13) {
							btnNext.trigger('click');
						}
					} else {
						btnNext.attr('disabled', 'disabled');
					}
				});
			});
		}

		/**
		 * Load product editions
		 * 
		 * @return  void
		 */
		function loadProductEditions ()
		{
			var
			btnNext		= self.panel.find('#btn-load-editions'),
			txtUsername	= self.panel.find('input[name="username"]'),
			txtPassword	= self.panel.find('input[name="password"]');

			// Hide all error panel
			self.panel
				.find('.jsn-error')
				.addClass('hide');

			// Disable all input fields
			self.panel
				.find('#btn-load-editions, input[name="username"], input[name="password"]')
				.attr('disabled', 'disabled');

			$.ajax({
				url: 'index.php?widget=upgrade&action=load-editions&template=' + self.params.template,
				type: 'POST',
				dataType: 'JSON',
				data: {
					username: txtUsername.val(),
					password: txtPassword.val()
				},
				success: function (response) {
					if (response.type == 'error') {
						var
						loginError = self.panel.find('#jsn-upgrade-login-error');
						loginError
							.text(response.data)
							.removeClass('hide');
						txtUsername
							.removeAttr('disabled')
							.focus();
						txtPassword
							.removeAttr('disabled');
						return;
					}

					var
					btnUpgrade = self.panel.find('#btn-start-upgrade'),
					editionPanel = self.panel.find('#jsn-upgrade-edition-select'),
					editionSelect = editionPanel.find('select[name="edition"]');

					if ($(response.data).size() == 1) {
						var
						nextEdition = response.data[0];
						nextEdition == 'PRO UNLIMITED' && self.params.edition == 'STANDARD'
							? replacementUpgrade(response.data[0])
							: startUpgrade(response.data[0], txtUsername.val(), txtPassword.val());
						
						return;
					}

					btnNext.addClass('hide');
					btnUpgrade.removeClass('hide');
					btnUpgrade.on('click', function () {
						startUpgrade(editionSelect.val(), txtUsername.val(), txtPassword.val());
					});

					$(response.data).each(function (index, value) {
						editionSelect.append($('<option/>', { 'value': value, 'text': value }));
					});

					editionSelect.on('change', function () {
						editionSelect.val() != ''
							? btnUpgrade.removeAttr('disabled')
							: btnUpgrade.attr('disabled', 'disabled');
					});

					editionPanel.removeClass('hide');
				}
			});
		}

		function replacementUpgrade (edition)
		{
			$.getJSON('index.php?widget=upgrade&action=upgrade&template=' + self.params.template + '&edition=' + edition, function (response) {
				self.panel.html(response.data);

				var
				divSuccess = self.panel.find('#jsn-upgrade-success'),
				liReplace = self.panel.find('#jsn-upgrade-replace');
				liReplace.removeClass('hide');
				
				$.getJSON('index.php?widget=upgrade&action=replace&template=' + self.params.template + '&edition=' + edition, function (response) {
					liReplace
						.removeClass('jsn-loading')
						.addClass('jsn-success');

					finishUpgrade();
				});
			});
		}

		/**
		 * Send request to start upgrade to selected edition
		 * 
		 * @return void
		 */
		function startUpgrade (edition, username, password)
		{
			self.panel.dialog('option', 'buttons', {});

			$.getJSON('index.php?widget=upgrade&action=upgrade&template=' + self.params.template + '&edition=' + edition, function (response) {
				self.panel.html(response.data);

				var
				downloadTask	= self.panel.find('li#jsn-upgrade-download'),
				downloadTitle	= downloadTask.find('.jsn-title'),
				downloadStatus	= downloadTask.find('.jsn-status');
				downloadTask.removeClass('hide');

				$.ajax({
					url: 'index.php?widget=upgrade&action=download-package&template=' + self.params.template,
					type: 'POST',
					data: {
						edition: edition,
						username: username,
						password: password
					},
					dataType: 'JSON',
					success: function (downloadResponse) {
						downloadTask.removeClass('jsn-loading');
						downloadTask.addClass('jsn-' + downloadResponse.type);

						if (downloadResponse.type == 'error') {
							downloadStatus.text(downloadResponse.data);

							return finishUpgrade(false);
						}

						installUpgrade();
					}
				});
			});
		}

		/**
		 * Send a request to start install upgrade package
		 * 
		 * @return  void
		 */
		function installUpgrade ()
		{
			var
			installTask		= self.panel.find('li#jsn-upgrade-install'),
			installTitle	= installTask.find('.jsn-title'),
			installStatus	= installTask.find('.jsn-status');

			installTask.removeClass('hide');

			$.getJSON('index.php?widget=upgrade&action=install&template=' + self.params.template, function (response) {
				installTask.removeClass('jsn-loading');
				installTask.addClass('jsn-' + response.type);

				if (response.type == 'error') {
					installStatus.text(response.data);

					return finishUpgrade(false);
				}

				migrateSettings(self.params.styleId, response.data.styleId);
			});
		}

		/**
		 * Migrate all settings from free edition to upgraded edition
		 * 
		 * @return  void
		 */
		function migrateSettings (from, to)
		{
			var
			migrateTask		= self.panel.find('li#jsn-upgrade-migrate'),
			migrateTitle	= migrateTask.find('.jsn-title'),
			migrateStatus	= migrateTask.find('.jsn-status');

			migrateTask.removeClass('hide');

			$.ajax({
				url: 'index.php?widget=upgrade&action=migrate&template=' + self.params.template,
				type: 'POST',
				data: {
					from: from,
					to: to
				},
				dataType: 'JSON',
				success: function (response) {
					migrateTask.removeClass('jsn-loading');
					migrateTask.addClass('jsn-' + response.type);

					if (response.type == 'error') {
						migrateStatus.text(response.data);

						return finishUpgrade(false);
					}

					finishUpgrade(to);
				}
			});
		}

		function finishUpgrade (styleId)
		{
			var btnFinish = self.panel.find('#jsn-upgrade-finish').removeAttr('disabled');

			if (styleId === false) {
				btnFinish.on('click', function () {
					self.panel.dialog('close');
				});

				return;
			} else if (styleId !== undefined) {
				btnFinish.on('click', function () {
					window.location = 'index.php?option=com_templates&task=style.edit&id=' + styleId;
				});
			} else {
				btnFinish.on('click', function () {
					window.location.reload();
				});
			}

			self.panel.find('#jsn-upgrade-success').removeClass('hide');
		}

		init();
	};

	/**
	 * Register jQuery plugin
	 *
	 * @param   element  button  Button that will triggered event to start install sample data
	 * @param   object   params  Object parameters
	 * 
	 * @return  void
	 */
	$.initTemplateUpgrade = function (button, params) {
		if ($.__template_admin_auto_upgrade__ === undefined)
			$.__template_admin_auto_upgrade__ = new JSNTemplateUpgrade(button, params);
	};
}(jQuery);