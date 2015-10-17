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
	'jquery.ui'
],

function ($, modal)
{
	// Declare JSNReviewPopup contructor
	var JSNReviewPopup = function(params)
	{
		// Object parameters
		this.params = $.extend({url: ''}, params);
		this.lang = this.params.language || {};

		// Do initialization
		$(document).ready($.proxy(function() {
			this.initialize();
		}, this));
	};

	JSNReviewPopup.prototype = {
		initialize: function() {
			// Popup a modal asking user to review product on JED
			this.modal = this.modal || new modal({
				url: this.params.url,
				title: this.lang['JSN_EXTFW_CHOOSERS_REVIEW_ON_JED'],
				width: 480,
				height: 320,
				buttons: [
					{text: JSNCoreLanguage['JSN_EXTFW_GENERAL_CLOSE'], click: $.proxy(function() { this.modal.close(); }, this)}
				]
			});

			// Disable scrolling in modal body
			this.modal.iframe.parent().css('overflow', 'hidden');

			this.modal.show();
		}
	};

	return JSNReviewPopup;
});
