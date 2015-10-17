/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: emailAddresses.js 14957 2012-08-10 11:47:52Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
    'jsn/libs/modal',
    'uniform/visualdesign/visualdesign',
    'jquery.tipsy',
    'jquery.scrollto',
    'jquery.json',
    'jquery.ui'],
    function ($, JSNModal, JSNVisualDesign) {

        function JSNUniform(params, visualDesign) {
            this.params = params;
            this.lang = params.language;
            this.visualDesign = visualDesign;
            this.init();
        }

        JSNUniform.prototype = {
            init:function () {
                this.btnSelectArticle = $("#list-article").size() ? $("#list-article") : $("#btn_jsnconfig_form_action_article");
                this.btnSelectMenuItem = $("#list-menuit").size() ? $("#list-menuit") : $("#btn_jsnconfig_form_action_menu");
                this.showDivAddEmail = $("#show-div-add-email");
                this.addMoreEmail = $('#addMoreEmail');
                this.inputNewEmail = $("#input_new_email");
                this.spanEmailAddress = $("span.email-address");
                this.inputEditEmail = $(".input-editemail");
                this.btnDelEmail = $("#emailAddresses .element-delete");
                this.btnEditEmail = $(".element-edit");
                this.btnSaveEmail = $("#add-email");
                this.btnSelectEmail = $("#email-select");
                this.btnCustomizeEmail = $("#jsnconfig-email-notification-field #btn_email_list");
                this.btnCustomizeEmailList = $("#form-action #btn_email_list");
                this.btnCustomizeEmailSubmit = $("#btn_email_submit");
                var self = this;
                $('.jsn-label-des-tipsy').tipsy({
                    gravity:'w',
                    fade:true
                });
                //Action
                this.btnCustomizeEmail.click(function () {
                    self.createModalCustomizeEmail();
                });
                this.btnCustomizeEmailList.click(function () {
                    self.createModalEmailList();
                });
                this.btnCustomizeEmailSubmit.click(function () {
                    self.createModalEmailSubmit();
                });
                this.btnSelectArticle.click(function () {
                    self.createModalSelectArticle();
                });
                this.btnSelectMenuItem.click(function () {
                    self.createModalSelectMenu();
                });

                this.btnSelectEmail.click(function () {
                    self.createModalSelectEmail();
                })

                //Action Email notification
                this.showDivAddEmail.click(function (e) {
                    e.stopPropagation();
                    self.inputNewEmail.val('');
                    self.addMoreEmail.show();
                    self.inputNewEmail.focus();
                    $(this).hide();
                });

                self.inputNewEmail.click(function (e) {
                    e.stopPropagation();
                }).bind('keypress', function (e) {
                        if (e.keyCode == 13) {
                            self.saveEmail('new', $(this).val());
                            return false;
                        }
                        if (e.keyCode == 27) {
                            self.addMoreEmail.hide();
                            self.showDivAddEmail.show();
                        }
                    });

                this.btnSaveEmail.click(function () {
                    if (!self.inputNewEmail) {
                        self.inputNewEmail.focus();
                    }
                    self.saveEmail('new', self.inputNewEmail.val());
                    return false;
                });

                this.btnEditEmail.click(function () {
                    if (!$(this).parent().parent().hasClass('ui-state-edit')) {
                        self.editEmail($(this).parent().parent());
                    }
                });

                this.btnDelEmail.click(function () {
                    self.removeByValue(listemail, $(this).attr('data-email'));
                    $(this).parent().parent().remove();
                });
                // close modal box
                $.closeModalBox = function () {
                    self.jsnUniformModal.close();
                    $(".jsn-modal").remove();
                }
                $(document).click(function () {
                    self.addMoreEmail.hide();
                    self.showDivAddEmail.show();
                });
            },
            //Create modal box email list select

            createModalSelectEmail:function () {
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                var self = this;
                buttons["Select"] = $.proxy(function () {
                    var $inputUserEmail = this.jsnUniformModal.iframe.contents().find("input.useremail[type=checkbox]:checked");
                    $inputUserEmail.each(function () {
                        if ($("input[type=checkbox][name='" + this.name + "']:checked")) {
                            var userEmail = $.evalJSON($(this).val());
                            self.getEmailByListUser(userEmail.email, userEmail.id);
                        }
                    });
                }, this);
                buttons["Cancel"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:'index.php?option=com_uniform&view=users&tmpl=component',
                    title:"Select Email",
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });
                this.jsnUniformModal.show();
            },
            //Create modal box Customize Email
            createModalCustomizeEmail:function () {
                var buttons = {};
                buttons["Save"] = $.proxy(function () {
                    this.jsnUniformModal.iframe[0].contentWindow.jQuery.save();
                }, this);
                buttons["Cancel"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:'index.php?option=com_uniform&view=emailsettings&layout=config&tmpl=component&action=1&control=config',
                    title:this.lang['JSN_UNIFORM_EMAIL_ADDRESS_TITLE'],
                    buttons:buttons,
                    height:600,
                    width:850,
                    scrollable:true,
                    loaded:function (modal, iframe) {
                        modal.options.loaded = null;
                        iframe.contentWindow.location.reload();
                        $(iframe).load(function(){
                            $(iframe).contents().find("#form-loading").hide();
                            $(iframe).contents().find("#uni-form").show();
                        });
                    }
                });
                this.jsnUniformModal.show();
            },
            //Create modal box config contet send email list
            createModalEmailList:function () {
                JSNVisualDesign.savePage();
                $('#jform_form_content').val(this.visualDesign.serialize());
                var buttons = {};
                buttons["Save"] = $.proxy(function () {
                    this.jsnUniformModal.iframe[0].contentWindow.save();
                }, this);
                buttons["Cancel"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:'index.php?option=com_uniform&view=emailsettings&tmpl=component&action=1&control=form&form_id=' + $("#jform_form_id").val(),
                    title:this.lang['JSN_UNIFORM_EMAIL_ADDRESS_TITLE'],
                    buttons:buttons,
                    height:750,
                    width:850,
                    scrollable:true,
                    loaded:function (modal, iframe) {
                        modal.options.loaded = null;
                        iframe.contentWindow.location.reload();
                        $(iframe).load(function(){
                            $(iframe).contents().find("#form-loading").hide();
                            $(iframe).contents().find("#uni-form").show();
                        });
                    }
                });
                this.jsnUniformModal.show();

            },
            //Create modal box config contet send email submit
            createModalEmailSubmit:function () {
                JSNVisualDesign.savePage();
                $('#jform_form_content').val(this.visualDesign.serialize());
                var buttons = {};
                buttons["Save"] = $.proxy(function () {
                    this.jsnUniformModal.iframe[0].contentWindow.save();
                }, this);
                buttons["Cancel"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:'index.php?option=com_uniform&view=emailsettings&tmpl=component&action=0&control=form&form_id=' + $("#jform_form_id").val(),
                    title:this.lang['JSN_UNIFORM_EMAIL_SUBMITTER_TITLE'],
                    buttons:buttons,
                    height:750,
                    width:850,
                    scrollable:true,
                    loaded:function (modal, iframe) {
                        modal.options.loaded = null;
                        iframe.contentWindow.location.reload();
                        $(iframe).load(function(){
                            $(iframe).contents().find("#form-loading").hide();
                            $(iframe).contents().find("#uni-form").show();
                        });
                    }
                });
                this.jsnUniformModal.show();
            },
            // Create modal box acrticle list
            createModalSelectArticle:function () {
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                buttons["Close"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:'index.php?option=com_uniform&view=articles&tmpl=component&function=jsnGetSelectArticle',
                    title:"Select Article",
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });
                this.jsnUniformModal.show();
            },
            //Create modal box menu item lists
            createModalSelectMenu:function () {
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                buttons["Close"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:'index.php?option=com_uniform&view=menus&tmpl=component&function=jsnGetSelectMenu',
                    title:"Select Menu Item",
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });
                this.jsnUniformModal.show();
            },
            //Edit email
            editEmail:function (_this) {

                var idEmail = $(_this).attr('id');
                var self = this;
                var liEdit = $("#" + idEmail);
                $('#emailAddresses div').removeClass('ui-state-edit');
                $("#emailAddresses .input-editemail").remove();
                $("#emailAddresses span.email-address").show();
                this.inputEditEmail.remove();
                this.spanEmailAddress.show();
                liEdit.find(".input-editemail").remove();
                liEdit.find("span.email-address").show();
                liEdit.append(
                    $('<div/>', {
                        'class':'input-editemail'
                    }).append(
                        $("<div/>", {
                            "class":"control-group"
                        }).append(
                            $('<input/>', {
                                type:'text',
                                value:_this.attr('data-email'),
                                "class":"jsn-input-fluid"
                            })).append(
                            $("<button/>", {
                                "class":"btn btn-icon input-add",
                                "onclick":"return false;",
                                "title":self.lang['JSN_UNIFORM_BUTTON_SAVE']
                            }).append(
                                $("<i/>", {
                                    "class":"icon-ok"
                                }))).append(
                            $("<button/>", {
                                "class":"btn btn-icon input-cancel",
                                "onclick":"return false;",
                                "title":self.lang['JSN_UNIFORM_BUTTON_CANCEL']
                            }).append(
                                $("<i/>", {
                                    "class":"icon-remove"
                                }))))).addClass("ui-state-edit");
                liEdit.find("span.email-address").hide();
                liEdit.find(".input-add").click(function (e) {
                    if (liEdit.find(".jsn-input-fluid").val()) {
                        self.saveEmail('edit', liEdit.find(".jsn-input-fluid").val(), idEmail);
                        $('#emailAddresses .jsn-item').removeClass('ui-state-edit');
                        liEdit.find(".input-editemail").hide();
                        liEdit.find("span.email-address").show()
                        e.stopPropagation();
                    } else {
                        liEdit.find(".jsn-input-fluid").focus();
                        return false;
                    }
                })
                liEdit.find(".jsn-input-fluid").focus().bind('keypress', function (e) {
                    if (e.keyCode == 13) {
                        self.saveEmail('edit', liEdit.find(".jsn-input-fluid").val(), idEmail);
                        return false;
                    }
                    if (e.keyCode == 27) {
                        $('#emailAddresses  .jsn-item').removeClass('ui-state-edit');
                        self.inputEditEmail.remove();
                        self.spanEmailAddress.show();

                    }
                });
                liEdit.find(".input-cancel").click(function (e) {
                    $('#emailAddresses  .jsn-item').removeClass('ui-state-edit');
                    liEdit.find(".input-editemail").hide();
                    liEdit.find("span.email-address").show();
                    e.stopPropagation();
                })
            },
            //Save email
            saveEmail:function (check, value, liIdOld) {
                var liId = value,
                    self = this;

                while (/[^a-zA-Z0-9_]+/.test(liId)) {
                    liId = liId.replace(/[^a-zA-Z0-9_]+/, '_');
                }
                liId = "email_" + liId;
                if (check == "new" && this.checkEmail(value)) {
                    $("#emailAddresses").append(
                        $("<li/>", {
                            id:liId,
                            "data-email":value,
                            "class":"jsn-item ui-state-default jsn-iconbar-trigger"
                        })
                            .append($("<input />", {
                            type:'hidden',
                            name:'form_email_notification[]',
                            value:value
                        }))
                            .append($("<span />", {
                            "class":"email-address"
                        }).text(value)).append(
                            $("<div/>", {
                                "class":"jsn-iconbar"
                            }).append(
                                $("<a/>", {
                                    "href":"javascript:void(0)",
                                    "title":"Edit email",
                                    "class":"element-edit",
                                    "data-email":value
                                }).append('<i class="icon-pencil"></i>')).append(
                                $("<a/>", {
                                    "href":"javascript:void(0)",
                                    "title":"Delete email",
                                    "class":"element-delete",
                                    "data-email":value
                                }).append('<i class="icon-trash"></i>'))));
                    this.showDivAddEmail.show();
                    this.addMoreEmail.hide();
                    listemail.push(this.inputNewEmail.val());
                    var element = JSNVisualDesign.getBoxStyle($("#" + liId));
                    $("#emailAddresses").scrollTop(element.offset.top);
                } else if (check == "edit" && this.checkEmail(value)) {
                    this.removeByValue(listemail, $("#" + liIdOld).attr('data-email'));
                    listemail.push(value);
                    $("#emailAddresses #" + liIdOld).before(
                        $("<li/>", {
                            id:liId,
                            "data-email":value,
                            "class":"jsn-item ui-state-default jsn-iconbar-trigger"
                        })
                            .append($("<input />", {
                            type:'hidden',
                            name:'form_email_notification[]',
                            value:value
                        }))
                            .append($("<span />", {
                            "class":"email-address"
                        }).text(value)).append(
                            $("<div/>", {
                                "class":"jsn-iconbar"
                            }).append(
                                $("<a/>", {
                                    "href":"javascript:void(0)",
                                    "title":"Edit email",
                                    "class":"element-edit",
                                    "data-email":value
                                }).append('<i class="icon-pencil"></i>')).append(
                                $("<a/>", {
                                    "href":"javascript:void(0)",
                                    "title":"Delete email",
                                    "class":"element-delete",
                                    "data-email":value
                                }).append('<i class="icon-trash"></i>'))))
                    $("#emailAddresses #" + liIdOld).hide();
                    $("#emailAddresses #" + liIdOld).find("input").attr("name", "remove");
                    $('#emailAddresses .jsn-item').removeClass('ui-state-edit');
                    this.inputEditEmail.hide();
                    this.spanEmailAddress.show();
                }
                $("#" + liId).find(".element-edit").click(function (e) {
                    if (!$(this).parent().parent().hasClass('ui-state-edit')) {
                        e.stopPropagation();
                        self.editEmail($(this).parent().parent());
                    }
                });
                $("#" + liId).find(".element-delete").click(function () {
                    self.removeByValue(listemail, $(this).attr('data-email'));
                    $(this).parent().parent().remove();
                });
            },
            // get email by list user
            getEmailByListUser:function (email, id) {
                var self = this;
                if ($.inArray(email, listemail) == -1) {
                    var liId = email;
                    while (/[^a-zA-Z0-9_]+/.test(liId)) {
                        liId = liId.replace(/[^a-zA-Z0-9_]+/, '_');
                    }
                    liId = "email_" + liId;
                    $("#emailAddresses").append(
                        $("<li/>", {
                            id:liId,
                            "data-email":email,
                            "class":"jsn-item ui-state-default jsn-iconbar-trigger"
                        })
                            .append($("<input />", {
                            type:'hidden',
                            name:'form_email_notification_user_id[]',
                            value:id
                        }))
                            .append($("<input />", {
                            type:'hidden',
                            name:'form_email_notification[]',
                            value:email
                        }))
                            .append($("<span />", {
                            "class":"email-address"
                        }).text(email)).append(
                            $("<div/>", {
                                "class":"jsn-iconbar"
                            }).append(
                                $("<a/>", {
                                    "href":"javascript:void(0)",
                                    "title":"Edit email",
                                    "class":"element-edit",
                                    "data-email":email
                                }).append('<i class="icon-pencil"></i>')).append(
                                $("<a/>", {
                                    "href":"javascript:void(0)",
                                    "title":"Delete email",
                                    "class":"element-delete",
                                    "data-email":email
                                }).append('<i class="icon-trash"></i>'))));
                    listemail.push(email);
                    var element = JSNVisualDesign.getBoxStyle($("#" + liId));
                    $("#emailAddresses").scrollTop(element.offset.top);
                    $("#" + liId).find(".element-edit").click(function (e) {
                        if (!$(this).parent().parent().hasClass('ui-state-edit')) {
                            e.stopPropagation();
                            self.editEmail($(this).parent().parent());
                        }
                    });
                    $("#" + liId).find(".element-delete").click(function () {
                        self.removeByValue(listemail, $(this).attr('data-email'));
                        $(this).parent().parent().remove();
                    });
                } else {
                    $('input[value="' + email + '"]').parent().effect("highlight", {}, 3000);
                }
                $.closeModalBox();
            },
            //Remove value in array
            removeByValue:function (arr, val) {
                for (var i = 0; i < arr.length; i++) {
                    if (arr[i] == val) {
                        arr.splice(i, 1);
                        break;
                    }
                }
            },
            //Check validate email
            checkEmail:function (val) {
                var filter = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,6})$/;
                if (!filter.test(val) || val == "") {
                    return false;
                }
                if ($.inArray(val, listemail) != -1) {
                    var liId = val;
                    while (/[^a-zA-Z0-9_]+/.test(liId)) {
                        liId = liId.replace(/[^a-zA-Z0-9_]+/, '_');
                    }
                    liId = "email_" + liId;
                    $("#emailAddresses").scrollTo($("#" + liId));
                    $('input[value="' + val + '"]').parent().effect("highlight", {}, 3000);
                    return false;
                } else {
                    return true;
                }
            }
        }
        return JSNUniform;
    });