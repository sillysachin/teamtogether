define([
	'jquery'
],

function ($) {
	var JSNDropMenu = function(params)
	{
		this.params = $.extend({
			menuSelector: '.jsn-drop-menu'
		}, params);

		// Get dropdown menu element
		if ((this.$menu = $(this.params.menuSelector)).length) {
			// Initialize
			this.initialize();
		}
	};

	JSNDropMenu.prototype = {
		initialize: function() {
			var	self = this,
				$parent = self.$menu.find('li.dropdown');

			$parent.click(function(event) {
				var target = event.target;

				// Check if clicked element is a menu item that has sub-menu
				$parent.each(function(i, e) {
					if (
						(target.nodeName == 'LI' && target == e)
						||
						(target.nodeName == 'BUTTON' && target.parentNode == e)
						||
						(target.nodeName == 'A' && target.parentNode == e)
						||
						(target.nodeName == 'SPAN' && target.parentNode.parentNode == e)
						||
						(target.nodeName == 'B' && target.parentNode.parentNode == e)
					) {
						var $e = $(e);

						// Fine-tune event
						event.preventDefault();
						event.stopPropagation();
		
						// Check sub-menu status
						if ($e.hasClass('open')) {
							$e.removeClass('open');
						} else {
							// Hide all open sub-menu of same level
							self.$menu.find('.open').each(function(i2, e2) {
								$(e2).has(e).length || $(e2).removeClass('open');
							});
		
							// Show sub-menu
							$e.addClass('open');

							/* Fine-tune sub-menu alignment
							if ( ! $e.parent().hasClass('nav')) {
								$e.find('.dropdown-menu').css('margin-left', $e.outerWidth());
							}*/
						}
					}
				});
			});

			$(document).click(function(event) {
				var target = event.target;

				// Check if clicked element is a menu item that has sub-menu
				$parent.each(function(i, e) {
					if ((target.nodeName == 'LI' && target == e) || (target.nodeName == 'A' && target.parentNode == e) || (target.nodeName == 'I' && target.parentNode.parentNode == e)) {
						// Do nothing
					} else {
						// Hide all open sub-menu
						$parent.removeClass('open');
					}
				});
			});	
		}
	};

	return JSNDropMenu;
});
