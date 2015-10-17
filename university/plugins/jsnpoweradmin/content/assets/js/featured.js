/**
 * @subpackage	com_poweradmin (JSN POERADMIN JoomlaShine - http://www.joomlashine.com)
 * @copyright	Copyright (C) 2001 BraveBits,Ltd. All rights reserved.
 **/
(function($){
	$.com_content_featured = function(Itemid){
		if ($['com_content_category_blog'] == undefined){
			var JSNComponent = new $.JSNComponent( 'com_content', 'category', 'blog', Itemid );
			JSNComponent.__destruct();
			JSNComponent.__construct( 'com_content', 'category', 'blog', Itemid );
			$(window).unbind('jsn.script.loaded.success').bind('jsn.script.loaded.success', function(){
				var JSNComponent = new $.JSNComponent( 'com_content', 'featured', '', Itemid );
				JSNComponent.__destruct();
				JSNComponent.__construct( 'com_content', 'featured', '', Itemid );
			});
		}else{
			var featured = new $.com_content_category_blog(Itemid);
			for( k in featured ){
				this[k] = featured[k];
			}
			this.initVariables = function(){
				this.option = 'com_content';
				this.view   = 'featured';
				this.layout = '';
				this.itemid = Itemid;
				this.setData( 'option', 'com_content' );
				this.setData( 'view', 'featured' );
				this.setData( 'layout', '' );
				this.setData( 'id', '' );
				this.setData( 'Itemid', Itemid );
				this.setData( 'requestType', 'only' );
				
				//Scan elements approved context menu
				this.contextElements = new Array( $( this.classApprovedContextMenu ).length );
				var $this = this;
				$( '.'+this.classApprovedContextMenu ).each(function(){
					//Children items is of category
					if ( ( $(this).parents('div.category').length || $(this).hasClass('category-desc') ) && $(this).attr('id') != 'show_description_image' || $(this).hasClass('empty-category')){
						$(this).data('category', true);
					}else{
						$(this).data('category', false);
					}
					//Children items is of article
					if ( $(this).parents('div.article_layout').length && (/show_intro/.test($(this).attr('id')) || /show_title/.test($(this).attr('id'))) ){
						$(this).data('article', true);
					}else{
						$(this).data('article', false);
					}
					//Children items is of linkitems
					if ( $(this).parents('div.link-items').length ){
						$(this).data('link_items', true);
					}else{
						$(this).data('link_items', false);
					}
					//Children items is of children categories
					if ( $(this).hasClass('cat-children') ){
						$(this).data('categories_childrens', true);
					}else{
						$(this).data('categories_childrens', false);
					}
					//Children items is of pagination
					if ( $(this).parents('div.jsn-rawmode-pagination').length ){
						$(this).data('pagination', true);
					}else{
						$(this).data('pagination', false);
					}
					
					if ( $(this).hasClass('display-default') ){
						$(this).data('show', true);
					}else{
						$(this).data('show', false);
					}
					
					if ( $(this).hasClass('createdby') && $(this).attr('contactid') != ''){
						$(this).data('author', true);
					}else{
						$(this).data('author', false);
					}
					
					if (/show_intro/.test($(this).attr('id'))){
						$(this).data('introtext', true);
					}else{
						$(this).data('introtext', false);
					}
					$this.contextElements[$(this).attr('id')] = $(this);
				});
			};
		}
	};
})(JoomlaShine.jQuery);