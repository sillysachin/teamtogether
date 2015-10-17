/**
 * Functions
 * 
 * Added 24/08/2011
 * Power by JoomlaShine - http://www.joomlashine.com
 * Package JSNPOWERADMIN
 * Subpackage - functions
 * 
 **/
(function($){	
	$.draganddrop = function(){
		
		var _this = this;
	    //Define an item object to store
		this.item = $.extend({
			_fpos:'',
			_tpos:'',
			_id:'',
			_orders:[]
		});
	    
	    /**
		  * Drop & Save drag module if jQuery UI not received
		  */
		 var dropObject     = false;
		 var dragObject     = null;
		 var dragOverObject = null;
		 this.dragOut = function(){
		 	if (dragOverObject.parent() !== undefined && dragOverObject.parent().attr('id') != undefined){
                try{
                	dragOverObject.before(dragObject.draggable);
                }catch(e){
                	throw e.message;
                }finally{
                	//
                }
			 	_this.item._tpos =  dragOverObject.parent().attr('id').replace('-jsnposition', '');
				var i = 0;
				dragOverObject.parent().find('.poweradmin-module-item').each(function(){
					if ($(this).attr('id') !== undefined){
						_this.item._orders[i] = $(this).attr('id').split('-')[0];
						i++;
					}
				});

				this.save();

		 	}
		 	$('.ui-draggable-dragging').removeClass('ui-draggable-dragging');
		    $('.poweradmin-module-item').attr('style', 'position:relative;');
		 };	
		 
		 /**
		 * Destroy Drag&Drop
		 */
		this.destroy = function(){
			$('.jsn-poweradmin-position div.poweradmin-module-item').draggable("destroy");
			$('.jsn-poweradmin-position div.poweradmin-module-item').droppable("destroy");
			$('.poweradmin-module-item').droppable("destroy");
		};
		this.destroy();
		
	    /**
	     * JSN Poweradmin Drag&Drop module
	     */
		this.init = function(){
		
			$('.jsn-poweradmin-position div.poweradmin-module-item').draggable({
				revert:false,
				start: function(event, s){
					if ($(this).parent().attr('id')!= undefined){
				    	_this.item._id = $(this).attr('id').split('-')[0];
						_this.item._fpos = $(this).parent().attr('id').replace('-jsnposition', '');
				    }else{
				    	_this.item._fpos = '';
				    }
					
					dropObject = false;
					
					$('.inactive-position').each(function(){
						if ($(this).find('.poweradmin-module-item').length == 0){
							var text = $(this).text();
							$(this).html('');
							$('<div/>')
							  .appendTo(this)
							  .addClass('poweradmin-module-item')
							  .addClass('jsn-temp-item')								  
							  .droppable({
									over:function(){
										dragOverObject = $(this);
										dragObject = s;
										
								  		$(this).addClass('jsn-temp-item-hover');
										$(this).css('margin-top', '-5px');
										$(this).css('margin-left', '-10px');
										$(this).css('height', $(this).parent().height());
							  		},
									
									out:function(){
										$(this).removeClass('jsn-temp-item-hover');
									},
									
									drop:function(e,s){
										try{
											$(this).after(s.draggable);
										}catch(e){
											throw e.message;
										}finally{
											//
										}
										var dropItem = $(this).next().fadeOut(0).fadeIn(300);
										_this.item._tpos   = $(this).parent().attr('id').replace('-jsnposition', '');
										
										_this.item._orders = new Array();
										_this.item._orders[0] = _this.item._id;
										
										_this.save();
										$(this).parent().removeClass('inactive-position');
										$(this).parent().find('label[class="jsn-position-name"]').remove();
										$('.jsn-item-hover').remove();
										$('.jsn-temp-item').remove();										
										$(this).parent().find('.jsn-item-hover').remove();
										setTimeout(function(){
											var _blueBoxContainer = $('.jsn-show-module-container', dropItem);
											_blueBoxContainer.children('.jsn-show-module').css('width', _blueBoxContainer.width()-6);
											if (_blueBoxContainer.height() < 29 ) {
												_blueBoxContainer.children('.jsn-show-module').css('height', '28px');
												if ( _blueBoxContainer.height() < 24 ) {
													_blueBoxContainer.children('.jsn-show-module').css('top', '-5px');
												}
											} else {
												_blueBoxContainer.children('.jsn-show-module').css('height', _blueBoxContainer.height()-6);
											}
										}, 200);
										
										$('.jsn-poweradmin-position').each(function(){
											if ($(this).find('.poweradmin-module-item').length == 0){
												$(this).html('');
												$(this).addClass('inactive-position');
												$('<label/>').appendTo(this).addClass('jsn-position-name').html($(this).attr('id').replace('-jsnposition', ''));
												$('<a />', {'title':'Add new module to this position.', 'class':'add-new-module', 'href':'javascript:;'}).appendTo(this).click(function(){window.parent.jQuery._visualmode.addNewModule(this);});
											}
										});
										
									}
							})
							.html(text)
							.css('z-index', $.topZIndex());
						}
					});
				
				},
				drag: function(e){
					$(this).css('z-index', $.topZIndex());
					$(this).css('opacity', 1);
					
				},
				stop:function(){
					if (!dropObject && (dragOverObject instanceof jQuery && dragOverObject != null)){
						_this.dragOut();
					}else{
						$(this).removeClass('ui-draggable-dragging');
						$(this).attr('style', 'position:relative;');
					}
					
					$('.jsn-temp-item').each(function(){
						var text = $(this).text();
						$(this).parent().html(text);
					});
					$('.jsn-item-hover').remove();
					$('.jsn-poweradmin-position').each(function(){
						if ($(this).find('.poweradmin-module-item').length == 0){
							$(this).html('');
							$(this).addClass('inactive-position');
							$('<label/>').appendTo(this).addClass('jsn-position-name').html($(this).attr('id').replace('-jsnposition', ''));
							$('<a />', {'title':'Add new module to this position.', 'class':'add-new-module', 'href':'javascript:;'}).appendTo(this).click(function(){window.parent.jQuery._visualmode.addNewModule(this);});
						}
					});
					
				}
			});
			
			$('.jsn-poweradmin-position div.poweradmin-module-item').droppable({
				over: function(e,s){
					dragOverObject = $(this);
					dragObject = s;
					
				    $('.jsn-item-hover').remove();
					if ( $(this).mouseHoverBottom()){
						$(this).after('<div class="jsn-item-hover"></div>');
					}else{
						$(this).before('<div class="jsn-item-hover"></div>');
					}
				},
				
				out: function(e,s){			
					$('.jsn-item-hover').remove();
				},
				
				drop: function(e,s){
					try{
						if ( $(this).mouseHoverBottom() ){
							$(this).after(s.draggable);
							var dropItem = $(this).next().fadeOut(0).fadeIn(300);
						}else{
							$(this).before(s.draggable);
							var dropItem = $(this).prev().fadeOut(0).fadeIn(300);
						}
					}catch(e){
						throw e.message;
					}finally{
					}
					
					_this.item._tpos =  $(this).parent().attr('id').replace('-jsnposition', '');
					var i = 0;
					$(this).parent().find('.poweradmin-module-item').each(function(){
						_this.item._orders[i] = $(this).attr('id').split('-')[0];
						i++;
					});						
					
					_this.save();
					$('.jsn-item-hover').remove();
					$('.jsn-temp-item').remove();
					setTimeout(function(){
						var _blueBoxContainer = $('.jsn-show-module-container', dropItem);
						_blueBoxContainer.children('.jsn-show-module').css('width', _blueBoxContainer.width()-6);
						if (_blueBoxContainer.height() < 29 ) {
							_blueBoxContainer.children('.jsn-show-module').css('height', '28px');
							if ( _blueBoxContainer.height() < 24 ) {
								_blueBoxContainer.children('.jsn-show-module').css('top', '-5px');
							}
						} else {
							_blueBoxContainer.children('.jsn-show-module').css('height', _blueBoxContainer.height()-6);
						}
					}, 200);
					
					$('.jsn-poweradmin-position').each(function(){
						if ($(this).find('.poweradmin-module-item').length == 0){
							$(this).html('');
							$(this).addClass('inactive-position');
							$('<label/>').appendTo(this).addClass('jsn-position-name').html($(this).attr('id').replace('-jsnposition', ''));
							$('<a />', {'title':'Add new module to this position.', 'class':'add-new-module', 'href':'javascript:;'}).appendTo(this).click(function(){window.parent.jQuery._visualmode.addNewModule(this);});
						}
					});
				}
			});
		};
		
		/**
		 * Save sort to Database
		 */
		this.save = function(){
			$.post( baseUrl + 'index.php?option=com_poweradmin&view=modules&task=modules.moveModule&lang='+lang, 
				{
					moduleid:    _this.item._id, 
					oldposition: _this.item._fpos, 
					newposition: _this.item._tpos, 
					order:       _this.item._orders
				}, function( msg ){
					window.parent.jQuery._visualmode.showMessage(msg);
			});
		};
		
		//Set this to new variable
		_this = this;
		
		//Call init function
		this.init();
		
		$('div.poweradmin-module-item').unbind("jsn.autodragdrop").bind("jsn.autodragdrop", function(){
			$(this).draggable("destroy")
			       .droppable("destroy")
			       .removeClass('ui-draggable')
			       .removeClass('ui-droppable');
		});
	};
	
	/**
	 * Destroy UI Drag and Drop 
	 */
	$.draganddrop_destroy = function(){
		$('.jsn-poweradmin-position div.poweradmin-module-item').draggable("destroy");
		$('.jsn-poweradmin-position div.poweradmin-module-item').droppable("destroy");
		$('.poweradmin-module-item').droppable("destroy");
	};
})(JoomlaShine.jQuery);