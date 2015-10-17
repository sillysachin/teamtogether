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
	// Declare JSN Update contructor
	JSNUpdate = function(params) {
		// Object parameters
		this.params = $.extend({}, params);
		this.lang = this.params.language || {};

		$(document).ready($.proxy(function() {
			// Get update button object
			this.button = document.getElementById(this.params.button);
		
			// Get elements need to to be updated
			this.elements = $('#jsn-update-products > li');
			this.current = 0;

			// Set event handler to update product
			$(this.button).click($.proxy(function(event) {
				event.preventDefault();
				this.install();
			}, this));
		}, this));
	};

	// Declare JSN Update methods
	JSNUpdate.prototype = {
		install: function() {
			// Mark installation step
			this.step = 1;
			
			// Hide form action
			$('#jsn-update-action').hide();
			
			// Execute current installation step
			this.execute();
		},
	
		execute: function() {
			// Call appropriate method
			this['step' + this.step]();
		},
	
		step1: function() {
			// Is login required?
			if (document.JSNUpdateLogin) {
				// Show login form
				$('#jsn-update-login').show();
				
				// Setup login form
				$(document.JSNUpdateLogin).delegate('input[type="text"], input[type="password"]', 'keyup', $.proxy(function() {
					var canLogin = true;
					$('input[type="text"], input[type="password"]', document.JSNUpdateLogin).each(function() {
						this.value != '' || (canLogin = false);
					});
					canLogin ? $('button', document.JSNUpdateLogin).removeProp('disabled') : $('button', document.JSNUpdateLogin).attr('disabled', 'disabled');
				}, this));
		
				$('button', document.JSNUpdateLogin).click($.proxy(function(event) {
					event.preventDefault();
		
					// Execute next step
					this.step++;
					this.execute();
				}, this));
			} else {
				// Execute next step
				this.step++;
				this.execute();
			}
		},
		
		step2: function() {
			// Update indicators
			$('#jsn-update-cancel').hide();
			$('#jsn-update-login').hide();
			$('#jsn-update-indicator').show();
			$('.jsn-update-downloading', this.elements[this.current]).show();
			$('.jsn-update-downloading-unsuccessful-message', this.elements[this.current]).hide();

			// Request server-side to download update package
			$.ajax({
				url: $(this.button).attr('data-source') + '&' + this.elements.eq(this.current).attr('ref'),
				type: document.JSNUpdateLogin ? document.JSNUpdateLogin.method : 'GET',
				data: (document.JSNUpdateLogin ? $(document.JSNUpdateLogin).serialize() + '&' : '') + 'tmpl=component&ajax=1',
				context: this
			}).done(function(data) {
				this.clearTimer('.jsn-update-downloading-indicator');

				if (data.substr(0, 4) == 'DONE') {
					// Update indicators
					$('.jsn-update-downloading-indicator', this.elements[this.current]).removeClass('jsn-icon-loading').addClass('jsn-icon-ok');

					// Update download link to install link
					$(this.button).attr('data-source', $(this.button).attr('data-source').replace('.download', '.install'));
					this.button.data = 'path=' + data.replace(/^DONE:(\s+)?/, '');

					// Execute next step
					this.step++;
					this.execute();
				} else {
					// Update indicators
					$('.jsn-update-downloading-indicator', this.elements[this.current]).removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
					$('.jsn-update-downloading-unsuccessful-message', this.elements[this.current]).html(data.replace(/^FAIL:(\s+)?/, '')).show();

					// Update next element
					this.current++;
					if (typeof this.elements[this.current] != 'undefined') {
						this.execute();
					}
				}
			});

			this.setTimer('.jsn-update-downloading-indicator');
		},
	
		step3: function() {
			// Update indicators
			$('.jsn-update-installing', this.elements[this.current]).show();
			$('.jsn-update-installing-unsuccessful-message', this.elements[this.current]).hide();
			$('.jsn-update-installing-warnings', this.elements[this.current]).hide();
			
			// Request server-side to install dowmloaded package
			this.modal = new modal({
	            url: $(this.button).attr('data-source') + ($(this.button).attr('data-source').indexOf('?') ? '&' : '?') + this.button.data + '&tmpl=component&ajax=1&tool_redirect=' + this.params.redirect,
	            loaded: $.proxy(function() {
	            	var data = this.modal.iframe[0].contentDocument.doctype == null ? this.modal.iframe[0].contentDocument.body.innerHTML : 'DONE';
	            	this.modal.container.parent().css('visibility', 'hidden');
	    			this.clearTimer('.jsn-update-installing-indicator');
	    			
	    			if (data.substr(0, 4) == 'DONE') {
	    				// Update indicators
	    				$('.jsn-update-installing-indicator', this.elements[this.current]).removeClass('jsn-icon-loading').addClass('jsn-icon-ok');

	    				// State that all elements is updated successfully
	    				if (typeof this.elements[this.current + 1] == 'undefined') {
	    					$('#jsn-update-successfully').show();
	    				}
	    			} else if (data != '') {
	    				// Update indicators
	    				$('.jsn-update-installing-indicator', this.elements[this.current]).removeClass('jsn-icon-loading').addClass('jsn-icon-remove');
	    				
	    				// Displaying any error/warning message
	    				if (data.substr(0, 4) == 'FAIL') {
	    					$('.jsn-update-installing-unsuccessful-message', this.elements[this.current]).html(data.replace(/^FAIL:(\s+)?/, '')).show();
	    				} else {
	    					$('.jsn-update-installing-warnings', this.elements[this.current]).append(data).show();
	    				}
	    			}

	    			// Update next element
	    			this.current++;
	    			if (typeof this.elements[this.current] != 'undefined') {
	    				// Reset install link to download link
	    				$(this.button).attr('data-source', $(this.button).attr('data-source').replace('.install', '.download'));
	    				this.button.data = '';
	    				
	    				// Reset step then process
	    				this.step--;
	    				this.execute();
	    			}
	            }, this)
	        });
			this.modal.iframe.attr('src', this.modal.options.url);
	
			this.setTimer('.jsn-update-installing-indicator');
		},
	
		setTimer: function(element) {
			// Schedule still loading notice
			this.timer = setInterval($.proxy(function() {
				var el = $(element, this.elements[this.current]);
				if (el.hasClass('jsn-icon-loading')) {
					var msg = el.next('.jsn-processing-message').html();
					if (msg == this.lang['JSN_EXTFW_GENERAL_STILL_WORKING']) {
						el.next('.jsn-processing-message').html(this.lang['JSN_EXTFW_GENERAL_PLEASE_WAIT']);
					} else {
						el.next('.jsn-processing-message').html(this.lang['JSN_EXTFW_GENERAL_STILL_WORKING']);
					}
				}
			}, this), 3000);
		},
	
		clearTimer: function(element) {
			clearInterval(this.timer);
			$(element, this.elements[this.current]).next('.jsn-processing-message').hide();
		}
	};

	return JSNUpdate;
});
