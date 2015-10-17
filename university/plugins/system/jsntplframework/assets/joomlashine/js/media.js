(function ($) {
	"use strict";

	/**
	 * Class to render UI for folder tree
	 * control
	 * 
	 * @param   object  params  Options for class
	 */
	function JSNFolderTree (params)
	{
		var
		self = this;

		this.defaultParams = {
			basePath: '/',
			renderTo: document.body
		};

		this.params = $.extend(this.defaultParams, params);
		this.panel = $('<div />', { 'class': 'jsn-folder-tree' });

		/**
		 * Public method to retrieve path of activated node
		 * in the folder tree
		 * 
		 * @return  string
		 */
		this.getActivePath = function () {
			var activeNode = self.tree.getActiveNode();

			if (activeNode) {
				return activeNode.data.data.path;
			}
		};

		/**
		 * Initialize folder tree
		 * @return void
		 */
		function init () {
			// Append element to DOM tree
			self.panel.appendTo(
				$(self.params.renderTo)
			);

			// Initlize tree
			self.panel.dynatree({
				clickFolderMode: 1,
				children: [{
					title		: self.params.basePath,
					key 		: 'root',
					data		: { path: self.params.basePath },
					isFolder	: true,
					isLazy		: true
				}],
				debugLevel: 0,
				onLazyRead: loadNode,
				onQueryExpand: queryExpandNode,
				onActivate: activateNode
			});

			// Find instance of tree
			self.tree = self.panel.dynatree('getTree');

			// Active root node
			var
			root = self.tree.getNodeByKey('root');
			root.removeChildren();
			root.resetLazy();
			root.expand(true);
			root.activate();
		};

		/**
		 * Load children folder for the node
		 * 
		 * @param   object  node  Parent node
		 * @return  void
		 */
		function loadNode (node)
		{
			var
			path = node.data.data.path,
			template = self.params.template;

			node.appendAjax({
				url: 'index.php?widget=media&action=folders&path=' + path + '&template=' + template + '&rand=' + Math.random()
			});
		};

		/**
		 * Reset node before expand it
		 * 
		 * @param   boolean  flag  True if node is expanded
		 * @param   object   node  Current node
		 * @return  void
		 */
		function queryExpandNode (flag, node)
		{
			if (flag == true && node.data.key != 'root') {
				node.removeChildren();
				node.resetLazy();
			}
		};

		/**
		 * Expand a node when activated if possible
		 * 
		 * @param   object  node  Node to expand
		 * @return  void
		 */
		function activateNode (node)
		{
			if (node.isExpanded() == false) {
				node.expand(true);
			}

			$(self).trigger('folder-selected', node.data.data);
		};

		// Initialize object
		init();
	};

	/**
	 * This class use to render a data grid to show
	 * all image files from a folder
	 * 
	 * @param   object  params  Object parameters
	 */
	function JSNImageList (params)
	{
		var
		self = this;

		this.defaultParams = {
			renderTo: document.body
		};

		this.params = $.extend(this.defaultParams, params);
		this.panel  = $('<div />', { 'class': 'jsn-image-list thumbnails' });

		/**
		 * Alias of _loadPath
		 * 
		 * @param   string  path    Path to the folder
		 * @param   int     offset  Start offset of file list
		 * 
		 * @return  void
		 */
		this.loadPath = function (path, startOffset)
		{
			loadPath(path, startOffset);
		};

		/**
		 * Retrieve path to a selected file
		 * @return  string
		 */
		this.getSelectedFile = function ()
		{
			if (self.activeItem !== undefined) {
				return self.activeItem.data('jsn-file-data');
			}

			return null;
		};

		/**
		 * Method to initialize control
		 * @return  void
		 */
		function init ()
		{
			self.panel.appendTo($(self.params.renderTo));
			self.panel.delegate('a.jsn-image-file', 'click', $.proxy(itemClicked));
			self.panel.delegate('a.jsn-image-file', 'dblclick', $.proxy(itemDoubleClicked));
		};

		/**
		 * This method use to load list of files from the
		 * server and display it into grid
		 *
		 * @param   string  path    Path to the folder
		 * @param   int     offset  Start offset of file list
		 * 
		 * @return  void
		 */
		function loadPath (path, selectedId)
		{
			$.getJSON('index.php?widget=media&action=files&path=' + path + '&template=' + self.params.template + '&rand=' + Math.random(), function (response) {
				self.loadedFiles = response.data;
				
				// Clear all existing items
				self.panel.empty();
				self.activeItem = undefined;

				// Generate items from loaded data
				self.loadedFiles.each(function (file) {
					var
					isActive	= selectedId !== undefined && selectedId == file.data['id'],
					className	= isActive ? 'jsn-image-file thumbnail active' : 'jsn-image-file thumbnail',
					element		= $('<a />',    { 'class': className, 'href': 'javascript:void(0)', 'id': file.data['id'] }),
					thumb		= $('<span />', { 'class': 'jsn-image-thumb' }),
					imgUrl		= file.data.thumbnail != false ? file.data.thumbnail : 'index.php?widget=media&action=thumbnail&file=' + file.data.url + '&template=' + self.params.template;

					if (isActive) {
						self.activeItem = element;
					}

					thumb.append($('<img />', { 'src': imgUrl, 'alt': file.title }));

					element
						.data('jsn-file-data', file)
						.append(thumb)
						.appendTo(self.panel);
				});
			});
		};

		/**
		 * Method to handling click event for the grid item
		 * @return void
		 */
		function itemClicked (e)
		{
			var
			el = e.target.nodeName == 'A' ? $(e.target) : $(e.target).closest('a');
			e.preventDefault();

			if (self.activeItem !== undefined) {
				self.activeItem.removeClass('active');
			}

			el.addClass('active');
			self.activeItem = el;
		};

		function itemDoubleClicked (e)
		{
			var
			el = e.target.nodeName == 'A' ? $(e.target) : $(e.target).closest('a'),
			data = el.data('jsn-file-data');

			$(self).trigger('file-selected', data);
		};

		// Initialize object
		init();
	};

	/**
	 * This class will generate a modal window
	 * and contains folder tree control that allow
	 * user to select image from selected folder
	 * 
	 * @param  Element  el      DOM Element
	 * @param  Object   params  Object parameters
	 */
	function JSNImageSelector (el, params)
	{
		var
		self = this;

		this.defaultParams = {
			basePath: '/',
			title: 'Select Image',
			width: 800,
			height: 500,
			lang: {
				SELECT: 'Select',
				CANCEL: 'Cancel'
			}
		};

		this.el         = $(el);
		this.elTarget   = $(this.el.attr('data-target'));
		this.params     = $.extend(this.defaultParams, params);

		// Create DOM elements
		this.pnlDialog		= $('<div />', { 'class': 'jsn-image-selector' });
		this.pnlFolders		= $('<div />', { 'class': 'jsn-image-folders ui-layout-west' }); 
		this.pnlFiles		= $('<div />', { 'class': 'jsn-image-grid ui-layout-center' });
		this.pnlFileItems	= $('<div />', { 'class': 'jsn-image-files jsn-bootstrap' });
		this.uploadForm		= $('<form />', {
			'class': 'jsn-image-upload',
			'method': 'POST',
			'enctype': 'multipart/form-data',
			'target': 'jsn-media-upload-iframe'
		});

		/**
		 * Initialize folder selector control
		 * @return  void
		 */
		function init ()
		{
			var 
			btnSelect = { text: self.params.lang.SELECT, click: selectFile },
			btnCancel = { text: self.params.lang.CANCEL, click: closeDialog };

			// Update DOM tree
			self.pnlDialog
				.append(self.pnlFolders)
				.append(self.pnlFiles);

			self.pnlFiles
				.append(self.pnlFileItems)
				.append(self.uploadForm)
				.append(self.uploadStatus);

			createUploadField(self.uploadForm);

			self.tree = new JSNFolderTree({
				basePath: self.params.basePath,
				renderTo: self.pnlFolders,
				template: self.params.template
			});

			self.imageList = new JSNImageList({
				renderTo: self.pnlFileItems,
				startPath: '/',
				template: self.params.template
			});

			// Create jQuery wrapped tree object
			self.elTree = $(self.tree);
			self.elImageList = $(self.imageList);

			// Initialize dialog object
			self.pnlDialog.dialog({
				width: self.params.width,
				height: self.params.height,
				title: self.params.title,
				autoOpen: false,
				modal: true,
				buttons: [btnSelect, btnCancel],
				open: dialogOpened
			});

			// Register events
			self.el.on('click', $.proxy(openDialog));
			self.elTree.bind('folder-selected', $.proxy(folderActivated));
			self.elImageList.bind('file-selected', $.proxy(fileSelected));
		};

		/**
		 * Method to display dialog for select folder
		 * 
		 * @param   Event  e  Event object
		 * @return  void
		 */
		function openDialog (e)
		{
			e.preventDefault();

			if ($(e.target).hasClass('disabled'))
				return;

			self.pnlDialog.dialog('open');
		};

		/**
		 * Handling event when dialog is opened
		 * @return void
		 */
		function dialogOpened ()
		{
			self.pnlDialog.layout({
				closable			: true,
				resizable			: true,
				slidable			: true,

				south__resizable	: false,
				south__size 		: 50,
				south__spacing_open	: 0
			});

			self.imageList.loadPath(self.tree.getActivePath());
		};

		/**
		 * Handling folder selected event to update file list
		 * @return  void
		 */
		function folderActivated (e, data)
		{
			self.imageList.loadPath(data.path);
		};

		/**
		 * Callback function to select a folder
		 * @return  void
		 */
		function selectFile (event, ui)
		{
			if (self.elTarget.size() > 0) {
				self.elTarget.val(
					self.imageList.getSelectedFile().data.url
				);
			}

			self.pnlDialog.dialog('close');
		};

		/**
		 * Close dialog
		 * @return  void
		 */
		function closeDialog (event, ui)
		{
			self.pnlDialog.dialog('close');
		};

		/**
		 * Handling file selected event to update
		 * value to target element
		 * 
		 * @param   event   e     Event object
		 * @param   object  data  Custom data
		 * 
		 * @return  void
		 */
		function fileSelected (e, data)
		{
			self.elTarget.val(data.data.url);
			self.pnlDialog.dialog('close');
		};

		/**
		 * Create field that will be used to select file
		 * for upload to server
		 * 
		 * @return  void
		 */
		function createUploadField (form)
		{
			var
			control			= $('<div />', { 'class': 'jsn-file-field input-append' }),
			controlFile		= $('<input />', { 'type': 'file', 'name': 'jsn-file-upload', 'class': 'jsn-file-upload' }),
			controlInput	= $('<input />', { 'type': 'text', 'class': 'jsn-text', 'readonly': 'readonly' }),
			controlBrowse	= $('<button />', { 'type': 'button', 'class': 'btn btn-browse', 'text': '...' }),
			controlInputWrapper = $('<span />', { 'class': 'jsn-media-input-wrapper' }),
			controlUpload	= $('<button />', { 'type': 'button', 'class': 'btn btn-upload', 'text': 'Upload' });

			controlInputWrapper
				.append(controlFile)
				.append(controlBrowse);

			control
				.append(controlInput)
				.append(controlInputWrapper)
				.append(controlUpload);

			controlUpload.on('click', processUpload);
			controlFile.on('change', function () {
				controlInput.val(controlFile.val());
			});
			
			form.empty().append(control);
		}

		/**
		 * Submit upload form
		 * 
		 * @return  void
		 */
		function processUpload (e)
		{
			var
			el = $(this),
			form = el.closest('form'),
			file = form.find('input[type="file"]'),
			uploadIframe = $('#jsn-media-upload-iframe');

			if (uploadIframe.size() == 0) {
				uploadIframe = $('<iframe />', {
					'name': 'jsn-media-upload-iframe',
					'id': 'jsn-media-upload-iframe',
					'src': 'about:blank'
				})
				.appendTo($('body'));
			}

			uploadIframe.unbind('load').bind('load', self.uploadComplete);

			if (file.val() != '') {
				form
					.attr('action', 'index.php?widget=media&action=upload&template=' + self.params.template + '&path=' + self.tree.getActivePath())
					.submit()
					.append($('<span />', { 'class': 'jsn-loading' }));
				el
					.attr('disabled', 'disabled');
			}
		};

		/**
		 * Handle upload completed event to refresh UI
		 * 
		 * @return  void
		 */
		this.uploadComplete = function() {
			try {
				var
				response = $.parseJSON($(this).contents().text());

				if (response != null && $.isPlainObject(response)) {
					self.uploadForm.empty();
					createUploadField(self.uploadForm);

					if (response.type == 'success') {
						self.imageList.loadPath(self.tree.getActivePath(), response.data['id']);
					} else {
						alert(response.data);
					}
				}
			} catch (e) {
				// Do nothing
			}
		};

		// Initialize object
		init();
	};

	/**
	 * Register image selector plugin to jQuery
	 * 
	 * @param   object  options  Plugin options
	 * @return  void
	 */
	$.fn.imageSelector = function (options) {
		return this.each(function () {
			var
			el = $(this);
			
			if (el.data('jsn-image-selector') === undefined) {
				el.data('jsn-image-selector', new JSNImageSelector(el, options));
			}
		});
	};
})(jQuery);