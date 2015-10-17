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
var JSNWindow = new Class
({
	// Implements
    Implements: [Options],
	
	// Options
	options: {
		source: '',
		handle: 'element', // inline, element, ajax, iframe
		
		injectCSS: false, // Inject CSS file to document
		
		width : 400,
		height: 300,
		
		draggable: false,
		scrollable: false,
		
		title: false,
		modal: true,
		
		toolbarPosition: 'bottom',
		toolbarTitle: '',
		buttons: {},
		
		closeButton: true
	},
	
	/**
	 * Initialize for class
	 * @param options
	 * @return void
	 */
	initialize: function (options)
	{
		this.setOptions(options);
		this.createUI();
		this.addEvents();
	},
	
	/**
	 * Create element for build window UI
	 * @return void
	 */
	createUI: function ()
	{
		var self 			= this;
		
		this.body			= $$('body')[0];
		this.overlay		= new Element('div', { 'class': 'jsn-ui-window-overlay' });
		this.window 		= new Element('div', { 'class': 'jsn-ui-window' });
		this.windowWrapper	= new Element('div', { 'class': 'window-wrapper'});
		this.windowBody 	= new Element('div', { 'class': 'window-body' ,'style': 'height:'+ (this.options.height - 95) + 'px' });
		this.windowHeader 	= this.createHeaderUI();
		this.windowToolbar 	= this.createToolbarUI();
		this.iframe			= undefined;
		this.overlay.adopt(
			new Element('div', { 'class': 'indicator' })
		);
		
		// Add header
		if (this.windowHeader != null) {
			this.windowWrapper.adopt(this.windowHeader);
		}
		
		// Add body
		this.windowWrapper.adopt(this.windowBody);
		
		// Add toolbar
		if (this.windowToolbar != null) {
			if (this.options.toolbarPosition == 'bottom')
				this.windowToolbar.inject(this.windowWrapper);
			else
				this.windowToolbar.inject(this.windowBody, 'before');
		}
		
		this.windowWrapper.inject(this.window);
		
		// Set window size
		this.window.setStyles({
			width: this.options.width,
			height: this.options.height
		});
		
		// Initialize dragger if needed
		if (this.options.draggable === true) {
			this.dragger = new Drag(this.window, {
				handle		: this.windowHeader,
				onDrag		: function () { self.setState('dragging'); },
				onComplete	: function () { self.setState('active'); }
			});
			
			this.windowHeader.setStyle('cursor', 'move');
		}
	},
	
	/**
	 * Create elements structure for toolbar
	 * @return object
	 */
	createToolbarUI: function ()
	{
		if (Object.keys(this.options.buttons).length == 0) {
			return null;
		}
		
		var self			= this;
		var toolbar 		= new Element('div', { 'class': 'window-toolbar' });
		var toolbarTitle 	= "";		
		if(this.options.toolbarTitle){
			toolbarTitle 	= new Element('h3', { 'class': 'toolbar-title', 'text': this.options.toolbarTitle });
		}
		
		var toolbarButtons 	= new Element('div', { 'class': 'toolbar-buttons' });
		
		Object.map(this.options.buttons, function (callback, caption) {
			var button = new Element('button', { text: caption });
			
			
			if (typeof(callback) == 'function') {
				button.addEvent('click', function (event) {
					callback(self, event);
				});
			}
			
			button.inject(toolbarButtons);
		}, this);	
		
		toolbar.adopt(toolbarTitle);
		toolbar.adopt(toolbarButtons);
		toolbar.adopt(new Element('div', { 'style': 'clear:both'}));
		return toolbar;
	},
	
	/**
	 * Create elements structure for header
	 * @return object
	 */
	createHeaderUI: function ()
	{
		if (this.options.closeButton === true || typeof(this.options.title) == 'string') 
		{
			var self = this;
			var windowHeader = new Element('div', { 'class': 'window-header' });
			
			// Add title to window header
			if (typeof(this.options.title) == 'string') {
				windowHeader.adopt(
					new Element('span', { 'class': 'header-title',text: this.options.title })
				);
			}
			
			// Add close button to window header
			if (this.options.closeButton === true) {
				windowHeader.adopt(
					new Element('a', { 'class': 'btn-close', 'text': ' ' })
						.addEvent('click', function (e) {
							e.stop();
							self.close();
						})
				);
			}
			windowHeader.adopt(
					new Element('div', { 'style': 'clear:both'})
				);
			return windowHeader;
		}
		
		return null;
	},
	
	/**
	 * Register events for an elements on window
	 * @return void
	 */
	addEvents: function ()
	{
		var self = this;
		this.overlay.addEvent('click', function () {
			self.close();
		});
		
		$(window).addEvent('resize', function () {
			self.updatePosition();
		});
	},
	
	/**
	 * Set state for the window
	 * @param state Can be loading, active, inactive, moving, resizing
	 */
	setState: function (state)
	{
		this.window
			.removeClass('state-' + this.currentState)
			.addClass('state-' + state);
			
		this.overlay
			.removeClass('state-' + this.currentState)
			.addClass('state-' + state);
		
		this.currentState = state;
		
		if (typeof(this.options.stateChanged) == 'function') {
			this.options.stateChanged(this);
		}
		
		return this;
	},
	
	/**
	 * Retrieve contents from source to display on window
	 * @param callback
	 * @return void
	 */
	loadContents: function (callback)
	{
		var self = this;
		this.setState('loading');
		
		if (this.options.handle == 'element' && typeof(this.options.source) != 'Element') {
			this.options.handle = '';
		}
		
		switch (this.options.handle) {
			case 'ajax':
				new Request.HTML({
					url: this.options.source,
					onSuccess: function (responseTree, responseElements, responseHtml, responseJs) {
						self.windowBody.set('html', responseHtml);
						callback();
					}
				})
				.get();
			break;
			
			case 'iframe':
				var windowBodySize = this.windowBody.getSize();
				var iframe = new IFrame({
					src: this.options.source,
					scrolling: (this.options.scrollable === false) ? 'no' : 'auto',
					frameborder: 0,
					styles: {
						width: windowBodySize.x,
						height: windowBodySize.y,
						border: 0
					},
					events: {
						load: function () {
							if (typeof(self.options.contentLoaded) == 'function') {
								if (self.options.contentLoaded(self) === true) {
									return;
								}
							}
							
							if (self.options.injectCSS !== false) {
								new Request({
									method: 'GET',
									url: self.options.injectCSS,
									onSuccess: function (response) {
										var styleTag = new Element('style', { 'type': 'text/css' }).inject(self.getDocument().getElement('head'));
										(styleTag.styleSheet) ? styleTag.styleSheet.cssText = response : styleTag.appendChild(self.getDocument().createTextNode(response));

										callback();
									},
									onFailure: function () {
										callback();
									}
								})
								.send();
							}
							else {
								callback();
							}
						}
					}
				});
				
				iframe.inject(this.windowBody);
				this.iframe	= iframe;
			break;
			
			case 'element':
				this.windowBody.adopt(this.options.source);
				callback();
			break;
			
			default:
				this.windowBody.set('html', this.options.source);
				callback();
			break;
		}
	},

	/**
	 * Get popup iframe
	 * 
	 */
	getIframe: function ()
	{
		return this.iframe;
	},
	
	/**
	 * Submit form inside window content
	 * @return void
	 */
	submitForm: function (selector)
	{
		var form = this.getDocument().getElement(selector);
		if (form.get('tag') != 'form') {
			return this;
		}
		
		form.submit();
		return this;
	},
	
	/**
	 * Retrieve DOM element inside window body
	 * @return Element
	 */
	getDocument: function ()
	{
		var container = this.windowBody;
		if (this.options.handle == 'iframe') {
			var iframe = this.windowBody.getElement('iframe');
			container = iframe.contentDocument || iframe.contentWindow.document;
		}
		
		return $(container);
	},
	
	/**
	 * Move window to center of the screen
	 * @return void
	 */
	updatePosition: function ()
	{
		var documentSize = $(document).getSize();
		var windowSize   = this.window.getSize();
		var position = {
			left: (documentSize.x > windowSize.x) ? Math.round((documentSize.x - windowSize.x)/2) : 0,
			top: (documentSize.y > windowSize.y) ? Math.round((documentSize.y - windowSize.y)/2) : 0
		};
		
		this.window.setStyles(position);
	},
	
	/**
	 * Display window into screen
	 * @return void
	 */
	open: function ()
	{
		var self = this;
		
		if (this.options.modal === true)
			this.overlay.inject(this.body);
			
		this.window.inject(this.body);
		this.windowBody.empty();
		this.updatePosition();
		
		this.loadContents(function () {
			self.setState('active');
		});
	},
	
	/**
	 * Hide window
	 * @return void
	 */
	close: function ()
	{
		this.overlay.dispose();
		this.window.dispose();
	}
});

/**
 * Confirm dialog
 * @param options
 * @return void
 */
JSNWindow.confirm = function (options) {
	var buttons = {};
	
	buttons[JoomlaShine.language.JYES] = function (ui) {
		if (typeof(options.callback) == 'function')
			options.callback(true);
			
		ui.close();
	};
	
	buttons[JoomlaShine.language.JNO] = function (ui) {
		if (typeof(options.callback) == 'function')
			options.callback(false);
			
		ui.close();
	};
	
	var win = new JSNWindow({
		title: options.title,
		toolbarPosition: 'bottom',
		source: options.message,
		width: 500,
		height: 140,
		buttons: buttons
	});
	
	win.window.addClass('window-confirm');
	win.open();

	return win;
}

/**
 * Message box
 */
JSNWindow.messageBox = function (options) {
	var buttons = {};

	buttons['OK'] = function (ui) {
		if (typeof(options.callback) == 'function')
			options.callback(true);
			
		ui.close();
	};

	var win = new JSNWindow({
		title: options.title,
		toolbarPosition: 'bottom',
		source: options.message,
		width: 500,
		height: 200,
		buttons: buttons
	});
	
	win.window.addClass('window-message');
	win.open();

	return win;
}
