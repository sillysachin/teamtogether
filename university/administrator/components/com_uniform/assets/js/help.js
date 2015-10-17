/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: help.js 19014 2012-11-28 04:48:56Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
    'jsn/libs/modal',
    'jquery.ui'],

function ($, JSNModal) {
    var JSNUniformHelpView = function () {
        this.init();
    }
    JSNUniformHelpView.prototype = {
        //Create modal box email list select 
        init: function () {
            var self = this;
            $("#jsn-help").click(function () {
                self.createModalHelp();
            })
            // close modal box
            $.closeModalBoxHelp = function () {
                self.jsnUniformModal.close();
                $(".jsn-modal").remove();
            }
        },
        createModalHelp: function () {
            var height = $(window).height();
            var width = $(window).width();
            var buttons = {};
            buttons["Close"] = $.proxy(function () {
                $.closeModalBoxHelp();
            }, this);
            this.jsnUniformModal = new JSNModal({
                url: 'index.php?option=com_uniform&view=help&tmpl=component',
                title: "Help",
                buttons: buttons,
                height: height * (95 / 100),
                width: width * (95 / 100),
                scrollable: true
            });
            this.jsnUniformModal.show();
        }
    }
    return JSNUniformHelpView;
});