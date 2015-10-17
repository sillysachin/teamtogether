/**
 * @package   com_jsonexport
 * @copyright Copyright (C) 2013 joocode. All rights reserved.
 * @license   GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link      http://www.joocode.com
 */

(function() {
  'use strict';

  App.Router.map(function() {
    this.route('tables', { path: '/' });
    this.route('tables');
    this.route('settings');
  });

  App.TablesRoute = Ember.Route.extend({
    // model: function(params) {
    //   App.set('productsController', this.controllerFor('products'));
    //   return this.controllerFor('products').findAll();
    // }
  });

  App.SettingsRoute = Ember.Route.extend({
    // model: function(params) {
    //   App.set('productsController', this.controllerFor('products'));
    //   return this.controllerFor('products').findAll();
    // }
  });

  App.Router.reopen({
    didTransition: function(infos) {
      jQuery('#toolbar-help button').popover('hide');

      if (infos[1].name === 'settings') {
        App.loadSettings();
      }

      this._super(infos);
    }
  })

}());
