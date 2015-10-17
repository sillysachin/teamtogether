/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.CommonView = Ember.View.extend({

    didInsertElement: function() {
      this._super();

      jQuery.when(App.DeferredHelper.domElementIsPresent('#toolbar-help button'))
      .then(function() {
        jQuery('#toolbar-help button').popover({
           placement: 'bottom',
           trigger: 'click',
           content: 'Consult the <a href="https://joocode.zendesk.com" target="_blank">guides on the support portal</a>, and if you find no answer, <a href="https://joocode.zendesk.com/tickets/new" target="_blank">open a ticket</a>',
           title: 'How to get help'
        });
      });

      setTimeout(function() {
        jQuery('.hasTooltip').tooltip()
      }, 500);
    },

    _setPageTitle: function _setPageTitle(title) {
      jQuery('h1.page-title').html(title); //j3
      jQuery('.pagetitle h2').html('JSON Export: ' + title); //j25
    }

  });
}());
