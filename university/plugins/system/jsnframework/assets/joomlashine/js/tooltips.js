define(
	['jquery', 'jquery.tipsy'],
	function($) {
		var tipsySetup = function(params) {
			this.params = $.extend({
				selector: 'form label.control-label[original-title]'
			}, params);

			$(this.params.selector).tipsy({
				gravity: 'w',
				fade: true
			});
		};

		return tipsySetup;
	}
);