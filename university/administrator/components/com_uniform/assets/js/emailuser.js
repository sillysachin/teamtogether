/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: emailuser.js 19014 2012-11-28 04:48:56Z thailv $
 -------------------------------------------------------------------------*/
define([
	'jquery',
	'jquery.ui'],
function($) {
	var JSNUniformEmailUserView = function(params) {
		this.params = params;
		this.lang = params.language;
		this.init();
	}
	JSNUniformEmailUserView.prototype = {
		init: function() {
			$("table.table-popup tr th input.checkall").click(function() {
				if($(this).is(':checked') == true) {
					$(this).prop("checked", true);
					$("table.table-popup tr td.checkbox-items input[type=\"checkbox\"]").each(function() {
						if($(this).is(':checked') == false) {
							$(this).prop("checked", true);
						}
					});
				} else {
					$(this).prop("checked", false);
					$("table.table-popup tr td.checkbox-items input[type=\"checkbox\"]").each(function() {
						$(this).prop("checked", false);
					});
				}
			});
			$('table.table-popup tr.items').click(function() {
				var checkbox = $(this).find('input[type=\"checkbox\"].useremail');
				if(checkbox.is(':checked') == true) {
					checkbox.removeAttr('checked');
				} else {
					checkbox.attr('checked', 'true');
				}
			});
			$('input[type=\"checkbox\"].useremail').change(function() {
				if($(this).is(':checked') == true) {
					$(this).removeAttr('checked');
				} else {
					$(this).attr('checked', 'true');
				}
			});
		}
	}
	return JSNUniformEmailUserView;
});