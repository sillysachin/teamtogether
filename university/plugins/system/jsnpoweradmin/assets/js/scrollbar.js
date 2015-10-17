/*------------------------------------------------------------------------
 # JSN PowerAdmin
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # @version $Id
 -------------------------------------------------------------------------*/
var JSNScrollbar = new Class
({
	Implements: [Options],
	
	// Default options
	options: {
		scrollSize: 45
	},
	
	/**
	 * Class constructor
	 */
	initialize: function (element, options)
	{
		this.el = element;
		this.setOptions(options);
		
		this.createUI();
		this.addEvents();
	},
	
	/**
	 * Create user interface for scrollbar
	 * @return void
	 */
	createUI: function ()
	{
		var self = this;
		
		this.scrollbar 	= new Element('div', { 'class': 'scrollbar' });
		this.handle 	= new Element('div', { 'class': 'handle' });
		
		this.viewport 	= new Element('div', { 'class': 'viewport' });
		this.overview 	= new Element('div', { 'class': 'overview' });
		
		this.scrollbar.grab(this.handle);
		this.viewport.grab(this.overview);
		
		this.el.getChildren().each(function (element) {
			self.overview.grab(element);
		});
		
		this.el.adopt(this.scrollbar, this.viewport);
		this.el.addClass('scrollable');
		
		this.refresh();
	},
	
	/**
	 * Register event to handle scrollbar
	 * @return void
	 */
	addEvents: function ()
	{
		var self = this;
		
		// Add mouse wheel event to scroll element
		this.el.addEvent('mousewheel', function (event) {
			isScrollDown = event.wheel > 0 ? false : true;
			self.setScroll(self.options.scrollSize, isScrollDown);
			event.stop();
		});
		
		/**
		 * Create drag handler for scrollbar
		 */
		if(!Browser.ie){
			new Drag.Move(this.handle, {
				container: this.scrollbar,
				onDrag: function (handle) {
					self.drag(handle);
				}
			});	
		}

	},
	
	/**
	 * Scroll up/down that position is based on step
	 * @param step
	 * @param isScrollDown
	 * @return void
	 */
	setScroll: function (step, isScrollDown)
	{
		var currentPosition = Math.abs(parseInt(this.overview.getStyle('top'))),
			newPosition = (isScrollDown) ? currentPosition + step : currentPosition - step,
			handleOffset = newPosition * this.viewRatio,
			overviewSize = this.overview.getSize(),
			viewportSize = this.viewport.getSize(),
			scrollbarSize = this.scrollbar.getSize();
			
		if (newPosition + viewportSize.y > overviewSize.y) {
			newPosition = overviewSize.y - viewportSize.y;
			handleOffset = scrollbarSize.y - this.handle.getSize().y;
		}
		
		if (newPosition < 0) {
			newPosition = 0;
			handleOffset = 0;
		}
		
		this.handle.setStyles({
			top: handleOffset
		});
		
		this.overview.setStyles({
			top: -newPosition
		});
	},
	
	/**
	 * Set scroll offset to new position
	 * @param position
	 * @return void
	 */
	scrollToPosition: function (position)
	{
		var overviewSize = this.overview.getSize(),
			viewportSize = this.viewport.getSize();
			
		if (typeof(position) == 'string') {
			position = (position == 'top') ? 0 : overviewSize.y - viewportSize.y;
		}

		if (position >= 0 && position <= overviewSize.y - viewportSize.y) {
			this.overview.setStyles({
				top: -position
			});
			this.handle.setStyles({
				top: position * this.viewRatio
			});
		}
		else {
			this.overview.setStyles({
				top: 0
			});
			this.handle.setStyles({
				top: 0
			});
		}
	},
	
	/**
	 * Handling drag event for scrollbar
	 * @param handle
	 * @return void
	 */
	drag: function (handle)
	{
		var handleOffset = parseInt(this.handle.getStyle('top')),
			offsetTop = handleOffset / this.viewRatio,
			viewportSize = this.viewport.getSize(),
			overviewSize = this.overview.getSize();
		
		if (offsetTop > overviewSize.y - viewportSize.y) {
			offsetTop = overviewSize.y - viewportSize.y;
		}
		
		this.overview.setStyles({
			top: -offsetTop
		});
	},
	
	getCurrentOffset: function ()
	{
		return Math.abs(parseInt(this.overview.getStyle('top')));
	},
	
	/**
	 * Update size for scrollbar, viewport
	 * @return void
	 */
	refresh: function ()
	{
		var size 			= this.el.getSize(),
			overviewSize	= this.overview.getSize(),
			paddingTop 		= parseInt(this.el.getStyle('padding-top')),
			paddingBottom 	= parseInt(this.el.getStyle('padding-bottom')),
			borderTop 		= parseInt(this.el.getStyle('border-top-width')),
			borderBottom 	= parseInt(this.el.getStyle('border-bottom-width')),
			height 			= size.y - paddingTop - paddingBottom - borderTop - borderBottom,
			ratio			= overviewSize.y > 0 ? height / overviewSize.y : 0,
			handleHeight	= ratio * height,
			viewRatio		= height > 0 ? handleHeight / height : 0;
			
		this.ratio = ratio;
		this.viewRatio = viewRatio;
		
		this.viewport.setStyles({ height: height });
		this.scrollbar.setStyles({ height: height });

		if (!isNaN(handleHeight)) {
			this.handle.setStyles({ height: handleHeight });
		}
		
		if (this.ratio >= 1) {
			this.el.addClass('scrollbar-disabled');
		}
		else {
			this.el.removeClass('scrollbar-disabled');
		}
	}
});










