/*------------------------------------------------------------------------
 # Full Name of JSN UniForm
 # ------------------------------------------------------------------------
 # author    JoomlaShine.com Team
 # copyright Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 # Websites: http://www.joomlashine.com
 # Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 # @license - GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 # @version $Id: forms.js 19014 2012-11-28 04:48:56Z thailv $
 -------------------------------------------------------------------------*/
define([
    'jquery',
    'uniform/help',
    'uniform/dialogedition',
    'jquery.ui'],

function ($, JSNHelp, JSNUniformDialogEdition) {
    function JSNUniformForms(params) {
        this.params = params;
        this.lang = params.language;
        this.init();
    }
    JSNUniformForms.prototype = {
        //Create modal box email list select 
        init: function () {
            this.JSNHelp = new JSNHelp();
            var self = this;
            this.JSNUniformDialogEdition = new JSNUniformDialogEdition(this.params);
            $(".jsn-popup-upgrade").click(function () {
                JSNUniformDialogEdition.createDialogLimitation($(this), self.lang["JSN_UNIFORM_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_FORM_IN_FREE_EDITION_0"]);
            })
        }
    }

    return JSNUniformForms;
});