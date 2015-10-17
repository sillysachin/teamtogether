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
	// Declare JSNMedia contructor
	var JSNMedia = function(params)
	{
		// Object parameters
		this.params = $.extend({}, params);
		this.lang = this.params.language || {};

		// Set event handler
		$(document).ready($.proxy(function() {
			this.modalLink = $(this.params.field).next();
			this.initialize();
		}, this));
	};

	JSNMedia.prototype = {
		initialize: function() {
			// Register event to show modal window
			this.modalLink.click($.proxy(function(event) {
				event.preventDefault();

				this.modal = this.modal || new modal({
					title: this.lang['JSN_EXTFW_CONFIG_CLICK_TO_SELECT'],
					width: $(window).width() - 72,
					height: $(window).height() - 72,
					buttons: [{text: JSNCoreLanguage['JSN_EXTFW_GENERAL_CLOSE'], click: $.proxy(function() { this.modal.close(); }, this)}]
				});

				this.modal.options.url = this.params.url.replace(/current=[^&]*/, 'current=' + $(this.params.field).attr('value'));
				this.modal.show();
			}, this));

			// Handle window resize event
			$(window).resize($.proxy(function() {
				if (this.modal) {
					this.modal.setOption('width', $(window).width() - 72);
					this.modal.setOption('height', $(window).height() - 72);
				}
			}, this));

			// Setup clear button
			if (this.modalLink.next('button')) {
				this.modalLink.next('button').click($.proxy(function() {
					this.update('');
				}, this));
			}

			// Create selection update function
			window.JSNMediaUpdateField = $.proxy(this.update, this);
		},
		
		update: function(selected, field) {
			field = field || this.params.field;

			$(field).attr('value', selected);
			$(field).trigger('change');

			this.modal && this.modal.close();
		}
	};

	return JSNMedia;
});
