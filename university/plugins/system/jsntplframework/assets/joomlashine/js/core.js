jQuery.noConflict();

(function ($) {
	"use strict";

	$.JSNTPLFrameworkCore = function (params)
	{
		this.params = params;
		this.body      = document.body;
		this.container = document.getElementById('style-form');
		this.navTabs   = $(this.container.querySelector('ul.nav.nav-tabs'));
		this.mediaButton = $(this.container.querySelectorAll('.btn-media'));
		this.clearMediaButton = $(this.container.querySelectorAll('.btn-media-clear'));
		this.colorList = $(this.container.querySelectorAll('.jsn-color-list'));
		this.sortableColorList = this.colorList.find('.jsn-items-list');
		this.coloredLogo = $('input[name="jsn[logoColored]"]');

		this.toggleButton = $(document.getElementById('jsn-toggle-menu'));
		this.checkAllGroup = $(this.container.querySelectorAll('input[name="checkAll"]'));

		this.verifyFolderBtn = $(this.container.querySelectorAll('.btn-verify-folder'));
		this.closeButton = $(this.container.querySelectorAll('.jsn-close-message'));

		this.sampleDataButton = $(document.getElementById('install-sample-data'));
		this.updateButton = $('.jsn-update-link');
		this.upgradeButton = $('.jsn-upgrade-link');
		this.getQuickstartButton = $('#get-quickstart-package');
		/*this.seeMoreButton = $('a#jsn-about-promotion-modal');
		this.seeMorePanel = $('<iframe/>', { 'id': 'jsn-see-more-products', 'scrolling': 'no', 'width': 640, 'height': 510 });
		this.seeMorePanel.appendTo(this.body);*/
		this.checkIntegrity = $('span.jsn-integrity-check a, span.jsn-integrity-notchange a');
		this.seeModifiedFiles = $('span.jsn-integrity-changed > a:first-child');

		this.integrityDetails = $('<div />', { 'id': 'jsn-integrity-details', 'class': 'jsn-bootstrap' });
		this.integrityDetailsFiles = $('<ul />', { 'id': 'jsn-integrity-details-files' });

		this.init();
		this.addEvents();
		this.coloredLogo.trigger('change');
	};

	$.JSNTPLFrameworkCore.prototype = {
		init: function () {
			var self = this;

			// Setup template parameters restoration
			new $.JSNTplMaintenance({
				template: self.params.template,
				styleId: self.params.styleId,
				lang: self.params.lang
			});

			$.initSampleDataInstallation(this.sampleDataButton, this.params);
			$.initTemplateUpdate(this.updateButton, this.params);
			$.initTemplateUpgrade(this.upgradeButton, this.params);
			$.initDownloadQuickstartPackage(this.getQuickstartButton, this.params);

			/*this.seeMorePanel
				.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					width: 640,
					height: 600,
					modal: true,
					title: 'See Other Templates',
					dialogClass: 'jsn-see-more-window',
					open: $.proxy(function () {
						this.seeMorePanel.css({
							width: 640,
							height: 510
						});
					}, this),
					buttons: {
						'Close': function () {
							self.seeMorePanel.dialog('close');
						}
					}
				});*/

			// Initialize file modification dialog
			this.integrityDetails
				.append(this.integrityDetailsFiles)
				.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					width: 640,
					height: 600,
					modal: true,
					title: 'Modified Files List',
					dialogClass: 'jsn-integrity-modified-files',
					open: $.proxy(this.loadModifiedFiles, this),
					buttons: {
						'Close': function () {
							self.integrityDetails.dialog('close');
						}
					}
				});

			// Initialize image selector
			this.mediaButton.imageSelector({
				basePath: '/images',
				template: this.params.template
			});

			// Initialize sortable for color list
			if (!this.sortableColorList.parent().hasClass('disabled')) {
				this.sortableColorList.sortable({
					forceHelperSize: true,
					forcePlaceholderSize: true,
					placeholder: 'ui-state-highlight',
					axis: 'y',
					stop: $.proxy(function(event, ui) {
						ui.item.css({
							'position': '',
							'top': '',
							'left': ''
						});

						this.sortableChanged(event, ui);
					}, this),
					update: $.proxy(this.sortableChanged, this),
					change: $.proxy(this.sortableChanged, this)
				});
			}

			// Set last actived tab to active
			var lastActivedTabIndex = $.cookie('jsn-active-tab');
			if (lastActivedTabIndex != null) {
				this.navTabs.find('li:eq(' + lastActivedTabIndex + ') a').tab('show');
			}

			// Update checkbox state for menu assignment
			$(this.container.querySelectorAll('#menu-links ul')).each(function () {
				var
				checkboxHeader = $(this.querySelector('li.menu-type-header input')),
				checkboxItems  = $(this).find('li:not(li.menu-type-header) input.menu-item');

				checkboxItems.filter(':checked').size() == checkboxItems.size()
					? checkboxHeader.attr('checked', 'checked')
					: checkboxHeader.removeAttr('checked');
			});

			// Setup action to verify folder
			this.verifyFolderBtn.click(function(event) {
				var $target = $(event.target),
					markup = $target.html();

				// Switch loading status
				$target.html('Checking...');

				// Request server to check for directory permission
				$.getJSON(
					'index.php?widget=media&action=verifyFolder&folder=' + $target.prev().attr('value') + '&template=' + self.params.template,
					function(response) {
						if (response.type == 'success') {
							// Switch status
							$target.html(markup);

							if (response.data.pass) {
								$target.parent().next().next()
									.removeClass('label-important').addClass('label-success')
									.removeClass('hide').html(response.data.message);
							} else {
								$target.parent().next().next()
									.removeClass('label-success').addClass('label-important')
									.removeClass('hide').html(response.data.message);
							}
						} else {
							$target.parent().next().next()
								.removeClass('label-success').addClass('label-important')
								.removeClass('hide').html('Unable to connect with server to verify directory.');
						}
					}
				);
			});

			// Setup close message button
			this.closeButton.click(function(event) {
				$(event.target).parent().hide();
			});

			$('label[rel="tipsy"]').tipsy({
				'gravity': 'w',
				'fade': true,
				'html': true
			});
		},

		addEvents: function () {
			this.clearMediaButton.on('click', $.proxy(this.clearMediaButtonClicked, this));
			this.navTabs.on('shown', $.proxy(this.navTabsShown, this));
			this.sortableColorList.delegate('input[type=checkbox]', 'change', $.proxy(function (e) {
				var el = $(e.target),
					sortable = el.closest('ul.jsn-items-list');

				this.updateColorList(sortable);
			}, this));
			this.coloredLogo.on('change', $.proxy(function (e) {
				var
				el = $(e.target),
				disableItems = $('#jsn_logoFile, #jsn_logoFile_select, #jsn_logoFile_clear');

				if (!el.is(':checked'))
					return;

				el.val() == 1
					? disableItems.addClass('disabled').attr('disabled', 'disabled')
					: disableItems.removeClass('disabled').removeAttr('disabled');
			}));

			this.checkIntegrity.on('click', $.proxy(this.checkFilesModification, this));

			this.toggleButton.on('click', $.proxy(this.updateGroupSelection, this));
			this.checkAllGroup.on('change', $.proxy(this.updateMenuSelection, this));
			//this.seeMoreButton.on('click', $.proxy(this.openSeeMoreProducts, this));

			this.seeModifiedFiles.on('click', $.proxy(function () {
				this.integrityDetails.dialog('open');
			}, this));

			// Setup notice for Pro only features
			var editionAlert = function() {
				$('#jsn_pro_edition_only_modal').removeClass('hide');
			};

			$('.jsn-pro-edition-only').each(function(i, e) {
				if (e.nodeName == 'INPUT') {
					if ($(e).attr('type') != 'text') {
						$(e).click(editionAlert);
					} else {
						$(e).focus(editionAlert);
						$(e).change(editionAlert);
					}
				} else if (e.nodeName == 'OPTION') {
					$(e).parent().change(function(event) {
						if ($(this.options[this.selectedIndex]).hasClass('jsn-pro-edition-only')) {
							editionAlert();

							// Restore last selected
							this.selectedIndex = this.lastSelected || 0;
							$(this).trigger('change');
						} else {
							this.lastSelected = this.selectedIndex;
						}
					});
				}
			});
		},

		checkFilesModification: function (e) {
			e.preventDefault();

			var
			self = this,
			integrityContainer = $('dd#jsn-integrity'),
			integrityStatus = integrityContainer.find('span.jsn-integrity-status');

			integrityContainer.find('>:not(span.jsn-integrity-status)').addClass('hide');
			integrityStatus.removeClass('hide');

			$.getJSON('index.php?widget=integrity&action=check&template=' + this.params.template, function (response) {
				integrityStatus.addClass('hide');
				integrityContainer
					.find(response.data.hasModification == true ? 'span.jsn-integrity-changed' : 'span.jsn-integrity-notchange')
					.removeClass('hide');

				$(response.data.files.add).each(function (index, file) {
					self.integrityDetailsFiles
						.append(
							$('<li />', { 'class': 'jsn-file-added', 'text': ' ' + file })
								.prepend($('<i/>', {'class': 'icon-plus'}))
						);
				});

				$(response.data.files.edit).each(function (index, file) {
					self.integrityDetailsFiles
						.append(
							$('<li />', { 'class': 'jsn-file-edited', 'text': ' ' + file })
								.prepend($('<i/>', {'class': 'icon-pencil'}))
						);
				});

				$(response.data.files['delete']).each(function (index, file) {
					self.integrityDetailsFiles
						.append(
							$('<li />', { 'class': 'jsn-file-deleted', 'text': ' ' + file })
								.prepend($('<i/>', {'class': 'icon-trash'}))
						);
				});
			});
		},

		/*openSeeMoreProducts: function (e) {
			e.preventDefault();
			this.seeMorePanel.attr('src', e.currentTarget.href);
			this.seeMorePanel.dialog('open');
		},*/

		navTabsShown: function (e) {
			$.cookie('jsn-active-tab', $(e.target).parent().index());
		},

		clearMediaButtonClicked: function (e) {
			var el = e.target.nodeName == 'A' ? $(e.target) : $(e.target).closest('a'),
				elTarget = $(el.attr('data-target'));

			if (el.hasClass('disabled'))
				return;

			elTarget.val(el.attr('data-default'));
		},

		sortableChanged: function (e, ui) {
			this.updateColorList(e.target);
		},

		updateColorList: function (sortable) {
			var colors = [],
				checkedColors = [],
				el = $(sortable);

			if (!el.hasClass('jsn-color-list')) {
				el.closest('div.jsn-color-list');
			}

			var inputTarget = $(el.attr('data-target')),
				checkboxes  = el.find('li.jsn-item input[type="checkbox"]');

			checkboxes.each(function () {
				colors.push(this.value);
				if (this.checked) {
					checkedColors.push(this.value);
				}
			});

			inputTarget.val(JSON.stringify({
				list: colors,
				colors: checkedColors
			}));
		},

		updateGroupSelection: function (e) {
			this.checkAllGroup.filter(':checked').size() > 0
				? this.checkAllGroup.removeAttr('checked')
				: this.checkAllGroup.attr('checked', 'checked');

			this.checkAllGroup.trigger('change');
		},

		updateMenuSelection: function (e) {
			var
			el = $(e.target),
			parent = el.closest('ul');

			el.attr('checked') === 'checked'
				? parent.find('input.menu-item').attr('checked', 'checked')
				: parent.find('input.menu-item').removeAttr('checked');
		}
	};
})(jQuery);
