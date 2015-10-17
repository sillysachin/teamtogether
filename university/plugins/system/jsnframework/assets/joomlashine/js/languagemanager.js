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
	'jsn/libs/modal',
	'jquery.ui',
	'jquery.tipsy'
],

function ($, modal)
{
	// Declare JSNLangEditor contructor
	var JSNLangEditor = function(params)
	{
		// Object parameters
		this.params = $.extend({
			editSelector: '',
			revertSelector: ''
		}, params);

		this.lang = this.params.language || {};

		// Do initialization
		$(document).ready($.proxy(function() {
			// Get necessary elements
			this.$editLinks = $(this.params.editSelector);
			this.$revertLinks = $(this.params.revertSelector);

			this.initialize();
		}, this));
	};

	JSNLangEditor.prototype = {
		initialize: function() {
			// Register event handler to revert a language to default
			this.$revertLinks.click($.proxy(function(event) {
				var $target = $(event.target);

				if (confirm(this.lang['JSN_EXTFW_EDITORS_LANG_REVERT_CONFIRM'].replace('%s', $target.parent().children('span').text()))) {
					// Show loading indicator
					$target.removeClass('icon16 icon-refresh').addClass('jsn-icon16 jsn-icon-loading');
	
					// Revert now
					$.ajax({
						url: $target.attr('action'),
						context: this,
						complete: function(jqXHR, textStatus) {
							// Create a message
							this.$msg = this.$msg || $('<div class="alert jsn-box-shadow-medium" />')
							.append($('<a class="close" title="' + JSNCoreLanguage['JSN_EXTFW_GENERAL_CLOSE'] + '" href="javascript:void(0);" onclick="this.parentNode.style.display = \'none\';">×</a>'))
							.append($('<span class="message" />'))
							.appendTo($('#jsn-langs #jsnconfig-languagemanager-field').css('position', 'relative'));
	
							// Check if language reverted successful
							if (jqXHR.responseText.match(/^FAIL:/)) {
								// Set failure message then show
								this.$msg.removeClass('alert-success').addClass('alert-error')
								.children('span.message').html(jqXHR.responseText.replace(/^FAIL:/, ''));
	
								// Show warning icon
								$target.removeClass('jsn-icon16 jsn-icon-loading').addClass('jsn-icon16 jsn-icon-warning-sign').attr('title', this.lang['JSN_EXTFW_EDITORS_LANG_LAST_REVERT_FAIL']);
							} else {
								this.$msg.removeClass('alert-error').addClass('alert-success');
	
								// Generate success message
								this.$msg.children('span.message').text(this.lang['JSN_EXTFW_EDITORS_LANG_REVERT_SUCCESS'].replace('%s', $target.parent().children('span').text()));
	
								// Schedule to hide success message automatically
								setTimeout($.proxy(function() { this.$msg.fadeOut('slow'); }, this), 1500);
	
								// Remove revert icon
								$target.remove();
							}

							// Show message centralized
							this.$msg.show().css('margin-left', '-' + (this.$msg.outerWidth() / 2) + 'px');
						}
					});
				}
			}, this));

			// Register event handler to show language editor modal
			this.$editLinks.click($.proxy(function(event) {
				this.modal = this.modal || new modal({
					title: this.lang['JSN_EXTFW_EDITORS_LANG'],
					width: $(window).width() - 72,
					height: $(window).height() - 72,
					buttons: [
						{text: JSNCoreLanguage['JSN_EXTFW_GENERAL_CLOSE'], click: $.proxy(function() { this.modal.close(); }, this)}
					]
				});

				this.modal.options.url = $(event.target).attr('data-source');
				this.modal.show();
			}, this));

			// Handle window resize event
			$(window).resize($.proxy(function() {
				if (this.modal) {
					this.modal.setOption('width', $(window).width() - 72);
					this.modal.setOption('height', $(window).height() - 72);
				}
			}, this));
		}
	};

	return JSNLangEditor;
});
