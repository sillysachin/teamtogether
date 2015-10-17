/*------------------------------------------------------------------------
# JSN PowerAdmin
# ------------------------------------------------------------------------
# author    JoomlaShine.com Team
# copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
# Websites: http://www.joomlashine.com
# Technical Support:  Feedback - http://www.joomlashine.com/joomlashine/contact-us.html
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @version $Id$
-------------------------------------------------------------------------*/
(function ($){
	/*
	 * extend 'contains' selector of jquery
	 * to make case-insensitve selection 
	 */
	$.expr[':'].ciContains = function(obj, index, meta, stack){
		return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;
	};	
	
	jQuery.fn.highlight = function (str, className) {
	    var regex = new RegExp(str, "gi");
	    return this.each(function () {
	        this.innerHTML = this.innerHTML.replace(regex, function(matched) {
	            return "<span class=\"" + className + "\">" + matched + "</span>";
	        });
	    });
	};
	
	$.fn.stripTags = function ()
	{ 
		return this.replaceWith( this.html().replace(/<\/?[^>]+>/gi, '') );
	};

})(JoomlaShine.jQuery);