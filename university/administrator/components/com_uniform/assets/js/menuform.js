/**
 * Description for file (if any)...
 *
 * @category Functions
 * @package Unifom
 * @author JoomlaShine.com
 * @copyright JoomlaShine.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version $Id:
 * @link JoomlaShine.com
 */
define([
    'jquery',
    'jsn/libs/modal',
    'uniform/dialogedition',
    'jquery.ui'],
    function ($, JSNModal, JSNUniformDialogEdition) {
        var edition = "";
        var JSNUniformMenuFormView = function (params) {
            this.params = params;
            this.lang = params.language;
            edition = params.edition;
            this.init();
        }
        JSNUniformMenuFormView.prototype = {
            init:function () {
                this.paramSelect();
                this.JSNUniformDialogEdition = new JSNUniformDialogEdition(this.params);
                var self = this;
                $.getSetModal = function (formId) {
                    self.loadListForm(formId);
                }
                $("#select-forms").click(function () {
                    if (window.parent)
                        window.parent.jsnSelectForm($("select.jform_request_form_id").val());
                });
            },
            loadListForm:function (formId) {
                $.ajax({
                    type:"GET",
                    async:true,
                    url:"index.php?option=com_uniform&view=forms&task=forms.getListForm",
                    dataType:'json',
                    success:function (response) {
                        $("select.jform_request_form_id").html("");
                        $("select.jform_request_form_id").append($("<option/>", {
                            "value":"",
                            "text":"- Select Form -"
                        }));
                        $.each(response, function (index, value) {
                            if (formId == value.form_id) {
                                $("select.jform_request_form_id").append(
                                    $("<option/>", {
                                        "value":value.form_id,
                                        "text":value.form_title,
                                        "selected":"selected"
                                    }))
                            } else {
                                $("select.jform_request_form_id").append(
                                    $("<option/>", {
                                        "value":value.form_id,
                                        "text":value.form_title
                                    }))
                            }

                        });
                        $("select.jform_request_form_id").trigger("change");
                    }
                });
            },
            paramSelect:function () {
                var self = this;
                var form = $('select.jform_request_form_id');
                $(form).parent().find("div").hide();
                $(form).show();
                form.change(function () {
                    if (form.val() == 0) {
                        $(this).css("background", "#CC0000").css("color", "#fff")
                        $('#form-icon-warning').css('display', '');
                        $('#form-icon-edit').css('display', 'none');
                        $('#select-forms').attr("disabled", "disabled");
                    } else {
                        $('#select-forms').removeAttr("disabled");
                        form.css("background", "#FFFFDD").css("color", "#000")
                        $('#form-icon-warning').css('display', 'none');
                        $('#form-icon-edit').css('display', '').click(function (e) {
                            if ($(this).attr("action") == "article") {
                                window.open("index.php?option=com_uniform&view=form&task=form.edit&form_id=" + form.val(), '_blank');
                                return false;
                            } else {
                                self.creatModalEditForm("index.php?option=com_uniform&view=form&task=form.edit&tmpl=component&form_id=" + form.val());
                                if ($("#jsn-modal").size()) {
                                    return false;
                                }
                            }
                        });
                    }
                }).trigger("change");

                $("#form-icon-add").click(function () {
                    if (edition.toLowerCase() == "free" && $("select.jform_request_form_id option").length > 3) {
                        JSNUniformDialogEdition.createDialogLimitation($(this), self.lang["JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0"]);
                    } else {
                        if ($(this).attr("action") == "article") {
                            window.open("index.php?option=com_uniform&view=form&task=form.edit", '_blank');
                            return false;
                        } else {
                            self.creatModalEditForm("index.php?option=com_uniform&view=form&task=form.edit&tmpl=component");
                        }


                    }
                    return false;
                });
                // close modal box
                $.closeModalBox = function () {

                    self.jsnUniformModal.close();
                    $(".jsn-modal").remove();
                }
            },
            //Create modal box Edit Form
            creatModalEditForm:function (src) {
                $(".jsn-modal").remove();
                var height = $(window).height();
                var width = $(window).width();
                var buttons = {};
                buttons["Save"] = $.proxy(function () {
                    this.jsnUniformModal.iframe[0].contentWindow.parentSaveForm();
                }, this);
                buttons["Close"] = $.proxy(function () {
                    $.closeModalBox();
                }, this);
                this.jsnUniformModal = new JSNModal({
                    url:src,
                    title:"Form Settings",
                    buttons:buttons,
                    height:height * (95 / 100),
                    width:width * (95 / 100),
                    scrollable:true
                });

                this.jsnUniformModal.show();
            }

        }
        return JSNUniformMenuFormView;
    });