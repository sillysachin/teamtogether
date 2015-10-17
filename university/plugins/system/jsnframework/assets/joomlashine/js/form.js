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
	'jquery.ui'
], 

function ($)
{
	//Declare JSN Form contructor
	var JSNForm = function(form, finalize) {
		var self = this;

		this.form = form;
		this.finalize = finalize;


		$(document).ready($.proxy(function() {
			this.initialize();
		}, this));
	};

	//Declare JSN Form methods
	JSNForm.prototype = {
		/**
		 * Initialize 
		 * 
		 * @return {[type]} [description]
		 */
		initialize: function () {
			// Get all form buttons
			this.buttons = $('.form-actions input[type="button"], .form-actions button', this.form).each(function() {
				if ($(this).attr('track-change') !== 'disabled') {
					$(this)
						.attr('disabled', 'disabled')
						.after('<span class="jsn-form-saving" style="display: none;">&nbsp;<i class="jsn-icon16 jsn-icon-loading"></i></span>');
				}
			});

			this.registerEvents();
		},

		/**
		 * Method to add event handler for elements inside form
		 * 
		 * @return void
		 */
		registerEvents: function () {
			var self = this;

			// Handle changed event for the form
			this.form.change(function (event) {
				self.updateForm(this);
			});

			// Update form state when text field is changed
			this.form.each(function () {
				var form = $(this);

				// Save current values of the form
				form.data('jsn-form-data', form.serialize());

				// Handle keyup event to update form state
				form.delegate('input[type="text"]', 'keyup', $.proxy(function () {
					form.trigger('change');
				}, form));
			});

			// Set event handler to submit form data
			this.buttons.click($.proxy(function(event) {
				this.submit(event);
			}, this));
		},

		/**
		 * Refresh state of the elements when form is changed
		 * @return void
		 */
		updateForm: function (form) {
			var formData = $(form).data('jsn-form-data'),
				currentFormData = $(form).serialize();

			$('.form-actions input[type="button"], .form-actions button', form).each(function() {
				if ($(this).attr('track-change') !== 'disabled') {
					(formData != currentFormData)
						? $(this).removeAttr('disabled')
						: $(this).attr('disabled', 'disabled');
				}
			});
		},

		submit: function(event) {
			// Looking for clicked button
			var btn 		= $(event.target),
				btnValue 	= btn.val(),
				form 		= btn.closest('form'),
				task 		= $('input[name="task"]', form),
				view		= $('input[name="view"]', form);

			// Set some hidden form fields
			if (task.size() > 0) task.val(btnValue);
			if (view.size() > 0 && btnValue.indexOf('.') > 0) view.val(btnValue.substr(0, btnValue.indexOf('.')));

			// Submit form
			if (btn.attr('ajax-request') === 'disabled') {
				var form = btn.closest('form');
				if (form.size() > 0) {
					form.get(0).submit();
				}
			} 
			else {
				// Update indicator
				btn.attr('disabled', 'disabled');
				$('span.jsn-form-saving', btn.parent()).show();

				// Send request
				$.ajax({
					url: form.attr('action'),
					type: form.attr('method'),
					data: form.serialize() + '&tmpl=component&ajax=1'
				})
				.done($.proxy(function(response) {
					// Update indicator
					$('span.jsn-form-saving', btn.parent()).hide();

					form.data('jsn-form-data', form.serialize());

					// Finalize form submission results
					this.finalize(response);
				}, this));
			}
		}
	};

	return JSNForm;
});