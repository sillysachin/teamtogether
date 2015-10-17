(function ($){	
	var JSNItemListInstances = new Array();
	  $.JSNItemList = function(obj, options){
		  new jsnItemList(obj, options);
	  };
	/**
	 * Class to create an editable list from 
	 * a textarea
	 * 
	 * @param jquery object obj: transformed text area
	 * @returns void
	 * 
	 */
	function jsnItemList(obj, options){
		this.obj = obj;
		this.inputName = options.inputName ? options.inputName : 'item-list';
		var _prefix	= "jsn-tsn-";
		this.listId = _prefix + this.obj.attr('id');	
		this.options = options;
		this.initUI();
			
	}
	
	/**
	 * Create skeleton for class
	 * @returns void
	 * 
	 */
	jsnItemList.prototype = {
		/**
		 * Init UI for the firstime load
		 */
		initUI: function(){		
			var self = this;
			if($('#'+this.listId).length == 0){
				this.createUi();
				this.tranformedList.appendTo(this.obj.parent()).after(this.obj);
			}else{
				var itemArr = this.obj.val().split("\n");	
				this.obj.hide();
				this.checkExist(itemArr);
			}			
			this.tranformedListObject = $('#'+this.listId);
			this.tranformedListObject.sortable(
					{
						axis: 'y', 
						update: function (){
							self.syncTextArea();
						}
					});
			this.addEvents();
		},
		
		/**
		 * Creat UI
		 */
		createUi: function (){			
			this.obj.hide();			
			var itemArr = this.obj.val().split("\n");	
			var tranformedList = "<div class='jsn-items-list'  id='" + this.listId + "'>";
			var items = this.createItems(itemArr);
			tranformedList += items;
			tranformedList += "</div>";			
			this.tranformedList = $(tranformedList);
		},
		
		/**
		 * Creat Items in the list
		 */
		createItems: function (itemArr){
			var self = this;
			var items = '';		
			var uniqueItems = [];
			for (var i = itemArr.length; i--; ) {
	            var val = itemArr[i];
	            if ($.inArray(val, uniqueItems) === -1) {
	            	if(val){
	            		uniqueItems.unshift(val);
	            	}	            	
	            }
	        }
			var currentList = $('#'+this.listId);			
			
			$.each(uniqueItems, function (key, value){				
				value = self.formatInputValue(value);				
				currentItem = currentList.find('input[value="'+value+'"]');
				var checked = '';				
				if(currentItem.length){
					if(currentItem.attr('checked') == 'checked' || currentItem.attr('checked') == true){
						checked = 'checked';
					}
				}else{
					checked = '';
				}
				
				items += "<div class='jsn-item ui-state-default'>" +
							"<label class='checkbox'>" +
							"<input type='checkbox' "+ checked +" name='" + self.inputName + "' value='" + value + "'>" + value+ "</label>" +
						"</div>";
			});
			return items;
		},
		
		/**
		 * Transform between List & Edit box
		 */
		transform: function(){
			if(this.obj.css('display') == 'none'){
				if(handlerButton = this.options.handlerButton){
					var icon = handlerButton.find('i').removeClass('icon-pencil').addClass('icon-ok');
					handlerButton.text(this.options.btnDoneLabel).prepend(icon);
				}
				
				this.syncTextArea();
				this.obj.show();
				this.tranformedListObject.hide();
			}else{
				if(handlerButton = this.options.handlerButton){
					var icon = handlerButton.find('i').removeClass('icon-ok').addClass('icon-pencil');
					handlerButton.text(this.options.btnEditLabel).prepend(icon);
				}
				
				this.syncTransformedList();
				this.obj.hide();
				this.tranformedListObject.show();
			}			
		},
		
		/**
		 * Update text area value 
		 */
		syncTextArea: function ()
		{
			var newItemArr = [];
			newItemList = document.querySelectorAll('#' + this.listId + ' .jsn-item input');
			$.each(newItemList, function (key, value){
				if(value){
					newItemArr.push($(value).val());
				}					
			});				
			this.obj.val(newItemArr.join('\r'));			
		},
		
		/**
		 * Update list from text area value 
		 */
		syncTransformedList: function ()
		{
			var itemArr = this.obj.val().split("\n");
			items = this.createItems(itemArr)
			this.tranformedListObject.html(items);
			this.checkExist(itemArr);
		},
		
		/**
		 * Add events
		 */
		addEvents: function(){
			var self = this;
			this.options.handlerButton.unbind('click').bind('click', function (){
				self.transform();
				self.obj.focus();
			})
		},
		
		/**
		 * Format input value
		 * Replace quotes, double quotes...
		 */
		formatInputValue: function (str){
			str = str.replace("'","").replace('"','');
			return str;
		},
		/**
		 * Set item exist or not 
		 */
		setExistStatus: function (input, status){			
			if(status){
				input.removeAttr('disabled');
				input.addClass('jsn-existed');				
			}else{
				input.addClass('jsn-not-existed');
				input.attr('disabled','true');
				input.attr('title', this.options.fileNotExistedTitle);
			}	
		},
		/**
		 * Check exist for asset files
		 */
		checkExist: function (itemArr){		
			var self = this;
			$.each(itemArr, function (key, value){
				value = self.formatInputValue(value);
				var loadingIndicator = $('<i id="' + 'ind-' + self.inputName + '-' + key + '" class="jsn-menuassets-loading-ind">');
				var checkbox = $("input[value='" + value + "']");
				checkbox.before(loadingIndicator);				
				$.post(
					self.options.baseUrl + 'administrator/index.php?option=com_poweradmin&task=menuitem.checkassetfile',
					{
						url: value
					},
					function (data){
						if(data == 'true'){
							self.setExistStatus($("input[value='" + value + "']"), true);							
							loadingIndicator.hide();							
						}else{
							self.setExistStatus($("input[value='" + value + "']"), false);
							loadingIndicator.hide();
						}
					}
				);
			})
		}
	}
})(JoomlaShine.jQuery)