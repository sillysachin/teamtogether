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
	$.JSNLayoutCustomizer = function(params) {
		// Initialize parameters
		this.params = $.extend({
			id: '',
			boundary: null,
			sortable: true,
			resizable: true
		}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.check, this));

		// Handle window resize event
		$(window).resize($.proxy(function(event) {
			if (event.target == window) {
				this.timer && clearTimeout(this.timer);
				this.timer = setTimeout($.proxy(this.check, this), 500);
			}
		}, this));
	};

	$.JSNLayoutCustomizer.prototype = {
		check: function() {
			$($('#jsn-main-nav > .ui-tabs-active > a').attr('href')).find('#' + this.params.id).each($.proxy(function(i, e) {
				this.init();
			}, this));

			$('#jsn-template-config-tabs').on('tabsactivate', $.proxy(function(event, ui) {
				$(ui.newPanel).find('#' + this.params.id).each($.proxy(function(i, e) {
					this.init();
				}, this));
			}, this));
		},

		init: function() {
			// Get necessary elements
			this.container = this.container || $('#' + this.params.id);
			this.columns = this.columns || this.container.children('li');

			// Detect parent fieldset
			if (!this.params.boundary) {
				this.params.boundary = this.container.parent();
	
				while (this.params.boundary[0].nodeName != 'BODY' && this.params.boundary[0].nodeName != 'FIELDSET') {
					this.params.boundary = this.params.boundary.parent();
				}
			}

			// Reset width for necessary elements
			this.columns.children().css('width', '');
			this.container.css('width', '');

			// Initialize variables
			this.maxWidth = this.params.boundary.parent().css('overflow-x', 'hidden').width();
			this.maxHeight = this.container.removeClass('hide').height();
			this.spacing = parseInt(this.columns.css('margin-left')) + parseInt(this.columns.css('margin-right'));
			this.step = parseInt((this.maxWidth - (this.spacing * 12)) / 12);

			// Calculate width for resizable columns
			var total = 0;

			this.columns.children().each($.proxy(function(i, e) {
				// Calculate column width
				var	span = parseInt($(e).attr('class').replace('span', '')),
					width = (this.step * span) + (this.spacing * (span - 1));

				$(e).css('width', width + 'px');

				// Count total width
				total += $(e).parent().outerWidth(true);
			}, this));

			// Update width for container
			this.container.css('width', total + 'px');

			// Initialize sortable
			if (this.params.sortable) {
				this.container.sortable({
					axis: 'x',
					placeholder: 'ui-state-highlight',
	
					start: $.proxy(function(event, ui) {
						ui.placeholder.append(ui.item.children().clone());
					}, this),
	
					stop: $.proxy(function(event, ui) {
						// Refresh columns ordering
						this.columns = this.container.children('li');
	
						// Re-initialize resizable
						this.initResizable();
					}, this)
				});
	
				this.container.disableSelection();
			}

			// Initialize resizable
			this.params.resizable && this.initResizable();

			// Generate grid preview
			if (!this.container.find('.jsn-layout-grid-preview').length) {
				this.container.append($('<div />', {'class': 'jsn-layout-grid-preview'}));
	
				for (var i = 0; i < 11; i++) {
					this.container.children('.jsn-layout-grid-preview').append($('<div />'));
				}
			}

			this.container.children('.jsn-layout-grid-preview').children().each($.proxy(function(i, e) {
				// Calculate left position for this grid
				var left = (this.step * (i + 1)) + (parseInt(this.columns.css('margin-left')) * (i + 1)) + (parseInt(this.columns.css('margin-right')) * i);

				$(e).css({
					left: left + 'px',
					height: this.container.height() + 'px'
				});
			}, this));

			// Reset overflow-x property for boundary element
			this.params.boundary.parent().css('overflow-x', '');
		},

		initResizable: function() {
			this.columns.children().each($.proxy(function(i, e) {
				// Reset resizable column
				!$(e).hasClass('ui-resizable') || $(e).resizable('destroy');
				!e.__next || (e.__next = null);

				// Initialize resizable column
				if (i + 1 < this.columns.length) {
					$(e).resizable({
						handles: 'e',
						minWidth: this.step,
						grid: [this.step, this.maxHeight],

						start: $.proxy(function(event, ui) {
							ui.element[0].__next = ui.element[0].__next || ui.element.parent().next().children();

							// Store original width for next sibling element
							ui.element[0].__next[0].originalWidth = ui.element[0].__next.width();

							// Show grid preview
							this.container.children('.jsn-layout-grid-preview').show();

							// Calculate max span
							ui.element[0].__maxSpan = (parseInt(ui.element.children('input').val().replace('span', '')) + parseInt(ui.element[0].__next.children('input').val().replace('span', '')) - 1);

							// Temporary disable sortable move icon
							var allSortableLists = $('ul.jsn-layout.ui-sortable');

							allSortableLists.attr('class', allSortableLists.attr('class').replace('ui-sortable', 'ui-unsortable'));
						}, this),

						resize: $.proxy(function(event, ui) {
							var span = parseInt(ui.element.width() / this.step);

							// Verify span
							if (span > ui.element[0].__maxSpan) {
								span = ui.element[0].__maxSpan;
							} else if (span < 1) {
								span = 1;
							}

							// Calculate correct width to align column
							var	thisWidth = (this.step * span) + (this.spacing * (span - 1)),
								nextWidth = ui.element[0].__next[0].originalWidth - (thisWidth - ui.originalSize.width);

							// Snap column to grid
							ui.element.css('width', thisWidth + 'px');

							// Update field value and column class
							ui.element.children('input').val('span' + span);
							ui.element.attr('class', ui.element.attr('class').replace(/\bspan\d+\b/, 'span' + span));

							// Reset nested layout
							ui.element[0].__nestedLayout && ui.element[0].__nestedLayout.init();

							// Resize next sibling element as well
							ui.element[0].__next.css('width', nextWidth + 'px');

							// Update field value and column class
							ui.element[0].__next.children('input').val('span' + (ui.element[0].__maxSpan - span + 1));
							ui.element[0].__next.attr('class', ui.element[0].__next.attr('class').replace(/\bspan\d+\b/, 'span' + (ui.element[0].__maxSpan - span + 1)));

							// Reset nested layout
							ui.element[0].__next[0].__nestedLayout && ui.element[0].__next[0].__nestedLayout.init();
						}, this),

						stop: $.proxy(function(event, ui) {
							// Hide grid preview
							this.container.children('.jsn-layout-grid-preview').hide();

							// Restore sortable move icon
							var allSortableLists = $('ul.jsn-layout.ui-unsortable');

							allSortableLists.attr('class', allSortableLists.attr('class').replace('ui-unsortable', 'ui-sortable'));
						}, this)
					});
				}

				// Check if this column has nested column
				var nested = $(e).children().children('ul.jsn-layout');

				if (nested.length) {
					e.__nestedLayout = new $.JSNLayoutCustomizer({
						id: nested.attr('id'),
						boundary: $(e),
						sortable: false
					});
				}

				$(e).parent().css('box-shadow', $(e).children().css('box-shadow'));
			}, this));
		}
	};
})(jQuery);
