/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: configemailsettings.js 19013 2012-11-28 04:48:47Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
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
        init: function () {
            $('.jsn-label-des-tipsy').tipsy({
                gravity: 'w',
                fade: true
            });
            if(parent.jQuery("#form-default-settings").length<1){
                window.location.href = "index.php?option=com_uniform";
            }
            $("#jform_template_subject").attr("placeholder", this.lang['JSN_UNIFORM_PLACEHOLDER_EMAIL_SUBJECT_1']);

            $.save = function () {
                $("#uni-form").hide();
                $("#form-loading").show();
                document.adminForm.submit();
                return false;
            }
            $('input, textarea').placeholder();
        }
    }
    return JSNUniformEmailSettingsView;
});