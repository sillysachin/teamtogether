define([
    'jquery'
],

    function ($) {
        // Declare JSNMedia contructor
        var JSNMenuToolBar = function (params) {
            this.params = $.extend({}, params);
            this.lang = this.params.language || {};
            // Set event handler
            $(document).ready($.proxy(function () {
                this.initialize();
            }, this));
        };
        JSNMenuToolBar.prototype = {
            initialize:function () {
                var self = this;
                $('li.menu-name').bind('mouseleave',function (e) {
                    self.hideSubMenu($(this).find(".jsn-submenu"));
                }).hover(function () {
                        $('.jsn-submenu > li').removeClass("active");
                 });
                $('.jsn-submenu').bind('mouseleave', function (e) {
                    self.hideSubMenu($(this));
                });
                $('.jsn-submenu > li').hover(function () {
                    $('.jsn-submenu > li').removeClass("active");
                    $(this).addClass("active");
                });
            },
            hideSubMenu:function (_this) {
                $(_this).css({
                    "left":"0",
                    "right":"auto"
                });
                setTimeout(function () {
                    $(_this).css({
                        "left":"",
                        "right":""
                    });
                }, 500);
            }
        }

        return JSNMenuToolBar;
    });