/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
    $(function () {

        $.initJSNSubmissions = function () {
            var self = this;
            $("#submission-fields-list").hide();
            $(".jsn-items-list").sortable({
                items:"li:not(.field-disabled)"
            });
            $('button.select-field').click(function (e) {
                self.dialogSelectFields($(this));
                e.stopPropagation();
            });
            if ($("#filter_date_submission").length) {
                $("#filter_date_submission").daterangepicker({
                    startDate:moment().subtract('days', 29),
                    endDate:moment(),
                    showDropdowns:true,
                    showWeekNumbers:true,
                    ranges:{
                        'Today':[moment(), moment()],
                        'Yesterday':[moment().subtract('days', 1), moment().subtract('days', 1)],
                        'Last 7 Days':[moment().subtract('days', 6), moment()],
                        'Last 30 Days':[moment().subtract('days', 29), moment()]
                    },
                    opens:'right',
                    buttonClasses:['btn btn-default'],
                    applyClass:'btn-small btn-primary',
                    cancelClass:'btn-small',
                    format:'MM/DD/YYYY',
                    separator:' - ',
                    locale:{
                        applyLabel:"Apply",
                        fromLabel:'From',
                        toLabel:'To',
                        customRangeLabel:'Custom Range',
                        daysOfWeek:['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames:['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay:1
                    }
                });
            }
            $("#filter_form_id").change(function () {
                $("form[name=adminForm] input[type=text],form[name=adminForm] select").attr("disabled", "disabled");
            });
            $(".daterangepicker").addClass("jsn-bootstrap hide");
        };
        $.dialogSelectFields = function (_this) {
            var self = this;
            var dialog = $("#submission-fields-list"), parentDialog = $("#submission-fields-list").parent();
            $(dialog).appendTo('body');
            dialog.show();
            $("#submission-fields-list .popover").show();
            var elmStyle = self.getBoxStyle($(dialog).find(".popover")),
                parentStyle = self.getBoxStyle($(_this)),
                position = {};
            position.left = parentStyle.offset.left - elmStyle.outerWidth + parentStyle.outerWidth;
            position.top = parentStyle.offset.top + parentStyle.outerHeight;
            $(dialog).find(".arrow").css("left", elmStyle.outerWidth - (parentStyle.outerWidth / 2));
            dialog.css(position).click(function (e) {
                e.stopPropagation();
            });
            $("#done").click(function () {
                if ($(dialog).css('display') != 'none') {
                    $(dialog).appendTo($(parentDialog));
                    dialog.hide();
                }
                var field = [];
                var list_fields = [];
                $('input:checkbox[name="field[]"]:checked').each(function (index) {
                    field.push('"' + $(this).val() + '"');
                });
                $('input:checkbox[name="field[]"]').each(function (index) {
                    list_fields.push($(this).val());
                });
                $('#list_view_field').val(field);
                $('#filter_position_field').val(list_fields);

                $("#adminForm").submit();
            });
            $(document).click(function () {
                if ($(dialog).css('display') != 'none') {
                    $(dialog).appendTo($(parentDialog));
                    dialog.hide();
                }
            });
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
        function jsnLoadSubmissions($) {
            $.initJSNSubmissions();
        }
        jQuery(document).ready(jsnLoadSubmissions($));
    });
})(jQuery);