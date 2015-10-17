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
	$.JSNSocialIntegration = function(params) {
		// Initialize parameters
		this.params = $.extend({
			id: 'jsn_socialIcons',
			language: {
				JSN_TPLFW_SAVE: 'Save',
				JSN_TPLFW_CLOSE: 'Close'
			}
		}, params);

		this.lang = this.params.language;

		// Initialize functionality
		$(document).ready($.proxy(this.init, this));
	};

	$.JSNSocialIntegration.prototype = {
		init: function() {
			// Get necessary elements
			this.container = $('#' + this.params.id + ' ul.jsn-items-list');
			this.message = $('#' + this.params.id + '_message');
			this.$modal = $('#' + this.params.id + '_modal');

			// Initialize sortable list
			this.initSortable();

			// Setup modal to configure social channels
			$('.jsn-social-integration-button').click($.proxy(function() {
				this.$modal.dialog('open');
			}, this));

			var buttons = {};
			buttons[this.lang.JSN_TPLFW_SAVE] = $.proxy(this.save, this);
			buttons[this.lang.JSN_TPLFW_CLOSE] = function() { $(this).dialog('close'); };

			this.$modal.dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				resizable: false,
				width: 750,
				height: 600,
				dialogClass: 'jsn-master',
				buttons: buttons
			});
		},

		save: function() {
			var reinit = false;

			this.$modal.find('input[type="text"]').each($.proxy(function(i, e) {
				var dataTarget = this.container.find('#' + $(e).attr('data-target'));

				if ($(e).val() != '' && !dataTarget.length) {
					this.container.append(
						$('<li class="jsn-item ui-state-default" />').html(
							'<input id="' + $(e).attr('data-target') + '" type="hidden" name="' + $(e).attr('data-name') + '" value="' + $(e).attr('data-value') + '" />'
							+
							$(e).parent().prev().text()
						)
					);

					reinit = true;
				} else if ($(e).val() == '' && dataTarget.length) {
					dataTarget.parent().remove();

					reinit = true;
				}
			}, this));

			if (reinit) {
				// Show/hide elements
				if (this.container.children('li.jsn-item').length) {
					this.message.addClass('hide');
					this.container.parent().removeClass('hide');
				} else {
					this.message.removeClass('hide');
					this.container.parent().addClass('hide');
				}

				// Re-initialize sortable list
				this.initSortable();
			}

			// Hide modal
			this.$modal.dialog('close');
		},

		initSortable: function() {
			this.container.sortable({
				forceHelperSize: true,
				forcePlaceholderSize: true,
				placeholder: 'ui-state-highlight',
				axis: 'y',
				stop: function(event, ui) {
					ui.item.css({
						'position': '',
						'top': '',
						'left': ''
					});
				}
			});

			this.container.disableSelection();
		}
	};
})(jQuery);
