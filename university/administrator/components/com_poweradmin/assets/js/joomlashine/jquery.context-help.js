/**
 * @subpackage	com_poweradmin (JSN POERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/
(function ($) {
	
	/**
	 * This class use to manage all tip items.
	 * It can show, hide all tip items
	 */
	function JSNContextHelp (helps, options)
	{
		this.contextTips = [];
		this.contextHelps = helps;
		this.options = $.extend({}, options);
		
		var self = this;
		var interval = null;
		var currentIndex = 0;
		
		/**
		 * Initialize context help object
		 * @return void
		 */
		(function initialize () {
			if (!$.isArray(self.contextHelps))
				return;
				
			$(self.contextHelps).each(function () {
				self.contextTips.push(new JSNContextHelpItem(this));
			});
		})();
		
		this.show = function () {
			if (self.contextTips.length > currentIndex) {
				var element = self.contextTips[currentIndex++].getElement();
				element.stop().css({ display: 'block', opacity: 0 }).animate({ opacity: 1 });
				
				interval = setTimeout(self.show, 150);
			}
		};
		
		this.hide = function () {
			clearTimeout(interval);
			currentIndex = 0;
			
			$(self.contextTips).each(function () {
				this.getElement().stop().animate({ opacity: 0 }, function (){
					this.hide();
				});
			});
		};
	}
	
	/**
	 * Tip item class
	 */
	function JSNContextHelpItem (options)
	{
		var self = this;
		var wrapper = $('<div/>', { 'class': 'jsn-context-wrapper '+options.arrow });
		var container = (options.inside !== undefined && options.inside == true) ? $(options.element).parent() : $('body');
		if (options.inside !== undefined && options.inside == true)
			container.css('position', 'relative');

		var button			= $('<div/>',	{ 'class': 'jsn-tip-arrow', 'text': ' ' });
		var plusButton		= $('<div/>',	{ 'class': 'jsn-tip-plus' })
		var content			= $('<div/>',	{ 'class': 'jsn-tip-content gradient '+options.arrow });
		var contentText		= $('<p/>',		{ 'html': options.text }).hide();
		var positionCount	= $('#modules-list .jsn-element-container').size(); 
		var isHiding		= false;
			
		(function initialize () {
			wrapper
				.hide()
				.css('z-index', parseInt($('ul#menu').css('z-index')) - 2)
				.append(
					content
						.append(plusButton)
						.append(contentText)
				)
				.append(button)
			.appendTo(container);
		
			if (options.onCreate !== undefined && $.isFunction(options.onCreate)) {
				options.onCreate(self, options);
			}
			
			wrapper.hover(expand, collapse);

			$('#jsn-rawmode-layout').bind('UILayout.resize.complete', refresh);

			var scrollInterval = null;
			$('#modules-list').scroll(function () {
				if (scrollInterval != null)
					clearInterval(scrollInterval);
				
				if (isHiding == false)
					scrollInterval = setInterval(refresh, 500)
			});

			setInterval(function () {
				var count = $('#modules-list .jsn-element-container').size();
				if (count != positionCount) {
					refresh();
					positionCount = count;
				}
			}, 500);
		})();
		
		this.getElement = function () {
			return wrapper;
		}
		
		this.setLocation = function(left, top) {
			wrapper.css({
				left: left,
				top: top
			});
		}
		
		function expand () {
			var marginLeft = Math.round((options.width - content.outerWidth())/2);
			var marginTop  = Math.round(options.height - content.outerHeight());
			var offset     = wrapper.offset();

			if (offset.left - marginLeft <= 0)
				marginLeft = 20;
				
			var newOffset  = offset.left - marginLeft;
			if (newOffset  + options.width > $('#element-box').outerWidth()) {
				marginLeft = (options.width - content.outerWidth());
			}

			var styles = {
				width  : options.width,
				height : options.height
			};

			styles.marginLeft = -marginLeft;
			if (options.arrow == 'bottom')
				styles.marginTop = -marginTop;
			
			$('.jsn-context-wrapper').css({ zIndex: parseInt($('#menu').css('z-index')) - 2 });
			
			wrapper.css({ zIndex: parseInt($('#menu').css('z-index')) - 1 });

			plusButton.hide();
			content.stop(false, false).animate(styles, 200, function () {
				contentText.stop(false, false).fadeIn();
			});
		}
		
		function collapse () {
			contentText.hide();
			content.stop(false, false).animate({
				marginLeft: 0,
				marginTop: (options.arrow == 'bottom') ? 0 : 10,
				width: 32,
				height: 34
			}, 200, function () {
				plusButton.show();
			});
		}
		
		function refresh () {
			if (options.refresh !== undefined && $.isFunction(options.refresh))
				return options.refresh(self);
				
			if (options.inside !== undefined && options.inside == true) {
				wrapper.css({
					left: options.offset.left,
					top: options.offset.top,
					right: options.offset.right,
					bottom: options.offset.bottom
				});
				return;
			}

			var offset = $(options.element).offset();
			var wOffset = {
				left: offset.left,
				top: offset.top
			};
			
			if (options.offset.left !== undefined) {
				if (typeof(options.offset.left) == 'string' && options.offset.left.match(/^[0-9]+%/)) {
					var result = /^([0-9]+)+%/.exec(options.offset.left);
					wOffset.left = offset.left + Math.floor($(options.element).innerWidth()/100*parseInt(result[1]));
				}
				else {
					wOffset.left = wOffset.left + options.offset.left;
				}
			}
			
			if (options.offset.top !== undefined) {
				if (typeof(options.offset.top) == 'string' && options.offset.top.match(/^[0-9]+%/)) {
					var result = /^([0-9]+)+%/.exec(options.offset.top);
					wOffset.top = offset.top + Math.floor($(options.element).innerHeight()/100*parseInt(result[1]));
				}
				else {
					wOffset.top = wOffset.top + options.offset.top;
				}
			}
				
			if (options.offset.right !== undefined) {
				wOffset.left = offset.left + ($(options.element).innerWidth() + options.offset.right);
			}

			self.setLocation(wOffset.left, wOffset.top);
		}
	}
	
	window.JSNContextHelp = JSNContextHelp;
	
})(JoomlaShine.jQuery);
