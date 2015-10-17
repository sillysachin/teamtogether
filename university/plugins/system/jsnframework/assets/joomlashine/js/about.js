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
	// Declare JSNAbout contructor
	var JSNAbout = function(params)
	{
		// Object parameters
		this.params = $.extend({}, params);
		this.lang = this.params.language || {};

		// Set event handler
		$(document).ready($.proxy(function() {
			this.modalLink = $('#jsn-about-promotion-modal');
			this.initialize();
		}, this));
	};

	JSNAbout.prototype = {
		initialize: function () {
			// Register event to show modal window
			this.modalLink.click($.proxy(function(event) {
				event.preventDefault();

				this.modal = this.modal || new modal({
					url: this.modalLink.attr('href'),
					title: this.lang['JSN_EXTFW_ABOUT_SEE_OTHERS_MODAL_TITLE'],
					width: 640,
					height: 575,
					buttons: [{text: JSNCoreLanguage['JSN_EXTFW_GENERAL_CLOSE'], click: $.proxy(function() { this.modal.close(); }, this)}]
				});

				this.modal.show();
			}, this));
		}
	}

	return JSNAbout;
});
