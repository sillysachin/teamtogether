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
	$.JSNFontCustomizer = function(params) {
		// Initialize parameters
		this.params = $.extend({
			id: 'jsn_fontStyle',
			sections: ['heading', 'menu', 'body'],
			template: '',
			lang: {JSN_TPLFW_FONT_FILE_SELECT:'', JSN_TPLFW_FONT_FILE_UPLOADING:'', JSN_TPLFW_FONT_FILE_NOT_SUPPORTED:''}
		}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init, this));
	};

	$.JSNFontCustomizer.prototype = {
		init: function() {
			var self = this;

			// Get style selector
			this.styleSelector = $('#' + this.params.id + '_style');

			// Get style editor
			this.styleEditor = {};

			this.styleSelector.children().each($.proxy(function(i, e) {
				var editor = $('#' + this.params.id + '_style_' + $(e).val());

				if (editor.length) {
					this.styleEditor[$(e).val()] = editor;

					// Initialize standard font family / Google font face preview
					editor.find('select.jsn-font-select-box').chosen({
						inherit_select_classes: true,
						width: '220px'
					});

					// Initialize font file uploader
					editor.find('input[type="file"] + button').click(function() {
						// Verify selected font file
						if ($(this).prev().val() == '' || !$(this).prev().val().match(/\.(ttf|otf|eot|svg|woff)$/)) {
							// Show error message
							$(this).parent().prev().removeClass('hide').children('span').removeClass('label-success').addClass('label-important').text(
								$(this).prev().val() == ''
								? self.params.lang['JSN_TPLFW_FONT_FILE_SELECT']
								: self.params.lang['JSN_TPLFW_FONT_FILE_NOT_SUPPORTED']
							);

							// Abort upload
							return false;
						}

						// Initialize an iframe for uploading font in background
						if (!self.uploaderIframe) {
							self.uploaderIframe = $(
								'<iframe />',
								{
									id: 'jsn-font-uploader-iframe',
									name:  'jsn-font-uploader-iframe',
									src: 'about:blank',
									'class': 'hide'
								}
							).appendTo(document.body);

							self.uploaderIframe.load(function() {
								var uploader = $('form.jsn-font-uploader.uploading-font').removeClass('uploading-font');

								if ($(this).contents().text() == 'OK') {
									// Update status message
									uploader.prev().children('span').addClass('label-success').text(
										uploader.children('input').val()
									);

									// Update value
									uploader.prev().prev().val(uploader.children('input').val());
								} else {
									// Show error message
									uploader.prev().children('span').addClass('label-important').text($(this).contents().text());
								}

								// Hide loading indicator
								uploader.prev().children('i').addClass('hide');
							});
						}

						// Prepare form for uploading font in background
						if (!this.initialized) {
							$(this).parent().attr('action', $(this).parent().attr('action') + '&template=' + self.params.template);
							$(this).parent().attr('target', 'jsn-font-uploader-iframe');

							this.initialized = true;
						}

						// Show status message
						$(this).parent().prev().removeClass('hide').children('span').removeClass('label-success').removeClass('label-important').text(
							self.params.lang['JSN_TPLFW_FONT_FILE_UPLOADING'].replace('%s', $(this).prev().val())
						);

						// Show loading indicator
						$(this).parent().prev().children('i').removeClass('hide');

						// Submit uploader form
						$(this.form).addClass('uploading-font')[0].submit();
					});

					for (var i = 0; i < this.params.sections.length; i++) {
						// Handle change event for font type selector
						editor.find('#' + this.params.id + '_' + $(e).val() + '_' + this.params.sections[i] + '_type').change(function() {
							// Toggle editor
							for (var k in {standard:'', google:'', embed:''}) {
								if ($(this).val() == k) {
									$('#' + $(this).attr('id') + '_' + k).removeClass('hide');
								} else {
									$('#' + $(this).attr('id') + '_' + k).addClass('hide');
								}
							}
						});
					}
				}
			}, this));

			// Handle overall style selector change
			this.styleSelector.change(function() {
				// Hide all style editors
				for (var i in self.styleEditor) {
					self.styleEditor[i].addClass('hide');
				}

				// If selected style has editor, show the associated editor
				if (typeof self.styleEditor[$(this).val()] != 'undefined') {
					self.styleEditor[$(this).val()].removeClass('hide');
				}
			});
		}
	};
})(jQuery);
