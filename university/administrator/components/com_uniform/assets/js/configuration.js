/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: configuration.js 19013 2012-11-28 04:48:47Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
    'uniform/uniform',
    'uniform/help',
    'uniform/dialogedition',
    'jquery.jwysiwyg09',
    'jquery.wysiwyg.colorpicker',
    'jquery.wysiwyg.table',
    'jquery.wysiwyg.cssWrap',
    'jquery.wysiwyg.image',
    'jquery.wysiwyg.link',
    'jquery.json',
    'jquery.ui',
    'jquery.tipsy' ],
    function ($, JSNUniform, JSNHelp, JSNUniformDialogEdition) {
        var JSNUniformConfigView = function (options) {
            this.options = $.extend({
                currentAction:currentAction
            }, options);
            this.lang = options.language;
            this.actionClasses = ['no-action', 'redirect-to-url', 'redirect-to-menu', 'show-article', 'show-message'];
            this.init();
        }
        JSNUniformConfigView.prototype = {
            init:function () {
                var self = this;
                this.actionSelect = $('#jsnconfigform_action');
                this.actionPanel = $('#form-default-settings');
                this.btnConfigSave = $("button[value='configuration.save']");
                this.btnapplyFolder = $("#apply-folder");
                this.inputFolderUpload = $("#jsnconfig_folder_upload");
                this.imgLoading = $("#jsn-apply-folder-loading");
                this.registerEvents();
                this.updateAction(this.options.currentAction);
                this.JSNUniform = new JSNUniform(this.options);
                this.JSNUniform = new JSNUniform(this.options);
                this.JSNHelp = new JSNHelp();
                this.btnapplyFolder.click(function () {
                    self.applyFolder();
                });
                this.inputFolderUpload.bind('keypress', function (e) {
                    if (e.keyCode == '13') {
                        self.applyFolder();
                        return false;
                    }
                });
                //get menu item
                window.jsnGetSelectMenu = function (id, title, object, link) {
                    var valueMenu = new Object();
                    valueMenu.id = id;
                    valueMenu.title = title;
                    $("#jsnconfig_form_action_menu_title").val(title);
                    $("#jsnconfig_form_action_menu").val($.toJSON(valueMenu));
                    $.closeModalBox();
                };
                // get article
                window.jsnGetSelectArticle = function (id, title, catid, object, link) {
                    var valueArticle = new Object();
                    valueArticle.id = id;
                    valueArticle.title = title;
                    $("#jsnconfig_form_action_article_title").val(title);
                    $("#jsnconfig_form_action_article").val($.toJSON(valueArticle));
                    $.closeModalBox();
                };
                if (this.options.edition.toLowerCase() == "free") {
                    $("input[name='jsnconfig[disable_show_copyright]']").click(function () {
                        if ($(this).val() == 0) {
                            self.JSNUniformDialogEdition = new JSNUniformDialogEdition(self.options);
                            JSNUniformDialogEdition.createDialogLimitation($(this), self.lang["JSN_UNIFORM_YOU_CAN_NOT_HIDE_THE_COPYLINK"]);
                            return false;
                        }
                    });
                } else {
                    $("#jsnconfig-disable-show-copyright-field").remove();
                }
                $('.icon-question-sign').tipsy({
                    gravity:'w',
                    fade:true
                });

                $("#jsnconfig_form_action_message").wysiwyg({
                    controls:{
                        bold:{ visible:true },
                        italic:{ visible:true },
                        underline:{ visible:true },
                        strikeThrough:{ visible:true },
                        justifyLeft:{ visible:true },
                        justifyCenter:{ visible:true },
                        justifyRight:{ visible:true },
                        justifyFull:{ visible:true },
                        indent:{ visible:true },
                        outdent:{ visible:true },
                        subscript:{ visible:true },
                        superscript:{ visible:true },
                        undo:{ visible:true },
                        redo:{ visible:true },
                        insertOrderedList:{ visible:true },
                        insertUnorderedList:{ visible:true },
                        insertHorizontalRule:{ visible:true },
                        h4:{
                            visible:true,
                            className:'h4',
                            command:($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
                            arguments:($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
                            tags:['h4'],
                            tooltip:'Header 4'
                        },
                        h5:{
                            visible:true,
                            className:'h5',
                            command:($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
                            arguments:($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
                            tags:['h5'],
                            tooltip:'Header 5'
                        },
                        h6:{
                            visible:true,
                            className:'h6',
                            command:($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
                            arguments:($.browser.msie || $.browser.safari) ? '<h6>' : 'h6',
                            tags:['h6'],
                            tooltip:'Header 6'
                        },
                        html:{ visible:true },
                        increaseFontSize:{ visible:true },
                        decreaseFontSize:{ visible:true }
                    }
                });

            },
            //Submit ajax folder , check permission and create folder
            applyFolder:function () {
                var self = this;
                this.imgLoading.show();
                $("#message-apply").html("");
                var spanMessage = $("#message-apply");
                spanMessage.hide();
                $.ajax({
                    type:"POST",
                    dataType:'json',
                    url:"index.php?option=com_uniform&task=configuration.checkFolderUpload",
                    data:{
                        folder_tmp:$("#jsnconfig_folder_upload").val(),
                        folder_old:$("#folder_upload_old").val()
                    },
                    success:function (response) {
                        self.imgLoading.hide();
                        spanMessage.show();
                        if (response.success == true) {
                            spanMessage.attr("class", "label label-success").text(response.message);
                            $("#folder_upload_old").val($("#jsnconfig_folder_upload").val());

                        } else {
                            spanMessage.attr("class", "label label-important").text(response.message);
                        }
                        spanMessage.delay(3600).fadeOut(400);
                        self.btnConfigSave.removeAttr("disabled")
                        self.btnConfigSave.trigger('click');
                    }
                });
            }, //Register events
            registerEvents:function () {
                var self = this;
                this.actionSelect.bind('change', function () {
                    self.updateAction($(this).val());
                })
            },
            //Update action select box
            updateAction:function (actionIndex) {

                this.actionPanel.removeClass(this.actionClasses.join(' '));
                this.actionPanel.addClass(this.actionClasses[actionIndex]);
            }
        }
        return JSNUniformConfigView;
    });