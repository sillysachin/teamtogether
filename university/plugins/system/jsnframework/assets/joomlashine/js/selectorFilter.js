define([
	'jquery'
],
function($)
{
	// Declare JSNMedia contructor
	var JSNSelectFilter = function(params)
	{
		this.params = $.extend({}, params);
		this.lang = this.params.language || {};
		// Set event handler
		$(document).ready($.proxy(function() {
			this.initialize();
		}, this));
	};
	JSNSelectFilter.prototype = {
		initialize: function() {
			var self = this;
			$('button.filter-select').click(function(e) {
				self.dialogSelectFilters($(this));
				e.stopPropagation();
			});
		},
		dialogSelectFilters: function(_this) {
			var self = this;
			var dialog = $(".jsn-fieldset-filter-select");
			dialog.show();
			var elmStyle = self.getBoxStyle($(dialog)),
			parentStyle = self.getBoxStyle($(_this)),
			position = {};
			position.left = parentStyle.offset.left - elmStyle.outerWidth + parentStyle.outerWidth;
            position.top = parentStyle.offset.top + parentStyle.outerHeight;
			dialog.css(position).click(function(e) {
				e.stopPropagation();
			});
			$(document).click(function() {
				dialog.hide();
			});
		},
		getBoxStyle: function(element) {

			var style = {
				width: element.width(),
				height: element.height(),
				outerHeight: element.outerHeight(),
				outerWidth: element.outerWidth(),
				offset: element.offset(),
				margin: {
					left: parseInt(element.css('margin-left')),
					right: parseInt(element.css('margin-right')),
					top: parseInt(element.css('margin-top')),
					bottom: parseInt(element.css('margin-bottom'))
				},
				padding: {
					left: parseInt(element.css('padding-left')),
					right: parseInt(element.css('padding-right')),
					top: parseInt(element.css('padding-top')),
					bottom: parseInt(element.css('padding-bottom'))
				}
			};

			return style;
		}
	}
	return JSNSelectFilter;
});