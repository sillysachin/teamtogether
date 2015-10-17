/**
 * 
 * Rawmode Drag&Drop
 *
 * @author    JoomlaShine.com http://www.joomlashine.com
 * @copyright Copyright (C) 2011 JoomlaShine.com. All rights reserved.
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 
 Descriptions:
    1. Required files/libs:
       - jQuery lib
       - jQuery UI
       - rawmode.JSNGrid.js
**/
(function($){
	/**
	 * 
	 * Init drag&drop HTML element 
	 *
	 * @param: (array) (objs) is array element need init
	 * @param: (JoomlaShine.jQuery) (rawmode) is rawmode js
	 * @return: Init set HTML element
	 */
	$.JSNDragandDrop = function( objs, JSNGrid ) 
	{
		var $this = this;
		/**
		* Draggable multiple items
		*/
		this.items = [];
		/**
		* 
		* Init function to set events and data
		* 
		* @param: (array) (objs) is arrray elements
		* @param: (int) (i) is index item need init
		* @return: Init 
		*/
		this.init = function(objs, i){
			if (i == objs.length){
				return;
			}else{				
		 		$( objs[i] ).sortable({
					connectWith: '.jsn-poweradmin-position',
					handle: 'div.poweradmin-module-item-drag-handle',
					// tolerance: 'pointer',
					// axis: 'y',
					scroll: true,
					// scrollSensitivity:50,
					helper: function(e, item){
						if ( JSNGrid.grid.multipleselect.hasSelected( $(item) ) && JSNGrid.grid.multipleselect.hasItemMultipleSelect() ){
							var container = $('<div />', {
								'class' : 'jsn-module-multiple-select-container',
								'id'    : 'jsn-module-multiple-select-container'
							});
							var sumHeight = 0, i = 0;
							$this.items   = new Array( JSNGrid.grid.multipleselect.getTotal() );
							
							JSNGrid.grid.multipleselect.getAll().each(function(){
								sumHeight += $(this).height() + 9;
								var dragElement = $(this).clone();
								$(this).addClass('multiple-draggable').hide();
								container.append( dragElement );
								$this.items[i] = { _dragged : false, _oldElement : undefined, _dragElement : undefined, _fpos : undefined, _tpos : undefined, _id : undefined, _orders: [] };
								$this.items[i]._oldElement  = $(this);
								$this.items[i]._dragElement = $('<div />', {
									'class' : $(this).attr('class'),
									'id'    : $(this).attr('id')
								}).html( $(this).html() );
								$this.items[i]._id     = $(this).attr('id').split('-')[0];
								$this.items[i]._fpos   = $(this).parent().attr('id').replace('-jsnposition', '');
								$this.items[i++]._tpos = '';
							});
							container.css({
								'height': sumHeight
							});
						}else{
							container   =  $(item).clone();
							$this.items = new Array(1);
							$this.items[0]              = { _dragged : false, _oldElement : undefined, _dragElement : undefined, _fpos : undefined, _tpos : undefined, _id : undefined, _orders: [] };
							$this.items[0]._oldElement  = $(item);
							$this.items[0]._dragElement = container;
							$this.items[0]._id          = $(item).attr('id').split('-')[0];
							$this.items[0]._fpos        = $(item).parent().attr('id').replace('-jsnposition', '');
							$this.items[0]._tpos        = '';
						}
						$(item).show();
						return container;
					},
					start: function(event, ui){
						var moduleList = $('#modules-list');
						$.jsnSubmenu.hideAll();
						$(document)
							.unbind('mousemove.jsn')
							.bind('mousemove.jsn', function () {
								moduleList.scrollLeft(0);
							});
					},
					out: function(){
						JSNGrid.eastContentResize();
					},
					over: function(){
						JSNGrid.eastContentResize();
					},
					update: function(event, ui){
						var toPos =  $(this).attr('id').replace('-jsnposition', '');
						var i = 0, orders = new Array(), current = ui['item'];
						
						for(i = 0; i < $this.items.length; i++){
							if ($this.items[i]._tpos != undefined){
								$this.items[i]._tpos   = toPos;
								$this.items[i]._orders =  new Array();
								if ($this.items[i]._dragElement.attr('id') != ui['item'].attr('id')){
									current.after($this.items[i]._dragElement);current = current.next();
									$this.items[i]._oldElement.remove();
								}else{
									$this.items[i]._dragElement = ui['item'];
								}
								$this.items[i]._dragged = true;
							}
						}
						i = 0;
						JSNGrid.grid.modules.getAllFromParent( $(this) ).each(function(){
							orders[i] = $(this).attr('id').split('-')[0];
							i++;
						});
						for(i = 0; i < $this.items.length; i++){
							$this.items[i]._orders = orders;
						}
					},
					stop: function(event, ui){
						$(document).unbind('mousemove.jsn');

						for(var i = 0; i < $this.items.length; i++){
							if ($this.items[i]._tpos != undefined){
								try{
									if ($this.items[i]._dragged){
										if ( $this.items[i]._fpos != $this.items[i]._tpos ){
											JSNGrid.moveObjItem($this.items[i]._fpos, $this.items[i]._tpos, $this.items[i]._id);
											JSNGrid.toInactivePosition($this.items[i]._fpos);
											JSNGrid.toInactivePosition($this.items[i]._tpos);
											JSNGrid.toActivePosition($this.items[i]._tpos);
											if ( ! JSNGrid.publishing.cookie.isEnableShowUnpublishedPositions() || ! JSNGrid.publishing.cookie.isEnableUnpublished() ){
												JSNGrid.hideEmptyPositions();
											}
										}
										$this.save( $this.items[i]._dragElement, $this.items[i]._id, $this.items[i]._fpos, $this.items[i]._tpos, $this.items[i]._orders );
									}else{
										$this.items[i]._oldElement.show();
									}
								}catch(e){
									throw e.message;
								}
							}
						}						
					}
				}).disableSelection();

				setTimeout(function(){
					$this.init( objs, i+1 );
				}, 50);
			}
		};
		
		/**
		 * 
		 * Destroy function to restore system: remove events and data
		 *
		 * @param: (Array) (objs) is array HTML elements
		 * @param: (number) (i) is index array item need destroy
		 * @return: destroy sortable
		 */
		this.destroy = function( objs, i ){
//			if (i == objs.length){
//				return;
//			}else{
//				$(objs[i]).sortable();
//				$this.destroy( objs, i+1 );
//			}
		};
		
		/**
		 *
		 * Save your drag and drop modulesList
		 *		 
		 * @return: Save to the database
		 * if (not success){
		 *	undo drag
		 *}
		 */
		this.save = function( dragElement, elementId, fromPos, toPos, orders ){
			JSNGrid.grid.imageStatus.request( dragElement );
			$.post
			(
				baseUrl + 'administrator/index.php?option=com_poweradmin&view=module&task=module.moveModule&lang='+lang, 
				{
					moduleid    : elementId, 
					oldposition : fromPos, 
					newposition : toPos, 
					order       : orders
			}).success( function( msg ){
				$.checkResponse(msg);
				JSNGrid.grid.multipleselect.deSelect( dragElement );
				JSNGrid.grid.imageStatus.success( dragElement );
				dragElement.unbind("mousedown").mousedown(function(e){
					if (e.which == 1){
						if ( e.ctrlKey ){
							if ( !JSNGrid.grid.multipleselect.hasSelected( $(this) ) ){
								JSNGrid.grid.multipleselect.select( $(this) );
							}else{
								JSNGrid.grid.multipleselect.deSelect( $(this) );
							}
						}
					}
				});
				JSNGrid.buildModulesContextMenu(dragElement, false);
			}).error( function(msg){
				console.log(msg);
				JSNGrid.grid.imageStatus.error( dragElement );
				$(window).bind("imgstatus.remove", function(){
					$.autoDragDrop
					(
						{
							element    : elementId+'-jsnposition',
							toElement  : fromPos,
							dropElement: true,
							dragShow   : true,
							cloneData  : true,
							keyPrefix  : true,
							end        : function( obj ){
								JSNGrid.grid.multipleselect.deSelect( obj['item'] );
								obj['item'].unbind("mousedown").mousedown(function(e){
									if (e.which == 1){
										if ( e.ctrlKey ){
											if ( !JSNGrid.grid.multipleselect.hasSelected( $(this) ) ){
												JSNGrid.grid.multipleselect.select( $(this) );
											}else{
												JSNGrid.grid.multipleselect.deSelect( $(this) );
											}
										}
									}
								});
								JSNGrid.moveObjItem(obj['fromPos'], obj['toPos'], obj['mId']);
								JSNGrid.toInactivePosition(obj['fromPos']);
								JSNGrid.toInactivePosition(obj['toPos']);
								JSNGrid.toActivePosition(obj['toPos']);
								JSNGrid.buildModulesContextMenu(obj['goalElement'].find('div.poweradmin-module-item:last'), false);
							}
						}
					);
				});
			});
		};
		//Unbind olds init
		this.destroy(objs, 0);
		//new bind
		this.init( objs, 0 );
	};
})(JoomlaShine.jQuery);