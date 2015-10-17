(function ($) {
	$.jstree.plugin("jsn_contextmenu", {
		__init : function () {			
			var _this = this;
			var setting = this._get_settings();
			_this.contextMenu = this.get_container().jsnSubmenu({rebuild: true, rightClick:false});
			
			if ( _this.contextMenu != null ){
				if ( _this.contextMenu.isNew() ){
					addContextMenuItems(_this.contextMenu, setting.jsn_contextmenu.items);
				}
			}
			
			function addContextMenuItems(parent, items)
			{
				$.each (items, function (key, value){
					value._class = value._class == undefined ? '':value._class;					
					if (value.show === false) {
						value._class += ' jsn-hidden';
					}
					var attrs = {'class': value._class};
					
					if (value.separator_before) {
						parent.addDivider(value.label);
					}
					
					if (value.submenu == undefined || value.submenu.length < 0) {
						var item = parent.addItem( value.label, attrs );
						if (value.action != undefined) {
							item.addEventHandler('click', 
									function (e){
										e.preventDefault();										
										$('a', this).blur();
										_this.contextMenu.hide({});										
										value.action.call(_this.contextMenu.target);										
									}
							);
						}
					}else{
						var _parent = parent.addParentItem( value.label, attrs );
						addContextMenuItems(_parent, value.submenu);						
					}
					if (value.separator_after) {
						parent.addDivider(value.label);
					}
					
				})
			}
			
			this.get_container().bind('mousedown', function(e){
				if (e.target.tagName == 'A') {
					e.preventDefault();
					// Enable evenhandler for all which were disabled
					_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' ))).enableEventHandler('click');
					_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_MAKEHOME_MENU_ITEM' ))).enableEventHandler('click');
					_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_TRASH_MENU_ITEM' ))).enableEventHandler('click');
					_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_CHECKIN_MENU_ITEM' ))).enableEventHandler('click');
					
					
					$('li', _this.contextMenu.getSelf()).removeClass('disabled');
					var obj	= $(e.target).parent();					
					
					if (obj.children('ul').length > 0 && (obj.hasClass('jstree-open') || obj.hasClass('jstree-closed'))){
						_this.contextMenu.showItem(JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_EXPAND_ALL' ));
						_this.contextMenu.showItem(JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_COLLAPSE_ALL' ));	
					}else{
						_this.contextMenu.hideItem(JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_EXPAND_ALL' ));
						_this.contextMenu.hideItem(JSNLang.translate( 'TITLE_SUBMENU_SUBPANEL_COLLAPSE_ALL' ));
					}
					
					if (obj.hasClass('unpublish')){						
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' )).children('a').text(JSNLang.translate( 'TITLE_SUBMENU_PUBLISH_MENU_ITEM' ));						
					}else if( $(obj).hasClass('default') ){
						_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' ))).disableEventHandler('click');
						_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_MAKEHOME_MENU_ITEM' ))).disableEventHandler('click');
						_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_TRASH_MENU_ITEM' ))).disableEventHandler('click');
						_this.contextMenu.addSwapEvents(_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_CHECKIN_MENU_ITEM' ))).disableEventHandler('click');	

						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' )).removeClass('disabled').addClass('disabled');
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_MAKEHOME_MENU_ITEM' )).removeClass('disabled').addClass('disabled');							
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_TRASH_MENU_ITEM' )).removeClass('disabled').addClass('disabled');
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_CHECKIN_MENU_ITEM' )).removeClass('disabled').addClass('disabled');

					}else if($(obj).hasClass('checked-in')){
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_CHECKIN_MENU_ITEM' )).disableEventHandler('click').removeClass('disabled').addClass('disabled');
					}else{
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' )).children('a').text(JSNLang.translate( 'TITLE_SUBMENU_UNPUBLISH_MENU_ITEM' ));
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_MAKEHOME_MENU_ITEM' )).removeClass('disabled');
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_TRASH_MENU_ITEM' )).removeClass('disabled');
						_this.contextMenu.getItem(JSNLang.translate( 'TITLE_SUBMENU_CHECKIN_MENU_ITEM' )).removeClass('disabled');
					}
					
					_this.contextMenu.show({
						x : e.pageX,
						y : e.pageY
					});					
					_this.contextMenu.trigger('jstree.jsncontext.show');
					
				} else {					
					_this.contextMenu.hide({});
				}
				_this.contextMenu.target = e.target;				
			});			
		},
		defaults : {					
			items : { }
		},
		_fn : {		
			
		}
	});
})(jQuery);