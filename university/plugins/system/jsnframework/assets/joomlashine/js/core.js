define([
	'jquery',
	'jquery.ui'
],

function ($)
{
	/**
	 * This is a core object that will use for all
	 * view of the component
	 *
	 * @since  1.0.0
	 */
	var JSNCore = function (params)
	{
		this.params = $.extend({
			useAjax 	: true,
			useForm 	: true,
			useTabs 	: true,
			useTooltip 	: true
		}, params);

		this.lang = this.params.lang;

		$(document).ready($.proxy(function() {
			this.initialize();
		}, this));
	};

	JSNCore.prototype = {
		/**
		 * Prepare common components for global use
		 * 
		 * @return  void
		 */
		initialize: function () {
			if (this.params.useAjax === true) {
				this.initAjax();
				this.initAjaxEvents();
			}

			if (this.params.useTooltip === true)
				this.initTooltip();
			
			if (this.params.useForm === true) {
				this.initForm();
				this.initFormEvents();
			}

			if (this.params.useTabs === true) {
				this.initTabs();
				this.initTabsEvents();
			}
		},

		initAjax: function () {
			$.ajaxSetup({
				cache: false,
				complete: $.proxy(function () {
					this.initialize();
				}, this)
			});
		},

		initAjaxEvents: function () {
			// Ajax links
			$('a[ajax-request="yes"]')
				.unbind('click.jsn')
				.bind('click.jsn', this.ajaxLinkClicked);
		},

		/**
		 * Initial tooltips
		 * 
		 * @return  void
		 */
		initTooltip: function () {
		},

		/**
		 * Setting up base events for form, action buttons
		 * 
		 * @return  void
		 */
		initForm: function () {
			var self = this;

			$('form:not([data-initialized])').each(function () {
				var form = $(this),
					buttons  = $('.form-actions button', form),
					fileFields = $('input[type="file"]', form);

				// Save current form data
				form.data('jsn-form-data', self.serializeForm(form));

				// Add form changed event to update buttons state
				form.change(function () {
					var currentData = self.serializeForm(form),
						isChanged   = currentData != form.data('jsn-form-data');

					form.trigger('formStateChanged', { isChanged: isChanged });
				});

				// Save current data when submit form
				form
					.unbind('submit.jsn')
					.bind('submit.jsn', function () {
						form.data('jsn-form-data', self.serializeForm(form));
						form.trigger('change');
					});

				fileFields.change(function () {
					form.trigger('formStateChanged', { isChanged: true });
				});

				// Click event for buttons to submit form through Ajax
				buttons.click(function (e) {
					e.preventDefault();

					var button 		= $(this),
						form 		= button.closest('form'),
						formData 	= form.serialize(),
						formAction 	= form.attr('action'),
						task 		= button.val(),
						view 		= (task.indexOf('.')) ? task.substring(0, task.indexOf('.')) : '';

					// Fire formBeforeSubmit event
					form.trigger('formBeforeSubmit');
					form.find('input[name="view"]').val(view);

					if (task != '') {
						$('input[name="task"]').val(task);
					}

					var isMultipart = form.attr('enctype') == 'multipart/form-data',
						isAjax 		= button.attr('ajax-request') == 'yes';

					if (!isAjax) {
						form.get(0).submit();
						return;
					}

					// Add loading icon
					button.data('jsn-default-text', button.html());
					button.html(JSNCoreLanguage['JSN_EXTFW_GENERAL_LOADING']);

					// Trigger form state changed event
					form.trigger('formStateChanged', { isChanged: false });

					if (isMultipart == true) {
						// Submit form through iframe to allow file upload
						var iframeId = 'jsn-form-target-' + (new Date()).getTime(),
							iframe = $('<iframe/>', { 'src': 'about:blank', 'id': iframeId }),
							formTarget = form.attr('target');

						// Change target of the for that point to iframe
						form.attr('target', iframeId);

						// Add loaded event for the iframe
						iframe.unbind('load').load(function () {
							form.data('jsn-form-data', self.serializeForm(form));
							form.trigger('formSubmitted', { response: iframe.contents().html() });

							// Restore default target for the form
							(formTarget != '')
								? form.attr('target', formTarget)
								: form.removeAttr('target');

							iframe.remove();
						});
					}
					else {
						$.ajax({
							url: form.attr('action'),
							type: form.attr('method'),
							data: form.serialize() + '&tmpl=component&ajax=1'
						})
						.done(function (response) {
							form.data('jsn-form-data', self.serializeForm(form));
							form.trigger('formSubmitted', { button: button, content: response });
							button.html(button.data('jsn-default-text'));
						});
					}
				});

				// Save state of the form
				form.attr('data-initialized', 'yes');
			});
		},

		initFormEvents: function () {
			var self = this;

			// Listen formStateChanged event to update buttons state
			$('form').bind('formStateChanged', function (event, state) {
				this.timer && clearTimeout(this.timer);

				// Wait till all handlers attached to change event has been executed
				this.timer = setTimeout(
					$.proxy(function() {
						var	hasInvalid = $(this).find('input.invalid, select.invalid, textarea.invalid'),
							trackButtons = $(this).find('.form-actions button[track-change="yes"]');
			
						if (!hasInvalid.length) {
							state.isChanged == true ? trackButtons.removeAttr('disabled') : trackButtons.attr('disabled', 'disabled');
						} else {
							trackButtons.attr('disabled', 'disabled');
						}
					}, this),
					500
				);
			});

			$('form').bind('formSubmitted', function (event, response) {
				var message = $(
					'<div class="alert alert-block fade in"><a href="javascript:void(0);" title="'
					+ JSNCoreLanguage['JSN_EXTFW_GENERAL_CLOSE'] + '" class="close">×</a>'
					+ response.content + '</div>'
				);

				response.button.prev().empty().append(message);
				response.button.prev().find('a.close').click(function() { $(this).parent().remove(); });
			});

			// Trigger default events on page init
			$('form').trigger('change');
		},

		/**
		 * Retrieve data of ther form
		 * 
		 * @return  string
		 */
		serializeForm: function (form) {
			var formData = form.serialize(),
				fileFields = form.find('input[type="file"]');

			fileFields.each(function () {
				formData = formData + '&' + this.name + '=' + this.value;
			});

			return formData;
		},

		/**
		 * Initialize tabs
		 * 
		 * @return  void
		 */
		initTabs: function () {
			$('.jsn-tabs').tabs();
		},

		/**
		 * Initialize events for tab controls
		 * 
		 * @return  void
		 */
		initTabsEvents: function () {

		},

		/**
		 * Handle click event for ajax links to load content
		 * 
		 * @return  void
		 */
		ajaxLinkClicked: function (e) {
			var element = $(this),
				target  = element.attr('ajax-target'),
				targetElement = $(target);

			e.preventDefault();
			element.trigger('linkBeforeRequest');

			$.get(element.attr('href') + '&tmpl=component&ajax=1', function (response) {
				(targetElement.size() > 0)
					? targetElement.html(response)
					: element.trigger('linkRequested', { content: response });
			});

			element.attr('data-initialized', 'yes');
		}
	}

	return JSNCore;
});