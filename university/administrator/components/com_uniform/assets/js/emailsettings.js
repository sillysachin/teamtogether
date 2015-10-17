/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: emailsettings.js 14957 2012-08-10 11:47:52Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
    'jquery.json',
    'jquery.tipsy',
    'uniform/libs/jquery.placeholder',
    'jquery.ui'],
    function ($) {
        var JSNUniformEmailSettingsView = function (params) {
            this.params = params;
            this.lang = params.language;
            this.init();
        }
        JSNUniformEmailSettingsView.prototype = {
            init:function () {
                var self = this;
                var listOptionPage = [];
                var wordlist = [];
                $('.jsn-label-des-tipsy').tipsy({
                    gravity:'w',
                    fade:true
                });
                if (parent.jQuery(".jsn-page-settings").length < 1) {
                    window.location.href = "index.php?option=com_uniform";
                }
                $(".toggle-editor a").click(function () {
                    $("#jform_template_message").css("width", "530px");
                    if ($("#jform_template_message").css("display") == "none") {
                        $("#btn-select-field-message").show();
                    } else {
                        $("#btn-select-field-message").hide();
                    }
                });

                if (this.params.editor == 'none') {
                    $("#jform_template_message").css("width", "530px");
                }
                if (this.params.editor == 'jce') {
                    $("#btn-select-field-message").css("margin-top", "30px");
                }

                if ($("#template_notify_to").val() == 0) {
                    $("#jform_template_from").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_FROM_0']);
                    $("#jform_template_reply_to").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_REPLY_TO_0']);
                    $("#jform_template_subject").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_0']);
                } else {
                    $("#jform_template_from").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_FROM_1']);
                    $("#jform_template_reply_to").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_REPLY_TO_1']);
                    $("#jform_template_subject").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_1']);
                }
                parent.jQuery(" ul.jsn-page-list li.page-items").each(function () {
                    listOptionPage.push([$(this).find("input").attr('data-id'), $(this).find("input").attr('value')]);
                });
                $.ajax({
                    type:"POST",
                    dataType:'json',
                    url:"index.php?option=com_uniform&view=form&task=form.loadsessionfield&tmpl=component",
                    data:{
                        form_id:parent.jQuery("#jform_form_id").val(),
                        form_page_name:parent.jQuery("#form-design-header").attr('data-value'),
                        form_list_page:listOptionPage
                    },
                    success:function (response) {
                        var replyToSelect = "";
                        var liFields = "";
                        var fileAttach = "";
                        //  var typeClear = ["file-upload"];
                        var defaultAttach =  $("#attach-file ul").attr("data-value");
                        var dataAttach = "";
                        if(defaultAttach){
                            dataAttach = $.evalJSON(defaultAttach);
                        }

                        if (response) {
                            $.each(response, function (i, item) {
                                if(item.type != "google-maps"){
                                    if (item.type == 'email') {
                                        replyToSelect += "<li class=\"jsn-item\" id='" + item.identify + "'>" + item.options.label + "</li>";
                                    }
                                    if (item.type == 'file-upload') {
                                        if($.inArray(item.identify,dataAttach)>=0){
                                            fileAttach += '<li class="jsn-item ui-state-default"><label class="checkbox">'+ item.options.label + '<input type="checkbox" checked="checked" name="file_attach[]" value="' + item.identify + '"></label></li>';
                                        }else{
                                            fileAttach += '<li class="jsn-item ui-state-default"><label class="checkbox">'+ item.options.label + '<input type="checkbox" name="file_attach[]" value="' + item.identify + '"></label></li>';
                                        }

                                    }
                                    liFields += "<li class=\"jsn-item\" id='" + item.identify + "'>" + item.options.label + "</li>";
                                    wordlist.push(item.options.label);
                                }
                            });
                        }
                        if ($("#template_notify_to").val() == 1) {
                            self.createListField($("#btn-select-field-from"), liFields, "FIELD");
                            self.createListField($("#btn-select-field-to"), replyToSelect, "EMAIL");
                        }
                        if(fileAttach){
                            $("#attach-file ul").html(fileAttach);
                        }
                        self.createListField($("#btn-select-field-message"), liFields, "FIELD");
                        self.createListField($("#btn-select-field-subject"), liFields, "FIELD");
                    }
                });
                $('input, textarea').placeholder();
            },
            eventField:function (field, btnField, type) {
                var self = this;
                var oldField = "";
                $(field).find(".jsn-items-list .jsn-item").click(function () {
                    if (this.id) {
                        switch (type) {
                            case "btn-select-field-message":
                                jInsertEditorText('{$' + this.id + '}', 'jform_template_message');
                                break;
                            case "btn-select-field-from":
                                $("#jform_template_from").val($("#jform_template_from").val() + "{$" + this.id + "}");
                                break;
                            case "btn-select-field-subject":
                                $("#jform_template_subject").val($("#jform_template_subject").val() + "{$" + this.id + "}");
                                break;
                            case "btn-select-field-to":
                                $("#jform_template_reply_to").val($("#jform_template_reply_to").val() + "{$" + this.id + "}");
                                break;
                        }
                        $("div.control-list-fields").hide();
                    }
                });
                $(btnField).click(function (e) {
                    $("div.control-list-fields").hide();
                    var elmStyle = self.getBoxStyle($(field)),
                        parentStyle = self.getBoxStyle($(this)),
                        position = {};
                    position.left = parentStyle.offset.left - elmStyle.outerWidth + parentStyle.outerWidth;
                    position.top = parentStyle.offset.top + parentStyle.outerHeight;
                    $(field).find(".arrow").css("left", elmStyle.outerWidth - (parentStyle.outerWidth / 2));
                    $(field).css(position);
                    $(field).show();
                    e.stopPropagation();
                });
                $("div.control-list-fields").click(function (e) {
                    e.stopPropagation();
                });
                $.fn.delayKeyup = function (callback, ms) {
                    var timer = 0;
                    var el = $(this);
                    $(this).keyup(function () {
                        clearTimeout(timer);
                        timer = setTimeout(function () {
                            callback(el)
                        }, ms);
                    });
                    return $(this);
                };
                $(field).find(".jsn-quicksearch-field").delayKeyup(function (el) {
                    self.searchField($(el).val(), $(field).find(".jsn-items-list"));
                    if ($(el).val() == "") {
                        $(field).find(".jsn-reset-search").hide();
                    } else {
                        $(field).find(".jsn-reset-search").show();
                    }
                }, 500)

                $(document).click(function () {
                    $("div.control-list-fields").hide();
                    $(field).find(".jsn-reset-search").trigger("click");
                });
            },
            // Search field in list
            searchField:function (value, resultsFilter) {
                $(resultsFilter).find("li").hide();
                if (value != "") {
                    $(resultsFilter).find("li").each(function () {
                        var textField = $(this).attr("id").toLowerCase();
                        if (textField.search(value.toLowerCase()) == -1) {
                            $(this).hide();
                        } else {
                            $(this).fadeIn(800);
                        }
                    });
                } else {
                    $(resultsFilter).find("li").each(function () {
                        $(this).fadeIn(800);
                    });
                }
            },
            //Create list field
            createListField:function (btnInput, fields, type) {
                var self = this;
                var listField = fields;
                if (!fields) {
                    listField = "<li title=\"" + self.lang["JSN_UNIFORM_NO_" + type + "_DES"] + "\" class=\"ui-state-default ui-state-disabled\">" + self.lang["JSN_UNIFORM_NO_" + type] + "</li>"
                }
                var dialog = $("<div/>", {
                    'class':'control-list-fields jsn-bootstrap hide',
                    'id':"control-" + $(btnInput).attr("id")
                }).append(
                    $("<div/>", {
                        "class":"popover"
                    }).css("display", "block").append($("<div/>", {
                        "class":"arrow"
                    })).append($("<h3/>", {
                        "class":"popover-title",
                        "text":this.lang['JSN_UNIFORM_SELECT_FIELDS']
                    })).append(
                        $("<form/>").append(
                            $("<div/>", {"class":"jsn-elementselector"}).append(
                                $("<div/>", {"class":"jsn-fieldset-filter"}).append(
                                    $("<fieldset/>").append(
                                        $("<div/>", {"class":"pull-right"}).append(
                                            $("<input/>", {
                                                "type":"text",
                                                "class":"jsn-quicksearch-field input search-query",
                                                "placeholder":"Search…"
                                            }).bind('keypress', function (e) {
                                                    if (e.keyCode == 13) {
                                                        return false;
                                                    }
                                                })
                                        ).append(
                                            $("<a/>", {"href":"javascript:void(0);", "title":"Clear Search", "class":"jsn-reset-search"}).append($("<i/>", {"class":"icon-remove"})).click(function () {
                                                $(dialog).find(".jsn-quicksearch-field").val("");
                                                self.searchField("", $(dialog).find(".jsn-items-list"));
                                                $(this).hide();
                                            })
                                        )
                                    )
                                )
                            ).append(
                                $("<ul/>", {"class":"jsn-items-list"}).append(listField)
                            )
                        )
                    )
                )
                if (!fields) {
                    $(dialog).find(".field-seach").hide();
                } else {
                    $(dialog).find(".field-seach").show();
                }
                $(dialog).find(".jsn-quicksearch-field").attr("placeholder","Search…");
                $(dialog).appendTo('body');
                dialog.hide();
                self.eventField("#control-" + $(btnInput).attr("id"), btnInput, $(btnInput).attr("id"));
                $(document).click(function () {
                    dialog.hide();
                });
                $('input, textarea').placeholder();
            },
            getBoxStyle:function (element) {
                var display = element.css('display')
                if (display == 'none') {
                    element.css({
                        display:'block',
                        visibility:'hidden'
                    });
                }
                var style = {
                    width:element.width(),
                    height:element.height(),
                    outerHeight:element.outerHeight(),
                    outerWidth:element.outerWidth(),
                    offset:element.offset(),
                    margin:{
                        left:parseInt(element.css('margin-left')),
                        right:parseInt(element.css('margin-right')),
                        top:parseInt(element.css('margin-top')),
                        bottom:parseInt(element.css('margin-bottom'))
                    },
                    padding:{
                        left:parseInt(element.css('padding-left')),
                        right:parseInt(element.css('padding-right')),
                        top:parseInt(element.css('padding-top')),
                        bottom:parseInt(element.css('padding-bottom'))
                    }
                };
                element.css({
                    display:display,
                    visibility:'visible'
                });
                return style;
            }
        }
        return JSNUniformEmailSettingsView;
    });

function save() {
    jQuery(".control-list-fields").hide();
    jQuery("#uni-form").hide();
    jQuery("#form-loading").show();
    document.adminForm.submit();
    return false;
}