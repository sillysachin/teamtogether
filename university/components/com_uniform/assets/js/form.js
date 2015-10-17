(function ($) {
    $(function () {
        var lang = null, forms = [], baseUrl = "";
        $.initJSNForm = function (formname) {
            var self = this;
            if ($("form[name=form_" + formname + "]").width() <= 480) {
                $("form[name=form_" + formname + "]").addClass("jsn-narrow");
            }
            $(".form-captcha").hide();

            if ($('.jsn-uniform .icon-question-sign').length) {
                $('.jsn-uniform .icon-question-sign').tipsy({
                    gravity:'w',
                    fade:true
                });
            }

            $('form[name=form_' + formname + '] input,form[name=form_' + formname + '] button.btn,form[name=form_' + formname + '] textarea,form[name=form_' + formname + '] select').focus(function () {
                var form = $(this).parents('form:first');
                $(form).find(".ui-state-highlight").removeClass("ui-state-highlight");
                $(this).parents(".control-group").addClass("ui-state-highlight");
                self.captcha(form);
            }).click(function (e) {
                    var form = $(this).parents('form:first');
                    $(form).find(".ui-state-highlight").removeClass("ui-state-highlight");
                    $(this).parents(".control-group").addClass("ui-state-highlight");
                    e.stopPropagation();
                });
            $("form[name=form_" + formname + "] input").keypress(function (e) {
                if (e.which == 13) {
                    if ($("form[name=form_" + formname + "] button.next").hasClass("hide")) {
                        $("form[name=form_" + formname + "] button.jsn-form-submit").click();
                    } else {
                        $("form[name=form_" + formname + "] button.next").click();
                    }
                    return false;
                }
            });
            $("form[name=form_" + formname + "] .form-actions .jsn-form-submit").click(function () {
                $("form[name=form_" + formname + "]").submit();
                return false;
            });
            $(document).click(function () {
                $(".ui-state-highlight").removeClass("ui-state-highlight");
            });
            var formDefaultCaptcha = $('.form-captcha')[0];
            if ($(formDefaultCaptcha).size()) {
                $($(formDefaultCaptcha).parents('form:first').find("input,textarea,select")[0]).focus();
            }
            var randomizeListGroups = $('form[name=form_' + formname + '] select.list');
            randomizeListGroups.each(function () {
                if ($(this).hasClass("list-randomize")) {
                    self.randomValueItems(this);
                }
            });
            var randomizeDropdownGroups = $('form[name=form_' + formname + '] select.dropdown');
            randomizeDropdownGroups.each(function () {
                var selfDropdown = this;
                if ($(this).hasClass("dropdown-randomize")) {
                    self.randomValueItems(this);
                    $(this).find("option").each(function () {
                        if ($(this).attr("selectdefault") == "true") {
                            $(this).prop("selected", true);
                        }
                    });
                }
                $(this).change(function () {
                    if ($(this).val() == "Others" || $(this).val() == "others") {
                        $(selfDropdown).parent().find("textarea.jsn-dropdown-Others").removeClass("hide");
                    } else {
                        $(selfDropdown).parent().find("textarea.jsn-dropdown-Others").addClass("hide");
                    }
                });
            });
            var randomizeChoiceGroups = $('form[name=form_' + formname + '] div.choices');
            randomizeChoiceGroups.each(function () {
                var selfChoices = this;
                if ($(this).hasClass("choices-randomize")) {
                    self.randomValueItems(this);
                }
                $(this).find("input[type=radio]").click(function () {
                    if ($(this).val() == "Others" || $(this).val() == "others") {
                        $(selfChoices).find("textarea.jsn-value-Others").removeAttr("disabled");
                    } else {
                        $(selfChoices).find("textarea.jsn-value-Others").attr("disabled", "true");
                    }
                });
            });
            var randomizeCheckboxGroups = $('form[name=form_' + formname + '] div.checkboxes');
            randomizeCheckboxGroups.each(function () {
                var selfChexbox = this;
                if ($(this).hasClass("checkbox-randomize")) {
                    self.randomValueItems(this);
                }
                $(this).find(".lbl-allowOther input[type=checkbox]").click(function () {
                    if ($(this).is(':checked')) {
                        $(selfChexbox).find("textarea.jsn-value-Others").removeAttr("disabled");
                    } else {
                        $(selfChexbox).find("textarea.jsn-value-Others").attr("disabled", "true");
                    }
                });
            });
            $("form[name=form_" + formname + "]").find("input.number,input.phone,input.currency").each(function () {
                $(this).keypress(function (e) {
                    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                        return false;
                    }
                });
            });
            $("form[name=form_" + formname + "]").find("div.choices .jsn-column-item input").change(function () {
                if ($(this).is(':checked')) {
                    var idField = $(this).parents(".jsn-columns-container").attr("id");
                    $("form[name=form_" + formname + "]").find("div.control-group." + idField).removeAttr("style");
                    self.getActionField(formname, $(this), idField);
                }
            }).trigger("change");
            $("form[name=form_" + formname + "]").find("div.checkboxes .jsn-column-item input").change(function () {
                var idField = $(this).parents(".jsn-columns-container").attr("id");
                $("form[name=form_" + formname + "]").find("div.control-group." + idField).removeAttr("style");
                $(this).parents(".jsn-columns-container").find("input").each(function () {
                    if ($(this).is(':checked')) {
                        self.getActionField(formname, $(this), idField);
                    }
                });
            }).trigger("change");
            $("form[name=form_" + formname + "]").find("select.dropdown").change(function () {
                var idField = $(this).attr("id");
                $("form[name=form_" + formname + "]").find("div.control-group." + idField).removeAttr("style");
                self.getActionField(formname, $(this), idField);
            }).trigger("change");
            $("form[name=form_" + formname + "]").find("input.limit-required,textarea.limit-required").each(function () {
                var settings = $(this).attr("data-limit");
                var limitSettings = $.evalJSON(settings);
                if (limitSettings) {
                    $(this).keypress(function (e) {
                            s
                            if (e.which != 27 && e.which != 13 && e.which != 8) {
                                if (limitSettings.limitType == "Characters" && $(this).val().length == limitSettings.limitMax) {
                                    alert(lang['JSN_UNIFORM_CONFIRM_FIELD_MAX_LENGTH'] + " " + limitSettings.limitMax + " "+lang['JSN_UNIFORM_CHARACTERS']);
                                    return false;
                                }
                                if (limitSettings.limitType == "Words") {
                                    var lengthValue = $.trim($(this).val()).split(/[\s]+/);
                                    if (lengthValue.length == limitSettings.limitMax && e.which != 0) {
                                        alert(lang['JSN_UNIFORM_CONFIRM_FIELD_MAX_LENGTH'] + " " + limitSettings.limitMax + " "+lang['JSN_UNIFORM_WORDS']);
                                        return false;
                                    }
                                }
                            }
                        }
                    );
                }
            });
            $("form[name=form_" + formname + "] input,textarea").bind('change', function () {
                self.checkValidateForm($(this).parents(".control-group"), "detailInput", $(this), "onchange");
            });
            $("form[name=form_" + formname + "]").submit(function () {
                $(this).find(".help-block").remove();
                var selfsubmit = this;
                if (self.checkValidateForm($(this))) {
                    $("body").append($("<div/>", {
                        "class":"jsn-modal-overlay",
                        "style":"z-index: 1000; display: inline;"
                    })).append($("<div/>", {
                        "class":"jsn-modal-indicator",
                        "style":"display:block"
                    })).addClass("jsn-loading-page");
                    $("#jsn-form-target").remove();
                    $(selfsubmit).find('.message-uniform').html("");
                    var iframe = $('<iframe/>', {
                        name:'jsn-form-target',
                        id:'jsn-form-target'
                    });
                    iframe.appendTo($('body'));
                    iframe.css({
                        display:'none'
                    });
                    var publickey = $(this).find(".form-captcha").attr("publickey");
                    iframe.load(function () {
                        var message = iframe.contents().find("input[name$=message]").val();
                        var error = iframe.contents().find("input[name$=error]").val();
                        var redirect = iframe.contents().find("input[name$=redirect]").val();
                        if (redirect) {
                            window.location = redirect;
                        } else if (error) {
                            error = $.evalJSON(error);
                            self.callMessageError(formname, error);
                            $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
                        } else if (message) {
                            $.ajax({
                                type:"GET",
                                async:true,
                                encoding:"UTF-8",
                                scriptCharset:"utf-8",
                                cache:false,
                                contentType:"text/plain; charset=UTF-8",
                                url:baseUrl + "/index.php?option=com_uniform&view=form&task=form.getHtmlForm&tmpl=component&form_id=" + $(selfsubmit).find("input[name=form_id]").val(),
                                success:function (htmlForm) {
                                    $(selfsubmit).find(".jsn-row-container").empty();
                                    $(selfsubmit).find(".jsn-row-container").html(htmlForm);
                                    if (message) {
                                        $(selfsubmit).find('.message-uniform').html(
                                            $("<div/>", {
                                                "class":"success-uniform alert alert-success clearfix"
                                            }).append(
                                                $("<button/>", {
                                                    "class":"close",
                                                    "onclick":"return false;",
                                                    "data-dismiss":"alert",
                                                    text:"×"
                                                })).append(message));
                                    }
                                    self.initJSNForm(formname);
                                    var messagesFocus = $("form[name=form_" + formname + "]").find(".message-uniform")[0];
                                    $(window).scrollTop($(messagesFocus).offset().top - 50);
                                    $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
                                }
                            });

                        } else {
                            $.ajax({
                                type:"GET",
                                async:true,
                                cache:false,
                                encoding:"UTF-8",
                                scriptCharset:"utf-8",
                                contentType:"text/plain; charset=UTF-8",
                                url:baseUrl + "/index.php?option=com_uniform&view=form&task=form.getHtmlForm&tmpl=component&form_id=" + $(selfsubmit).find("input[name=form_id]").val(),
                                success:function (htmlForm) {
                                    $(selfsubmit).find(".jsn-row-container").empty();
                                    $(selfsubmit).find(".jsn-row-container").html(htmlForm);
                                    self.initJSNForm(formname);
                                    var messagesFocus = $("form[name=form_" + formname + "]").find(".message-uniform")[0];
                                    $(window).scrollTop($(messagesFocus).offset().top - 50);
                                    $(".jsn-modal-overlay,.jsn-modal-indicator").remove();
                                }
                            });
                        }
                        var idcaptcha;
                        idcaptcha = $(selfsubmit).find(".form-captcha").attr("id");
                        if (idcaptcha) {
                            Recaptcha.destroy();
                            Recaptcha.create(publickey, idcaptcha, {
                                theme:'white',
                                tabindex:0,
                                callback:function () {
                                    $(selfsubmit).find(".form-captcha").removeClass("error");
                                    $(selfsubmit).find(".form-captcha #recaptcha_area").addClass("controls");
                                    $(selfsubmit).find("#recaptcha_response_field").keypress(function (e) {
                                        if (e.which == 13) {
                                            if ($("form[name=form_" + formname + "] button.next").hasClass("hide")) {
                                                $("form[name=form_" + formname + "] button.jsn-form-submit").click();
                                            } else {
                                                $("form[name=form_" + formname + "] button.next").click();
                                            }
                                            return false;
                                        }
                                    });
                                    if (error) {

                                        if (error.captcha) {
                                            $(selfsubmit).find(".form-captcha").addClass("error");
                                            $(selfsubmit).find(".form-captcha #recaptcha_area").append(
                                                $("<span/>", {
                                                    "class":"help-block"
                                                }).append(
                                                    $("<span/>", {
                                                        "class":"validation-result label label-important",
                                                        text:error.captcha
                                                    })));
                                            $(selfsubmit).find("#recaptcha_response_field").focus();
                                        }
                                    }
                                }
                            });
                        }
                    });
                    $(this).attr('target', 'jsn-form-target');
                } else {
                    return false;
                }
            });
            $("form[name=form_" + formname + "]").find("input.jsn-daterangepicker").each(function () {
                var dateSettings = $.evalJSON($(this).attr("date-settings"));
                if (dateSettings) {
                    var yearRangeList = [];
                    if (dateSettings.yearRangeMin && dateSettings.yearRangeMax) {
                        yearRangeList.push(dateSettings.yearRangeMin);
                        yearRangeList.push(dateSettings.yearRangeMax);
                    } else if (dateSettings.yearRangeMin) {
                        yearRangeList.push(dateSettings.yearRangeMin);
                        yearRangeList.push((new Date).getFullYear());
                    } else if (dateSettings.yearRangeMax) {
                        yearRangeList.push(dateSettings.yearRangeMax);
                        yearRangeList.push((new Date).getFullYear());
                    }
                    var yearRange = "1930:+0";
                    if (yearRangeList.length) {
                        yearRange = yearRangeList.join(":");
                    }
                    var dateOptionFormat = "";
                    if (dateSettings.dateOptionFormat == "custom") {
                        dateOptionFormat = dateSettings.customFormatDate;
                    } else {
                        dateOptionFormat = dateSettings.dateOptionFormat;
                    }
                    if (dateSettings.dateFormat == "1" && dateSettings.timeFormat == "1") {
                        $(this).datetimepicker({
                            changeMonth:true,
                            changeYear:true,
                            showOn:"button",
                            yearRange:yearRange,
                            dateFormat:dateOptionFormat,
                            timeFormat:dateSettings.timeOptionFormat,
                            timeText:"",
                            hourText:lang['JSN_UNIFORM_DATE_HOUR_TEXT'],
                            minuteText:lang['JSN_UNIFORM_DATE_MINUTE_TEXT'],
                            closeText:lang['JSN_UNIFORM_DATE_CLOSE_TEXT'],
                            prevText:lang['JSN_UNIFORM_DATE_PREV_TEXT'],
                            nextText:lang['JSN_UNIFORM_DATE_NEXT_TEXT'],
                            currentText:lang['JSN_UNIFORM_DATE_CURRENT_TEXT'],
                            monthNames:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER']],
                            monthNamesShort:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT']],
                            dayNames:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY']],
                            dayNamesShort:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT']],
                            dayNamesMin:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_MIN']],
                            weekHeader:lang['JSN_UNIFORM_DATE_DAY_WEEK_HEADER']
                        });
                    } else if (dateSettings.dateFormat == "1") {
                        $(this).datepicker({
                            changeMonth:true,
                            changeYear:true,
                            showOn:"button",
                            yearRange:yearRange,
                            dateFormat:dateOptionFormat,
                            hourText:lang['JSN_UNIFORM_DATE_HOUR_TEXT'],
                            minuteText:lang['JSN_UNIFORM_DATE_MINUTE_TEXT'],
                            closeText:lang['JSN_UNIFORM_DATE_CLOSE_TEXT'],
                            prevText:lang['JSN_UNIFORM_DATE_PREV_TEXT'],
                            nextText:lang['JSN_UNIFORM_DATE_NEXT_TEXT'],
                            currentText:lang['JSN_UNIFORM_DATE_CURRENT_TEXT'],
                            monthNames:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER']],
                            monthNamesShort:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT']],
                            dayNames:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY']],
                            dayNamesShort:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT']],
                            dayNamesMin:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_MIN']],
                            weekHeader:lang['JSN_UNIFORM_DATE_DAY_WEEK_HEADER']
                        });
                    } else if (dateSettings.timeFormat == "1") {
                        $(this).timepicker({
                            showOn:"button",
                            timeText:"",
                            timeFormat:dateSettings.timeOptionFormat,
                            hourText:lang['JSN_UNIFORM_DATE_HOUR_TEXT'],
                            minuteText:lang['JSN_UNIFORM_DATE_MINUTE_TEXT'],
                            closeText:lang['JSN_UNIFORM_DATE_CLOSE_TEXT'],
                            prevText:lang['JSN_UNIFORM_DATE_PREV_TEXT'],
                            nextText:lang['JSN_UNIFORM_DATE_NEXT_TEXT'],
                            currentText:lang['JSN_UNIFORM_DATE_CURRENT_TEXT'],
                            monthNames:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER']],
                            monthNamesShort:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT']],
                            dayNames:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY']],
                            dayNamesShort:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT']],
                            dayNamesMin:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_MIN']],
                            weekHeader:lang['JSN_UNIFORM_DATE_DAY_WEEK_HEADER']
                        });
                    } else {
                        $(this).datepicker({
                            changeMonth:true,
                            changeYear:true,
                            yearRange:yearRange,
                            showOn:"button",
                            hourText:lang['JSN_UNIFORM_DATE_HOUR_TEXT'],
                            minuteText:lang['JSN_UNIFORM_DATE_MINUTE_TEXT'],
                            closeText:lang['JSN_UNIFORM_DATE_CLOSE_TEXT'],
                            prevText:lang['JSN_UNIFORM_DATE_PREV_TEXT'],
                            nextText:lang['JSN_UNIFORM_DATE_NEXT_TEXT'],
                            currentText:lang['JSN_UNIFORM_DATE_CURRENT_TEXT'],
                            monthNames:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER']],
                            monthNamesShort:[lang['JSN_UNIFORM_DATE_MONTH_JANUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_FEBRUARY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MARCH_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_APRIL_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_MAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JUNE_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_JULY_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_AUGUST_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_SEPTEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_OCTOBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_NOVEMBER_SHORT'],
                                lang['JSN_UNIFORM_DATE_MONTH_DECEMBER_SHORT']],
                            dayNames:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY']],
                            dayNamesShort:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_SHORT'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_SHORT']],
                            dayNamesMin:[lang['JSN_UNIFORM_DATE_DAY_SUNDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_MONDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_TUESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_WEDNESDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_THURSDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_FRIDAY_MIN'],
                                lang['JSN_UNIFORM_DATE_DAY_SATURDAY_MIN']],
                            weekHeader:lang['JSN_UNIFORM_DATE_DAY_WEEK_HEADER']
                        });
                    }
                    $("button.ui-datepicker-trigger").addClass("btn btn-icon").html($("<i/>", {
                        "class":"icon-calendar"
                    }));
                }
            });

            $("form[name=form_" + formname + "] .form-actions .prev").click(function () {
                $('form[name=form_' + formname + '] div.jsn-form-content').each(function (i) {
                    if (!$(this).is(':hidden')) {
                       // if (self.checkValidateForm($(this))) {
                            self.prevpaginationPage(formname);
                      //  }
                        return false;
                    }
                });
            });
            $("form[name=form_" + formname + "] .form-actions .next").click(function () {
                $('form[name=form_' + formname + '] div.jsn-form-content').each(function (i) {
                    if (!$(this).is(':hidden')) {
                        if (self.checkValidateForm($(this))) {
                            self.nextpaginationPage(formname);
                        }
                        return false;
                    }
                });
            });
            $("form[name=form_" + formname + "] .form-actions .reset").click(function () {
                $('form[name=form_' + formname + ']').trigger("reset");
                $('form[name=form_' + formname + '] .error').removeClass("error").find(".help-block").remove();
                $('form[name=form_' + formname + '] .jsn-form-content').addClass("hide");
                $('form[name=form_' + formname + '] .jsn-form-content').each(function (i, _formContent) {
                    if (i == 0) {
                        $(_formContent).removeClass("hide");
                    }
                });
                self.checkPage(formname);
            });
            this.defaultPage(formname);
            $('input, textarea').placeholder();
        };
        $.getActionField = function (formname, selfInput, idField) {
            var dataSettings = $(selfInput).parents(".control-group").attr("data-settings");
            if (dataSettings) {
                dataSettings = $.evalJSON(dataSettings);
            }
            if (dataSettings) {
                $.each(dataSettings, function (i, item) {
                    if ($(selfInput).val() == i) {
                        if (item.showField) {
                            var classShow = [];
                            $.each(item.showField, function (j, actionField) {
                                if (actionField) {
                                    classShow.push(".control-group." + actionField);
                                }

                            });
                            $("form[name=form_" + formname + "]").find(classShow.join(",")).addClass(idField).show();
                        }
                        if (item.hideField) {
                            var classHide = [];
                            $.each(item.hideField, function (j, actionField) {
                                if (actionField) {
                                    classHide.push("div.control-group." + actionField);
                                }
                            });
                            $("form[name=form_" + formname + "]").find(classHide.join(",")).addClass(idField).hide();
                        }
                    }
                });
            }
        };
        $.randomValueItems = function (_this) {
            var group = $(_this),
                choices = group.find('.jsn-column-item'),
                otherItem = choices.filter(function () {
                    return $('label.lbl-allowOther', this).size() > 0;
                }),
                randomItems = choices.not(otherItem);
            randomItems.detach();
            otherItem.detach();
            while (randomItems.length > 0) {
                var index = Math.floor(Math.random() * choices.length),
                    choice = randomItems[index];

                if (group.find(".lbl-allowOther").size()) {
                    group.find(".lbl-allowOther").before(choice);
                } else {
                    group.append(choice);
                }
                delete(randomItems[index]);
                var newList = [];
                $(randomItems).each(function (index, element) {
                    if (element !== undefined) {
                        newList.push(element);
                    }
                });
                randomItems = newList;
            }
            delete(randomItems[0]);
            if (group.find(".lbl-allowOther").size()) {
                group.find(".lbl-allowOther").before(otherItem);
            } else {
                group.append(otherItem);
            }
            return true;
        };
        $.captcha = function (form) {
            var self = this;
            var idcaptcha = "";
            var idcaptcha = form.find(".form-captcha").attr("id");
            var publickey = form.find(".form-captcha").attr("publickey");
            if (form.find(".form-captcha").length > 0 && form.find(".form-captcha").is(':hidden') && idcaptcha) {
                $(".form-captcha").hide();
                form.find(".form-captcha").show();
                Recaptcha.create(publickey, idcaptcha, {
                    theme:'white',
                    tabindex:0,
                    callback:function () {
                        $(form).find(".form-captcha").removeClass("error");
                        $(form).find(".form-captcha #recaptcha_area").addClass("controls");
                        $(form).find("#recaptcha_response_field").keypress(function (e) {
                            if (e.which == 13) {
                                if ($("form[name=form_" + formname + "] button.next").hasClass("hide")) {
                                    $("form[name=form_" + formname + "] button.jsn-form-submit").click();
                                } else {
                                    $("form[name=form_" + formname + "] button.next").click();
                                }
                                return false;
                            }
                        });
                    }
                });
            }
        };
        $.callMessageError = function (formname, messageError) {
            var self = this;
            $.each(messageError, function (key, value) {
                if (key != "captcha") {
                    if (key == "name" || key == "address" || key == "date" || key == "phone" || key == "currency" || key == "password") {
                        $.each(value, function (i, item) {
                            $("form[name=form_" + formname + "] input[name=password\\[" + i + "\\]\\[\\]],form[name=form_" + formname + "] input[name=currency\\[" + i + "\\]\\[value\\]],form[name=form_" + formname + "] input[name=phone\\[" + i + "\\]\\[default\\]],form[name=form_" + formname + "] input[name=phone\\[" + i + "\\]\\[one\\]],form[name=form_" + formname + "] input[name=date\\[" + i + "\\]\\[date\\]],form[name=form_" + formname + "] input[name=name\\[" + i + "\\]\\[first\\]],form[name=form_" + formname + "] input[name=address\\[" + i + "\\]\\[street\\]]").parents(".control-group").addClass("error").find(".controls").append($("<span/>", {
                                "class":"help-block"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:item
                                })));
                        });
                    } else if (key != "max-upload") {
                        if (key == "captcha_2") {
                            $("form[name=form_" + formname + "] #jsn-captcha").parents(".control-group").addClass("error").find(".controls").append($("<span/>", {
                                "class":"help-block"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:value
                                })));
                        } else {
                            if ($("form[name=form_" + formname + "] #" + key).size()) {
                                $("form[name=form_" + formname + "] #" + key).parents(".control-group").addClass("error").find(".controls").append($("<span/>", {
                                    "class":"help-block"
                                }).append(
                                    $("<span/>", {
                                        "class":"validation-result label label-important",
                                        text:value
                                    })));
                            }
                        }
                    } else if (key == "max-upload") {
                        $("form[name=form_" + formname + "] .message-uniform").html($("<div/>", {
                            "class":"alert alert-error"
                        }).append(value));
                    }
                }
            });
            var formError = $('form[name=form_' + formname + '] .error')[0];
            if ($(formError).parents('.jsn-form-content').attr("data-value")) {
                $('form[name=form_' + formname + '] .jsn-form-content').addClass("hide");
                $(formError).parents('.jsn-form-content').removeClass("hide");
                self.checkPage(formname);
            } else {
                var countPage = $('form[name=form_' + formname + '] div.jsn-form-content').length;
                $('form[name=form_' + formname + '] div.jsn-form-content')[countPage - 1].removeClass("hide");
                $('form[name=form_' + formname + '] input,form[name=form_' + formname + '] button,form[name=form_' + formname + '] textarea').focus();
            }
            if ($("form[name=form_" + formname + "]").find(".error input,.error textarea,.error select").length) {
                var fieldFocus = $("form[name=form_" + formname + "]").find(".error")[0];
                if ($(fieldFocus).find(".blank-required").size()) {
                    $(fieldFocus).find("input,select,textarea").each(function () {
                        var val = $(this).val();
                        var val2 = val.replace(' ', '');
                        if (val2 == '' || val2 == 0) {
                            $(window).scrollTop($(this).offset().top - 50);
                            $(this).click();
                            return false;
                        }
                    });
                } else {
                    var fieldFocus = $("form[name=form_" + formname + "]").find(".error input,.error textarea,.error select")[0];
                    $(window).scrollTop($(fieldFocus).offset().top - 50);
                    fieldFocus.click();
                }
            }
        };
        $.defaultPage = function (formname) {
            if (forms.length < 1) {
                this.captcha($('form[name=form_' + formname + ']'));
            }
            $($('form[name=form_' + formname + '] div.jsn-form-content')[0]).removeClass("hide");
            this.checkPage(formname);
            $('form[name=form_' + formname + ']').find("#page-loading").addClass("hide");
            forms.push(formname);
        };
        $.checkPage = function (formname) {
            $('form[name=form_' + formname + '] div.jsn-form-content').each(function (i) {
                if (!$(this).hasClass("hide")) {
                    if ($(this).next().attr("data-value")) {
                        $("form[name=form_" + formname + "] .form-actions .next").removeClass("hide");
                    } else {
                        $("form[name=form_" + formname + "] .form-actions .next").addClass("hide");
                    }
                    if ($(this).prev().attr("data-value")) {
                        $("form[name=form_" + formname + "] .form-actions .prev").removeClass("hide");
                    } else {
                        $("form[name=form_" + formname + "] .form-actions .prev").addClass("hide");
                    }
                    if (i + 1 == $('form[name=form_' + formname + '] div.jsn-form-content').length) {
                        $("form[name=form_" + formname + "] .form-actions .next").addClass("hide");
                        $("form[name=form_" + formname + "] .form-actions .jsn-form-submit").removeClass("hide");
                        $("form[name=form_" + formname + "] .form-actions .reset").removeClass("hide");

                    } else {
                        $("form[name=form_" + formname + "] .form-actions .next").removeClass("hide");
                        $("form[name=form_" + formname + "] .form-actions .jsn-form-submit").addClass("hide");
                        $("form[name=form_" + formname + "] .form-actions .reset").addClass("hide");
                    }
                    $(this).find(".content-google-maps").each(function () {
                        $(this).find('.google_maps').width($(this).attr("data-width"));
                        $(this).find('.google_maps').height($(this).attr("data-height"));
                        var dataValue = $(this).attr("data-value");
                        var dataMarker = $(this).attr("data-marker");
                        if (dataValue) {
                            var gmapOptions = $.evalJSON(dataValue);
                            if (dataMarker) {
                                var gmapMarker = $.evalJSON(dataMarker);
                            }
                            if (!gmapOptions.center.nb && gmapOptions.center.lb) {
                                gmapOptions.center.nb = gmapOptions.center.lb;
                            }
                            if (!gmapOptions.center.ob && gmapOptions.center.mb) {
                                gmapOptions.center.ob = gmapOptions.center.mb;
                            }
                            $(this).find('.google_maps').gmap({'zoom':gmapOptions.zoom, 'mapTypeId':gmapOptions.mapTypeId, 'center':gmapOptions.center.nb + ',' + gmapOptions.center.ob, 'disableDefaultUI':false, 'callback':function (map) {
                                var self = this;
                                self.set('inforWindow', function (marker, val) {
                                    var descriptions = val.descriptions;
                                    var content = '<div class="thumbnail">';
                                    if (val.images) {
                                        content += '<img  src="' + val.images + '">';
                                    }
                                    content += '<div class="caption">';
                                    if (val.title) {
                                        content += '<h4>' + val.title + '</h4>';
                                    }
                                    if (descriptions) {
                                        content += '<p>' + descriptions.replace(new RegExp('\n', 'g'), "<br/>") + '</p>';
                                    }
                                    if (val.link) {
                                        content += '<p><a target="_blank" href="' + val.link + '">more info</a></p>';
                                    }
                                    content += '</div></div>';
                                    self.openInfoWindow({ 'content':content}, marker);
                                });
                                self.get('map').setOptions({streetViewControl:false});
                                if (gmapMarker) {
                                    $.each(gmapMarker, function (i, val) {
                                        var position = $.evalJSON(val.position);

                                        if (position) {
                                            if (!position.nb && position.lb) {
                                                position.nb = position.lb;
                                            }
                                            if (!position.ob && position.mb) {
                                                position.ob = position.mb;
                                            }
                                            self.addMarker({'position':position.nb + "," + position.ob, 'draggable':false, 'bounds':false},function (map, marker) {
                                                if (val.open == "true") {
                                                    self.get('inforWindow')(marker, val);
                                                }
                                                if (val.title) {
                                                    marker.setTitle(val.title);
                                                }
                                            }).xclick(function (event) {
                                                    self.get('inforWindow')(this, val);
                                                })
                                        }
                                    });
                                }
                                setTimeout(function () {

                                    self.get('map').setCenter(self._latLng(gmapOptions.center.nb + ',' + gmapOptions.center.ob));
                                    self.get('map').setZoom(gmapOptions.zoom);
                                    self.get('map').setMapTypeId(gmapOptions.mapTypeId);
                                }, 1000);
                            }});

                        }
                    });
                }
            });
        };
        $.nextpaginationPage = function (formname) {
            var self = this;
            $('form[name=form_' + formname + '] div.jsn-form-content').each(function () {
                if (!$(this).hasClass("hide")) {
                    $(this).addClass("hide");
                    $(this).next().removeClass("hide");
                    self.checkPage(formname);
                    return false;
                }
            });
        };
        $.prevpaginationPage = function (formname) {
            var self = this;
            $('form[name=form_' + formname + '] div.jsn-form-content').each(function () {
                if (!$(this).hasClass("hide")) {
                    $(this).addClass("hide");
                    $(this).prev().removeClass("hide");
                    self.checkPage(formname);
                    return false;
                }
            });
        };
        $.checkValidateForm = function (_this, type, element, onchange) {
            var check = 0;
            var $inputBlank = $(_this).find(".blank-required");
            var self = this;
            $inputBlank.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    var checkBlank = true;
                    $(this).find(".help-blank").remove();
                    $(this).parent().removeClass("error");
                    $(this).find("input,select,textarea").each(function () {
                        var val = $(this).val();
                        var val2 = val.replace(' ', '');
                        if ($(this).attr("type") == "text") {
                            if (val2 == '') {
                                checkBlank = false;
                            }
                        } else {
                            if (val2 == '') {
                                checkBlank = false;
                            }
                        }
                    });
                    if (!checkBlank) {
                        $(this).parent().addClass("error");
                        $(this).append(
                            $("<span/>", {
                                "class":"help-block help-blank"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                })));
                        check++;
                    }
                }
            });
            var groupBlank = $(_this).find(".group-blank-required");
            groupBlank.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    var checkGroupBlank = false;
                    $(this).find(".help-blank").remove();
                    $(this).parent().removeClass("error");
                    $(this).find("input").each(function () {
                        var val = $(this).val();
                        var val2 = val.replace(' ', '');
                        if (val2 != '') {
                            checkGroupBlank = true;
                        }
                    });
                    if (!checkGroupBlank) {
                        $(this).parents(".control-group").addClass("error");
                        $(this).append(
                            $("<span/>", {
                                "class":"help-block help-blank"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                })));
                        check++;
                    }
                }
            });
            var $dropdown = $(_this).find(".dropdown-required");
            $dropdown.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    $(this).find(".help-dropdown").remove();
                    $(this).parent().removeClass("error");
                    if ($(this).find("select").val() == "") {
                        $(this).parent().addClass("error");
                        $(this).append(
                            $("<span/>", {
                                "class":"help-block help-dropdown"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                })))
                        check++;
                    } else if ($(this).find("select option:selected").hasClass('lbl-allowOther')) {
                        var selfRadio = this;

                        $(this).find(".jsn-dropdown-Others").focusout(function () {
                            var checkRadio = false;
                            var valchoices = $(selfRadio).find(".jsn-dropdown-Others").val();
                            var valchoices2 = valchoices.replace(' ', '');
                            if (valchoices2 == '') {
                                checkRadio = true;
                            }
                            if (checkRadio) {
                                $(selfRadio).find(".help-dropdown").remove();
                                $(selfRadio).parent().addClass("error");
                                $(selfRadio).append(
                                    $("<span/>", {
                                        "class":"help-block help-dropdown"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                        })))
                                check++;
                            }
                        });
                        if (type != "detailInput") {
                            $(this).find(".jsn-dropdown-Others").trigger("focusout");
                        }
                    }
                }
            });
            var $inputEmailNull = $(_this).find("input.email");
            $inputEmailNull.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    var parentEmail = $(this).parents(".control-group");
                    $(parentEmail).find(".help-email").remove();
                    $(parentEmail).removeClass("error");
                    var val = $(this).val();
                    var filter = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,6})$/;
                    if (!filter.test(val) && $(this).hasClass("email-required")) {
                        $(parentEmail).addClass("error");
                        $(this).parents(".controls").append(
                            $("<span/>", {
                                "class":"help-block help-email"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_INVALID']
                                })));
                        check++;
                    } else if (!$(this).hasClass("email-required") && val && !filter.test(val)) {
                        $(parentEmail).addClass("error");
                        $(this).parents(".controls").append(
                            $("<span/>", {
                                "class":"help-block help-email"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_INVALID']
                                })));
                        check++;
                    }
                    if (val && filter.test(val) && $(parentEmail).find(".jsn-email-confirm").hasClass("jsn-email-confirm") && ($(element).hasClass("jsn-email-confirm") || !$(parentEmail).hasClass("ui-state-highlight"))) {
                        if ($(parentEmail).find(".jsn-email-confirm").val() != $(this).val()) {
                            $(parentEmail).addClass("error");
                            $(this).parents(".controls").append(
                                $("<span/>", {
                                    "class":"help-block help-email"
                                }).append(
                                    $("<span/>", {
                                        "class":"validation-result label label-important",
                                        text:lang['JSN_UNIFORM_CONFIRM_FIELD_EMAIL_CONFIRM']
                                    })));
                            check++;
                        }
                    }
                }
            });
            var $inputWebsite = $(_this).find("input.website");
            $inputWebsite.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    $(this).parent().find(".help-website").remove();
                    $(this).parent().parent().removeClass("error");
                    var val = $(this).val();
                    var regexp = /^(https?:\/\/|ftp:\/\/|www([0-9]{0,9})?\.)?(((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
                    if ((!regexp.test(val) && $(this).hasClass("website-required")) || (val != "" && val != "http://" && val != "https://" && !$(this).hasClass("website-required") && !regexp.test(val))) {
                        $(this).parent().parent().addClass("error");
                        $(this).after(
                            $("<span/>", {
                                "class":"help-block help-website"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_INVALID']
                                })));
                        check++;
                    }
                }
            });
            var $inputInteger = $(_this).find("input.integer-required");
            $inputInteger.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    $(this).parent().find(".help-integer").remove();
                    $(this).parent().parent().removeClass("error");
                    var val = $(this).val();
                    var regexp = /^[0-9]+$/;
                    if (!regexp.test(val)) {
                        $(this).parent().parent().addClass("error");
                        $(this).parent().append(
                            $("<span/>", {
                                "class":"help-block help-integer"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_INVALID']
                                })));
                        check++;
                    }
                }
            });
            if (onchange != "onchange") {
                var $valueLimitPassword = $(_this).find(".limit-password-required");
                $valueLimitPassword.each(function () {
                    if ($(this).parents(".control-group").css("display") != "none") {
                        var checkval = false;
                        if ($(this).hasClass("group-blank-required")) {
                            $(this).find("input").each(function () {
                                var val = $(this).val();
                                var val2 = val.replace(' ', '');
                                if (val2 == '') {
                                    checkval = true;
                                }
                            });
                        }
                        if (!checkval) {
                            var inputPassword = $(this).find("input");
                            var limitSettings = $.evalJSON($(inputPassword).attr("data-limit"));
                            var checkPassword = false;
                            if ($(this).find("input").length > 1) {
                                $(this).parent().removeClass("error");
                                $(this).find(".help-limit").remove();
                                $(this).find("input").each(function () {
                                    if ($(this).val().length < limitSettings.limitMin) {
                                        checkPassword = true;
                                    } else if ($(this).val().length > limitSettings.limitMax) {
                                        checkPassword = true;
                                    }
                                });
                            } else {
                                if ($(inputPassword).val() != '' || $(inputPassword).val() != 0) {
                                    $(inputPassword).parent().find(".help-limit").remove();
                                    $(inputPassword).parent().parent().removeClass("error");
                                    if ($(inputPassword).val().length < limitSettings.limitMin) {
                                        checkPassword = true;
                                    } else if ($(inputPassword).val().length > limitSettings.limitMax) {
                                        checkPassword = true;
                                    }
                                }
                            }

                            if (checkPassword) {
                                check++;
                                var textLang = lang['JSN_UNIFORM_CONFIRM_FIELD_PASSWORD_MIN_MAX_CHARACTER'];
                                textLang = textLang.replace("%mi%", limitSettings.limitMin);
                                textLang = textLang.replace("%mx%", limitSettings.limitMax);
                                $(this).parent().addClass("error");
                                $(this).append(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:textLang
                                        })));
                            }
                        }
                    }
                });
            }
            var $valueLimit = $(_this).find(".limit-required");
            $valueLimit.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    var limitSettings = $.evalJSON($(this).attr("data-limit"));
                    var checkval = false;
                    if ($(this).parent().hasClass("group-blank-required")) {
                        $(this).find("input").each(function () {
                            var val = $(this).val();
                            var val2 = val.replace(' ', '');
                            if (val2 == '') {
                                checkval = true;
                            }
                        });
                    }
                    if ($(this).parent().hasClass("blank-required")) {
                        var val = $(this).val();
                        var val2 = val.replace(' ', '');
                        if ($(this).attr("type") == "text") {
                            if (val2 == '') {
                                checkval = true;
                            }
                        } else {
                            if (val2 == '' || val2 == 0) {
                                checkval = true;
                            }
                        }
                    }
                    if (!checkval) {
                        $(this).parent().find(".help-limit").remove();
                        $(this).parent().parent().removeClass("error");
                        if (limitSettings.limitType == "Words") {
                            var lengthValue = $.trim($(this).val()).split(/[\s]+/);
                            if (lengthValue.length < limitSettings.limitMin) {
                                check++;
                                $(this).parent().parent().addClass("error");
                                $(this).after(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_MIN_LENGTH'] + " " + limitSettings.limitMin + " "+lang['JSN_UNIFORM_WORDS']
                                        })));
                            } else if (lengthValue.length > limitSettings.limitMax) {
                                check++;
                                $(this).parent().parent().addClass("error");
                                $(this).after(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_MAX_LENGTH'] + " " + limitSettings.limitMax + " "+lang['JSN_UNIFORM_WORDS']
                                        })));
                            }
                        } else {
                            if ($(this).val().length < limitSettings.limitMin) {
                                check++;
                                $(this).parent().parent().addClass("error");
                                $(this).after(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_MIN_LENGTH'] + " " + limitSettings.limitMin + " "+lang['JSN_UNIFORM_CHARACTERS']
                                        })));
                            } else if ($(this).val().length > limitSettings.limitMax) {
                                check++;
                                $(this).parent().parent().addClass("error");
                                $(this).after(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_MAX_LENGTH'] + " " + limitSettings.limitMax + " "+lang['JSN_UNIFORM_CHARACTERS']
                                        })));
                            }
                        }

                    }
                }
            });

            var $valueNumberLimit = $(_this).find(".number-limit-required");
            $valueNumberLimit.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    var checkval = false;
                    if ($(this).hasClass("integer-required")) {
                        var val = $(this).val();
                        var regexp = /^[0-9]+$/;
                        if (!regexp.test(val)) {
                            checkval = true;
                        }
                    }
                    if (!checkval) {
                        var limitNumberSettings = $.evalJSON($(this).attr("data-limit"));
                        $(this).parent().find(".help-limit").remove();
                        $(this).parent().parent().removeClass("error");
                        if ($(this).val() != '' || $(this).val() != 0) {
                            if (parseInt($(this).val(), 10) < limitNumberSettings.limitMin) {
                                check++;
                                $(this).parent().parent().addClass("error");
                                $(this).parent().append(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_MIN_NUMBER'] + " " + limitNumberSettings.limitMin
                                        })));
                            } else if (parseInt($(this).val(), 10) > limitNumberSettings.limitMax) {
                                check++;
                                $(this).parent().parent().addClass("error");
                                $(this).parent().append(
                                    $("<span/>", {
                                        "class":"help-block help-limit"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_MAX_NUMBER'] + " " + limitNumberSettings.limitMax
                                        })));
                            }
                        }

                    }
                }
            });

            var $list = $(_this).find(".list-required");
            $list.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    $(this).parent().find(".help-list").remove();
                    $(this).parent().removeClass("error");
                    if (!$(this).find("select").val()) {
                        $(this).parent().addClass("error");
                        $(this).find("select").after(
                            $("<span/>", {
                                "class":"help-block help-list"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_INVALID']
                                })));
                        check++;
                    }
                }
            });
            var $inputchoices = $(_this).find(".choices-required");
            $inputchoices.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    $(this).find(".help-choices").remove();
                    $(this).parent().removeClass("error");
                    if ($(this).find("input[type=radio]:checked").length < 1) {
                        $(this).parent().addClass("error");
                        $(this).append(
                            $("<span/>", {
                                "class":"help-block help-choices"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                })))
                        check++;
                    } else if ($(this).find("input[type=radio]:checked").hasClass('allowOther') && $(this).find("input[type=radio]:checked").length == 1) {
                        var selfRadio = this;
                        $(this).find(".jsn-value-Others").focusout(function () {
                            var checkRadio = false;
                            var valchoices = $(selfRadio).find(".jsn-value-Others").val();
                            var valchoices2 = valchoices.replace(' ', '');
                            if (valchoices2 == '') {
                                checkRadio = true;
                            }
                            if (checkRadio) {
                                $(selfRadio).find(".help-choices").remove();
                                $(selfRadio).parent().addClass("error");
                                $(selfRadio).append(
                                    $("<span/>", {
                                        "class":"help-block help-choices"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                        })))
                                check++;
                            }
                        });
                        if (type != "detailInput") {
                            $(this).find(".jsn-value-Others").trigger("focusout");
                        }
                    }
                }
            });
            if (onchange != "onchange") {
                var $inputlikert = $(_this).find(".likert-required");
                $inputlikert.each(function () {
                    if ($(this).parents(".control-group").css("display") != "none") {
                        $(this).find(".help-likert").remove();
                        $(this).parents(".control-group").removeClass("error");
                        $(this).find("tbody tr").each(function () {
                            if ($(this).find("input[type=radio]:checked").length < 1) {
                                $(this).parents(".control-group").addClass("error");
                                if (!$(this).parents(".controls").find(".help-likert").size()) {
                                    $(this).parents(".controls").append(
                                        $("<span/>", {
                                            "class":"help-block help-likert"
                                        }).append(
                                            $("<span/>", {
                                                "class":"validation-result label label-important",
                                                text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                            })))
                                }
                                check++;
                            }
                        })

                    }
                });
            }
            var $inputCheckbox = $(_this).find(".checkbox-required");
            $inputCheckbox.each(function () {
                if ($(this).parents(".control-group").css("display") != "none") {
                    $(this).find(".help-checkbox").remove();
                    $(this).parent().parent().removeClass("error");
                    if ($(this).find("input[type=checkbox]:checked").length < 1) {
                        $(this).parent().parent().addClass("error");
                        $(this).append(
                            $("<span/>", {
                                "class":"help-block help-checkbox"
                            }).append(
                                $("<span/>", {
                                    "class":"validation-result label label-important",
                                    text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                })))
                        check++;
                    } else if ($(this).find("input[type=checkbox]:checked").length == 1 && $(this).find("input[type=checkbox]:checked").hasClass('allowOther')) {
                        var selfCheckbox = this;
                        $(this).find(".jsn-value-Others").focusout(function () {
                            var checkCheckbox = false;
                            var valchoices = $(selfCheckbox).find(".jsn-value-Others").val();
                            var valchoices2 = valchoices.replace(' ', '');
                            if (valchoices2 == '') {
                                checkCheckbox = true;
                            }
                            if (checkCheckbox) {
                                $(selfCheckbox).find(".help-checkbox").remove();
                                $(selfCheckbox).parent().parent().addClass("error");
                                $(selfCheckbox).append(
                                    $("<span/>", {
                                        "class":"help-block help-checkbox"
                                    }).append(
                                        $("<span/>", {
                                            "class":"validation-result label label-important",
                                            text:lang['JSN_UNIFORM_CONFIRM_FIELD_CANNOT_EMPTY']
                                        })))
                                check++;
                            }
                        });
                        if (type != "detailInput") {
                            $(this).find(".jsn-value-Others").trigger("focusout");
                        }
                    }
                }
            });
            if (check > 0 && type != "detailInput") {
                var fieldFocus = $(_this).find(".error")[0];
                if ($(fieldFocus).find(".blank-required").size()) {
                    $(fieldFocus).find("input,select,textarea").each(function () {
                        var val = $(this).val();
                        var val2 = val.replace(' ', '');
                        if (val2 == '' || val2 == 0) {
                            $(window).scrollTop($(this).offset().top - 50);
                            $(this).focus();
                            $(this).click();
                            return false;
                        }
                    })
                } else {
                    var fieldFocus = $(_this).find(".error input,.error textarea,.error select")[0];
                    $(window).scrollTop($(fieldFocus).offset().top - 50);
                    $(fieldFocus).focus();
                }
                return false;
            }
            if (check > 0 && type == "detailInput") {
                return false;
            }
            return true;
        };
        $.getBoxStyle = function (element) {

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
            return style;
        };
        function jsnLoadForm($) {
            $(".jsn-uniform").each(function () {
                if ($(this).attr("data-form-name")) {
                    var getLang = $(this).find("span.jsn-language").attr("data-value");
                    baseUrl = $(this).find("span.jsn-base-url").attr("data-value");
                    if (getLang) {
                        lang = $.evalJSON(getLang);
                    }
                    $.initJSNForm($(this).attr("data-form-name"));
                }
            });
        }

        jQuery(document).ready(jsnLoadForm($));
    });
})(jQuery);