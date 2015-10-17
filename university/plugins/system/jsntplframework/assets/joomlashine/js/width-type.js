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
	$.JSNWidthType = function(params) {
		// Initialize parameters
		this.params = $.extend({
			id: 'jsn_templateWidth',
			lang: {JSN_TPLFW_LAYOUT_YOU_MUST_SELECT_AN_OPTION:''}
		}, params);

		// Initialize functionality
		$(document).ready($.proxy(this.init, this));
	};

	$.JSNWidthType.prototype = {
		init: function() {
			var self = this;

			// Get width type selector
			this.typeSelector = $('#' + this.params.id + '_type');

			// Get width type editor
			this.typeEditor = {};

			this.typeSelector.children().each($.proxy(function(i, e) {
				var editor = $('#' + this.params.id + '_type_' + $(e).val());

				if (editor.length) {
					this.typeEditor[$(e).val()] = editor;

					editor.find('input[type="checkbox"]').change(function(event) {
						event.preventDefault();

						if (!this.checked) {
							var validAction = false;

							$(this).parent().parent().find('input[type="checkbox"]').each(function(i, e) {
								!e.checked || (validAction = true);
							});

							if (!validAction) {
								alert(self.params.lang['JSN_TPLFW_LAYOUT_YOU_MUST_SELECT_AN_OPTION']);

								// Re-check the check-box
								this.checked = true;
							}
						}
					});
				}
			}, this));

			// Handle overall width type selector change
			this.typeSelector.change(function() {
				// Hide all width type editors
				for (var i in self.typeEditor) {
					self.typeEditor[i].addClass('hide');
				}

				// If selected width type has editor, show the associated editor
				if (typeof self.typeEditor[$(this).val()] != 'undefined') {
					self.typeEditor[$(this).val()].removeClass('hide');
				}
			});
		}
	};
})(jQuery);
